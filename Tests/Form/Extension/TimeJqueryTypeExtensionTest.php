<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Tests\Form\Extension;

use Fxp\Component\FormExtensions\Form\Extension\DateTimeJqueryTypeExtension;
use Fxp\Component\FormExtensions\Form\Extension\TimeJqueryTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Tests case for time jquery form extension type.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class TimeJqueryTypeExtensionTest extends TypeTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(
                new DateTimeJqueryTypeExtension()
            )
            ->addTypeExtension(
                new TimeJqueryTypeExtension()
            )
            ->getFormFactory();

        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    public function testDefaultOption()
    {
        $form = $this->factory->create(TimeType::class, null, ['locale' => 'en']);
        $config = $form->getConfig();

        $this->assertEquals('single_text', $config->getOption('widget'));
        $this->assertFalse($config->getOption('date_picker'));
        $this->assertTrue($config->getOption('time_picker'));
        $this->assertEquals('en', $config->getOption('locale'));
        $this->assertEquals('h:mm a', $config->getOption('format'));
    }

    public function testFormatFr()
    {
        $form = $this->factory->create(TimeType::class, null, ['locale' => 'fr_FR']);

        $this->assertEquals('HH:mm', $form->getConfig()->getOption('format'));
    }

    public function testDefaultAttributes()
    {
        $form = $this->factory->create(TimeType::class, null, ['locale' => 'en']);
        $view = $form->createView();
        $validAttr = [
            'data-locale' => 'en',
            'data-date-picker' => 'false',
            'data-time-picker' => 'true',
            'data-time-picker-first' => 'false',
            'data-open-focus' => 'true',
            'data-format' => 'h:mm A',
            'data-with-minutes' => 'true',
            'data-with-seconds' => 'false',
            'data-datetime-picker' => 'true',
            'data-button-id' => 'time_datetime_btn',
        ];

        $this->assertEquals($validAttr, $view->vars['attr']);
    }

    public function testWidgetIsNotSingleText()
    {
        $form = $this->factory->create(TimeType::class, null, [
            'locale' => 'en_EN',
            'widget' => 'text',
        ]);
        $view = $form->createView();

        $this->assertEquals([], $view->vars['attr']);
    }

    public function testGetExtendedType()
    {
        $ext = new TimeJqueryTypeExtension();
        $this->assertEquals(TimeJqueryTypeExtension::getExtendedTypes(), [$ext->getExtendedType()]);
    }
}
