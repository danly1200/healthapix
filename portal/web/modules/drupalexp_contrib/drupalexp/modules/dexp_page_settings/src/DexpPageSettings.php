<?php

namespace Drupal\dexp_page_settings;

class DexpPageSettings{
  protected $nid;
  protected $config;
  
  public function __construct($nid) {
    $this->nid = $nid;
    $this->config = \Drupal::service('config.factory')->getEditable('dexp.settings');
  }
  
  /**
   * 
   * @param type $key
   * @param type $default
   * @return type
   */
  public function get($key = '', $default = NULL){
    if($key == ''){
      $key = 'node.' . $this->nid;
    }else{
      $key = 'node.' . $this->nid . '.' . $key;
    }
    if( $return = $this->config->get($key)){
      return $return;
    }
    return $default;
  }
  
  /**
   * 
   * @param string $key
   * @param type $value
   */
  public function set($key, $value){
    $key = 'node.' . $this->nid . '.' . $key;
    $this->config->set($key, $value);
    $this->config->save();
  }
}