<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 06/04/17
 * Time: 11:21
 */

namespace Lch\MediaBundle\Event;


use Doctrine\ORM\QueryBuilder;
use Symfony\Contracts\EventDispatcher\Event;

class PreSearchEvent extends Event
{
    /**
     * @var array
     */
    private $mediaType;
    /**
     * @var string $alias
     */
    private $alias;
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;
    /**
     * @var array
     */
    private $parameters;

    public function __construct(array $mediaType, string $alias, QueryBuilder $queryBuilder, array $parameters) {
        $this->mediaType = $mediaType;
        $this->alias = $alias;
        $this->queryBuilder = $queryBuilder;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return array
     */
    public function getMediaType()
    {
        return $this->mediaType;
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
     * @return PreSearchEvent
     */
    public function setParameters(array $parameters): PreSearchEvent
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
     * @return PreSearchEvent
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder): PreSearchEvent
    {
        $this->queryBuilder = $queryBuilder;
        return $this;
    }
}