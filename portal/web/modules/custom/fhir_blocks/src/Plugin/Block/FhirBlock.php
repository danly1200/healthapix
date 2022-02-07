<?php
/**
 * @file
 * Contains \Drupal\fhir_blocks\Plugin\Block\FhirBlock.
 */

namespace Drupal\fhir_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'FHIR' block.
 *
 * @Block(
 *   id = "fhir_block",
 *   admin_label = @Translation("FHIR block"),
 *   category = @Translation("Custom FHIR block")
 * )
 */
class FhirBlock extends BlockBase
{
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    return array(
      '#type' => 'markup',
      '#markup' => 'This block list the Fhir block.',
    );
  }
}
