<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Language\Language;

/**
 * Base shortcode element of Builder
 */
class BuilderElement extends ShortcodeBase {

  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * Get default shortcode param value
   * @param type $key
   * @param type $default
   * @return type
   */
  public function get($key, $default = '') {
    $attr = \Drupal::request()->get('attr');
    if (is_array($attr) && isset($attr[$key])) {
      return $attr[$key];
    }
    return $default;
  }

  /**
   * generate Attribute element from shortcode attribues
   * @param type $attr
   * @return css string
   */
  public function getCSS($attr = array()) {
    $attrObject = $this->createAttribute($attr);
    return $attrObject->toArray()['style'];
  }

  public function createAttribute($attr = array()){
    $attrObject = new Attribute();
    $keys = ['margin_left', 'margin_top', 'margin_right', 'margin_bottom', 'padding_left', 'padding_top', 'padding_right', 'padding_bottom'];
    $css = [];
    foreach ($keys as $key) {
      if (isset($attr[$key]) && $attr[$key] != '') {
        $css[] = str_replace('_', '-', $key) . ':' . $attr[$key];
      }
    }
    $border_width = 'border-width:%top %right %bottom %left';
    $border_style = 'border-style:%style';
    $border_radius = 'border-radius:%radius';
    $border_color = 'border-color:%color';
    $attrs = $this->getAttributes(array(
      'border_top' => 0,
      'border_right' => 0,
      'border_bottom' => 0,
      'border_left' => 0,
      'border_style' => 'solid',
      'border_color' => '',
      'border_radius' => 0,
      'background_image' => '',
      'background_type' => 'default',
      'background_position' => 'default',
      'background_repeat' => 'repeat',
      'background_attachment' => 'scroll',
      'background_size' => 'auto',
      'background_color' => '',
      'animate' => '',
      'animate_delay' => 0,
      'custom_css' => '',
        ), $attr
    );
    if ($attrs['border_top'] || $attrs['border_right'] || $attrs['border_bottom'] || $attrs['border_left']) {
      $css[] = str_replace(array('%top', '%right', '%bottom', '%left'), array(
        $attrs['border_top'] == '' ? 0 : $attrs['border_top'] . 'px',
        $attrs['border_right'] == '' ? 0 : $attrs['border_right'] . 'px',
        $attrs['border_bottom'] == '' ? 0 : $attrs['border_bottom'] . 'px',
        $attrs['border_left'] == '' ? 0 : $attrs['border_left'] . 'px',
          ), $border_width);

      $css[] = str_replace('%style', $attrs['border_style'], $border_style);
      $css[] = str_replace('%color', $attrs['border_color'], $border_color);
      //$css[] = $this->t($border_color, array('%color' => $attrs['border_color']))->__toString();
      if ($attrs['border_radius']) {
        $css[] = str_replace('%radius', $attrs['border_radius'], $border_radius);
      }
    }
    //Background
    if ($attrs['background_color']) {
      $css[] = 'background-color:' . $attrs['background_color'];
    }
    if ($attrs['background_image']) {
      $fid = str_replace('file:', '', $attrs['background_image']);
      $file = \Drupal\file\Entity\File::load($fid);
      if ($file) {
        $css[] = 'background-image:url(\'' . file_create_url($file->getFileUri()) . '\')';
        if($attrs['background_type'] == 'parallax'){
          $image = \Drupal::service('image.factory')->get($file->getFileUri());
          $attrObject->addClass('dexp-parallax');
          $attrObject->setAttribute('data-bg-width', $image->getWidth());
          $attrObject->setAttribute('data-bg-height', $image->getHeight());
        }else{
          $css[] = 'background-repeat: ' . $attrs['background_repeat'];
        }
        if($attrs['background_type'] == 'fixed'){
          $css[] = 'background-attachment: fixed';
        }
        if($attrs['background_position'] != 'default'){
          $css[] = 'background-position: ' . $attrs['background_position'];
        }
        if($attrs['background_size'] != 'auto'){
          $css[] = 'background-size: ' . $attrs['background_size'];
        }
      }
    }
    if($attrs['custom_css']){
      $css[] = $attrs['custom_css'];
    }
    //Animate
    if($attrs['animate']){
      //$attrObject->addClass('animated dexp-animate');
      $attrObject->setAttribute('data-aos', $attrs['animate']);
      $attrObject->setAttribute('data-aos-delay', $attrs['animate_delay']);
      $attrObject->setAttribute('data-aos-duration', 1000);
    }
    if(!empty($css)){
      $attrObject->setAttribute('style', implode(';', $css));
    }
    return $attrObject;
  }

