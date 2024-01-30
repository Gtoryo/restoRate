<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class Users extends Fixture
{ 

    public function __construct(private UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {

        $admin = new User();

        $admin->setNom('massina');
        $admin->setPrenom('Lenny');
        $admin->setEmail('Lenny@gmail.com');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'Lenny2')
        );

        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);



        $faker = Faker\Factory::create('fr_FR');

        for($usr = 1; $usr <= 5; $usr++)
        {
            $user = new User();

            $user->setNom($faker->lastName);
            $user->setPrenom($faker->firstName);
            $user->setEmail($faker->email);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'stephane')
            );

            $manager->persist($user);
        }

        for($rst = 1; $rst <= 5; $rst++)
        {
            $restaurateur = new User();

            $restaurateur->setNom($faker->lastName);
            $restaurateur->setPrenom($faker->firstName);
            $restaurateur->setEmail($faker->email);
            $restaurateur->setPassword(
                $this->passwordEncoder->hashPassword($restaurateur, 'restaurateur')
            );

            $restaurateur->setRoles(['ROLE_RESTAURATEUR']);

            $manager->persist($restaurateur);

        }

        $manager->flush();
    }
}
