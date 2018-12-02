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

use Fxp\Component\FormExtensions\Form\Extension\ChoiceSelect2TypeExtension;
use Fxp\Component\FormExtensions\Form\Extension\CollectionSelect2TypeExtension;
use Fxp\Component\FormExtensions\Form\Extension\CurrencySelect2TypeExtension;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouterInterface;

/**
 * Tests case for collection of select2 form extension type.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class CollectionSelect2TypeExtensionTest extends AbstractSelect2TypeExtensionTest
{
    protected function buildFormFactory(FormFactoryBuilderInterface $factoryBuilder)
    {
        /* @var EventDispatcherInterface $dispatcher */
        $dispatcher = $this->dispatcher;
        /* @var RouterInterface $router */
        $router = $this->router;
        $extName = $this->getExtensionTypeName();

        $factoryBuilder
            ->addTypeExtension(new ChoiceSelect2TypeExtension($dispatcher, $this->requestStack, $router, 10))
            ->addTypeExtension(new CurrencySelect2TypeExtension($dispatcher, $this->requestStack, $router, 10))
            ->addTypeExtension(new $extName(10))
        ;
    }

    protected function getExtensionTypeName()
    {
        return CollectionSelect2TypeExtension::class;
    }

    protected function getTypeName()
    {
        return CollectionType::class;
    }

    protected function mergeOptions(array $options)
    {
        $options = parent::mergeOptions($options);
        $options['entry_type'] = CurrencyType::class;
        $options['select2'] = isset($options['select2']) ? $options['select2'] : [];

        if (!array_key_exists('enabled', $options['select2'])) {
            $options['select2']['enabled'] = true;
        }

        return $options;
    }

    protected function getSingleData()
    {
        return ['EUR'];
    }

    protected function getValidSingleValue()
    {
        return 'EUR';
    }

    protected function getValidAjaxSingleValue()
    {
        return 'EUR';
    }

    protected function getMultipleData()
    {
        return ['EUR', 'USD'];
    }

    protected function getValidMultipleValue()
    {
        return ['EUR', 'USD'];
    }

    protected function getValidAjaxMultipleValue()
    {
        return implode(',', $this->getValidMultipleValue());
    }

    /**
     * @group bug2
     */
    public function testDefaultOptions()
    {
        $form = $this->factory->create($this->getTypeName(), $this->getSingleData(), $this->mergeOptions([]));

        $this->assertTrue($form->getConfig()->hasAttribute('selector'));
        /* @var FormBuilderInterface $config */
        $config = $form->getConfig()->getAttribute('selector');
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilderInterface', $config);

        $this->assertTrue($form->getConfig()->getOption('compound'));
        $this->assertTrue($form->getConfig()->getOption('allow_add'));
        $this->assertTrue($form->getConfig()->getOption('allow_delete'));

        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertFalse($select2Opts['ajax']);
        $this->assertTrue($select2Opts['tags']);
        $this->assertInstanceOf('Fxp\Component\FormExtensions\Form\ChoiceList\Loader\DynamicChoiceLoaderInterface', $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertArrayHasKey('selector', $view->vars);
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $this->assertEquals($this->getSingleData(), $selectorView->vars['data']);
        $this->assertEquals((array) $this->getValidSingleValue(), $selectorView->vars['value']);
    }

    public function testDefaultEnabledOptions()
    {
        // Skip test
        $this->assertTrue(true);
    }

    public function testDisabled()
    {
        $options = $this->mergeOptions([]);
        $options['select2']['enabled'] = false;
        $form = $this->factory->create($this->getTypeName(), $this->getSingleData(), $options);

        $this->assertFalse($form->getConfig()->hasAttribute('selector'));
        $config = $form->getConfig();

        $this->assertTrue($config->getOption('compound'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertFalse($select2Opts['enabled']);

        $view = $form->createView();
        $this->assertArrayNotHasKey('selector', $view->vars);
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals((array) $this->getValidSingleValue(), $view->vars['value']);
    }

    public function testSingleWithTags()
    {
        $options = ['select2' => ['tags' => true]];
        $form = $this->factory->create($this->getTypeName(), $this->getSingleData(), $this->mergeOptions($options));

        $this->assertTrue($form->getConfig()->hasAttribute('selector'));
        /* @var FormBuilderInterface $config */
        $config = $form->getConfig()->getAttribute('selector');
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilderInterface', $config);

        $this->assertTrue($form->getConfig()->getOption('compound'));
        $this->assertTrue($form->getConfig()->getOption('allow_add'));
        $this->assertTrue($form->getConfig()->getOption('allow_delete'));

        $this->assertFalse($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertFalse($select2Opts['ajax']);
        $this->assertInstanceOf('Fxp\Component\FormExtensions\Form\ChoiceList\Loader\DynamicChoiceLoaderInterface', $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertArrayHasKey('selector', $view->vars);
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $this->assertEquals($this->getSingleData(), $selectorView->vars['data']);
        $this->assertEquals((array) $this->getValidSingleValue(), $selectorView->vars['value']);
        $this->assertArrayHasKey('tags', $selectorView->vars['select2']);
    }

    public function testSingleAjax()
    {
        $options = ['select2' => ['ajax' => true]];
        $form = $this->factory->create($this->getTypeName(), $this->getSingleData(), $this->mergeOptions($options));

        $this->assertTrue($form->getConfig()->hasAttribute('selector'));
        /* @var FormBuilderInterface $config */
        $config = $form->getConfig()->getAttribute('selector');
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilderInterface', $config);

        $this->assertTrue($form->getConfig()->getOption('compound'));
        $this->assertTrue($form->getConfig()->getOption('allow_add'));
        $this->assertTrue($form->getConfig()->getOption('allow_delete'));

        $this->assertFalse($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertTrue($select2Opts['ajax']);
        $this->assertInstanceOf('Fxp\Component\FormExtensions\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface', $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertArrayHasKey('selector', $view->vars);
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $this->assertEquals($this->getSingleData(), $selectorView->vars['data']);
        $this->assertEquals((array) $this->getValidAjaxSingleValue(), $selectorView->vars['value']);
    }

    public function testSingleAjaxWithTags()
    {
        $options = ['select2' => ['ajax' => true, 'tags' => true]];
        $form = $this->factory->create($this->getTypeName(), $this->getSingleData(), $this->mergeOptions($options));

        $this->assertTrue($form->getConfig()->hasAttribute('selector'));
        /* @var FormBuilderInterface $config */
        $config = $form->getConfig()->getAttribute('selector');
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilderInterface', $config);

        $this->assertTrue($form->getConfig()->getOption('compound'));
        $this->assertTrue($form->getConfig()->getOption('allow_add'));
        $this->assertTrue($form->getConfig()->getOption('allow_delete'));

        $this->assertFalse($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertTrue($select2Opts['ajax']);
        $this->assertTrue($select2Opts['tags']);
        $this->assertInstanceOf('Fxp\Component\FormExtensions\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface', $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertArrayHasKey('selector', $view->vars);
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $this->assertEquals($this->getSingleData(), $selectorView->vars['data']);
        $this->assertEquals((array) $this->getValidAjaxSingleValue(), $selectorView->vars['value']);
    }

    public function testMultiple()
    {
        // Skip test
        $this->assertTrue(true);
    }

    public function testMultipleAjax()
    {
        // Skip test
        $this->assertTrue(true);
    }

    public function testRequiredAjaxEmptyChoice()
    {
        $options = ['select2' => ['ajax' => true]];
        $form = $this->factory->create($this->getTypeName(), null, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertArrayHasKey('selector', $view->vars);
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $this->assertEquals([], $selectorView->vars['choices']);
    }

    public function testSinglePlaceHolder()
    {
        // Skip test
        $this->assertTrue(true);
    }

    public function testAjaxRoute()
    {
        $options = ['required' => false, 'select2' => ['ajax' => true, 'ajax_route' => 'foobar']];
        $form = $this->factory->create($this->getTypeName(), null, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertArrayHasKey('selector', $view->vars);
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $this->assertEquals('/foobar', $selectorView->vars['select2']['ajax']['url']);
    }

    public function testAjaxEmptyRoute()
    {
        // Skip test
        $this->assertTrue(true);
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The "Symfony\Component\Form\Extension\Core\Type\TextType" type is not an "choice" with Select2 extension, because: the options "multiple", "select2" do not exist.
     */
    public function testWithoutChoice()
    {
        $options = $this->mergeOptions([]);
        $options['entry_type'] = TextType::class;

        $this->factory->create($this->getTypeName(), null, $options);
    }

    public function testChoiceLoaderOption()
    {
        // Skip test
        $this->assertTrue(true);
    }

    public function testInvalidChoiceLoaderOption()
    {
        // Skip test
        $this->assertTrue(true);
    }

    public function testDefaultType()
    {
        $options = [
            'select2' => [
                'enabled' => true,
            ],
        ];

        $form = $this->factory->create($this->getTypeName(), null, $options);

        $this->assertSame('Symfony\Component\Form\Extension\Core\Type\ChoiceType', $form->getConfig()->getOption('entry_type'));
    }

    public function testAllowAddTag()
    {
        $options = ['allow_add' => true, 'entry_options' => ['choices' => ['Bar' => 'foo']]];
        $data = ['foo', 'Baz'];
        $form = $this->factory->create($this->getTypeName(), $data, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertArrayHasKey('selector', $view->vars);
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $valid = [
            'foo' => new ChoiceView('foo', 'foo', 'Bar'),
            'Baz' => new ChoiceView('Baz', 'Baz', 'Baz'),
        ];
        $this->assertEquals($valid, $selectorView->vars['choices']);
        $this->assertSame('true', $selectorView->vars['select2']['tags']);
    }

    public function testDenyAddTag()
    {
        $options = ['allow_add' => false, 'entry_options' => ['choices' => ['Bar' => 'foo']]];
        $data = ['foo', 'Baz'];
        $form = $this->factory->create($this->getTypeName(), $data, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertArrayHasKey('selector', $view->vars);
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $valid = [
            'foo' => new ChoiceView('foo', 'foo', 'Bar'),
        ];
        $this->assertEquals($valid, $selectorView->vars['choices']);
        $this->assertArrayNotHasKey('tags', $selectorView->vars['select2']);
    }

    public function testAjaxRouteAttribute()
    {
        // Skip test
        $this->assertTrue(true);
    }
}
