<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;

/**
 * Provides a shortcode for countdown.
 *
 * @Shortcode(
 *   id = "dexp_builder_countdown",
 *   title = @Translation("Countdown"),
 *   description = @Translation("Countdown to final date"),
 *   group = @Translation("Content"),
 *   child = {},
 * )
 */
class BuilderCountdown extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
        'title' => '',
        'final_date' => '',
        'render_el' => 'h3',
        'total' => 'days',
        'month' => '',
        'week' => '',
        'day' => '',
        'hour' => '',
        'minute' => '',
        'second' => '',
        'message' => '',
        'class' => '',
            ), $attributes
    );

    $clock = [
        'month' => '<div class="clock-month"><span class="value"></span><span class="label">' . $attrs['month'] . '</span></div>',
        'week' => '<div class="clock-week"><span class="value"></span><span class="label">' . $attrs['week'] . '</span></div>',
        'day' => '<div class="clock-day"><span class="value"></span><span class="label">' . $attrs['day'] . '</span></div>',
        'hour' => '<div class="clock-hour"><span class="value"></span><span class="label">' . $attrs['hour'] . '</span></div>',
        'minute' => '<div class="clock-minute"><span class="value"></span><span class="label">' . $attrs['minute'] . '</span></div>',
        'second' => '<div class="clock-second"><span class="value"></span><span class="label">' . $attrs['second'] . '</span></div>'
    ];
    switch($attrs['total']){
        case 'hours':
            unset($clock['month']);
            unset($clock['week']);
            unset($clock['day']);
            break;
        case 'days':
            unset($clock['month']);
            unset($clock['week']);
            break;
        case 'weeks':
            unset($clock['month']);
            break;
        default: //months
            unset($clock['week']);
            break;
    }

    $title = '';
    if($attrs['title']){
      $title = ['#markup' => '<' . $attrs['render_el'] . ' class="dexp-countdown-title">' . $attrs['title'] . '</' . $attrs['render_el'] . '>'];
    }
    $attribute = $this->createAttribute($attributes);
    $attribute->addClass('dexp-builder-countdown');
    $attribute->addClass($attrs['class']);
    $output = array(
        '#theme' => 'dexp_builder_countdown',
        '#final_date' =>$attrs['final_date'],
        '#total' =>$attrs['total'],
        '#message' =>$attrs['message'],
        '#title' => $title,
        '#clock' => ['#markup' => implode('',$clock)],
        '#attributes' => $attribute,
        '#attached' => ['library' => ['dexp_builder/countdown']],
    );
    return $this->render($output);
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['general_options']['title'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#default_value' => $this->get('title'),
        '#description' => $this->t('Enter your desired text to use as the addon title. Leave blank if no title is needed.')
    );
    $form['general_options']['render_el'] = [
        '#type' => 'select',
        '#title' => $this->t('Render Element'),
        '#options' => [
            'h1' => 'H1',
            'h2' => 'H2',
            'h3' => 'H3',
            'h4' => 'H4',
            'h5' => 'H5',
            'h6' => 'H6',
            'div' => 'DIV',
        ],
        '#default_value' => $this->get('render_el','h2'),
        '#description' => $this->t('Select Title HTML element from the list.')
    ];
    $form['general_options']['final_date'] = array(
        '#type' => 'textfield',
        '#required' => true,
        '#size' => 20,
        '#title' => $this->t('Final Date'),
        '#default_value' => $this->get('final_date',''),
        '#description' => $this->t('The target date that you are seeking to countdown. String formatted as following:
<pre>
    YYYY/MM/DD
    MM/DD/YYYY
    YYYY/MM/DD hh:mm:ss
    MM/DD/YYYY hh:mm:ss
</pre>
        For example: 2018/11/24 or 12/24/2018 18:00:00')
    );
    $form['general_options']['clock'] = array(
        '#type' => 'details',
        '#title' => $this->t('Counter Settings'),
        '#open' => FALSE,
    );
    $form['general_options']['clock']['total'] = [
        '#type' => 'select',
        '#title' => $this->t('Total amount'),
        '#options' => [
            'hours' => 'Hour',
            'days' => 'Day',
            'weeks' => 'Week',
            'months' => 'Month'
        ],
        '#default_value' => $this->get('total','days'),
        '#description' => $this->t('Total amount of counter left until final date.')
    ];
    $form['general_options']['clock']['month'] = [
        '#type' => 'select',
        '#title' => $this->t('Unit of month'),
        '#options' => [
            'months' => 'months',
            'm' => 'm',
            '' => 'none'
        ],
        '#default_value' => $this->get('month','months'),
        '#description' => $this->t('The unit after amount of month.'),
        '#states' => [
            'visible' => [
            ':input[name=total]' => ['value' => 'months'],
            ]
        ]
    ];
    $form['general_options']['clock']['week'] = [
        '#type' => 'select',
        '#title' => $this->t('Unit of week'),
        '#options' => [
            'weeks' => 'weeks',
            'w' => 'w',
            '' => 'none'
        ],
        '#default_value' => $this->get('week','weeks'),
        '#description' => $this->t('The unit after amount of week.'),
        '#states' => [
            'visible' => [
            ':input[name=total]' => ['value' => 'weeks'],
            ]
        ]
    ];
    $form['general_options']['clock']['day'] = [
        '#type' => 'select',
        '#title' => $this->t('Unit of day'),
        '#options' => [
            'days' => 'days',
            'd' => 'd',
            '' => 'none'
        ],
        '#default_value' => $this->get('day','days'),
        '#description' => $this->t('The unit after amount of day.'),
        '#states' => [
            'invisible' => [
            ':input[name=total]' => ['value' => 'hours'],
            ]
        ]
    ];
    $form['general_options']['clock']['hour'] = [
        '#type' => 'select',
        '#title' => $this->t('Unit of hour'),
        '#options' => [
            'hours' => 'hours',
            'hr' => 'hr',
            ':' => ':',
            '' => 'none'
        ],
        '#default_value' => $this->get('hour','hr'),
        '#description' => $this->t('The unit after amount of hour.')
    ];
    $form['general_options']['clock']['minute'] = [
        '#type' => 'select',
        '#title' => $this->t('Unit of minute'),
        '#options' => [
            'minutes' => 'minutes',
            'min' => 'min',
            ':' => ':',
            '' => 'none'
        ],
        '#default_value' => $this->get('minute','min'),
        '#description' => $this->t('The unit after amount of minute.')
    ];
    $form['general_options']['clock']['second'] = [
        '#type' => 'select',
        '#title' => $this->t('Unit of second'),
        '#options' => [
            'seconds' => 'seconds',
            'sec' => 'sec',
            '' => 'none'
        ],
        '#default_value' => $this->get('minute','sec'),
        '#description' => $this->t('The unit after amount of second.')
    ];
    $form['general_options']['clock']['message'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Expired message'),
        '#default_value' => $this->get('message','This offer has expired!'),
        '#description' => $this->t('Enter your desired text to use as the message when the counter has expired.')
    );
    $form['general_options']['class'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Custom class'),
        '#description' => $this->t('Adding a custom class allows you to target the counter easily within your custom codes.'),
        '#default_value' => $this->get('class', ''),
    );
    $form['design_options'] += $this->designOptions();    
    unset($form['animate_options']);
    return $form;
  }

}
