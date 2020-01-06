<?php

namespace Lch\ComponentsBundle\Behavior;

use Doctrine\ORM\QueryBuilder;

/**
 * Trait SearchableEntityRepository
 *
 * @package App\Repository\Traits
 */
trait SearchableEntityRepository
{
    /**
     * Used to search in like mode in entity fields
     * @param array $fields the fields names wanted to be searched in
     * @param string $term the term to be searched
     * @param int|null $maxResults the batch size, if any.
     * @param string|null $language if any, ISO code language to filter items on
     *
     * @return array
     */
    public function findByFulltextTerm(
        array $fields,
        string $term,
        int $maxResults = null,
        string $language = null
    ): array {
        /** @var QueryBuilder $qb */
        $qb = $this->_em->createQueryBuilder();


        $firstField = array_shift($fields);
        $qb
            ->select("entity.$firstField")
            ->from($this->getClassName(), 'entity');

        foreach ($fields as $field) {
            $qb->addSelect("entity.$field");
            $qb->orWhere($qb->expr()->like("entity.$field", ':term'));
        }
        $qb->setParameter('term', "%$term%");

        if(null !== $maxResults) {
            $qb->setMaxResults($maxResults);
        }

        if (null !== $language && ! empty($language)) {
            $qb->andWhere('entity.language = :language');
            $qb->setParameter('language', $language);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $linkedEntities
     * @param string $term
     * @param int|null $maxResults the batch size, if any.
     * @param string|null $language
     *
     * @return array
     */
    public function findByRelationEntity(
        array $linkedEntities,
        string $term,
        int $maxResults = null,
        string $language = null
    ): array {
        /** @var QueryBuilder $qb */
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->select('entity.id')
            ->from($this->getClassName(), 'entity');

        foreach ($linkedEntities as $field => $entityData) {
            $subFieldName = "{$field}.{$entityData['field']}";
            $qb
                ->join("entity.{$field}", $field)
                ->addSelect($subFieldName)
                ->orWhere($qb->expr()->like($subFieldName, ":term"));
        }
        $qb->setParameter('term', "%$term%");

        if(null !== $maxResults) {
            $qb->setMaxResults($maxResults);
        }

        if (null !== $language && ! empty($language)) {
            $qb->andWhere('entity.language = :language');
            $qb->setParameter('language', $language);
        }

        return $qb->getQuery()->getResult();
    }
}
