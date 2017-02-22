<?php

/**
 * @file
 *
 * Contains \Drupal\Tests\aquifer\Unit\AquiferManagerServiceTest
 */

namespace Drupal\Tests\aquifer\Unit;

use Drupal\aquifer\AquiferManagerService;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Tests\UnitTestCase;

/**
 * Tests all the methods in the Aquifer Manager Service
 *
 * @coversDefaultClass \Drupal\aquifer\AquiferManagerService
 * @group aquifer
 */
class AquiferManagerServiceTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }

  /**
   * Provides good data for updating / adding aquifers
   *
   * @return array
   */
  public function provideGoodAquiferData () {
    return [
      [array(
        'name' => 'Northern Aquifer',
        'coordinates' => 'Way up north',
        'status' => 'adequate',
        'volume' => '1'
      )],
      [array(
        'name' => 'Eastern Aquifer',
        'coordinates' => 'Way to the east',
        'status' => 'low',
        'volume' => '2'
      )],
      [array(
        'name' => 'Southern Aquifer',
        'coordinates' => 'Way to the south',
        'status' => 'full',
        'volume' => '3'
      )],
      [array(
        'name' => 'Western Aquifer',
        'coordinates' => 'Way to the south',
        'status' => 'critical',
        'volume' => '4'
      )],
    ];
  }

  /**
   * Test updating a non-existing aquifer
   *
   * @covers ::createAquifer
   * @dataProvider provideGoodAquiferData
   */
  public function testCreateGoodAquifer($aquiferData) {
    $query_object = $this->getMockBuilder('Drupal\Core\Entity\Query\QueryInterface')
      ->getMock();
    $node = $this->getMockBuilder('\Drupal\node\Entity\Node')
      ->disableOriginalConstructor()
      ->getMock();
    $nodeStorage = $this->getMockBuilder('\Drupal\Core\Entity\EntityStorageInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $nodeStorage->expects($this->any())
      ->method('getQuery')
      ->willReturn($query_object);
    $query_object->expects($this->any())
      ->method('condition')
      ->willReturn($query_object);
    $query_object->expects($this->any())
      ->method('count')
      ->willReturn($query_object);
    $query_object->expects($this->any())
      ->method('execute')
      ->willReturn(0);
    $nodeStorage->expects($this->any())
      ->method('create')
      ->willReturn($node);
    $entityManager = $this->getMockBuilder('\Drupal\Core\Entity\EntityManagerInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $entityManager->expects($this->any())
      ->method('getStorage')
      ->with('node')
      ->willReturn($nodeStorage);
    $aquiferManagerService = new AquiferManagerService($entityManager);
    // It doesn't really matter what the expected result is because we need
    // to mock the nodeStorage and its result as well. Hence, they will always
    // match because we are mocking both of them.
    $expectedResult = '';
    $this->assertEquals($expectedResult, $aquiferManagerService->updateAquifer($aquiferData));
  }

  /**
   * Provides missing for adding aquifers
   *
   * @return array
   */
  public function provideMissingAquiferData () {
    return [
      [array(
        'name' => 'Northern Aquifer',
        'status' => 'adequate',
        'volume' => '1'
      )],
      [array(
        'name' => 'Eastern Aquifer',
        'coordinates' => 'Way to the east',
        'volume' => '2'
      )],
      [array(
        'name' => 'Southern Aquifer',
        'coordinates' => 'Way to the south',
        'status' => 'full',
      )],
      [array(
        'name' => 'Western Aquifer',
        'status' => 'critical',
        'volume' => '4'
      )],
    ];
  }

  /**
   * Test updating a non-existing aquifer
   *
   * @covers ::createAquifer
   * @dataProvider provideMissingAquiferData
   * @expectedException \InvalidArgumentException
   */
  public function testCreateMissingAquifer($aquiferData) {
    $query_object = $this->getMockBuilder('Drupal\Core\Entity\Query\QueryInterface')
      ->getMock();
    $node = $this->getMockBuilder('\Drupal\node\Entity\Node')
      ->disableOriginalConstructor()
      ->getMock();
    $nodeStorage = $this->getMockBuilder('\Drupal\Core\Entity\EntityStorageInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $nodeStorage->expects($this->any())
      ->method('getQuery')
      ->willReturn($query_object);
    $query_object->expects($this->any())
      ->method('condition')
      ->willReturn($query_object);
    $query_object->expects($this->any())
      ->method('count')
      ->willReturn($query_object);
    $query_object->expects($this->any())
      ->method('execute')
      ->willReturn(0);
    $nodeStorage->expects($this->any())
      ->method('create')
      ->willReturn($node);
    $entityManager = $this->getMockBuilder('\Drupal\Core\Entity\EntityManagerInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $entityManager->expects($this->any())
      ->method('getStorage')
      ->with('node')
      ->willReturn($nodeStorage);
    $aquiferManagerService = new AquiferManagerService($entityManager);
    // It doesn't really matter what the expected result is because we need
    // to mock the nodeStorage and its result as well. Hence, they will always
    // match because we are mocking both of them.
    $expectedResult = '';
    $this->assertEquals($expectedResult, $aquiferManagerService->updateAquifer($aquiferData));
  }

  /**
   * Provides bad data for adding aquifers
   *
   * @return array
   */
  public function provideBadAquiferData () {
    return [
      [array(
        'name' => 'Northern Aquifer',
        'coordinates' => 'Way up north',
        'status' => 'adequate',
        'volume' => '1',
        'bogusField' => 'throw an exception'
      )],
      [array(
        'name' => 'Eastern Aquifer',
        'coordinates' => 'Way to the east',
        'status' => 'low',
        'bogusField' => 'throw an exception',
        'volume' => '2'
      )],
      [array(
        'name' => 'Southern Aquifer',
        'coordinates' => 'Way to the south',
        'bogusField' => 'throw an exception',
        'status' => 'full',
        'volume' => '3'
      )],
      [array(
        'name' => 'Western Aquifer',
        'bogusField' => 'throw an exception',
        'coordinates' => 'Way to the south',
        'status' => 'critical',
        'volume' => '4'
      )],
    ];
  }

  /**
   * Test updating a non-existing aquifer
   *
   * @covers ::createAquifer
   * @dataProvider provideBadAquiferData
   * @expectedException \UnexpectedValueException
   */
  public function testCreateBadAquifer($aquiferData) {
    $query_object = $this->getMockBuilder('Drupal\Core\Entity\Query\QueryInterface')
      ->getMock();
    $node = $this->getMockBuilder('\Drupal\node\Entity\Node')
      ->disableOriginalConstructor()
      ->getMock();
    $nodeStorage = $this->getMockBuilder('\Drupal\Core\Entity\EntityStorageInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $nodeStorage->expects($this->any())
      ->method('getQuery')
      ->willReturn($query_object);
    $query_object->expects($this->any())
      ->method('condition')
      ->willReturn($query_object);
    $query_object->expects($this->any())
      ->method('count')
      ->willReturn($query_object);
    $query_object->expects($this->any())
      ->method('execute')
      ->willReturn(0);
    $nodeStorage->expects($this->any())
      ->method('create')
      ->willReturn($node);
    $entityManager = $this->getMockBuilder('\Drupal\Core\Entity\EntityManagerInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $entityManager->expects($this->any())
      ->method('getStorage')
      ->with('node')
      ->willReturn($nodeStorage);
    $aquiferManagerService = new AquiferManagerService($entityManager);
    // It doesn't really matter what the expected result is because we need
    // to mock the nodeStorage and its result as well. Hence, they will always
    // match because we are mocking both of them.
    $expectedResult = '';
    $this->assertEquals($expectedResult, $aquiferManagerService->updateAquifer($aquiferData));
  }

  /**
   * Test updating an existing aquifer
   *
   * @covers ::updateAquifer
   * @dataProvider provideGoodAquiferData
   */
  public function testUpdateGoodAquifer($aquiferData) {
    $query_object = $this->getMockBuilder('Drupal\Core\Entity\Query\QueryInterface')
      ->getMock();
    $query_object2 = $this->getMockBuilder('Drupal\Core\Entity\Query\QueryInterface')
      ->getMock();
    $node = $this->getMockBuilder('\Drupal\node\Entity\Node')
      ->disableOriginalConstructor()
      ->getMock();
    $nodeStorage = $this->getMockBuilder('\Drupal\Core\Entity\EntityStorageInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $nodeStorage->expects($this->any())
      ->method('getQuery')
      ->willReturn($query_object);
    $query_object->expects($this->any())
      ->method('condition')
      ->willReturn($query_object);
    $query_object->expects($this->any())
      ->method('count')
      ->willReturn($query_object);
    $query_object->expects($this->any())
      ->method('execute')
      ->willReturn(1);
    $query_object->expects($this->any())
      ->method('range')
      ->willReturn($query_object2);
    $query_object2->expects($this->any())
      ->method('execute')
      ->willReturn(array(1, 2, 3));
    $nodeStorage->expects($this->any())
      ->method('create')
      ->willReturn($node);
    $nodeStorage->expects($this->any())
      ->method('load')
      ->willReturn($node);
    $entityManager = $this->getMockBuilder('\Drupal\Core\Entity\EntityManagerInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $entityManager->expects($this->any())
      ->method('getStorage')
      ->with('node')
      ->willReturn($nodeStorage);
    $aquiferManagerService = new AquiferManagerService($entityManager);
    // It doesn't really matter what the expected result is because we need
    // to mock the nodeStorage and its result as well. Hence, they will always
    // match because we are mocking both of them.
    $expectedResult = (object) array('status' => 'updated', 'object' => $node);
    $this->assertEquals($expectedResult, $aquiferManagerService->updateAquifer($aquiferData));
  }

  /**
   * Test updating an existing aquifer
   *
   * @covers ::updateAquifer
   * @dataProvider provideBadAquiferData
   * @expectedException \UnexpectedValueException
   */
  public function testUpdateBadAquifer($aquiferData) {
    $query_object = $this->getMockBuilder('Drupal\Core\Entity\Query\QueryInterface')
      ->getMock();
    $query_object2 = $this->getMockBuilder('Drupal\Core\Entity\Query\QueryInterface')
      ->getMock();
    $node = $this->getMockBuilder('\Drupal\node\Entity\Node')
      ->disableOriginalConstructor()
      ->getMock();
    $nodeStorage = $this->getMockBuilder('\Drupal\Core\Entity\EntityStorageInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $nodeStorage->expects($this->any())
      ->method('getQuery')
      ->willReturn($query_object);
    $query_object->expects($this->any())
      ->method('condition')
      ->willReturn($query_object);
    $query_object->expects($this->any())
      ->method('count')
      ->willReturn($query_object);
    $query_object->expects($this->any())
      ->method('execute')
      ->willReturn(1);
    $query_object->expects($this->any())
      ->method('range')
      ->willReturn($query_object2);
    $query_object2->expects($this->any())
      ->method('execute')
      ->willReturn(array(1, 2, 3));
    $nodeStorage->expects($this->any())
      ->method('create')
      ->willReturn($node);
    $nodeStorage->expects($this->any())
      ->method('load')
      ->willReturn($node);
    $entityManager = $this->getMockBuilder('\Drupal\Core\Entity\EntityManagerInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $entityManager->expects($this->any())
      ->method('getStorage')
      ->with('node')
      ->willReturn($nodeStorage);
    $aquiferManagerService = new AquiferManagerService($entityManager);
    // It doesn't really matter what the expected result is because we need
    // to mock the nodeStorage and its result as well. Hence, they will always
    // match because we are mocking both of them.
    $expectedResult = (object) array('status' => 'updated', 'object' => $node);
    $this->assertEquals($expectedResult, $aquiferManagerService->updateAquifer($aquiferData));
  }

}
