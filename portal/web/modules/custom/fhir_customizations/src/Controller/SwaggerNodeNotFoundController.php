<?php

namespace Drupal\fhir_customizations\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Defines SwaggerNodeController class
 */
class SwaggerNodeNotFoundController extends ControllerBase {

  /**
   * The default 404 content.
   *
   * @return array
   *   A render array containing the message to display for 404 pages.
   */
  public function swagger_not_found() {
    return [
      '#markup' => '<div class="swagger-not-found-wrap">' . $this->t('Currently selected end point is deprecated') . '</div>',
    ];
  }


}

