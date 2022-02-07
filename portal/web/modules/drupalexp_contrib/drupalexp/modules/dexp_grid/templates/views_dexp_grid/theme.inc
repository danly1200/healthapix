<?php

use Drupal\Component\Utility\Html;
use Drupal\Core\Template\Attribute;
use Drupal\file\Entity\File;

/**
 * Preprocess function to build the dexp views grid
 */
function template_preprocess_views_dexp_grid(&$vars) {
  //dsm($vars['view']->id());
  $id = Html::getUniqueId($vars['view']->id() . '-' . $vars['view']->current_display);
  $uuid_service = \Drupal::service('uuid');
  $uuid = $uuid_service->generate();
  $vars['id'] = $id;
  $vars['attributes']['data-uuid'] = $uuid;
  $options = $vars['view']->style_plugin->options;
  // Carousel options.
  $vars['lg_cols'] = $options['lg_cols'];
  $vars['md_cols'] = $options['md_cols'];
  $vars['sm_cols'] = $options['sm_cols'];
  $vars['xs_cols'] = $options['xs_cols'];
  $items = array();
  // Iterate over each rendered views result row.
  $vars['attributes']['class'][] = 'dexp-grid';
  $vars['attributes']['id'] = $id;
  // Get filters
  $filters = [];
  if($options['grid_filter'] && $options['grid_filter_vocabulary']){
    $filter = [];
    foreach(\Drupal::entityManager()->getStorage('taxonomy_term')->loadTree($options['grid_filter_vocabulary']) as $term){
      $term->data_filter = Html::getClass($term->name);
      $filter[] = $term;
    }
    $filters[] = $filter;
    $vars['attributes']['class'][] = 'dexp-grid-filter';
    $vars['#attached']['library'][] = 'dexp_grid/grid_filter';
  }
  //$vars['filters'] = $filters;
  $vars['show_all'] = t($options['grid_filter_show_all_text']);
  foreach ($vars['rows'] as $result_index => $item) {
    $entity = $vars['view']->result[$result_index]->_entity;
    //dsm($entity);
    $row_attributes = array('class' => array('dexp-grid-item'));
    if(!empty($filters)){
      $results = db_query('select t.tid FROM {taxonomy_index} t LEFT JOIN {taxonomy_term_data} tt ON t.tid = tt.tid WHERE t.nid = :nid AND tt.vid = :vid', array(':vid' => $options['grid_filter_vocabulary'], ':nid' => $entity->id()));
      foreach ($results as $result) {
        $term = taxonomy_term_load($result->tid);
        $row_attributes['class'][] =  Html::getClass($term->getName());
      }
      if(get_class($entity) == 'Drupal\commerce_product\Entity\Product'){
        //Do something
      }
    }
    if ($options['grid_style'] == 'masonry_resize') {
      if($background_field = $options['gird_masonry_background']){
        $images = $entity->get($background_field)->getValue();
        if($images){
          $fid = $images[0]['target_id'];
          $image = File::load($fid);
          $row_attributes['style'][] = 'background-image: url(\'' . file_create_url($image->getFileUri()) . '\');';
        }
      }
      $row_attributes['data-index'] = $result_index;
      $config = \Drupal::service('config.factory')->getEditable('dexp_grid.config');
      $row_settings = $config->get($vars['view']->element['#name'] . '.' . $vars['view']->element['#display_id'] . '.' . $result_index);
      if(empty($row_settings)){
        $row_settings = array('width'=>1,'height'=>1);
      }
      $row_attributes['data-grid-width'] = $row_settings['width'];
      $row_attributes['data-grid-height'] = $row_settings['height'];
    }
    $items[] = array(
      'content' => $item,
      'attributes' => new Attribute($row_attributes),
    );
  }
  $vars['#attached']['library'][] = 'dexp_grid/grid_masonry';
  $vars['items'] = $items;
}