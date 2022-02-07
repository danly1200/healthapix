<?php

/**
 * @file
 * Contains \Drupal\dexp_grid\Controller\DexpGridMasonrySaveController.
 */

namespace Drupal\dexp_grid\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Ajax\CssCommand;

class DexpGridController extends ControllerBase {

  public function update(Request $request) {
    $config = \Drupal::service('config.factory')->getEditable('dexp_grid.config');
    $key = $request->request->get('view') . '.' . $request->request->get('display_id');
    $value = $config->get($key);
    $value[$request->request->get('index')] = array(
      'width' => $request->request->get('width'),
      'height' => $request->request->get('height'),
    );
    $config->set($key, $value);
    $config->save();
    $tags = ['config:views.view.' . $request->request->get('view')];
    \Drupal::service('cache_tags.invalidator')->invalidateTags($tags);
    $ajax_response = new AjaxResponse();
    $ajax_response->addCommand(new CssCommand('#dexp-grid-message', array('display' => 'none')));
    return $ajax_response;
  }

}
