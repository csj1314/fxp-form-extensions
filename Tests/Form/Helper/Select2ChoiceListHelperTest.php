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

/**
 * Tests case for select2 choice list helper.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class Select2ChoiceListHelperTest extends AjaxChoiceListHelperTest
{
    /**
     * {@inheritdoc}
     */
    protected function getHelperClass()
    {
        return 'Fxp\Component\FormExtensions\Form\Helper\Select2ChoiceListHelper';
    }

    /**
     * @dataProvider getAjaxIds
     *
     * @param null|string|array $ajaxIds
     */
    public function testGenerateResponseWithCreateFormatter($ajaxIds)
    {
        $validContent = array(
            'size' => null,
            'pageNumber' => null,
            'pageSize' => null,
            'search' => null,
            'items' => array(),
        );

        $this->executeGenerateResponseWithCreateFormatter($ajaxIds, $validContent);
    }

    public function testInvalidFormatter()
    {
        // skip test
        $this->assertTrue(true);
    }
}
