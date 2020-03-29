<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

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
     * @param TokenGeneratorInterface $generator
     * @return bool|JsonResponse
     */
    public function sendActivationMail(
        ?User $user,
        Request $request,
        EntityManagerInterface $manager,
        \Swift_Mailer $mailer,
        TokenGeneratorInterface $generator
    )
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

        $token = $generator->generateToken();

        $user->setToken($token);
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

        return new JsonResponse('email send');
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

    /**
     * @Route("/send-reset-password", name="send_reset_password_email")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param \Swift_Mailer $mailer
     * @param TokenGeneratorInterface $generator
     * @return JsonResponse
     */
    public function sendResetPasswordMail(
        Request $request,
        EntityManagerInterface $manager,
        \Swift_Mailer $mailer,
        TokenGeneratorInterface $generator
    )
    {
        $requestContent = json_decode($request->getContent());
        $email = $requestContent->email;

        if (null === $email) {
            return new JsonResponse(
                [
                    "code" => "401",
                    "message" => "Aucune adresse e-mail renseignée."
                ],
                401
            );
        }

        $user = $manager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (null === $user) {
            return new JsonResponse(
                [
                    "code" => "401",
                    "message" => "Cette adresse E-mail ne correspond à aucun comptes."
                ],
                401
            );
        }

        $token = $generator->generateToken();

        $user->setToken($token);
        $manager->flush();

        $message = (new \Swift_Message("Réinitialisation de votre mot de passe Renarou"))
            ->setFrom($this->getParameter('email'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/reset_password.html.twig',
                    [
                        'user' => $user,
                        'email' => $this->getParameter('admin_email')
                    ]
                ),
                'text/html'
            );

        $mailer->send($message);

        return new JsonResponse('email send');
    }

    /**
     * @Route("/reset-password", name="reset_password")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function resetPassword(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder
    )
    {
        $requestContent = json_decode($request->getContent());
        $token = $requestContent->token;
        $plainPassword = $requestContent->password;
        $confirmPassword = $requestContent->confirmPassword;

        if (null === $plainPassword) {
            return new JsonResponse(
                [
                    "code" => "401",
                    "message" => "Aucun Mot de passe."
                ],
                401
            );
        }

        if ($confirmPassword !== $plainPassword) {
            return new JsonResponse(
                [
                    "code" => "401",
                    "message" => "Les mots de passe doivent être identiques."
                ],
                401
            );
        }

        if (strlen($plainPassword) < 6) {
            return new JsonResponse(
                [
                    "code" => "401",
                    "message" => "Le mot de passe doit être composé d'au moins 6 caractères"
                ],
                401
            );
        }

        if (null === $token) {
            return new JsonResponse(
                [
                    "code" => "401",
                    "message" => "Aucun token détecté."
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

        $user->clearToken();
        $passwordEncoded = $encoder->encodePassword($user, $plainPassword);

        $user->setPassword($passwordEncoded);

        $manager->flush();

        return new JsonResponse('updated');
    }

}
