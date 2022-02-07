<?php

namespace Drupal\dexp_layerslider\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\dexp_layerslider\Entity\Slider;

class SliderImportForm extends FormBase{
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dexp_layerslider_slider_import';
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['file'] = array(
      '#type' => 'managed_file',
      '#title' => $this->t('Upload export file'),
      '#upload_location' => 'temporary://',
      '#upload_validators' => array(
        'file_validate_extensions' => array('txt'),
      ),
      '#required' => true,
    );
    
    $form['actions'] = array(
      '#type' => 'actions',
    );
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Import'),
    );
    
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $fid = $form_state->getValue('file')[0];
    $file = File::load($fid);
    $file_content = file_get_contents($file->getFileUri());
    $slider_data = json_decode($file_content);
    if($slider_data == null){
      drupal_set_message( $this->t('Invalid file'), 'error');
      return false;
    }
    $slider = Slider::create();
    $slider->save();
    
    $data = $slider_data->data;
    $images = $slider_data->images;
    foreach($images as $fid => $image){
      $file = file_save_data(base64_decode($image->data), 'public://'.$image->filename);
      $file->setPermanent();
      $file->save();
      $file_usage = \Drupal::service('file.usage');
      $file_usage->add($file, 'dexp_layerslider', 'dexp_slider', $slider->id());
      $data = str_replace(array('"fid":"file:' . $fid . '"', '"fid":"' . $fid . '"'), '"fid":"' . $file->id() . '"', $data);
      //$data = str_replace('"file:'.$fid.'"', '"file:'.$file->id().'"', $data);
    }
    $slider->set('name', $slider_data->name);
    $slider->set('settings', $slider_data->settings);
    $slider->set('data', $data);
    $slider->save();
    $form_state->setRedirect('entity.dexp_slider.collection');
    drupal_set_message('Slider has been imported.');
  }
}
