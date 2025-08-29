<?php

namespace App\Controller;

use App\Entity\Entretien;
use App\Entity\RendezVous;
use App\Form\EntretienType;
use App\Repository\EntretienRepository;
use App\Service\EntretienNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
#[Route('/entretien')]
final class EntretienController extends AbstractController
{
    #[Route('/', name: 'app_entretien_index', methods: ['GET'])]
    public function index(EntretienRepository $entretienRepository): Response
    {
        return $this->render('entretien/index.html.twig', [
            'entretiens' => $entretienRepository->findAll(),
            'total_entretiens' => $entretienRepository->count([]),
            'entretiens_termines' => $entretienRepository->count(['status' => Entretien::STATUS_DONE]),
            'entretiens_attente' => $entretienRepository->count(['status' => Entretien::STATUS_PENDING]),
        ]);
    }

    public function calendar(EntretienRepository $entretienRepository): JsonResponse
    {
        $entretiens = $entretienRepository->findAll();
        
        $events = [];
        foreach ($entretiens as $entretien) {
            $events[] = [
                'title' => $entretien->getTypeEntretien(),
                'start' => $entretien->getDatePlanifiee()->format('Y-m-d'),
                'color' => $this->getStatusColor($entretien->getStatus()),
                'extendedProps' => [
                    'id' => $entretien->getId(),
                    'status' => $entretien->getStatus(),
                    'cout' => $entretien->getCout()
                ]
            ];
        }

        return new JsonResponse($events);
    }

    #[Route('/new-from-rdv/{id}', name: 'app_entretien_new_from_rdv', methods: ['GET', 'POST'])]
    public function newFromRendezVous(
        RendezVous $rendezVous, 
        Request $request,
        EntityManagerInterface $entityManager,
        EntretienNotifier $entretienNotifier // Injectez le service
    ): Response {
        // Vérifier seulement si un entretien existe déjà
        if ($rendezVous->getEntretien()) {
            $this->addFlash('error', 'Un entretien existe déjà pour ce rendez-vous');
            return $this->redirectToRoute('app_rendez_vous_show', ['id' => $rendezVous->getId()]);
        }
    
        $entretien = new Entretien();
        $entretien->setRendezVous($rendezVous)
                  ->setDatePlanifiee($rendezVous->getDateRdv())
                  ->setStatus(Entretien::STATUS_PENDING); // Statut défini automatiquement
    
        // Confirmer directement le rendez-vous ici
        $rendezVous->setStatus('confirmé');
    
        $form = $this->createForm(EntretienType::class, $entretien);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entretien);
            $entityManager->flush();

 // Envoyer un SMS si un numéro de téléphone est disponible
   // Envoi de l'email de confirmation
   if ($rendezVous->getEmail()) {
    try {
        dump($rendezVous->getEmail());
        $entretienNotifier->sendEntretienConfirmation(
            $rendezVous->getEmail(),
            [
                'type' => $entretien->getTypeEntretien(),
                'date' => $entretien->getDatePlanifiee()->format('d/m/Y H:i'),
                'cout' => $entretien->getCout()
            ]
        );
        
        $this->addFlash('success', 'Email de confirmation envoyé au client');
    } catch (\Exception $e) {
        $this->addFlash('warning', 'L\'email n\'a pas pu être envoyé: '.$e->getMessage());
    }
}


            
            
            $this->addFlash('success', 'Entretien créé avec succès');
            return $this->redirectToRoute('app_entretien_index');
        }
    
        return $this->render('entretien/new.html.twig', [
            'form' => $form->createView(),
            'rendezVous' => $rendezVous
        ]);
    }
    

    #[Route('/{id}', name: 'app_entretien_show', methods: ['GET'])]
    public function show(Entretien $entretien): Response
    {
        return $this->render('entretien/show.html.twig', [
            'entretien' => $entretien,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_entretien_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Entretien $entretien, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EntretienType::class, $entretien,[
            'is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Le statut n'est pas modifié via le formulaire
            $entityManager->flush();
            
            $this->addFlash('success', 'Entretien modifié avec succès');
            return $this->redirectToRoute('app_entretien_index');
        }

        return $this->render('entretien/edit.html.twig', [
            'entretien' => $entretien,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/mark-done', name: 'app_entretien_mark_done', methods: ['POST'])]
    public function markAsDone(Entretien $entretien, EntityManagerInterface $entityManager): Response
    {
        $entretien->setStatus(Entretien::STATUS_DONE);
        $entityManager->flush();

        $this->addFlash('success', 'Entretien marqué comme terminé');
        return $this->redirectToRoute('app_entretien_show', ['id' => $entretien->getId()]);
    }

    #[Route('/{id}/cancel', name: 'app_entretien_cancel', methods: ['POST'])]
    public function cancel(Entretien $entretien, EntityManagerInterface $entityManager): Response
    {
        $entretien->setStatus(Entretien::STATUS_CANCELED);
        $entityManager->flush();

        $this->addFlash('warning', 'Entretien annulé');
        return $this->redirectToRoute('app_entretien_show', ['id' => $entretien->getId()]);
    }

    #[Route('/{id}', name: 'app_entretien_delete', methods: ['POST'])]
    public function delete(Request $request, Entretien $entretien, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$entretien->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($entretien);
                $entityManager->flush();
                $this->addFlash('success', 'Entretien supprimé avec succès');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression');
            }
        }
    
        return $this->redirectToRoute('app_entretien_index');
    }
    

    private function getStatusColor(string $status): string
    {
        switch ($status) {
            case Entretien::STATUS_DONE:
                return '#28a745'; // Vert
            case Entretien::STATUS_PENDING:
                return '#ffc107'; // Jaune
            case Entretien::STATUS_CANCELED:
                return '#dc3545'; // Rouge
            default:
                return '#007bff'; // Bleu
        }
    }
   

#[Route('/{id}/pdf', name: 'app_entretien_pdf', methods: ['GET'])]
public function generatePdf(Entretien $entretien): Response
{
    // Configurer Dompdf
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf = new Dompdf($options);

    // Générer le HTML du PDF
    $html = $this->renderView('entretien/pdf.html.twig', [
        'entretien' => $entretien
    ]);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Télécharger le fichier
    return new Response($dompdf->stream('entretien_'.$entretien->getId().'.pdf', [
        'Attachment' => true
    ]));
}

}