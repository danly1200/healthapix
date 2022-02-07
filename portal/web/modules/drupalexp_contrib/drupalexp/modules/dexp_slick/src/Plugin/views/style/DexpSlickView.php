<?php

namespace Drupal\dexp_slick\Plugin\views\style;

use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Style plugin for the dexp_bxslider view.
 *
 * @ViewsStyle(
 *   id = "dexp_slick",
 *   title = @Translation("DrupalExp: Slick Carousel"),
 *   help = @Translation("Display content in a slick carousel format."),
 *   theme = "views_dexp_slick",
 *   display_types = {"normal"}
 * )
 */
class DexpSlickView extends StylePluginBase {

  /**
   * Specifies if the plugin uses row plugins.
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  // Class methods…
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['slick_general'] = [
      'contains' => [
        'mode' => ['default' => 'horizontal'],
        'speed' => ['default' => 500],
        'slideMargin' => ['default' => 0],
        'initialSlide' => ['default' => 0],
        'infinite' => ['default' => 1],
        'hideControlOnEnd' => ['default' => 0],
        'rows' => ['default' => 1],
        'autoplay' => ['default' => 0],
        'autoplaySpeed' => ['default' => 5000],
        'pauseOnHover' => ['default' => 0],
        'hideControlOnEnd' => ['default' => 0],
        'swipe' => ['default' => 1]
      ]
    ];
    $options['slick_carousel'] = [
      'contains' => [
        'lg' => ['default' => 4],
        'md' => ['default' => 4],
        'sm' => ['default' => 2],
        'xs' => ['default' => 1],
      ],
    ];
    $options['slick_controls'] = [
      'contains' => [
        'dots' => ['default' => 1],
        'arrows' => ['default' => 1],
        'arrows_position' => ['default' => ''],
      ]
    ];
    $options['slick_centermode'] = [
      'contains' => [
        'centerMode' => ['default' => 0],
        'centerPadding' => ['default' => '50px'],
      ]
    ];
    return $options;
  }

  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['slick_general'] = [
      '#type' => 'details',
      '#title' => t('General'),
      '#open' => TRUE,
    ];
    
    $form['slick_general']['mode'] = [
      '#type' => 'select',
      '#title' => t('Mode'),
      '#description' => t('Type of transition between slides'),
      '#options' => ['horizontal' => 'Horizontal', 'vertical' => 'Vertical', 'fade' => 'Fade'],
      '#default_value' => $this->options['slick_general']['mode'],
      '#parent' => 'slick_general',
    ];
    
    $form['slick_general']['rows'] = [
      '#type' => 'number',
      '#min' => 1,
      '#title' => t('Rows'),
      '#description' => t('Setting this to more than 1 initializes grid mode'),
      '#default_value' => $this->options['slick_general']['rows'],
    ];
    
    $form['slick_general']['autoplay'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Auto Play'),
      '#description' => $this->t('Enables Autoplay'),
      '#default_value' => $this->options['slick_general']['autoplay'],
    ];
    
    $form['slick_general']['autoplaySpeed'] = [
      '#type' => 'number',
      '#title' => $this->t('Auto Play Speed'),
      '#description' => $this->t('Autoplay Speed in milliseconds'),
      '#default_value' => $this->options['slick_general']['autoplaySpeed'],
      '#states' => [
        'visible' => [
          ':input[name*=autoplay]' => ['checked' => TRUE]
        ]
      ]
    ];
    
    $form['slick_general']['pauseOnHover'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Pause on hover'),
      '#default_value' => $this->options['slick_general']['pauseOnHover'],
      '#states' => [
        'visible' => [
          ':input[name*=autoplay]' => ['checked' => TRUE]
        ]
      ]
    ];
    
    $form['slick_general']['speed'] = [
      '#type' => 'textfield',
      '#title' => t('Transition duration'),
      '#description' => t('Slide transition duration (in ms)'),
      '#default_value' => $this->options['slick_general']['speed'],
    ];

    $form['slick_general']['slideMargin'] = [
      '#type' => 'number',
      '#title' => t('Slide Margin'),
      '#min' => 0,
      '#field_suffix' => 'px',
      '#description' => t('Margin between each slide'),
      '#default_value' => $this->options['slick_general']['slideMargin'],
    ];

    $form['slick_general']['initialSlide'] = [
      '#type' => 'number',
      '#title' => t('Start Slide'),
      '#description' => t('Starting slide index (zero-based)'),
      '#min' => 0,
      '#default_value' => $this->options['slick_general']['initialSlide'],
      '#states' => [
        'visible' => [
          ':input[name*=randomStart]' => ['checked' => FALSE]
        ]
      ]
    ];

    $form['slick_general']['infinite'] = [
      '#type' => 'checkbox',
      '#title' => t('Infinite Loop'),
      '#description' => t('If checked, clicking "Next" while on the last slide will transition to the first slide and vice-versa'),
      '#default_value' => $this->options['slick_general']['infinite'],
    ];

    $form['slick_general']['hideControlOnEnd'] = [
      '#type' => 'checkbox',
      '#title' => t('Hide control on last slide item'),
      '#description' => t('If checked, "Next" control will be hidden on last slide and vice-versa'),
      '#default_value' => $this->options['slick_general']['hideControlOnEnd'],
      '#states' => [
        'visible' => [
          ':input[name*=infinite]' => ['checked' => FALSE]
        ]
      ]
    ];
    
    $form['slick_general']['swipe'] = [
      '#type' => 'checkbox',
      '#title' => t('Touch enable'),
      '#default_value' => $this->options['slick_general']['swipe'],
    ];

    $form['slick_carousel'] = [
      '#type' => 'details',
      '#title' => t('Carousel'),
    ];

    $form['slick_carousel']['lg'] = [
      '#type' => 'number',
      '#title' => t('Items on large screen'),
      '#min' => 1,
      '#default_value' => $this->options['slick_carousel']['lg'],
      '#description' => t('Number of items visible on large devices (desktops)'),
    ];

    $form['slick_carousel']['md'] = [
      '#type' => 'number',
      '#min' => 1,
      '#title' => t('Items on medium screen'),
      '#default_value' => $this->options['slick_carousel']['md'],
      '#description' => t('Number of items visible on medium devices (desktops)'),
    ];

    $form['slick_carousel']['sm'] = [
      '#type' => 'number',
      '#min' => 1,
      '#title' => t('Items on small screen'),
      '#default_value' => $this->options['slick_carousel']['sm'],
      '#description' => t('Number of items visible on small devices (tablet)'),
    ];

    $form['slick_carousel']['xs'] = [
      '#type' => 'number',
      '#min' => 1,
      '#title' => t('Items on extra small screen'),
      '#default_value' => $this->options['slick_carousel']['xs'],
      '#description' => t('Number of items visible on extra small devices (phone)'),
    ];

    $form['slick_controls'] = [
      '#type' => 'details',
      '#title' => t('Controls'),
    ];
    
    $form['slick_controls']['arrows'] = [
      '#type' => 'checkbox',
      '#title' => t('Show Next/Prev controls'),
      '#default_value' => $this->options['slick_controls']['arrows'],
      '#description' => t('If checked, a pager will be added'),
    ];

    $form['slick_controls']['arrows_position'] = [
      '#type' => 'select',
      '#title' => t('Next/Prev controls position'),
      '#default_value' => $this->options['slick_controls']['arrows_position'],
      '#options' => [
        '' => $this->t('Not set'),
        'left-right-center' => $this->t('Left/Right - Center'),
        'center-bottom' => $this->t('Center - Bottom'),
        'left-top' => $this->t('Left - Top'),
        'right-top' => $this->t('Right - Top'),
        'left-bottom' => $this->t('Left - Bottom'),
        'right-bottom' => $this->t('Right - Bottom'),
      ],
      '#states' => [
        'visible' => [
          ':input[name="style_options[slick_controls][arrows]"]' => [
            'checked' => true,
          ]
        ]
      ],
    ];
    
    $form['slick_controls']['dots'] = [
      '#type' => 'checkbox',
      '#title' => t('Show Pager'),
      '#default_value' => $this->options['slick_controls']['dots'],
      '#description' => t('If checked, a pager will be added'),
    ];
    
    $form['slick_centermode'] = [
      '#type' => 'details',
      '#title' => t('Center mode'),
    ];
    
    $form['slick_centermode']['centerMode'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable center mode'),
      '#description' => t('Enables centered view with partial prev/next slides. Use with odd numbered slidesToShow counts'),
      '#default_value' => $this->options['slick_centermode']['centerMode'],
    ];
   
    $form['slick_centermode']['centerPadding'] = [
      '#type' => 'textfield',
      '#title' => t('Center padding'),
      '#description' => t('Side padding when in center mode (px or %)'),
      '#default_value' => $this->options['slick_centermode']['centerPadding'],
    ];
  }

}
