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
    const NEW_PRODUCTS_COUNT = 5;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * find all products for the sub-category in parameter
     * @param SubCategory $subCategory
     * @param int $page
     * @param $displayAll
     * @return Paginator
     * @throws QueryException
     */
    public function findProductsBySubCategory(SubCategory $subCategory, int $page, $displayAll)
    {
        $firstResult = ($page -1) * self::ITEMS_PER_PAGE;

        $qb = $this->createQueryBuilder('product');

        $query = $qb
            ->where('product.subCategory = :subCat')
            ->andWhere('product.isOnline = true')
            ->orderBy('product.createdAt', 'DESC')
            ->setParameter('subCat', $subCategory);

        if ($displayAll === null || $displayAll === "false") {
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

        return $query->getQuery()->getResult();
    }

    /**
     * find all products for the category in parameter
     * @param Category $category
     * @param int $page
     * @param $displayAll
     * @return Paginator
     * @throws QueryException
     */
    public function findProductsByCategory(Category $category, int $page, $displayAll)
    {

        $firstResult = ($page -1) * self::ITEMS_PER_PAGE;

        $qb = $this->createQueryBuilder('product');

        $query = $qb
            ->join('product.subCategory', 'subCategory')
            ->where('subCategory.category = :category')
            ->andWhere('product.isOnline = true')
            ->orderBy('product.createdAt', 'DESC')
            ->setParameter('category', $category);

        if ($displayAll === null || $displayAll === "false") {
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

        return $query->getQuery()->getResult();
    }

    public function getNewProducts()
    {
        $date = new \DateTime();
        $startDate = $date->modify("-1 week");
        return $this->createQueryBuilder('product')
            ->where('product.createdAt > :now')
            ->andWhere('product.isOnline = true')
            ->orderBy('product.createdAt', 'DESC')
            ->setMaxResults(self::NEW_PRODUCTS_COUNT)
            ->setParameter('now', $startDate)
            ->getQuery()->getResult();
    }

    public function getDiscountedProducts()
    {
        $date = new \DateTime();
        $startDate = $date->modify("-1 week");
        return $this->createQueryBuilder('product')
            ->where('product.discount is not null')
            ->andWhere('product.discount > 0')
            ->andWhere('product.isOnline = true')
            ->orderBy('product.createdAt', 'DESC')
            ->setMaxResults(self::NEW_PRODUCTS_COUNT)
            ->getQuery()->getResult();
    }
}
