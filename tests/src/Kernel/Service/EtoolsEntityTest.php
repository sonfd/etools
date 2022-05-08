<?php

namespace Drupal\Tests\etools\Kernel\Service\EtoolsEntity;

use Drupal\etools\EtoolsEntity;
use Drupal\KernelTests\KernelTestBase;

/**
 * Test the `etools.entity` service.
 *
 * @covers \Drupal\etools\EtoolsEntity
 *
 * @group etools
 */
class EtoolsEntityTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['etools'];

  /**
   * The `etools.entity` service, the service being tested.
   *
   * @var \Drupal\etools\EtoolsEntity
   */
  protected EtoolsEntity $etools;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->etools = \Drupal::service('etools.entity');
  }

  /**
   * Test whether the service is valid.
   *
   * @return void
   */
  public function testServiceIsValid(): void {
    $this->assertTrue(is_callable([$this->etools, 'getFieldValue']), 'etools.entity method, getFieldValue, not callable.');
    $this->assertTrue(is_callable([$this->etools, 'getFieldDisplay']), 'etools.entity method, getFieldDisplay, not callable.');
  }

  /**
   * Test whether the getFieldValue() method works as expected.
   *
   * @return void
   */
  public function testGetFieldValue(): void {

  }

}
