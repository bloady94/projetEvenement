<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_USER")]
class MainController extends AbstractController
{
    #[Route('/homepage', name: 'main_homepage')]
    public function index(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'SortieController',
            'sorties' => $sorties,
        ]);
    }
}
