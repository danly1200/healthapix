<?php
/**
 * @file
 * Preprocessors and helper functions to make theming easier.
 */

use Drupal\Component\Utility\Html;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;

/**
 * Prepares variables for views grid template.
 *
 * Default template: views-dexp-grid-bootstrap.html.twig.
 *
 * @param array $vars
 *   An associative array containing:
 *   - view: A ViewExecutable object.
 *   - rows: The raw row data.
 */
function template_preprocess_views_dexp_grid_bootstrap(&$vars) {
  $id = Html::getUniqueId($vars['view']->id() . '-' . $vars['view']->current_display);
  $vars['id'] = $id;
  $vars['attributes']['id'] = $id;
  $options = $vars['view']->style_plugin->options;
  // Carousel options.
  $vars['xl_cols'] = $options['xl_cols'];
  $vars['lg_cols'] = $options['lg_cols'];
  $vars['md_cols'] = $options['md_cols'];
  $vars['sm_cols'] = $options['sm_cols'];
  $vars['xs_cols'] = $options['xs_cols'];
  $padding = $options['col_padding'] === ''?30:$options['col_padding'];
  $items = array();
  // Iterate over each rendered views result row.
  $vars['attributes']['class'][] = 'dexp-grid';
  $vars['attributes']['class'][] = $options['wrapper_class'];
  $vars['attributes']['class'][] = 'dexp-grid-bootstrap';
  $vars['attributes']['class'][] = 'dexp-grid-bootstrap-xl-' . $vars['xl_cols'];
  $vars['attributes']['class'][] = 'dexp-grid-bootstrap-lg-' . $vars['lg_cols'];
  $vars['attributes']['class'][] = 'dexp-grid-bootstrap-md-' . $vars['md_cols'];
  $vars['attributes']['class'][] = 'dexp-grid-bootstrap-sm-' . $vars['sm_cols'];
  $vars['attributes']['class'][] = 'dexp-grid-bootstrap-xs-' . $vars['xs_cols'];
  $vars['wrapper_row'] = $options['wrapper_row'];
  $vars['wrapper_row_class'] = $options['wrapper_row_class'];
  foreach ($vars['rows'] as $result_index => $item) {
    $row_attributes = array('class' => array('dexp-grid-item'));

    if(12 % $vars['xl_cols'] == 0){
      $row_attributes['class'][] = 'col-xl-' . 12 / $vars['xl_cols'];
    }else{
      $row_attributes['class'][] = 'col-xl';
    }
    if(12 % $vars['lg_cols'] == 0){
      $row_attributes['class'][] = 'col-lg-' . 12 / $vars['lg_cols'];
    }else{
      $row_attributes['class'][] = 'col-lg';
    }
    if(12 % $vars['md_cols'] == 0){
      $row_attributes['class'][] = 'col-md-' . 12 / $vars['md_cols'];
    }else{
      $row_attributes['class'][] = 'col-md';
    }
    if(12 % $vars['sm_cols'] == 0){
      $row_attributes['class'][] = 'col-sm-' . 12 / $vars['sm_cols'];
    }else{
      $row_attributes['class'][] = 'col-sm';
    }
    if(12 % $vars['xs_cols'] == 0){
      $row_attributes['class'][] = 'col-' . 12 / $vars['xs_cols'];
    }else{
      $row_attributes['class'][] = 'col';
    }
    $row_inner_attributes = [];//new \Drupal\Core\Template\Attribute();
    $row_inner_attributes['class'][] = 'dexp-grid-inner';
    $row_inner_attributes['class'][] = 'row';
    if($padding == 0){
      $row_inner_attributes['class'][] = 'no-gutters';
    }elseif($padding != 30){
      //$vars['attributes']['style'][] = 'margin-left: -' . $padding/2 . 'px;';
      //$vars['attributes']['style'][] = 'margin-right: -' . $padding/2 . 'px;';
      $row_inner_attributes['style'][] = 'margin-left: -' . $padding/2 . 'px;';
      $row_inner_attributes['style'][] = 'margin-right: -' . $padding/2 . 'px;';
      $row_attributes['style'][] = 'padding-left:' . $padding/2 . 'px;';
      $row_attributes['style'][] = 'padding-right:' . $padding/2 . 'px;';
    }
    $row_attributes['style'][] = 'margin-bottom:' . $padding . 'px;';
    //$vars['attributes']['style'][] = 'margin-bottom:-' . ($padding * 2) . 'px;';
    //$row_inner_attributes['style'][] = 'margin-bottom:-' . $padding . 'px;';
    $items[] = array(
      'content' => $item,
      'attributes' => new Attribute($row_attributes),
    );
  }
  $vars['items'] = $items;
  $vars['#attached']['library'][] = 'dexp_grid/grid_bootstrap';
  $vars['row_inner_attributes'] = new Attribute($row_inner_attributes);
}
