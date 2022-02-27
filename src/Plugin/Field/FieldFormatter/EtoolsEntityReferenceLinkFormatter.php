<?php

namespace Drupal\etools\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Entity Label linked to a specified URL with Entity ID as query parameter.
 *
 * Useful to link to a page with a filter pre-applied.
 *
 * @FieldFormatter(
 *   id = "etools_entity_reference_link",
 *   label = @Translation("Etools Entity Link"),
 *   description = @Translation("Entity label linked to a URL with the entity id as a query parameter."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class EtoolsEntityReferenceLinkFormatter extends EntityReferenceFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'destination' => '/',
      'query_param_key' => 'tag',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);

    $form['instructions'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('This formatter outputs each field value as a link with label as link text and link URL pattern like DESTINATION?QUERY_PARAMETER_KEY=ENTITY_ID.'),
    ];

    $form['destination'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Destination'),
      '#description' => $this->t('Enter a URL string, e.g. /my-link-destination or https://example.com.'),
      '#default_value' => $this->getSetting('destination'),
      '#required' => TRUE,
    ];

    $form['query_param_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Query Parameter Key'),
      '#default_value' => $this->getSetting('query_param_key'),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];
    $summary[] = $this->t('Link URL pattern: %destination?%query_param_key=ENTITY_ID', [
      '%destination' => $this->getSetting('destination'),
      '%query_param_key' => $this->getSetting('query_param_key'),
    ]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    $destination = Url::fromUserInput($this->getSetting('destination'));
    $query_param_key =  $this->getSetting('query_param_key');

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      $label = $entity->label();
      if (!$entity->isNew()) {
        $elements[$delta] = [
          '#type' => 'link',
          '#title' => $label,
          '#url' => $destination,
          '#options' => [
            'query' => [
              $query_param_key => $entity->id(),
            ],
          ],
        ];
      }
      else {
        $elements[$delta] = ['#plain_text' => $label];
      }

      $elements[$delta]['#cache']['tags'] = $entity->getCacheTags();
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity) {
    return $entity->access('view label', NULL, TRUE);
  }

}
