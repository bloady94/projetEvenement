<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/inscrire/{id}', name: 'main_inscrire', requirements: ["id" => "\d+"])]
    public function inscrire(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);
        $message ="";

        if($sortie->getEtat() == 2){
            $message = "tu peux t'inscrire khey.";
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


            return $this->render('main/index.html.twig', [
                'sorties' => $sortie,
            ]);
//       return $this->redirectToRoute('main_homepage');
    }
}
