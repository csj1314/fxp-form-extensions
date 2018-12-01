<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Doctrine\Form\ChoiceList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class ORMQueryBuilderLoader implements EntityLoaderInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var QueryBuilderTransformer
     */
    private $qbTransformer;

    /**
     * Constructor.
     *
     * @param QueryBuilder $queryBuilder The query builder for creating the query builder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->setQueryBuilderTransformer(new QueryBuilderTransformer());
    }

    /**
     * Get the query builder.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities()
    {
        $this->preLoad();
        $result = $this->qbTransformer->getQuery($this->queryBuilder)->execute();
        $this->postLoad();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntitiesByIds($identifier, array $values)
    {
        $qb = clone $this->queryBuilder;
        $alias = current($qb->getRootAliases());
        $parameter = 'ORMQueryBuilderLoader_getEntitiesByIds_'.$identifier;
        $parameter = str_replace('.', '_', $parameter);
        $where = $qb->expr()->in($alias.'.'.$identifier, ':'.$parameter);

        list($parameterType, $values) = static::cleanValues($qb, $identifier, $values);

        if (!$values) {
            return [];
        }

        $this->preLoad();
        $result = $this->qbTransformer->getQuery($qb->andWhere($where))
            ->setParameter($parameter, $values, $parameterType)
            ->getResult();
        $this->postLoad();

        return $result;
    }

    /**
     * Cleaned the values and get the parameter type.
     *
     * @param QueryBuilder $qb         The query builder
     * @param string       $identifier The identifier field of the object. This method
     *                                 is not applicable for fields with multiple
     *                                 identifiers.
     * @param array        $values     The values of the identifiers
     *
     * @return array The parameter type and the cleaned values
     */
    public static function cleanValues(QueryBuilder $qb, $identifier, array $values)
    {
        // Guess type
        $entity = current($qb->getRootEntities());
        $metadata = $qb->getEntityManager()->getClassMetadata($entity);

        if (\in_array($metadata->getTypeOfField($identifier), ['integer', 'bigint', 'smallint'])) {
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

                return !empty($guid) && '00000000-0000-0000-0000-000000000000' !== $guid;
            }));
        } else {
            $parameterType = Connection::PARAM_STR_ARRAY;
        }

        return [$parameterType, $values];
    }

    /**
     * Set the query builder transformer.
     *
     * @param QueryBuilderTransformer $qbTransformer The query builder transformer
     */
    public function setQueryBuilderTransformer(QueryBuilderTransformer $qbTransformer)
    {
        $this->qbTransformer = $qbTransformer;
    }

    /**
     * Action before the loading.
     */
    protected function preLoad()
    {
    }

    /**
     * Action after the loading.
     */
    protected function postLoad()
    {
    }
}
