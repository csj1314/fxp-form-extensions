<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Tests\Doctrine\Form\Loader;

use Fxp\Component\FormExtensions\Doctrine\Form\Loader\DynamicDoctrineChoiceLoader;
use Fxp\Component\FormExtensions\Tests\Doctrine\Form\Fixtures\MockEntity;
use Fxp\Component\FormExtensions\Tests\Form\ChoiceList\Loader\AbstractChoiceLoaderTest;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Bridge\Doctrine\Form\ChoiceList\IdReader;
use Symfony\Component\Form\Exception\RuntimeException;

/**
 * Tests case for dynamic doctrine choice loader.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class DynamicDoctrineChoiceLoaderTest extends AbstractChoiceLoaderTest
{
    /**
     * @var EntityLoaderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectLoader;

    /**
     * @var IdReader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $idReader;

    /**
     * @var MockEntity[]
     */
    protected $objects;

    public function setUp()
    {
        $this->objects = [
            new MockEntity('foo', 'Bar'),
            new MockEntity('bar', 'Foo'),
            new MockEntity('baz', 'Baz'),
        ];

        $this->objectLoader = $this->getMockBuilder('Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface')->getMock();
        $this->idReader = $this->getMockBuilder('Symfony\Bridge\Doctrine\Form\ChoiceList\IdReader')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function tearDown()
    {
        $this->objectLoader = null;
        $this->idReader = null;
    }

    public function getIsGroup()
    {
        return [
            [false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function createChoiceLoader($group = false)
    {
        $objects = $this->objects;

        $this->objectLoader->expects($this->any())
            ->method('getEntities')
            ->will($this->returnCallback(function () use ($objects) {
                $values = [];

                foreach ($objects as $object) {
                    $values[] = $object;
                }

                return $values;
            }));

        $this->objectLoader->expects($this->any())
            ->method('getEntitiesByIds')
            ->will($this->returnCallback(function ($idField, $values) use ($objects) {
                $entities = [];

                foreach ($values as $id) {
                    foreach ($objects as $object) {
                        if ($id === $object->getId()) {
                            $entities[] = $object;
                            break;
                        }
                    }
                }

                return $entities;
            }));

        $this->idReader->expects($this->any())
            ->method('getIdField')
            ->will($this->returnValue('id'));

        $this->idReader->expects($this->any())
            ->method('isSingleId')
            ->will($this->returnValue(true));

        $this->idReader->expects($this->any())
            ->method('getIdValue')
            ->will($this->returnCallback(function ($value) use ($objects) {
                foreach ($objects as $i => $object) {
                    if ($object === $value) {
                        return $object->getId();
                    }
                }

                throw new RuntimeException('MOCK_EXCEPTION');
            }));

        return new DynamicDoctrineChoiceLoader($this->objectLoader, [$this->idReader, 'getIdValue'], $this->idReader->getIdField(), 'label');
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidStructuredValues($group)
    {
        return [
            '0' => 'foo',
            '1' => 'bar',
            '2' => 'baz',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidStructuredValuesWithNewTags($group)
    {
        return array_merge($this->getValidStructuredValues($group), [
            '3' => 'Test',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDataChoicesForValues()
    {
        return [
            'foo',
            'Test',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidChoicesForValues($group)
    {
        return [
            0 => $this->objects[0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidChoicesForValuesWithNewTags($group)
    {
        return [
            0 => $this->objects[0],
            1 => 'Test',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDataForValuesForChoices($group)
    {
        return [
            $this->objects[0],
            'Test',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidValuesForChoices($group)
    {
        return [
            'foo',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDataForValuesForChoicesWithNewTags($group)
    {
        return $this->getDataForValuesForChoices($group);
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidValuesForChoicesWithNewTags($group)
    {
        return [
            'foo',
            'Test',
        ];
    }

    /**
     * @dataProvider getIsGroup
     *
     * @param bool $group
     */
    public function testDefault($group)
    {
        $loader = $this->createChoiceLoader($group);

        $this->assertNotNull($loader->getLabel());
        $this->assertEquals(3, $loader->getSize());
        $this->assertFalse($loader->isAllowAdd());

        $loader->setAllowAdd(true);
        $this->assertTrue($loader->isAllowAdd());
    }

    /**
     * @dataProvider getIsGroup
     *
     * @param bool $group
     *
     * @expectedException \Symfony\Component\Form\Exception\RuntimeException
     * @expectedExceptionMessage MOCK_EXCEPTION
     */
    public function testNotAddNewTags($group)
    {
        $loader = $this->createChoiceLoader($group);
        $choices = [
            $this->objects[0],
            new MockEntity(null, 'Test'),
        ];

        $loader->loadValuesForChoices($choices);
    }
}
