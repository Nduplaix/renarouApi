<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
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

    public function __invoke(User $data, ObjectManager $manager): User
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
