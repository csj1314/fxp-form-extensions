<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Tests\Form\ChoiceList\Formatter;

use Fxp\Component\FormExtensions\Form\ChoiceList\Formatter\Select2AjaxChoiceListFormatter;

/**
 * Tests case for select2 choice list formatter.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class Select2AjaxChoiceListFormatterTest extends AbstractAjaxChoiceListFormatterTest
{
    /**
     * {@inheritdoc}
     */
    protected function getFormatter()
    {
        return new Select2AjaxChoiceListFormatter($this->choiceListFactory);
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidResponseData()
    {
        return array(
            'size' => 3,
            'pageNumber' => 1,
            'pageSize' => 10,
            'search' => null,
            'items' => array(
                array(
                    'id' => '0',
                    'text' => 'Bar',
                ),
                array(
                    'id' => '1',
                    'text' => 'Foo',
                ),
                array(
                    'id' => '2',
                    'text' => 'Baz',
                ),
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidGroupResponseData()
    {
        return array(
            'size' => 3,
            'pageNumber' => 1,
            'pageSize' => 10,
            'search' => null,
            'items' => array(
                array(
                    'text' => 'Group 1',
                    'children' => array(
                        array(
                            'id' => '0',
                            'text' => 'Bar',
                        ),
                        array(
                            'id' => '1',
                            'text' => 'Foo',
                        ),
                    ),
                ),
                array(
                    'text' => 'Group 2',
                    'children' => array(
                        array(
                            'id' => '2',
                            'text' => 'Baz',
                        ),
                    ),
                ),
            ),
        );
    }
}
