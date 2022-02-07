<?php

/**
 * @file
 * Contains \Drupal\dexp_layerslider\Entity\Slider.
 */

namespace Drupal\dexp_layerslider\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\dexp_layerslider\SliderInterface;
use Drupal\user\UserInterface;
use Drupal\file\Entity\File;
use Drupal\Component\Serialization\Json;
/**
 * Defines the Slider entity.
 *
 * @ingroup dexp_layerslider
 *
 * @ContentEntityType(
 *   id = "dexp_slider",
 *   label = @Translation("Slider"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dexp_layerslider\SliderListBuilder",
 *     "views_data" = "Drupal\dexp_layerslider\Entity\SliderViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\dexp_layerslider\Entity\Form\SliderForm",
 *       "add" = "Drupal\dexp_layerslider\Entity\Form\SliderForm",
 *       "edit" = "Drupal\dexp_layerslider\Entity\Form\SliderForm",
 *       "delete" = "Drupal\dexp_layerslider\Entity\Form\SliderDeleteForm",
 *       "edit_slides" = "Drupal\dexp_layerslider\Entity\Form\SliderEditSlidesForm",
 *       "file_upload" = "Drupal\dexp_layerslider\Entity\Form\SliderFileUploadForm",
 *     },
 *     "access" = "Drupal\dexp_layerslider\SliderAccessControlHandler",
 *   },
 *   base_table = "dexp_slider",
 *   admin_permission = "administer Slider entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/dexp_slider/{dexp_slider}",
 *     "edit-form" = "/admin/dexp_slider/{dexp_slider}/edit",
 *     "delete-form" = "/admin/dexp_slider/{dexp_slider}/delete"
 *   },
 *   field_ui_base_route = "dexp_slider.settings"
 * )
 */
