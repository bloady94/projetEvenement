<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/lieu', name: 'lieu_')]
class LieuController extends AbstractController
{
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/add', name: 'add')]
    public function add(
        LieuRepository $lieuRepository,
        Request $request,
    ): Response
    {
        $lieu = new Lieu();

        $lieuForm = $this->createForm(LieuType::class, $lieu);

        $lieuForm->handleRequest($request);

        if($lieuForm->isSubmitted() && $lieuForm->isValid()){

            $lieuRepository->save($lieu, true);
            $this->addFlash('success', 'Le lieu vient d\'être ajouté!');
            return $this->redirectToRoute('sortie_add');
        }

        return $this->render('lieu/add.html.twig', [
            'lieuForm' => $lieuForm->createView()
        ]);
    }
}
