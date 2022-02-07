<?php

namespace Drupal\fhir_rebranding\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Cache\Cache;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "header_right_block",
 *   admin_label = @Translation("Header Right side Block"),
 * )
 */
class RightBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $get_user = \Drupal::currentUser()->id();
    $user_mail = '';
    $user_current = User::load($get_user);
    if (isset($user_current->get('mail')->value)) {
      $user_mail = substr($user_current->get('mail')->value, 0, 16) . '...';
    }
    return [
      'user_mail_clipped' => $user_mail,
      'user_id' => $get_user,
    ];
  }

  /**
   * {@inheritdoc}
   * Add cache tag for current user
   */
  public function getCacheTags() {
    if ($user = \Drupal::currentUser()) {
      return Cache::mergeTags(parent::getCacheTags(), ['user:' . $user->id()]);
    }
    else {
      return parent::getCacheTags();
    }
  }
  /**
   * {@inheritdoc}
   * Add cache context for user
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['user']);
  }
}
