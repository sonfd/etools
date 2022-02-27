<?php

namespace Drupal\etools\Plugin\Field\FieldFormatter\Traits;

use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Display a subset of referenced entities.
 *
 * Important: This must be used by a formatter that extends an EntityReference
 * formatter.
 */
trait EntityReferenceSubsetTrait {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'allowed_bundles' => '',
      'allowed_count' => 1,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);

    $form['allowed_bundles'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Allowed Bundles'),
      '#description' => $this->t('Enter a comma separated list of bundles allowed to display. Leave empty to allow all bundles.'),
      '#placeholder' => $this->t('E.g. page,article'),
      '#default_value' => $this->getSetting('allowed_bundles'),
    ];

    $form['allowed_count'] = [
      '#type' => 'number',
      '#title' => $this->t('Allowed Count'),
      '#description' => $this->t('Enter the number of referenced items allowed to display. Enter 0 to display all items.'),
      '#default_value' => $this->getSetting('allowed_count'),
      '#size' => 20,
      '#min' => 0,
      '#max' => 100,
      '#step' => 1,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = parent::settingsSummary();

    $summary[] = $this->t('Allowed Bundles: @allowed_bundles', ['@allowed_bundles' => $this->getSetting('allowed_bundles')]);
    $summary[] = $this->t('Allowed Count: @allowed_count', ['@allowed_count' => $this->getSetting('allowed_count')]);

    return $summary;
  }

  /**
   * Restrict entities to view by bundle and count settings.
   *
   * {@inheritdoc}
   */
  protected function getEntitiesToView(EntityReferenceFieldItemListInterface $items, $langcode): array {
    $allowed_bundles = explode(',', $this->getSetting('allowed_bundles'));
    $allowed_count = $this->getSetting('allowed_count');
    $entities = parent::getEntitiesToView($items, $langcode);

    // No restrictions, return all.
    if (empty($allowed_bundles) && empty($allowed_count)) {
      return $entities;
    }

    // If 0 (unlimited), convert to a large number to simplify our loop.
    $allowed_count = $allowed_count ?: 9999;
    $shown_count = 0;
    $allowed_entities = [];
    foreach ($entities as $entity) {
      // Check if bundle is allowed.
      if (empty($allowed_bundles) || (in_array($entity->bundle(), $allowed_bundles))) {
        $allowed_entities[] = $entity;
        $shown_count++;
      }

      // We've reached the max allowed, stop looking for more, exit the loop.
      if ($shown_count >= $allowed_count) {
        break;
      }
    }

    return $allowed_entities;
  }

}
