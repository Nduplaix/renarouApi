<?php


namespace App\Controller\Basket;


use App\Entity\Basket;
use App\Entity\BasketLine;
use App\Entity\Reference;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateBasketLine
{
    public function __invoke(BasketLine $data, EntityManagerInterface $manager)
    {
        $basket = $data->getBasket();

        if ($basket) {
            $alreadyExistLine = $this->basketLineAlreadyExist($basket, $data->getReference());

            if ($alreadyExistLine) {
                $quantity = $alreadyExistLine->getQuantity();
                $alreadyExistLine->setQuantity($quantity + $data->getQuantity());

                if ($alreadyExistLine->getQuantity() > $alreadyExistLine->getReference()->getStock()) {
                    return new JsonResponse(["code" => 401, "message" => sprintf("Stock insuffisant. Vous avez déja %d fois cette article dans votre panier.", $quantity)], 401);
                }
            } else {
                if ($data->getQuantity() > $data->getReference()->getStock()) {
                    return new JsonResponse(["code" => 401, "message" => "Stock insuffisant."], 401);
                }

                $manager->persist($data);
            }

            $manager->flush();

            return new JsonResponse(["code" => 201], 201);
        }

        return new JsonResponse(["code" => "401", "message" => "Veuillez renseigner le panier"], 401);
    }

    public function basketLineAlreadyExist(Basket $basket, Reference $reference)
    {
        foreach ($basket->getBasketLines() as $line) {
            if ($line->getReference() === $reference) {
                return $line;
            }
        }

        return null;
    }
}
