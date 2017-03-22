<?php

declare(strict_types=1);

/*
 * This file is part of CSBill project.
 *
 * (c) 2013-2016 Pierre du Plessis <info@customscripts.co.za>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CSBill\ClientBundle\Repository;

use CSBill\CoreBundle\Util\ArrayUtil;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * ClientRepository.
 *
 * Custom Repository class for managing clients
 */
class ClientRepository extends EntityRepository
{
    /**
     * Gets total number of clients.
     *
     * @param string $status
     *
     * @return int
     */
    public function getTotalClients(string $status = null): int
    {
        $qb = $this->createQueryBuilder('c');

        $qb->select('COUNT(c.id)');

        if (null !== $status) {
            $qb->where('c.status = :status')
                ->setParameter('status', $status);
        }

        $query = $qb->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    /**
     * Gets the most recent created clients.
     *
     * @param int $limit
     *
     * @return array
     */
    public function getRecentClients($limit = 5): array
    {
        $qb = $this->createQueryBuilder('c');

        $qb->select(
            [
                'c.id',
                'c.name',
                'c.created',
                'c.status',
            ]
        )
            ->orderBy('c.created', 'DESC')
            ->setMaxResults($limit);

        $query = $qb->getQuery();

        return $query->getArrayResult();
    }

    /**
     * @return array
     */
    public function getStatusList(): array
    {
        $qb = $this->createQueryBuilder('c');

        $qb->select('DISTINCT c.status');

        return ArrayUtil::column($qb->getQuery()->getResult(), 'status');
    }

    /**
     * @return QueryBuilder
     */
    public function getGridQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');

        $qb->select('c');

        return $qb;
    }

    /**
     * @return QueryBuilder
     */
    public function getArchivedGridQuery(): QueryBuilder
    {
        $this->getEntityManager()->getFilters()->disable('archivable');

        $qb = $this->createQueryBuilder('c');

        $qb->select('c');

        $qb->where('c.archived is not null');

        return $qb;
    }
}
