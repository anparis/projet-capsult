<?php

namespace App\Repository;

use App\Entity\Bloc;
use App\Entity\User;
use App\Entity\Capsule;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Capsule>
 *
 * @method Capsule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Capsule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Capsule[]    findAll()
 * @method Capsule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CapsuleRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Capsule::class);
  }

  public function save(Capsule $entity, bool $flush = false): void
  {
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function remove(Capsule $entity, bool $flush = false): void
  {
    $this->getEntityManager()->remove($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  /* Custom DQL query
  Get all Capsules that are private */
  public function findSealedCapsules()
  {
    return $this->createQueryBuilder('c')
      ->andWhere('c.open = 0')
      ->orderBy('c.updated_at', 'DESC')
      ->getQuery()
      ->getResult();
  }

  /* Custom DQL query
  Get all Capsules that are public */
  public function findPublicCapsules()
  {
    return $this->createQueryBuilder('c')
      ->andWhere('c.open = 1')
      ->orderBy('c.updated_at', 'DESC')
      ->getQuery()
      ->getResult();
  }

  /* Custom DQL query
  Allows to get all Capsules that are explorable */
  public function findExplorableCapsules()
  {
    return $this->createQueryBuilder('c')
      ->andWhere('c.explore = 1')
      ->andWhere('c.open = 1')
      ->orderBy('c.updated_at', 'DESC')
      ->getQuery()
      //getResult gives back an array of Capsules
      ->getResult();
  }

  /* Custom DQL query
  Find all capsules from a user that are not connected to current bloc */
  public function getNonConnectedCapsules(Bloc $bloc, User $user)
  {
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
      'SELECT c
      FROM App\Entity\User u
      INNER JOIN App\Entity\Capsule c WITH c.user = u
      LEFT JOIN App\Entity\Connection conn WITH conn.capsule = c AND conn.bloc = :bloc
      WHERE u.id = :user AND conn.capsule IS NULL
      ORDER BY c.updated_at
      '
    )->setParameters(['user' => $user, 'bloc' => $bloc]);

    return $query->getResult();
  }

  //    /**
  //     * @return Capsule[] Returns an array of Capsule objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('c')
  //            ->andWhere('c.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('c.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?Capsule
  //    {
  //        return $this->createQueryBuilder('c')
  //            ->andWhere('c.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
