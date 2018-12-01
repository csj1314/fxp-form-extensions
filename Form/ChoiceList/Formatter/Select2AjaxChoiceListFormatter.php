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

use Fxp\Component\FormExtensions\Doctrine\Form\ChoiceList\Factory\TagDecorator;
use Fxp\Component\FormExtensions\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Factory\DefaultChoiceListFactory;
use Symfony\Component\Form\ChoiceList\Factory\PropertyAccessDecorator;
use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class Select2AjaxChoiceListFormatter implements AjaxChoiceListFormatterInterface
{
    /**
     * @var ChoiceListFactoryInterface
     */
    private $choiceListFactory;

    /**
     * Constructor.
     *
     * @param ChoiceListFactoryInterface|null $choiceListFactory
     */
    public function __construct(ChoiceListFactoryInterface $choiceListFactory = null)
    {
        $this->choiceListFactory = $choiceListFactory ?: new PropertyAccessDecorator(new TagDecorator(new DefaultChoiceListFactory()));
    }

    /**
     * {@inheritdoc}
     */
    public function formatResponseData(AjaxChoiceLoaderInterface $choiceLoader)
    {
        $view = $this->choiceListFactory->createView($choiceLoader->loadPaginatedChoiceList(), null, $choiceLoader->getLabel());

        return [
            'size' => $choiceLoader->getSize(),
            'pageNumber' => $choiceLoader->getPageNumber(),
            'pageSize' => $choiceLoader->getPageSize(),
            'search' => $choiceLoader->getSearch(),
            'items' => FormatterUtil::formatResultData($this, $view),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function formatChoice(ChoiceView $choice)
    {
        return [
            'id' => $choice->value,
            'text' => $choice->label,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function formatGroupChoice(ChoiceGroupView $choiceGroup)
    {
        return [
            'text' => $choiceGroup->label,
            'children' => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addChoiceInGroup($group, ChoiceView $choice)
    {
        $group['children'][] = $this->formatChoice($choice);

        return $group;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmptyGroup($group)
    {
        return 0 === \count($group['children']);
    }
}
