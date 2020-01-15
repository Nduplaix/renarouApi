<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
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

    public function __invoke(User $data, ObjectManager $manager): User
    {
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
