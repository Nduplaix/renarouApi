<?php


namespace App\Controller\Getters;


use App\Entity\Category;
use App\Entity\Product;
use App\Entity\SubCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class GetProducts
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Request $request
     * @return Product[]
     */
    public function __invoke(Request $request)
    {
        $page = (int) $request->query->get('page', 1);

        $repo = $this->manager->getRepository(Product::class);

        $subCategoryId = $request->query->get('subcategory');
        $categoryId = $request->query->get('category');

        if ($subCategoryId)
        {
            $subCategory = $this->manager->getRepository(SubCategory::class)->find($subCategoryId);
            $data = $repo->findProductsBySubCategory($subCategory, $page);
        } elseif ($categoryId) {
            $category = $this->manager->getRepository(Category::class)->find($categoryId);
            $data = $repo->findProductsByCategory($category, $page);
        }

        return $data?:null;

    }
}