  /**
   * Check and generate link
   */
  public function getLink($str){
    if($str == '#'){
      return $str;
    }else{
      if($url = \Drupal::service('path.validator')->getUrlIfValid($str)){
        return  $url->toString();
      }else{
        return $str;
      }
    }
  }

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

  }

  public function render(array &$element) {
    $renderer = \Drupal::service('renderer');
    return $renderer->render($element);
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['element_settings'] = array(
      '#type' => 'vertical_tabs',
    );

    $form['general_options'] = array(
      '#type' => 'details',
      '#title' => $this->t('General'),
      '#group' => 'element_settings',
      '#open' => TRUE,
    );

    $form['design_options'] = array(
      '#type' => 'details',
      '#title' => $this->t('Designs'),
      #'#description' => $this->t('Custom background, margin, padding, border ...'),
      '#group' => 'element_settings',
    );

    $form['animate_options'] = array(
      '#type' => 'details',
      '#title' => $this->t('Animate'),
      '#group' => 'element_settings',
    );

    return $form;
  }

  public function designOptions() {
    return array(
      'background' => array(
        '#type' => 'details',
        '#title' => $this->t('Background'),
        '#open' => TRUE,
        //'#attributes' => ['class' => ['row']],
        'background_color' => array(
          '#title' => $this->t('Background color'),
          '#type' => 'textfield',
          '#size' => 20,
          '#default_value' => $this->get('background_color', ''),
          '#attributes' => ['class' => ['color']],
          '#description' => $this->t('Select background color. Leave blank if no color is needed.'),
        ),
        'background_image' => array(
          '#title' => $this->t('Background Image'),
          '#type' => 'image_browser',
          '#default_value' => 'file:' . $this->get('background_image'),
          '#description' => $this->t('Select an image from your Drupal library directory or upload a picture.'),
        ),
        'background_type' => array(
          '#title' => $this->t('Background Attachment'),
          '#type' => 'select',
          '#options' => ['default' => 'Default', 'fixed' =>'Fixed', 'parallax' => 'Parallax'],
          '#default_value' => $this->get('background_type','default'),
        ),
        'background_position' => array(
          '#title' => $this->t('Background Position'),
          '#type' => 'select',
          '#options' => [
            'default' => 'Default', 
            'left top' => 'Left Top', 
            'left center' => 'Left Center',
            'left bottom' => 'Left Bottom', 
            'right top' => 'Right Top', 
            'right center' => 'Right Center',
            'right bottom' => 'Right Bottom',
            'center top' => 'Center Top', 
            'center center' => 'Center Center',
            'center bottom' => 'Center Bottom',
          ],
          '#default_value' => $this->get('background_position','default'),
        ),
        'background_repeat' => array(
          '#type' => 'select',
          '#title' => t('Background Repeat'),
          '#default_value' => $this->get('background_repeat','no-repeat'),
          '#options' => ['no-repeat' => 'no-repeat', 'repeat' => 'repeat', 'repeat-x'=>'repeat-x', 'repeat-y' => 'repeat-y'],
        ),
        'background_size' => array(
          '#type' => 'select',
          '#title' => t('Background Size'),
          '#default_value' => $this->get('background_size','auto'),
          '#options' => ['auto' => 'auto', 'cover' => 'cover', 'contain' => 'contain'],
        ),
      ),
      'margin' => array(
        '#type' => 'details',
        '#title' => $this->t('Margin'),
        //'#attributes' => ['class' => ['row']],
        '#open' => FALSE,
        'margin_top' => array(
          '#type' => 'textfield',
          //'#title' => $this->t('Top'),
          '#default_value' => $this->get('margin_top'),
          '#attributes' => ['placeholder' => 'top', 'style'=>'text-align:center;width:60px'],
          '#wrapper_attributes' => ['style' => 'margin-left:60px'],
        ),
        'margin_left' => array(
          '#type' => 'textfield',
          //'#title' => $this->t('Left'),
          '#default_value' => $this->get('margin_left'),
          '#attributes' => ['placeholder' => 'left','style'=>'text-align:center'],
          '#wrapper_attributes' => ['style' => 'display:inline-block;width:60px;margin-right:56px'],
        ),
        'margin_right' => array(
          '#type' => 'textfield',
          //'#title' => $this->t('Right'),
          '#default_value' => $this->get('margin_right'),
          '#attributes' => ['placeholder' => 'right','style'=>'text-align:center'],
          '#wrapper_attributes' => ['style' => 'display:inline-block;width:60px;text-align:center'],
        ),
        'margin_bottom' => array(
          '#type' => 'textfield',
          //'#title' => $this->t('Bottom'),
          '#default_value' => $this->get('margin_bottom'),
          '#attributes' => ['placeholder' => 'bottom','style' => 'text-align:center'],
          '#wrapper_attributes' => ['style' => 'margin-left:60px;width:60px'],
        ),
      ),
      'padding' => array(
        '#type' => 'details',
        '#title' => $this->t('Padding'),
        //'#attributes' => ['class' => ['row']],
        '#open' => FALSE,
        'padding_top' => array(
          '#type' => 'textfield',
          //'#title' => $this->t('Top'),
          '#default_value' => $this->get('padding_top'),
          '#attributes' => ['placeholder' => 'top', 'style'=>'text-align:center;width:60px'],
          '#wrapper_attributes' => ['style' => 'margin-left:60px'],
        ),
        'padding_left' => array(
          '#type' => 'textfield',
          //'#title' => $this->t('Left'),
          '#default_value' => $this->get('padding_left'),
          '#attributes' => ['placeholder' => 'left','style'=>'text-align:center'],
          '#wrapper_attributes' => ['style' => 'display:inline-block;width:60px;margin-right:56px'],
        ),
        'padding_right' => array(
          '#type' => 'textfield',
          //'#title' => $this->t('Right'),
          '#default_value' => $this->get('padding_right'),
          '#attributes' => ['placeholder' => 'right','style'=>'text-align:center'],
          '#wrapper_attributes' => ['style' => 'display:inline-block;width:60px;text-align:center'],
        ),
        'padding_bottom' => array(
          '#type' => 'textfield',
          //'#title' => $this->t('Bottom'),
          '#default_value' => $this->get('padding_bottom'),
          '#attributes' => ['placeholder' => 'bottom','style' => 'text-align:center'],
          '#wrapper_attributes' => ['style' => 'margin-left:60px;width:60px'],
        ),
      ),
      'border' => array(
        '#type' => 'details',
        '#title' => $this->t('Border'),
        //'#attributes' => ['class' => ['row']],
        '#open' => FALSE,
        'border_top' => array(
          '#type' => 'number',
          //'#title' => $this->t('Border top'),
          '#min' => 0,
          '#default_value' => $this->get('border_top'),
          '#attributes' => ['placeholder' => 'top', 'style'=>'text-align:center;width:60px'],
          '#wrapper_attributes' => ['style' => 'margin-left:60px'],
        ),
        'border_left' => array(
          '#type' => 'number',
          //'#title' => $this->t('Border left'),
          '#min' => 0,
          '#default_value' => $this->get('border_left'),
          '#attributes' => ['placeholder' => 'left','style'=>'text-align:center'],
          '#wrapper_attributes' => ['style' => 'display:inline-block;width:60px;margin-right:56px'],
        ),
        'border_right' => array(
          '#type' => 'number',
          //'#title' => $this->t('Border right'),
          '#min' => 0,
          '#default_value' => $this->get('border_right'),
          '#attributes' => ['placeholder' => 'right','style'=>'text-align:center'],
          '#wrapper_attributes' => ['style' => 'display:inline-block;width:60px;text-align:center'],
        ),
        'border_bottom' => array(
          '#type' => 'number',
          //'#title' => $this->t('Border bottom'),
          '#min' => 0,
          '#default_value' => $this->get('border_bottom'),
          '#attributes' => ['placeholder' => 'bottom','style' => 'text-align:center'],
          '#wrapper_attributes' => ['style' => 'margin-left:60px;width:60px'],
        ),
        'border_color' => array(
          '#type' => 'textfield',
          '#title' => $this->t('Border color'),
          '#default_value' => $this->get('border_color', ''),
          '#attributes' => ['class' => ['color'],'style' => 'width:150px'],
          '#size' => 12,
          '#wrapper_attributes' => ['style' => 'display:inline-block;width:200px'],
        ),
        'border_style' => array(
          '#type' => 'select',
          '#title' => $this->t('Border style'),
          '#options' => ['dotted' => 'dotted', 'dashed' => 'dashed', 'solid' => 'solid', 'double' => 'double', 'groove' => 'groove', 'ridge' => 'ridge', 'inset' => 'inset', 'outset' => 'outset', 'initial' => 'initial', 'inherit' => 'inherit'],
          '#default_value' => $this->get('border_style', 'solid'),
          '#attributes' => ['style' => 'width:150px'],
          '#wrapper_attributes' => ['style' => 'display:inline-block;width:200px'],
        ),
        'border_radius' => array(
          '#type' => 'textfield',
          '#title' => $this->t('Border radius'),
          '#default_value' => $this->get('border_radius', ''),
          '#attributes' => ['style' => 'width:150px'],
          '#wrapper_attributes' => ['style' => 'display:inline-block;width:200px'],
        ),
      ),
    );
  }

  public function animateOptions() {
    return array(
      'animate' => array(
        '#type' => 'select',
        '#title' => $this->t('Animate effect'),
        '#options' => $this->__animates(),
        '#default_value' => $this->get('animate'),
        '#description' => $this->t('Select animate effect from the list. Default <strong>None</strong>, no animate is needed.'),
      ),
      'animate_delay' => array(
        '#type' => 'number',
        '#title' => $this->t('Delay'),
        '#min' => 0,
        '#default_value' => $this->get('animate_delay', 0),
        '#description' => $this->t('Delay time before animate effect starts in millisecond'),
        '#field_suffix' => 'ms',
      ),
    );
  }

  /**
   * Helper: return an array of effects
   */
  protected function __animates() {
    return [
      '' => $this->t('None'),
      'Fade animations' => [
        'fade' => 'fade',
        'fade-up' => 'fade-up',
        'fade-down' => 'fade-down',
        'fade-left' => 'fade-left',
        'fade-right' => 'fade-right',
        'fade-up-right' => 'fade-up-right',
        'fade-up-left' => 'fade-up-left',
        'fade-down-right' => 'fade-down-right',
        'fade-down-left' => 'fade-down-left',
      ],
      'Flip animations' => [
        'flip-up' => 'flip-up',
        'flip-down' => 'flip-down',
        'flip-left' => 'flip-left',
        'flip-right' => 'flip-right',
      ],
      'Slide animations' => [
        'slide-up' => 'slide-up',
        'slide-down' => 'slide-down',
        'slide-left' => 'slide-left',
        'slide-right' => 'slide-right',
      ],
      'Zoom animations' => [
        'zoom-in' => 'zoom-in',
        'zoom-in-up' => 'zoom-in-up',
        'zoom-in-down' => 'zoom-in-down',
        'zoom-in-left' => 'zoom-in-left',
        'zoom-in-right' => 'zoom-in-right',
        'zoom-out' => 'zoom-out',
        'zoom-out-up' => 'zoom-out-up',
        'zoom-out-down' => 'zoom-out-down',
        'zoom-out-left' => 'zoom-out-left',
        'zoom-out-right' => 'zoom-out-right',
      ],
    ];
    return [
      '' => 'None',
      'Attention Seekers' => [
        'bounce' => 'bounce',
        'flash' => 'flash',
        'pulse' => 'pulse',
        'rubberBand' => 'rubberBand',
        'shake' => 'shake',
        'swing' => 'swing',
        'tada' => 'tada',
        'wobble' => 'wobble',
        'jello' => 'jello',
      ],
      'Bouncing Entrances' => [
        'bounceIn' => 'bounceIn',
        'bounceInDown' => 'bounceInDown',
        'bounceInLeft' => 'bounceInLeft',
        'bounceInRight' => 'bounceInRight',
        'bounceInUp' => 'bounceInUp',
      ],
      'Fading Entrances' => [
        'fadeIn' => 'fadeIn',
        'fadeInDown' => 'fadeInDown',
        'fadeInDownBig' => 'fadeInDownBig',
        'fadeInLeft' => 'fadeInLeft',
        'fadeInLeftBig' => 'fadeInLeftBig',
        'fadeInRight' => 'fadeInRight',
        'fadeInRightBig' => 'fadeInRightBig',
        'fadeInUp' => 'fadeInUp',
        'fadeInUpBig' => 'fadeInUpBig',
      ],
      'Flippers' => [
        'flip' => 'flip',
        'flipInX' => 'flipInX',
        'flipInY' => 'flipInY',
        'flipOutX' => 'flipOutX',
        'flipOutY' => 'flipOutY',
      ],
      'Lightspeed' => [
        'lightSpeedIn' => 'lightSpeedIn',
        'lightSpeedOut' => 'lightSpeedOut',
      ],
      'Rotating Entrances' => [
        'rotateIn' => 'rotateIn',
        'rotateInDownLeft' => 'rotateInDownLeft',
        'rotateInDownRight' => 'rotateInDownRight',
        'rotateInUpLeft' => 'rotateInUpLeft',
        'rotateInUpRight' => 'rotateInUpRight',
      ],
      'Sliding Entrances' => [
        'slideInUp' => 'slideInUp',
        'slideInDown' => 'slideInDown',
        'slideInLeft' => 'slideInLeft',
        'slideInRight' => 'slideInRight',
      ],
      'Zoom Entrances' => [
        'zoomIn' => 'zoomIn',
        'zoomInDown' => 'zoomInDown',
        'zoomInLeft' => 'zoomInLeft',
        'zoomInRight' => 'zoomInRight',
        'zoomInUp' => 'zoomInUp',
      ],
      'Specials' => [
        'hinge' => 'hinge',
        'rollIn' => 'rollIn',
        'rollOut' => 'rollOut',
      ],
    ];
  }

}
