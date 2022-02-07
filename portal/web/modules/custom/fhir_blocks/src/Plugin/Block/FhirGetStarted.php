<?php
/**
 * @file
 * Contains \Drupal\fhir_blocks\Plugin\Block\FhirGetStarted.
 */

namespace Drupal\fhir_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'FHIR' block.
 *
 * @Block(
 *   id = "fhir_get_started",
 *   admin_label = @Translation("FHIR get started"),
 *   category = @Translation("Custom FHIR Get Started block")
 * )
 */
class FhirGetStarted extends BlockBase
{
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    return array(
      '#type' => 'markup',
      '#markup' => 'FHIR Get Started Block.',
    );
  }
}
