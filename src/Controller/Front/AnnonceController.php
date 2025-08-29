<?php

namespace App\Controller\Front;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\VoitureRepository;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // <-- Import correct
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Endroid\QrCode\QrCode; // <-- Ajout de l'import correct pour QrCode
use Endroid\QrCode\Writer\PngWriter; 
use Symfony\Component\Mailer\MailerInterface; // Import correct pour MailerInterface
use Symfony\Component\Mime\Email;
use Knp\Component\Pager\PaginatorInterface;

class AnnonceController extends AbstractController
{
    #[Route('/annonces', name: 'front_annonces')]
    public function index(Request $request, AnnonceRepository $annonceRepository, PaginatorInterface $paginator): Response
    {
        $nom = $request->query->get('nom', '');
        $prixMin = $request->query->get('prix_min', '');
        $prixMax = $request->query->get('prix_max', '');
        $page = $request->query->getInt('page', 1);

        $qb = $annonceRepository->createQueryBuilder('a')
            ->leftJoin('a.voiture', 'v')
            ->where('a.valider = :valider')
            ->setParameter('valider', true);
        
        // Search by announcement title or voiture brand/model
        if ($nom !== '') {
            $qb->andWhere('LOWER(a.titre) LIKE :nom OR LOWER(v.marque) LIKE :nom OR LOWER(v.modele) LIKE :nom')
                ->setParameter('nom', '%' . strtolower($nom) . '%');
        }
        
        // Filter by minimum price
        if ($prixMin !== '' && is_numeric($prixMin)) {
            $qb->andWhere('v.prix >= :prixMin')
                ->setParameter('prixMin', (int)$prixMin);
        }
        
        // Filter by maximum price
        if ($prixMax !== '' && is_numeric($prixMax)) {
            $qb->andWhere('v.prix <= :prixMax')
                ->setParameter('prixMax', (int)$prixMax);
        }

        $pagination = $paginator->paginate(
            $qb,
            $page,
            8 // 8 annonces per page
        );

        return $this->render('front/annonce/annonces.html.twig', [
            'pagination' => $pagination,
            'current_nom' => $nom,
            'current_prix_min' => $prixMin,
            'current_prix_max' => $prixMax
        ]);
    }

