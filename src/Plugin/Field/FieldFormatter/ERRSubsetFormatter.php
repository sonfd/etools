<?php

namespace Drupal\etools\Plugin\Field\FieldFormatter;

use Drupal\entity_reference_revisions\Plugin\Field\FieldFormatter\EntityReferenceRevisionsEntityFormatter;
use Drupal\etools\Plugin\Field\FieldFormatter\Traits\EntityReferenceSubsetTrait;

/**
 * Display a subset of referenced entities.
 *
 * @FieldFormatter(
 *   id = "etools_err_subset",
 *   label = @Translation("Rendered entity (subset)"),
 *   description = @Translation("Display a subset of referenced entities."),
 *   field_types = {
 *     "entity_reference_revisions"
 *   }
 * )
 */
class ERRSubsetFormatter extends EntityReferenceRevisionsEntityFormatter {

  use EntityReferenceSubsetTrait;

}