<?php


namespace App\DataFixtures;

use App\Entity\CommandeStatus;
use App\Entity\Delivery;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CommandeFixtures extends Fixture
{
    const STATUS = [
        "Non payé",
        "En cours de préparation",
        "Prête à être expédié",
        "Expédié",
        "Réceptionné"
    ];

    const DELIVRY = [
        [
            "label" => "Livraison",
            "price" => "4.99",
        ],
        [
            "label" => "Click and Collect",
            "price" => "0"
        ]
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::STATUS as $status) {
            $data = new CommandeStatus();
            $data->setLabel($status);
            $manager->persist($data);
        }

        foreach (self::DELIVRY as $delivery) {
            $data = new Delivery();
            $data->setLabel($delivery["label"])
                ->setShippingPrice($delivery["price"])
                ->setIsActive(true);
            $manager->persist($data);
        }

        $manager->flush();
    }
}
