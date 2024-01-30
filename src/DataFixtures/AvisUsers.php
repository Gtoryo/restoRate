<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Avis;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AvisUsers extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        for($avs = 1; $avs <= 5; $avs++) {
        
            $avis = new Avis();
            $avis->setTitre($faker->sentence(5));
            $avis->setNote($faker->numberBetween(1, 5));
            $avis->setCommentaire($faker->paragraph(4));

            $manager->persist($avis);
        }
        

        

        $manager->flush();
    }
}
