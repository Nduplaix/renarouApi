<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * @Route("/send-activation", name="sendActivationMail")
     * @param User|null $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param \Swift_Mailer $mailer
     * @return bool|JsonResponse
     */
    public function sendActivationMail(?User $user, Request $request, EntityManagerInterface $manager, \Swift_Mailer $mailer)
    {
        if (null === $user) {
            $requestContent = json_decode($request->getContent());
            $user = $manager->getRepository(User::class)->findOneBy(['email' => $requestContent->email]);

            if ($user === null) {
                return new JsonResponse(
                    [
                        "code" => "401",
                        "message" => "Ce compte ne peut pas être activé"
                    ],
                    401
                );
            } elseif ($user->getActivated()) {
                return new JsonResponse(
                    [
                        "code" => "401",
                        "message" => "Ce compte est déja activé"
                    ],
                    401
                );
            }
        }

        $user->generateToken();
        $manager->flush();

        $message = (new \Swift_Message("Bienvenu sur le site Renarou!"))
            ->setFrom($this->getParameter('email'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/address_confirmation.html.twig',
                    ['user' => $user]
                ),
                'text/html'
            );

        $mailer->send($message);

        return new JsonResponse();
    }

    /**
     * @Route("/active-mail", name="email_activation")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return JsonResponse
     */
    public function emailActivation(Request $request, EntityManagerInterface $manager)
    {
        $requestContent = json_decode($request->getContent());
        $token = $requestContent->token;

        if (null === $token) {
            return new JsonResponse(
                [
                    "code" => "401",
                    "message" => "Aucun token détecté"
                ],
                401
            );
        }

        $user = $manager->getRepository(User::class)->findOneBy(['token' => urldecode($token)]);

        if (null === $user) {
            return new JsonResponse(
                [
                    "code" => "401",
                    "message" => "Ce token a expiré"
                ],
                401
            );
        }

        if ($user->getActivated()) {
            return new JsonResponse(
                [
                    "code" => "401",
                    "message" => "Ce compte est déja activé"
                ],
                401
            );
        }

        $user->clearToken();
        $user->setActivated(true);

        $manager->flush();

        return new JsonResponse('Activated');
    }
}
