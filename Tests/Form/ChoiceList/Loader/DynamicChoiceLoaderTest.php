<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Component\FormExtensions\Tests\Form\ChoiceList\Loader;

use Sonatra\Component\FormExtensions\Form\ChoiceList\Loader\DynamicChoiceLoader;

/**
 * Tests case for dynamic choice loader.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class DynamicChoiceLoaderTest extends AbstractChoiceLoaderTest
{
    /**
     * {@inheritdoc}
     */
    protected function createChoiceLoader($group = false)
    {
        if ($group) {
            $choices = array(
                'Group 1' => array(
                    'Bar' => 'foo',
                    'Foo' => 'bar',
                ),
                'Group 2' => array(
                    'Baz' => 'baz',
                ),
            );
        } else {
            $choices = array(
                'Bar' => 'foo',
                'Foo' => 'bar',
                'Baz' => 'baz',
            );
        }

        return new DynamicChoiceLoader($choices);
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidStructuredValues($group)
    {
        if ($group) {
            return array(
                'Group 1' => array(
                    'Bar' => 'foo',
                    'Foo' => 'bar',
                ),
                'Group 2' => array(
                    'Baz' => 'baz',
                ),
            );
        }

        return array(
            'Bar' => 'foo',
            'Foo' => 'bar',
            'Baz' => 'baz',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidStructuredValuesWithNewTags($group)
    {
        $existing = $this->getValidStructuredValues($group);

        if ($group) {
            $existing['-------'] = array(
                'Test' => 'Test',
            );
        } else {
            $existing['Test'] = 'Test';
        }

        return $existing;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDataChoicesForValues()
    {
        return array(
            'foo',
            'Test',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidChoicesForValues($group)
    {
        return array(
            'foo',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidChoicesForValuesWithNewTags($group)
    {
        return array(
            'foo',
            'Test',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getDataForValuesForChoices($group)
    {
        return array(
            'foo',
            'Test',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidValuesForChoices($group)
    {
        return array(
            'foo',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getDataForValuesForChoicesWithNewTags($group)
    {
        return array(
            0,
            'Test',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidValuesForChoicesWithNewTags($group)
    {
        return array(
            2 => '0',
            3 => 'Test',
        );
    }
}
