<?php


namespace App\Controller\Getters;


use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetNewProducts
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
        return $this->manager->getRepository(Product::class)->getNewProducts();
    }
}
