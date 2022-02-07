<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\views\Views;

/**
 * Provides a shortcode for bootstrap row.
 *
 * @Shortcode(
 *   id = "dexp_builder_view_embed",
 *   title = @Translation("View Embed"),
 *   description = @Translation("Embed view"),
 *   group = @Translation("Content"),
 *   child = {},
 * )
 */
class BuilderViewEmbed extends BuilderElement {
  
  function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array('view' => '', 'params' => ''), $attributes);
    $value = explode(':', $attrs['view']);
    $view = Views::getView($value[0]);
    if($view){
      $params = explode('/', $attrs['params']);
      $output = $view->buildRenderable($value[1], $params);
      $output['#view_id'] = $value[0];
      $output['#view_display_show_admin_links'] = $view->getShowAdminLinks();
      $output['#view_display_plugin_id'] = $view->display_handler->getPluginId();
      views_add_contextual_links($output,'block',$value[1]);
      return $this->render($output);
    }
    return '';
  }

  function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED){
    $attrs = $this->getAttributes(array('view' => ''), $attributes);
    $options = [];
    foreach (\Drupal\views\Views::getAllViews() as $view){
      //$options[$view->label()] = [];
      foreach($view->get('display') as $display){
        if($display['id'] != 'default'){
          $options[$view->id() . ':' . $display['id']] = $view->label() . ': ' .$display['display_title'];
        }
      }
    }
    return '[ ' . $options[$attrs['view']] . ' ]';
  }
  
  function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $options = [];
    foreach (\Drupal\views\Views::getAllViews() as $view){
      //$options[$view->label()] = [];
      foreach($view->get('display') as $display){
        if($display['id'] != 'default'){
          $options[$view->label()][$view->id() . ':' . $display['id']] = $view->label() . ': ' .$display['display_title'];
        }
      }
    }
    $form['general_options']['view'] = array(
      '#title' => $this->t('Select View'),
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $this->get('view'),
      '#description' => $this->t('Select view you want embed from the list.'),
    );
    $form['general_options']['params'] = array(
      '#title' => $this->t('Params'),
      '#type' => 'textfield',
      '#default_value' => $this->get('params'),
      '#description' => $this->t('Parametters pass to contextual filters. For example: arg1/arg2'),
    );
    unset($form['design_options']);
    unset($form['animate_options']);
    return $form;
  }

}
