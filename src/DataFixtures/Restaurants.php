<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Restaurant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
;

class Restaurants extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($resto = 1; $resto <= 10; $resto++)
        {
            $restaurant = new Restaurant();

            $restaurant->setNomResto($faker->name);
            $restaurant->setDescription($faker->sentence(10));
            $restaurant->setAdresse($faker->streetAddress);
            $restaurant->setCodePostale(str_replace(' ', '', $faker->postcode));
            $restaurant->setville($faker->city);
            $restaurant->setTelephone($faker->numerify('04########'));

            $manager->persist($restaurant);

        }

        $manager->flush();
        
    }
}
