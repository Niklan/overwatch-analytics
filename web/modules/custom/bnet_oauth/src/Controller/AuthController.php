<?php

namespace Drupal\bnet_oauth\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class AuthController.
 */
class AuthController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function auth() {
    \Drupal::service('page_cache_kill_switch')->trigger();
    $client_id = \Drupal::state()->get('bnet_oauth_settings_key');

    /** @var \Drupal\bnet_oauth\BnetOAuth $bnet_oauth */
    $bnet_auth_code_url = \Drupal::service('bnet_oauth')
      ->setClientId($client_id)
      ->setRegion('eu')
      ->getAuthorizationCodeUrl();

    return TrustedRedirectResponse::create($bnet_auth_code_url);
  }

  /**
   * {@inheritdoc}
   */
  public function callback() {
    $request_query = \Drupal::request()->query;
    $state = $request_query->get('state');
    /** @var \Drupal\bnet_oauth\BnetOAuth $bnet_oauth */
    $bnet_oauth = \Drupal::service('bnet_oauth')->setRegion('eu');
    $frontpage_url = Url::fromRoute('<front>')->toString();
    if ($bnet_oauth->compareCsrfToken($state)) {
      $response = \Drupal::httpClient()
        ->post('https://eu.battle.net/oauth/token', [
          'verify' => TRUE,
          'form_params' => [
            'client_id' => \Drupal::state()->get('bnet_oauth_settings_key'),
            'client_secret' => \Drupal::state()
              ->get('bnet_oauth_settings_secret'),
            'redirect_uri' => 'https://overwatch-analytics.localhost/bnet/callback',
            'scope' => '',
            'grant_type' => 'authorization_code',
            'code' => $request_query->get('code'),
          ],
        ]);
      $response_result = Json::decode($response->getBody()->getContents());
      if (!empty($response_result['access_token'])) {
        $request = \Drupal::httpClient()
          ->get('https://eu.api.battle.net/account/user?access_token=' . $response_result['access_token']);
        $account_data = Json::decode($request->getBody()->getContents());
        $this->finalizeAuth($account_data);
      }
      return RedirectResponse::create($frontpage_url);
    }
    else {
      drupal_set_message(t("Something wen't wrong during battle.net access."), 'error');
      return RedirectResponse::create($frontpage_url);
    }
  }

  /**
   * Finalize auth. Creates or login in-to user.
   */
  public function finalizeAuth($account_data) {
    $user = \Drupal::entityQuery('user')
      ->condition('bnet_oauth_id', $account_data['id'])
      ->range(0, 1)
      ->execute();

    if (empty($user)) {
      $this->createNewUser($account_data);
    }
    else {
      $this->loginAsUser(reset($user));
    }
  }

  /**
   * Login as existing user.
   *
   * @param $uid
   */
  public function loginAsUser($uid) {
    $user = User::load($uid);
    user_login_finalize($user);
  }

  /**
   * Create new user from Battle.net data.
   */
  public function createNewUser($account_data) {
    // Username can't contain hash symbol.
    $battletag = str_replace('#', '-', $account_data['battletag']);
    $id = $account_data['id'];
    $user = User::create([
      // The name in Drupal must be unique. name-tag-id.
      'name' => $battletag . '-' . $id,
      'pass' => NULL,
      'mail' => NULL,
      'init' => NULL,
      'status' => 1,
      'roles' => [
        AccountInterface::AUTHENTICATED_ROLE,
      ],
      'bnet_oauth_battletag' => [
        'value' => $battletag,
      ],
      'bnet_oauth_id' => [
        'value' => $id,
      ],
    ]);
    $user->save();
    $this->loginAsUser($user->id());
  }

}
