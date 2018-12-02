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

use Fxp\Component\Ajax\AjaxEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormFactoryBuilderInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

/**
 * Tests case for abstract select2 form extension type.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractSelect2TypeExtensionTest extends TypeTestCase
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var RouterInterface||\PHPUnit_Framework_MockObject_MockObject
     */
    protected $router;

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dispatcher;

    protected function setUp()
    {
        parent::setUp();

        \Locale::setDefault('en');

        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
        $this->router = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')->getMock();
        $this->requestStack = new RequestStack();
        /* @var Request $request */
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $this->requestStack->push($request);

        $this->router->expects($this->any())
            ->method('generate')
            ->will($this->returnCallback(function ($param) {
                return '/'.$param;
            }))
        ;

        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();

        $factoryBuilder = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
        ;
        $this->buildFormFactory($factoryBuilder);

        $this->factory = $factoryBuilder->getFormFactory();
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->requestStack = null;
        $this->router = null;
    }

    /**
     * Build the form factory builder.
     *
     * @param FormFactoryBuilderInterface $factoryBuilder The form factory builder
     */
    protected function buildFormFactory(FormFactoryBuilderInterface $factoryBuilder)
    {
        $extName = $this->getExtensionTypeName();
        $factoryBuilder->addTypeExtension(new $extName($this->dispatcher, $this->requestStack, $this->router, 10));
    }

    /**
     * @return array|null
     */
    protected function getChoices()
    {
        return;
    }

    protected function mergeOptions(array $options)
    {
        $choices = $this->getChoices();

        if (\is_array($choices)) {
            $options['choices'] = $choices;
        }

        return $options;
    }

    protected function getDynamicLoaderInterface()
    {
        return 'Fxp\Component\FormExtensions\Form\ChoiceList\Loader\DynamicChoiceLoaderInterface';
    }

    protected function getAjaxLoaderInterface()
    {
        return 'Fxp\Component\FormExtensions\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface';
    }

    /**
     * @return string
     */
    abstract protected function getExtensionTypeName();

    /**
     * @return string
     */
    abstract protected function getTypeName();

    /**
     * @return string
     */
    abstract protected function getSingleData();

    /**
     * @return string
     */
    abstract protected function getValidSingleValue();

    /**
     * @return string
     */
    abstract protected function getValidAjaxSingleValue();

    /**
     * @return array
     */
    abstract protected function getMultipleData();

    /**
     * @return array
     */
    abstract protected function getValidMultipleValue();

    /**
     * @return string
     */
    abstract protected function getValidAjaxMultipleValue();

    public function testDefaultOptions()
    {
        $form = $this->factory->create($this->getTypeName(), $this->getSingleData(), $this->mergeOptions([]));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertFalse($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertFalse($select2Opts['enabled']);
        $this->assertFalse($select2Opts['ajax']);
        $this->validateChoiceLoaderForDefaultOptions($config);

        $view = $form->createView();

        $this->assertArrayNotHasKey('select2', $view->vars);
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidSingleValue(), $view->vars['value']);
    }

    protected function validateChoiceLoaderForDefaultOptions(FormConfigInterface $config)
    {
        $this->assertInstanceOf(ChoiceLoaderInterface::class, $config->getOption('choice_loader'));
    }

    public function testDefaultEnabledOptions()
    {
        $form = $this->factory->create($this->getTypeName(), $this->getSingleData(), $this->mergeOptions([
            'select2' => [
                'enabled' => true,
            ],
        ]));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertFalse($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertFalse($select2Opts['ajax']);
        $this->assertFalse($select2Opts['tags']);
        $this->assertInstanceOf($this->getDynamicLoaderInterface(), $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertArrayHasKey('select2', $view->vars);
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidSingleValue(), $view->vars['value']);
    }

    public function testDisabled()
    {
        $options = ['select2' => ['enabled' => false]];
        $form = $this->factory->create($this->getTypeName(), $this->getSingleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertFalse($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertFalse($select2Opts['enabled']);
        $this->assertFalse($select2Opts['tags']);

        $view = $form->createView();
        $this->assertArrayNotHasKey('select2', $view->vars);
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidSingleValue(), $view->vars['value']);
    }

    public function testSingleWithTags()
    {
        $options = ['select2' => ['enabled' => true, 'tags' => true]];
        $form = $this->factory->create($this->getTypeName(), $this->getSingleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertFalse($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertFalse($select2Opts['ajax']);
        $this->assertTrue($select2Opts['tags']);
        $this->assertInstanceOf($this->getDynamicLoaderInterface(), $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertArrayHasKey('select2', $view->vars);
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidSingleValue(), $view->vars['value']);
        $this->assertArrayHasKey('tags', $view->vars['select2']);
    }

    public function testSingleAjax()
    {
        $options = ['select2' => ['enabled' => true, 'ajax' => true]];
        $form = $this->factory->create($this->getTypeName(), $this->getSingleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertFalse($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertTrue($select2Opts['ajax']);
        $this->assertFalse($select2Opts['tags']);
        $this->assertInstanceOf($this->getAjaxLoaderInterface(), $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertArrayHasKey('select2', $view->vars);
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidAjaxSingleValue(), $view->vars['value']);
    }

    public function testSingleAjaxWithTags()
    {
        $options = ['select2' => ['enabled' => true, 'ajax' => true, 'tags' => true]];
        $form = $this->factory->create($this->getTypeName(), $this->getSingleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertFalse($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertTrue($select2Opts['ajax']);
        $this->assertTrue($select2Opts['tags']);
        $this->assertInstanceOf($this->getAjaxLoaderInterface(), $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertArrayHasKey('select2', $view->vars);
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidAjaxSingleValue(), $view->vars['value']);
    }

    public function testMultiple()
    {
        $options = ['multiple' => true, 'select2' => ['enabled' => true]];
        $form = $this->factory->create($this->getTypeName(), $this->getMultipleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertFalse($select2Opts['ajax']);
        $this->assertFalse($select2Opts['tags']);
        $this->assertInstanceOf($this->getDynamicLoaderInterface(), $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertArrayHasKey('select2', $view->vars);
        $this->assertEquals($this->getMultipleData(), $view->vars['data']);
        $this->assertEquals($this->getValidMultipleValue(), $view->vars['value']);
    }

    public function testMultipleAjax()
    {
        $options = ['multiple' => true, 'select2' => ['enabled' => true, 'ajax' => true]];
        $form = $this->factory->create($this->getTypeName(), $this->getMultipleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertTrue($select2Opts['ajax']);
        $this->assertFalse($select2Opts['tags']);
        $this->assertInstanceOf($this->getAjaxLoaderInterface(), $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertArrayHasKey('select2', $view->vars);
        $this->assertEquals($this->getMultipleData(), $view->vars['data']);
        $this->assertEquals($this->getValidAjaxMultipleValue(), $view->vars['value']);
    }

    public function testRequiredAjaxEmptyChoice()
    {
        $options = ['select2' => ['enabled' => true, 'ajax' => true]];
        $form = $this->factory->create($this->getTypeName(), null, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertEquals([], $view->vars['choices']);
    }

    public function testSinglePlaceHolder()
    {
        $options = ['required' => false, 'select2' => ['enabled' => true, 'ajax' => true]];
        $form = $this->factory->create($this->getTypeName(), null, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertTrue(isset($view->vars['placeholder']));
        $this->assertEquals('', $view->vars['placeholder']);
    }

    public function testAjaxRoute()
    {
        $options = ['required' => false, 'select2' => ['enabled' => true, 'ajax' => true, 'ajax_route' => 'foobar']];
        $form = $this->factory->create($this->getTypeName(), null, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertEquals('/foobar', $view->vars['select2']['ajax']['url']);
    }

    public function testAjaxEmptyRoute()
    {
        $options = ['required' => false, 'select2' => ['enabled' => true, 'ajax' => true, 'ajax_route' => null]];
        $formBuilder = $this->factory->createBuilder($this->getTypeName(), null, $this->mergeOptions($options));
        $formBuilder->setAttribute('select2_ajax_route', null);
        $form = $formBuilder->getForm();

        $this->assertNull($form->getConfig()->getAttribute('select2_ajax_route'));

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(AjaxEvents::INJECTION);

        $form->createView();
    }

    public function testAjaxUrl()
    {
        $options = ['required' => false, 'select2' => ['enabled' => true, 'ajax' => true, 'ajax_url' => '/foo/bar']];
        $form = $this->factory->create($this->getTypeName(), null, $this->mergeOptions($options));
        $view = $form->createView();

        $url = $this instanceof CollectionSelect2TypeExtensionTest
            ? $view->vars['selector']->vars['select2']['ajax']['url']
            : $view->vars['select2']['ajax']['url'];

        $this->assertEquals('/foo/bar', $url);
    }

    public function testChoiceLoaderOption()
    {
        $choiceLoader = $this->getMockBuilder($this->getDynamicLoaderInterface())->getMock();
        $choiceLoader->expects($this->any())
            ->method('loadValuesForChoices')
            ->will($this->returnValue([]));
        $choiceLoader->expects($this->any())
            ->method('loadChoicesForValues')
            ->will($this->returnValue([]));

        $options = ['select2' => ['enabled' => true], 'choice_loader' => $choiceLoader];

        $form = $this->factory->create($this->getTypeName(), null, $this->mergeOptions($options));

        $this->assertSame($choiceLoader, $form->getConfig()->getOption('choice_loader'));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The "choice_loader" option must be an instance of DynamicChoiceLoaderInterface or the "choices" option must be an array
     */
    public function testInvalidChoiceLoaderOption()
    {
        $options = ['select2' => ['enabled' => true], 'choices' => null];

        $this->factory->create($this->getTypeName(), null, $options);
    }

    public function testAjaxRouteAttribute()
    {
        $form = $this->factory->create($this->getTypeName());
        $config = $form->getConfig();

        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertArrayHasKey('ajax_route', $select2Opts);
        $this->assertNull($select2Opts['ajax_route']);
        $this->assertEquals('fxp_form_extensions_ajax_'.StringUtil::fqcnToBlockPrefix($this->getTypeName()), $config->getAttribute('select2_ajax_route'));
    }
}