    #[Route('/annonces/{id}', name: 'front_annonce_details')]
    public function show($id, AnnonceRepository $annonceRepository, Request $request): Response
    {   
        // Récupérer l'annonce depuis la base de données
        $annonce = $annonceRepository->find($id);

        // Si l'annonce n'existe pas, afficher une erreur
        if (!$annonce) {
            throw $this->createNotFoundException('L\'annonce n\'existe pas.');
        }

        // Générer l'URL complète vers cette page d'annonce
        $annonceUrl = $request->getSchemeAndHttpHost() . $this->generateUrl('front_annonce_details', ['id' => $id]);

        // Créer un format vCard pour l'annonce qui sera bien affiché par les scanners QR
        $vCardData = "BEGIN:VCARD\n";
        $vCardData .= "VERSION:3.0\n";
        $vCardData .= "FN:" . $annonce->getTitre() . "\n";
        $vCardData .= "ORG:Neo Cars\n";
        $vCardData .= "TITLE:" . $annonce->getVoiture()->getMarque() . " " . $annonce->getVoiture()->getModele() . "\n";
        
        // Construire les notes avec toutes les informations
        $notes = "🚗 VÉHICULE: " . $annonce->getVoiture()->getMarque() . " " . $annonce->getVoiture()->getModele() . "\\n";
        $notes .= "📅 ANNÉE: " . $annonce->getVoiture()->getAnnee() . "\\n";
        
        if ($annonce->getVoiture()->getKilometrage()) {
            $notes .= "🛣️ KILOMÉTRAGE: " . number_format($annonce->getVoiture()->getKilometrage(), 0, ',', ' ') . " km\\n";
        }
        
        if ($annonce->getVoiture()->getCarburant()) {
            $notes .= "⛽ CARBURANT: " . $annonce->getVoiture()->getCarburant() . "\\n";
        }
        
        if ($annonce->getVoiture()->getBoiteVitesse()) {
            $notes .= "⚙️ TRANSMISSION: " . $annonce->getVoiture()->getBoiteVitesse() . "\\n";
        }
        
        if ($annonce->getVoiture()->getPrix()) {
            $notes .= "💰 PRIX: " . number_format($annonce->getVoiture()->getPrix(), 0, ',', ' ') . " TND\\n";
        } else {
            $notes .= "💰 PRIX: Sur demande\\n";
        }
        
        if ($annonce->getDescription()) {
            $description = str_replace(["\n", "\r"], " ", $annonce->getDescription());
            $notes .= "📝 DESCRIPTION: " . substr($description, 0, 150);
            if (strlen($description) > 150) {
                $notes .= "...";
            }
            $notes .= "\\n";
        }
        
        $notes .= "🌐 PLUS D'INFOS: " . $annonceUrl;
        
        $vCardData .= "NOTE:" . $notes . "\n";
        $vCardData .= "URL:" . $annonceUrl . "\n";
        $vCardData .= "END:VCARD";

        // Générer un QR code avec les données vCard de l'annonce
        $qrCode = new QrCode($vCardData);
        
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Utiliser getDataUri() pour obtenir le QR code encodé en base64
        $qrCodeBase64 = $result->getDataUri();

        // Renvoyer la vue avec l'annonce et le QR code
        return $this->render('front/annonce/annonce_details.html.twig', [
            'annonce' => $annonce,
            'qrCode' => $qrCodeBase64,
            'annonceUrl' => $annonceUrl, // Passer l'URL pour d'autres utilisations potentielles
        ]);
    }








    
    #[Route('/annonces/create/{voitureId}', name: 'create_annonce')]
    public function createAnnonce(int $voitureId, Request $request, VoitureRepository $voitureRepository, EntityManagerInterface $em, MailerInterface $mailer, \App\Repository\UserRepository $userRepository): Response
    {
        // Récupère la voiture correspondante
        $voiture = $voitureRepository->find($voitureId);
    
        if (!$voiture) {
            throw $this->createNotFoundException('La voiture n\'existe pas.');
        }
    
        // Créer une nouvelle instance d'annonce
        $annonce = new Annonce();
        $annonce->setVoiture($voiture);
    
        // Créer le formulaire
        $form = $this->createForm(AnnonceType::class, $annonce, [
            'voitures' => [$voiture] // Passer la voiture comme option dans le formulaire
        ]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer l'annonce dans la base de données
            $em->persist($annonce);
            $em->flush();
    
            // Envoi d'un email à tous les admins
            $admins = $userRepository->searchUsers('', 'admin');
            $this->addFlash('info', count($admins) . ' admin(s) found for notification.');
            foreach ($admins as $admin) {
                $email = (new Email())
                    ->from('houssem.benmabrouk12@gmail.com')
                    ->to($admin->getEmail())
                    ->subject('Nouvelle annonce en attente de confirmation')
                    ->html('<p>Une nouvelle annonce a été ajoutée et attend votre confirmation.</p><p>Titre : ' . $annonce->getTitre() . '</p>');
                $mailer->send($email);
            }
    
            // Message flash de succès
            $this->addFlash('success', 'Annonce créée avec succès.');
    
            return $this->redirectToRoute('front_annonces');
        }
    
        return $this->render('front/annonce/create.html.twig', [
            'form' => $form->createView(),
            'voiture' => $voiture
        ]);
    }

#[Route('/annonces/edit/{id}', name: 'edit_annonce')]
public function editAnnonce(int $id, Request $request, AnnonceRepository $annonceRepository, EntityManagerInterface $em): Response
{
    $annonce = $annonceRepository->find($id);
    

    if (!$annonce) {
        throw $this->createNotFoundException('L\'annonce n\'existe pas.');
    }

    $form = $this->createForm(AnnonceType::class, $annonce, [
    'voitures' => [$annonce->getVoiture()]
]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($annonce);
        $em->flush();

        $this->addFlash('success', 'Annonce modifiée avec succès.');

        return $this->redirectToRoute('front_annonces');
    }

    return $this->render('front/annonce/edit.html.twig', [
        'form' => $form->createView(),
        'voiture' => $annonce->getVoiture() 
    ]);
}


#[Route('/annonces/{id}/delete', name: 'annonce_delete', methods: ['POST'])]
public function deleteAnnonce(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
{
    
        $entityManager->remove($annonce);
        $entityManager->flush();
        
        $this->addFlash('success', 'Annonce supprimée avec succès.');
    

    return $this->redirectToRoute('front_annonces', [], Response::HTTP_SEE_OTHER);
}

#[Route('/annonces/{id}/pdf', name: 'front_annonce_pdf')]
public function generateAnnoncePdf(Annonce $annonce, Request $request): Response
{
    $dompdf = new Dompdf();

    // Générer l'URL complète vers cette page d'annonce pour référence
    $annonceUrl = $request->getSchemeAndHttpHost() . $this->generateUrl('front_annonce_details', ['id' => $annonce->getId()]);
    
    // Créer un format vCard pour l'annonce qui sera bien affiché par les scanners QR
    $vCardData = "BEGIN:VCARD\n";
    $vCardData .= "VERSION:3.0\n";
    $vCardData .= "FN:" . $annonce->getTitre() . "\n";
    $vCardData .= "ORG:Neo Cars\n";
    $vCardData .= "TITLE:" . $annonce->getVoiture()->getMarque() . " " . $annonce->getVoiture()->getModele() . "\n";
    
    // Construire les notes avec toutes les informations
    $notes = "🚗 VÉHICULE: " . $annonce->getVoiture()->getMarque() . " " . $annonce->getVoiture()->getModele() . "\\n";
    $notes .= "📅 ANNÉE: " . $annonce->getVoiture()->getAnnee() . "\\n";
    
    if ($annonce->getVoiture()->getKilometrage()) {
        $notes .= "🛣️ KILOMÉTRAGE: " . number_format($annonce->getVoiture()->getKilometrage(), 0, ',', ' ') . " km\\n";
    }
    
    if ($annonce->getVoiture()->getCarburant()) {
        $notes .= "⛽ CARBURANT: " . $annonce->getVoiture()->getCarburant() . "\\n";
    }
    
    if ($annonce->getVoiture()->getBoiteVitesse()) {
        $notes .= "⚙️ TRANSMISSION: " . $annonce->getVoiture()->getBoiteVitesse() . "\\n";
    }
    
    if ($annonce->getVoiture()->getPrix()) {
        $notes .= "💰 PRIX: " . number_format($annonce->getVoiture()->getPrix(), 0, ',', ' ') . " TND\\n";
    } else {
        $notes .= "💰 PRIX: Sur demande\\n";
    }
    
    if ($annonce->getDescription()) {
        $description = str_replace(["\n", "\r"], " ", $annonce->getDescription());
        $notes .= "📝 DESCRIPTION: " . substr($description, 0, 120);
        if (strlen($description) > 120) {
            $notes .= "...";
        }
        $notes .= "\\n";
    }
    
    $notes .= "🌐 PLUS D'INFOS: " . $annonceUrl;
    
    $vCardData .= "NOTE:" . $notes . "\n";
    $vCardData .= "URL:" . $annonceUrl . "\n";
    $vCardData .= "END:VCARD";
    
    $qrCode = new QrCode($vCardData);
    
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    $qrCodeBase64 = $result->getDataUri(); // QR Code en base64

    // Récupérer l'image de la voiture
    $imagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $annonce->getVoiture()->getImage();
    $imageBase64 = null;
    if (file_exists($imagePath)) {
        $imageData = base64_encode(file_get_contents($imagePath));
        $mimeType = mime_content_type($imagePath);
        $imageBase64 = 'data:' . $mimeType . ';base64,' . $imageData;
    }

    // Personnaliser le contenu HTML du PDF
    $html = $this->renderView('front/annonce/annonce_pdf.html.twig', [
        'annonce' => $annonce,
        'imageBase64' => $imageBase64,
        'qrCode' => $qrCodeBase64,  // Ajouter le QR code en base64
        'annonceUrl' => $annonceUrl, // Ajouter l'URL pour référence
    ]);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return new Response(
        $dompdf->output(),
        200,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="annonce_'.$annonce->getId().'.pdf"',
        ]
    );
}



#[Route('/admin/gestion-annonce/valider/{id}', name: 'valider_annonce')]
public function validerAnnonce(EntityManagerInterface $entityManager, Annonce $annonce): Response
{
    $annonce->setValider(true);
    $entityManager->flush();

    return $this->redirectToRoute('gestion_annonce');
}

}