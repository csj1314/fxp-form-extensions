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
use Sonatra\Component\FormExtensions\Doctrine\Form\ChoiceList\Factory\TagDecorator;
use Sonatra\Component\FormExtensions\Doctrine\Form\Loader\AjaxDoctrineChoiceLoader;
use Sonatra\Component\FormExtensions\Doctrine\Form\Loader\DynamicDoctrineChoiceLoader;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\ChoiceList\Factory\CachingFactoryDecorator;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Factory\DefaultChoiceListFactory;
use Symfony\Component\Form\ChoiceList\Factory\PropertyAccessDecorator;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
abstract class DoctrineSelect2TypeExtension extends AbstractTypeExtension
{
    /**
     * @var ChoiceListFactoryInterface
     */
    protected $choiceListFactory;

    /**
     * @var AjaxDoctrineChoiceLoader[]
     */
    private $choiceLoaders = array();

    /**
     * Constructor.
     *
     * @param ChoiceListFactoryInterface $choiceListFactory
     */
    public function __construct(ChoiceListFactoryInterface $choiceListFactory = null)
    {
        $this->choiceListFactory = $choiceListFactory ?: new PropertyAccessDecorator(new TagDecorator(new DefaultChoiceListFactory()));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choiceListFactory = $this->choiceListFactory;
        $choiceLoaders = &$this->choiceLoaders;
        $type = $this;

        $choiceLoader = function (Options $options, $value) use ($choiceListFactory, &$choiceLoaders, $type) {
            if (null === $options['choices'] && $options['select2']['enabled']) {
                $hash = null;
                $qbParts = null;

                // If there is no QueryBuilder we can safely cache DoctrineChoiceLoader,
                // also if concrete Type can return important QueryBuilder parts to generate
                // hash key we go for it as well
                if (!$options['query_builder'] || false !== ($qbParts = $type->getQueryBuilderPartsForCachingHash($options['query_builder']))) {
                    $hash = CachingFactoryDecorator::generateHash(array(
                        $options['em'],
                        $options['class'],
                        $qbParts,
                        $options['select2']['ajax'],
                    ));

                    if (isset($choiceLoaders[$hash])) {
                        return $choiceLoaders[$hash];
                    }
                }

                if (null !== $options['query_builder']) {
                    $entityLoader = $type->getLoader($options, $options['query_builder']);
                } else {
                    $queryBuilder = $options['em']->getRepository($options['class'])->createQueryBuilder('e');
                    $entityLoader = $type->getLoader($options, $queryBuilder);
                }

                if ($options['select2']['ajax']) {
                    $doctrineChoiceLoader = new AjaxDoctrineChoiceLoader($entityLoader,
                        $options['choice_value'],
                        $options['id_reader']->getIdField(),
                        null === $options['choice_label_name'] && is_string($options['choice_label']) ? $options['choice_label'] : $options['choice_label_name'],
                        $choiceListFactory
                    );
                } else {
                    $doctrineChoiceLoader = new DynamicDoctrineChoiceLoader($entityLoader,
                        $options['choice_value'],
                        $options['id_reader']->getIdField(),
                        null === $options['choice_label_name'] && is_string($options['choice_label']) ? $options['choice_label'] : $options['choice_label_name'],
                        $choiceListFactory
                    );
                }

                if (null !== $hash) {
                    $choiceLoaders[$hash] = $doctrineChoiceLoader;
                }

                return $doctrineChoiceLoader;
            }

            return $value;
        };

        $choiceName = function (Options $options, $value) {
            return isset($options['id_reader'])
                ? array($options['id_reader'], 'getIdValue')
                : $value;
        };

        $resolver->setDefaults(array(
            'choice_loader' => $choiceLoader,
            'choice_name' => $choiceName,
            'choice_label_name' => null,
        ));

        $resolver->setAllowedTypes('choice_label_name', array('null', 'string'));
    }

    /**
     * Return the default loader object.
     *
     * @param Options $options
     * @param mixed   $queryBuilder
     *
     * @return AjaxEntityLoaderInterface
     */
    abstract public function getLoader(Options $options, $queryBuilder);

    /**
     * Gets important parts from QueryBuilder that will allow to cache its results.
     * For instance in ORM two query builders with an equal SQL string and
     * equal parameters are considered to be equal.
     *
     * @param object $queryBuilder
     *
     * @return array|false Array with important QueryBuilder parts or false if
     *                     they can't be determined
     *
     * @internal This method is public to be usable as callback. It should not
     *           be used in user code
     */
    abstract public function getQueryBuilderPartsForCachingHash($queryBuilder);
}
