<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Doctrine\Form\Loader;

use Fxp\Component\FormExtensions\Form\ChoiceList\Loader\AbstractDynamicChoiceLoader;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class DynamicDoctrineChoiceLoader extends AbstractDynamicChoiceLoader
{
    /**
     * @var EntityLoaderInterface
     */
    protected $objectLoader;

    /**
     * @var callable
     */
    protected $choiceValue;

    /**
     * @var string
     */
    protected $idField;

    /**
     * Creates a new choice loader.
     *
     * @param EntityLoaderInterface             $objectLoader The objects loader
     * @param callable                          $choiceValue  The callable choice value
     * @param string                            $idField      The id field
     * @param null|callable|string|PropertyPath $label        The callable or path generating the choice labels
     * @param ChoiceListFactoryInterface|null   $factory      The factory for creating
     *                                                        the loaded choice list
     */
    public function __construct(EntityLoaderInterface $objectLoader, $choiceValue, $idField, $label, $factory = null)
    {
        parent::__construct($factory);

        $this->objectLoader = $objectLoader;
        $this->choiceValue = $choiceValue;
        $this->idField = $idField;
        $this->label = $label;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return \count($this->objectLoader->getEntities());
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceListForView(array $values, $value = null)
    {
        $list = $this->loadEntities();

        if ($this->isAllowAdd()) {
            $choices = $this->loadChoicesForValues($this->getRealValues($values, $value), $value);

            foreach ($choices as $choice) {
                if (\is_string($choice)) {
                    $list[] = $choice;
                }
            }
        }

        return $this->factory->createListFromChoices($list, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceList($value = null)
    {
        if ($this->choiceList) {
            return $this->choiceList;
        }

        $this->choiceList = $this->factory->createListFromChoices($this->loadEntities(), $value);

        return $this->choiceList;
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        // Performance optimization
        if (empty($values)) {
            return [];
        }

        $value = $this->getCallableValue($value);
        $unorderedObjects = $this->objectLoader->getEntitiesByIds($this->idField, $values);
        $objectsById = [];
        $objects = [];

        foreach ($unorderedObjects as $object) {
            $objectsById[\call_user_func($value, $object)] = $object;
        }

        foreach ($values as $i => $id) {
            if (isset($objectsById[$id])) {
                $objects[$i] = $objectsById[$id];
            } elseif ($this->isAllowAdd()) {
                $objects[$i] = $id;
            }
        }

        return $objects;
    }

    /**
     * {@inheritdoc}
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        // Performance optimization
        if (empty($choices)) {
            return [];
        }

        $value = $this->getCallableValue($value);
        $values = [];

        foreach ($choices as $i => $object) {
            if (\is_object($object)) {
                try {
                    $values[$i] = (string) \call_user_func($value, $object);
                } catch (RuntimeException $e) {
                    if (!$this->isAllowAdd()) {
                        throw $e;
                    }
                }
            } elseif ($this->isAllowAdd()) {
                $values[$i] = $object;
            }
        }

        return $values;
    }

    /**
     * Load the entities.
     *
     * @return object[]
     */
    protected function loadEntities()
    {
        return $this->objectLoader->getEntities();
    }

    /**
     * Get the choice names of values.
     *
     * @param array         $values The selected values
     * @param null|callable $value  The callable which generates the values
     *                              from choices
     *
     * @return array
     */
    protected function getRealValues(array $values, $value = null)
    {
        $value = $this->getCallableValue($value);

        foreach ($values as &$val) {
            if (\is_object($val) && \is_callable($value)) {
                $val = \call_user_func($value, $val);
            }
        }

        return $values;
    }

    /**
     * Get the callable which generates the values from choices.
     *
     * @param null|callable $value The callable which generates the values
     *                             from choices
     *
     * @return callable
     */
    protected function getCallableValue($value = null)
    {
        return null === $value
            ? $this->choiceValue
            : $value;
    }
}
