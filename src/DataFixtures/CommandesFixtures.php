<?php


namespace App\DataFixtures;


use App\Entity\Commande;
use App\Entity\CommandeLine;
use App\Entity\CommandeStatus;
use App\Entity\Delivery;
use App\Entity\Reference;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommandesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $refs = $manager->getRepository(Reference::class)->findAll();
        $status = $manager->getRepository(CommandeStatus::class)->find(1);
        $delivery = $manager->getRepository(Delivery::class)->find(1);
        $user = $manager->getRepository(User::class)->find(3);

        $commande = new Commande();
        $commande->setUser($user)
            ->setStatus($status)
            ->setDelivery($delivery);

        foreach ($refs as $ref) {
            $commandeLine = new CommandeLine();
            $commandeLine->setReference($ref)
                ->setQuantity(mt_rand(1, 5))
                ->setCommande($commande);
            $commande->addCommandeLine($commandeLine);
            $manager->persist($commandeLine);
        }

        $manager->persist($commande);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            AppFixture::class,
            CommandeFixtures::class
        );
    }
}
