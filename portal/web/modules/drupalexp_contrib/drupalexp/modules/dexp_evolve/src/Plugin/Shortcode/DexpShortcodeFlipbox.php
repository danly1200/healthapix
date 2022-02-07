<?php

namespace Drupal\dexp_evolve\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use \Drupal\Component\Utility\Html;
use Drupal\dexp_builder\Plugin\Shortcode\BuilderElement;

/**
 * Provides a shortcode for flipbox.
 *
 * @Shortcode(
 *   id = "dexp_shortcode_flipbox",
 *   title = @Translation("Flipbox"),
 *   description = @Translation("Flipbox"),
 *   group = @Translation("Content"),
 *   child = {}
 * )
 */
class DexpShortcodeFlipbox extends BuilderElement {

    public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
        parent::process($attrs, $text, $langcode);

        $attrs = $this->getAttributes(array(
            'front_title' => '',
            'type' => '',
            'icon' => '',
            'front_desc' => '',
            'image' => '',
            'back_title' => '',
            'same_title' => TRUE,
            'class' => '',
                ), $attrs
        );
        $classes = $this->addClass([], $attrs['class']);
        $media = '';
        if ($attrs['type'] == 'icon') {
            $media .='<i class="' . $attrs['icon'] . '"></i>';
        } else {
            $fid = str_replace('file:', '', $attrs['image']);
            if ($file = \Drupal\file\Entity\File::load($fid)) {
                $media .= '<img src="' . file_create_url($file->getFileUri()) . '"/>';
            }
        }

        $back_title = $attrs['back_title'];
        if ($attrs['same_title'] == true) {
            $back_title = $attrs['front_title'];
        }

        $return = array(
            '#theme' => 'dexp_evolve_flipbox',
            '#front_title' => $attrs['front_title'],
            '#media' => $media,
            '#front_desc' => $attrs['front_desc'],
            '#content' => $text,
            '#back_title' => $back_title,
            '#class' => $classes,
            '#attached' => ['library' => 'dexp_evolve/dexp-flipbox'],
        );
        return $this->render($return);
    }

    public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
        $form = parent::settingsForm($form, $form_state);
        $form['general_options']['type'] = array(
            '#type' => 'select',
            '#title' => $this->t('Type'),
            '#options' => array('icon' => $this->t('Icon'), 'image' => $this->t('Image')),
            '#default_value' => $this->get('type', 'icon'),
        );
        $form['general_options']['icon'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Icon'),
            '#default_value' => $this->get('icon', ''),
            '#attributes' => ['class' => ['icon-select']],
            '#states' => [
                'visible' => [
                    ':input[name*=type]' => ['value' => 'icon']
                ]
            ],
        );
        $form['general_options']['image'] = array(
            '#type' => 'image_browser',
            '#title' => $this->t('Image'),
            '#default_value' => $this->get('image', 0),
            '#states' => [
                'visible' => [
                    ':input[name*=type]' => ['value' => 'image']
                ]
            ],
        );
        $form['general_options']['front_title'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Front end title'),
            '#default_value' => $this->get('front_title'),
        );
        $form['general_options']['front_desc'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Front end description'),
            '#default_value' => $this->get('front_desc', ''),
        );
        $form['general_options']['same_title'] = array(
            '#type' => 'checkbox',
            '#title' => $this->t('Back end title same as front end?'),
            '#default_value' => $this->get('same_title', true),
        );
        $form['general_options']['back_title'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Back end title'),
            '#default_value' => $this->get('front_title'),
            '#states' => [
                'visible' => [
                    ':input[name*=same_title]' => ['checked' => FALSE]
                ]
            ],
        );
        $form['general_options']['html_content'] = array(
            '#type' => 'text_format',
			'#format' => 'full_html',
            '#title' => $this->t('Back end description'),
            '#default_value' => $this->get('html_content', ''),
        );
        $form['general_options']['class'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Custom class'),
            '#default_value' => $this->get('class'),
        );

        return $form;
    }

    public function processBuilder($attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
        parent::process($attrs, $text, $langcode);

        return $attrs['front_title'];
    }

}
