<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;
use Drupal\Core\Language\Language;
use Drupal\image\Entity\ImageStyle;
/**
 * Provides a shortcode for bootstrap row.
 *
 * @Shortcode(
 *   id = "dexp_builder_galleries",
 *   title = @Translation("Galleries"),
 *   description = @Translation("Create gallery"),
 *   group = @Translation("Content"),
 *   child = {
 *     "dexp_builder_gallery"
 *   }
 * )
 */
class BuilderGalleries extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);
    
    $attrs = $this->getAttributes(array(
      'title' => '',
      'render_el' => '',
      'transition' => 'elastic',
      'slideshow' => 0,
      'column' => 'column',
      'thumbnail' => 'thumbnail',
      'width' => '',
      'height' => '',
      'class' => '',
        ), $attributes
    );

    global $builder_galleries;
    $galleries = array();
    if(!empty($builder_galleries)){
        foreach($builder_galleries as $image){
            $url = '';
            $image_thumb = '';
            $fid = str_replace('file:', '', $image['image']);
            if($file = \Drupal\file\Entity\File::load($fid)){
                $thumb_style = [
                    '#theme' => 'image_style',
                    '#style_name' => $attrs['thumbnail'],
                    '#uri' => $file->getFileUri(),
                    '#alt' => $image['title']
                ];
                $url = file_create_url($file->getFileUri());
                $image_thumb = $this->render($thumb_style);
            }
            $image = [
                'title' => $image['title'],
                'image_path' => $url,
                'image_thumb' => $image_thumb
            ];
            $galleries[] = $image;
        }
    }
    $slideshow = empty($attrs['slideshow'])? 0 : 1;
    $title = '';
    if($attrs['title']){
      $title = ['#markup' => '<' . $attrs['render_el'] . ' class="dexp-gallery-title">' . $attrs['title'] . '</' . $attrs['render_el'] . '>'];
    }
    $return = array(
      '#theme' => 'dexp_builder_galleries',
      '#title' => $title,
      '#galleries' => $galleries,
      '#transition' => $attrs['transition'],
      '#slideshow' => $slideshow,
      '#width' => $attrs['width'],
      '#height' => $attrs['height'],
      '#class' => $attrs['class'],
      '#attached' => ['library' => ['dexp_builder/gallery']],
    );
    $builder_galleries = array();
    return $this->render($return);
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    
    $styles = ImageStyle::loadMultiple();
    $options = array();
    if (!empty($styles)) {
        foreach ($styles as $name => $style) {
            $options[$name] = $style->label();
        }
    }else{
        $options[''] = $this->t('No defined styles');
    }
    $form['general_options']['title'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#default_value' => $this->get('title'),
        '#description' => $this->t('Enter your desired text to use as the gallery title. Leave blank if no title is needed.')
      );
      $form['general_options']['render_el'] = [
        '#type' => 'select',
        '#title' => $this->t('Render Element'),
        '#options' => [
          'h1' => 'H1',
          'h2' => 'H2',
          'h3' => 'H3',
          'h4' => 'H4',
          'h5' => 'H5',
          'h6' => 'H6',
          'div' => 'DIV',
        ],
        '#default_value' => $this->get('render_el','h2'),
        '#description' => $this->t('Select Title HTML element from the list.')
    ];
    $form['general_options']['thumbnail'] = [
        '#type' => 'select',
        '#title' => $this->t('Thumbnail'),
        '#options' => $options,
        '#default_value' => $this->get('thumbnail','thumbnail'),
        '#description' => $this->t('Select an image thumbnail style. This image will be displayed in the thumbnail of the gallery.')
    ];
    $form['general_options']['popup'] = array(
        '#type' => 'details',
        '#title' => $this->t('Popup Settings'),
        '#open' => FALSE,
    );
    $form['general_options']['popup']['width'] = array(
        '#type' => 'textfield',
        '#size' => 17,
        '#default_value' => $this->get('width', ''),
        '#title' => $this->t('Width'),
        '#description' => $this->t('Set a fixed total width popup window. This includes borders and buttons. For example: "100%", "500px", or 500. Leave blank if auto width is needed.'),
    );
    $form['general_options']['popup']['height'] = array(
        '#type' => 'textfield',
        '#size' => 17,
        '#default_value' => $this->get('height', ''),
        '#title' => $this->t('Height'),
        '#description' => $this->t('Set a fixed total height popup window. This includes borders and buttons. For example: "100%", "500px", or 500. Leave blank if auto height is needed.'),
    );
    $form['general_options']['popup']['transition'] = array(
      '#type' => 'select',
      '#title' => $this->t('Transition'),
      '#options' => array(
          'none' => $this->t('None'), 
          'elastic' => $this->t('Elastic'),
          'fade' => $this->t('Fade')
        ),
      '#default_value' => $this->get('transition','elastic'),
      '#description' => $this->t('Select the transition type from the list. Can be set to "elastic", "fade", or "none".'),
    );
    $form['general_options']['popup']['slideshow'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Slideshow?'),
        '#default_value' => $this->get('slideshow', 0),
        '#description' => $this->t('If true, adds an automatic slideshow to a gallery item.'),
    );
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#description' => $this->t('Adding a custom class allows you to target the gallery easily within your custom codes.'),
      '#default_value' => $this->get('class'),
    );
    unset($form['design_options']);
    unset($form['animate_options']);
    return $form;
  }
  
  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);

    return $text;
  }

}
