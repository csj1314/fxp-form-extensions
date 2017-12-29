<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Form\Helper;

use Fxp\Component\FormExtensions\Form\ChoiceList\Formatter\Select2AjaxChoiceListFormatter;

/**
 * Helper for generate the AJAX response for the select2 form choice list.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class Select2ChoiceListHelper extends AjaxChoiceListHelper
{
    /**
     * {@inheritdoc}
     */
    protected static function createChoiceListFormatter()
    {
        return new Select2AjaxChoiceListFormatter();
    }
}
