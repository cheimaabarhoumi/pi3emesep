<?php
namespace App\Controller\back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AnnonceRepository;
use App\Entity\Annonce;
use App\Repository\VoitureRepository;
use App\Entity\Voiture;
use App\Form\AnnonceType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Form\VoitureType;

class GestionAnnonceController extends AbstractController {

    #[Route('/admin/gestion-annonce/pdf', name: 'annonce_pdf')]
    public function pdfAllAnnonces(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findAll();

        // Configure Dompdf

    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isRemoteEnabled', true); // Allow Dompdf to fetch remote images
    $dompdf = new Dompdf($options);

        // Render the PDF template
        $html = $this->renderView('back/annonce/pdf_annonces.html.twig', [
            'annonces' => $annonces
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output the generated PDF (force download)
        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="annonces.pdf"'
            ]
        );
    }


    #[Route('/admin/gestion-annonce', name: 'gestion_annonce')]
    public function accueilAdminannonce(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findAll(); 
        return $this->render('back/annonce/gestion_annonce.html.twig', [
            'annonces' => $annonces, 
        ]);
        
    }
    #[Route('/admin/gestion-annonce/delete/{id}', name: 'delete_annonce')]
    public function deleteAnnonce(EntityManagerInterface $entityManager, Annonce $annonce): Response
    {
        // Supprimer l'annonce
        $entityManager->remove($annonce);
        $entityManager->flush();

        // Rediriger vers la liste des annonces
        return $this->redirectToRoute('gestion_annonce');
    }
    #[Route('/admin/gestion-voiture', name: 'gestion_voiture')]
    public function accueilAdmin(VoitureRepository $voitureRepository): Response
    {
        $voitures = $voitureRepository->findAll();
        return $this->render('back/voiture/gestion_voiture.html.twig', [
            'voitures' => $voitures,
        ]);
    }

    // Admin add voiture (serves a fragment for modal and handles POST)
    #[Route('/admin/voitures/add', name: 'admin_voiture_add', methods: ['GET','POST'])]
    public function adminAddVoiture(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $voiture = new Voiture();
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $uploadsDir = $this->getParameter('uploads_directory');
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0755, true);
                }
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move($uploadsDir, $newFilename);
                    $voiture->setImage($newFilename);
                } catch (FileException $e) {
                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse(['success' => false, 'message' => 'Erreur upload image: '.$e->getMessage()], 500);
                    }
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $em->persist($voiture);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true, 'message' => 'Voiture ajoutée avec succès.']);
            }

            $this->addFlash('success', 'Voiture ajoutée avec succès.');
            return $this->redirectToRoute('gestion_voiture');
        }

        // If AJAX POST with invalid form, return rendered fragment so client can replace it
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $html = $this->renderView('back/voiture/_modal_form.html.twig', [
                'form' => $form->createView(),
            ]);
            return new Response($html, 400);
        }

        // For GET (AJAX) or normal request render fragment
        return $this->render('back/voiture/_modal_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Admin add annonce (serves a fragment for modal and handles POST)
    #[Route('/admin/annonces/add', name: 'admin_annonce_add', methods: ['GET','POST'])]
    public function adminAddAnnonce(Request $request, EntityManagerInterface $em, VoitureRepository $voitureRepository): Response
    {
        $annonce = new Annonce();
        $voitures = $voitureRepository->findAll();
        $form = $this->createForm(AnnonceType::class, $annonce, [
            'voitures' => $voitures,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($annonce);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true, 'message' => 'Annonce créée avec succès.']);
            }

            $this->addFlash('success', 'Annonce créée avec succès.');
            return $this->redirectToRoute('gestion_annonce');
        }

        // If AJAX POST with invalid form, return the rendered fragment so client can replace it
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $html = $this->renderView('back/annonce/_modal_form.html.twig', [
                'form' => $form->createView(),
            ]);
            return new Response($html, 400);
        }

        // For GET (AJAX) or normal request render fragment
        return $this->render('back/annonce/_modal_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/admin/gestion-annonce/valider/{id}', name: 'valider_annonce')]
    public function validerAnnonce(EntityManagerInterface $entityManager, Annonce $annonce, \Symfony\Component\Mailer\MailerInterface $mailer, \App\Repository\UserRepository $userRepository): Response
    {
        $annonce->setValider(true);
        $entityManager->flush();

        $users = $userRepository->findAll();
        foreach ($users as $user) {
            $email = (new \Symfony\Component\Mime\Email())
                ->from('no-reply@votre-garage.com')
                ->to($user->getEmail())
                ->subject('Annonce validée')
                ->html('<p>L\'annonce <b>' . $annonce->getTitre() . '</b> a été validée avec succès !</p>');
            $mailer->send($email);
        }

        return $this->redirectToRoute('gestion_annonce');
    }




}
