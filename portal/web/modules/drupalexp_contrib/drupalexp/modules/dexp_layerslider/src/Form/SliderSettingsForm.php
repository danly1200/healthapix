<?php

/**
 * @file
 * Contains \Drupal\dexp_layerslider\Entity\Form\SliderEditSlidesForm.
 */

namespace Drupal\dexp_layerslider\Form;

use Drupal\dexp_layerslider\Entity\Slider;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;

/**
 * Form controller for Slider edit forms.
 *
 * @ingroup dexp_layerslider
 */
class SliderSettingsForm extends FormBase {
  
  public $settings;
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dexp_layerslider_settings';
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $dexp_slider = 0) {
    $slider = Slider::load($dexp_slider);
    $this->settings = $slider->getSettings();
    $form['general'] = array(
      '#type' => 'details',
      '#title' => $this->t('General Settings'),
      '#open' => TRUE,
    );
    $form['general']['delay'] = array(
      '#title' => $this->t('Default slide duration'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('delay',9000),
      '#description' => $this->t('The time one slide stay on screen in milliseconds. This value is gloal and can be adjust each slide in slides edit page'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['general']['startwidth'] = array(
      '#title' => $this->t('Width'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('startwidth',1140),
      '#description' => $this->t(' This Width of the Grid where the Captions are displayed in Pixel. This Width is the Max width of Slider in Fullwidth Layout and in Responsive Layout.  In Fullscreen Layout the Gird will be centered Vertically in case the Slider is higher then this value.'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['general']['startheight'] = array(
      '#title' => $this->t('Height'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('startheight',500),
      '#description' => $this->t(' This Height of the Grid where the Captions are displayed in Pixel. This Height is the Max height of Slider in Fullwidth Layout and in Responsive Layout.  In Fullscreen Layout the Gird will be centered Vertically in case the Slider is higher then this value.'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['general']['md_custom'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Custom size on notebook screen'),
      '#default_value' => $this->getSetting('md_custom'),
      '#attributes' => ['class'=> ['setting-option']],
    );

    $form['general']['md_startwidth'] = array(
      '#title' => $this->t('Notebook Width'),
      '#type' => 'number',
      '#max' => 992,
      '#default_value' => $this->getSetting('md_startwidth',960),
      '#attributes' => ['class'=> ['setting-option']],
      '#states' => [
        'visible' => [
          ':input[name=md_custom]' => [
            'checked' => true,
          ],
        ]
      ],
    );
    
    $form['general']['md_startheight'] = array(
      '#title' => $this->t('Notebook Height'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('md_startheight',650),
      '#attributes' => ['class'=> ['setting-option']],
      '#states' => [
        'visible' => [
          ':input[name=md_custom]' => [
            'checked' => true,
          ],
        ]
      ],
    );

    $form['general']['sm_custom'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Custom size on tablet screen'),
      '#default_value' => $this->getSetting('sm_custom'),
      '#attributes' => ['class'=> ['setting-option']],
    );

    $form['general']['sm_startwidth'] = array(
      '#title' => $this->t('Tablet Width'),
      '#type' => 'number',
      '#max' => 768,
      '#default_value' => $this->getSetting('sm_startwidth',720),
      '#attributes' => ['class'=> ['setting-option']],
      '#states' => [
        'visible' => [
          ':input[name=sm_custom]' => [
            'checked' => true,
          ],
        ]
      ],
    );
    
    $form['general']['sm_startheight'] = array(
      '#title' => $this->t('Tablet Height'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('sm_startheight',500),
      '#attributes' => ['class'=> ['setting-option']],
      '#states' => [
        'visible' => [
          ':input[name=sm_custom]' => [
            'checked' => true,
          ],
        ]
      ],
    );

    $form['general']['xs_custom'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Custom size on mobile screen'),
      '#default_value' => $this->getSetting('xs_custom'),
      '#attributes' => ['class'=> ['setting-option']],
    );

    $form['general']['xs_startwidth'] = array(
      '#title' => $this->t('Mobile Width'),
      '#type' => 'number',
      '#max' => 576,
      '#default_value' => $this->getSetting('xs_startwidth',540),
      '#attributes' => ['class'=> ['setting-option']],
      '#states' => [
        'visible' => [
          ':input[name=xs_custom]' => [
            'checked' => true,
          ],
        ]
      ],
    );
    
    $form['general']['xs_startheight'] = array(
      '#title' => $this->t('Mobile Height'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('xs_startheight',370),
      '#attributes' => ['class'=> ['setting-option']],
      '#states' => [
        'visible' => [
          ':input[name=xs_custom]' => [
            'checked' => true,
          ],
        ]
      ],
    );
    $form['general']['onHoverStop'] = array(
      '#title' => $this->t('Pause on hover'),
      '#type' => 'select',
      '#options' => ['on' => $this->t('Yes'),'off' => $this->t('No')],
      '#default_value' => $this->getSetting('onHoverStop', 'on'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['general']['loopsingle'] = array(
      '#title' => $this->t('Loop Single Slide'),
      '#type' => 'select',
      '#options' => [0 => $this->t('No'), 1 => $this->t('Yes')],
      '#default_value' => $this->getSetting('loopsingle', 0),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['general']['touchenabled'] = array(
      '#title' => $this->t('Touch enable'),
      '#type' => 'select',
      '#options' => ['on' => $this->t('Yes'),'off' => $this->t('No')],
      '#default_value' => $this->getSetting('touchenabled', 'on'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['layout'] = array(
      '#type' => 'details',
      '#title' => $this->t('Layout Settings'),
    );
    
    $form['layout']['fullWidth'] = array(
      '#title' => $this->t('Full Width'),
      '#type' => 'select',
      '#options' => ['on' => $this->t('Yes'),'off' => $this->t('No')],
      '#default_value' => $this->getSetting('fullWidth','on'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['layout']['fullScreen'] = array(
      '#title' => $this->t('Full Screen'),
      '#type' => 'select',
      '#options' => ['on' => $this->t('Yes'),'off' => $this->t('No')],
      '#default_value' => $this->getSetting('fullScreen','off'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['layout']['spinner'] = array(
      '#title' => t('Spinner'),
      '#type' => 'select',
      '#options' => ['spinner0' => 'spinner0','spinner1' => 'spinner1', 'spinner2' => 'spinner2', 'spinner3' => 'spinner3', 'spinner4' => 'spinner4', 'spinner5' => 'spinner5'],
      '#default_value' => $this->getSetting('spinner','spinner3'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['layout']['timer'] = array(
      '#title' => t('Banner Timer'),
      '#type' => 'select',
      '#options' => ['' => 'None', 'bottom' => 'Bottom', 'top' => 'Top'],
      '#default_value' => $this->getSetting('timer',''),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['layout']['shadow'] = array(
      '#title' => $this->t('Shadow'),
      '#type' => 'textfield',
      '#description' => $this->t('Possible values: 0,1,2,3  (0 == no Shadow, 1,2,3 - Different Shadow Types)'),
      '#default_value' => $this->getSetting('shadow', 0),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['layout']['dottedOverlay'] = array(
      '#title' => $this->t('Dotted Overlay'),
      '#type' => 'textfield',
      '#description' => $this->t('Possible Values: "none", "twoxtwo", "threexthree", "twoxtwowhite", "threexthreewhite" - Creates a Dotted Overlay for the Background images extra. Best use for FullScreen / fullwidth sliders, where images are too pixaleted.'),
      '#default_value' => $this->getSetting('dottedOverlay', 'none'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['nivagation'] = array(
      '#type' => 'details',
      '#title' => $this->t('Navigation Settings'),
    );
    $form['nivagation']['arrows'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable arrows'),
      '#default_value' => $this->getSetting('arrows',1),
      '#attributes' => ['class'=> ['setting-option']],
    );
    $form['nivagation']['arrows_style'] = array(
      '#type' => 'select',
      '#title' => $this->t('Arrows style'),
      '#default_value' => $this->getSetting('arrows_style','hermes'),
      '#options' => [
        '' => $this->t('No style'),
        'gyges' => $this->t('Gyges'),
        'hades' => $this->t('Hades'), 
        'ares' => $this->t('Ares'),
        'hebe' => $this->t('Hebe'),
        'hermes' => $this->t('Hermes'),
        'custom' => $this->t('Custom'),
        'hephaistos' => $this->t('Hephaistos'),
        'persephone' => $this->t('Persephone'),
        'erinyen' => $this->t('Erinyen'),
        'zeus' => $this->t('Zeus'),
        'metis' => $this->t('Metis'),
        'dione' => $this->t('Dione'),
        'uranus' => $this->t('Uranus'),
      ],
      '#attributes' => ['class'=> ['setting-option']],
      '#states' => [
        'visible' => [
          ':input[name=arrows]' => [
            'checked' => true,
          ],
        ],
      ],
    );
    $form['nivagation']['arrows_always_show'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Always show'),
      '#default_value' => $this->getSetting('arrows_always_show',1),
      '#attributes' => ['class'=> ['setting-option']],
      '#states' => [
        'visible' => [
          ':input[name=arrows]' => [
            'checked' => true,
          ],
        ],
      ],
    );
    $form['nivagation']['bullets'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable bullets/pagers'),
      '#default_value' => $this->getSetting('bullets',1),
      '#attributes' => ['class'=> ['setting-option']],
    );
    $form['nivagation']['bullets_style'] = array(
      '#type' => 'select',
      '#title' => $this->t('Bullets style'),
      '#default_value' => $this->getSetting('bullets_style','hermes'),
      '#options' => [
        '' => $this->t('No style'),
        'gyges' => $this->t('Gyges'),
        'hades' => $this->t('Hades'), 
        'ares' => $this->t('Ares'),
        'hebe' => $this->t('Hebe'),
        'hermes' => $this->t('Hermes'),
        'custom' => $this->t('Custom'),
        'hephaistos' => $this->t('Hephaistos'),
        'persephone' => $this->t('Persephone'),
        'erinyen' => $this->t('Erinyen'),
        'zeus' => $this->t('Zeus'),
        'metis' => $this->t('Metis'),
        'dione' => $this->t('Dione'),
        'uranus' => $this->t('Uranus'),
      ],
      '#attributes' => ['class'=> ['setting-option']],
      '#states' => [
        'visible' => [
          ':input[name=bullets]' => [
            'checked' => true,
          ],
        ],
      ],
    );
    $form['nivagation']['bullets_always_show'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Always show'),
      '#default_value' => $this->getSetting('bullets_always_show',1),
      '#attributes' => ['class'=> ['setting-option']],
      '#states' => [
        'visible' => [
          ':input[name=bullets]' => [
            'checked' => true,
          ],
        ],
      ],
    );
    
    $form['id'] = array(
      '#type' => 'hidden',
      '#default_value' => $dexp_slider,
    );
    
    $form['settings'] = array(
      '#type' => 'hidden',
      '#default_value' => json_encode($slider->getSettings()),
    );
    
    $form['actions'] = array(
      '#type' => 'actions',
    );
    
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#default_value' => $this->t('Save'),
      '#button_type' => 'primary',
    );
    $form['#attached']['library'][] = 'dexp_layerslider/settings';
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $slider = Slider::load($form_state->getValue('id'));
    $slider->set('settings', $form_state->getValue('settings'));
    $slider->save();
  }
  
  public function getSetting($setting, $default = null) {
    return isset($this->settings->{$setting})?$this->settings->$setting:$default;
  }
}
