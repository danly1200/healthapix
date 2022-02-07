<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use \Drupal\Component\Utility\Html;
use Drupal\dexp_builder\Plugin\Shortcode\BuilderElement;
use Drupal\Core\Template\Attribute;

/**
 * Provides a shortcode for Carousel wrapper.
 *
 * @Shortcode(
 *   id = "dexp_builder_carousels",
 *   title = @Translation("Carousels"),
 *   description = @Translation("Carousel wrapper"),
 *   group = @Translation("Content"),
 *   child = {
 *   "dexp_builder_carousel"
 * }
 * )
 */
class BuilderCarousels extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);

    $attrs = $this->getAttributes(array(
        'carousels' => array(),        
        'autoplay' => 1,
        'autoplayspeed' => 5000,
        'pauseonhover' => '',
        'speed' => 500,
        'move' => 1,
        'infinite' => 1,
        'lg' => 1,
        'md' => 1,
        'sm' => 1,
        'xs' => 1,
        'arrows' => true,
        'dots' => true,
        'class' => '',
        ), $attributes
    );
    $carousel_wrapper_id = Html::getUniqueId('dexp_builder_carousels_wrapper_' . REQUEST_TIME);
    global $builder_carousels_stack;
	  $uuid_service = \Drupal::service('uuid');
    $uuid = $uuid_service->generate();
	  $attribute = new Attribute();
	  $attribute->addClass('dexp-builder-slick-carousel');
    $attribute->addClass($attrs['class']);
    $attribute->setAttribute('data-uuid', $uuid);
    $attribute->setAttribute('id', $carousel_wrapper_id);
    $options=[
        "autoplay"=> (bool)$attrs['autoplay'],
        "autoplayspeed"=> $attrs['autoplayspeed'],
        "pauseonhover"=> $attrs['pauseonhover'],
        "speed"=> $attrs['speed'],
        "slidesToScroll"=> $attrs['move'],
        "infinite"=> (bool)$attrs['infinite'],
        "lg"=> $attrs['lg'],
        "md"=> $attrs['md'],
        "sm"=> $attrs['sm'],
        "xs"=> $attrs['xs'],
        "arrows"=> (bool)$attrs['arrows'],
        "dots"=> (bool)$attrs['dots'],
        "mode"=> 'horizontal',
        "rows"=> 1,
        "slideMargin"=> '20px',
        "initialSlide"=> 0,
        "hideControlOnEnd"=> 'hideControlOnEnd',
        "swipe"=> 1,
        "centerMode"=> 0,
        "centerPadding"=> '50px',
    ];
    //'arrows', 'infinite', 'dots', 'centerMode', 'autoplay', 'pauseonhover', 'swipe', 'variableWidth']
    $output = array(
      '#theme' => 'dexp_builder_carousels',      
      '#carousels' => $builder_carousels_stack,
      '#wrapper_id' => $carousel_wrapper_id,      
      '#attributes' => $attribute,
      '#attached' => ['library' => 'dexp_builder/slick-carousels'],
    );
    $output['#attached']['drupalSettings']['dexp_builder_carousels'] = [
        $uuid => $options,
    ];
    $builder_carousels_stack = array();
    return $this->render($output);
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
      $form['general_options']['slick_general'] = [
        '#type' => 'details',
        '#title' => t('General'),
        '#open' => TRUE,
      ];
    
      $form['general_options']['slick_general']['autoplay'] = [
        '#type' => 'checkbox',
        '#title' => t('Auto Play'),
        '#description' => $this->t('If checked, slides will automatically transition.'),
        '#default_value' => $this->get('autoplay', 1),
      ];
  
      $form['general_options']['slick_general']['autoplayspeed'] = [
        '#type' => 'number',
        '#title' => t('Auto Play Speed'),
        '#description' => t('Autoplay Speed in milliseconds'),
        '#default_value' => $this->get('autoplayspeed', 5000),
        '#states' => [
          'visible' => [
            ':input[name*=autoplay]' => ['checked' => TRUE]
          ]
        ]
      ];
  
      $form['general_options']['slick_general']['pauseonhover'] = [
        '#type' => 'checkbox',
        '#title' => t('Pause on hover'),
        '#description' => $this->t('Pause Autoplay On Hover'),
        '#default_value' => $this->get('pauseOnHover',1),
        '#states' => [
          'visible' => [
            ':input[name*=autoplay]' => ['checked' => TRUE]
          ]
        ]
      ];
  
      $form['general_options']['slick_general']['speed'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Transition duration'),
        '#size' => 17,
        '#description' => $this->t('Slide transition duration (in ms)'),
        '#default_value' => $this->get('speed', 500),
      ];

      $form['general_options']['slick_general']['move'] = [
        '#type' => 'number',
        '#title' => $this->t('Slides to scroll'),
        '#min' => 1,
        '#default_value' => $this->get('move', 1),
        '#description' => $this->t('Slides to move when clicking on the control'),
      ];

      $form['general_options']['slick_general']['infinite'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Infinite Loop'),
        '#description' => $this->t('If checked, clicking "Next" while on the last slide will transition to the first slide and vice-versa'),
        '#default_value' =>  $this->get('infinite', 1),
      ];

      $form['general_options']['slick_carousel'] = [
        '#type' => 'details',
        '#title' => t('Carousel'),
      ];

      $form['general_options']['slick_carousel']['lg'] = [
        '#type' => 'number',
        '#title' => $this->t('Items on large screen'),
        '#min' => 1,
        '#default_value' => $this->get('lg', 1),
        '#description' => $this->t('Number of items visible on large devices (desktops)'),
      ];

      $form['general_options']['slick_carousel']['md'] = [
        '#type' => 'number',
        '#min' => 1,
        '#title' => $this->t('Items on medium screen'),
        '#default_value' => $this->get('md', 1),
        '#description' => $this->t('Number of items visible on medium devices (desktops)'),
      ];

      $form['general_options']['slick_carousel']['sm'] = [
        '#type' => 'number',
        '#min' => 1,
        '#title' => $this->t('Items on small screen'),
        '#default_value' => $this->get('sm', 1),
        '#description' => $this->t('Number of items visible on small devices (tablet)'),
      ];

      $form['general_options']['slick_carousel']['xs'] = [
        '#type' => 'number',
        '#min' => 1,
        '#title' => $this->t('Items on extra small screen'),
        '#default_value' => $this->get('xs', 1),
        '#description' => $this->t('Number of items visible on extra small devices (phone)'),
      ];

      $form['general_options']['slick_controls'] = [
        '#type' => 'details',
        '#title' => t('Controls'),
      ];
  
      $form['general_options']['slick_controls']['arrows'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Control buttons?'),
        '#default_value' => $this->get('arrows', true),
        '#description' => $this->t('If checked, "Next" / "Prev" controls will be added'),
      ];
  
      $form['general_options']['slick_controls']['dots'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Show pager?'),
        '#default_value' => $this->get('dots', true),
        '#description' => $this->t('If checked, a pager will be added'),
      ];
      $form['general_options']['class'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Custom class'),
        '#default_value' => $this->get('class'),
        '#description' => $this->t('Adding a custom class allows you to target the carousel easily within your custom codes.'),
      );
      unset($form['design_options']);
      unset($form['animate_options']);
    return $form;
  }

  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    return $text;
  }

}
