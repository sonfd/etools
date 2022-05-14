<?php

namespace Drupal\etools;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\FieldableEntityInterface;

/**
 * The Etools Entity (etools.entity) service.
 *
 * A collection of helper methods to make it easier to interact with entities.
 *
 * @package Drupal\etools
 */
class EtoolsEntity implements EtoolsEntityInterface {

  /**
   * {@inheritdoc}
   */
  public function getFieldValue(FieldableEntityInterface $entity, string $field_name, string $property_name = '') {
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

    $values = [];
    for ($i = 0; $i < $entity->get($field_name)->count(); $i++) {
      $val = !empty($property_name)
        ? $entity->get($field_name)->get($i)->{$property_name}
        : $entity->get($field_name)->get($i)->getValue();

      if (!is_null($val) && $val != []) {
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
   * {@inheritdoc}
   */
  public function getFieldDisplay(FieldableEntityInterface $entity, string $field_name, $display_options = 'default'): array {
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
