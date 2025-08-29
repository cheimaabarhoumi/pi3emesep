<?php
// Pi2025/src/Controller/Api/DashboardApiController.php

namespace App\Controller\Api;

use App\Repository\SponsoringRepository;
use App\Service\ReportingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class DashboardApiController extends AbstractController
{
    public function __construct(
        private ReportingService $reportingService
    ) {}

    #[Route('/dashboard/stats', name: 'dashboard_stats', methods: ['GET'])]
    public function getDashboardStats(): JsonResponse
    {
        return $this->json([
            'stats' => $this->reportingService->getMonthlyStats(),
            'type_distribution' => $this->sponsoringRepository->getTypeDistribution(),
            'total_amount' => $this->sponsoringRepository->getTotalAmount()
        ]);
    }

    #[Route('/dashboard/chart-data', name: 'dashboard_chart_data', methods: ['GET'])]
    public function getChartData(): JsonResponse
    {
        $currentYear = (new \DateTime())->format('Y');
        $monthlyData = [];

        // Données mensuelles pour l'année en cours
        for ($month = 1; $month <= 12; $month++) {
            $date = new \DateTime("$currentYear-$month-01");
            $monthlyData[$date->format('M')] = $this->sponsoringRepository->getMonthlyTotal($date);
        }

        return $this->json([
            'monthly_revenue' => $monthlyData,
            'sponsorship_types' => $this->sponsoringRepository->getTypeDistribution()
        ]);
    }

 
}
