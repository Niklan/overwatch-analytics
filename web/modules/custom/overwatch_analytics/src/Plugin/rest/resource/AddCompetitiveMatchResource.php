<?php

namespace Drupal\overwatch_analytics\Plugin\rest\resource;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\overwatch_match\Entity\OverwatchMatch;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Provides a resource to add competitive match for OverwatchMatch entity.
 *
 * @RestResource(
 *   id = "add_competitive_match_resource",
 *   label = @Translation("Add competitive match resource"),
 *   uri_paths = {
 *     "https://www.drupal.org/link-relations/create" =
 *   "/api/v1/add-competitive-match",
 *   }
 * )
 */
class AddCompetitiveMatchResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new AddCompetitiveMatchResource object.
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
   * Responds to POST requests.
   *
   * @todo maybe change values names to fields and pass it directly.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function post($data, Request $request) {
    if (!$this->currentUser->hasPermission('add overwatch match entities')) {
      throw new AccessDeniedHttpException();
    }

    try {
      $form_values = $data['form_values'];
      $match = OverwatchMatch::create([
        'type' => 'competitive',
      ]);

      if (!empty($form_values['sr'])) {
        $match->field_skill_rating->value = $form_values['sr'];
      }

      if (!empty($form_values['heroesSelected'])) {
        foreach ($form_values['heroesSelected'] as $hid) {
          $match->field_heroes[] = $hid;
        }
      }

      if (!empty($form_values['duration'])) {
        if (preg_match("/([0-9]+):?([0-9]+)?/", $form_values['duration'])) {
          $match->field_duration->value = $form_values['duration'];
        }
      }

      if (!empty($form_values['notes'])) {
        $match->field_notes->value = $form_values['notes'];
      }

      if (!empty($form_values['mapSelected'])) {
        $match->field_map = $form_values['mapSelected'];
      }

      if (!empty($form_values['groupSize'])) {
        $match->field_group_size->value = $form_values['groupSize'];
      }

      if (!empty($form_values['matchResult'])) {
        $match->field_match_result->value = $form_values['matchResult'];
      }

      if (!empty($form_values['seasonSelected'])) {
        $match->field_season = $form_values['seasonSelected'];
      }

      if (!empty($form_values['startingSide'])) {
        $match->field_starting_side->value = $form_values['startingSide'];
      }

      if (!empty($form_values['scoreTeam']) && !empty($form_values['scoreEnemy'])) {
        $match->field_score->value = $form_values['scoreTeam'] . ':' . $form_values['scoreEnemy'];
      }

      if (!empty($form_values['isPlacement'])) {
        $match->field_is_placement->value = $form_values['isPlacement'];
      }

      // Date.
      /** @var \Drupal\Core\Datetime\DateFormatter $date_formatter */
      $date_formatter = \Drupal::service('date.formatter');
      $request_time = \Drupal::time()->getRequestTime();
      if (!empty($form_values['dateDate'])) {
        $date = $form_values['dateDate'];
      }
      else {
        $date = $date_formatter->format($request_time, 'custom', 'd.m.Y');
      }

      if (!empty($form_values['dateTime'])) {
        $time = $form_values['dateTime'];
      }
      else {
        $time = $date_formatter->format($request_time, 'custom', 'h:m');
      }

      $date_time = strtotime("$date $time");
      $match->field_date = $date_time;

      $match->validate();
      $match->save();
      ksm($match->field_heroes->getValue());
      drupal_set_message('Match was added successfully.');

      $response['statusText'] = 'Match was added successfully.';
      return new ResourceResponse($response);
    } catch (EntityStorageException $e) {
      \Drupal::logger('overwatch_match')->error($e);
      $response['statusText'] = 'Something went wrong when we try to create match.';
      return new BadRequestHttpException('Something went wrong when we try to create match.');
    }
  }

}
