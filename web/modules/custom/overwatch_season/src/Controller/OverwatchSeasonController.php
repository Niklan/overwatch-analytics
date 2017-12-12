<?php

namespace Drupal\overwatch_season\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\overwatch_season\Entity\OverwatchSeasonInterface;

/**
 * Class OverwatchSeasonController.
 *
 *  Returns responses for Overwatch season routes.
 */
class OverwatchSeasonController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Overwatch season  revision.
   *
   * @param int $overwatch_season_revision
   *   The Overwatch season  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($overwatch_season_revision) {
    $overwatch_season = $this->entityManager()->getStorage('overwatch_season')->loadRevision($overwatch_season_revision);
    $view_builder = $this->entityManager()->getViewBuilder('overwatch_season');

    return $view_builder->view($overwatch_season);
  }

  /**
   * Page title callback for a Overwatch season  revision.
   *
   * @param int $overwatch_season_revision
   *   The Overwatch season  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($overwatch_season_revision) {
    $overwatch_season = $this->entityManager()->getStorage('overwatch_season')->loadRevision($overwatch_season_revision);
    return $this->t('Revision of %title from %date', ['%title' => $overwatch_season->label(), '%date' => format_date($overwatch_season->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Overwatch season .
   *
   * @param \Drupal\overwatch_season\Entity\OverwatchSeasonInterface $overwatch_season
   *   A Overwatch season  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(OverwatchSeasonInterface $overwatch_season) {
    $account = $this->currentUser();
    $langcode = $overwatch_season->language()->getId();
    $langname = $overwatch_season->language()->getName();
    $languages = $overwatch_season->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $overwatch_season_storage = $this->entityManager()->getStorage('overwatch_season');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $overwatch_season->label()]) : $this->t('Revisions for %title', ['%title' => $overwatch_season->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all overwatch season revisions") || $account->hasPermission('administer overwatch season entities')));
    $delete_permission = (($account->hasPermission("delete all overwatch season revisions") || $account->hasPermission('administer overwatch season entities')));

    $rows = [];

    $vids = $overwatch_season_storage->revisionIds($overwatch_season);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\overwatch_season\OverwatchSeasonInterface $revision */
      $revision = $overwatch_season_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $overwatch_season->getRevisionId()) {
          $link = $this->l($date, new Url('entity.overwatch_season.revision', ['overwatch_season' => $overwatch_season->id(), 'overwatch_season_revision' => $vid]));
        }
        else {
          $link = $overwatch_season->link($date);
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
              Url::fromRoute('entity.overwatch_season.translation_revert', ['overwatch_season' => $overwatch_season->id(), 'overwatch_season_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.overwatch_season.revision_revert', ['overwatch_season' => $overwatch_season->id(), 'overwatch_season_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.overwatch_season.revision_delete', ['overwatch_season' => $overwatch_season->id(), 'overwatch_season_revision' => $vid]),
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

    $build['overwatch_season_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
