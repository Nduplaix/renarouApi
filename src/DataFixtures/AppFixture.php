<?php

namespace App\DataFixtures;

use App\Entity\Adress;
use App\Entity\Banner;
use App\Entity\Category;
use App\Entity\Commande;
use App\Entity\CommandeLine;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Images;
use App\Entity\Product;
use App\Entity\Reference;
use App\Entity\Size;
use App\Entity\SubCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class AppFixture extends Fixture
{
    private $encoder;

    const CHILD_SIZES = [
        "1 ans",
        "6 ans",
        "7 ans"
    ];

    const BABY_SIZES = [
        "2-3 mois",
        "4-5 mois",
    ];

    const CATEGORIES_DATA = [
        [
            "label" => "Fille",
            "subCategories" => [
                "Pantalons",
                "T-shirts",
                "Pulls",
                "Vestes",
                "Robes",
                "Jupes"
            ]
        ],
        [
            "label" => "Garçon",
            "subCategories" => [
                "Pantalons",
                "T-shirts",
                "Pulls",
                "Vestes",
            ]
        ],
        [
            "label" => "Bébé Fille",
            "subCategories" => [
                "Pantalons",
                "T-shirts",
                "Pulls",
                "Vestes",
                "Robes",
                "Jupes",
                "Bodies"
            ]
        ],
        [
            "label" => "Bébé Garçon",
            "subCategories" => [
                "Pantalons",
                "T-shirts",
                "Pulls",
                "Vestes",
                "Bodies"
            ]
        ]
    ];

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        $childSizes = [];
        $babySizes = [];

        foreach (self::CHILD_SIZES as $sizeData) {
            $size = new Size();
            $size->setLabel($sizeData);

            $childSizes[] = $size;

            $manager->persist($size);
        }

        foreach (self::BABY_SIZES as $sizeData) {
            $size = new Size();
            $size->setLabel($sizeData);

            $babySizes[] = $size;

            $manager->persist($size);
        }

        foreach (self::CATEGORIES_DATA as $key => $categoryData) {
            $category = new Category();
            $category->setLabel($categoryData['label']);

            $manager->persist($category);

            foreach ($categoryData['subCategories'] as $subCategoryData) {
                $subCategory = new SubCategory();
                $subCategory->setLabel($subCategoryData)
                    ->setCategory($category);

                $manager->persist($subCategory);

                for ($i = 0; $i < mt_rand(10, 50); $i++) {
                    $product = new Product();
                    $product->setLabel($faker->sentence(3))
                        ->setPrice($faker->randomFloat(2, 1.99, 20))
                        ->setDescription($faker->paragraph)
                        ->setIsOnline(true)
                        ->setSubCategory($subCategory);

                    $discountCondition = mt_rand(0, 10);

                    if ($discountCondition < 2) {
                        $product->setDiscount(mt_rand(10, 75));
                    }

                    $manager->persist($product);

                    for ($j = 0; $j < mt_rand(1,5); $j++) {
                        $image = new Image();
                        $image
                            ->setLabel('immage'.$i)
                            ->setLink('http://placehold.it/250x400')
                            ->setProduct($product);

                        $manager->persist($image);

                    }

                    $sizes = [];

                    if ($key < 2) {
                        $sizes = $childSizes;
                    } else {
                        $sizes = $babySizes;
                    }

                    foreach ($sizes as $size) {
                        $reference = new Reference();
                        $reference->setSize($size)
                            ->setProduct($product)
                            ->setStock(mt_rand(0,20));
                        $manager->persist($reference);
                    }
                }
            }
        }

        for ($i = 0; $i < mt_rand(1,5); $i++) {
            $banner = new Banner();
            $banner->setLabel($faker->unique()->word)
                ->setDescription($faker->sentence)
                ->setLink("https://via.placeholder.com/468x60?text=468x60+Full +Banner");
            $manager->persist($banner);
        }

//
//        $comment = new Comment();
//        $comment
//            ->setProduct($product)
//            ->setContent('Très bien')
//            ->setRating(10)
//            ->setUser($user)
//        ;
//        $manager->persist($comment);
//
//        $commande = new Commande();
//        $commande
//            ->setUser($user)
//            ->setAdress($adress)
//            ->setCommandDate(new \DateTime())
//            ->setCommandNumber(1)
//        ;
//        $manager->persist($commande);
//
//        $commandeLine = new CommandeLine();
//        $commandeLine
//            ->setNumber(2)
//            ->setCommande($commande)
//            ->setReference($reference)
//            ->setProduct($reference->getProduct());
//        ;
//        $manager->persist($commandeLine);
//
        $manager->flush();
    }
}
