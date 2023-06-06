<?php

namespace App\Controller;

use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use DateInterval;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_USER")]
class MainController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/homepage', name: 'main_homepage')]
    public function index(SortieRepository $sortieRepository, ParticipantRepository $participantRepository, EtatRepository $etatRepository): Response
    {
        //trouver toutes les sorties
        $sorties = $sortieRepository->findAll();

        //établir une variable pour chaque etat
        $etatArchive = $etatRepository->find(7);
        $etatCloture = $etatRepository->find(3);
        $etatPassee = $etatRepository->find(5);
        $etatEnCours = $etatRepository->find(4);



        //initialiser le compte des participants à 0, et l'inscrit à une chaîne de caractères vide
        $count = 0;

        foreach ($sorties as $sortie) {
            $duree = $sortie->getDuree();
            $dateDebut = $sortie->getDateHeureDebut();
            $dateCloture = $sortie->getDateLimiteInscription();
            // Ajouter trente jours à la date de début
            $dateArchivage = clone $dateDebut; // Créer une copie de la date de début
            $dateArchivage->add(new DateInterval('P30D')); // Ajouter trente jours

            $dateFin = clone $dateDebut;
            $dateFin->add(new DateInterval('PT'.$duree.'M'));

            $dateDuJour = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
            $dateHeureNow = DateTime::createFromFormat('Y-m-d H:i', date('Y-m-d H:i'));

            if ($dateCloture <= $dateDuJour){
                $sortie->setEtat($etatCloture);
            }
            if ($dateDebut < $dateDuJour){
                $sortie->setEtat($etatPassee);
            }
            if ($dateArchivage <= $dateDuJour){
                $sortie->setEtat($etatArchive);
            }
            if ($dateDebut <= $dateHeureNow && $dateHeureNow <= $dateFin){
                $sortie->setEtat($etatEnCours);
            }
        }

        //trouver le participant correspondant à la personne connectée
        $user = $this->getUser()->getId();
        $participant = $participantRepository->findOneBy(['username' => $user]);

        return $this->render('main/index.html.twig', [
            'controller_name' => 'SortieController',
            'sorties' => $sorties,
            'participant' => $participant,
            'count' => $count
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
