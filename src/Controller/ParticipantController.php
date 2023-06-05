<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_USER")]
class ParticipantController extends AbstractController
{

    #[Route('/profile', name: 'profile_index')]
    public function index(): Response
    {
        return $this->render('participant/profile.html.twig');
    }

    #[Route('/profile/update/{id}', name: 'profile_update', requirements: ["id" => "\d+"])]
    public function update(Request $request, int $id, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);
        $participantForm = $this->createForm(RegistrationFormType::class, $participant);
        $participantForm->handleRequest($request);

        if($participantForm->isSubmitted() && $participantForm->isValid()) {
            $file = $participantForm->get('photo')->getData();
            if($file){
                $newFileName = $participant->getNom() . "-" . $participant->getPrenom() . "-" . uniqid().".".$file->guessExtension();
                $file->move('img/photo', $newFileName);
                $participant->setPhoto($newFileName);

            }
            $participantRepository->save($participant, true);
            return $this->redirectToRoute('profile_index');
        }


        return $this->render('participant/update.html.twig', [
            'participantForm' => $participantForm->createView()
        ]);
    }

    #[Route('/profile/{id}', name: 'profile_show', requirements: ["id" => "\d+"])]
    public function show(int $id, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);

        return $this->render('participant/show.html.twig', [
                'participant' => $participant
        ]);
    }
}
