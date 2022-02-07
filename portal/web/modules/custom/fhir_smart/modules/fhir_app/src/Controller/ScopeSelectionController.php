<?php

namespace Drupal\fhir_app\Controller;

use Drupal\Core\Controller\ControllerBase;
use Laminas\Diactoros\Response\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ScopeSelectionController.
 */
class ScopeSelectionController extends ControllerBase {

  /**
   * Getscopes.
   *
   * @return string
   *   Return Hello string.
   */
  public function getScopes($fhir_server) {
    $scopes_list = getAppScopeList($fhir_server);
    return new JsonResponse($scopes_list);
  }

}
