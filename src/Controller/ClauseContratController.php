<?php

namespace App\Controller;

use App\Entity\ClauseContrat;
use App\Entity\Contrat;
use App\Form\ClauseContratType;
use App\Repository\ClauseContratRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\LegalTermGeneratorService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
#[Route('/clause-contrat')]
final class ClauseContratController extends AbstractController
{
    #[Route('/contrat/{contratId}', name: 'app_clause_contrat_index', methods: ['GET'])]
    public function index(int $contratId, ClauseContratRepository $clauseContratRepository): Response
    {
        $clauses = $clauseContratRepository->findByContratOrdered($contratId);
        
        return $this->render('clause_contrat/index.html.twig', [
            'clauses' => $clauses,
            'contratId' => $contratId,
        ]);
    }

    #[Route('/new/{contratId}', name: 'app_clause_contrat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $contratId, EntityManagerInterface $entityManager): Response
    {
        $contrat = $entityManager->getRepository(Contrat::class)->find($contratId);
        
        if (!$contrat) {
            throw $this->createNotFoundException('Contrat non trouvé.');
        }

        // Check if user has access to this contract
        if ($contrat->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous n'avez pas le droit d'ajouter des clauses à ce contrat.");
        }

        $clause = new ClauseContrat();
        $clause->setContrat($contrat);
        
        $form = $this->createForm(ClauseContratType::class, $clause);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($clause);
            $entityManager->flush();

            return $this->redirectToRoute('app_clause_contrat_index', [
                'contratId' => $contratId
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('clause_contrat/new.html.twig', [
            'clause' => $clause,
            'form' => $form,
            'contratId' => $contratId,
        ]);
    }

    #[Route('/{id}', name: 'app_clause_contrat_show', methods: ['GET'])]
    public function show(ClauseContrat $clause): Response
    {
        // Check if user has access to this clause's contract
        if ($clause->getContrat()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à cette clause.");
        }

        return $this->render('clause_contrat/show.html.twig', [
            'clause' => $clause,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_clause_contrat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ClauseContrat $clause, EntityManagerInterface $entityManager): Response
    {
        // Check if user has access to this clause's contract
        if ($clause->getContrat()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous n'avez pas le droit de modifier cette clause.");
        }

        $form = $this->createForm(ClauseContratType::class, $clause);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_clause_contrat_index', [
                'contratId' => $clause->getContrat()->getIdContrat()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('clause_contrat/edit.html.twig', [
            'clause' => $clause,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_clause_contrat_delete', methods: ['POST'])]
    public function delete(Request $request, ClauseContrat $clause, EntityManagerInterface $entityManager): Response
    {
        // Check if user has access to this clause's contract
        if ($clause->getContrat()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous n'avez pas le droit de supprimer cette clause.");
        }

        $contratId = $clause->getContrat()->getIdContrat();

        if ($this->isCsrfTokenValid('delete'.$clause->getIdClause(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($clause);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_clause_contrat_index', [
            'contratId' => $contratId
        ], Response::HTTP_SEE_OTHER);
    }
    #[Route('/generate-content/{type}', name: 'app_clause_contrat_generate_content', methods: ['GET'])]
    public function generateContent(string $type, LegalTermGeneratorService $legalTermGeneratorService): JsonResponse
    {
        $generatedText = $legalTermGeneratorService->generateClause($type);

        return new JsonResponse(['content' => $generatedText]);
    }

    #[Route('/clause/generate', name: 'clause_generate', methods: ['POST'])]
    public function generateClauseContent(Request $request, LegalTermGeneratorService $generatorService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $type = $data['type'] ?? null;
    
        if (!$type) {
            return new JsonResponse(['error' => 'Type manquant'], 400);
        }
    
        $content = $generatorService->generateClause($type);
    
        return new JsonResponse(['content' => $content]);
    }
    
} 