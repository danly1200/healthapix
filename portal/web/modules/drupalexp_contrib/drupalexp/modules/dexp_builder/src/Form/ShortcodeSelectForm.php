<?php

namespace Drupal\dexp_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\dexp_builder\Ajax\CloseBuilderModalDialogCommand;
use Drupal\Core\Url;

class ShortcodeSelectForm extends FormBase {

  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $form['elements'] = array(
      '#type' => 'vertical_tabs',
    );
    $form['all'] = array(
      '#type' => 'details',
      '#title' => $this->t('All'),
      '#group' => 'elements',
      '#open' => TRUE,
    );
    $builderShortcodeService = \Drupal::service('dexp_builder.shortcode');
    $format = \Drupal::request()->get('format');
    $parent = \Drupal::request()->get('parent');
    $shortcode_plugins = $builderShortcodeService->getShortcodePlugins($format);
    if($parent){
      $parent_shortcode = $builderShortcodeService->getShortcodePlugin($parent);
      $parent_shortcode_definition = $parent_shortcode->getPluginDefinition();
    }
    foreach ($shortcode_plugins as $shortcode_id => $s) {
      $shortcode = $builderShortcodeService->getShortcodePlugin($shortcode_id);
      if(empty($shortcode)){    
        continue;
      }
      if(($shortcode instanceof \Drupal\dexp_builder\Plugin\Shortcode\BuilderElement) == false){
        continue;
      }
      $definition =  $shortcode->getPluginDefinition();
      //Has defined parent elements
      if(isset($definition['parent'])){
        if(!in_array($parent, $definition['parent']))
          continue;
      }
      //Has defined child elements
      if($parent){
        if(isset($parent_shortcode_definition['child'])){
          if(!in_array($shortcode_id, $parent_shortcode_definition['child']))
            continue;
        }
      }
      $el = array(
        '#type' => 'link',
        '#title' => $shortcode->getLabel(),
        '#url' => Url::fromRoute('dexp_builder.shortcode_settings', array('shortcode_id' => $shortcode_id, 'action' => 'add')),
        '#attributes' => ['class' => ['dexp-builder-modal']],
        '#prefix' => '<div class="element-wrapper">',
        '#suffix' => '</div>',
      );
      $form['all'][] = $el;
      if(isset($definition['group'])){
        $tab = $definition['group']->__toString();
        if(!isset($form[$tab])){
          $form[$tab] = array(
            '#type' => 'details',
            '#title' => $definition['group'],
            '#group' => 'elements',
          );
        }
        $form[$tab][] = $el;
      }
    }

    $form['actions'] = array(
      '#type' => 'actions',
    );
    $form['actions']['cancel'] = array(
      '#type' => 'button',
      '#value' => 'Cancel',
      '#ajax' => array(
        'callback' => '::closeModal',
      ),
    );
    return $form;
  }

  public function getFormId() {
    return 'dexp_builder_shortcode_select';
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    
  }

  public function closeModal() {
    $response = new AjaxResponse();
    $command = new CloseBuilderModalDialogCommand();
    $response->addCommand($command);
    return $response;
  }

}
