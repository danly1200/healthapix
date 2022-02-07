<?php

namespace Drupal\dexp_layerslider;

class Layer extends \stdClass{
    public function __construct($layer, $settings){
        $this->settings = $settings;
        foreach($layer as $key => $value){
            $this->{$key} = $value;
        }
    }
    public function getResponsive($key){
        if(!isset($this->{$key})){
            return [];
        }else{
            $return = [$this->{$key}];
            if(isset($this->settings->md_custom) && $this->settings->md_custom){
                $mdkey = 'md_' . $key;
                $return[] = isset($this->{$mdkey}) ? $this->{$mdkey} : $this->{$key};
            }
            if(isset($this->settings->sm_custom) && $this->settings->sm_custom){
                $smkey = 'sm_' . $key;
                $return[] = isset($this->{$smkey}) ? $this->{$smkey} : $this->{$key};
            }
            if(isset($this->settings->xs_custom) && $this->settings->xs_custom){
                $xskey = 'xs_' . $key;
                $return[] = isset($this->{$xskey}) ? $this->{$xskey} : $this->{$key};
            }
            return $return;
        }        
    }
    public function getResponsiveNum($key){
        $return = $this->getResponsive($key);
        foreach($return as $k => $v){
            if($v){
                $return[$k] = (float)($v);
            }
        }
        return $return;
    }
    public function getVisibility(){
        $return = $this->getResponsive('visibility');
        foreach($return as $k => $v){
            if($v){
                $return[$k] = "on";
            }else{
                $return[$k] = "off";
            }
        }
        return $return;
    }
}