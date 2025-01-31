<?php

namespace App\Controller;

use App\Service\Logger\AuditLogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly AuditLogService $service
    )
    {
    }

    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        $logs = $this->service->getLogs();

        dump($logs);

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'logs'=>  $logs,
        ]);
    }
}
