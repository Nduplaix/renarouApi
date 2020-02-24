<?php


namespace App\Controller\Commandes;


use App\Entity\Basket;
use App\Entity\Commande;
use App\Entity\CommandeLine;
use App\Entity\CommandeStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateCommande extends AbstractController
{
    public function __invoke(Commande $data, EntityManagerInterface $manager)
    {
        $basket = $this->getUser()->getBasket();

        if ($basket instanceof Basket && sizeof($basket->getBasketLines()) !== 0) {
            $status = $manager->getRepository(CommandeStatus::class)->find(1);

            $data->setUser($this->getUser())
                ->setStatus($status);
            foreach ($basket->getBasketLines() as $line) {
                $commandeLine = new CommandeLine();
                $commandeLine->setQuantity($line->getQuantity())
                    ->setReference($line->getReference())
                    ->setCommande($data);
                $data->addCommandeLine($commandeLine);
                $line->getReference()->setStock($line->getReference()->getStock() - $line->getQuantity());

                if ($line->getReference()->getStock() < 0) {
                    return new JsonResponse(
                        [
                            "code" => "401",
                            "message" => sprintf(
                                "Le stocke pour l'article %s est insufisant",
                                $line->getReference()->getProduct()->getLabel())
                        ],
                        401
                    );
                }

                $manager->remove($line);
                $manager->persist($commandeLine);
            }
            $manager->persist($data);
            $manager->flush();

            return $data;
        }
        return new JsonResponse(["code" => "401", "message" => "Le panier ne peut pas être vide"], 401);
    }
}
