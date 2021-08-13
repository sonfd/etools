<?php

namespace Drupal\etools\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\etools\EtoolsEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'Text linked' formatter.
 *
 * @FieldFormatter(
 *   id = "etools_text_linked",
 *   label = @Translation("Text linked"),
 *   field_types = {
 *     "string",
 *   },
 * )
 */
class TextLinkedFormatter extends StringFormatter {

  /**
   * The `etools.entity` service.
   *
   * @var \Drupal\etools\EtoolsEntity
   */
  protected EtoolsEntity $etoolsEntity;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, EntityTypeManagerInterface $entity_type_manager, EtoolsEntity $etools_entity) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $entity_type_manager);

    $this->etoolsEntity = $etools_entity;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('etools.entity')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'link_field' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = [];

    $form['link_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link field'),
      '#description' => $this->t("The machine name of a link field on this entity. If the link field is not empty, text will link to the link field's url. Otherwise text displays unlinked."),
      '#default_value' => $this->getSetting('link_field'),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];

    $summary['link_field'] = $this->t('<b>Url provided by:</b> %link_field', [
      '%link_field' => $this->getSetting('link_field'),
    ]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    $entity = $items->getEntity();

    $url = $this->getUrl($entity);
    foreach ($items as $delta => $item) {
      $text = $this->viewValue($item);
      if ($url) {
        $elements[$delta] = [
          '#type' => 'link',
          '#title' => $text,
          '#url' => $url,
        ];
      }
      else {
        $elements[$delta] = $text;
      }
    }

    return $elements;
  }

  /**
   * Get a url object for the text's link.
   *
   * @param $entity
   *   The current entity being viewed.
   *
   * @return \Drupal\Core\Url|null
   *   A url object or NULL.
   */
  protected function getUrl($entity): ?Url {
    $url = NULL;

    $link_field_display = $this->etoolsEntity
      ->getFieldDisplay($entity, $this->getSetting('link_field'), [
        'link',
      ]);

    if (!empty($link_field_display[0]['#url']) && $link_field_display[0]['#url'] instanceof Url) {
      $url = $link_field_display[0]['#url'];
    }

    return $url;
  }

}
