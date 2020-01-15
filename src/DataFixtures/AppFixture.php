<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Adress;
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
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixture extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // #################### Categorie Fille #################################
        $category = new Category();
        $category->setLabel('Filles');

        $manager->persist($category);

        // ##################### SubCatégoires ##################################
        $subCategory = new SubCategory();
        $subCategory->setLabel('Pantalon');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('T-shirt');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('Pulls');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('Vestes');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('Jupes');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('Robes');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        // #################### Categorie Garçons #################################
        $category = new Category();
        $category->setLabel('Graçons');
        $manager->persist($category);

        // ##################### SubCatégoires ##################################
        $subCategory = new SubCategory();
        $subCategory->setLabel('Pantalon');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('T-shirt');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('Pulls');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('Vestes');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        // #################### Categorie Bébé Fille #################################
        $category = new Category();
        $category->setLabel('Bébés Filles');
        $manager->persist($category);

        // ##################### SubCatégoires ##################################
        $subCategory = new SubCategory();
        $subCategory->setLabel('Pantalon');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('T-shirt');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('Pulls');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('Vestes');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('Jupes');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('Robes');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('Bodies');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        // #################### Categorie Bébé Garçons #################################
        $category = new Category();
        $category->setLabel('Bébé Graçons');
        $manager->persist($category);

        // ##################### SubCatégoires ##################################
        $subCategory = new SubCategory();
        $subCategory->setLabel('Pantalon');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('T-shirt');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('Pulls');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('Vestes');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $subCategory = new SubCategory();
        $subCategory->setLabel('Bodies');
        $subCategory->setCategory($category);
        $manager->persist($subCategory);

        $size = new Size();
        $size->setLabel('2-3 mois');
        $manager->persist($size);

        for ($i = 0; $i<30; $i++ ){
            $product = new Product();
            $product
                ->setIsOnline(true)
                ->setLabel('body bleu')
                ->setDescription('Joli body bleu')
                ->setPrice(10.0)
                ->setSubCategory($subCategory)
            ;
            $manager->persist($product);

            for ($j = 0; $j < mt_rand(1,5); $j++) {
                $image = new Image();
                $image
                    ->setLabel('immage'.$i)
                    ->setPath('http://placehold.it/250x400')
                    ->setProduct($product);

                $manager->persist($image);

            }
            $reference = new Reference();
            $reference
                ->setProduct($product)
                ->setSize($size)
                ->setStock(10)
            ;
            $manager->persist($reference);
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
