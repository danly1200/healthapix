<?php

namespace Drupal\dexp_builder\FontIcon;

class FontIconService {

  function getIcons() {
    $icons = [];
    $type = \Drupal::service('plugin.manager.fonticon');
    foreach($type->getDefinitions() as $id => $fonticon){
      $icons[$id] =  array(
        'title' => $fonticon['title'],
        'icons' => $type->createInstance($id)->icons(),
      );
    }
    return $icons;
  }
  
  function getLibraries(){
    $libraries = [];
    $type = \Drupal::service('plugin.manager.fonticon');
    foreach($type->getDefinitions() as $id => $fonticon){
      $libraries[] = $type->createInstance($id)->library();
    }
    
    return $libraries;
  }
  
  function getFontIconPlugin($id){
    $type = \Drupal::service('plugin.manager.fonticon');
    if(isset($type->getDefinitions()[$id])){
      return $type->createInstance($id);
    }else{
      return null;
    }
  }

}
