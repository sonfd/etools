<?php

namespace Drupal\Tests\etools\Kernel\Service;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\etools\EtoolsEntityInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
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
  protected static $modules = [
    'etools',
    'user',
    'field',
    'entity_test',
  ];

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('entity_test');
    $this->installConfig(['field']);

    $this->setUpSingleValueField()
      ->setUpMultiValueField()
      ->setUpMultiPropertyField()
      ->setUpNoMainPropertyField();
  }

  /**
   * Test whether the service exists and is valid.
   */
  public function testServiceIsValid(): void {
    $this->assertInstanceOf(EtoolsEntityInterface::class, \Drupal::service('etools.entity'));
  }

  /**
   * Test whether the getFieldValue() method works as expected.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function testGetFieldValue(): void {
    $etools = \Drupal::service('etools.entity');

    // Create a test entity.
    $entity = EntityTest::create();
    $entity->set('name', $this->randomMachineName());
    $entity->save();

    // 1. Test getFieldValue returns NULL when the field doesn't exist.
    $this->assertNull($etools->getFieldValue($entity, 'field_does_not_exist'));

    // 2. Test getFieldValue returns correct value for a single-value field.
    $entity->set('field_single_value', "hello world");
    $this->assertEquals(
      $entity->field_single_value->value,
      $etools->getFieldValue($entity, 'field_single_value')
    );

    // 3. Test getFieldValue returns NULL when requested property doesn't exist.
    $this->assertNull($etools
      ->getFieldValue($entity, 'field_single_value', 'non_existent_property'));

    // 4. Test getFieldValue returns an array for a multi-value field.
    $entity->set('field_multi_value', ["hello", "world"]);
    $result = $etools->getFieldValue($entity, 'field_multi_value');
    $this->assertIsArray($result);
    // 4a. And test the array is what we expect it to be.
    $this->assertCount(2, $result);
    $this->assertEquals($entity->field_multi_value[0]->value, $result[0]);
    $this->assertEquals($entity->field_multi_value[1]->value, $result[1]);

    // 5. Test getFieldValue correctly returns a non-main property, 'entity'.
    // Note: we could maybe remove this entity_reference field test and just
    // test with the map field, but this is a very common case, and I'd like to
    // test getting an alternate property works even when there is a main
    // property (in this case, the main property would be target_id).
    $referenced = EntityTest::create();
    $referenced->set('name', $this->randomMachineName());
    $referenced->save();
    $entity->set('field_multi_property', $referenced);
    $entity_property_value = $etools
      ->getFieldValue($entity, 'field_multi_property', 'entity');
    $this->assertInstanceOf(get_class($referenced), $entity_property_value);
    $this->assertEquals(
      $referenced->id(),
      $entity_property_value->id()
    );

    // 6. Test getFieldValue returns an array with all properties for a field
    // with no main property.
    $misc_array_values = [
      'foo' => 'hello',
      'bar' => 'world',
    ];
    $entity->set('field_no_main_property', $misc_array_values);
    $result = $etools->getFieldValue($entity, 'field_no_main_property');
    $this->assertIsArray($result);
    $this->assertCount(count($misc_array_values), $result);
    $this->assertEquals($entity->field_no_main_property->foo, $result['foo']);
    $this->assertEquals($entity->field_no_main_property->bar, $result['bar']);
  }

  /**
   * Define a single-value test field and add to the test entity bundle.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function setUpSingleValueField(): self {
    FieldStorageConfig::create([
      'field_name' => 'field_single_value',
      'entity_type' => 'entity_test',
      'type' => 'string',
      'cardinality' => 1,
    ])->save();
    FieldConfig::create([
      'entity_type' => 'entity_test',
      'field_name' => 'field_single_value',
      'bundle' => 'entity_test',
    ])->save();

    return $this;
  }

  /**
   * Define a multi-value test field and add to the test entity bundle.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function setUpMultiValueField(): self {
    FieldStorageConfig::create([
      'field_name' => 'field_multi_value',
      'entity_type' => 'entity_test',
      'type' => 'string',
      'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
    ])->save();
    FieldConfig::create([
      'entity_type' => 'entity_test',
      'field_name' => 'field_multi_value',
      'bundle' => 'entity_test',
    ])->save();

    return $this;
  }

  /**
   * Define a multi-property test field and add to the test entity bundle.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function setUpMultiPropertyField(): self {
    FieldStorageConfig::create([
      'field_name' => 'field_multi_property',
      'entity_type' => 'entity_test',
      'type' => 'entity_reference',
      'target_type' => 'entity_test',
      'cardinality' => 1,
    ])->save();
    FieldConfig::create([
      'entity_type' => 'entity_test',
      'field_name' => 'field_multi_property',
      'bundle' => 'entity_test',
    ])->save();

    return $this;
  }

  /**
   * Define a no main property test field and add to the test entity bundle.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function setUpNoMainPropertyField(): self {
    FieldStorageConfig::create([
      'field_name' => 'field_no_main_property',
      'entity_type' => 'entity_test',
      'type' => 'map',
      'cardinality' => 1,
    ])->save();
    FieldConfig::create([
      'entity_type' => 'entity_test',
      'field_name' => 'field_no_main_property',
      'bundle' => 'entity_test',
    ])->save();

    return $this;
  }

}
