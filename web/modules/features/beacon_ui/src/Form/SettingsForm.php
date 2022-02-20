<?php

namespace Drupal\beacon_ui\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'beacon_ui.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $config = $this->config('beacon_ui.settings');
    
    $form['delete_events'] = [
      '#type' => 'details',
      '#title' => t('Delete events (cron)'),
      '#open' => TRUE,
    ];
    $form['delete_events']['date_old'] = [
      '#type' => 'select',
      '#title' => t('Date'),
      '#description' => t('Older than.'),
      '#default_value' => $config->get('settingsServer.date_old'),
      //Added only quatity day (example: 1,2,10, 30,100...)
      '#options' => array(7 => 'Older than one week', 14 => 'Older than two weeks', 30 => 'One month', 60 => 'Two month', ),
      '#required' => TRUE,
      '#weight' => '1',
    ];
    $form['delete_events']['quantity'] = [
      '#type' => 'textfield',
      '#title' => t('Quantity'),
      '#description' => t('In one go.'),
      '#default_value' => $config->get('settingsServer.quantity'),
      '#required' => TRUE,
      '#attributes' => ['type' => 'number',],
      '#weight' => '2',
    ];
    $form['actions'] = [
      'submit' => [
        '#type' => 'submit',
        '#value' => t('Submit'),
      ],
      '#weight' => '3',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if(!is_numeric($form_state->getValue('quantity'))){
      \Drupal::messenger()->adderror(t('The field can only contain numbers.'));
  
    }
    else if(empty($form_state->getValue('quantity'))) {
      \Drupal::messenger()->adderror(t('The field cannot be empty.'));
    }
    else {
      $this->configFactory->getEditable('beacon_ui.settings')
        ->set('settingsServer.date_old', $form_state->getValue('date_old'))
        ->set('settingsServer.quantity', $form_state->getValue('quantity'))
        ->save();
      parent::submitForm($form, $form_state);
    }
  }

}
