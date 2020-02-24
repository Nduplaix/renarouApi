<?php


namespace App\DataFixtures;

use App\Entity\CommandeStatus;
use App\Entity\Delivery;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CommandeFixtures extends Fixture
{
    const STATUS = [
        "En cours de préparation",
        "Prête à être expédié",
        "Expédié",
        "Réceptionné"
    ];

    const DELIVRY = [
        "Livraison",
        "Click and Collect"
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
            $data->setLabel($delivery)
                ->setIsActive(true);
            $manager->persist($data);
        }

        $manager->flush();
    }
}
