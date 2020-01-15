<?php

namespace App\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\SubCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    const ITEMS_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * find all products for the sub-category in parameter
     * @param SubCategory $subCategory
     * @param int $page
     * @return Paginator
     * @throws QueryException
     */
    public function findProductsBySubCategory(SubCategory $subCategory, int $page)
    {
        $firstResult = ($page -1) * self::ITEMS_PER_PAGE;

        $qb = $this->createQueryBuilder('product');

        $query = $qb
            ->where('product.subCategory = :subCat')
            ->setParameter('subCat', $subCategory);

        $criteria = Criteria::create()
            ->setFirstResult($firstResult)
            ->setMaxResults(self::ITEMS_PER_PAGE);

        try {
            $query->addCriteria($criteria);
        } catch (QueryException $e) {
            throw $e;
        }

        $doctrinePaginator = new DoctrinePaginator($query);
        $paginator = new Paginator($doctrinePaginator);

        return $paginator;
    }

    /**
     * find all products for the category in parameter
     * @param Category $category
     * @param int $page
     * @return Paginator
     * @throws QueryException
     */
    public function findProductsByCategory(Category $category, int $page)
    {

        $firstResult = ($page -1) * self::ITEMS_PER_PAGE;

        $qb = $this->createQueryBuilder('product');

        $query = $qb
            ->join('product.subCategory', 'subCategory')
            ->where('subCategory.category = :category')
            ->setParameter('category', $category);

        $criteria = Criteria::create()
            ->setFirstResult($firstResult)
            ->setMaxResults(self::ITEMS_PER_PAGE);

        try {
            $query->addCriteria($criteria);
        } catch (QueryException $e) {
            throw $e;
        }

        $doctrinePaginator = new DoctrinePaginator($query);
        $paginator = new Paginator($doctrinePaginator);

        return $paginator;
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
