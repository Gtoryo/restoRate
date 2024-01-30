<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Restaurant;
use App\Form\AvisFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvisController extends AbstractController
{
    public function __construct(private Security $security)
    {
        $this->security = $security;
    }

    #[Route('/avis/{restoId}', name: 'app_avis')]
    public function index(Request $request, EntityManagerInterface $entityManager, int $restoId): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->security->getUser();

        $resto = $entityManager->getRepository(Restaurant::class)->find($restoId);

        $avis = new Avis;

        $formAvis = $this->createForm(AvisFormType::class, $avis);

        $formAvis->handleRequest($request);

        if ($formAvis->isSubmitted() && $formAvis->isValid()) {
            // Associer l'avis au restaurant et à l'utilisateur
            $avis->setIdResto($resto);
            $avis->setUser($user);

            $entityManager->persist($avis);
            $entityManager->flush();

            return $this->redirectToRoute('details_resto', ['restoId' => $restoId]);
        }


        return $this->render('avis/avis.html.twig', [
            'formAvis' => $formAvis->createView(),
            'resto' => $resto
        ]);
    }
}
