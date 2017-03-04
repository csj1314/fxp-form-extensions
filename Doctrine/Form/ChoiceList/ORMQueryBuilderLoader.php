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
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class ORMQueryBuilderLoader implements EntityLoaderInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * Constructor.
     *
     * @param QueryBuilder $queryBuilder The query builder for creating the query builder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities()
    {
        return $this->queryBuilder->getQuery()->execute();
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
            return array();
        }

        return $qb->andWhere($where)
            ->getQuery()
            ->setParameter($parameter, $values, $parameterType)
            ->getResult();
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

        return array($parameterType, $values);
    }
}
