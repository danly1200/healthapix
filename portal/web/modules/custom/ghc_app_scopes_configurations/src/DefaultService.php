<?php

namespace Drupal\ghc_app_scopes_configurations;

use Drupal\node\Entity\Node;

/**
 * Class DefaultService.
 */
class DefaultService implements DefaultServiceInterface {

  /**
   * Constructs a new DefaultService object.
   */
  public function __construct() {

  }

  public function getScopes(){
    $availableScopes = [];

    // Load active APIResources
    $nids = \Drupal::entityQuery('node')
      ->condition('type','smartdocs')
      ->sort('title', 'ASC')
      ->execute();
    $nodes =  Node::loadMultiple($nids);

    // Load active configurations
    $config = \Drupal::config('ghc_app_scopes_configurations.scopeconfig');

    // Config format for resource & scopes enabling.
    //    $configFormat = [
    //      'showToPatientScope',
    //      'showToUserScope',
    //      'showWritePatient',
    //      'showReadPatient',
    //      'showWriteUser',
    //      'showReadUser'
    //    ];

    foreach ($nodes as $node) {
//    $label = str_replace(' ', '_', $node->label());
      $label =  preg_replace('/\s/', '', $node->label());
      $api_version = $node->get('field_api_version')->target_id;
      if(!array_key_exists($label,$availableScopes)) {
        $availableScopes[$label] = [
          'resource' => $label,
          'api_version' => $api_version,
          'enabled_for_patient_scope' => $config->get("$label"."___showToPatientScope"),
          'enabled_for_user_scope' => $config->get("$label"."___showToUserScope"),

          'patient_scope' => [
            'write_scope_value' => "patient/".$label.".write",
            'read_scope_value' => "patient/".$label.".read",
            'show_write_for_patient_scope' => $config->get("$label"."___showWritePatient"),
            'show_read_for_patient_scope' => $config->get("$label"."___showReadPatient")
          ],

          'user_scope' => [
            'write_scope_value' => "user/".$label.".write",
            'read_scope_value' => "user/".$label.".read",
            'show_write_for_user_scope' => $config->get("$label"."___showWriteUser"),
            'show_read_for_user_scope' => $config->get("$label"."___showReadUser"),
          ]
        ];
      }  else {
        $values = [
          'type' => 'smartdocs',
          'title' => $node->label(),
          'status'=> 1,
        ];
        $fhir_swagger_apis = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties($values);
        $api_version_arr = [];
        foreach ($fhir_swagger_apis as $res_node) {
          $api_version_arr[] = $res_node->get('field_api_version')->target_id;
        }
        $availableScopes[$label]['api_version'] = $api_version_arr;
      }
    }

    return $availableScopes;


  }

}
