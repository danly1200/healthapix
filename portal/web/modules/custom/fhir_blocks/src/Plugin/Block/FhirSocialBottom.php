<?php
/**
 * @file
 * Contains \Drupal\fhir_blocks\Plugin\Block\FhirSocialBottom.
 */

namespace Drupal\fhir_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'FHIR' block.
 *
 * @Block(
 *   id = "fhir_social_bottom",
 *   admin_label = @Translation("FHIR Social Bottom"),
 *   category = @Translation("Custom FHIR Social Bottom Block")
 * )
 */
class FhirSocialBottom extends BlockBase
{
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    return array(
      '#type' => 'markup',
      '#markup' => 'Fhir Social Bottom block.',
    );
  }
}
