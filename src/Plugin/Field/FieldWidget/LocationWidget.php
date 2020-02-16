<?php

namespace Drupal\user_location\Plugin\Field\FieldWidget;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Field\Annotation\FieldWidget;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user_location\Exception\MunicipalityProviderException;
use Drupal\user_location\Form\SettingsForm;
use Drupal\user_location\MunicipalityProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a 'Location' field widget.
 *
 * @FieldWidget(
 *   id = "location_widget",
 *   label = @Translation("Location"),
 *   field_types = {
 *     "location"
 *   }
 * )
 */
class LocationWidget extends WidgetBase {

  /**
   * @var \Drupal\user_location\Exception\MunicipalityProviderException
   */
  protected $municipalityProvider;

  /**
   * LocationWidget constructor.
   *
   * @param $plugin_id
   * @param $plugin_definition
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   * @param array $settings
   * @param array $third_party_settings
   * @param \Drupal\user_location\MunicipalityProviderInterface $municipalityProvider
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    MunicipalityProviderInterface $municipalityProvider
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->municipalityProvider = $municipalityProvider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('user_location.municipality_provider')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    try {
      $municipalities = $this->municipalityProvider->fetch();
    }
    catch (MunicipalityProviderException $e) {
      $this->messenger()->addError($this->t('Failed to fetch municipalities.'));
      $municipalities = [];
    }

    $element = [
      '#type' => 'fieldset',
      '#title' => $this->t('Location'),
    ];

    $element['municipality'] = [
      '#type' => 'select',
      '#title' => $this->t('Municipality'),
      '#empty_option' => $this->t('- Select -'),
      '#options' => array_combine($municipalities, $municipalities),
      '#default_value' => $items[$delta]->municipality ?? NULL,
    ];

    $element['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#default_value' => $items[$delta]->city ?? NULL,
      '#states' => [
        'visible' => [
          'select[name="field_location['. $delta . '][municipality]"]' => ['!value' => ''],
        ],
      ],
    ];

    return $element;
  }

}
