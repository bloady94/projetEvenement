<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{
    #[Route('/profile', name: 'profile_index')]
    public function index(): Response
    {
        return $this->render('participant/profile.html.twig');
    }

    #[Route('/profile/update/{id}', name: 'profile_update', requirements: ["id" => "\d+"])]
    public function update(int $id, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);
        $participantForm = $this->createForm(RegistrationFormType::class, $participant);
        return $this->render('participant/update.html.twig', [
            'participantForm' => $participantForm->createView()
        ]);
    }
}
