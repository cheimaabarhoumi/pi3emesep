<?php
// Pi2025/src/Service/ReportingService.php

namespace App\Service;

use App\Repository\SponsoringRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReportingService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SponsoringRepository $sponsoringRepository
    ) {}

    public function getMonthlyStats(): array
    {
        $currentMonth = new \DateTime('first day of this month');
        $lastMonth = new \DateTime('first day of last month');

        return [
            'current_month' => [
                'total' => $this->sponsoringRepository->getMonthlyTotal($currentMonth),
                'new_sponsors' => $this->sponsoringRepository->countNewSponsors($currentMonth),
                'active_sponsors' => $this->sponsoringRepository->countActiveSponsors()
            ],
            'last_month' => [
                'total' => $this->sponsoringRepository->getMonthlyTotal($lastMonth),
                'new_sponsors' => $this->sponsoringRepository->countNewSponsors($lastMonth)
            ]
        ];
    }

    public function generateExportData(): array
    {
        $sponsorings = $this->sponsoringRepository->findAll();
        $data = [];
        
        foreach ($sponsorings as $sponsoring) {
            $data[] = [
                'Partenaire' => $sponsoring->getNomPartenaire(),
                'Montant' => $sponsoring->getMontant(),
                'Date début' => $sponsoring->getDateDebut()->format('d/m/Y'),
                'Date fin' => $sponsoring->getDateFin()->format('d/m/Y'),
                'Type' => $sponsoring->getTypeSponsoring(),
                'État' => $sponsoring->getEtat()
            ];
        }

        return $data;
    }

    public function generateCsvContent(array $data): string
    {
        $output = fopen('php://temp', 'r+');
        fputs($output, "\xEF\xBB\xBF"); // BOM UTF-8
        
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]), ';');
        }
        
        foreach ($data as $row) {
            fputcsv($output, $row, ';');
        }
        
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);
        
        return $content;
    }
}