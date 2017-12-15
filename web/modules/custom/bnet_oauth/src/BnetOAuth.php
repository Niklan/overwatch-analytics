<?php

namespace Drupal\bnet_oauth;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Url;

/**
 * Class BnetOAuth.
 */
class BnetOAuth {

  protected $clientId;

  protected $secret;

  protected $region = 'eu';

  protected $authorizeUriPattern = 'https://@region.battle.net/oauth/authorize';

  protected $authorizeUri;

  protected $tokenUriPattern = 'https://@region.battle.net/oauth/token';

  protected $tokenUri;

  /**
   * BnetOAuth constructor.
   */
  public function __construct() {
    return $this;
  }

  /**
   * @return mixed
   */
  public function getClientId() {
    return $this->clientId;
  }

  /**
   * @param mixed $clientId
   */
  public function setClientId($clientId) {
    $this->clientId = $clientId;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getSecret() {
    return $this->secret;
  }

  /**
   * @param mixed $secret
   */
  public function setSecret($secret) {
    $this->secret = $secret;
    return $this;
  }

  /**
   * @return string
   */
  public function getRegion() {
    return $this->region;
  }

  /**
   * @param string $region
   */
  public function setRegion($region) {
    $this->region = $region;
    $this->authorizeUri = new FormattableMarkup($this->authorizeUriPattern, [
      '@region' => $this->region,
    ]);
    $this->tokenUri = new FormattableMarkup($this->tokenUriPattern, [
      '@region' => $this->region,
    ]);
    return $this;
  }

  /**
   * Request Auth code from battle.net server.
   */
  public function getAuthorizationCodeUrl() {
    $url = Url::fromUri($this->authorizeUri, [
      'query' => [
        'client_id' => $this->clientId,
        'scope' => '',
        'state' => $this->getCsrfToken(),
        'redirect_uri' => 'https://overwatch-analytics.localhost/bnet/callback',
        'response_type' => 'code',
      ],
    ]);
    return $url->toString();
  }

  /**
   * Generate token for state parameter of auth.
   */
  public function getCsrfToken() {
    return \Drupal::csrfToken()->get(\Drupal::request()->getClientIp());
  }

  /**
   * Compare Drupal generated csrf Token with provided.
   *
   * @param string $token
   *   Token to compare.
   *
   * @return bool
   *   TRUE if they are equal.
   */
  public function compareCsrfToken($token) {
    return \Drupal::csrfToken()->validate($token);
  }

  /**
   * Request Auth code from battle.net server.
   */
  public function getAccessTokenUrl($code) {
    $url = Url::fromUri($this->tokenUri, [
      'query' => [
        'redirect_uri' => 'https://overwatch-analytics.localhost/',
        'scope' => '',
        'grant_type' => 'authorization_code',
        'code' => $code,
      ],
    ]);

    return $url->toString();
  }

  /**
   * @return mixed
   */
  public function getTokenUri() {
    return $this->tokenUri;
  }

}