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

use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
abstract class BaseAjaxORMQueryBuilderLoader implements AjaxEntityLoaderInterface
{
    /**
     * @var int|null
     */
    protected $size;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * {@inheritdoc}
     */
    public function setSearch($identifier, $search)
    {
        $qb = $this->getFilterableQueryBuilder();
        $alias = current($qb->getRootAliases());
        $qb->andWhere("LOWER({$alias}.{$identifier}) LIKE LOWER(:{$identifier})");
        $qb->setParameter($identifier, "%{$search}%");
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        if (null === $this->size) {
            $paginator = new Paginator($this->getFilterableQueryBuilder());
            $this->size = (int) $paginator->count();
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

        return $paginator->getIterator();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities()
    {
        $qb = clone $this->getFilterableQueryBuilder();

        return $qb->getQuery()->getResult();
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

        // Guess type
        $entity = current($qb->getRootEntities());
        $metadata = $qb->getEntityManager()->getClassMetadata($entity);

        if (in_array($metadata->getTypeOfField($identifier), array('integer', 'bigint', 'smallint'))) {
            $parameterType = Connection::PARAM_INT_ARRAY;

            // Filter out non-integer values (e.g. ""). If we don't, some
            // databases such as PostgreSQL fail.
            $values = array_values(array_filter($values, function ($v) {
                return (string) $v === (string) (int) $v;
            }));
        } elseif ('guid' === $metadata->getTypeOfField($identifier)) {
            $parameterType = Connection::PARAM_STR_ARRAY;

            // Like above, but we just filter out empty strings.
            $values = array_values(array_filter($values, function ($v) {
                return (string) $v !== '';
            }));
        } else {
            $parameterType = Connection::PARAM_STR_ARRAY;
        }

        return !$values
            ? array()
            : $qb->andWhere($where)
                ->getQuery()
                ->setParameter($parameter, $values, $parameterType)
                ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->size = null;
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
