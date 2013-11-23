<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Doctrine\Form\ChoiceList;

use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class AjaxORMQueryBuilderLoader extends ORMQueryBuilderLoader
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilderBak;

    /**
     * Construct an ORM Query Builder Loader
     *
     * @param QueryBuilder|\Closure $queryBuilder
     * @param EntityManager         $manager
     * @param string                $class
     *
     * @throws UnexpectedTypeException
     */
    public function __construct($queryBuilder, $manager = null, $class = null)
    {
        $this->queryBuilderBak = $queryBuilder;

        parent::__construct(clone $queryBuilder, $manager, $class);
    }

    /**
     * Get query builder.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        $ref = new \ReflectionClass($this);
        $parent = $ref->getParentClass();
        $prop = $parent->getProperty('queryBuilder');
        $prop->setAccessible(true);

        return $prop->getValue($this);
    }

    /**
     * Restaure the query builder.
     */
    public function reset()
    {
        $ref = new \ReflectionClass($this);
        $parent = $ref->getParentClass();
        $prop = $parent->getProperty('queryBuilder');
        $prop->setAccessible(true);
        $prop->setValue($this, clone $this->queryBuilderBak);
    }
}