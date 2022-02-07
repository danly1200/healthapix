<?php
/**
 * @file
 * Contains \Drupal\fhir_blocks\Plugin\Block\FhirCopyright.
 */

namespace Drupal\fhir_customizations\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\node\Entity\Node;

/**
 * Provides a 'OAuth API List' block.
 *
 * @Block(
 *   id = "oauth_api_list",
 *   admin_label = @Translation("OAuth API List"),
 *   category = @Translation("OAuth API List Block")
 * )
 */
class OAuthApiList extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    //Getting the current node details
    $current_url = Url::fromRoute('<current>');
    $path = $current_url->getInternalPath();
    if ($path != 'node/add/smartdocs') {
      $node = \Drupal::routeMatch()->getParameter('node');

      if ($node instanceof \Drupal\node\NodeInterface) {
        // You can get nid and anything else you need from the node object.

        if($node->bundle() == 'oauth_swagger_api') {
          $api_doc_id = \Drupal::request()->query->get('api_doc_id');
          if ($api_doc_id) {
            $current_nid = $node->id();
          }
        } else if ($node->bundle() == 'smartdocs') {
          $api_doc_id = $node->id();
          $current_nid = '';
        }
      } else {
        if (\Drupal::routeMatch()
          ->getRouteName('fhir_customizations.swagger_not_found')) {
          $current_nid = \Drupal::routeMatch()->getParameters()->get('nid');
        }
      }
      $node_data = $this->getOAuthApiList();

      $swagger_link_theme = $this->swaggerApiListTheme($node_data, $api_doc_id, $current_nid);

      $data = [
        '#markup' => $swagger_link_theme,
      ];

      return $data;
    }
  }

  public function getCacheTags() {
    //With this when your node change your block will rebuild
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      //if there is node add its cachetag
      return Cache::mergeTags(parent::getCacheTags(), ['node:' . $node->id()]);
    }
    else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }

  public function getCacheContexts() {
    //if you depends on \Drupal::routeMatch()
    //you must set context of this block with 'route' context tag.
    //Every new route this block will rebuild
    return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
  }

  public function getOAuthApiList() {

    $connection = \Drupal::database();
    $query = $connection->select('node_field_data', 'n');
    //$query->join('node__field_api_version', 'v', 'n.nid = v.entity_id');
    $query->fields('n', ['nid', 'title']);
    $query->condition('n.status', 1, '=');
    //$query->condition('v.field_api_version_target_id', $node_term_id, '=');
    $query->condition('n.type', 'oauth_swagger_api', '=');
    $query->orderBy('n.created', 'DESC');
    $node_data = $query->execute()->fetchAll();

    return $node_data;
  }

  public function swaggerApiListTheme($node_data, $api_doc_id,$current_nid) {

    $output = '<h2 class="swagger-api-list-header">' . t("OAuth APIs") . '</h2>';
    $output .= '<ul class="swagger-api-list-links">';
    foreach ($node_data as $key => $val) {
      $options = ['absolute' => TRUE];

      $node_title = $node_data[$key]->title;
      $nid = $node_data[$key]->nid;

      $link = Link::fromTextAndUrl($node_title, Url::fromRoute('entity.node.canonical', ['node' => $nid,'api_doc_id'=>$api_doc_id], $options))
        ->toString();

      $link_class = ($nid == $current_nid) ? 'swagger-api-list-link-active swagger-api-list-link' : 'swagger-api-tab';
      $output .= '<li class="' . $link_class . '">' . $link . '</li>';
    }
    $output .= '</ul>';

    return $output;

  }

  public function getCacheMaxAge() {
    return 0;
  }

}
