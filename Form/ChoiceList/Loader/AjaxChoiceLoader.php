<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Form\ChoiceList\Loader;

use Fxp\Component\FormExtensions\Form\ChoiceList\Loader\Traits\AjaxLoaderTrait;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AjaxChoiceLoader extends DynamicChoiceLoader implements AjaxChoiceLoaderInterface
{
    use AjaxLoaderTrait;

    /**
     * @var array
     */
    protected $filteredChoices;

    /**
     * Creates a new choice loader.
     *
     * @param array                           $choices The choices
     * @param ChoiceListFactoryInterface|null $factory The factory for creating
     *                                                 the loaded choice list
     */
    public function __construct(array $choices, $factory = null)
    {
        parent::__construct($choices, $factory);

        $this->allChoices = false;
        $this->initAjax();
        $this->reset();
    }

    /**
     * {@inheritdoc}
     */
    public function loadPaginatedChoiceList($value = null)
    {
        $choices = LoaderUtil::paginateChoices($this, $this->filteredChoices);

        return $this->factory->createListFromChoices($choices, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        if (null === $this->search || '' === $this->search) {
            $filteredChoices = $this->choices;
        } else {
            $filteredChoices = $this->resetSearchChoices();
        }

        $this->initialize($filteredChoices);

        return $this;
    }

    /**
     * Reset the choices for search.
     *
     * @return array The filtered choices
     */
    protected function resetSearchChoices()
    {
        $filteredChoices = [];

        foreach ($this->choices as $key => $choice) {
            if (\is_array($choice)) {
                $this->resetSearchGroupChoices($filteredChoices, $key, $choice);
            } else {
                $this->resetSearchSimpleChoices($filteredChoices, $key, $choice);
            }
        }

        return $filteredChoices;
    }

    /**
     * Reset the search group choices.
     *
     * @param array  $filteredChoices The filtered choices
     * @param string $group           The group name
     * @param array  $choices         The choices
     */
    protected function resetSearchGroupChoices(array &$filteredChoices, $group, array $choices)
    {
        foreach ($choices as $key => $choice) {
            list($id, $label) = $this->getIdAndLabel($key, $choice);

            if (false !== stripos($label, $this->search) && !\in_array($id, $this->getIds())) {
                if (!array_key_exists($group, $filteredChoices)) {
                    $filteredChoices[$group] = [];
                }

                $filteredChoices[$group][$key] = $choice;
            }
        }
    }

    /**
     * Reset the search simple choices.
     *
     * @param array  $filteredChoices The filtered choices
     * @param string $key             The key
     * @param string $choice          The choice
     */
    protected function resetSearchSimpleChoices(array &$filteredChoices, $key, $choice)
    {
        list($id, $label) = $this->getIdAndLabel($key, $choice);

        if (false !== stripos($label, $this->search) && !\in_array($id, $this->getIds())) {
            $filteredChoices[$key] = $choice;
        }
    }

    /**
     * Get the id and label of original choices.
     *
     * @param string $key   The key of array
     * @param string $value The value of array
     *
     * @return array The id and label
     */
    protected function getIdAndLabel($key, $value)
    {
        return [$value, $key];
    }

    /**
     * @param array $choices The choices
     */
    protected function initialize($choices)
    {
        parent::initialize($choices);

        $this->filteredChoices = $choices;
        $this->choiceList = null;
    }

    /**
     * {@inheritdoc}
     */
    protected function getChoicesForChoiceList()
    {
        return $this->filteredChoices;
    }
}
