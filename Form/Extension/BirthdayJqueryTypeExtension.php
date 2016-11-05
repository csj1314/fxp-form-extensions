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

use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class BirthdayJqueryTypeExtension extends DateJqueryTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return BirthdayType::class;
    }
}
