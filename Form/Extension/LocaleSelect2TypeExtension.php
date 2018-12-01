<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type\LocaleType;

/**
 * Locale choice type extension.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class LocaleSelect2TypeExtension extends AbstractChoiceSelect2TypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return LocaleType::class;
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes()
    {
        return [LocaleType::class];
    }
}
