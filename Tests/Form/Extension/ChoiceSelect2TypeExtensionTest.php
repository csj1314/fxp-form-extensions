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

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormConfigInterface;

/**
 * Tests case for choice of select2 form extension type.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class ChoiceSelect2TypeExtensionTest extends AbstractSelect2TypeExtensionTest
{
    protected function getChoices()
    {
        return array_flip([0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D']);
    }

    protected function getExtensionTypeName()
    {
        return ChoiceType::class;
    }

    protected function getSingleData()
    {
        return 1;
    }

    protected function getValidSingleValue()
    {
        return '1';
    }

    protected function getValidAjaxSingleValue()
    {
        return '1';
    }

    protected function getMultipleData()
    {
        return ['1', '2'];
    }

    protected function getValidMultipleValue()
    {
        return ['1', '2'];
    }

    protected function getValidAjaxMultipleValue()
    {
        return $this->getValidMultipleValue();
    }

    protected function validateChoiceLoaderForDefaultOptions(FormConfigInterface $config)
    {
        $this->assertNull($config->getOption('choice_loader'));
    }
}
