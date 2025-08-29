<?php

namespace App\Controller;

use App\Entity\Sponsoring;
use App\Form\SponsoringType;
use App\Repository\SponsoringRepository;
use App\Service\ReportingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Event\Subscriber\Paginate\Doctrine\ORM\QueryBuilderSubscriber;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/sponsoring')]
class SponsoringController extends AbstractController
{
    #[Route('/', name: 'app_sponsoring_index', methods: ['GET'])]
    public function index(
        Request $request,
        SponsoringRepository $sponsoringRepository,
        PaginatorInterface $paginator,
        ReportingService $reportingService
    ): Response {
        $searchTerm = $request->query->get('search');
        $etat = $request->query->get('etat');
        $queryBuilder = $sponsoringRepository->search($searchTerm, $etat);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10,
            [
                'defaultSortFieldName' => 's.dateDebut',
                'defaultSortDirection' => 'desc'
            ]
        );

        $stats = $reportingService->getMonthlyStats();

        return $this->render('sponsoring/index.html.twig', [
            'pagination' => $pagination,
            'stats' => $stats,
            'searchTerm' => $searchTerm,
            'currentEtat' => $etat
        ]);
    }

    #[Route('/dashboard', name: 'app_sponsoring_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        return $this->render('sponsoring/dashboard.html.twig');
    }

    #[Route('/new', name: 'app_sponsoring_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $sponsoring = new Sponsoring();
        $form = $this->createForm(SponsoringType::class, $sponsoring);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Redirige vers la route checkout (POST)
            return $this->redirectToRoute('app_sponsoring_checkout', [], 307);
        }

        return $this->render('sponsoring/new.html.twig', [
            'sponsoring' => $sponsoring,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/checkout', name: 'app_sponsoring_checkout', methods: ['POST'])]
    public function checkout(Request $request, SessionInterface $session): Response
    {
        // Récupère les données du formulaire
        $formData = $request->request->all('sponsoring');
        // Stocke temporairement en session
        $session->set('pending_sponsoring', $formData);

        // Montant en centimes
        $amount = (float) $formData['montant'] * 100;

        \Stripe\Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        $checkoutSession = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Sponsoring pour ' . $formData['nomPartenaire'],
                    ],
                    'unit_amount' => (int) $amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_sponsoring_success', [], 0),
            'cancel_url' => $this->generateUrl('app_sponsoring_cancel', [], 0),
        ]);

        return $this->redirect($checkoutSession->url, 303);
    }

    #[Route('/success', name: 'app_sponsoring_success')]
    public function success(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $formData = $session->get('pending_sponsoring');
        if (!$formData) {
            $this->addFlash('danger', 'Aucune donnée de sponsoring trouvée.');
            return $this->redirectToRoute('app_sponsoring_new');
        }

        // Création de l'entité Sponsoring
        $sponsoring = new Sponsoring();
        $sponsoring->setNomPartenaire($formData['nomPartenaire']);
        $sponsoring->setMontant((float) $formData['montant']);
        $sponsoring->setDateDebut(new \DateTime($formData['dateDebut']));
        $sponsoring->setDateFin(new \DateTime($formData['dateFin']));
        $sponsoring->setTypeSponsoring($formData['typeSponsoring']);
        $sponsoring->setEtat($formData['etat']);

        $entityManager->persist($sponsoring);
        $entityManager->flush();

        $session->remove('pending_sponsoring');

        $this->addFlash('success', 'Le paiement a été validé et le sponsoring créé avec succès.');
        return $this->redirectToRoute('app_sponsoring_index');
    }

    #[Route('/cancel', name: 'app_sponsoring_cancel')]
    public function cancel(SessionInterface $session): Response
    {
        $session->remove('pending_sponsoring');
        $this->addFlash('warning', 'Le paiement a été annulé.');
        return $this->redirectToRoute('app_sponsoring_new');
    }

    #[Route('/{id}', name: 'app_sponsoring_show', methods: ['GET'])]
    public function show(Sponsoring $sponsoring): Response
    {
        return $this->render('sponsoring/show.html.twig', [
            'sponsoring' => $sponsoring,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sponsoring_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sponsoring $sponsoring, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SponsoringType::class, $sponsoring);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le sponsoring a été modifié avec succès.');
            return $this->redirectToRoute('app_sponsoring_index');
        }

        return $this->render('sponsoring/edit.html.twig', [
            'sponsoring' => $sponsoring,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_sponsoring_delete', methods: ['POST'])]
    public function delete(Request $request, Sponsoring $sponsoring, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sponsoring->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sponsoring);
            $entityManager->flush();
            $this->addFlash('success', 'Le sponsoring a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_sponsoring_index');
    }

    #[Route('/{id}/change-status', name: 'app_sponsoring_change_status', methods: ['POST'])]
    public function changeStatus(Request $request, Sponsoring $sponsoring, EntityManagerInterface $entityManager): Response
    {
        $newStatus = $request->request->get('status');
        if (in_array($newStatus, ['Actif', 'Terminé', 'Annulé'])) {
            $sponsoring->setEtat($newStatus);
            $entityManager->flush();
            $this->addFlash('success', 'Le statut du sponsoring a été mis à jour.');
        }

        return $this->redirectToRoute('app_sponsoring_show', ['id' => $sponsoring->getId()]);
    }
}