<?php

namespace App\Controller;

use App\Form\SearchRestoFormType;
use App\Repository\AvisRepository;
use App\Repository\RestaurantRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    #[Route('/', name: 'accueil')]
    public function search(Request $request, RestaurantRepository $restoRepo, AvisRepository $avisRepo ): Response
    {
        $restoForm = $this->createForm(SearchRestoFormType::class);

        $restoForm->handleRequest($request);

        $restaurants = $restoRepo->findWithMinRating(4, $avisRepo );

        foreach ($restaurants as $restaurant) {
            $moyenne = $avisRepo->getMoyenneNotesByResto($restaurant);
        }


        if($restoForm->isSubmitted() && $restoForm->isValid())
        {
            $codePostale = $restoForm->get('codePostale')->getData();

            $restaurants = $restoRepo->findByCodePostale($codePostale);

            return $this->render('home/resultat.html.twig', [
                'restaurants' => $restaurants,
                'restoForm' => $restoForm->createView(),
                'codePostale' => $codePostale,
                'moyenne' => $moyenne
            ]);
        }

        
        
        return $this->render('home/index.html.twig', [
            'restoForm' => $restoForm->createView(),
            'restaurants' => $restaurants,
            'moyenne' => $moyenne
        ]);
    }

    
    #[Route('/details/{restoId}', name: 'details_resto')]
    public function details(int $restoId, RestaurantRepository $restoRepo, AvisRepository $avisRepo): Response
    {
        $resto = $restoRepo->find($restoId);

        // Si le restaurant n'est pas trouvé, on gérère ce cas
        if (!$resto) {
            throw $this->createNotFoundException('Restaurant non trouvé');
        }

        // On récupère les images grâce à la relation entre les deux tables
        $images = $resto->getImages();

        // Ici pareil
        $avis = $resto->getAvis();

        $moyenne = $avisRepo->getMoyenneNotesByResto($resto);

        return $this->render('home/details.html.twig', [
            'resto' => $resto,
            'images' => $images,
            'avis' => $avis,
            'moyenne' => $moyenne
        ]);
    }


}
