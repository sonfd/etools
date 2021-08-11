<?php

namespace Drupal\etools;

use Drupal\Core\Entity\ContentEntityInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * The ETools Twig Extension (etools.twig_extension) service.
 *
 * Extend twig with additional functions and filters.
 *
 * @package Drupal\etools
 */
class EtoolsTwigExtension extends AbstractExtension {

  /**
   * The `etools.entity` service.
   *
   * @var \Drupal\etools\EtoolsEntity
   */
  protected EtoolsEntity $etoolsEntity;

  /**
   * Construct the Etools Twig Extension service.
   *
   * @param \Drupal\etools\EtoolsEntity $etools_entity
   *   The `etools.entity` service.
   */
  public function __construct(EtoolsEntity $etools_entity) {
    $this->etoolsEntity = $etools_entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions(): array {
    return [
      new TwigFunction('etools_field_value', [$this, 'getFieldValue']),
    ];
  }

  /**
   * Wrapper for the `etools.entity` service's getFieldValue() method.
   *
   * @see \Drupal\etools\EtoolsEntity::getFieldValue()
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
   *   - If the the field allows a single value, the value or NULL.
   *   - If the field allows multiple values, an array of values or empty array.
   *
   * @throws \Exception
   */
  public function getFieldValue(ContentEntityInterface $entity, string $field_name, string $property_name = '') {
    return $this->etoolsEntity
      ->getFieldValue($entity, $field_name, $property_name);
  }

}
