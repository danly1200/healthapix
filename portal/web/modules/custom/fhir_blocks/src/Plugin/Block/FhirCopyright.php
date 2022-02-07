<?php
/**
 * @file
 * Contains \Drupal\fhir_blocks\Plugin\Block\FhirCopyright.
 */

namespace Drupal\fhir_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'FHIR' block.
 *
 * @Block(
 *   id = "fhir_copyright",
 *   admin_label = @Translation("FHIR Copyright"),
 *   category = @Translation("Custom FHIR Copyright Block")
 * )
 */
class FhirCopyright extends BlockBase
{
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    return array(
      '#type' => 'markup',
      '#markup' => 'Fhir Copyright block.',
    );
  }
}
