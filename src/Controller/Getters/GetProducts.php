<?php


namespace App\Controller\Getters;


use App\Entity\Category;
use App\Entity\Product;
use App\Entity\SubCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetProducts
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Request $request
     * @return Product[]|Response
     */
    public function __invoke(Request $request)
    {
        $page = (int) $request->query->get('page', 1);

        $repo = $this->manager->getRepository(Product::class);

        $subCategorySlug = $request->query->get('subcategory');
        $categorySlug = $request->query->get('category');

        if ($subCategorySlug)
        {
            $subCategory = $this->manager->getRepository(SubCategory::class)->findOneBy([
                'slug' => $subCategorySlug
            ]);

            if ($subCategory) {
                $data = $repo->findProductsBySubCategory($subCategory, $page);
            }
        } elseif ($categorySlug) {
            $category = $this->manager->getRepository(Category::class)->findOneBy([
                'slug' => $categorySlug
            ]);

            if ($category) {
                $data = $repo->findProductsByCategory($category, $page);
            }
        }

        return $data??new Response('liste des produits introuvable', 404);

    }
}
