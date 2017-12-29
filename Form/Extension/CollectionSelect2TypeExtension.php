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

use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class CollectionSelect2TypeExtension extends AbstractSelect2ConfigTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return CollectionType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['select2']['enabled']) {
            return;
        }

        try {
            $selector = $builder->getFormFactory()->createBuilder($options['entry_type'], null, array_merge(
                $this->normalizeOptions($options, $options['entry_options']), array(
                    'multiple' => true,
                ))
            );
            $builder->setAttribute('selector', $selector);
            $builder->setAttribute('choice_loader', $selector->getOption('choice_loader'));
        } catch (UndefinedOptionsException $e) {
            $msg = 'The "%s" type is not an "choice" with Select2 extension, because: %s';
            throw new InvalidConfigurationException(sprintf($msg, $options['entry_type'], lcfirst($e->getMessage())), 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        if (!$options['select2']['enabled']) {
            return;
        }

        /* @var FormBuilderInterface $selectorBuilder */
        $selectorBuilder = $form->getConfig()->getAttribute('selector');
        $selectorBuilder->setData($form->getData());
        $selector = $selectorBuilder->getForm();
        $selectorView = $selector->createView($view);

        $selectorView->vars = array_replace($selectorView->vars, array(
            'id' => $view->vars['id'],
            'full_name' => $view->vars['full_name'].'[]',
        ));

        $view->vars = array_replace($view->vars, array(
            'selector' => $selectorView,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'entry_type' => function (Options $options, $value) {
                return $options['select2']['enabled'] ? ChoiceType::class : $value;
            },
            'allow_add' => function (Options $options, $value) {
                return $options['select2']['enabled'] ? true : $value;
            },
            'allow_delete' => function (Options $options, $value) {
                return $options['select2']['enabled'] ? true : $value;
            },
            'prototype' => function (Options $options, $value) {
                return $options['select2']['enabled'] ? false : $value;
            },
        ));

        $resolver->setNormalizer('prototype', function (Options $options, $value) {
            return $options['select2']['enabled'] ? false : $value;
        });

        $resolver->setNormalizer('entry_options', function (Options $options, $value) {
            if ($options['select2']['enabled']) {
                $value = array_merge($value, array(
                    'select2' => array_merge($options['select2'], array(
                        'tags' => $options['allow_add'],
                    )),
                ));
            }

            return $value;
        });
    }

    /**
     * Normalise the options for selector.
     *
     * @param array $options The form options
     * @param array $value   The options of form type
     *
     * @return array The normalized options for selector
     */
    protected function normalizeOptions(array $options, array $value)
    {
        return $options['select2']['enabled']
            ? array_merge($value, array(
                'error_bubbling' => false,
                'multiple' => false,
                'select2' => array_merge($options['select2'], array(
                    'tags' => $options['allow_add'],
                )),
            ))
            : $value;
    }
}
