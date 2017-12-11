<?php

namespace Drupal\overwatch_match\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\overwatch_match\Entity\OverwatchMatchInterface;

/**
 * Class OverwatchMatchController.
 *
 *  Returns responses for Overwatch match routes.
 */
class OverwatchMatchController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Overwatch match  revision.
   *
   * @param int $overwatch_match_revision
   *   The Overwatch match  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($overwatch_match_revision) {
    $overwatch_match = $this->entityManager()->getStorage('overwatch_match')->loadRevision($overwatch_match_revision);
    $view_builder = $this->entityManager()->getViewBuilder('overwatch_match');

    return $view_builder->view($overwatch_match);
  }

  /**
   * Page title callback for a Overwatch match  revision.
   *
   * @param int $overwatch_match_revision
   *   The Overwatch match  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($overwatch_match_revision) {
    $overwatch_match = $this->entityManager()->getStorage('overwatch_match')->loadRevision($overwatch_match_revision);
    return $this->t('Revision of %title from %date', ['%title' => $overwatch_match->label(), '%date' => format_date($overwatch_match->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Overwatch match .
   *
   * @param \Drupal\overwatch_match\Entity\OverwatchMatchInterface $overwatch_match
   *   A Overwatch match  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(OverwatchMatchInterface $overwatch_match) {
    $account = $this->currentUser();
    $langcode = $overwatch_match->language()->getId();
    $langname = $overwatch_match->language()->getName();
    $languages = $overwatch_match->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $overwatch_match_storage = $this->entityManager()->getStorage('overwatch_match');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $overwatch_match->label()]) : $this->t('Revisions for %title', ['%title' => $overwatch_match->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all overwatch match revisions") || $account->hasPermission('administer overwatch match entities')));
    $delete_permission = (($account->hasPermission("delete all overwatch match revisions") || $account->hasPermission('administer overwatch match entities')));

    $rows = [];

    $vids = $overwatch_match_storage->revisionIds($overwatch_match);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\overwatch_match\OverwatchMatchInterface $revision */
      $revision = $overwatch_match_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $overwatch_match->getRevisionId()) {
          $link = $this->l($date, new Url('entity.overwatch_match.revision', ['overwatch_match' => $overwatch_match->id(), 'overwatch_match_revision' => $vid]));
        }
        else {
          $link = $overwatch_match->link($date);
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
              Url::fromRoute('entity.overwatch_match.translation_revert', ['overwatch_match' => $overwatch_match->id(), 'overwatch_match_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.overwatch_match.revision_revert', ['overwatch_match' => $overwatch_match->id(), 'overwatch_match_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.overwatch_match.revision_delete', ['overwatch_match' => $overwatch_match->id(), 'overwatch_match_revision' => $vid]),
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

    $build['overwatch_match_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
