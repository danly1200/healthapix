<?php
/**
 * @file
 * Contains \Drupal\fhir_blocks\Plugin\Block\FhirLogoCaption.
 */

namespace Drupal\fhir_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'FHIR' block.
 *
 * @Block(
 *   id = "fhir_logo_caption",
 *   admin_label = @Translation("FHIR Logo Caption"),
 *   category = @Translation("Custom FHIR logo caption Block")
 * )
 */
class FhirLogoCaption extends BlockBase
{
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    return array(
      '#type' => 'markup',
      '#markup' => 'Fhir Logo Caption block.',
    );
  }
}
