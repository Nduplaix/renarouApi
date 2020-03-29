<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class CreateUser extends UserController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function __invoke(
        User $data,
        Request $request,
        EntityManagerInterface $manager,
        \Swift_Mailer $mailer,
        TokenGeneratorInterface $generator
    )
    {
        $user = $manager->getRepository(User::class)->findOneBy(["email" => $data->getEmail()]);

        if ($user) {
            return new JsonResponse(
                [
                    "message" => "Cette addresse email est déja utilisé",
                    "code" => Response::HTTP_UNAUTHORIZED
                ],
                Response::HTTP_UNAUTHORIZED);
        }

        $passwordEncoded = $this->encoder->encodePassword($data, $data->getPlainPassword());
        $roles = $data->getRoles();

        if(!in_array("ROLE_USER", $roles)) {
           $roles[] = "ROLE_USER";

           $data->setRoles($roles);
        }

        $data->setPassword($passwordEncoded);
        $data->setPlainPassword(null);
        $manager->persist($data);
        $manager->flush();

        $mailResponse = $this->sendActivationMail($data, $request, $manager, $mailer, $generator);

        if ($mailResponse->getStatusCode() !== 200) {
            return $mailResponse;
        }

        return $data;
    }
}
