<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[IsGranted("ROLE_USER")]
#[Route('/lieu', name: 'lieu_')]
class LieuController extends AbstractController
{

    #[Route('/add', name: 'add')]
    public function add(
        LieuRepository $lieuRepository,
        Request        $request,
    ): Response
    {
        $lieu = new Lieu();

        $lieuForm = $this->createForm(LieuType::class, $lieu);

        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {

            $lieuRepository->save($lieu, true);
            $this->addFlash('success', 'Le lieu vient d\'être ajouté!');
            return $this->redirectToRoute('sortie_add');
        }

        return $this->render('lieu/add.html.twig', [
            'lieuForm' => $lieuForm->createView()
        ]);
    }

      #[Route('/details', name: 'get_lieu_details', methods: "POST")]
    public function getLieuDetails(Request $request, LieuRepository $lieuRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];
        $lieu = $lieuRepository->find($id);

        // Vérifie si le lieu existe
        if ($lieu === null) {
            return new JsonResponse(['error' => 'Lieu not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Renvoie les détails du lieu en tant que réponse JSON
        return new JsonResponse([
            'longitude' => $lieu->getLongitude(),
            'latitude' => $lieu->getLatitude(),
            'rue' => $lieu->getRue()
        ]);
    }

    #[Route('/lieu/lieuByVille', name: 'lieu_get_lieux_by_ville', methods: "POST")]
    public function getLieuxByVille(Request $request, LieuRepository $lieuRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $villeId = $data['id'];
        $lieux = $lieuRepository->findBy(['ville' => $villeId]);

        $lieuxData = [];
        foreach ($lieux as $lieu) {
            $lieuxData[] = [
                'nom' => $lieu->getNom()
            ];
        }

        return new JsonResponse($lieuxData);
    }


}
