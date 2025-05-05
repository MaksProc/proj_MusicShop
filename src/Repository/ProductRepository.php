<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Enum\ProductType;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[] Returns array of Products with similar name
     */
    public function findByNameLike(string $searchTerm): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->orderBy('p.name','ASC')
            ->getQuery()
            ->getResult();
    }

    public function filterProducts(?string $searchTerm, ?float $minPrice, ?float $maxPrice, ?string $type): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($searchTerm) {
            $qb->andWhere('p.name LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%');
        }

        if ($minPrice !== null) {
            $qb->andWhere('p.base_price >= :minPrice')
            ->setParameter('minPrice', $minPrice);
        }

        if ($maxPrice !== null) {
            $qb->andWhere('p.base_price <= :maxPrice')
            ->setParameter('maxPrice', $maxPrice);
        }

        if ($type === 'buy') {
            $qb->andWhere('p.type IN (:types)')
               ->setParameter('types', [ProductType::BUY, ProductType::BOTH]);
        } elseif ($type === 'rent') {
            $qb->andWhere('p.type IN (:types)')
               ->setParameter('types', [ProductType::RENT, ProductType::BOTH]);
        } elseif ($type === 'both') {
            $qb->andWhere('p.type IN (:types)')
                ->setParameter('types', [ProductType::BOTH]);
        }

        return $qb->getQuery()->getResult();
    }


//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
