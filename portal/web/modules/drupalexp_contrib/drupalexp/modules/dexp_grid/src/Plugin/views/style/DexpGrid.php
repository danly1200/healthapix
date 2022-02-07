<?php

namespace Drupal\dexp_grid\Plugin\views\style;

use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Style plugin for the cards view.
 *
 * @ViewsStyle(
 *   id = "dexp_grid_views",
 *   title = @Translation("DrupalExp: Shuffle Grid"),
 *   help = @Translation("Display content in a responsive grid."),
 *   theme = "views_dexp_grid",
 *   display_types = {"normal"}
 * )
 */
class DexpGrid extends StylePluginBase {

  /**
   * Specifies if the plugin uses row plugins.
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  // Class methods…
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['grid_style'] = ['default' => 'classic'];
    $options['lg_cols'] = ['default' => 4];
    $options['md_cols'] = ['default' => 4];
    $options['sm_cols'] = ['default' => 2];
    $options['xs_cols'] = ['default' => 1];
    $options['grid_margin'] = ['default' => 30];
    $options['grid_filter'] = ['default' => 0];
    $options['grid_filter_show_all_text'] = ['default' => 'Show all'];
    $options['grid_ratio'] = ['default' => 1];
    $options['grid_masonry_background'] = ['default' => 0];
    return $options;
  }

  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['grid_style'] = array(
      '#type' => 'select',
      '#title' => t('Grid Style'),
      '#options' => array(
        'masonry' => t('Masonry'),
        'masonry_resize' => t('Masonry Resize'),
      ),
      '#attributes' => array('class' => array('grid-style')),
      '#default_value' => $this->options['grid_style'],
    );
    
    $field_options = array();
    $field_image_options = array('' => '-None-');
    $fields = \Drupal::entityManager()->getFieldMapByFieldType('image');
    foreach ($fields as $k_field => $field) {
      $field_options[$k_field] = $field;
    }
    $field_options_node = $field_options['node'];
    foreach ($field_options_node as $key => $value) {
      $field_image_options[$key] = $key;
    }

    $form['gird_masonry_background'] = array(
      '#type' => 'select',
      '#title' => t('Image'),
      '#options' => $field_image_options,
      '#default_value' => $this->options['gird_masonry_background'],
      '#description' => t('Use image field as background of item'),
      '#states' => array(
        'visible' => array(
          '.grid-style' => array('value' => 'masonry_resize'),
        )
      )
    );

    $form['grid_ratio'] = array(
      '#type' => 'textfield',
      '#title' => t('Aspect ratio'),
      '#description' => t('Proportional relationship between width and height of a standard item.'),
      '#default_value' => $this->options['grid_ratio'],
      '#states' => array(
        'visible' => array(
          '.grid-style' => array('value' => 'masonry_resize'),
        )
      )
    );

    $form['lg_cols'] = array(
        '#type' => 'number',
        '#title' => t('LG columns'),
        '#description' => t('Number of columns on large screen (width ≥ 1200px)'),
        '#min' => 1,
        '#max' => 12,
        '#default_value' => $this->options['lg_cols'],
    );
    
    $form['md_cols'] = array(
        '#type' => 'number',
        '#title' => t('MD columns'),
        '#description' => t('Number of columns on medium screen (width ≥ 992px)'),
        '#min' => 1,
        '#max' => 12,
        '#default_value' => $this->options['md_cols'],
    );
    
    $form['sm_cols'] = array(
        '#type' => 'number',
        '#title' => t('SM columns'),
        '#description' => t('Number of columns on small screen (width ≥ 768px)'),
        '#min' => 1,
        '#max' => 12,
        '#default_value' => $this->options['sm_cols'],
    );
    
    $form['xs_cols'] = array(
        '#type' => 'number',
        '#title' => t('XS columns'),
        '#description' => t('Number of columns on extra small screen (width < 768px)'),
        '#min' => 1,
        '#max' => 12,
        '#default_value' => $this->options['xs_cols'],
    );

    $form['grid_margin'] = array(
      '#type' => 'number',
      '#title' => t('Margin'),
      '#description' => t('The spacing beetween items'),
      '#default_value' => $this->options['grid_margin'],
      '#min' => 0,
      '#field_suffix' => 'px',
    );

    $form['grid_filter'] = array(
      '#type' => 'checkbox',
      '#title' => t('Add filter'),
      '#description' => t('Filter content by taxonomy term'),
      '#default_value' => $this->options['grid_filter'],
    );

    $opts = array('' => '-' . t('Select') . '-');
    $tmp = array();
    foreach (taxonomy_vocabulary_load_multiple() as $name => $vocab) {
      $tmp[$name] = $vocab->get('name');
    }
    
    $opts += $tmp;
    $form['grid_filter_vocabulary'] = array(
      '#type' => 'select',
      '#title' => t('Filter Vocabulary'),
      '#options' => $opts,
      '#description' => t('Which taxonomy vocabulary do you want to use for the filter'),
      '#default_value' => $this->options['grid_filter_vocabulary'],
      '#states' => array(
        'visible' => array(
          'input[name=style_options\\[grid_filter\\]]' => array('checked' => true),
        ),
      )
    );
    
    $form['grid_filter_show_all_text'] = array(
      '#type' => 'textfield',
      '#title' => t('Show all button text'),
      '#default_value' => $this->options['grid_filter_show_all_text'],
      '#description' => t('Leave blank to hide button'),
      '#states' => array(
        'visible' => array(
          'input[name=style_options\\[grid_filter\\]]' => array('checked' => true),
        ),
      )
    );
  }

}
