<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 06/04/17
 * Time: 11:21
 */

namespace Lch\MediaBundle\Event;


use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\Event;

class PostSearchEvent extends Event
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var array $parameters
     */
    private $parameters;

    public function __construct(QueryBuilder $queryBuilder, array $parameters) {
        $this->queryBuilder = $queryBuilder;
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return PostSearchEvent
     */
    public function setParameters(array $parameters): PostSearchEvent
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return PostSearchEvent
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder): PostSearchEvent
    {
        $this->queryBuilder = $queryBuilder;
        return $this;
    }
}