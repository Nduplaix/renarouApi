<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UpdateUser
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function __invoke(User $data, EntityManagerInterface $manager): User
    {
        if($data->getPlainPassword()) {
            $passwordEncoded = $this->encoder->encodePassword($data, $data->getPlainPassword());

            $data->setPassword($passwordEncoded);
            $data->setPlainPassword(null);
        }
        $manager->flush();

        return $data;
    }
}
