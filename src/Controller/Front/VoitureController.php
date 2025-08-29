<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\VoitureRepository;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Voiture;
use App\Form\VoitureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;



class VoitureController extends AbstractController
{
    // Liste des voitures
    #[Route('/voitures', name: 'app_voiture')]
    public function index(Request $request, VoitureRepository $voitureRepository, PaginatorInterface $paginator): Response
    {
        $marque = $request->query->get('marque', '');
        $prixMin = $request->query->get('prix_min', '');
        $prixMax = $request->query->get('prix_max', '');
        $isAjax = $request->query->get('ajax', false);
        $page = $request->query->getInt('page', 1);

        $qb = $voitureRepository->createQueryBuilder('v');
        
        // Search by brand
        if ($marque !== '') {
            $qb->andWhere('LOWER(v.marque) LIKE :marque')
                ->setParameter('marque', '%' . strtolower($marque) . '%');
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
            6 // 6 voitures per page
        );

        if ($isAjax) {
            return $this->render('front/voiture/_voitures_list.html.twig', [
                'pagination' => $pagination
            ]);
        }

        return $this->render('front/voiture/voitures.html.twig', [
            'pagination' => $pagination,
            'current_marque' => $marque,
            'current_prix_min' => $prixMin,
            'current_prix_max' => $prixMax
        ]);
    }

   

    #[Route('/voitures/add', name: 'voiture_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
{
    $voiture = new Voiture();
    $form = $this->createForm(VoitureType::class, $voiture);
    $form->handleRequest($request);
    

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData();
        
        if ($imageFile) {
            // Ensure uploads directory exists
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
                $this->addFlash('success', 'Image uploadée avec succès.');
            } catch (FileException $e) {
                $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image: ' . $e->getMessage());
            }
        }
        
        $em->persist($voiture);
        $em->flush();

        $this->addFlash('success', 'Voiture ajoutée avec succès.');
        return $this->redirectToRoute('app_voiture');
    }

    return $this->render('front/voiture/add.html.twig', [
        'form' => $form->createView(),
    ]);
}

    // Détail d'une voiture
    #[Route('/voitures/{id}', name: 'front_voiture_details', methods: ['GET'])]
    public function show(Voiture $voiture): Response
    {
        return $this->render('front/voiture/voiture_details.html.twig', [
            'voiture' => $voiture
        ]);
    }

    #[Route('/voitures/{id}/edit', name: 'edit_voiture', methods: ['GET', 'POST'])]
    public function editVoiture(Request $request, Voiture $voiture, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    
                    // Delete old image if it exists
                    $oldImage = $voiture->getImage();
                    if ($oldImage && file_exists($this->getParameter('uploads_directory').'/'.$oldImage)) {
                        unlink($this->getParameter('uploads_directory').'/'.$oldImage);
                    }
                    
                    $voiture->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                }
            }
            
            $entityManager->flush();

            $this->addFlash('success', 'Voiture modifiée avec succès.');

            return $this->redirectToRoute('app_voiture');
        }

        return $this->renderForm('front/voiture/edit.html.twig', [
            'voiture' => $voiture,
            'form' => $form,
        ]);
    }

    #[Route('/voitures/{id}/delete', name: 'voiture_delete', methods: ['POST'])]
    public function delete(Request $request, Voiture $voiture, EntityManagerInterface $entityManager): Response
    {
        // Delete the image file if it exists
        $image = $voiture->getImage();
        if ($image) {
            $imagePath = $this->getParameter('uploads_directory').'/'.$image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        // Supprimer la voiture (les annonces liées seront supprimées automatiquement grâce à onDelete: CASCADE)
        $entityManager->remove($voiture);
        $entityManager->flush();
    
        $this->addFlash('success', 'La voiture et ses annonces associées ont été supprimées avec succès.');
    
        return $this->redirectToRoute('app_voiture', [], Response::HTTP_SEE_OTHER);
    }

}
