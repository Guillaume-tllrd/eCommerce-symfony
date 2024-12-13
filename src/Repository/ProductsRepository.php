<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    public function findProductsPaginated(int $page, int $id, int $limit = 6): array
    {
        // abs pour return une valeur positive
        $limit = abs($limit);

        $result = [];

        // ppour la requete: on sélectionne tout ce qu'il ya dans catégories et products de la table products jointe à la table catégories en fonction du slug de catégorie
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('c', 'p')
            ->from('App\Entity\Products', 'p')
            ->join('p.categories', 'c')
            ->where("c.id = '$id'")
            ->setMaxResults($limit)
            ->setFirstResult(($page * $limit) - $limit);


        $paginator = new Paginator($query);
        // on récuprèe les données:
        $data = $paginator->getQuery()->getResult();

        // on vérifie qu'on a des donées:
        if (empty($data)) {
            return $result;
        }

        // on calcul le nombre de pages:
        $pages = ceil($paginator->count() / $limit);

        // Une fois qu'on a le nombre de pages, on remplit le tableau.
        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
    }
    //    /**
    //     * @return Products[] Returns an array of Products objects
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

    //    public function findOneBySomeField($value): ?Products
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
