<?php

namespace Drupal\overwatch_match;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Overwatch match entity.
 *
 * @see \Drupal\overwatch_match\Entity\OverwatchMatch.
 */
class OverwatchMatchAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\overwatch_match\Entity\OverwatchMatchInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished overwatch match entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published overwatch match entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit overwatch match entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete overwatch match entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add overwatch match entities');
  }

}
