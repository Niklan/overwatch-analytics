<?php

namespace Drupal\overwatch_analytics\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Psr\Log\LoggerInterface;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "is_user_has_rating_in_season_resource",
 *   label = @Translation("Is user has rating in season resource"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/is-user-has-rating-in-season",
 *   }
 * )
 */
class IsUserHasRatingInSeasonResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new IsUserHaveRatingInSeasonResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('overwatch_analytics'),
      $container->get('current_user')
    );
  }

  /**
   * Responds to GET requests.
   *
   * Returns indication is has user SR in provided season or not.
   *
   * @todo add Season ID check.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function get() {
    $query = \Drupal::request()->query;
    if (!$query->has('sid')) {
      $response['message'] = 'The SID (Season ID) is not provided.';
      return new ResourceResponse($response, 400);
    }

    if ($query->has('uid')) {
      $uid = $query->get('uid');
    }
    else {
      // If User ID is not provided we load current user.
      $uid = \Drupal::currentUser()->id();
    }

    $user = \Drupal::entityTypeManager()
      ->getStorage('user')
      ->load($uid);
    if ($user instanceof UserInterface) {
      $overwatch_match_helper = \Drupal::service('overwatch_match.helper');
      $response['has_sr'] = $overwatch_match_helper->isUserHasSrInSeason($query->get('sid'), $user->id());
      return new ResourceResponse($response);
    }
    else {
      $response['message'] = 'User is not found';
      return new ResourceResponse($response, 400);
    }
  }

}
