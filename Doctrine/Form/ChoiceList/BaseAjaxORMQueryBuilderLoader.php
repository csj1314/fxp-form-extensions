<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Component\FormExtensions\Doctrine\Form\ChoiceList;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
abstract class BaseAjaxORMQueryBuilderLoader implements AjaxEntityLoaderInterface
{
    /**
     * @var AjaxORMFilter
     */
    protected $filter;

    /**
     * @var int|null
     */
    protected $size;

    /**
     * Constructor.
     *
     * @param AjaxORMFilter $filter The ajax filter
     */
    public function __construct(AjaxORMFilter $filter = null)
    {
        $this->filter = $filter ?: new AjaxORMFilter();
        $this->reset();
    }

    /**
     * {@inheritdoc}
     */
    public function setSearch($identifier, $search)
    {
        $qb = $this->getFilterableQueryBuilder();
        $alias = current($qb->getRootAliases());
        $this->filter->filter($qb, $alias, $identifier, $search);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        if (null === $this->size) {
            $paginator = new Paginator($this->getFilterableQueryBuilder());
            $this->prePaginate();
            $this->size = (int) $paginator->count();
            $this->postPaginate();
        }

        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedEntities($pageSize, $pageNumber = 1)
    {
        $pageSize = $pageSize < 1 ? 1 : $pageSize;
        $pageNumber = $pageNumber < 1 ? 1 : $pageNumber;
        $paginator = new Paginator($this->getFilterableQueryBuilder());
        $paginator->getQuery()->setFirstResult(($pageNumber - 1) * $pageSize)
            ->setMaxResults($pageSize);

        $this->prePaginate();
        $result = $paginator->getIterator();
        $this->postPaginate();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities()
    {
        $qb = clone $this->getFilterableQueryBuilder();

        $this->prePaginate();
        $result = $qb->getQuery()->getResult();
        $this->postPaginate();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntitiesByIds($identifier, array $values)
    {
        $qb = clone $this->getQueryBuilder();
        $alias = current($qb->getRootAliases());
        $parameter = 'AjaxORMQueryBuilderLoader_getEntitiesByIds_'.$identifier;
        $where = $qb->expr()->in($alias.'.'.$identifier, ':'.$parameter);

        list($parameterType, $values) = ORMQueryBuilderLoader::cleanValues($qb, $identifier, $values);

        if (empty($values)) {
            return array();
        }

        $this->prePaginate();
        $result = $qb->andWhere($where)
            ->getQuery()
            ->setParameter($parameter, $values, $parameterType)
            ->getResult();
        $this->postPaginate();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->size = null;
    }

    /**
     * Action before the pagination.
     */
    protected function prePaginate()
    {
    }

    /**
     * Action after the pagination.
     */
    protected function postPaginate()
    {
    }

    /**
     * Get the original query builder.
     *
     * @return QueryBuilder
     */
    abstract public function getQueryBuilder();

    /**
     * Get the filterable query builder.
     *
     * @return QueryBuilder
     */
    abstract protected function getFilterableQueryBuilder();
}
