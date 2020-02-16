<?php

namespace Drupal\user_location\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines settings form for User Location module.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * Configuration name.
   */
  const CONFIG_NAME = 'user_location.settings';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [static::CONFIG_NAME];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_location_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::CONFIG_NAME);
    $form['#tree'] = TRUE;

    $form['ws'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Web API settings'),
    ];

    $form['ws']['service_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Service URL'),
      '#default_value' => $config->get('ws.service_url'),
    ];

    $form['ws']['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API key'),
      '#default_value' => $config->get('ws.api_key'),
    ];

    $form['ws']['limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Result limit'),
      '#min' => 0,
      '#max' => 20,
      '#default_value' => $config->get('ws.limit'),
    ];

    $form['cache']['lifetime'] = [
      '#type' => 'number',
      '#title' => $this->t('Webservice result cache lifetime (in hours).'),
      '#min' => 0,
      '#default_value' => $config->get('cache.lifetime'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config(static::CONFIG_NAME)
      ->set('ws', $form_state->getValue('ws'))
      ->set('cache', $form_state->getValue('cache'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
