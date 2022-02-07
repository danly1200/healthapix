<?php

namespace Drupal\fhir_datasets\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;

/**
 * Controller to list all the available FHIR datasets.
 */
class FHIRDatasetList extends ControllerBase {

  /**
   * Returns a render-able array for a FHIR datasets listing page.
   */
  public function getDatasetList() {
    $vname = 'fhir_datasets';
    $datasets = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vname, 0, NULL);
    $dataset = [];
    $apps_count = get_fhir_server_associated_apps();

    foreach ($datasets as $fhir_dataset) {
      $fhir_servers = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
        'field_fhir_version_autofill' => $fhir_dataset->tid,
        'status' => 1,
      ]);

//      if(!empty($fhir_servers)){
        $server_list = [];
        foreach ($fhir_servers as $server) {
          $server_title = $server->getTitle();
          $fid = $server->get('field_server_file')->target_id;
          if($fid) {
            $file = \Drupal\file\Entity\File::load($fid);
            $fileurl = file_create_url($file->getFileUri());
            $server_list[$server->id()] = array('node_title'=>$server_title,'url'=>$fileurl);
          } else {
            $server_list[$server->id()] = array('node_title'=>$server_title);
          }
        }
        $dataset[$fhir_dataset->tid.'#$'.$fhir_dataset->name] = $server_list;
//      }
    }

    return array(
      '#theme' => 'fhir_datasets_listing',
      '#datasets' => $dataset,
      '#apps_count' => $apps_count,
      '#attached' => [
        'library' => [
          'fhir_datasets/fhir_datasets.datasetlist',
        ],
      ],
    );
  }

}
