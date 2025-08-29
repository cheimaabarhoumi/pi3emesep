<?php
namespace App\Controller;
use App\Entity\Signature;
use App\Entity\Contrat;
use App\Form\SignatureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ContratRepository;

class SignatureController extends AbstractController
{
    #[Route('/signature/{idContrat}', name: 'app_signature')]
public function signature(Request $request, EntityManagerInterface $em, int $idContrat, ContratRepository $contratRepository): Response
{
    $contrat = $contratRepository->find($idContrat);
    if (!$contrat) {
        throw $this->createNotFoundException('Contrat non trouvé.');
    }

    if ($request->isMethod('POST')) {
        $base64 = $request->request->get('base64');

        if (!$base64) {
            $this->addFlash('error', 'Veuillez signer le contrat.');
            return $this->redirectToRoute('app_signature', ['idContrat' => $idContrat]);
        }

        $signature = new Signature();
        $signature->setBase64($base64);
        $signature->setSignedAt(new \DateTimeImmutable());
        $signature->setIp($request->getClientIp());
        $signature->setContrat($contrat);

        $em->persist($signature);
        $em->flush();

        $this->addFlash('success', 'Signature enregistrée avec succès.');
        return $this->redirectToRoute('app_contrat_show', ['idContrat' => $contrat->getIdContrat()]);
    }

    return $this->render('signature/sign.html.twig', [
        'contrat' => $contrat,
    ]);
}

}
    