class Slider extends ContentEntityBase implements SliderInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getSettings(){
    $settingsStr =  $this->get('settings')->value;
    $settings = json_decode($settingsStr);;
    $settings->minFullScreenHeight = $settings->startheight;
    $settings->responsiveLevels = [1200];
    $settings->gridwidth = [(int)$settings->startwidth];
    $settings->gridheight = [(int)$settings->startheight];
    if(!isset($settings->md_custom)){
      $settings->md_custom = 0;
    }
    if(!isset($settings->sm_custom)){
      $settings->sm_custom = 0;
    }
    if(!isset($settings->xs_custom)){
      $settings->xs_custom = 0;
    }
    if($settings->md_custom){
      $settings->responsiveLevels[] = 992;
      $settings->gridwidth[] = (int)$settings->md_startwidth;
      $settings->gridheight[] = (int)$settings->md_startheight;
    }
    if($settings->sm_custom){
      $settings->responsiveLevels[] = 768;
      $settings->gridwidth[] = (int)$settings->sm_startwidth;
      $settings->gridheight[] = (int)$settings->sm_startheight;
    }
    if($settings->xs_custom){
      $settings->responsiveLevels[] = 576;
      $settings->gridwidth[] = (int)$settings->xs_startwidth;
      $settings->gridheight[] = (int)$settings->xs_startheight;
    }    
    $settings->visibilityLevels = $settings->responsiveLevels;
    if($settings->fullWidth == 'on'){
      $settings->sliderLayout = 'fullwidth';
      if($settings->fullScreen == 'on'){
        $settings->sliderLayout = 'fullscreen';
        $settings->minHeight = $settings->gridheight;
      }
    }else{
      $settings->sliderLayout = 'auto';
      $settings->autoHeight = 'off';
    }
    if(count($settings->gridwidth) === 1){
      $settings->gridwidth = reset($settings->gridwidth);
      $settings->gridheight = reset($settings->gridheight);
      unset($settings->responsiveLevels);
      unset($settings->visibilityLevels);
    }
    return $settings;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getSlides(){
    $slidesStr =  $this->get('data')->value;
    $slides = Json::decode($slidesStr);
    //dsm($slides);
    foreach($slides as $k => &$slide){
      if(!isset($slide['background_image'])){
        $slide['background_image'] = ['fid' => 'file:0', 'url' => ''];
      }
      if(!is_array($slide['background_image'])){
        $slide['background_image'] = [
          'fid' => $slide['background_image'],
          'url' => '',
        ];
      }
      $fid = str_replace('file:', '', $slide['background_image']['fid']);
      $slide['background_image']['url'] = '';
      if($file = File::load($fid)){
        $slide['background_image']['url'] = file_create_url($file->getFileUri());
      }
      if(!isset($slide['thumbnail_image'])){
        $slide['thumbnail_image'] = [
          'fid' => 'file:0',
          'url' => '',
        ];
      }
      if(!is_array($slide['thumbnail_image'])){
        $slide['thumbnail_image'] = [
          'fid' => $slide['thumbnail_image'],
          'url' => '',
        ];
      }
      $fid = str_replace('file:', '', $slide['thumbnail_image']['fid']);
      $slide['thumbnail_image']['url'] = '';
      if($file = File::load($fid)){
        $slide['thumbnail_image']['url'] = file_create_url($file->getFileUri());
      }
      if(isset($slide['layers']) && !empty($slide['layers'])){
        foreach($slide['layers'] as $k => &$layer){
          if(!is_array($layer['image'])){
            $layer['image'] = [
              'fid' => $layer['image'],
              'url' => '',
            ];
          }
          $fid = str_replace('file:', '', $layer['image']['fid']);
          $layer['image']['url'] = ''; 
          if($file = File::load($fid)){
            $layer['image']['url']  = file_create_url($file->getFileUri());
          }
          if(!isset($layer['video_poster'])){
            $layer['video_poster'] = [
              'fid' => 'file:0',
              'url' => '',
            ];
          }
          if(!is_array($layer['video_poster'])){
            $layer['video_poster'] = [
              'fid' => $layer['video_poster'],
              'url' => '',
            ];
          }
          $fid = str_replace('file:', '', $layer['video_poster']['fid']);
          $file = File::load($fid);
          //$layer->html5_video_poster_url = '';
          if($file){
            $layer['video_poster']['url']  = file_create_url($file->getFileUri());
          }
        }
      }
    }
    return $slides;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
        ->setLabel(t('ID'))
        ->setDescription(t('The ID of the Slider entity.'))
        ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
        ->setLabel(t('UUID'))
        ->setDescription(t('The UUID of the Slider entity.'))
        ->setReadOnly(TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
        ->setLabel(t('Authored by'))
        ->setDescription(t('The user ID of author of the Slider entity.'))
        ->setRevisionable(TRUE)
        ->setSetting('target_type', 'user')
        ->setSetting('handler', 'default')
        ->setDefaultValueCallback('Drupal\node\Entity\Node::getCurrentUserId')
        ->setTranslatable(TRUE)
        ->setDisplayOptions('view', array(
          'label' => 'hidden',
          'type' => 'author',
          'weight' => 0,
        ))
        ->setDisplayOptions('form', array(
          //'type' => 'entity_reference_autocomplete',
          'type' => 'hidden',
          'weight' => 5,
          'settings' => array(
            'match_operator' => 'CONTAINS',
            'size' => '60',
            'autocomplete_type' => 'tags',
            'placeholder' => '',
          ),
        ))
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Name'))
        ->setDescription(t('The name of the Slider entity.'))
        ->setSettings(array(
          'max_length' => 50,
          'text_processing' => 0,
        ))
        ->setDefaultValue('')
        ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'string',
          'weight' => -4,
        ))
        ->setDisplayOptions('form', array(
          'type' => 'string_textfield',
          'weight' => -4,
        ))
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);

    $fields['settings'] = BaseFieldDefinition::create('string_long')
        ->setLabel(t('Settings'))
        ->setDescription(t('Slider settings'))
        ->setDefaultValue('{"delay":"9000","startwidth":"1170","startheight":"500","onHoverStop":"on","loopsingle":"0","touchenabled":"on","fullWidth":"on","fullScreen":"off","shadow":"0","dottedOverlay":"none","navigationType":"none","thumbAmount":"2","thumbWidth":"50","thumbHeight":"50","navigationArrows":"solo","navigationStyle":"preview1","hideThumbs":"0"}')
        ->setDisplayOptions('form', array(
          'type' => 'hidden',
        ))
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', FALSE);
    
     $fields['data'] = BaseFieldDefinition::create('string_long')
        ->setLabel(t('Data'))
        ->setDescription(t('Slider Data'))
        /*->setSettings(array(
          'max_length' => 5000,
          'text_processing' => 0,
        ))*/
        ->setDefaultValue('[{}]')
        ->setDisplayOptions('form', array(
          'type' => 'hidden',
        ))
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', FALSE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
        ->setLabel(t('Language code'))
        ->setDescription(t('The language code for the Slider entity.'));

    $fields['created'] = BaseFieldDefinition::create('created')
        ->setLabel(t('Created'))
        ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
        ->setLabel(t('Changed'))
        ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }
  
  public function save() {
    \Drupal::service('plugin.manager.block')->clearCachedDefinitions();
    return parent::save();
  }
  
}
