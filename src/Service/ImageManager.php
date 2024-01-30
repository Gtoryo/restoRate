<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\Restaurant;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormInterface;

class ImageManager
{
    public function handleImages(FormInterface $form, Restaurant $restaurant, EntityManager $em, ImageService $imageSrc): void
    {
        $images = $form->get('images')->getData();

        foreach($images as $image) {

            // On definit le dossier de destination

            $folder = 'restaurants';

            // On appelle le service d'ajout d'images

            $fileName = $imageSrc->add($image, $folder);

            // On crée une nouvelle image à inserer dans la base de données

            $img = new Image;

            $img->setNomImage($fileName);

            $em->persist($restaurant);                               
            $em->flush();
        }
    }
}