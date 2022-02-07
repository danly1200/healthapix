<?php

namespace Drupal\fhir_restapi\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Class MyAppAnalyticsController.
 */
class MyAppAnalyticsController extends ControllerBase {

  /*
   * function getMyAppName.
   *
   * @return string
   */
  public function getMyAppName($app) {
    $userId = \Drupal::currentUser()->id();
    $user = User::load($userId);
    $developerId = $user->get('apigee_edge_developer_id')->value;
    $entityId = \Drupal::entityQuery('developer_app')->condition('name', $app)->condition('developerId',$developerId)->execute();
    $entity = \Drupal::entityTypeManager()->getStorage('developer_app')->load($entityId[0]);
    $displayName = $entity->label();
    $url = Url::fromRoute("fhir_restapi.my_developer_apps_list_controller_getListMyApps")->toString();
    return [
      '#type' => 'markup',
      '#markup' => 'Analytics' . " - " . t($displayName),
    ];
  }
  /*
   * function getMyAppAnalytic.
   *
   * @return string
   */
  public function getMyAppAnalytic($app) {
    $userId = \Drupal::currentUser()->id();
    $user = User::load($userId);
    $developerId = $user->get('apigee_edge_developer_id')->value;
    $entityId = \Drupal::entityQuery('developer_app')->condition('name', $app)->condition('developerId',$developerId)->execute();
    $entity = \Drupal::entityTypeManager()->getStorage('developer_app')->load($entityId[0]);
    $displayName = $entity->label();
    $form = \Drupal::formBuilder()->getForm('\Drupal\apigee_edge\Form\DeveloperAppAnalyticsFormForDeveloper',$entity);
    return [
      '#theme' => 'page__user__myapps__analytics',
      '#analytics_form' => $form,
      '#displayName' => $displayName,
    ];
  }
}
