<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BlogPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlogPost>
 */
final class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

    /**
     * @return list<BlogPost>
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('p')
            // id en départage : deux articles publiés le même jour doivent
            // rester dans l'ordre de création, pas dans un ordre arbitraire.
            ->orderBy('p.date', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<BlogPost>
     */
    public function findByProjectSlug(string $projectSlug): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.project', 'project')
            ->where('project.slug = :slug')
            ->setParameter('slug', $projectSlug)
            ->orderBy('p.date', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
