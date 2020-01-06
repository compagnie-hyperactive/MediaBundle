<?php

namespace Lch\TranslateBundle\Event;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class QueryingTranslatedParentEvent
 * @package Lch\TranslateBundle\Event
 */
class QueryingTranslatedParentEvent extends Event
{
    public const NAME = 'translated_parent.querying';

    /** @var QueryBuilder $queryBuilder */
    protected $queryBuilder;

    /**
     * QueryingTranslatedParentEvent constructor.
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }
}
