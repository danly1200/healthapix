<?php

namespace Drupal\dexp_block\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'dexp_field_view_embed' widget.
 *
 * @FieldWidget(
 *   id = "dexp_field_view_embed",
 *   module = "dexp_block",
 *   label = @Translation("View Embed"),
 *   field_types = {
 *     "dexp_field_view_embed"
 *   }
 * )
 */
class DexpViewEmbedWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $blocks = \Drupal::service('plugin.manager.block')->getDefinitions();
    $options = ['' => t('None')];
    foreach (\Drupal\views\Views::getAllViews() as $view){
		$options[$view->label()] = [];
		foreach($view->get('display') as $display){
			$options[$view->label()][$view->id() . ':' . $display['id']] = $display['display_title'];
		}
	}
	$value = isset($items[$delta]->value) ? $items[$delta]->value : '';
    $element += array(
      '#type' => 'select',
      '#default_value' => $value,
      '#options' => $options,
      '#element_validate' => array(
        array($this, 'validate'),
      ),
    );
    return array('value' => $element);
  }

  /**
   * Validate the color text field.
   */
  public function validate($element, FormStateInterface $form_state) {
  	return true;
    $value = $element['#value'];
    if (strlen($value) == 0) {
      $form_state->setValueForElement($element, '');
      return;
    }
    if (!preg_match('/^#([a-f0-9]{6})$/iD', strtolower($value))) {
      $form_state->setError($element, t("Color must be a 6-digit hexadecimal value, suitable for CSS."));
    }
  }

}