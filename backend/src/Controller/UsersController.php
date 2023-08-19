<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Role;
use App\Repository\UserRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UsersController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var RoleRepository
     */
    private $roleRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;


    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    public function getAllPersonnes():JsonResponse
    {
        $personnes = $this->userRepository->findAll();

        return $this->json($personnes,200,[],[
            'skip_null_values' => true,
            'ignored_attributes'=> ['personnes','roles','__isInitialized__']
        ]);
    }

    public function showPersonne( int $id_person): JsonResponse
    {
        $userToShow = $this->userRepository->find($id_person);
        if (!$userToShow) {
            return $this->json(['msg'=>'No user found for id '.$id_person],200);
        }
        if($userToShow->getRole()->getNomRole()=='admin') {
            return $this->json($userToShow, 200, [], [
                'skip_null_values'=>true,
                'ignored_attributes'=> ['personnes','roles','__isInitialized__','consultations','consultationsOfUser','reacts','posts',
                    'abonnes','abonnants','personne']
            ]);
        }elseif ($userToShow->getRole()->getNomRole()=='medecin'){
            return $this->json($userToShow, 200, [], [
                'skip_null_values'=>true,
                'ignored_attributes'=> ['personnes','roles','__isInitialized__','consultationsOfUser']
            ]);
        }
        elseif ($userToShow->getRole()->getNomRole()=='user') {
            return $this->json($userToShow, 200, [], [
                'skip_null_values'=>true,
                'ignored_attributes'=> ['personnes','roles','__isInitialized__','articles','consultations','abonnes']
            ]);
        }
    }

    public function getAllAdmins():JsonResponse
    {
        $role = $this->roleRepository->findOneBy(['nomRole'=>'admin']);
        return $this->json($role->getPersonnes(), 200, [], [
            'skip_null_values'=>true,
            'ignored_attributes'=> ['personnes','personne','consultationsOfUser','consultations','roles']
        ]);
    }

    public function getAllMedecins():JsonResponse
    {
        $role = $this->roleRepository->findOneBy(['nomRole'=>'medecin']);
        return $this->json($role->getPersonnes(), 200, [], [
            'skip_null_values'=>true,
            'ignored_attributes'=> ['personnes','roles','personne']
        ]);
    }

    public function getAllUsers():JsonResponse
    {
        $role = $this->roleRepository->findOneBy(['nomRole'=>'user']);
        return $this->json($role->getPersonnes(), 200, [], [
            'skip_null_values'=>true,
            'ignored_attributes'=> ['personnes', 'roles']
        ]);
    }

    public function addPersonne(Request $request ):JsonResponse
    {
        $person = new User();
        $role = new Role();
        if ($request->query->get('dateDip')!= null){
            $role = $this->roleRepository->findOneBy(['nomRole'=>'medecin']);
            $person->setNom($request->query->get('nom'));
            $person->setPrenom($request->query->get('prenom'));
            $person->setDateNaiss($request->query->get('dateNaiss'));
            $person->setAdresse($request->query->get('adresse'));
            $person->setEmail($request->query->get('email'));
            $person->setTelephone($request->query->get('telephone'));
            $person->setPays($request->query->get('pays'));
            $person->setMaladie($request->query->get('maladie'));
            $person->setPassword($this->encoder->encodePassword($person, $request->query->get('password')));
            $person->setDateDip($request->query->get('dateDip'));
            $person->setAdresseCab($request->query->get('adresseCab'));
        } elseif($request->query->get('maladie')!= null){
            $role = $this->roleRepository->findOneBy(['nomRole'=>'user']);

            $person->setNom($request->query->get('nom'));
            $person->setPrenom($request->query->get('prenom'));
            $person->setDateNaiss($request->query->get('dateNaiss'));
            $person->setAdresse($request->query->get('adresse'));
            $person->setEmail($request->query->get('email'));
            $person->setPays($request->query->get('pays'));
            $person->setTelephone($request->query->get('telephone'));
            $person->setMaladie($request->query->get('maladie'));
            $person->setPassword($this->encoder->encodePassword($person, $request->query->get('password')));
        }
        elseif ($request->query->get('pseudo')!= null){
            $role = $this->roleRepository->findOneBy(['nomRole'=>'admin']);
            $person->setPseudo($request->query->get('pseudo'));
            $person->setEmail($request->query->get('email'));
            $person->setPassword($this->encoder->encodePassword($person, $request->query->get('password')));
        }

        $person->setRole($role);
        try {
            $this->entityManager->persist($person);
            $this->entityManager->flush();
            return $this->json($person,201,[],[
                'ignored_attributes'=> ['personnes'],
                'skip_null_values'=>true,
            ]);
        } catch (ORMException $e) {
        }
    }

    public function updatePersonne(int $id_person, Request $request):JsonResponse
    {
        $personToUpdate = $this->userRepository->find($id_person);
        if($personToUpdate->getRole()->getNomRole()== 'medecin'){
            if($request->request->get('nom')!=null)
                $personToUpdate->setNom($request->request->get('nom'));
            if($request->request->get('prenom')!=null)
                $personToUpdate->setPrenom($request->request->get('prenom'));
            if($request->request->get('dateNaiss')!=null)
                $personToUpdate->setDateNaiss($request->request->get('dateNaiss'));
            if($request->query->get('adresse')!=null)
                $personToUpdate->setAdresse($request->request->get('adresse'));
            if($request->request->get('email')!=null)
                $personToUpdate->setEmail($request->request->get('email'));
            if($request->request->get('telephone')!=null)
                $personToUpdate->setTelephone($request->request->get('telephone'));
            if($request->request->get('pays')!=null)
                $personToUpdate->setPays($request->request->get('pays'));
            if($request->request->get('password')!=null)
                $personToUpdate->setPassword($this->encoder->encodePassword($personToUpdate, $request->request->get('password')));
            if($request->request->get('dateDip')!=null)
                $personToUpdate->setDateDip($request->request->get('dateDip'));
            if($request->request->get('adresseCab')!=null)
                $personToUpdate->setAdresseCab($request->request->get('adresseCab'));
            if($request->request->get('maladie')!=null)
                $personToUpdate->setMaladie($request->request->get('maladie'));
            if ($request->files->get('photo')!=null)
            {
                if($personToUpdate->getPhoto()!=null)
                {
                    $filesystem = new Filesystem();
                    $oldimagename = strrchr($personToUpdate->getPhoto(),'/');
                    $filesystem->remove($this->getParameter('doctors_directory').$oldimagename);
                }
                $photoFile = $request->files->get('photo');
                $newFilename = 'doctor_'.$personToUpdate->getEmail().'.'.$photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('doctors_directory'),
                    $newFilename
                );
                $photoUrl = $request->getSchemeAndHttpHost().'/doctors/'.$newFilename;
                $personToUpdate->setPhoto($photoUrl);
            }
        }
        elseif ($personToUpdate->getRole()->getNomRole()== 'user'){
            if($request->request->get('nom')!=null)
                $personToUpdate->setNom($request->request->get('nom'));
            if($request->request->get('prenom')!=null)
                $personToUpdate->setPrenom($request->request->get('prenom'));
            if($request->request->get('dateNaiss')!=null)
                $personToUpdate->setDateNaiss($request->request->get('dateNaiss'));
            if($request->request->get('adresse')!=null)
                $personToUpdate->setAdresse($request->request->get('adresse'));
            if($request->request->get('email')!=null)
                $personToUpdate->setEmail($request->request->get('email'));
            if($request->request->get('telephone')!=null)
                $personToUpdate->setTelephone($request->request->get('telephone'));
            if($request->request->get('pays')!=null)
                $personToUpdate->setPays($request->request->get('pays'));
            if($request->request->get('maladie')!=null)
                $personToUpdate->setMaladie($request->request->get('maladie'));
            if($request->request->get('password')!=null)
                $personToUpdate->setPassword($this->encoder->encodePassword($personToUpdate, $request->request->get('password')));
            if($request->files->get('photo')!=null)
            {
                if($personToUpdate->getPhoto()!=null)
                {
                    $filesystem = new Filesystem();
                    $oldimagename = strrchr($personToUpdate->getPhoto(),'/');
                    $filesystem->remove($this->getParameter('users_directory').$oldimagename);
                }
                $photoFile = $request->files->get('photo');
                $newFilename = 'user_'.$personToUpdate->getEmail().'.'.$photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('users_directory'),
                    $newFilename
                );
                $photoUrl = $request->getSchemeAndHttpHost().'/users/'.$newFilename;
                $personToUpdate->setPhoto($photoUrl);

            }
        }
        elseif ($personToUpdate->getRole()->getNomRole()== 'admin'){
            if($request->request->get('email')!=null)
                $personToUpdate->setEmail($request->request->get('email'));
            if($request->request->get('password')!=null)
                $personToUpdate->setPassword($this->encoder->encodePassword($personToUpdate, $request->request->get('password')));
            if($request->request->get('pseudo')!=null)
                $personToUpdate->setPseudo($request->request->get('pseudo'));
            if($request->files->get('photo')!=null)
            {
                if($personToUpdate->getPhoto()!=null)
                {
                    $filesystem = new Filesystem();
                    $oldimagename = strrchr($personToUpdate->getPhoto(),'/');
                    $filesystem->remove($this->getParameter('admins_directory').$oldimagename);
                }

                $photoFile = $request->files->get('photo');
                $newFilename = 'admin_'.$personToUpdate->getEmail().'.'.$photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('admins_directory'),
                    $newFilename
                );
                $photoUrl = $request->getSchemeAndHttpHost().'/admins/'.$newFilename;
                $personToUpdate->setPhoto($photoUrl);
            }

        }
        $this->entityManager->flush();
        return $this->json($personToUpdate,201,[],
            [
                'ignored_attributes'=> ['personnes'],
                'skip_null_values'=>true,
            ]);
    }

    public function deletePersonne(int $id_person): JsonResponse
    {
        $personToDelete = $this->userRepository->find($id_person);
        $msg = '';
        if($personToDelete->getRole()->getNomRole()== 'admin'){
            $filesystem = new Filesystem();
            $imagename = strrchr($personToDelete->getPhoto(),'/');
            $filesystem->remove($this->getParameter('admins_directory').$imagename);
            $msg= 'admin successfully deleted';
        } elseif ($personToDelete->getRole()->getNomRole()== 'user')
        {
            $filesystem = new Filesystem();
            $imagename = strrchr($personToDelete->getPhoto(),'/');
            $filesystem->remove($this->getParameter('users_directory').$imagename);
            $msg = 'user successfully deleted';
        } elseif ($personToDelete->getRole()->getNomRole()== 'medecin'){
            $filesystem = new Filesystem();
            $imagename = strrchr($personToDelete->getPhoto(),'/');
            $filesystem->remove($this->getParameter('doctors_directory').$imagename);

            $articlesdoctor = $personToDelete->getArticles();
            foreach ($articlesdoctor as $article){
                $filesystem = new Filesystem();
                $imagename = strrchr($article->getImage(),'/');
                $filesystem->remove($this->getParameter('articles_directory').$imagename);
            }
            $msg= "medecin successfully deleted" ;
        }
        $this->entityManager->remove($personToDelete);
        $this->entityManager->flush();
        return $this->json(['msg'=>$msg],200,[]);
    }

    public function Abonner (int $id_source, int $id_target): JsonResponse
    {
        $personne_source = $this->userRepository->find($id_source);  //el personne eli inzel 3l button abonner
        $personne_target = $this->userRepository->find($id_target);

        $personne_target->addAbonne($personne_source);
        $this->entityManager->flush();
        return $this->json($personne_source->getAbonnants(),200,[],[
            'ignored_attributes'=> ['personnes','role','roles','articles','consultations','consultationsOfUser'],
            'skip_null_values'=>true,
        ]);

    }

    public function Disabonner(int $id_source, int $id_target):JsonResponse
    {
        $personne_source = $this->userRepository->find($id_source);  //el personne eli inzel 3l button disabonner
        $personne_target = $this->userRepository->find($id_target);
        $personne_target->removeAbonne($personne_source);
        $this->entityManager->flush();
        return $this->json($personne_source->getAbonnants(),200,[],[
            'ignored_attributes'=> ['personnes','role','roles','articles','consultations','consultationsOfUser'],
            'skip_null_values'=>true,
        ]);
    }


}
