<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Tests\Form\ChoiceList\Loader;

use Fxp\Component\FormExtensions\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface;
use Fxp\Component\FormExtensions\Form\ChoiceList\Loader\DynamicChoiceLoaderInterface;
use Fxp\Component\FormExtensions\Tests\Doctrine\Form\Fixtures\MockEntity;
use PHPUnit\Framework\TestCase;

/**
 * Base tests case for dynamic choice loader.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractChoiceLoaderTest extends TestCase
{
    public function getIsGroup()
    {
        return array(
            array(false),
            array(true),
        );
    }

    /**
     * @return \Closure
     */
    protected function getValue()
    {
        return function ($val) {
            return $val instanceof MockEntity
                ? $val->getId()
                : $val;
        };
    }

    /**
     * @param bool|false $group
     *
     * @return DynamicChoiceLoaderInterface|AjaxChoiceLoaderInterface
     */
    abstract protected function createChoiceLoader($group = false);

    /**
     * @param bool $group
     *
     * @return array
     */
    abstract protected function getValidStructuredValues($group);

    /**
     * @param bool $group
     *
     * @return array
     */
    abstract protected function getValidStructuredValuesWithNewTags($group);

    /**
     * @return array
     */
    abstract protected function getDataChoicesForValues();

    /**
     * @param bool $group
     *
     * @return array
     */
    abstract protected function getValidChoicesForValues($group);

    /**
     * @param bool $group
     *
     * @return array
     */
    abstract protected function getValidChoicesForValuesWithNewTags($group);

    /**
     * @param bool $group
     *
     * @return array
     */
    abstract protected function getDataForValuesForChoices($group);

    /**
     * @param bool $group
     *
     * @return array
     */
    abstract protected function getValidValuesForChoices($group);

    /**
     * @param bool $group
     *
     * @return array
     */
    abstract protected function getDataForValuesForChoicesWithNewTags($group);

    /**
     * @param bool $group
     *
     * @return array
     */
    abstract protected function getValidValuesForChoicesWithNewTags($group);

    /**
     * @dataProvider getIsGroup
     *
     * @param bool $group
     */
    public function testDefault($group)
    {
        $loader = $this->createChoiceLoader($group);

        $this->assertNull($loader->getLabel());
        $this->assertEquals(3, $loader->getSize());
        $this->assertFalse($loader->isAllowAdd());

        $loader->setAllowAdd(true);
        $this->assertTrue($loader->isAllowAdd());
    }

    /**
     * @dataProvider getIsGroup
     *
     * @param bool $group
     */
    public function testLoadChoiceList($group)
    {
        $loader = $this->createChoiceLoader($group);
        $choiceList = $loader->loadChoiceList($this->getValue());
        $this->assertInstanceOf('Symfony\Component\Form\ChoiceList\ChoiceListInterface', $choiceList);
        $choiceList2 = $loader->loadChoiceList($this->getValue());
        $this->assertSame($choiceList, $choiceList2);
    }

    /**
     * @dataProvider getIsGroup
     *
     * @param bool $group
     */
    public function testLoadChoiceListForView($group)
    {
        $loader = $this->createChoiceLoader($group);
        $choiceList = $loader->loadChoiceListForView(array('foo', 'bar', 'Test'), $this->getValue());

        $this->assertInstanceOf('Symfony\Component\Form\ChoiceList\ChoiceListInterface', $choiceList);
        $this->assertEquals($this->getValidStructuredValues($group), $choiceList->getStructuredValues());
    }

    /**
     * @dataProvider getIsGroup
     *
     * @param bool $group
     */
    public function testLoadChoiceListForViewWithNewTags($group)
    {
        $loader = $this->createChoiceLoader($group);
        $loader->setAllowAdd(true);
        $choiceList = $loader->loadChoiceListForView(array('foo', 'bar', 'Test'), $this->getValue());

        $this->assertInstanceOf('Symfony\Component\Form\ChoiceList\ChoiceListInterface', $choiceList);

        $this->assertEquals($this->getValidStructuredValuesWithNewTags($group), $choiceList->getStructuredValues());
    }

    /**
     * @dataProvider getIsGroup
     *
     * @param bool $group
     */
    public function testLoadChoicesForValuesWithEmptyValues($group)
    {
        $loader = $this->createChoiceLoader($group);
        $this->assertCount(0, $loader->loadChoicesForValues(array()));
    }

    /**
     * @dataProvider getIsGroup
     *
     * @param bool $group
     */
    public function testLoadChoicesForValuesWithValues($group)
    {
        $loader = $this->createChoiceLoader($group);

        $this->assertEquals($this->getValidChoicesForValues($group), $loader->loadChoicesForValues($this->getDataChoicesForValues()));
    }

    /**
     * @dataProvider getIsGroup
     *
     * @param bool $group
     */
    public function testLoadChoicesForValuesWithValuesAndNewTag($group)
    {
        $loader = $this->createChoiceLoader($group);
        $loader->setAllowAdd(true);

        $this->assertEquals($this->getValidChoicesForValuesWithNewTags($group), $loader->loadChoicesForValues($this->getDataChoicesForValues()));
    }

    /**
     * @dataProvider getIsGroup
     *
     * @param bool $group
     */
    public function testLoadValuesForChoicesWithEmptyValues($group)
    {
        $loader = $this->createChoiceLoader($group);
        $this->assertCount(0, $loader->loadValuesForChoices(array()));
    }

    /**
     * @dataProvider getIsGroup
     *
     * @param bool $group
     */
    public function testLoadValuesForChoices($group)
    {
        $loader = $this->createChoiceLoader($group);

        $this->assertEquals($this->getValidValuesForChoices($group), $loader->loadValuesForChoices($this->getDataForValuesForChoices($group)));
    }

    /**
     * @dataProvider getIsGroup
     *
     * @param bool $group
     */
    public function testLoadValuesForChoicesWithNewTags($group)
    {
        $loader = $this->createChoiceLoader($group);
        $loader->setAllowAdd(true);

        $this->assertEquals($this->getValidValuesForChoicesWithNewTags($group), $loader->loadValuesForChoices($this->getDataForValuesForChoicesWithNewTags($group)));
    }
}
