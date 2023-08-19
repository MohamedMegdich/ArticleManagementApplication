<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class   RoleController extends AbstractController
{
    /**
     * @var RoleRepository
     */
    private $roleRepository;
    /**
     * @var PermissionRepository
     */
    private $permissionRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct (
        RoleRepository $roleRepository ,
        PermissionRepository $permissionRepository ,
        EntityManagerInterface $entityManager ){
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
        $this->entityManager = $entityManager ;
    }

    public function getAllRoles(): JsonResponse
    {
        return $this->json($this->roleRepository->findAll(), 200 , [],[
            'skip_null_values' => true,
            'ignored_attributes'=> ['roles','__isInitialized__','role','consultations','consultationsOfUser','articles',
                'abonnes','abonnants','posts','reacts'],
        ]);
    }

    public function addRole(Request $request):JsonResponse
    {
        $newRole = new Role();
        $newRole->setNomRole($request->query->get('nomRole'));
        $this->entityManager->persist($newRole);
        $this->entityManager->flush();
        return $this->json($newRole,201,[],[
            'skip_null_values' => true,
        ]);
    }

    public function updateRole(int $role_id, Request $request):JsonResponse
    {
        $roleToUpdate = $this->roleRepository->find($role_id);
        $roleToUpdate->setNomRole($request->query->get('nomRole'));
        $this->entityManager->flush();
        return $this->json($roleToUpdate,201, [],[
            'skip_null_values' => true,
        ]);
    }

    public function  deleteRole (int $role_id): JsonResponse
    {
        $roleToRemove = $this->roleRepository->find($role_id);
        $this->entityManager->remove($roleToRemove);
        $this->entityManager->flush();
        return $this->json($this->roleRepository->findAll(), 200, [], [
            'skip_null_values' => true,
        ]);
    }
}
