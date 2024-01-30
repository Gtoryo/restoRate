<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

#[Route('admin/users', name: 'user_')]
class UserController extends AbstractController
{
    #[Route('/', name: 'liste')]
    public function index(UserRepository $userRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $data = $userRepo->findBy([], ['nomUser' => 'ASC']);

        $users = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function remove(User $user, EntityManagerInterface $em): Response
    {
        if(!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $avis = $user->getAvis();

        foreach($avis as $avs) {
            
            $em->remove($avs);
        }

        $em->remove($user);

        $em->flush();

        return $this->redirectToRoute('user_liste');
    }
}