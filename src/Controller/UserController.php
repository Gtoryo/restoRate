<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/profil', name: 'app_profil_')]
class UserController extends AbstractController
{
    

    public function __construct(private AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    #[Route('/', name: 'info')]
    public function profil(RestaurantRepository $restoRepo): Response
    {
        if ($this->authorizationChecker->isGranted('ROLE_RESTAURATEUR')) {
            // Récupérer l'utilisateur connecté
            $user = $this->getUser();

            // Récupérer les restaurants liés à cet utilisateur
            $restaurants = $restoRepo->findBy(['idUser' => $user]);

            return $this->render('user/profilResto.html.twig', [
                'restaurants' => $restaurants,
            ]);
        }

        return $this->render('user/profil.html.twig');
    }
    

    #[Route('/setProfil', name: 'set')]
    public function setUser(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_profil_info');
        }

        return $this->render('user/setProfil.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
