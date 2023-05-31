<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/ville', name: 'ville_')]
class VilleController extends AbstractController
{
    #[Route('/add', name: 'add')]
    public function add(
        VilleRepository $villeRepository,
        Request $request
    ): Response
    {
        $ville = new Ville();

        $villeForm = $this->createForm(VilleType::class, $ville);

        $villeForm->handleRequest($request);

        if($villeForm->isSubmitted() && $villeForm->isValid()){

            $villeRepository->save($ville, true);
            $this->addFlash('success', 'Le campus vient d\'être ajouté!');
            return $this->redirectToRoute('ville_list');
        }

        return $this->render('ville/add.html.twig', [
            'villeForm' => $villeForm->createView()
        ]);
    }
    #[Route('/', name: 'list')]
    public function list(VilleRepository $villeRepository): Response
    {
        $ville = $villeRepository->findAll();

        return $this->render('ville/list.html.twig', [
            'controller_name' => 'VilleController',
            'ville' => $ville,
        ]);
    }
    #[Route('/update/{id}', name: 'update')]
    public function update(
        VilleRepository $villeRepository,
        int $id,
        Request $request
    )
    {
        $ville = $villeRepository->find($id);
        $villeForm = $this->createForm(VilleType::class, $ville);

        $villeForm->handleRequest($request);

        if($villeForm->isSubmitted() && $villeForm->isValid()) {
            $villeRepository->save($ville, true);
        }

        return $this->render('ville/update.html.twig', [
            'villeForm' => $villeForm->createView()
        ]);
    }


    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(int $id, VilleRepository $villeRepository): Response
    {
        $ville = $villeRepository->find($id);

        $villeRepository->remove($ville, true);

        $this->addFlash('success', $ville->getNom() . " vient d'être supprimé!");


        return $this->redirectToRoute('ville_list');
    }


}
