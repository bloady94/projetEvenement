<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_USER")]
class MainController extends AbstractController
{
    #[Route('/homepage', name: 'main_homepage')]
    public function index(SortieRepository $sortieRepository, ParticipantRepository $participantRepository): Response
    {
        //trouver toutes les sorties
        $sorties = $sortieRepository->findAll();

        //initialiser le compte des participants à 0, et l'inscrit à une chaîne de caractères vide
        $count = 0;
        $inscrit = "";

        //trouver le participant correspondant à la personne connectée
        $user = $this->getUser()->getId();
        $participant = $participantRepository->findOneBy(['username' => $user]);

        return $this->render('main/index.html.twig', [
            'controller_name' => 'SortieController',
            'sorties' => $sorties,
            'participant' => $participant,
            'count' => $count,
            "inscrit" => $inscrit
        ]);
    }



//        if($sortie->getDateHeureDebut() <= date(now))


//        Si la sortie = ouverte(2) ALors
//	'vous pouvez vous inscrire'
//
//Si la date sortie <= à la date de maintenant Alors
//	 'L'inscription à la sortie est dépassée'
//
//
//Si nb participant est >= au nb d'inscription max Alors :
//	'Pas possible trop de monde'
//
//Si tu es deja inscrit Alors :
//	'tu es dja inscrit'
//
//On ajoute le participant à la sortie



//       return $this->redirectToRoute('main_homepage');

}
