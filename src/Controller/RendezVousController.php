<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Form\RendezVousType;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rdv')] // Route simplifiée
final class RendezVousController extends AbstractController
{
    #[Route('/', name: 'app_rendez_vous_index', methods: ['GET'])]
    public function index(Request $request, RendezVousRepository $rendezVousRepository): Response
{
    $search = $request->query->get('search');
    $sort = $request->query->get('sort', 'dateRdv'); // Tri par défaut par date
    $direction = $request->query->get('direction', 'ASC'); // Ascendant par défaut

    $queryBuilder = $rendezVousRepository->createQueryBuilder('r')
        ->orderBy('r.' . $sort, $direction);

    if (!empty($search)) {
        $queryBuilder->andWhere('r.type_rendez_Vous LIKE :search OR r.status LIKE :search')
                     ->setParameter('search', '%'.$search.'%');
    }

    $rendezVous = $queryBuilder->getQuery()->getResult();

    return $this->render('rendez_vous/index.html.twig', [
        'rendez_vouses' => $rendezVous,
        'search' => $search,
        'sort' => $sort,
        'direction' => $direction,
    ]);
}

    #[Route('/new', name: 'app_rendez_vous_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rendezVous = new RendezVous();
        $form = $this->createForm(RendezVousType::class, $rendezVous);
        
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rendezVous);
            $entityManager->flush();
            
            $this->addFlash('success', 'Rendez-vous créé avec succès !');
            return $this->redirectToRoute('app_rendez_vous_index');
        }

        return $this->render('rendez_vous/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_rendez_vous_show', methods: ['GET'])]
    public function show(RendezVous $rendezVous): Response
    {
        return $this->render('rendez_vous/show.html.twig', [
            'rdv' => $rendezVous, // Variable simplifiée
            'can_create_entretien' => !$rendezVous->hasEntretien() && $rendezVous->isConfirmed()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rendez_vous_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RendezVous $rendezVous, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RendezVousType::class, $rendezVous);
        
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Rendez-vous modifié avec succès !');
            return $this->redirectToRoute('app_rendez_vous_show', ['id' => $rendezVous->getId()]);
        }

        return $this->render('rendez_vous/edit.html.twig', [
            'rdv' => $rendezVous, // Variable simplifiée
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/confirm', name: 'app_rendez_vous_confirm', methods: ['POST'])]
    public function confirm(RendezVous $rendezVous, EntityManagerInterface $entityManager): Response
    {
        $rendezVous->confirm();
        $entityManager->flush();
        
        $this->addFlash('success', 'Rendez-vous confirmé !');
        return $this->redirectToRoute('app_rendez_vous_show', ['id' => $rendezVous->getId()]);
    }

    #[Route('/{id}', name: 'app_rendez_vous_delete', methods: ['POST'])]
    public function delete(Request $request, RendezVous $rendezVous, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rendezVous->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rendezVous);
            $entityManager->flush();
            $this->addFlash('success', 'Rendez-vous supprimé avec succès !');
        }

        return $this->redirectToRoute('app_rendez_vous_index');
    }

  
}
