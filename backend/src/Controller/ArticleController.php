<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ArticleRepository
     */
    private $articleRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(ArticleRepository $articleRepository,UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->articleRepository = $articleRepository;
        $this->userRepository = $userRepository;
    }

    public function getAllArticles():JsonResponse
    {
        $articles = $this->articleRepository->findAll();
        return $this->json($articles,200,[],[
            'skip_null_values' => true,
            'ignored_attributes'=> ['__isInitialized__','articles','personnes','roles','consultationsOfUser','consultations'
            ,'abonnes','abonnants','posts','reacts'],
        ]);
    }

    public function getArticle(int $id): JsonResponse
    {
        $articleToShow = $this->articleRepository->find($id);
        return $this->json($articleToShow,200,[],[
            'skip_null_values' => true
        ]);
    }

    public function getArticlesOfMedecin($id_person): JsonResponse
    {
        $person = $this->userRepository->find($id_person);
        return $this->json($person->getArticles(),200,[],[
            'skip_null_values' => true,
            'ignored_attributes'=> ['personne'],
        ]);
    }

    public function getArticlesByMaladie(Request $request): JsonResponse
    {
        $articlesByMaladie = $this->articleRepository->findBy(['maladie'=> $request->query->get('maladie')]);
        return $this->json($articlesByMaladie,200,[],[
            'skip_null_values' => true,
            'ignored_attributes'=> ['__isInitialized__','personnes','roles','articles','consultations','consultationsOfUser','abonnes',
                'abonnants','reacts','posts','role'],
        ]);
    }

    public  function addArticleToPerson(int $personId,Request $request): JsonResponse
    {

        $person = $this->userRepository->find($personId);
        $articleToAdd = new Article();
        $articleToAdd->setTitle($request->request->get('title'));
        $articleToAdd->setDescription($request->request->get('description'));
        $articleToAdd->setMaladie($request->request->get('maladie'));

        $imageFile = $request->files->get('image');
        try {
            $totalArticles = $this->articleRepository->createQueryBuilder('a')
                // Filter by some parameter if you want
                // ->where('a.published = 1')
                ->select('count(a.id)')
                ->getQuery()
                ->getSingleScalarResult();
            $totalArticles = $totalArticles + 1 ;
            $newFilename = 'article_'.$totalArticles.'.'.$imageFile->guessExtension();
            $imageFile->move(
                $this->getParameter('articles_directory'),
                $newFilename
            );
            $photoUrl = $request->getSchemeAndHttpHost().'/articles/'.$newFilename;
            $articleToAdd->setImage($photoUrl);
        } catch (NoResultException $e) {
        } catch (NonUniqueResultException $e) {
        }

        $this->entityManager->persist($articleToAdd);
        $person->addArticle($articleToAdd);
        $this->entityManager->flush();

        return $this->json($this->articleRepository->findAll(),201,[],[
            'ignored_attributes'=> ['personne','__isInitialized__'],
        ]);
    }

    public function updateArticle(int $id_article, Request $request): JsonResponse
    {
        $articleToUpdate = $this->articleRepository->find($id_article);
        if($request->request->get('title')!= null){
            $articleToUpdate->setTitle($request->request->get('title'));
        }
        if($request->request->get('description')!= null){
            $articleToUpdate->setDescription($request->request->get('description'));
        }
        if($request->request->get('maladie')!= null){
            $articleToUpdate->setMaladie($request->request->get('maladie'));
        }
        if($request->files->get('image')!= null){
            $filesystem = new Filesystem();
            $oldimagename = strrchr($articleToUpdate->getImage(),'/');
            $filesystem->remove($this->getParameter('articles_directory').$oldimagename);

            $imageFile = $request->files->get('image');
            $newFilename = 'article_'.$articleToUpdate->getId().'.'.$imageFile->guessExtension();
            $imageFile->move(
                $this->getParameter('articles_directory'),
                $newFilename
            );
            $photoUrl = $request->getSchemeAndHttpHost().'/articles/'.$newFilename;
            $articleToUpdate->setImage($photoUrl);
        }

        $this->entityManager->flush();
        return $this->json($articleToUpdate,200,[],
            [
                'ignored_attributes'=> ['personne']
            ]);
    }

    public function deleteArticle($id_article): JsonResponse
    {
        $articleToDelete = $this->articleRepository->find($id_article);

        $filesystem = new Filesystem();
        $imagename = strrchr($articleToDelete->getImage(),'/');
        $filesystem->remove($this->getParameter('articles_directory').$imagename);

        $person = $articleToDelete->getPersonne();
        $person->removeArticle($articleToDelete);
        $this->entityManager->remove($articleToDelete);
        $this->entityManager->flush();
        return $this->json(['msg'=>'article succesfuly deleted'],200,[]);
    }
}
