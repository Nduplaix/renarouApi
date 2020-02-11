<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUser
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function __invoke(User $data, EntityManagerInterface $manager)
    {
        $user = $manager->getRepository(User::class)->findOneBy(["email" => $data->getEmail()]);

        if ($user) {
            return new JsonResponse(["message" => "Cette addresse email est déja utilisé", "code" => Response::HTTP_UNAUTHORIZED ], Response::HTTP_UNAUTHORIZED);
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

        return $data;
    }
}
