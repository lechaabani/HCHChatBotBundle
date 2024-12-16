<?php

namespace HCH\ChatBotBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use HCH\ChatBotBundle\Service\MonitoringService;
use HCH\ChatBotBundle\Service\AnalyticsService;

#[Route('/admin/chatbot')]
class DashboardController extends AbstractController
{
    public function __construct(
        private MonitoringService $monitoringService,
        private AnalyticsService $analyticsService
    ) {}

    #[Route('', name: 'hch_chatbot_admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('@HCHChatBot/admin/dashboard.html.twig', [
            'stats' => $this->analyticsService->getDashboardStats(),
            'providers' => $this->monitoringService->getProvidersStatus()
        ]);
    }

    #[Route('/providers', name: 'hch_chatbot_admin_providers')]
    public function providers(): Response
    {
        return $this->render('@HCHChatBot/admin/providers.html.twig', [
            'providers' => $this->monitoringService->getDetailedProvidersInfo()
        ]);
    }

    #[Route('/logs', name: 'hch_chatbot_admin_logs')]
    public function logs(): Response
    {
        return $this->render('@HCHChatBot/admin/logs.html.twig', [
            'logs' => $this->monitoringService->getRecentLogs()
        ]);
    }
} 