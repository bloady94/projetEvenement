<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieDescriptionType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    // Fonction qui d'accueil lorsqu'on clique sur "créer une sortie"
    // C'est un formulaire de création de sortie
    #[Route('/add', name: 'add')]
    public function add(Request $request, SortieRepository $sortieRepository, VilleRepository $villeRepository, EtatRepository $etatRepository): Response
    {
        $villes = $villeRepository->findAll();
        $etat = $etatRepository->find(1);
        $sortie = new Sortie();

        // Création variable qui va créer le formulaire.
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        // Extraction des données de la requête
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $sortie->setEtat($etat);
            $sortieRepository->save($sortie, true);
            $this->addFlash('success', 'La sortie vient d\'être ajoutée');
            return $this->redirectToRoute('main_homepage');
        }

        return $this->render('sortie/sortie.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'villes' => $villes
        ]);
    }

    #[Route('/{idSortie}', name: 'show', requirements: ["idSortie" => "\d+"])]
    public function show(int $idSortie, SortieRepository $sortieRepository, ParticipantRepository $participantRepository): Response
    {
        $sortie = $sortieRepository->find($idSortie);
        $sortieParticipants = $sortie->getParticipants();

        return $this->render('sortie/show.html.twig', [
            'sorties' => $sortie,
            'sortieParticipants' => $sortieParticipants
        ]);
    }

    #[Route('/cancel/{idSortie}', name: 'cancel', requirements: ["idSortie" => "\d+"])]
    public function cancel(Request $request, int $idSortie, SortieRepository $sortieRepository, EtatRepository $etatRepository): Response
    {
        $sortie = $sortieRepository->find($idSortie);
        $etat = $etatRepository->find(6);
        $sortieForm = $this->createForm(SortieDescriptionType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $sortie->setEtat($etat);

            $sortieRepository->save($sortie, true);
            return $this->redirectToRoute('main_homepage');
        }

        return $this->render('sortie/annulation.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sorties' => $sortie
        ]);
    }

    #[Route('/update/{idSortie}', name: 'update', requirements: ["idSortie" => "\d+"])]
    public function update(Request $request, int $idSortie, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($idSortie);
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortieRepository->save($sortie, true);
            return $this->redirectToRoute('main_homepage');
        }

        return $this->render('sortie/update.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sortie' => $sortie
        ]);
    }

    #[Route('/publish/{idSortie}}', name: 'publish', requirements: ["idSortie" => "\d+"])]
    public function publish(int $idSortie, SortieRepository $sortieRepository, EtatRepository $etatRepository): Response
    {
        $sortie = $sortieRepository->find($idSortie);
        $etat = $etatRepository->find(2);

            $sortie->setEtat($etat);

            $sortieRepository->save($sortie, true);

        return $this->redirectToRoute('main_homepage');
    }
}
