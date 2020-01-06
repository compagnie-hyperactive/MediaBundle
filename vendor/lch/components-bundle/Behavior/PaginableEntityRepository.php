<?php

namespace Lch\ComponentsBundle\Behavior;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Trait PaginableEntityRepository
 *
 * @package App\Repository\Traits
 */
trait PaginableEntityRepository
{

    /**
     * @param int $page
     * @param int|null $maxResults
     */
    protected function checkBoundariesValues(int $page = 1, int $maxResults = null): void
    {
        if ($page < 1) {
            throw new \UnexpectedValueException(
                'Requested page must be positive integer.'
            );
        }

        if ($maxResults && $maxResults < 1) {
            throw new \UnexpectedValueException(
                'Max results must be positive integer.'
            );
        }
    }

    /**
     * @param QueryBuilder $qb the QueryBuilder object containing the query description
     * @param int $page the page wanted (set to 1 by default)
     * @param int|null $maxResults the batch size, if any
     *
     * @return Paginator
     */
    public function getPaginator(QueryBuilder $qb, int $page = 1, int $maxResults = null): Paginator
    {
        // Calculating offset
        $firstResult = ($page - 1) * ($maxResults ?: 1);
        $qb->setFirstResult($firstResult);

        // Limiting query
        if ($maxResults) {
            $qb->setMaxResults($maxResults);
        }

        // Returning paginator
        $paginator = new Paginator($qb);
        if (($paginator->count() <= $firstResult) && $page !== 1) {
            throw new NotFoundHttpException('Requested page does not exist.');
        }

        return $paginator;
    }
}
