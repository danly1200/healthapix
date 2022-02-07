<?php

namespace Drupal\dexp_evolve\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use \Drupal\Component\Utility\Html;
use Drupal\dexp_builder\Plugin\Shortcode\BuilderElement;

/**
 * Provides a shortcode for Carousel.
 *
 * @Shortcode(
 *   id = "dexp_shortcode_carousel",
 *   title = @Translation("Carousel"),
 *   description = @Translation("Carousel content"),
 *   group = @Translation("Content"),
 *   parent = {
 *     "dexp_shortcode_carousels"
 *   },
 *   child = {}
 * )
 */
class DexpShortcodeCarousel extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);
    $attributes = $this->getAttributes(array(
      'image' => '',
	  'class' => '',
	  'title' => '',
        ), $attributes
    );
    global $builder_carousels_stack;
    if (empty($builder_carousels_stack)) {
      $builder_carousels_stack = array();
    }
	$image= '';
	$fid = str_replace('file:', '', $attributes['image']);
	if($file = \Drupal\file\Entity\File::load($fid)){
        $image= '<img src="' . file_create_url($file->getFileUri()) . '"/>';
    }

    $output = [
        'image' => $image,
	      'class' => $attributes['class'],
	      'title' => $attributes['title'],
        'content' => $text,
    ];

    $builder_carousels_stack[] = $output;
    return '';
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['image'] = array(
      '#type' => 'image_browser',
      '#title' => $this->t('Image'),
      '#default_value' => $this->get('image', 0),
    );
	$form['general_options']['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->get('title'),
    );
	$form['general_options']['html_content'] = array(
       '#type' => 'text_format',
	   '#format' => 'full_html',
       '#title' => $this->t('Description'),
       '#default_value' => $this->get('html_content', ''),
    );
	$form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class'),
    );
    return $form;
  }

   public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
        parent::process($attributes, $text, $langcode);
		$image= '';
		$fid = str_replace('file:', '', $attributes['image']);
			if($file = \Drupal\file\Entity\File::load($fid)){
				$image= '<img src="' . file_create_url($file->getFileUri()) . '"/>';
		}
        return $image;
    }
}
