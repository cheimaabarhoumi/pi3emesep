<?php

namespace App\Controller;

use App\Entity\SuiviSponsoring;
use App\Entity\Sponsoring;
use App\Form\SuiviSponsoringType;
use App\Repository\SuiviSponsoringRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/suivi-sponsoring')]
class SuiviSponsoringController extends AbstractController
{
    #[Route('/', name: 'app_suivi_sponsoring_index', methods: ['GET'])]
    public function index(SuiviSponsoringRepository $suiviSponsoringRepository): Response
    {
        return $this->render('suivi_sponsoring/index.html.twig', [
            'suivi_sponsorings' => $suiviSponsoringRepository->findAll(),
        ]);
    }

    #[Route('/sponsoring/{id}/suivis', name: 'app_suivi_sponsoring_by_sponsoring', methods: ['GET'])]
    public function suivisBySponsoring(Sponsoring $sponsoring, SuiviSponsoringRepository $suiviSponsoringRepository): Response
    {
        return $this->render('suivi_sponsoring/by_sponsoring.html.twig', [
            'sponsoring' => $sponsoring,
            'suivi_sponsorings' => $suiviSponsoringRepository->findBy(['sponsoring' => $sponsoring]),
        ]);
    }

    #[Route('/new/{idSponsoring}', name: 'app_suivi_sponsoring_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Sponsoring $idSponsoring): Response
    {
        $suiviSponsoring = new SuiviSponsoring();
        $suiviSponsoring->setSponsoring($idSponsoring);
        
        $form = $this->createForm(SuiviSponsoringType::class, $suiviSponsoring);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($suiviSponsoring);
            $entityManager->flush();

            $this->addFlash('success', 'Le suivi de sponsoring a été créé avec succès.');
            return $this->redirectToRoute('app_suivi_sponsoring_by_sponsoring', ['id' => $idSponsoring->getId()]);
        }

        return $this->render('suivi_sponsoring/new.html.twig', [
            'suivi_sponsoring' => $suiviSponsoring,
            'sponsoring' => $idSponsoring,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_suivi_sponsoring_show', methods: ['GET'])]
    public function show(SuiviSponsoring $suiviSponsoring): Response
    {
        return $this->render('suivi_sponsoring/show.html.twig', [
            'suivi_sponsoring' => $suiviSponsoring,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_suivi_sponsoring_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SuiviSponsoring $suiviSponsoring, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SuiviSponsoringType::class, $suiviSponsoring);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le suivi de sponsoring a été modifié avec succès.');
            return $this->redirectToRoute('app_suivi_sponsoring_by_sponsoring', ['id' => $suiviSponsoring->getSponsoring()->getId()]);
        }

        return $this->render('suivi_sponsoring/edit.html.twig', [
            'suivi_sponsoring' => $suiviSponsoring,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_suivi_sponsoring_delete', methods: ['POST'])]
    public function delete(Request $request, SuiviSponsoring $suiviSponsoring, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$suiviSponsoring->getId(), $request->request->get('_token'))) {
            $sponsoringId = $suiviSponsoring->getSponsoring()->getId();
            $entityManager->remove($suiviSponsoring);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le suivi de sponsoring a été supprimé avec succès.');
            return $this->redirectToRoute('app_suivi_sponsoring_by_sponsoring', ['id' => $sponsoringId]);
        }

        return $this->redirectToRoute('app_suivi_sponsoring_index');
    }

    #[Route('/{id}/change-status', name: 'app_suivi_sponsoring_change_status', methods: ['POST'])]
    public function changeStatus(Request $request, SuiviSponsoring $suiviSponsoring, EntityManagerInterface $entityManager): Response
    {
        $newStatus = $request->request->get('status');
        if (in_array($newStatus, ['En_Cours', 'Terminé', 'Rejeté'])) {
            $suiviSponsoring->setEtatSuivi($newStatus);
            $entityManager->flush();
            $this->addFlash('success', 'Le statut du suivi a été mis à jour.');
        }

        return $this->redirectToRoute('app_suivi_sponsoring_show', ['id' => $suiviSponsoring->getId()]);
    }
}