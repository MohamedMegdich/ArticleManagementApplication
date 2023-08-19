<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Repository\ConsultationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ConsultationController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var ConsultationRepository
     */
    private $consultationRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(UserRepository $userRepository,
                                ConsultationRepository $consultationRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->consultationRepository = $consultationRepository;
        $this->entityManager = $entityManager;
    }

    public function  getAllConsultations():JsonResponse
    {
        return $this->json($this->consultationRepository->findAll(),200,[],[
            'skip_null_values' => true,
            'ignored_attributes'=> ['role','consultations','consultationsOfUser','__isInitialized__','articles','roles'],
        ]);
    }

    public function addConsultation(int $id_medecin , int $id_user, Request $request): JsonResponse
    {
        $newConsultation = new Consultation();
        $medecin = $this->userRepository->find($id_medecin);
        $user = $this->userRepository->find(($id_user));
        $newConsultation->setDateConsul($request->query->get('DateConsul'));
        $this->entityManager->persist($newConsultation);
        $newConsultation->setMedecin($medecin);
        $newConsultation->setUser($user);
        $this->entityManager->flush();

        return $this->json($newConsultation,201,[],[
            'ignored_attributes'=> ['role'],
            'skip_null_values' => true,
        ]);

    }
    public function updateConsultation(int $id_consultation , Request $request): JsonResponse
    {
        $consultationToUpdate = $this->consultationRepository->find($id_consultation);
        $consultationToUpdate->setDateConsul($request->query->get('DateConsul'));
        $this->entityManager->flush();
        return $this->json($consultationToUpdate,200,[],[
            'ignored_attributes'=> ['role','articles','consultations','consultationsOfUser','__isInitialized__'],
            'skip_null_values' => true,
        ]);

    }
    public function deleteConsultation(int $id_consultation ): JsonResponse
    {
        $consultationToRemove = $this->consultationRepository->find($id_consultation);
        $this->entityManager->remove($consultationToRemove);
        $this->entityManager->flush();
        return $this->json($this->consultationRepository->findAll(),200,[],[
            'ignored_attributes'=> ['role','articles','consultations','consultationsOfUser'],
            'skip_null_values' => true,
        ]);
    }
}
