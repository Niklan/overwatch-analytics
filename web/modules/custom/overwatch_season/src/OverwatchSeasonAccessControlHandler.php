<?php

namespace Drupal\overwatch_season;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Overwatch season entity.
 *
 * @see \Drupal\overwatch_season\Entity\OverwatchSeason.
 */
class OverwatchSeasonAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\overwatch_season\Entity\OverwatchSeasonInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished overwatch season entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published overwatch season entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit overwatch season entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete overwatch season entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add overwatch season entities');
  }

}
