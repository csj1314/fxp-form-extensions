<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Component\FormExtensions\Form\Extension;

use Sonatra\Component\FormExtensions\Form\ChoiceList\Loader\AjaxChoiceLoader;
use Sonatra\Component\FormExtensions\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface;
use Sonatra\Component\FormExtensions\Form\ChoiceList\Loader\DynamicChoiceLoader;
use Sonatra\Component\FormExtensions\Form\ChoiceList\Loader\DynamicChoiceLoaderInterface;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\Options;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class Select2Util
{
    /**
     * Convert the array to the ajax choice loader.
     *
     * @param ChoiceListFactoryInterface                                  $choiceListFactory The choice list factory
     * @param Options                                                     $options           The options
     * @param DynamicChoiceLoaderInterface|AjaxChoiceLoaderInterface|null $value             The value of choice loader normalizer
     *
     * @return DynamicChoiceLoaderInterface|AjaxChoiceLoaderInterface The dynamic choice loader
     */
    public static function convertToDynamicLoader(ChoiceListFactoryInterface $choiceListFactory, Options $options, $value)
    {
        if ($value instanceof DynamicChoiceLoaderInterface) {
            return $value;
        }

        if (!is_array($options['choices'])) {
            throw new InvalidConfigurationException('The "choice_loader" option must be an instance of DynamicChoiceLoaderInterface or the "choices" option must be an array');
        }

        if ($options['select2']['ajax']) {
            return new AjaxChoiceLoader(static::getChoices($options, $value),
                $choiceListFactory);
        }

        return new DynamicChoiceLoader(static::getChoices($options, $value),
            $choiceListFactory);
    }

    /**
     * Get the choices.
     *
     * @param Options                                                     $options The options
     * @param DynamicChoiceLoaderInterface|AjaxChoiceLoaderInterface|null $value   The value of choice loader normalizer
     *
     * @return array
     */
    private static function getChoices(Options $options, $value)
    {
        return $value instanceof ChoiceLoaderInterface && empty($options['choices'])
            ? $value->loadChoiceList()->getStructuredValues()
            : $options['choices'];
    }
}
