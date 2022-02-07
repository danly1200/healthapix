<?php

/**
 * @file
 * Contains \Drupal\dexp_layerslider\Entity\Form\SliderEditSlidesForm.
 */

namespace Drupal\dexp_layerslider\Form;

use Drupal\dexp_layerslider\Entity\Slider;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\file\Entity\File;

/**
 * Form controller for Slider edit forms.
 *
 * @ingroup dexp_layerslider
 */
class SlidesEditForm extends FormBase {
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dexp_layerslider_slides_edit';
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $dexp_slider = 0) {
    $slider = Slider::load($dexp_slider);
    
    $form['id'] = array(
      '#type' => 'hidden',
      '#default_value' => $slider->id(),
    );
    $form['data'] = array(
      '#type' => 'hidden',
      '#default_value' => json_encode($slider->getSlides()),
    );
    $form['markup'] = array(
      '#theme' => 'slider_edit',
      '#slides' => [],
    );
    $form['actions'] = array(
      '#type' => 'actions',
    );
    
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#default_value' => $this->t('Save'),
      '#button_type' => 'primary',
    );
    $form['slider_design'] = [
      '#markup' => '<div id="slider_design"></div>',
    ];
    $google_fonts = file_get_contents(drupal_get_path('module', 'dexp_layerslider').'/vendor/google-fonts-api/google-fonts-api.json');
    $transitions = dexp_layerslider_layer_animation();
    foreach($transitions as $key => $transition){
      $transitions[$key] = json_decode($transition);
    }
    $form['#attached']['library'][] = 'dexp_layerslider/backend';
    $form['#attached']['library'][] = 'image_browser/image_browser';
    $form['#attached']['drupalSettings']['dexp_layerslider'] = array(
      'settings' => $slider->getSettings(),
      'slides' => $slider->getSlides(),
      'fonts' => json_decode($google_fonts),
      'transitions' => $transitions,
    );
    $form['#attached']['drupalSettings']['layerAnimations'] = dexp_layerslider_layer_animation();
    $form['#attached']['drupalSettings']['google_fonts'] = json_decode($google_fonts);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $slider = Slider::load($form_state->getValue('id'));
    $slider->set('data', $form_state->getValue('data'));
    $slider->save();
    $slides = $slider->getSlides();
    foreach($slides as $slide){
      if(isset($slide['background_image'])){
        $this->saveFile($slide['background_image']['fid'], $slider->id());
      }
      if(isset($slide['thumbnail_image'])){
        $this->saveFile($slide['thumbnail_image']['fid'], $slider->id());
      }
      foreach($slide['layers'] as $layer){
        if(isset($layer['image'])){
          $this->saveFile($layer['image']['fid'], $slider->id());
        }
        if(isset($layer['video_poster'])){
          $this->saveFile($layer['video_poster']['fid'], $slider->id());
        }
      }
    }
    $form_state->setRedirect('entity.dexp_slider.collection');
    drupal_set_message('Slider has been saved.');
  }
  
  private  function saveFile($fid, $slider_id){
    if($fid){
      $fid = str_replace('file:', '', $fid);
      $file = File::load($fid);
      if($file){
        $file->setPermanent();
        $file->save();
        $file_usage = \Drupal::service('file.usage');
        $file_usage->add($file, 'dexp_layerslider', 'dexp_slider', $slider_id);
      }
    }
  }

}