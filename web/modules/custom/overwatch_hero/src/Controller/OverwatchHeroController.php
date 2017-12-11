<?php

namespace Drupal\overwatch_hero\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\overwatch_hero\Entity\OverwatchHeroInterface;

/**
 * Class OverwatchHeroController.
 *
 *  Returns responses for Overwatch hero routes.
 */
class OverwatchHeroController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Overwatch hero  revision.
   *
   * @param int $overwatch_hero_revision
   *   The Overwatch hero  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($overwatch_hero_revision) {
    $overwatch_hero = $this->entityManager()->getStorage('overwatch_hero')->loadRevision($overwatch_hero_revision);
    $view_builder = $this->entityManager()->getViewBuilder('overwatch_hero');

    return $view_builder->view($overwatch_hero);
  }

  /**
   * Page title callback for a Overwatch hero  revision.
   *
   * @param int $overwatch_hero_revision
   *   The Overwatch hero  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($overwatch_hero_revision) {
    $overwatch_hero = $this->entityManager()->getStorage('overwatch_hero')->loadRevision($overwatch_hero_revision);
    return $this->t('Revision of %title from %date', ['%title' => $overwatch_hero->label(), '%date' => format_date($overwatch_hero->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Overwatch hero .
   *
   * @param \Drupal\overwatch_hero\Entity\OverwatchHeroInterface $overwatch_hero
   *   A Overwatch hero  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(OverwatchHeroInterface $overwatch_hero) {
    $account = $this->currentUser();
    $langcode = $overwatch_hero->language()->getId();
    $langname = $overwatch_hero->language()->getName();
    $languages = $overwatch_hero->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $overwatch_hero_storage = $this->entityManager()->getStorage('overwatch_hero');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $overwatch_hero->label()]) : $this->t('Revisions for %title', ['%title' => $overwatch_hero->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all overwatch hero revisions") || $account->hasPermission('administer overwatch hero entities')));
    $delete_permission = (($account->hasPermission("delete all overwatch hero revisions") || $account->hasPermission('administer overwatch hero entities')));

    $rows = [];

    $vids = $overwatch_hero_storage->revisionIds($overwatch_hero);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\overwatch_hero\OverwatchHeroInterface $revision */
      $revision = $overwatch_hero_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $overwatch_hero->getRevisionId()) {
          $link = $this->l($date, new Url('entity.overwatch_hero.revision', ['overwatch_hero' => $overwatch_hero->id(), 'overwatch_hero_revision' => $vid]));
        }
        else {
          $link = $overwatch_hero->link($date);
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
              Url::fromRoute('entity.overwatch_hero.translation_revert', ['overwatch_hero' => $overwatch_hero->id(), 'overwatch_hero_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.overwatch_hero.revision_revert', ['overwatch_hero' => $overwatch_hero->id(), 'overwatch_hero_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.overwatch_hero.revision_delete', ['overwatch_hero' => $overwatch_hero->id(), 'overwatch_hero_revision' => $vid]),
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

    $build['overwatch_hero_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
