<?php

namespace Drupal\bnet_oauth\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;

/**
 * Class AuthController.
 */
class AuthController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function auth() {
    $region = 'eu';
    $client_id = \Drupal::state()->get('bnet_oauth_settings_key');
    $secret = \Drupal::state()->get('bnet_oauth_settings_secret');

    $url = Url::fromUri('https://' . $region . '.battle.net/oauth/authorize', [
      'query' => [
        'client_id' => $client_id,
        'scope' => '',
        'state' => random_bytes(8),
        'redirect_uri' => 'https://localhost',
        'response_type' => 'code',
      ],
    ]);

    return TrustedRedirectResponse::create($url->toString());
  }

}
