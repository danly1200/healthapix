<?php

namespace Drupal\api_customization\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a 'InteroperabilityAPIBlock' block.
 *
 * @Block(
 *  id = "interoperability_apiblock",
 *  admin_label = @Translation("Interoperability apiblock"),
 * )
 */
class InteroperabilityAPIBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'interoperability_apiblock';
    $vid = 'interoperability_api_s';
    $html = '';
    $terms = $this->load($vid);
    $current_path = \Drupal::service('path.current')->getPath();
    $node_from_url = \Drupal::routeMatch()->getParameter('node');
    $expand_term_id = $node_from_url->get('field_interoperability_api_tag')->getValue()[0]['target_id'];
    if ($terms) {
      $html = '<div id="accordian"> <ul class="api-accordion">';
      foreach($terms as $each_term) {
        if($each_term->status == 1) {
          $term_obj = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($each_term->tid);
          $content_link = '#';
          if(isset($term_obj->get('field_api_content_link')->getValue()[0])) {
            $link_uri = $term_obj->get('field_api_content_link')->getValue()[0]['uri'];
            if($link_uri) {
              $content_link = Url::fromUri($link_uri)->toString();
            } else {
              $content_link = '#';
            }
          }

          if($each_term->depth == 0) {
            // check if there are first level nodes
            $first_leve_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
              'field_interoperability_api_tag' => $each_term->tid,
            ]);
            // li open
            if((isset($each_term->children) && !empty($each_term->children)) || $first_leve_nodes) {
              $html .= '<li class="first-level">
                         <h2>
                        <a href="' .$content_link .'"> ' . $each_term->name .'</a><span class="plus"> &nbsp; </span>
                     </h2>
                   ';
            } else {
              $html .= '<li class="first-level">
                         <h2>
                        <a href="' .$content_link .'"> ' . $each_term->name .'</a>
                     </h2>
                   ';
            }

            if(isset($each_term->children) && !empty($each_term->children)) {
              $html .= '<ul class="one-inner-api-accordion">';
              foreach ($each_term->children as $each_children){
                $child_term_obj = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($each_children->tid);
                $inner_content_link = '#';
                if(isset($child_term_obj->get('field_api_content_link')->getValue()[0])) {
                  $inner_link_uri = $child_term_obj->get('field_api_content_link')->getValue()[0]['uri'];
                  if($inner_link_uri) {
                    $inner_content_link = Url::fromUri($inner_link_uri)->toString();
                  } else {
                    $inner_content_link = '#';
                  }
                }
                $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
                  'field_interoperability_api_tag' => $each_children->tid,
                ]);
                usort($nodes,"str_cmp");
                if($nodes) {
                  $html .= '<li><a href="'. $inner_content_link .'"> '. $each_children->name .' </a> <span class="plus"> &nbsp;  </span>';
                } else {
                  $html .= '<li><a href="'. $inner_content_link .'"> '. $each_children->name .' </a> ';
                }

                if($nodes) {
                  $html .= '<ul class="two-inner-api-accordion">';

                  foreach($nodes as $each_node) {
                    if($each_node->bundle() == 'smartdocs' || $each_node->bundle() == 'oauth_swagger_api') {
                      $url_alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $each_node->id());
                      $title_from_url = $node_from_url->getTitle();
                      $expand_term_from_node = $each_node->get('field_interoperability_api_tag')->getValue()[0]['target_id'];
                      if($expand_term_id == $expand_term_from_node && $title_from_url == $each_node->getTitle()) {
                        $active = 'active';
                      } else {
                        $active = '';
                      }
                      $html .= '<li class="api-content-link '. $active .' "><a  href="' . $url_alias . '"> ' . $each_node->getTitle() . ' </a> <i class="material-icons">navigate_next</i></li>';
                    }
                  }
                  $html .= '</li></ul>';
                }
              }

              $html .= '</ul>';
            }

            if($first_leve_nodes) {
              $html .= '<ul class="one-inner-api-accordion">';
              usort($first_leve_nodes,"str_cmp");
              foreach($first_leve_nodes as $each_node) {
                if($each_node->bundle() == 'smartdocs' || $each_node->bundle() == 'oauth_swagger_api') {
                  $url_alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/'. $each_node->id());
                  $title_from_url = $node_from_url->getTitle();
                  $expand_term_from_node = $each_node->get('field_interoperability_api_tag')->getValue()[0]['target_id'];
                  if($expand_term_id == $expand_term_from_node && $title_from_url == $each_node->getTitle()) {
                    $active = 'active';
                  } else {
                    $active = '';
                  }
                  $html .= '<li class="api-content-link '. $active .'"><a  href="'. $url_alias .'"> '. $each_node->getTitle() .' </a><i class="material-icons">navigate_next</i> </li>';
                }
              }
              $html .= '</li></ul>';
            }
            // li close
            $html .= '</li>';
          }
        }
      }
      $html .= '</ul> </div>';
    }

    $build['#markup'] = $html;

    return $build;
  }

  public function getCacheMaxAge() {
    return 0;
  }

  public function load($vocabulary) {
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vocabulary);
    $tree = [];
    foreach ($terms as $tree_object) {
      if($tree_object->status == 1) {
        $this->buildTree($tree, $tree_object, $vocabulary);
      }
    }

    return $tree;
  }

  /**
   * Populates a tree array given a taxonomy term tree object.
   *
   * @param $tree
   * @param $object
   * @param $vocabulary
   */
  protected function buildTree(&$tree, $object, $vocabulary) {
    if ($object->depth != 0) {
      return;
    }
    $tree[$object->tid] = $object;
    $tree[$object->tid]->children = [];
    $object_children = &$tree[$object->tid]->children;

    $children = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadChildren($object->tid);
    if (!$children) {
      return;
    }

    $child_tree_objects = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vocabulary, $object->tid);

    foreach ($children as $child) {
      foreach ($child_tree_objects as $child_tree_object) {
        if ($child_tree_object->tid == $child->id()) {
          $this->buildTree($object_children, $child_tree_object, $vocabulary);
        }
      }
    }
  }

}
