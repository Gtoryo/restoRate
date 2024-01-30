<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,
     UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, 
     EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été crée avec succès. Il ne vous reste plus qu\'à l\'ctivé');

            // do anything else you need here, like send an email

            // On génère le token pour l'utlisateur qui vient de se connecter

            // Tout d'abord on crée  le header de notre token

            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // On crée le payload

            $payload = [
                'id' => $user->getId()
            ];

            // On génère le token

            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));


            //Envoi d'un mail à l'utilisateur qui vient de se connecter
            $mail->send(
                'no-reply@restoRate.net',
                $user->getEmail(),
                'Activation de votre compte sur restoRate',
                'register',
                compact('user', 'token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/{token}', name: 'compte_verify')]
    public function compteVerify($token, JWTService $jwt, UserRepository $userRepo, EntityManagerInterface $em): Response
    {

        // On vérifie si le token est validde, n'a pas expiré et n'a pas été modifié

        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {

            $payload = $jwt->getPayload($token);

            // On récupère le user du token 

            $user = $userRepo->find($payload['id']);

            // On verifie que l'utilisateur existe et n'a pas encore activé son compte  

            if($user && !$user->getIsVerified()) {
                $user->setIsVerified(true);
                $em->flush($user);

                $this->addFlash('success', 'compte activé');

                return $this->redirectToRoute('accueil');
            }
        }

        // Si le token n'est pas valide 

        return $this->redirectToRoute('app_login');
    }

    
}
