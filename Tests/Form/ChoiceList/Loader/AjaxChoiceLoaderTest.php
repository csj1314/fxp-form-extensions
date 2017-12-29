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

use Fxp\Component\FormExtensions\Form\ChoiceList\Loader\AjaxChoiceLoader;

/**
 * Tests case for ajax choice loader.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AjaxChoiceLoaderTest extends AbstractAjaxChoiceLoaderTest
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

        return new AjaxChoiceLoader($choices);
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
            ];
        }

        return [
            'Bar' => 'foo',
            'Foo' => 'bar',
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
    protected function getValidStructuredValuesForSearch($group)
    {
        if ($group) {
            $valid = [
                'Group 1' => [
                    'Bar' => 'foo',
                ],
                'Group 2' => [
                    'Baz' => 'baz',
                ],
            ];
        } else {
            $valid = [
                'Bar' => 'foo',
                'Baz' => 'baz',
            ];
        }

        return $valid;
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidStructuredValuesForPagination($group, $pageNumber, $pageSize)
    {
        if ($group) {
            $valid = [
                'Group 1' => [
                    'Bar' => 'foo',
                    'Foo' => 'bar',
                ],
            ];

            if ($pageSize <= 0) {
                $valid['Group 2'] = [
                    'Baz' => 'baz',
                ];
            }

            if (2 === $pageNumber) {
                $valid = [
                    'Group 2' => [
                        'Baz' => 'baz',
                    ],
                ];
            }
        } else {
            $valid = [
                'Bar' => 'foo',
                'Foo' => 'bar',
            ];

            if ($pageSize <= 0) {
                $valid['Baz'] = 'baz';
            }

            if (2 === $pageNumber) {
                $valid = [
                    'Baz' => 'baz',
                ];
            }
        }

        return $valid;
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
