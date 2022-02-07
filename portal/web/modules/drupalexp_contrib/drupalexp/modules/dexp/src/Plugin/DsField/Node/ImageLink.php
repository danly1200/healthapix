<?php

namespace Drupal\dexp\Plugin\DsField\Node;

use Drupal\file\Entity\File;
use Drupal\ds\Plugin\DsField\DsFieldBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin that renders the 'read more' link of a node.
 *
 * @DsField(
 *   id = "node_image_link",
 *   title = @Translation("Image Link"),
 *   entity_type = "node",
 *   provider = "node"
 * )
 */
class ImageLink extends DsFieldBase {
  
  public function settingsForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();
    $image_fileds = \Drupal::entityManager()->getFieldMapByFieldType('image');
    $image_fileds = isset($image_fileds['node'])?$image_fileds['node']:[];
    $field_options = [];
    foreach($image_fileds as $field_name => $field){
      if(in_array($config['bundle'], $field['bundles'])){
        $field_options[$field_name] = $field_name;
      }
    }
    $form['image_field'] = array(
      '#type' => 'select',
      '#title' => t('Image Field'),
      '#options' => $field_options,
      '#default_value' => $config['image_field'],
    );
    $form['link text'] = array(
      '#type' => 'textfield',
      '#title' => 'Link text',
      '#default_value' => $config['link text'],
    );
    $description = '';
    if(\Drupal::service('module_handler')->moduleExists('colorbox_load')){
      $description  = $this->t('Add class colorbox to open image in popup use colorbox plugin');
    }
    $form['link class'] = array(
      '#type' => 'textfield',
      '#title' => 'Link class',
      '#default_value' => $config['link class'],
      '#description' => $this->t('Put a class on the link. Eg: btn btn-default.') . '<br/>' . $description,
    );
    $form['wrapper'] = array(
      '#type' => 'textfield',
      '#title' => 'Wrapper',
      '#default_value' => $config['wrapper'],
      '#description' => $this->t('Eg: h1, h2, p'),
    );
    $form['wrapper class'] = array(
      '#type' => 'textfield',
      '#title' => 'Wrapper class',
      '#default_value' => $config['class'],
      '#description' => $this->t('Put a class on the wrapper. Eg: block-title'),
    );
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function settingsSummary($settings) {
    $config = $this->getConfiguration();

    $summary = array();
    $summary[] = 'Link text: ' . $config['link text'];
    if (!empty($config['link class'])) {
      $summary[] = 'Link class: ' . $config['link class'];
    }
    if (!empty($config['wrapper'])) {
      $summary[] = 'Wrapper: ' . $config['wrapper'];
    }
    if (!empty($config['wrapper class'])) {
      $summary[] = 'Class: ' . $config['wrapper class'];
    }
    
    return $summary;
  }
  
  public function defaultConfiguration() {

    $configuration = array(
      'link text' => 'View Image',
      'link class' => '',
      'wrapper' => '',
      'wrapper class' => '',
      'link' => 1,
      'image_field' => '',
    );

    return $configuration;
  }
  public function build() {
    $config = $this->getConfiguration();
    if($field = $this->entity()->get($config['image_field'])){
      if($image = File::load($field->getValue()[0]['target_id'])){
        $url = file_create_url($image->getFileUri());
        $wrapper = '';
        $wrapper_close = '';
        if($config['wrapper']){
          $wrapper = '<' . $config['wrapper'] . ' class="' . $config['wrapper class'] . '">';
          $wrapper_close = '</' . $config['wrapper'] . '>';
        }
        $hidden = '';
        foreach($field->getValue() as $k => $value){
          if($k > 0){
            if($image = File::load($value['target_id'])){
              $_url = file_create_url($image->getFileUri());
              $hidden .= '<a class="colorbox invisible" data-colorbox-gallery="node-' . $this->entity()->id() . '" href="' . $_url . '" title="' . $this->entity()->getTitle() . '"></a>';
            }
          }
        }
        return array(
          '#markup' => $wrapper . '<a data-colorbox-gallery="node-' . $this->entity()->id() . '" class="' . $config['link class'] . '" href="' . $url . '" title="'.$this->entity()->getTitle().'">'. $config['link text'] . '</a>' . $hidden . $wrapper_close,
        );
      }
    }
    return array('#markup' => '');
  }
}