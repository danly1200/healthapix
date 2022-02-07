<?php

namespace Drupal\fhir_swagger\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Defines SwaggerNodeController class
 */
class SwaggerNodeController extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
   */
  public function content() {
    /**
     * get fid from swagger content type to stop duplicate node creation
     */
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'smartdocs')
      ->execute();
    $nodes = Node::loadMultiple($nids);
    $node_fid = [];
    foreach ($nodes as $node_content) {
      $node_fid[] = $node_content->field_smartdocs->target_id;
    }
    $mask = '(json|yaml)';
    $file_scan_yaml = [];
    /**
     * @var  $filescans scan all the files with json and yaml extension
     */
    // Get taxonomy term storage.
    $taxonomyStorage = \Drupal::service('entity.manager')->getStorage('taxonomy_term');
    // Set name properties.
    $properties['name'] = 'term_name';
    // Set vocabulary - not important.
    if (!empty($vocabulary))
      $properties['vid'] = $vocabulary;

    // Load taxonomy term by properties.
    $terms = $taxonomyStorage->loadByProperties($properties);
    $term = reset($terms);

    $path = DRUPAL_ROOT . '/' . drupal_get_path('module', 'fhir_swagger') . "/swagger";
    $path_stu3 = DRUPAL_ROOT . '/' . drupal_get_path('module', 'fhir_swagger') . "/swagger-stu3";
    $filescans = file_scan_directory($path, $mask);
    $filescans_stu3 = file_scan_directory($path_stu3, $mask);
    if ($filescans) {
      foreach ($filescans as $filescan) {
        $file_contents = file_get_contents($filescan->uri);
        if (!is_dir('public://swagger-files')) {
          drupal_mkdir('public://swagger-files', 0777);
        }
        $file_path = 'public://swagger-files/' . $filescan->filename;
        $scanfilesave = file_save_data($file_contents, $file_path, FileSystemInterface::EXISTS_REPLACE);
        $file_scan_yaml[] = $scanfilesave->id();
      }
    }
    if ($filescans_stu3) {
      foreach ($filescans_stu3 as $filescan_stu3) {
        $file_contents_stu3 = file_get_contents($filescan_stu3->uri);
        if (!is_dir('public://swagger-files-stu3')) {
          drupal_mkdir('public://swagger-files-stu3', 0777);
        }
        $file_path = 'public://swagger-files-stu3/' . $filescan_stu3->filename;
        $scanfilesave_stu3 = file_save_data($file_contents_stu3, $file_path, FileSystemInterface::EXISTS_REPLACE);
        $file_scan_yaml[] = $scanfilesave_stu3->id();
      }
    }
    /**
     * @var  $scan_results contains unique fid's
     */
    if ($file_scan_yaml) {
      $scan_results = array_diff($file_scan_yaml, $node_fid);
      foreach ($scan_results as $scan_result) {
        $operations[] = [
          [
            $this->batchFunctionScan($scan_result),
            [],
          ],
        ];
      }
      /**
       * @var  $batch  create batch process
       */
      $batch = [
        'title' => t('Swagger Nodes Creating'),
        'operations' => $operations,
        'finished' => $this->finished_callback(),
      ];
      batch_set($batch);
      return [
        '#type' => 'markup',
        '#markup' => $this->t('Swagger Nodes Created!'),
      ];
    }
    else {
      return [
        '#type' => 'markup',
        '#markup' => $this->t('No json/yaml files found in directory!'),
      ];
    }

  }

  /**
   * function to create nodes for each unique fid's passed
   */
  function batchFunctionScan($field_tid) {
    $file = File::load($field_tid);
    $ext = pathinfo($file->url())['extension'];
    $file_dir = explode("://", drupal_dirname($file->getFileUri()));
    if ($file_dir[1] == 'swagger-files') {
      $terms = taxonomy_term_load_multiple_by_name("STU2");
      $taxid = key($terms);
    }
    else {
      $terms = taxonomy_term_load_multiple_by_name("STU3");
      $taxid = key($terms);
    }
    $file_name = basename($file->url(), '.' . $ext);
    $node_title = urldecode($file_name);
    $node = Node::create([
      'type' => 'smartdocs',
      'title' => $node_title,
      'field_description' => $node_title['info']['description'],
      'field_smartdocs' => [
        'target_id' => $field_tid,
      ],
      'field_api_version' => [
        'target_id' => $taxid,
      ],
    ]);
    $node->save();
  }

  /**
   * Callback function after batch process
   */
  function finished_callback() {
    $path = '/';
    return new RedirectResponse($path);
  }
}

