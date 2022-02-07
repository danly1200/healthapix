<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;
use Drupal\Core\Language\Language;
use Drupal\image\Entity\ImageStyle;
/**
 * Provides a gallery item for galleries shortcode.
 *
 * @Shortcode(
 *   id = "dexp_builder_gallery",
 *   title = @Translation("Gallery Item"),
 *   description = @Translation("Gallery Item"),
 *   group = @Translation("Content"),
 *   parent = {
 *     "dexp_builder_galleries"
 *   },
 *   child = {
 *   }
 * )
 */
class BuilderGallery extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => '',
      'image' => 0,
        ), $attributes
    );
    global $builder_galleries;
    if (empty($builder_galleries)) {
      $builder_galleries = array();
    }
    $output = array(
      'title' => $attrs['title'],
      'image' => $attrs['image'],
    );
    $builder_galleries[] = $output;
    return '';
  }
  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
        'title' => '',
        'image' => 0,
          ), $attributes
    );
    $fid = str_replace('file:', '', $attrs['image']);
    if($file = \Drupal\file\Entity\File::load($fid)){
        $thumb_style = [
            '#theme' => 'image_style',
            '#style_name' => 'medium',
            '#uri' => $file->getFileUri(),
            '#alt' => $attrs['title']
        ];
    }
    return $this->render($thumb_style);
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
    $form['general_options']['title'] = array(
        '#type' => 'textfield',
        '#description' => $this->t('Enter your desired text to use as the image title. Leave blank if no title is needed.'),
        '#title' => $this->t('Title'),
        '#default_value' => $this->get('title'),
    );
    unset($form['design_options']);
    unset($form['animate_options']);
    return $form;
  }
}
