<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/campus', name: 'campus_')]
class CampusController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->findAll();

        return $this->render('campus/homeCampus.html.twig', [
            'controller_name' => 'CampusController',
            'campus' => $campus,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(int $id, CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->find($id);

        $campusRepository->remove($campus, true);

        $this->addFlash('success', $campus->getNom() . " vient d'être supprimé!");


        return $this->redirectToRoute('campus_list');
   }

}
