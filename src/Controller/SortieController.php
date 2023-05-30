<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    // Fonction qui d'accueil lorsqu'on clique sur "créer une sortie"
    // C'est un formulaire de création de sortie
    #[Route('/', name: 'app_sortie')]
    public function index(Request $request, SortieRepository $sortieRepository): Response
    {
        $sortie = new Sortie();

        // Création variable qui va créer le formulaire.
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        // Extraction des données de la requête
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $sortieRepository->save($sortie, true);
            $this->addFlash('success', 'La sortie vient d\'être ajoutée');
            return $this->redirectToRoute('main_homepage');
        }

        return $this->render('sortie/sortie.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }
}
