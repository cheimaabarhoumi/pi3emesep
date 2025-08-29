<?php

namespace App\Controller\back;
use App\Entity\Voiture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\VoitureRepository;
use App\Repository\AnnonceRepository;
use App\Entity\Annonce;
class GestionVoitureController extends AbstractController
{
    #[Route('/admin/gestion-voiture', name: 'gestion_voiture')]
    public function accueilAdmin(VoitureRepository $voitureRepository): Response
    {
        $voitures = $voitureRepository->findAll();
        return $this->render('back/voiture/gestion_voiture.html.twig', [
            'voitures' => $voitures,
        ]);
    }

    #[Route('/admin', name: 'admin_home')]
    public function listVoitures(): Response
    {
        return $this->render('back/accueil/accueil_admin.html.twig', [
            'controller_name' => 'GestionVoitureController',
        ]);
    }

    #[Route('/admin/gestion-annonce', name: 'gestion_annonce')]
    public function accueilAdminannonce(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findAll(); 
        return $this->render('back/annonce/gestion_annonce.html.twig', [
            'annonces' => $annonces, 
        ]);
        
    }


    #[Route('/admin/gestion-voiture/delete/{id}', name: 'delete_voiture')]
    public function deleteVoiture(EntityManagerInterface $entityManager, Voiture $voiture): Response
    {
        // Supprimer la voiture
        $entityManager->remove($voiture);
        $entityManager->flush();

        // Rediriger vers la liste des voitures
        return $this->redirectToRoute('gestion_voiture');
    }




}