<?php

namespace Drupal\user_location\Plugin\Field\FieldFormatter;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Defines a 'Location' field formatter.
 *
 * @FieldFormatter(
 *   id = "location_formatter",
 *   label = @Translation("Location"),
 *   field_types = {
 *     "location"
 *   }
 * )
 */
class LocationFormatter extends FormatterBase {

  /**
   * @inheritDoc
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    /** @var \Drupal\Core\Field\FieldItemInterface $item */
    foreach ($items as $delta => $item) {
      if (!$item->isEmpty()) {
        $element[$delta] = [
          '#markup' => "{$item->city}, {$item->municipality}",
        ];
      }
    }

    return $element;
  }

}
