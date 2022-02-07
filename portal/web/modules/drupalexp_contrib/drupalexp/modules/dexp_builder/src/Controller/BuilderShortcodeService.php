<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\dexp_builder\Controller;

use Drupal\filter\Entity\FilterFormat;
use Drupal\shortcode\Shortcode\ShortcodeService;

/**
 * Description of ShortcodeService
 *
 * @author congnguyen
 */
class BuilderShortcodeService {

  public function process($text, $langcode = Language::LANGCODE_NOT_SPECIFIED, $format) {
    $shortcodes = $this->getShortcodePlugins($format);
    // Processing recursively, now embedding tags within other tags is supported!
    $chunks = preg_split('!(\[{1,2}.*?\]{1,2})!', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

    $heap = array();
    $heap_index = array();

    foreach ($chunks as $c) {

      if (!$c) {
        continue;
      }

      $escaped = FALSE;

      if ((substr($c, 0, 2) == '[[') && (substr($c, -2, 2) == ']]')) {
        $escaped = TRUE;
        // Checks media tags, eg: [[{ }]].
        if ((substr($c, 0, 3) != '{') && (substr($c, -3, 1) != '}')) {
          // Removes the outer [].
          $c = substr($c, 1, -1);
        }
      }
      // Decide this is a Shortcode tag or not.
      if (!$escaped && ($c[0] == '[') && (substr($c, -1, 1) == ']')) {
        // The $c maybe contains Shortcode macro.
        // This is maybe a self-closing tag.
        // Removes outer [].
        $original_text = $c;
        $c = substr($c, 1, -1);
        $c = trim($c);

        $ts = explode(' ', $c);
        $tag = array_shift($ts);
        $tag = trim($tag, '/');

        if (!$this->isValidShortcodeTag($tag, $format)) {
          // This is not a valid shortcode tag, or the tag is not enabled.
          array_unshift($heap_index, '_string_');
          array_unshift($heap, $original_text);
        }
        // This is a valid shortcode tag, and self-closing.
        elseif (substr($c, -1, 1) == '/') {
          // Processes a self closing tag, - it has "/" at the end-
          /*
           * The exploded array elements meaning:
           * 0 - the full tag text?
           * 1/5 - An extra [] to allow for escaping Shortcodes with double [[]].
           * 2 - The Shortcode name.
           * 3 - The Shortcode argument list.
           * 4 - The content of a Shortcode when it wraps some content.
           */

          $m = array(
            $c,
            '',
            $tag,
            implode(' ', $ts),
            NULL,
            '',
          );
          array_unshift($heap_index, '_string_');
          array_unshift($heap, $this->processTag($m, $shortcodes));
        }
        // A closing tag, we can process the heap.
        elseif ($c[0] == '/') {
          $closing_tag = substr($c, 1);

          $process_heap = array();
          $process_heap_index = array();
          $found = FALSE;

          // Get elements from heap and process.
          do {
            $tag = array_shift($heap_index);
            $heap_text = array_shift($heap);

            if ($closing_tag == $tag) {
              // Process the whole tag.
              $m = array(
                $tag . ' ' . $heap_text,
                '',
                $tag,
                $heap_text,
                implode('', $process_heap),
                '',
              );
              $str = $this->processTag($m, $shortcodes);
              array_unshift($heap_index, '_string_');
              array_unshift($heap, $str);
              $found = TRUE;
            }
            else {
              array_unshift($process_heap, $heap_text);
              array_unshift($process_heap_index, $tag);
            }
          } while (!$found && $heap);

          if (!$found) {
            foreach ($process_heap as $val) {
              array_unshift($heap, $val);
            }
            foreach ($process_heap_index as $val) {
              array_unshift($heap_index, $val);
            }
          }
        }
        // A starting tag. Add into the heap.
        else {
          array_unshift($heap_index, $tag);
          array_unshift($heap, implode(' ', $ts));
        }
      }
      else {
        // Maybe not found a pair?
        array_unshift($heap_index, '_string_');
        array_unshift($heap, $c);
      }
      // End of foreach.
    }
    $output = (implode('', array_reverse($heap)));
    //Remove comment generate by devel
    $output = preg_replace('/<!--(.*)-->/Uis', '', $output);
    //Remove break line
    $output = preg_replace( "/\r|\n/", "", $output );
    return $output;
  }

  public function processTag($m, $enabled_shortcodes) {
    //parent::processTag($m, $enabled_shortcodes);
    $shortcode_token = $m[2];
    $shortcode = NULL;
    if (isset($enabled_shortcodes[$shortcode_token])) {
      $shortcode_id = $enabled_shortcodes[$shortcode_token]['id'];
      $shortcode = $this->getShortcodePlugin($shortcode_id);
    }

    // If shortcode does not exist or is not enabled, return input sans tokens.
    if (empty($shortcode)) {
      // This is an enclosing tag, means extra parameter is present.
      if (!is_null($m[4])) {
        return $m[1] . $m[4] . $m[5];
      }
      // This is a self-closing tag.
      else {
        return $m[1] . $m[5];
      }
    }

    // Process if shortcode exists and enabled.
    $attr = $this->parseAttrs($m[3]);
    //return $m[1] . $shortcode_id . $m[5];
    if (method_exists($shortcode, 'processBuilder')) {
      $content = $m[1] . $shortcode->processBuilder($attr, $m[4]) . $m[5];
    }
    else {
      $content = $m[1] . $shortcode->process($attr, $m[4]) . $m[5];
    }
    $return = array(
      '#theme' => 'dexp_builder_element',
      '#content' => $content,
      '#text' => $m[4],
      '#shortcode' => $shortcode_id,
      '#title' => $shortcode->getLabel(),
      '#attr' => $attr,
    );
    $output = render($return);
    //Remove comment generate by devel
    $output = preg_replace('/<!--(.*)-->/Uis', '', $output);
    //Remove break line
    $output = preg_replace( "/\r|\n/", "", $output );
    return $output;
  }

  protected function parseAttrs($text) {
    $attributes = array();
    $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
    $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
    $text = html_entity_decode($text);
    if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
      foreach ($match as $m) {
        if (!empty($m[1])) {
          $attributes[strtolower($m[1])] = stripcslashes($m[2]);
        }
        elseif (!empty($m[3])) {
          $attributes[strtolower($m[3])] = stripcslashes($m[4]);
        }
        elseif (!empty($m[5])) {
          $attributes[strtolower($m[5])] = stripcslashes($m[6]);
        }
        elseif (isset($m[7]) and strlen($m[7])) {
          $attributes[] = stripcslashes($m[7]);
        }
        elseif (isset($m[8])) {
          $attributes[] = stripcslashes($m[8]);
        }
      }
    }
    else {
      $attributes = ltrim($text);
    }
    return $attributes;
  }

