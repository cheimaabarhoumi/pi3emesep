<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AnnonceRepository;
use App\Repository\VoitureRepository;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_front_accueil')]
    public function index(AnnonceRepository $annonceRepository, VoitureRepository $voitureRepository): Response
    {
        // fetch last 8 validated annonces with joined voiture
        $annonces = $annonceRepository->findBy(['valider' => true], ['id' => 'DESC'], 8);
        $voitures = $voitureRepository->findBy([], ['id' => 'DESC'], 8);

        return $this->render('front/accueil/accueil.html.twig', [
            'controller_name' => 'AccueilController',
            'annonces' => $annonces,
            'voitures' => $voitures,
        ]);
    }
}
