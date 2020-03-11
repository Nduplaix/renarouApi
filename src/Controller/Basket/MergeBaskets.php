<?php

namespace App\Controller\Basket;

use App\Entity\BasketLine;
use App\Entity\Reference;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MergeBaskets extends AbstractController
{
    /**
     * @Route("/api/basket/merge", name="merge_basket", methods={"POST"})
     *
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return JsonResponse
     */
    public function mergeBaskets(EntityManagerInterface $em, Request $request)
    {
        $currentBasket = $this->getUser()->getBasket();

        $basket = json_decode($request->getContent(), true);

        foreach ($basket['basketLines'] as $line) {
            $isInside = false;

            foreach ($currentBasket->getBasketLines() as $basketLine) {
                if ($basketLine->getReference()->getId() === $line['reference']['id']) {
                    $basketLine->setQuantity($basketLine->getQuantity() + $line['quantity']);

                    if ($basketLine->getQuantity() > $basketLine->getReference()->getStock()) {
                        $basketLine->setQuantity($basketLine->getReference()->getStock());
                    }

                    $isInside = true;
                }
            }

            if (!$isInside) {
                $newLine = new BasketLine();
                $reference = $em->getRepository(Reference::class)->find($line['reference']['id']);
                $newLine->setQuantity($line['quantity'])
                    ->setReference($reference)
                    ->setBasket($currentBasket);
                $currentBasket->addBasketLine($newLine);
                $em->persist($newLine);
            }
        }

        $em->flush();

        return new JsonResponse('ok', 201);
    }
}
