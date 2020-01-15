<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @author JD <jean-david@widop.com>
 */
class UserFixtures extends Fixture
{
    private const USERS_DATA = [
        [
            'email'          => 'admin.bush@mail.com',
            'firstName'      => 'Admin',
            'lastName'       => 'Bush',
            'roles'          => ['ROLE_ADMIN', 'ROLE_USER'],
            'password'       => '4dministr4t0r',
            'reference'      => 'admin-bush-user',
        ],
        [
            'email'          => 'john.doe@mail.com',
            'firstName'      => 'John',
            'lastName'       => 'Doe',
            'roles'          => ['ROLE_USER'],
            'password'       => 'C0ff3e',
            'reference'      => 'john-doe-user',
        ],
        [
            'email'          => 'nduplaix62@gmail.com',
            'firstName'      => 'Nicolas',
            'lastName'       => 'Duplaix',
            'roles'          => ['ROLE_ADMIN', 'ROLE_USER'],
            'password'       => 'azerty',
            'reference'      => 'nicolas-duplaix-user',
        ],
    ];

    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS_DATA as $userData) {
            $user = $this->createUser($userData);

            $address = new Address();
            $address
                ->setNumber('1')
                ->setStreetType('Rue')
                ->setStreet('test')
                ->setCity('TestCity')
                ->setPostalCode('59000')
                ->setUser($user);
            $manager->persist($address);
            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @param array $userData
     *
     * @return User
     */
    private function createUser(array $userData): User
    {
        $user = new User();

        $user
            ->setEmail($userData['email'])
            ->setFirstName($userData['firstName'])
            ->setLastName($userData['lastName'])
            ->setRoles($userData['roles'])
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
        ;

        $password = $this->encoder->encodePassword($user, $userData['password']);

        $user->setPassword($password);

        $this->addReference($userData['reference'], $user);

        return $user;
    }
}
