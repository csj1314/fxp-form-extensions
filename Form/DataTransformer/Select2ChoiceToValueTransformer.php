<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Component\FormExtensions\Form\DataTransformer;

use Sonatra\Component\FormExtensions\Form\ChoiceList\Loader\DynamicChoiceLoaderInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class Select2ChoiceToValueTransformer implements DataTransformerInterface
{
    private $choiceLoader;

    /**
     * Constructor.
     *
     * @param DynamicChoiceLoaderInterface $choiceLoader
     */
    public function __construct(DynamicChoiceLoaderInterface $choiceLoader)
    {
        $this->choiceLoader = $choiceLoader;
    }

    public function transform($choices)
    {
        return current($this->choiceLoader->loadValuesForChoices((array) $choices));
    }

    public function reverseTransform($value)
    {
        if (null !== $value && !is_scalar($value)) {
            throw new TransformationFailedException('Expected a scalar.');
        }

        $choices = $this->choiceLoader->loadChoicesForValues(array((string) $value));

        if (1 !== count($choices)) {
            if (null === $value || '' === $value) {
                return;
            }

            throw new TransformationFailedException(sprintf('The choice "%s" does not exist or is not unique', $value));
        }

        return current($choices);
    }
}
