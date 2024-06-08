<?php

namespace App\Controller;

use App\Repository\InstitutionRepository;
use App\Repository\ReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard', methods: ['GET'])]
    public function index(InstitutionRepository $institutionRepository, ReportRepository $reportRepository): Response
    {
        $institutions = $institutionRepository->findAll();
        $reports = $reportRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'institutions' => $institutions,
            'reports' => $reports,
        ]);
    }
}
