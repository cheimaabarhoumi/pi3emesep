<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function dashboard(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        
        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}/promote', name: 'app_admin_promote_user')]
    public function promoteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        if ($user->getRole() !== 'admin') {
            $user->setRole('admin');
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'utilisateur a été promu administrateur avec succès.');
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/user/{id}/demote', name: 'app_admin_demote_user')]
    public function demoteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        if ($user->getRole() === 'admin') {
            $user->setRole('client');
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'utilisateur n\'est plus administrateur.');
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/user/{id}/delete', name: 'app_admin_delete_user')]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        if ($user !== $this->getUser()) {
            $entityManager->remove($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }
} 