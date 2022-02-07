<?php

/**
 * @file
 * Definition of Drupal\dexp_grid\Plugin\views\style\ViewBootstrapGrid.
 */
namespace Drupal\dexp_grid\Plugin\views\style;

use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Style plugin to render each item as a col in a Bootstrap row.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "views_dexp_grid_bootstrap",
 *   title = @Translation("DrupalExp: Bootstrap Grid"),
 *   help = @Translation("Displays rows in a Bootstrap column."),
 *   theme = "views_dexp_grid_bootstrap",
 *   display_types = {"normal"}
 * )
 */
class ViewBootstrapGrid extends StylePluginBase{

  protected $usesRowPlugin = TRUE;
  /**
   * Definition.
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['wrapper_class'] = array('default' => '');
    $options['wrapper_row'] = array('default' => '');
    $options['wrapper_row_class'] = array('default' => '');
    $options['xl_cols'] = array('default' => 4);
    $options['lg_cols'] = array('default' => 4);
    $options['md_cols'] = array('default' => 4);
    $options['sm_cols'] = array('default' => 2);
    $options['xs_cols'] = array('default' => 1);
    $options['col_padding'] = array('default' => 30);
    return $options;
  }

  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['wrapper_class'] = array(
        '#type' => 'textfield',
        '#title' => t('Wrapper class(es)'),
        '#description' => t('The class to provide on the wrapper, outside the grid.'),
        '#default_value' => $this->options['wrapper_class'],
    );
    $form['wrapper_row'] = array(
        '#type' => 'checkbox',
        '#title' => t('Add Row Wrapper'),
        '#default_value' => $this->options['wrapper_row'],
    );
    $form['wrapper_row_class'] = array(
        '#type' => 'textfield',
        '#title' => t('Row Wrapper Class'),
        '#default_value' => $this->options['wrapper_row_class'],
        '#states' => array(
          ':input[name=wrapper_row]' => array(
            'checked' => TRUE,
          ),
        ),
    );
    $form['xl_cols'] = array(
        '#type' => 'number',
        '#title' => t('XL columns'),
        '#description' => t('Number of columns on large screen (width ≥ 1200px)'),
        '#min' => 1,
        '#max' => 12,
        //'#options' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11, 12 => 12),
        '#default_value' => $this->options['lg_cols'],
        '#description' => t('Any value from 1 to 12 is works, but we recommend using 1,2,3,4,6,12 for best result.')
    );
    $form['lg_cols'] = array(
        '#type' => 'number',
        '#title' => t('LG columns'),
        '#description' => t('Number of columns on large screen (width ≥ 992px)'),
        '#min' => 1,
        '#max' => 12,
        //'#options' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 6 => 6, 12 => 12),
        '#default_value' => $this->options['lg_cols'],
        '#description' => t('Any value from 1 to 12 is works, but we recommend using 1,2,3,4,6,12 for best result.')
    );
    $form['md_cols'] = array(
        '#type' => 'number',
        '#title' => t('MD columns'),
        '#description' => t('Number of columns on medium screen (width ≥ 768px)'),
        '#min' => 1,
        '#max' => 12,
        //'#options' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 6 => 6, 12 => 12),
        '#default_value' => $this->options['md_cols'],
        '#description' => t('Any value from 1 to 12 is works, but we recommend using 1,2,3,4,6,12 for best result.')
    );
    $form['sm_cols'] = array(
        '#type' => 'number',
        '#title' => t('SM columns'),
        '#description' => t('Number of columns on small screen (width ≥ 576px)'),
        '#min' => 1,
        '#max' => 12,
        //'#options' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 6 => 6, 12 => 12),
        '#default_value' => $this->options['sm_cols'],
        '#description' => t('Any value from 1 to 12 is works, but we recommend using 1,2,3,4,6,12 for best result.')
    );
    $form['xs_cols'] = array(
        '#type' => 'number',
        '#title' => t('XS columns'),
        '#description' => t('Number of columns on extra small screen (width < 576px)'),
        '#min' => 1,
        '#max' => 12,
        //'#options' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 6 => 6, 12 => 12),
        '#default_value' => $this->options['xs_cols'],
        '#description' => t('Any value from 1 to 12 is works, but we recommend using 1,2,3,4,6,12 for best result.')
    );
    $form['col_padding'] = array(
      '#type' => 'number',
      '#min' => 0,
      '#step' => 2,
      '#title' => t('Gutters'),
      '#field_suffix' => 'px',
      '#description' => t('The gutters between columns. Gutters must be even number'),
      '#default_value' => $this->options['col_padding'],
    );

  }
}
