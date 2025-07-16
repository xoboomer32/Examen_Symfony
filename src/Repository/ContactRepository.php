<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contact>
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    /**
     * Recherche et tri des contacts par nom
     * @param string|null $search
     * @param string $sort 'ASC' ou 'DESC'
     * @return Contact[]
     */
    public function findByNomSearchAndSort(?string $search, string $sort = 'ASC'): array
    {
        $qb = $this->createQueryBuilder('c');
        if ($search) {
            $qb->andWhere('LOWER(c.contact) LIKE :search OR LOWER(c.nom) LIKE :search OR LOWER(c.numero) LIKE :search OR LOWER(c.email) LIKE :search')
               ->setParameter('search', '%'.mb_strtolower($search).'%');
        }
        $qb->orderBy('c.nom', $sort);
        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Contact[] Returns an array of Contact objects
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

//    public function findOneBySomeField($value): ?Contact
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
