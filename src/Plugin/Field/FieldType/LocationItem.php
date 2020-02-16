<?php

namespace Drupal\user_location\Plugin\Field\FieldType;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines a 'Location' field type.
 *
 * @FieldType(
 *   id = "location",
 *   label = @Translation("Location"),
 *   description = @Translation("Field containing user location data."),
 *   default_formatter = "location_formatter",
 *   default_widget = "location_widget"
 * )
 */
class LocationItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['municipality'] = DataDefinition::create('string')
      ->setLabel(t('Municipality'));

    $properties['city'] = DataDefinition::create('string')
      ->setLabel(t('City'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'municipality' => [
          'type' => 'varchar',
          'length' => 64,
        ],
        'city' => [
          'type' => 'varchar',
          'length' => 64,
        ],
      ],
    ];
  }

}
