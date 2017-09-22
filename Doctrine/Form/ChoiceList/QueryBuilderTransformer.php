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

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class QueryBuilderTransformer
{
    /**
     * Get the query from the query builder with transformation.
     *
     * @param QueryBuilder $qb The query builder
     *
     * @return Query
     */
    public function getQuery(QueryBuilder $qb)
    {
        return $qb->getQuery();
    }
}
