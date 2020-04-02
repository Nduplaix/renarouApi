<?php

namespace App\Controller;

use App\Entity\DiscountCode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 * Class DiscountCodeController
 * @package App\Controller
 */
class DiscountCodeController extends AbstractController
{
    /**
     * @Route("/submit-discount", name="submitDiscountCode", methods={"POST"})
     *
     * Take 1 param: code (required)
     *
     * @param Request $request
     */
    public function submitCode(Request $request, EntityManagerInterface $manager)
    {
        $requestContent = json_decode($request->getContent());

        if (null === $requestContent->code) {
            return new JsonResponse(
                [
                    "code" => 401,
                    "message" => "Aucun code renseigné."
                ],
                401
            );
        }

        $code = $manager->getRepository(DiscountCode::class)->findOneBy(["code" => $requestContent->code]);

        if (null === $code) {
            return new JsonResponse(
                [
                    "code" => 401,
                    "message" => "Ce code n'existe pas."
                ],
                401
            );
        }

        $user = $code->getUser();

        if ($user !== null && $user !== $this->getUser()) {
            return new JsonResponse(
                [
                    "code" => 401,
                    "message" => "Ce code ne vous est pas destiné."
                ],
                401
            );
        }

        $today = new \DateTime();
        $today->setTimezone(new \DateTimeZone("Europe/Paris"));

        if ($code->getExperationDate() < $today) {
            return new JsonResponse(
                [
                    "code" => 401,
                    "message" => "Ce code a expiré."
                ],
                401
            );
        }

        return new JsonResponse(
            [
                "id" => $code->getId(),
                "amount" => $code->getAmount(),
                "isPercent" => $code->getIsPercent()
            ]
        );
    }
}
