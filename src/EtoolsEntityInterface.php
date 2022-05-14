<?php

namespace Drupal\etools;

use Drupal\Core\Entity\FieldableEntityInterface;

/**
 * Provides an interface defining an `etools.entity` service.
 */
interface EtoolsEntityInterface {

  /**
   * Get $entity's $field_name field values.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   A fieldable entity.
   * @param string $field_name
   *   The field machine name to get a value for. E.g. body, or field_tags.
   * @param string $property_name
   *   The property that contains the "value" for the field. For fields with
   *   only one relevant property (fields that implement the getMainPropertyName
   *   method) we can determine this automatically. If omitted, and no main
   *   property can be determined, will return the entire values array. E.g.
   *   target_id, value.
   *
   * @return mixed|null
   *   - NULL if the field doesn't exist on the entity.
   *   - If the field allows a single value, the value or NULL.
   *   - If the field allows multiple values, an array of values or empty array.
   */
  public function getFieldValue(FieldableEntityInterface $entity, string $field_name, string $property_name = '');

  /**
   * Render $entity's $field_name field with $display_settings.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   A fieldable entity.
   * @param string $field_name
   *   The field machine name to display. E.g. body, or field_tags.
   * @param string|array $display_options
   *   A view mode machine name or a field display configuration array. Defaults
   *   to the 'default' view mode.
   *
   * @return array
   *   A render array.
   */
  public function getFieldDisplay(FieldableEntityInterface $entity, string $field_name, $display_options = 'default'): array;

}
