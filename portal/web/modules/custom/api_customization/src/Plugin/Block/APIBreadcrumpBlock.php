<?php

namespace Drupal\api_customization\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use \Drupal\Core\Routing;

/**
 * Provides a 'APIBreadcrumpBlock' block.
 *
 * @Block(
 *  id = "apibreadcrump_block",
 *  admin_label = @Translation("Apibreadcrump block"),
 * )
 */
class APIBreadcrumpBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get the current path
    $current_path = \Drupal::service('path.current')->getPath();
    $route_name = \Drupal::routeMatch()->getRouteName();
    $build = [];
    if ($current_path !=  '/node/add/smartdocs' && $route_name != 'entity.node.edit_form') {
      $build['#theme'] = 'apibreadcrump_block';
      $build['apibreadcrump_block']['#markup'] = '<h2>Interopereability APIs /</h2>';
      $node = \Drupal::routeMatch()->getParameter('node');
      if($node) {
        if($node->bundle() == 'smartdocs' ||
          $node->bundle() == 'oauth_swagger_api' ||
          $node->bundle() == 'interoperability_help_page') {
          $term = $node->get('field_interoperability_api_tag')->getValue();
          $target_id = $term[0]['target_id'];
          $term_obj = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($target_id);
          $breadcrumb = [];
          if($term_obj) {
            if($term_obj->get('parent')->getValue()[0]['target_id']) {
              $parent_target_id = $term_obj->get('parent')->getValue()[0]['target_id'];
              if($parent_target_id) {
                $parent_obj = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($parent_target_id);
                $breadcrumb[0] = 'Interoperability APIs';
                $breadcrumb[1] = $parent_obj->get('name')->getValue()[0]['value'];
                $breadcrumb[2] = $term_obj->get('name')->getValue()[0]['value'];
              }
            } else {
              $breadcrumb[0] = 'Interoperability APIs';
              $breadcrumb[1] = $term_obj->get('name')->getValue()[0]['value'];

            }
            if($breadcrumb) {
              $breadcrumb_str = implode(' / ', $breadcrumb);
              $build['apibreadcrump_block']['#markup'] = '<h2>'. $breadcrumb_str .'</h2>';
            } else {
              $build['apibreadcrump_block']['#markup'] = '<h2>Interoperability APIs</h2>';
            }
          } else {
            $build['apibreadcrump_block']['#markup'] = '<h2>Interoperability APIs</h2>';
          }
        }
      }
    }

    return $build;
  }

  public function getCacheMaxAge() {
    return 0;
  }

}
