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
 * Provides a 'FHIR' block.
 *
 * @Block(
 *   id = "swagger_api_tabs",
 *   admin_label = @Translation("Swagger API Tabs"),
 *   category = @Translation("Swagger API Tabs Block")
 * )
 */
class SwaggerAPITabs extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {


    $current_url = Url::fromRoute('<current>');
    $path = $current_url->getInternalPath();
    $path_args = explode('/', $path);

    if ($path != 'node/add/smartdocs'){
      //Getting the current node details
      $node = \Drupal::routeMatch()->getParameter('node');
      if ($node instanceof \Drupal\node\NodeInterface) {
        $current_nid = $node->id();
        $current_node_title = $node->label();
      }
      else {
        if (\Drupal::routeMatch()
          ->getRouteName('fhir_customizations.swagger_not_found')) {

          $current_nid = \Drupal::routeMatch()->getParameters()->get('nid');
          if ($current_nid) {
            $current_node_data = Node::load($current_nid);
            $current_node_title = $current_node_data->label();
          }
        }
      }
      if($path_args[2] != 'edit') {
        $swagger_mapped_data = $this->getSwaggerMappedData($current_nid, $current_node_title);
        $swagger_link_theme = $this->swaggerLinkTheme($current_nid, $swagger_mapped_data);
        $data = [
          '#type' => 'markup',
          '#markup' => $swagger_link_theme,
        ];
        return $data;
      }
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

  public function getSwaggerMappedData($current_nid, $current_node_title) {

    //Getting the api version taxonomy data
    $vid = 'api_version';

    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vid, 0, 1, FALSE);
    foreach ($terms as $term) {

      $term_data[$term->tid] = $term->name;
      $term_ids[] = $term->tid;
    }

    //Getting the nodes mapped with the current node considering the title will be same.
    //If Title cannot be considered for comparison, have to add a new field in the content type to get the mapped nids.
    $connection = \Drupal::database();
    $query = $connection->select('node_field_data', 'n');
    $query->join('node__field_api_version', 'v', 'n.nid = v.entity_id');
    $query->addField('n', 'nid', 'node_id');
    $query->addField('v', 'field_api_version_target_id', 'tid');
    $query->condition('n.status', 1, '=');
    $query->condition('n.title', $current_node_title, 'LIKE');
    $query->condition('v.bundle', 'smartdocs', '=');
    if ($term_ids) {
      $query->condition('v.field_api_version_target_id', $term_ids, 'IN');
    }
    $result = $query->execute()->fetchAll();

    $output = ['term_data' => $term_data, 'mapped_data' => $result];

    return $output;
  }

  public function swaggerLinkTheme($current_nid, $swagger_mapped_data) {

    $mapped_data = $swagger_mapped_data['mapped_data'];
    $term_data = $swagger_mapped_data['term_data'];

    $mapped_nodes = [];
    foreach ($mapped_data as $key => $val) {
      $mapped_nodes[$mapped_data[$key]->tid] = $mapped_data[$key]->node_id;
    }

    $output = '<ul class="swagger-api-tabs">';
    foreach ($term_data as $key => $val) {
      $options = ['absolute' => TRUE];
      //If mapped node is not found, passing the swagger not found page.
      $mapped_node = 0;
      if (isset($mapped_nodes[$key])) {
        $mapped_node = $mapped_nodes[$key];
        $link = Link::fromTextAndUrl($val, Url::fromRoute('entity.node.canonical', ['node' => $mapped_node], $options))
          ->toString();
      }
      else {
        $link = Link::fromTextAndUrl($val, Url::fromRoute('fhir_customizations.swagger_not_found', ['nid' => $current_nid]))
          ->toString();
      }
      $link_class = ($mapped_node == $current_nid) ? 'swagger-api-tab-active swagger-api-tab' : 'swagger-api-tab';
      $output .= '<li class="' . $link_class . '">' . $link . '</li>';
    }
    $output .= '</ul>';


    return $output;

  }

  public function getCacheMaxAge() {
    return 0;
  }


}
