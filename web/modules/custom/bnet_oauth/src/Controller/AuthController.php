<?php

namespace Drupal\bnet_oauth\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use SebastianBergmann\RecursionContext\Exception;
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
        ksm($request->getBody()->getContents());
      }
      return RedirectResponse::create($frontpage_url);
    }
    else {
      drupal_set_message(t("Something wen't wrong during battle.net access."), 'error');
      return RedirectResponse::create($frontpage_url);
    }
  }

}
