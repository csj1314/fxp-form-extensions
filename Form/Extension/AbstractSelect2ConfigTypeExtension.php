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

use Fxp\Component\FormExtensions\Doctrine\Form\ChoiceList\Factory\TagDecorator;
use Fxp\Component\FormExtensions\Form\ChoiceList\Formatter\Select2AjaxChoiceListFormatter;
use Fxp\Component\FormExtensions\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Factory\DefaultChoiceListFactory;
use Symfony\Component\Form\ChoiceList\Factory\PropertyAccessDecorator;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
abstract class AbstractSelect2ConfigTypeExtension extends AbstractTypeExtension
{
    /**
     * @var int
     */
    protected $ajaxPageSize;

    /**
     * @var ChoiceListFactoryInterface
     */
    protected $choiceListFactory;

    /**
     * Constructor.
     *
     * @param int                        $defaultPageSize
     * @param ChoiceListFactoryInterface $choiceListFactory
     */
    public function __construct($defaultPageSize = 10, ChoiceListFactoryInterface $choiceListFactory = null)
    {
        $this->ajaxPageSize = $defaultPageSize;
        $this->choiceListFactory = $choiceListFactory ?: new PropertyAccessDecorator(new TagDecorator(new DefaultChoiceListFactory()));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'select2' => [],
        ]);

        $resolver->setAllowedTypes('select2', 'array');

        $this->addSelect2Normalizer($resolver);
        $this->addChoiceLoaderNormalizer($resolver);
    }

    /**
     * @param OptionsResolver $resolver The options resolver
     */
    protected function addSelect2Normalizer(OptionsResolver $resolver)
    {
        $choiceListFactory = $this->choiceListFactory;
        $ajaxPageSize = $this->ajaxPageSize;

        $resolver->setNormalizer('select2', function (Options $options, $value) use ($choiceListFactory, $ajaxPageSize) {
            $select2Resolver = new OptionsResolver();

            $select2Resolver->setDefaults([
                'enabled' => false,
                'wrapper_attr' => [],
                'width' => '100%',
                'template_result' => null,
                'template_selection' => null,
                'dropdown_parent' => null,
                'selection_adapter' => null,
                'data_adapter' => null,
                'dropdown_adapter' => null,
                'results_adapter' => null,
                'min_input_length' => null,
                'max_input_length' => null,
                'min_results_for_search' => null,
                'max_selection_length' => null,
                'close_on_select' => null,
                'token_separators' => [','],
                'create_tag' => null,
                'matcher' => null,
                'data' => null,
                'dir' => null,
                'theme' => null,
                'language' => \Locale::getDefault(),
                'allow_clear' => null,
                'tags' => false,
                'ajax' => false,
                'ajax_formatter' => new Select2AjaxChoiceListFormatter($choiceListFactory),
                'ajax_parameters' => [],
                'ajax_reference_type' => (bool) RouterInterface::ABSOLUTE_PATH,
                'ajax_data_type' => 'json',
                'ajax_delay' => 250,
                'ajax_cache' => false,
                'ajax_data' => null,
                'ajax_process_results' => null,
                'ajax_transport' => null,
                'ajax_route' => null,
                'ajax_url' => null,
                'ajax_page_size' => $ajaxPageSize,
            ]);

            $select2Resolver->setAllowedTypes('enabled', 'bool');
            $select2Resolver->setAllowedTypes('wrapper_attr', 'array');
            $select2Resolver->setAllowedTypes('template_result', ['null', 'string']);
            $select2Resolver->setAllowedTypes('template_selection', ['null', 'string']);
            $select2Resolver->setAllowedTypes('dropdown_parent', ['null', 'string']);
            $select2Resolver->setAllowedTypes('selection_adapter', ['null', 'string']);
            $select2Resolver->setAllowedTypes('data_adapter', ['null', 'string']);
            $select2Resolver->setAllowedTypes('dropdown_adapter', ['null', 'string']);
            $select2Resolver->setAllowedTypes('results_adapter', ['null', 'string']);
            $select2Resolver->setAllowedTypes('matcher', ['null', 'string']);
            $select2Resolver->setAllowedTypes('create_tag', ['null', 'string']);
            $select2Resolver->setAllowedTypes('min_input_length', ['null', 'int']);
            $select2Resolver->setAllowedTypes('max_input_length', ['null', 'int']);
            $select2Resolver->setAllowedTypes('min_results_for_search', ['null', 'int', 'string']);
            $select2Resolver->setAllowedTypes('max_selection_length', ['null', 'int', 'string']);
            $select2Resolver->setAllowedTypes('close_on_select', ['null', 'bool']);
            $select2Resolver->setAllowedTypes('token_separators', ['null', 'array']);
            $select2Resolver->setAllowedTypes('data', ['null', 'array']);
            $select2Resolver->setAllowedValues('dir', [null, 'ltr', 'rtl']);
            $select2Resolver->setAllowedTypes('width', ['null', 'string']);
            $select2Resolver->setAllowedTypes('theme', ['null', 'string']);
            $select2Resolver->setAllowedTypes('language', 'string');
            $select2Resolver->setAllowedTypes('allow_clear', ['null', 'bool']);
            $select2Resolver->setAllowedTypes('tags', 'bool');
            $select2Resolver->setAllowedTypes('ajax', 'bool');
            $select2Resolver->setAllowedTypes('ajax_formatter', 'Fxp\Component\FormExtensions\Form\ChoiceList\Formatter\AjaxChoiceListFormatterInterface');
            $select2Resolver->setAllowedTypes('ajax_parameters', 'array');
            $select2Resolver->setAllowedTypes('ajax_reference_type', 'bool');
            $select2Resolver->setAllowedTypes('ajax_data_type', ['null', 'string']);
            $select2Resolver->setAllowedTypes('ajax_delay', ['null', 'int']);
            $select2Resolver->setAllowedTypes('ajax_cache', 'bool');
            $select2Resolver->setAllowedTypes('ajax_data', ['null', 'string']);
            $select2Resolver->setAllowedTypes('ajax_process_results', ['null', 'string']);
            $select2Resolver->setAllowedTypes('ajax_transport', ['null', 'string']);
            $select2Resolver->setAllowedTypes('ajax_route', ['null', 'string']);
            $select2Resolver->setAllowedTypes('ajax_url', ['null', 'string', 'Closure']);
            $select2Resolver->setAllowedTypes('ajax_page_size', 'int');

            $select2Resolver->setNormalizer('ajax_url', function (Options $options, $value) {
                return $value instanceof \Closure ? $value($options, $value) : $value;
            });

            return $select2Resolver->resolve($value);
        });
    }

    /**
     * @param OptionsResolver $resolver The options resolver
     */
    protected function addChoiceLoaderNormalizer(OptionsResolver $resolver)
    {
        $choiceListFactory = $this->choiceListFactory;

        if ($resolver->isDefined('choice_loader')) {
            $resolver->addAllowedTypes('choice_loader', \Closure::class);

            $resolver->setNormalizer('choice_loader', function (Options $options, $value) use ($choiceListFactory) {
                $value = $value instanceof \Closure ? $value($options, $value) : $value;

                if ($options['select2']['enabled']) {
                    $value = Select2Util::convertToDynamicLoader($choiceListFactory, $options, $value);
                    $value->setAllowAdd($options['select2']['tags']);

                    if ($value instanceof AjaxChoiceLoaderInterface) {
                        $value->setPageSize($options['select2']['ajax_page_size']);
                        $value->setPageNumber(1);
                        $value->setSearch('');
                        $value->setIds([]);
                        $value->reset();
                    }
                }

                return $value;
            });
        }
    }
}
