<?php

namespace App\Repository;

use App\Entity\Medicamentos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Medicamentos>
 *
 * @method Medicamentos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Medicamentos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Medicamentos[]    findAll()
 * @method Medicamentos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicamentosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medicamentos::class);
    }

//    /**
//     * @return Medicamentos[] Returns an array of Medicamentos objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Medicamentos
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
