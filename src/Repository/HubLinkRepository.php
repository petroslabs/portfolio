<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\HubLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HubLink>
 */
final class HubLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HubLink::class);
    }

    /**
     * @return list<HubLink>
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
