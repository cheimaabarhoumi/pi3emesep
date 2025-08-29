<?php

namespace App\Controller;

use App\Service\LegalTermGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegalTermGeneratorController extends AbstractController
{
    #[Route('/generate-clause/{contractType}', name: 'generate_clause')]
    public function generate(string $contractType, LegalTermGeneratorService $generator): Response
    {
        $clause = $generator->generateClause($contractType);

        return $this->render('legal_term_generator/show.html.twig', [
            'clause' => $clause,
        ]);
    }
}
