<?php

/**
 * @file
 * Contains \Drupal\dexp_layerslider\SliderAccessControlHandler.
 */

namespace Drupal\dexp_layerslider;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Slider entity.
 *
 * @see \Drupal\dexp_layerslider\Entity\Slider.
 */
class SliderAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view slider entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit slider entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete slider entities');
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add slider entities');
  }

}
