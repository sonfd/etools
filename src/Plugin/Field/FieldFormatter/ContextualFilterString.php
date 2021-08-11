<?php

namespace Drupal\etools\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'Contextual Filter String' formatter.
 *
 * @FieldFormatter(
 *   id = "etools_contextual_filter_string",
 *   label = @Translation("Contextual Filter String"),
 *   description = @Translation("Display the IDs of referenced entities as a contextual filter string. I.e. A single string of IDs with AND ',' or OR '+' separators."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class ContextualFilterString extends EntityReferenceFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'separator' => '+',
      'default_value' => 'all',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form['separator'] = [
      '#type' => 'select',
      '#title' => $this->t('Separator'),
      '#description' => $this->t('Used if the field has multiple values.'),
      '#options' => $this->getSeparatorOptions(),
      '#default_value' => $this->getSetting('separator'),
    ];

    $form['default_value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default value'),
      '#description' => $this->t('This value is used if the field is empty.'),
      '#default_value' => $this->getSetting('default_value'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];

    $separator_options = $this->getSeparatorOptions();
    $summary['separator'] = $this->t('<strong>Separator:</strong> %separator', [
      '%separator' => $separator_options[$this->getSetting('separator')],
    ]);

    $summary['default_value'] = $this->t('<strong>Default value:</strong> %default_value', [
      '%default_value' => $this->getSetting('default_value'),
    ]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $separator = $this->getSetting('separator');
    $default_value = $this->getSetting('default_value');

    $ids = [];
    $cache_tags = [];
    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      $ids[] = $entity->id();
      $cache_tags = array_merge($cache_tags, $entity->getCacheTags());
    }

    return [
      '#plain_text' => empty($ids) ? $default_value : implode($separator, $ids),
      '#cache' => [
        'tags' => $cache_tags,
      ]
    ];
  }

  /**
   * Get an options array of separator options.
   *
   * @return array
   *   An options array of separator options.
   */
  protected function getSeparatorOptions(): array {
    return [
      '+' => $this->t('OR'),
      ',' => $this->t('AND'),
    ];
  }

}
