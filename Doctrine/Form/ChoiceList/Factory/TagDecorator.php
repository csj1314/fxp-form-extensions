<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Doctrine\Form\ChoiceList\Factory;

use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class TagDecorator implements ChoiceListFactoryInterface
{
    /**
     * @var ChoiceListFactoryInterface
     */
    private $decoratedFactory;

    /**
     * Decorates the given factory.
     *
     * @param ChoiceListFactoryInterface $decoratedFactory The decorated factory
     */
    public function __construct(ChoiceListFactoryInterface $decoratedFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createListFromChoices($choices, $value = null)
    {
        $value = function ($choice) use ($value) {
            if (is_string($choice)) {
                return $choice;
            }

            return is_callable($value)
                ? call_user_func($value, $choice)
                : $choice;
        };

        return $this->decoratedFactory->createListFromChoices($choices, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function createListFromLoader(ChoiceLoaderInterface $loader, $value = null)
    {
        $value = function ($choice) use ($value) {
            if (is_string($choice)) {
                return $choice;
            }

            return is_callable($value)
                ? call_user_func($value, $choice)
                : $choice;
        };

        return $this->decoratedFactory->createListFromLoader($loader, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function createView(ChoiceListInterface $list, $preferredChoices = null, $label = null, $index = null, $groupBy = null, $attr = null)
    {
        $label = function ($choice) use ($label, $list) {
            if (is_string($choice)) {
                if (null === $label) {
                    $keys = $list->getOriginalKeys();

                    if ($keys[$choice]) {
                        $choice = $keys[$choice];
                    }
                }

                return $choice;
            }

            return is_callable($label)
                ? call_user_func($label, $choice)
                : $label;
        };
        $index = function ($choice, $position) use ($index) {
            if (is_string($choice)) {
                return $choice;
            }

            return is_callable($index)
                ? call_user_func($index, $choice)
                : (null !== $index ? $index : $position);
        };

        return $this->decoratedFactory->createView($list, $preferredChoices, $label, $index, $groupBy, $attr);
    }
}
