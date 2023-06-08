<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_ADMIN")]
#[Route('/campus', name: 'campus_')]
class CampusController extends AbstractController
{

    #[Route('/', name: 'list')]
    public function list(CampusRepository $campusRepository): Response
    {
        // On récup tout ce qu'il y a dans la table campus.
        $campus = $campusRepository->findAll();

        return $this->render('campus/homeCampus.html.twig', [
            'controller_name' => 'CampusController',
            'campus' => $campus,
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, CampusRepository $campusRepository): Response
    {
        $campus = new Campus();
        $campusForm = $this->createForm(CampusType::class, $campus);
        $campusForm->handleRequest($request);

        if($campusForm->isSubmitted() && $campusForm->isValid()){
            $campusRepository->save($campus, true);
            $this->addFlash('success', 'Le campus vient d\'être ajouté!');
            return $this->redirectToRoute('campus_list');
        }
        return $this->render('campus/addCampus.html.twig', [
            'campusForm' => $campusForm->createView()
        ]);
    }

    #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d+'])]
    public function update(Request $request, int $id, CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->find($id);
        if (!$campus){
            return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
        }
        $campusForm = $this->createForm(CampusType::class, $campus);
        $campusForm->handleRequest($request);

        if($campusForm->isSubmitted() && $campusForm->isValid()) {
            $campusRepository->save($campus, true);
            return $this->redirectToRoute('campus_list');
        }
        return $this->render('campus/updateCampus.html.twig', [
            'campusUpdateForm' => $campusForm->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(int $id, CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->find($id);
        if (!$campus){
            return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
        }
        $campusRepository->remove($campus, true);
        $this->addFlash('success', $campus->getNom() . " vient d'être supprimé!");

        return $this->redirectToRoute('campus_list');
   }

    #[Route('/trouver', name: 'trouverCampus')]
    public function trouverCampus(Request $request, CampusRepository $campusRepository): Response
    {
        $recherche = $request->query->get('recherche');
        $campus = [];

        if ($recherche) {
            $campus = $campusRepository->findByNom($recherche);
        }

        return $this->render('campus/homeCampus.html.twig', [
            'campus' => $campus,
        ]);
    }

}