  public function isValidShortcodeTag($tag, $format) {
    $tokens = $this->getShortcodePlugins($format);
    // TODO: This is case-sensitive right now, consider if it should be.
    return isset($tokens[$tag]);
  }

  function getShortcodePlugins($format, $reset = FALSE) {
    $plugins = &drupal_static(__FUNCTION__);
    if(!isset($plugins) || !isset($plugins[$format]) || $reset){
      if(!isset($plugins)){ 
        $plugins = array();
      }
      $text_format = FilterFormat::load($format);
      if (empty($text_format)){
        $plugins[$format] = array();
      }else{
        $filters = $text_format->get('filters');
        $plugins[$format] = array();
        if (!isset($filters['shortcode']) || empty($filters['shortcode']['settings']))
          return array();
        foreach ($filters['shortcode']['settings'] as $shortcode_id => $status) {
          $sc_service = \Drupal::service('shortcode');
          if($sc_service->isValidShortcodeTag($shortcode_id) && $status){
            $plugins[$format][$shortcode_id] = array('id' => $shortcode_id, 'shortcode' => $this->getShortcodePlugin($shortcode_id)); //$this->getShortcodePlugin($shortcode_id);
          }
        }
      }
    }
    return $plugins[$format];
  }

  function getShortcodePlugin($shortcode_id) {
    $plugins = &drupal_static(__FUNCTION__, array());
    if (!isset($plugins[$shortcode_id])) {

      /** @var \Drupal\shortcode\Shortcode\ShortcodePluginManager $type */
      $type = \Drupal::service('plugin.manager.shortcode');

      $plugins[$shortcode_id] = $type->createInstance($shortcode_id);
    }
    return $plugins[$shortcode_id];
  }

}
