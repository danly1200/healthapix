<?php
/**
 * @file
 * Contains \Drupal\fhir_blocks\Plugin\Block\FhirAppGallery.
 */

namespace Drupal\fhir_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'FHIR' block.
 *
 * @Block(
 *   id = "fhir_app_gallery",
 *   admin_label = @Translation("FHIR App Gallery"),
 *   category = @Translation("Custom FHIR App Gallery Block")
 * )
 */
class FhirAppGallery extends BlockBase
{
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    return array(
      '#type' => 'markup',
      '#markup' => 'Fhir App Gallery block.',
    );
  }
}
