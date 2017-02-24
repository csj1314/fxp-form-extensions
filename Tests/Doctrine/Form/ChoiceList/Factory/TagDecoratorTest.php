<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Component\FormExtensions\Tests\Doctrine\Form\ChoiceList\Factory;

use Sonatra\Component\FormExtensions\Doctrine\Form\ChoiceList\Factory\TagDecorator;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class TagDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ChoiceListFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $factory;

    /**
     * @var TagDecorator
     */
    protected $decoratorFactory;

    protected function setUp()
    {
        $this->factory = $this->getMockBuilder(ChoiceListFactoryInterface::class)->getMock();
        $this->decoratorFactory = new TagDecorator($this->factory);
    }

    public function getValues()
    {
        $object = new \stdClass();

        return array(
            array(array('foo'), array('foo'), null),
            array(array(23), array(24), function ($v = null) {
                return is_int($v) ? $v + 1 : $v;
            }),
            array(array(23), array(23), null),
            array(array($object), array($object), null),
        );
    }

    /**
     * @dataProvider getValues
     *
     * @param array $choices
     * @param array $expected
     * @param mixed $value
     */
    public function testCreateListFromChoices(array $choices, array $expected, $value)
    {
        $self = $this;
        $this->factory->expects($this->once())
            ->method('createListFromChoices')
            ->willReturnCallback(function ($choices, $value) use ($self) {
                $self->assertTrue(is_array($choices));
                $self->assertGreaterThanOrEqual(1, count($choices));
                $self->assertInstanceOf(\Closure::class, $value);

                $result = $choices;

                foreach ($result as &$choice) {
                    $choice = call_user_func($value, $choice);
                }

                return $result;
            });

        $res = $this->decoratorFactory->createListFromChoices($choices, $value);

        $this->assertSame($expected, $res);
    }

    /**
     * @dataProvider getValues
     *
     * @param array $choices
     * @param array $expected
     * @param mixed $value
     */
    public function testCreateListFromLoader(array $choices, array $expected, $value)
    {
        /* @var ChoiceLoaderInterface|\PHPUnit_Framework_MockObject_MockObject $loader */
        $loader = $this->getMockBuilder(ChoiceLoaderInterface::class)->getMock();
        $self = $this;

        $loader->expects($this->once())
            ->method('loadValuesForChoices')
            ->willReturn($choices);

        $this->factory->expects($this->once())
            ->method('createListFromLoader')
            ->willReturnCallback(function ($loader, $value) use ($self, $loader) {
                $self->assertSame($loader, $loader);
                $self->assertInstanceOf(\Closure::class, $value);
                /* @var ChoiceLoaderInterface|\PHPUnit_Framework_MockObject_MockObject $loader */
                $result = $loader->loadValuesForChoices(array(), $value);

                foreach ($result as &$choice) {
                    $choice = call_user_func($value, $choice);
                }

                return $result;
            });

        $res = $this->decoratorFactory->createListFromLoader($loader, $value);

        $this->assertSame($expected, $res);
    }
}
