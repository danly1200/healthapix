<?php

namespace Drupal\fhir_server\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class GetDetailsFromApigeeForm class.
 */
class GetDetailsFromApigeeForm extends ControllerBase {

  /*
  Function to fetch the API product details from apigee
  */

  public function getProductDetailsFromApigee(Request $request) {
    $api_product_name = json_decode(\Drupal::request()->getcontent());
    $attributes = get_api_product_attributes($api_product_name);
    $attributes['name'] = $api_product_name;
    return new JsonResponse($attributes);
  }

}
