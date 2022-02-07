<?php

/**
 * @file
 * Contains dexp_slider.page.inc..
 *
 * Page callback for Slider entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Slider templates.
 *
 * Default template: dexp_slider.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_dexp_slider(array &$variables) {
  // Fetch Slider Entity Object.
  $dexp_slider = $variables['elements']['#dexp_slider'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
