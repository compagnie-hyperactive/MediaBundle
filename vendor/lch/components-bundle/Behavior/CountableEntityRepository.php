<?php

namespace Lch\ComponentsBundle\Behavior;

use Doctrine\ORM\QueryBuilder;

/**
 * Trait CountableEntityRepository
 * @package App\Repository\Traits
 */
trait CountableEntityRepository
{
    /**
     * @return int
     */
    public function getCount(): int
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('entity');
        $qb->select($qb->expr()->count('entity'));

        return $qb->getQuery()->getSingleScalarResult();
    }
}
