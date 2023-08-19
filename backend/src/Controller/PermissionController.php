<?php

namespace App\Controller;

use App\Entity\Permission;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PermissionController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var PermissionRepository
     */
    private $permissionRepository;
    /**
     * @var RoleRepository
     */
    private $roleRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                PermissionRepository $permissionRepository,
                                RoleRepository $roleRepository)
    {

        $this->entityManager = $entityManager;
        $this->permissionRepository = $permissionRepository;
        $this->roleRepository = $roleRepository;
    }

    public function getAllPermissions(): JsonResponse
    {
        return $this->json($this->permissionRepository->findAll(),200, [], [
            'skip_null_values' => true,
            'ignored_attributes'=> ['personnes','permissions','__isInitialized__'],
        ]);

    }

    public function addNewPermission(Request $request):JsonResponse
    {
        $permission = new Permission();
        $permission->setNomPer($request->query->get('nomPer'));
        $roles = $request->query->get('roles');
        preg_match_all('!\d+!', $roles, $matches);
        $rolesid = $matches[0];
        foreach ($rolesid as $roleid){
            $role = $this->roleRepository->find($roleid);
            $permission->addRole($role);
        }
        $this->entityManager->persist($permission);
        $this->entityManager->flush();

        return $this->json($permission,201,[],[]);
    }

    public function updatePermission(int $id_permission , Request $request):JsonResponse
    {
        $permissionToUpdate = $this->permissionRepository->find($id_permission);
        if($permissionToUpdate->setNomPer($request->query->get('nomPer'))!= null)
        {
            $permissionToUpdate->setNomPer($request->query->get('nomPer'));
        }
        if($request->query->get('roles')!=null)
        {
            $rolesOfThisPer = $permissionToUpdate->getRoles();
            foreach ( $rolesOfThisPer as $role ) {
                $permissionToUpdate->removeRole($role);
            }
            $newRoles = $request->query->get('roles');
            preg_match_all('!\d+!', $newRoles, $matches);
            $rolesid = $matches[0];
            foreach ($rolesid as $roleid){
                $role = $this->roleRepository->find($roleid);
                $permissionToUpdate->addRole($role);
            }
        }
        $this->entityManager->flush();
        return $this->json($permissionToUpdate->getRoles(),200,[],[
            'ignored_attributes'=> ['personnes'],
        ]);

    }

    public function deletePermission(int $id_permission):JsonResponse
    {
        $permissionToDelete = $this->permissionRepository->find($id_permission);
        $this->entityManager->remove($permissionToDelete);
        $this->entityManager->flush();

        return $this->json($this->permissionRepository->findAll(),200,[],[
            'skip_null_values' => true,
            'ignored_attributes'=> ['personnes','permissions'],
        ]);
    }
}
