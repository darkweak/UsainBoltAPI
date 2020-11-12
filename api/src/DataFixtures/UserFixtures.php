<?php


namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public const REFERENCES = 'USER_';

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail(\sprintf('admin%d@test.com', $i));

            $password = $this->encoder->encodePassword($user, 'admin');
            $user->setPassword($password);
            $manager->persist($user);
            $manager->flush();
            $this->addReference(\sprintf('%s%d', self::REFERENCES, $i), $user);
        }
    }
}
