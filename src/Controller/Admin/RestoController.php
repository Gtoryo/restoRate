<?php

namespace App\Controller\Admin;

use App\Entity\Restaurant;
use App\Form\RestoFormType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/restaurants', name: 'resto_')]
class RestoController extends AbstractController
{
    #[Route('/', name: 'list_resto')]
    public function index(RestaurantRepository $restoRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $data = $restoRepo->findBy([], ['nomResto' =>'ASC']);

        $restos = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('admin/index.html.twig', [
            'restos' => $restos
        ]);
    }

    // #[Route('/ajout', name: 'add_resto')]
    // public function add(Request $request, EntityManagerInterface $em): Response
    // {
    //     $resto = new Restaurant;

    //     $restoForm = $this->createForm(RestoFormType::class, $resto);

    //     $restoForm->handleRequest($request);

    //     if($restoForm->isSubmitted() && $restoForm->isValid())
    //     {
    //         $em->persist($resto);

    //         $em->flush();

    //         return $this->redirectToRoute('resto_list_resto');
    //     }

    //     return $this->render('resto/add.html.twig', [
    //         'restoForm' => $restoForm->createView()
    //     ]);
    // }

    // #[Route('/modifier/{id}', name: 'set_resto')]
    // public function set(Request $request, Restaurant $resto, EntityManagerInterface $em): Response
    // {
    //     $restoForm = $this->createForm(RestoFormType::class, $resto);

    //     $restoForm->handleRequest($request);

    //     if($restoForm->isSubmitted() && $restoForm->isValid())
    //     {
    //         $em->persist($resto);

    //         $em->flush();

    //         return $this->redirectToRoute('resto_list_resto');
    //     }

    //     return $this->render('resto/set.html.twig', [
    //         'restoForm' => $restoForm->createView()
    //     ]);
    // }

    #[Route('/delete/{id}', name: 'delete_resto')]
    public function remove(Restaurant $resto, EntityManagerInterface $em): Response
    {
        $em->remove($resto);

        $em->flush();

        return $this->redirectToRoute('resto_list_resto');
    }
}
