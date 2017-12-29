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

use Fxp\Component\FormExtensions\Form\ChoiceList\Loader\DynamicChoiceLoader;

/**
 * Tests case for dynamic choice loader.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class DynamicChoiceLoaderTest extends AbstractChoiceLoaderTest
{
    /**
     * {@inheritdoc}
     */
    protected function createChoiceLoader($group = false)
    {
        if ($group) {
            $choices = [
                'Group 1' => [
                    'Bar' => 'foo',
                    'Foo' => 'bar',
                ],
                'Group 2' => [
                    'Baz' => 'baz',
                ],
            ];
        } else {
            $choices = [
                'Bar' => 'foo',
                'Foo' => 'bar',
                'Baz' => 'baz',
            ];
        }

        return new DynamicChoiceLoader($choices);
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidStructuredValues($group)
    {
        if ($group) {
            return [
                'Group 1' => [
                    'Bar' => 'foo',
                    'Foo' => 'bar',
                ],
                'Group 2' => [
                    'Baz' => 'baz',
                ],
            ];
        }

        return [
            'Bar' => 'foo',
            'Foo' => 'bar',
            'Baz' => 'baz',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidStructuredValuesWithNewTags($group)
    {
        $existing = $this->getValidStructuredValues($group);

        if ($group) {
            $existing['-------'] = [
                'Test' => 'Test',
            ];
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
            'foo',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidChoicesForValuesWithNewTags($group)
    {
        return [
            'foo',
            'Test',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDataForValuesForChoices($group)
    {
        return [
            'foo',
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
        return [
            0,
            'Test',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidValuesForChoicesWithNewTags($group)
    {
        return [
            2 => '0',
            3 => 'Test',
        ];
    }
}
