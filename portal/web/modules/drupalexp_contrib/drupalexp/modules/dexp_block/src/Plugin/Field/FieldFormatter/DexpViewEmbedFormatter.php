<?php

namespace Drupal\dexp_block\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\views\Views;

/**
 * Plugin implementation of the 'dexp_field_view_embed' formatter.
 *
 * @FieldFormatter(
 *   id = "dexp_field_view_embed",
 *   label = @Translation("Embed block"),
 *   field_types = {
 *     "dexp_field_view_embed"
 *   }
 * )
 */
class DexpViewEmbedFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();

    foreach ($items as $delta => $item) {
      $value = explode(':', $item->value);
      $view = Views::getView($value[0]);
      if($view){
        $elements[$delta] = $view->buildRenderable($value[1]);
      }
    }
    return $elements;
  }

}