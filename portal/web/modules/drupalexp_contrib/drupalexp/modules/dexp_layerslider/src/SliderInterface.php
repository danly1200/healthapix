<?php

/**
 * @file
 * Contains \Drupal\dexp_layerslider\SliderInterface.
 */

namespace Drupal\dexp_layerslider;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Slider entities.
 *
 * @ingroup dexp_layerslider
 */
interface SliderInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {
  // Add get/set methods for your configuration properties here.

}
