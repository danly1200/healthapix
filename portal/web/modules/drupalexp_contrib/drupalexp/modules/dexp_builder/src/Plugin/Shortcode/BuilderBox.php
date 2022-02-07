<?php

namespace Drupal\dexp_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;
use Drupal\file\Entity\File;
/**
 * Provides a shortcode for bootstrap row.
 *
 * @Shortcode(
 *   id = "dexp_builder_box",
 *   title = @Translation("Box Icon"),
 *   description = @Translation("Builds a div with col-screen-size class"),
 *   group = @Translation("Content"),
 *   child = {},
 * )
 */
class BuilderBox extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'icon_type' => 'icon',
      'icon' => '',
      'icon_text' => '',
      'icon_image' => '',
      'icon_library' => '',
      'icon_class' => '',
      'icon_alignment' => 'left',
      'icon_bg' => '',
      'title' => '',
      'add_link' => 0,
      'readmore_text' =>'',
      'readmore_link' => '',
	    'readmore_link_target' => '',
	    'readmore_link_class' => '',
	    'readmore_link_title' => '',
      'class' => '',
      'icon_size' => '',
      'layout' => '',
      'render_el' => 'h3',
      'box_style' => '',
        ), $attributes
    );
    $attrObject = $this->createAttribute($attributes);//new Attribute();
    $attrObject->addClass('dexp-builder-box');
    $attrObject->addClass($attrs['layout']);
    $attrObject->addClass($attrs['class']);
    $attrObject->addClass($attrs['box_style']);
    $icon = '';
    $iconAttribute = new Attribute(['class' => $attrs['icon_class']]);
    switch($attrs['icon_type']){
      case 'icon':
        $iconAttribute->addClass($attrs['icon']);
        if($attrs['icon_size']){
          $iconAttribute->setAttribute('style', 'font-size: ' . $attrs['icon_size']);
        }
        $icon = '<i' . $iconAttribute->__toString() . '"></i>';
        break;
      case 'image':
        $fid = str_replace('file:', '', $attrs['icon_image']);
        if($file = File::load($fid)){
          $icon = [
            '#theme' => 'image',
            '#uri' => $file->getFileUri(),
          ];
        }
        break;
      case 'text':
        $icon = ['#markup' => '<i class="icon-text">' . $attrs['icon_text'] . '</i>'];
        break;
    }
    $title = '';
    if($attrs['title']){
      $title = ['#markup' => '<' . $attrs['render_el'] . '>' . $attrs['title'] . '</' . $attrs['render_el'] . '>'];
    }
    $readmore = [];
    if($attrs['add_link'] && $attrs['readmore_text'] && $attrs['readmore_link']){
      if($attrs['readmore_link_title']){
        $readmore = ['#markup' => '<a href="' . $this->getLink($attrs['readmore_link']) . '" title="' . $attrs['readmore_link_title'] . '" data-toggle="tooltip" class="' . $attrs['readmore_link_class'] . '" target="' . $attrs['readmore_link_target'] . '">' . $attrs['readmore_text'] . '</a>'];
      }else{
        $readmore = ['#markup' => '<a href="' . $this->getLink($attrs['readmore_link']) . '" title="" class="' . $attrs['readmore_link_class'] . '" target="' . $attrs['readmore_link_target'] . '">' . $attrs['readmore_text'] . '</a>'];
      }
    }
    $output = [
      '#theme' => 'dexp_builder_box',
      '#icon' => $icon,
      '#icon_alignment' => $attrs['icon_alignment'],
      '#title' => $title,
      '#content' => ['#markup' => $text],
      '#readmore' => $readmore,
      '#layout' => $attrs['layout'],
      '#attributes' => $attrObject,
    ];
    if($attrs['icon_type'] == 'icon' && $attrs['icon_library'] && ($icon_plugin = \Drupal::service('dexp_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
      $output['#attached']['library'] = $icon_plugin->library();
    }
    return $this->render($output);
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
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
    $form['general_options']['box_icon'] = array(
      '#type' => 'details',
      '#title' => $this->t('Icon Settings'),
      '#open' => true,
      'icon_type' => array(
        '#type' => 'select',
        '#title' => $this->t('Icon Type'),
        '#options' => [
          'icon' => $this->t('Font Icon'),
          'image' => $this->t('Image'),
          'text' => $this->t('Text')
        ],
        '#default_value' => $this->get('icon_type','icon'),
        '#description' => $this->t('Using this field to hundreds an icon or image or text will display in the box.')
      ),
      'icon' => array(
        '#type' => 'textfield',
        '#default_value' => $this->get('icon', ''),
        '#attributes' => ['class' => ['icon-select']],
        '#description' => $this->t('Select an icon from library.'),
        '#states' => array(
          'visible' => array(
            ':input[name=icon_type]' => array('value' => 'icon'),
          ),
        ),
      ),
      'icon_library' => array(
        '#type' => 'hidden',
        '#default_value' => $this->get('icon_library', ''),
      ),
      'icon_text' => array(
        '#type' => 'textfield',
        '#size' => 17,
        '#title' => $this->t('Text'),
        '#default_value' => $this->get('icon_text'),
        '#description' => $this->t('Enter text you want to display instead icon in the box. For example: 1, 2, A, B...'),
        '#states' => array(
          'visible' => array(
              ':input[name=icon_type]' => array('value' => 'text'),
            ),
        ),
      ),
      'icon_image' => array(
        '#type' => 'image_browser',
        '#title' => $this->t('Image'),
        '#default_value' => $this->get('icon_image'),
        '#description' => $this->t('Select an image icon from your Drupal library directory or upload a picture.'),
        '#states' => array(
          'visible' => array(
              ':input[name=icon_type]' => array('value' => 'image'),
            ),
        ),
      ),
      'icon_size' => array(
        '#type' => 'textfield',
        '#title' => $this->t('Font Size'),
        '#size' => 17,
        '#default_value' => $this->get('icon_size', ''),
        '#description' => $this->t('Set font size for icon or text, in pixels or percentage, for example: 20px or 80%.'),
        '#states' => array(
          'invisible' => array(
              ':input[name=icon_type]' => array('value' => 'image'),
            ),
        ),
      ),
      'icon_class' => array(
        '#type' => 'textfield',
        '#title' => $this->t('Icon Class'),
        '#default_value' => $this->get('icon_class', ''),
        '#description' => $this->t('Adding the desired CSS class to the icon. For example helpful class for Awesome icon, <a href="http://l-lin.github.io/font-awesome-animation/" target="_blank">click here</a>.'),
      ), 
    );
    $form['general_options']['html_content'] = array(
      '#type' => 'text_format',
      '#format' => 'full_html',
      '#description' => $this->t('Enter the content you want to display with the box. Use the text editor to bring necessary customization to your content. Leave blank if no content is needed.'),
      '#title' => $this->t('Content'),
      '#default_value' => $this->get('html_content', 'Leave blank if no content is needed.'),
    );
    $form['general_options']['add_link'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Add Readmore Button?'),
      '#default_value' => $this->get('add_link', 0),
      '#description' => $this->t('Use this option to add the link to the box. Upon enabling it the some options will open. You have to paste the link and select the option where will it open.')
    );
    $form['general_options']['readmore_text'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Readmore Text'),
      '#default_value' => $this->get('readmore_text', 'Readmore'),
      '#description' => $this->t('Enter text you want to display in the link button. Leave empty if you don\'t want to display it.'),
      '#states' => array(
        'visible' => array(
            ':input[name=add_link]' => array('checked' => true),
          ),
      ),
    );
    $form['general_options']['readmore_link'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Link'),
      '#default_value' => $this->get('readmore_link', '#'),
      '#description' => $this->t('Enter the destination URL.'),
      '#states' => array(
        'visible' => array(
            ':input[name=add_link]' => array('checked' => true),
          ),
      ),
    );
    $form['general_options']['readmore_link_title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Link title'),
      '#default_value' => $this->get('readmore_link_title', ''),
	    '#description' => $this->t('Enter text you want to display as tooltip of the readmore link.'),
      '#states' => array(
        'visible' => array(
            ':input[name=add_link]' => array('checked' => true),
          ),
      ),
    );
	  $form['general_options']['readmore_link_target'] = array(
      '#type' => 'select',
      '#title' => $this->t('Link target'),
      '#options' => array('_self' => $this->t('Same window'), '_blank' => $this->t('New window')),
      '#default_value' => $this->get('readmore_link_target', '_self'),
      '#description' => $this->t('Set target attribute for link.'),
      '#states' => array(
        'visible' => array(
            ':input[name=add_link]' => array('checked' => true),
          ),
      ),
    );
    $form['general_options']['readmore_link_class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Link class'),
      '#default_value' => $this->get('readmore_link_class', ''),
      '#description' => $this->t('Adding a custom class allows you to target the link easily within your custom codes.'),
      '#states' => array(
        'visible' => array(
            ':input[name=add_link]' => array('checked' => true),
          ),
      ),
    );
    $form['general_options']['layout'] = array(
      '#type' => 'select',
      '#title' => $this->t('Layout'),
      '#options' => array(
        'layout_1' => $this->t('Layout 1'),
        'layout_2' => $this->t('Layout 2'),
        'layout_3' => $this->t('Layout 3'),
        'layout_4' => $this->t('Layout 4'),
      ),
      '#default_value' => $this->get('layout','layout_1'),
      '#description' => $this->t('Select Box layout from the list. Preview image for layout will display bellow:')
    );
    $form['general_options']['icon_alignment'] = array(
      '#type' => 'select',
      '#title' => $this->t('Icon alignment'),
      '#options' => array(
        'left' => $this->t('Left'),
        'right' => $this->t('Right'),
      ),
      '#states' => [
        'visible' => [
          ':input[name=layout]' => [
            ['value' => 'layout_3'],
            ['value' => 'layout_4'],
          ],
        ],
      ],
      '#default_value' => $this->get('icon_alignment','left'),
      '#description' => $this->t('Select alignment of icon from the list.')
    );
    $form['general_options']['layout_preview'] = [
      '#type' => 'container',
    ];
    foreach(['layout_1','layout_2'] as $layout){
      $form['general_options']['layout_preview']['layout_preview_' . $layout] = [
        '#theme' => 'image',
        '#uri' => drupal_get_path('module', 'dexp_builder') . '/assets/images/' . $layout . '.jpg',
        '#prefix' => '<div class="js-form-item form-item">',
        '#suffix' => '</div>',
        '#states' => [
          'visible' => [
            ':input[name=layout]' => [
              'value' => $layout,
            ],
          ],
        ],
      ];
    }
    foreach(['layout_3','layout_4'] as $layout){
      $form['general_options']['layout_preview']['layout_preview_' . $layout] = [
        '#theme' => 'image',
        '#uri' => drupal_get_path('module', 'dexp_builder') . '/assets/images/' . $layout . '.jpg',
        '#prefix' => '<div class="js-form-item form-item">',
        '#suffix' => '</div>',
        '#states' => [
          'visible' => [
            ':input[name=layout]' => [
              'value' => $layout,
            ],
            ':input[name=icon_alignment]' => [
              'value' => 'left',
            ],
          ],
        ],
      ];
    }
    $form['general_options']['layout_preview']['layout_preview_layout_3_right'] = [
      '#theme' => 'image',
      '#uri' => drupal_get_path('module', 'dexp_builder') . '/assets/images/layout_5.jpg',
      '#prefix' => '<div class="js-form-item form-item">',
      '#suffix' => '</div>',
      '#states' => [
        'visible' => [
          ':input[name=layout]' => [
            'value' => 'layout_3',
          ],
          ':input[name=icon_alignment]' => [
            'value' => 'right',
          ],
        ],
      ],
    ];
    $form['general_options']['layout_preview']['layout_preview_layout_4_right'] = [
      '#theme' => 'image',
      '#uri' => drupal_get_path('module', 'dexp_builder') . '/assets/images/layout_6.jpg',
      '#prefix' => '<div class="js-form-item form-item">',
      '#suffix' => '</div>',
      '#states' => [
        'visible' => [
          ':input[name=layout]' => [
            'value' => 'layout_4',
          ],
          ':input[name=icon_alignment]' => [
            'value' => 'right',
          ],
        ],
      ],
    ];
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#description' => $this->t('Adding a custom class allows you to target the link easily within your custom codes.</ br>Helpful classes: <span>text-left</span>, text-center, text-right...'),
      '#default_value' => $this->get('class', ''),
    );
    $form['design_options'] += $this->designOptions();
    $form['animate_options'] += $this->animateOptions();
    return $form;
  }

}
