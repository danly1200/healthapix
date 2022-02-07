<?php

namespace Drupal\dexp_builder\FontIcon;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides a Fonticon plugin manager.
 */
class FontIconPluginManager extends DefaultPluginManager {

  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/FontIcon', $namespaces, $module_handler, 'Drupal\dexp_builder\Plugin\FontIconInterface', 'Drupal\dexp_builder\Annotation\FontIcon');

    // Allow other modules to alter shortcode info via hook_shortcode_info_alter.
    $this->alterInfo('fonticon_info');
    $this->setCacheBackend($cache_backend, 'fonticon_info_plugins');
  }

}
