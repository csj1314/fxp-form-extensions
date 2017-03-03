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
use Doctrine\DBAL\Types\Type;
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
            $type = Type::getType(Type::GUID);
            $platform = $qb->getEntityManager()->getConnection()->getDatabasePlatform();

            // Like above, but we just filter out empty strings and invalid guid.
            $values = array_values(array_filter($values, function ($v) use ($type, $platform) {
                $guid = $type->convertToDatabaseValue($v, $platform);

                return !empty($guid) && $guid !== '00000000-0000-0000-0000-000000000000';
            }));
        } else {
            $parameterType = Connection::PARAM_STR_ARRAY;
        }

        if (!$values) {
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
