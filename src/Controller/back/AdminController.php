<?php

namespace App\Controller\back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\VoitureRepository;
use App\Repository\AnnonceRepository;
use App\Entity\Annonce;
use App\Entity\Voiture;



class AdminController extends AbstractController
{

     #[Route('/admin', name: 'admin_home')]
    public function accueilAdmin(): Response
    {
        return $this->render('back/accueil/accueil_admin.html.twig', [
            'controller_name' => 'AdminController',
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


    #[Route('/admin/gestion-voiture', name: 'gestion_voiture')]
    public function accueilAdminvoiture(VoitureRepository $voitureRepository): Response
    {
        $voitures = $voitureRepository->findAll();
        return $this->render('back/voiture/gestion_voiture.html.twig', [
            'voitures' => $voitures,
        ]);
    }
}
