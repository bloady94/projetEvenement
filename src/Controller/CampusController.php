<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/campus', name: 'app_')]
class CampusController extends AbstractController
{
    #[Route('/', name: 'campus')]
    public function index(CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->findAll();

        return $this->render('campus/homeCampus.html.twig', [
            'controller_name' => 'CampusController',
            'campus' => $campus,
        ]);
    }

    #[Route('/add', name: 'addCampus')]
    public function add(): Response
    {
        return $this->render('campus/addCampus.html.twig', [
            'controller_name' => 'CampusController',
        ]);
    }

}
