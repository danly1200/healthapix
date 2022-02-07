<?php

namespace Drupal\fhir_restapi\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;

/**
 * Class MyDeveloperAppsListController.
 */
class MyDeveloperAppsListController extends ControllerBase {
  /*
   * function getListMyApps
   *
   * @return string
   * Returns the List of My Apps.
   */
  public function getListMyApps() {
    $userId = \Drupal::currentUser()->id();
    $userDetails = User::load($userId);
    $developerId = $userDetails->get('apigee_edge_developer_id')->value;
    $storageDeveloperApp = \Drupal::entityTypeManager()
      ->getStorage('developer_app');
    $query = $storageDeveloperApp->getQuery()
      ->condition('developerId', $developerId);
    $entityIds = $query->execute();
    $developerApps = $storageDeveloperApp->loadMultiple($entityIds);
    $formBuilder = \Drupal::formBuilder();
    $analyticsFrom = 'Drupal\apigee_edge\Form\DeveloperAppAnalyticsFormForDeveloper';
    return [
      '#theme' => 'page__user__apps',
      '#my_apps' => $developerApps,
      '#user_id' => $userId,
      '#attached' => [
        'library' => [
          'fhir_restapi/fhir_restapi.myapps',
        ],
      ]
    ];
  }
}
