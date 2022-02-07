<?php

/**
 * @file
 * Contains \Drupal\dexp_layerslider\SliderListBuilder.
 */

namespace Drupal\dexp_layerslider;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Slider entities.
 *
 * @ingroup dexp_layerslider
 */
class SliderListBuilder extends EntityListBuilder {
  use LinkGeneratorTrait;
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Slider ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\dexp_layerslider\Entity\Slider */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $this->getLabel($entity),
      new Url(
        'entity.dexp_slider.edit_form', array(
          'dexp_slider' => $entity->id(),
        )
      )
    );
    $row += parent::buildRow($entity);
    
    $row['operations']['data']['#links']['edit_slides'] = [
      'title' => t('Edit Slides'),
      'weight' => 0,
      'url' => \Drupal\Core\Url::fromRoute('entity.dexp_slider.edit_slides_form', ['dexp_slider' => $entity->id()]),
    ];
    
    $row['operations']['data']['#links']['slider_settings'] = [
      'title' => t('Settings'),
      'weight' => 1,
      'url' => \Drupal\Core\Url::fromRoute('entity.dexp_slider.settings_form', ['dexp_slider' => $entity->id()]),
    ];
    
    $row['operations']['data']['#links']['slider_duplicate'] = [
      'title' => t('Duplicate'),
      'weight' => 2,
      'url' => \Drupal\Core\Url::fromRoute('entity.dexp_slider.duplicate', ['dexp_slider' => $entity->id()]),
    ];
    
    $row['operations']['data']['#links']['slider_export'] = [
      'title' => t('Export'),
      'weight' => 2,
      'url' => \Drupal\Core\Url::fromRoute('dexp_slider.export', ['dexp_slider' => $entity->id()]),
    ];
    
    $weight = array();
    foreach ($row['operations']['data']['#links'] as $key => $link)
    {
        $weight[$key] = $link['weight'];
    }
    array_multisort($weight, SORT_ASC, $row['operations']['data']['#links']);
    
    return $row;
  }

}
