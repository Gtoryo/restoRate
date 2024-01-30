<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Restaurant;
use App\Form\RestoFormType;
use App\Repository\RestaurantRepository;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestaurateurController extends AbstractController
{
    #[Route('restaurateur/details/{idResto}', name: 'app_details')]
    public function details(RestaurantRepository $restoRepo, int $idResto, Request $request, EntityManagerInterface $em, ImageService $imageSrc): Response
    {
        $resto = $restoRepo->find($idResto);

        if(!$resto) {

            throw $this->createNotFoundException('Restaurant non touvé');
        }


        $form = $this->createForm(RestoFormType::class, $resto);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

             // On récupère les images
            $images = $form->get('images')->getData();

            foreach($images as $image) {
                
                // On définit le dossier de destination

                $folder = 'restaurants';

                // On appelle le service d'ajout 

                $fichier = $imageSrc->add($image, $folder);

                $img = new Image;

                $img->setNomImage($fichier);

                $em->persist($img);

                $resto->addImage($img);
            }
 
            
            $em->persist($resto);
            $em->flush();

            return $this->redirectToRoute('profil_info', ['idResto' => $resto->getId()]);
        }
        
        return $this->render('user/details.html.twig', [
            'restoForm' => $form->createView()
        ]);
    }

    #[Route('restaurateur/delete/{idResto}', name: 'app_delete')]
    public function delete(RestaurantRepository $restoRepo, int $idResto, EntityManagerInterface $em): Response
    {
        $resto = $restoRepo->find($idResto);

        if(!$resto) {

            throw $this->createNotFoundException('Restaurant non touvé');
        }

        // Récupération des avis et des images liés à ce restaurant 
        $avis = $resto->getAvis();

        $img = $resto->getImages();

        // Et les supprimés

        foreach($avis as $avisItem) {
            $em->remove($avisItem);
        }

        foreach($img as $imgItem) {
            $em->remove($imgItem);
        }

        $em->remove($resto);
        $em->flush();

        return $this->redirectToRoute('app_profil_info');

    }

    #[Route('restaurateur/newresto', name: 'new_resto')]
    public function newResto(Request $request, EntityManagerInterface $em, ImageService $imageSrc): Response
    {
        $user = $this->getUser();

        $resto = new Restaurant;

        if(!$user) {

            throw $this->createNotFoundException('Utilisateur non existant');
        }

        $resto->setIdUser($user);

        $form = $this->createForm(RestoFormType::class, $resto);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            // On récupère les images

            $images = $form->get('images')->getData();

            foreach($images as $image) {
                
                // On définit le dossier de destination

                $folder = 'restaurants';

                // On appelle le service d'ajout 

                $fichier = $imageSrc->add($image, $folder);

                $img = new Image;

                $img->setNomImage($fichier);

                $em->persist($img);

                $resto->addImage($img);

            }


            $em->persist($resto);
            $em->flush();

            return $this->redirectToRoute('app_profil_info', ['idResto' => $resto->getId()]);
        }

        
        return $this->render('user/newResto.html.twig', [
            'restoForm' => $form->createView()
        ]);
    }

    
    #[Route('restaurateur/mesAvis', name: 'app_mesAvis')]
    public function avis():Response
    {
        $user = $this->getUser();

        $resto = $user->getRestaurants();

        $allAvis = [];

        if(!$resto) {

            throw $this->createNotFoundException('Aucun restaurants trouvés');
        }
        
        foreach ($resto as $restaurant) {

            $avis = $restaurant->getAvis();

            $allAvis[] = [
                'restaurant' => $restaurant,
                'avis' => $avis,
            ];
        }


        return $this->render('user/avis.html.twig', [
            'allAvis' => $allAvis
        ]);
    }
}