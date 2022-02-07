<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;

/**
 * Provides a shortcode for empty space.
 *
 * @Shortcode(
 *   id = "dexp_builder_single_image",
 *   title = @Translation("Single Image"),
 *   description = @Translation("Add Single Image element"),
 *   group = @Translation("Content"),
 *   child = {}
 * )
 */
class BuilderSingleImage extends BuilderElement {
  
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'image' => 0,
      'class' => '',
      'add_link' => '',
      'link' => '',
      'alt' => '',
      'link_target' => '_self',
      'link_class' => '',
    ),$attrs);
    $fid = str_replace('file:', '', $attrs['image']);
    $link = '';
    if($attrs['link']){
      $link = $this->getLink($attrs['link']);
    }
    if($file = \Drupal\file\Entity\File::load($fid)){
      $image = [
        '#theme' => 'image',
        '#uri' => $file->getFileUri(),
        '#alt' => $attrs['alt'],
        '#attributes' => ['class' => [$attrs['class']]],
      ];
      if($link && $attrs['add_link']){
        if($attrs['link_target']){
          if($attrs['link_target'] == '_lightbox'){
            if(\Drupal\Component\Utility\UrlHelper::isExternal($link)){
              return '<a href="' . $link . '" class="colorbox dexp-builder-lightbox ' . $attrs['link_class'] . '">' . $this->render($image) . '</a>';
            }else{
              return '<a href="' . $link . '" class="colorbox dexp-builder-lightbox ' . $attrs['link_class'] . '">' . $this->render($image) . '</a>';
            }
          }else{
            return '<a target="' . $attrs['link_target'] . '" href="' . $link . '" class="' . $attrs['link_class'] . '">' . $this->render($image) . '</a>';
          }
        }
      }else{
        return $this->render($image);
      }
    }
    return '';
  }
  
  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['image'] = array(
      '#type' => 'image_browser',
      '#required' => true,
      '#title' => $this->t('Image'),
      '#default_value' => $this->get('image', 0),
      '#description' => $this->t('Select an image from your Drupal library directory or upload a picture.'),
    );
    $form['general_options']['alt'] = array(
      '#type' => 'textfield',
      '#required' => true,
      '#title' => $this->t('Alt Text'),
      '#description' => $this->t('Insert Alt text, which is an important for SEO purposes and part of making a site accessible.'),
      '#default_value' => $this->get('alt', ""),
    );
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class', ''),
      '#description' => $this->t('Adding a custom class allows you to target the image easily within your custom codes. For example useful classes: img-fluid, center-block, rounded, rounded-circle, img-thumbnail.')
    );
    $form['general_options']['add_link'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Add Link?'),
      '#default_value' => $this->get('add_link', 0),
      '#description' => $this->t('Use this option to add the link to the image. Upon enabling it the two options will open. You have to paste the link and select the option where will it open.')
    );
    $form['general_options']['link'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Link'),
      '#default_value' => $this->get('link', ''),
      '#description' => $this->t('Enter the destination URL.'),
      '#states' => array(
        'visible' => array(
          ':input[name=add_link]' => array('checked' => true),
        ),
      ),
    );
    $link_target_options = array(
      '_self' => $this->t('Same window'),
      '_blank' => $this->t('New window'),
    );
    if(\Drupal::service('module_handler')->moduleExists('colorbox_load')){
      $link_target_options['_lightbox'] = $this->t('NG Lightbox');
    }
    $form['general_options']['link_target'] = array(
      '#type' => 'select',
      '#title' => $this->t('Link Target'),
      '#options' => $link_target_options,
      '#default_value' => $this->get('link_target', ''),
      '#description' => $this->t('Set target attribute for link.'),
      '#states' => array(
        'visible' => array(
          ':input[name=add_link]' => array('checked' => true),
        ),
      ),
    );
    $form['general_options']['link_class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Link class'),
      '#default_value' => $this->get('link_class', ''),
      '#states' => array(
        'visible' => array(
          ':input[name=add_link]' => array('checked' => true),
        ),
      ),
      '#description' => $this->t('Adding a custom class allows you to target the link easily within your custom codes.')
    );
    unset($form['design_options']);
    unset($form['animate_options']);
    return $form;
  }
}
