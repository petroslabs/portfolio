<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Profile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Profile>
 */
final class ProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profile::class);
    }

    /**
     * Le profil est un singleton : une seule ligne, provisionnée par migration.
     */
    public function getSingleton(): Profile
    {
        $profile = $this->createQueryBuilder('p')->getQuery()->getOneOrNullResult();

        if (!$profile instanceof Profile) {
            throw new \RuntimeException('Aucun profil trouvé — la migration a-t-elle bien été jouée ?');
        }

        return $profile;
    }
}
