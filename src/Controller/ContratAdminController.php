<?php

namespace App\Controller;

use App\Entity\Contrat;
use App\Entity\ClauseContrat;
use App\Form\ContratType;
use App\Form\ClauseContratType;
use App\Repository\ContratRepository;
use App\Repository\ClauseContratRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SignatureRepository;

#[Route('/admin', name: 'admin_')]
class ContratAdminController extends AbstractController
{
    private ContratRepository $contratRepository;
    private ClauseContratRepository $clauseContratRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ContratRepository $contratRepository, ClauseContratRepository $clauseContratRepository, EntityManagerInterface $entityManager)
    {
        $this->contratRepository = $contratRepository;
        $this->clauseContratRepository = $clauseContratRepository;
        $this->entityManager = $entityManager;
    }

    // ====================== Dashboard ======================
    #[Route('/', name: 'dashboard')]
    public function dashboard(Request $request, \App\Repository\VoitureRepository $voitureRepository, \App\Repository\AnnonceRepository $annonceRepository): Response
    {
        $voitures = $voitureRepository->findAll();
        $annonces = $annonceRepository->findAll();
        return $this->render('admin/dashboard/index.html.twig', [
            'voitures' => $voitures,
            'annonces' => $annonces,
        ]);
    }

    // ====================== Contrat CRUD ======================
    #[Route('/contrat', name: 'contrat_index')]
    public function index(): Response
    {
        return $this->render('admin/contrat/index.html.twig', [
            'contrats' => $this->contratRepository->findAll(),
        ]);
    }

    #[Route('/contrat/new', name: 'contrat_new')]
    public function new(Request $request): Response
    {
        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($contrat);
            $this->entityManager->flush();
            return $this->redirectToRoute('admin_contrat_index');
        }

        return $this->render('admin/contrat/new.html.twig', [
            'contrat' => $contrat,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/contrat/{id}', name: 'contrat_show')]
    public function show(Contrat $contrat, SignatureRepository $signatureRepository): Response
    {
        $signature = $signatureRepository->findOneBy(['contrat' => $contrat]);

        return $this->render('admin/contrat/show.html.twig', [
            'contrat' => $contrat,
            'signature' => $signature,
        ]);
    }

    #[Route('/contrat/{id}/edit', name: 'contrat_edit')]
    public function edit(Request $request, Contrat $contrat): Response
    {
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('admin_contrat_index');
        }

        return $this->render('admin/contrat/edit.html.twig', [
            'contrat' => $contrat,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/contrat/{id}/delete', name: 'contrat_delete', methods: ['POST'])]
    public function delete(Request $request, Contrat $contrat): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contrat->getIdContrat(), $request->getPayload()->getString('_token'))) {
            $this->entityManager->remove($contrat);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_contrat_index');
    }

    // ====================== ClauseContrat CRUD ======================
    #[Route('/contrat/{contratId}/clause', name: 'clause_contrat_index', methods: ['GET'])]
    public function clauseIndex(int $contratId): Response
    {
        $clauses = $this->clauseContratRepository->findBy(
            ['contrat' => $contratId],
            ['ordre' => 'ASC']
        );

        return $this->render('admin/clause_contrat/index.html.twig', [
            'clauses'   => $clauses,
            'contratId' => $contratId,
        ]);
    }

    #[Route('/contrat/{contratId}/clause/new', name: 'clause_contrat_new', methods: ['GET', 'POST'])]
    public function clauseNew(int $contratId, Request $request): Response
    {
        $contrat = $this->entityManager->getRepository(Contrat::class)->find($contratId);
        if (!$contrat) {
            throw $this->createNotFoundException('Contrat introuvable');
        }

        $clause = new ClauseContrat();
        $clause->setContrat($contrat);

        $form = $this->createForm(ClauseContratType::class, $clause);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($clause);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_clause_contrat_index', [
                'contratId' => $contratId,
            ]);
        }

        return $this->render('admin/clause_contrat/new.html.twig', [
            'form'      => $form->createView(),
            'contratId' => $contratId,
        ]);
    }

    #[Route('/contrat/{contratId}/clause/{id}', name: 'clause_contrat_show', methods: ['GET'])]
    public function clauseShow(int $contratId, ClauseContrat $clause): Response
    {
        return $this->render('admin/clause_contrat/show.html.twig', [
            'clause'    => $clause,
            'contratId' => $contratId,
        ]);
    }

    #[Route('/contrat/{contratId}/clause/{id}/edit', name: 'clause_contrat_edit', methods: ['GET', 'POST'])]
    public function clauseEdit(int $contratId, ClauseContrat $clause, Request $request): Response
    {
        $form = $this->createForm(ClauseContratType::class, $clause);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_clause_contrat_index', [
                'contratId' => $contratId,
            ]);
        }

        return $this->render('admin/clause_contrat/edit.html.twig', [
            'form'      => $form->createView(),
            'clause'    => $clause,
            'contratId' => $contratId,
        ]);
    }

    #[Route('/contrat/{contratId}/clause/{id}/delete', name: 'clause_contrat_delete', methods: ['POST'])]
    public function clauseDelete(int $contratId, ClauseContrat $clause, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $clause->getIdClause(), $request->request->get('_token'))) {
            $this->entityManager->remove($clause);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_clause_contrat_index', [
            'contratId' => $contratId,
        ]);
    }

    #[Route('/search', name: 'contrat_search', methods: ['GET'])]
public function search(Request $request, ContratRepository $contratRepository): Response
{
    $term = $request->query->get('q', '');
    $results = $contratRepository->searchContracts($term);

    return $this->render('admin/contrat/_search_results.html.twig', [
        'contrats' => $results
    ]);
}

}
