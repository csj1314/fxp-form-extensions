<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Component\FormExtensions\Doctrine\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Sonatra\Component\FormExtensions\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as BaseEntityType;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class EntityType extends BaseEntityType
{
    /**
     * {@inheritdoc}
     */
    public function getLoader(ObjectManager $manager, $queryBuilder, $class)
    {
        return new ORMQueryBuilderLoader($queryBuilder);
    }
}
