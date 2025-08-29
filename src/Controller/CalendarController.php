<?php

namespace App\Controller;

use App\Repository\RendezVousRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    #[Route('/api/calendar/slots', name: 'calendar_available_slots')]
    public function getAvailableSlots(Request $request, RendezVousRepository $repository): JsonResponse
    {
        $start = new \DateTime($request->query->get('start'));
        $end = new \DateTime($request->query->get('end'));

        $rendezVous = $repository->findBetweenDates($start, $end);
        
        $events = [];
        foreach ($rendezVous as $rdv) {
            $events[] = [
                'id' => $rdv->getId(),
                'title' => $rdv->getDescription(),
                'start' => $rdv->getDateRdv()->format('Y-m-d H:i:s'),
                'end' => $rdv->getDateRdv()->modify('+1 hour')->format('Y-m-d H:i:s'),
                'color' => $rdv->getStatus() === 'confirmé' ? '#28a745' : '#ffc107',
                'textColor' => '#ffffff',
                'allDay' => false
            ];
        }

        return $this->json($events);
    }
}