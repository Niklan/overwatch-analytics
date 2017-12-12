<?php

namespace Drupal\overwatch_map;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Overwatch map entity.
 *
 * @see \Drupal\overwatch_map\Entity\OverwatchMap.
 */
class OverwatchMapAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\overwatch_map\Entity\OverwatchMapInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished overwatch map entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published overwatch map entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit overwatch map entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete overwatch map entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add overwatch map entities');
  }

}
