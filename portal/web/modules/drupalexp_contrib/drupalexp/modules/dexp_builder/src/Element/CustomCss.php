<?php

namespace Drupal\dexp_builder\Element;

use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a form element for choosing a color.
 *
 * Properties:
 * - #properties: list of css properties.
 *
 * Example usage:
 * @code
 * $form['custom_css'] = array(
 *   '#type' => 'custom_css',
 *   '#title' => $this->t('Custom Css'),
 *   '#properties' => array('background-color', 'border'),
 * );
 * @endcode
 *
 * @FormElement("custom_css")
 */
class CustomCss extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return array(
      '#input' => TRUE,
      '#process' => array(
        array($class, 'processAjaxForm'),
      ),
      '#pre_render' => array(
        array($class, 'preRenderColor'),
      ),
      '#theme' => 'custom_css',
      '#theme_wrappers' => array('form_element'),
    );
  }

  /**
   * Prepares a #type 'custom_css' render element for input.html.twig.
   *
   * @param array $element
   *   An associative array containing the properties of the element.
   *   Properties used: #title, #value, #description, #attributes.
   *
   * @return array
   *   The $element with prepared variables ready for input.html.twig.
   */
  public static function preRenderColor($element) {
    $element['#attributes']['type'] = 'hidden';
    $element['#attached']['library'][] = 'dexp_builder/custom_css';
    Element::setAttributes($element, array('id', 'name', 'value'));
    static::setAttributes($element, array('form-custom-css'));

    return $element;
  }

}