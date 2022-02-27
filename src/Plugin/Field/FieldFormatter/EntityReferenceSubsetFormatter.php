<?php

namespace Drupal\etools\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;
use Drupal\etools\Plugin\Field\FieldFormatter\Traits\EntityReferenceSubsetTrait;

/**
 * Display a subset of referenced entities.
 *
 * @FieldFormatter(
 *   id = "etools_er_subset",
 *   label = @Translation("Rendered entity (subset)"),
 *   description = @Translation("Display a subset of referenced entities."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class EntityReferenceSubsetFormatter extends EntityReferenceEntityFormatter {

  use EntityReferenceSubsetTrait;

}
