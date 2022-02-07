<?php

namespace Drupal\dexp_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\dexp_builder\Ajax\CloseBuilderModalDialogCommand;
use Drupal\Core\Url;

class IconSelectForm extends FormBase {

  public function buildForm(array $form, FormStateInterface $form_state) {
    $icons = \Drupal::service('dexp_builder.fonticon')->getIcons();
    $form['#attached']['library'] = \Drupal::service('dexp_builder.fonticon')->getLibraries();
    $form['library'] = array(
      '#type' => 'select',
      '#title' => $this->t('Icon library'),
      '#options' => array(),
      '#default_value' => \Drupal::request()->get('icon_library', 'fontawesome'),
    );
    $form['search'] = array(
      '#type' => 'textfield',
      '#attributes' => [
        'placeholder' => $this->t('Search'),
      ],
      '#prefix' => '<div class="col-sm-8>',
      '#suffix' => '</div>',
    );
    foreach($icons as $id => $font_icons){
      $form['library']['#options'][$id] = $font_icons['title'];
      $form[$id] = array(
        '#type' => 'container',
        '#attributes' => ['class' => ['inline-container']],
        '#states' => [
          'visible' => array(
            ':input[name=library]' => [
              'value' => $id,
            ],
          ),
        ],
      );
      foreach($font_icons['icons'] as $icon){
        $form[$id][] = [
        '#type' => 'link',
          '#markup' => '<i title="' . $icon['class'] . '" data-class="' . $icon['class'] . '" class="icon-button ' . $icon['class'] . '"></i>',
        ];
      }
    }
    $form['icon'] = array(
      '#type' => 'hidden',
      '#default_value' => '',
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Submit',
      '#attributes' => ['class' => ['invisible']],
      '#ajax' => array(
        'callback' => '::submitForm',
        'event' => 'click',
      ),
    );
    return $form;
  }

  public function getFormId() {
    return 'dexp_builder_icon_select';
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $icon = $form_state->getValue('icon');
    $library  = $form_state->getValue('library');
    $response = new AjaxResponse();
    $command = new CloseBuilderModalDialogCommand('#dexp-builder-icon-select');
    $response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('.icon-selector .selected-icon', '<i class="' . $icon . '"></i>'));
    $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('input.icon-select', 'val', array($icon)));
    $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('input[name=icon_library]', 'val', array($library)));
    $response->addCommand($command);
    return $response;
    
  }

  public function closeModal() {
    $response = new AjaxResponse();
    $command = new CloseBuilderModalDialogCommand('#dexp-builder-icon-select');
    $response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('.icon-selector .selected-icon', '<i class="fa fa-home"></i>'));
    $response->addCommand($command);
    return $response;
  }

}
