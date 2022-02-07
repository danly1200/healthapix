<?php

namespace Drupal\dexp\Plugin\DsField\Node;

use Drupal\ds\Plugin\DsField\DsFieldBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin that renders the 'read more' link of a node.
 *
 * @DsField(
 *   id = "node_comment_count",
 *   title = @Translation("Comment Count"),
 *   entity_type = "node",
 *   provider = "node"
 * )
 */
class CommentCount extends DsFieldBase {
  
  public function settingsForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();
    
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => 'Label',
      '#default_value' => $config['label'],
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
    $summary[] = 'Label: ' . $config['label'];
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
      'label' => 'Comment:',
      'wrapper' => '',
      'wrapper class' => '',
    );

    return $configuration;
  }
  public function build() {
    $config = $this->getConfiguration();
    $wrapper = '';
    $wrapper_close = '';
    if($config['wrapper']){
      $wrapper = '<' . $config['wrapper'] . ' class="' . $config['wrapper class'] . '">';
      $wrapper_close = '</' . $config['wrapper'] . '>';
    }
    $comment_count = '0';
    if(\Drupal::service('module_handler')->moduleExists('colorbox')){
      $entity = $this->entity();
      $static = \Drupal::service('comment.statistics')->read(array($entity->id()=>$entity), 'node');
      if(!empty($static)){
        $comment_count = $static[0]->comment_count;
      }
    }
    return array(
      '#markup' => $wrapper . $config['label'] . '<span class="comment-count">' . $comment_count . '</span>' . $wrapper_close,
    );
  }
}