<?php

namespace Drupal\overwatch_map\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\overwatch_map\Entity\OverwatchMapInterface;

/**
 * Class OverwatchMapController.
 *
 *  Returns responses for Overwatch map routes.
 */
class OverwatchMapController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Overwatch map  revision.
   *
   * @param int $overwatch_map_revision
   *   The Overwatch map  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($overwatch_map_revision) {
    $overwatch_map = $this->entityManager()->getStorage('overwatch_map')->loadRevision($overwatch_map_revision);
    $view_builder = $this->entityManager()->getViewBuilder('overwatch_map');

    return $view_builder->view($overwatch_map);
  }

  /**
   * Page title callback for a Overwatch map  revision.
   *
   * @param int $overwatch_map_revision
   *   The Overwatch map  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($overwatch_map_revision) {
    $overwatch_map = $this->entityManager()->getStorage('overwatch_map')->loadRevision($overwatch_map_revision);
    return $this->t('Revision of %title from %date', ['%title' => $overwatch_map->label(), '%date' => format_date($overwatch_map->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Overwatch map .
   *
   * @param \Drupal\overwatch_map\Entity\OverwatchMapInterface $overwatch_map
   *   A Overwatch map  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(OverwatchMapInterface $overwatch_map) {
    $account = $this->currentUser();
    $langcode = $overwatch_map->language()->getId();
    $langname = $overwatch_map->language()->getName();
    $languages = $overwatch_map->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $overwatch_map_storage = $this->entityManager()->getStorage('overwatch_map');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $overwatch_map->label()]) : $this->t('Revisions for %title', ['%title' => $overwatch_map->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all overwatch map revisions") || $account->hasPermission('administer overwatch map entities')));
    $delete_permission = (($account->hasPermission("delete all overwatch map revisions") || $account->hasPermission('administer overwatch map entities')));

    $rows = [];

    $vids = $overwatch_map_storage->revisionIds($overwatch_map);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\overwatch_map\OverwatchMapInterface $revision */
      $revision = $overwatch_map_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $overwatch_map->getRevisionId()) {
          $link = $this->l($date, new Url('entity.overwatch_map.revision', ['overwatch_map' => $overwatch_map->id(), 'overwatch_map_revision' => $vid]));
        }
        else {
          $link = $overwatch_map->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.overwatch_map.translation_revert', ['overwatch_map' => $overwatch_map->id(), 'overwatch_map_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.overwatch_map.revision_revert', ['overwatch_map' => $overwatch_map->id(), 'overwatch_map_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.overwatch_map.revision_delete', ['overwatch_map' => $overwatch_map->id(), 'overwatch_map_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['overwatch_map_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
