<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CheckUserPassword
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
        if($data->getPlainPassword()) {
            $isValidPassword = $this->encoder->isPasswordValid($data, $data->getPlainPassword());
            
            if ($isValidPassword) {
                return new JsonResponse(["code" => 202, "message" => "Authorized"], 202);
            }
        }

        return new JsonResponse(["code" => 401, "message" => "Mot de passe incorrect"], 401);
    }
}
