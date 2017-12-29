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

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AjaxORMQueryBuilderLoader extends BaseAjaxORMQueryBuilderLoader
{
    /**
     * Contains the query builder that builds the query for fetching the
     * entities.
     *
     * This property should only be accessed through query builder.
     *
     * @var QueryBuilder
     */
    private $filterableQueryBuilder;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * Construct an ORM Query Builder Loader.
     *
     * @param QueryBuilder            $query         The query builder for creating the query builder
     * @param AjaxORMFilter           $filter        The ajax filter
     * @param QueryBuilderTransformer $qbTransformer The query builder transformer
     *
     * @throws UnexpectedTypeException
     */
    public function __construct(QueryBuilder $query, AjaxORMFilter $filter = null, QueryBuilderTransformer $qbTransformer = null)
    {
        $this->queryBuilder = $query;

        parent::__construct($filter, $qbTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->filterableQueryBuilder = clone $this->getQueryBuilder();

        parent::reset();
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilterableQueryBuilder()
    {
        return $this->filterableQueryBuilder;
    }
}
