<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Component\FormExtensions\Doctrine\Form\Extension;

use Sonatra\Component\FormExtensions\Doctrine\Form\ChoiceList\AjaxEntityLoaderInterface;
use Sonatra\Component\FormExtensions\Doctrine\Form\ChoiceList\AjaxORMQueryBuilderLoader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class EntitySelect2TypeExtension extends DoctrineSelect2TypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return EntityType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoader(Options $options, $queryBuilder)
    {
        return null !== $options['ajax_entity_loader']
            ? $options['ajax_entity_loader']
            : new AjaxORMQueryBuilderLoader($queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilderPartsForCachingHash($queryBuilder)
    {
        return array(
            $queryBuilder->getQuery()->getSQL(),
            $queryBuilder->getParameters()->toArray(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'ajax_entity_loader' => null,
        ));

        $resolver->addAllowedTypes('ajax_entity_loader', array('null', AjaxEntityLoaderInterface::class));

        parent::configureOptions($resolver);
    }
}
