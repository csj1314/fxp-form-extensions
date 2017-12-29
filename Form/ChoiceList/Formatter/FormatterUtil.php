<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Form\ChoiceList\Formatter;

use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Symfony\Component\Form\ChoiceList\View\ChoiceListView;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FormatterUtil
{
    /**
     * Format the result data.
     *
     * @param AjaxChoiceListFormatterInterface $formatter      The ajax formatter
     * @param ChoiceListView                   $choiceListView The choice list view
     *
     * @return array The formatted result data
     */
    public static function formatResultData(AjaxChoiceListFormatterInterface $formatter, ChoiceListView $choiceListView)
    {
        $result = array();

        foreach ($choiceListView->choices as $i => $choiceView) {
            if ($choiceView instanceof ChoiceGroupView) {
                $group = $formatter->formatGroupChoice($choiceView);

                foreach ($choiceView->choices as $j => $subChoiceView) {
                    $group = $formatter->addChoiceInGroup($group, $subChoiceView);
                }

                if (!$formatter->isEmptyGroup($group)) {
                    $result[] = $group;
                }
            } else {
                $result[] = $formatter->formatChoice($choiceView);
            }
        }

        return $result;
    }
}
