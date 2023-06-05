<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\LieuType;
use App\Form\SortieDescriptionType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_USER")]
#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    // Fonction qui d'accueil lorsqu'on clique sur "créer une sortie"
    // C'est un formulaire de création de sortie
    #[Route('/add', name: 'add')]
    public function add(Request $request,
                        SortieRepository $sortieRepository,
                        VilleRepository $villeRepository,
                        EtatRepository $etatRepository,
                        ParticipantRepository $participantRepository,
                        LieuRepository $lieuRepository
    ): Response
    {
        $villes = $villeRepository->findAll();
        $etat = $etatRepository->find(1);
        $user = $this->getUser()->getUserIdentifier();
        $organisateur = $participantRepository->findOneBy(['username' => $user]);
        $sortie = new Sortie();

//        Ajax request

//        $lieu = new Lieu();
//
//        $lieuForm = $this->createForm(LieuType::class, $lieu);
//
//        $lieuForm->handleRequest($request);
//
//        if($lieuForm->isSubmitted() && $lieuForm->isValid()){
//
//            $lieuRepository->save($lieu, true);
//            $this->addFlash('success', 'Le lieu vient d\'être ajouté!');
//            return $this->redirectToRoute('sortie_add');
//        }
        // Récupération des données de l'utilisateur.
        $utilisateur = $this->getUser();

        // Création variable qui va créer le formulaire.
        $sortieForm = $this->createForm(SortieType::class, $sortie, [
            'utilisateur' =>$utilisateur,
        ]);

        // Extraction des données de la requête
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $sortie->setOrganisateur($organisateur);
            $sortie->setEtat($etat);
            $sortieRepository->save($sortie, true);
            $this->addFlash('success', 'La sortie vient d\'être ajoutée');
            return $this->redirectToRoute('main_homepage');
        }

        return $this->render('sortie/sortie.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'villes' => $villes,
            //'lieuForm' => $lieuForm->createView()
        ]);
    }

    #[Route('/{idSortie}', name: 'show', requirements: ["idSortie" => "\d+"])]
    public function show(int $idSortie, SortieRepository $sortieRepository): Response
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
        $utilisateur = $this->getUser();
        $sortie = $sortieRepository->find($idSortie);
        $organisateur = $sortie->getOrganisateur();
        //chercher l'état à l'id 6 "Annulée"
        $etat = $etatRepository->find(6);
        $sortieForm = $this->createForm(SortieDescriptionType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($organisateur === $utilisateur) {
            if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
                //modifier l'état de la sortie à "Annulée"
                $sortie->setEtat($etat);

                $sortieRepository->save($sortie, true);
                return $this->redirectToRoute('main_homepage');
            }
        }

        return $this->render('sortie/annulation.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sorties' => $sortie
        ]);
    }

    #[Route('/update/{idSortie}', name: 'update', requirements: ["idSortie" => "\d+"])]
    public function update(Request $request, int $idSortie, SortieRepository $sortieRepository): Response
    {
        $utilisateur = $this->getUser();
        $sortie = $sortieRepository->find($idSortie);
        $organisateur = $sortie->getOrganisateur();

        $sortieForm = $this->createForm(SortieType::class, $sortie, [
            'utilisateur' =>$utilisateur,
        ]);
        $sortieForm->handleRequest($request);
        if ($organisateur === $utilisateur){
            if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
                $sortieRepository->save($sortie, true);
                return $this->redirectToRoute('main_homepage');
            }
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
        //chercher l'état à l'id 2 "Ouverte"
        $etat = $etatRepository->find(2);

        //modifier l'état de la sortie à "Ouverte"
            $sortie->setEtat($etat);

            $sortieRepository->save($sortie, true);

        return $this->redirectToRoute('main_homepage');
    }

    #[Route('/inscrire/{id}', name: 'inscrire', requirements: ["id" => "\d+"])]
    public function inscrire(int $id, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        //Récupération de l'ID de la personne authentifiée


        $sortie = $sortieRepository->find($id);
        $user = $this->getUser()->getUserIdentifier();
        $participant = $participantRepository->findOneBy(['username' => $user]);

        if ($sortie->getEtat()->getId() == 2) {
            //ajouter l'enregistrement dans la bdd
            $sortie->addParticipant($participant);

            //$message = "tu peux t'inscrire khey.";
        }
        //valider la commande
        $entityManager->flush();
        //$sorties = $sortieRepository->findAll();
        return $this->redirectToRoute('main_homepage');
    }


    #[Route('/desinscrire/{id}', name: 'desinscrire', requirements: ["id" => "\d+"])]
    public function desinscrire(int $id, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);
        $user = $this->getUser()->getUserIdentifier();
        $participant = $participantRepository->findOneBy(['username' => $user]);

        if ($sortie->getEtat()->getId() == 2) {
            //enlever l'enregistrement dans la bdd
            $sortie->removeParticipant($participant);
        }
        //valider la commande
        $entityManager->flush();

        return $this->redirectToRoute('main_homepage');
    }
}
