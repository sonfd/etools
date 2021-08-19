<?php

namespace Drupal\etools;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * The Etools Entity (etools.entity) service.
 *
 * A collection of helper methods to make it easier to interact with entities.
 *
 * @package Drupal\etools
 */
class EtoolsEntity {

  /**
   * Get $entity's $field_name field values.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   A content entity.
   * @param string $field_name
   *   E.g. body, or field_tags. The field machine name to get a value for.
   * @param string $property_name
   *   E.g target_id, value. The property that contains the "value" for the
   *   field. For fields with only one relevant property, fields that implement
   *   the getMainPropertyName() method, we can determine this automatically.
   *
   * @return mixed|null
   *   - NULL if the field doesn't exist on the entity.
   *   - If the field allows a single value, the value or NULL.
   *   - If the field allows multiple values, an array of values or empty array.
   *
   * @throws \Exception
   */
  public function getFieldValue(ContentEntityInterface $entity, string $field_name, string $property_name = '') {
    // If the entity doesn't have the field, exit early.
    if (!$entity->hasField($field_name)) {
      return NULL;
    }

    $field_storage_definition = $entity
      ->get($field_name)
      ->getFieldDefinition()
      ->getFieldStorageDefinition();

    $cardinality = $field_storage_definition->getCardinality();
    $property_name = !empty($property_name) ? $property_name : $field_storage_definition->getMainPropertyName();
    if (empty($property_name)) {
      throw new \Exception("No property_name passed to \Drupal::service('etools.entity')->getFieldValue() and a property_name can't be determined automatically.");
    }

    $values = [];
    for ($i = 0; $i < $entity->get($field_name)->count(); $i++) {
      $val = $entity->get($field_name)->get($i)->{$property_name};
      if (!is_null($val)) {
        $values[] = $val;
      }
    }

    if ($cardinality === 1) {
      return empty($values) ? NULL : reset($values);
    }
    else {
      return $values;
    }
  }

  /**
   * Render $entity's $field_name field with $display_settings.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   A content entity.
   * @param string $field_name
   *   E.g. body, or field_tags. The field machine name to display.
   * @param string|array $display_options
   *   A view mode machine name or a field display configuration array. Defaults
   *   to the 'default' view mode.
   *
   * @return array
   *   A render array.
   */
  public function getFieldDisplay(ContentEntityInterface $entity, string $field_name, $display_options = 'default'): array {
    $access = $entity->access('view', NULL, TRUE);

    $field_display = [];
    if ($access->isAllowed() && $entity->hasField($field_name)) {
      $field_display = $entity->get($field_name)->view($display_options);
    }

    CacheableMetadata::createFromRenderArray($field_display)
      ->merge(CacheableMetadata::createFromObject($access))
      ->merge(CacheableMetadata::createFromObject($entity))
      ->applyTo($field_display);

    return $field_display;
  }
}
