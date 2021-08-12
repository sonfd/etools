<?php

namespace Drupal\etools;

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
      new TwigFunction('etools_field_value', [$this->etoolsEntity, 'getFieldValue']),
      new TwigFunction('etools_field_display', [$this->etoolsEntity, 'getFieldDisplay'])
    ];
  }

}
