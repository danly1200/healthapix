<?php

namespace Drupal\dexp_builder\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\BaseCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\shortcode\Shortcode\ShortcodeService;
use \Drupal\dexp_builder\Controller\BuilderShortcodeService;
use Drupal\Core\Url;

class BuilderController extends ControllerBase {

  public function parse() {
    $text = \Drupal::request()->get('text');
    $format = \Drupal::request()->get('format');
    $builder_selector = \Drupal::request()->get('selector');
    $response = new AjaxResponse();
    $builderShortcodeService = \Drupal::service('dexp_builder.shortcode');
    $ouput = $builderShortcodeService->process($text, NULL, $format);
    $response->addCommand(new HtmlCommand($builder_selector, $ouput));
    // Return ajax response.
    return $response;
  }

  public static function shortcode_settings_form() {
    $form = array();
    $form['class'] = array(
      '#title' => 'Class',
      '#type' => 'textfield',
    );
    $content = ['#markup' => 'Hello'];
    $content['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $response = new AjaxResponse();
    $response->addCommand(new OpenModalDialogCommand('settings form', $content));
    return $response;
  }

}
