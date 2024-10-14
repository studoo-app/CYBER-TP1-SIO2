<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SwitchController extends AbstractController
{
    #[Route('/switch', name: 'app_switch')]
    public function index(): Response
    {
        $route = match($this->getUser()->getRoles()[0]) {
            "ROLE_ADMIN" => 'app_admin_dashboard',
            "ROLE_USER" => 'app_home',
            default => 'app_login'
        };
        return $this->redirectToRoute($route);
    }
}
