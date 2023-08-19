<?php

namespace App\Controller;

use App\Entity\Reaction;
use App\Repository\ArticleRepository;
use App\Repository\ReactionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReactionController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var ArticleRepository
     */
    private $articleRepository;
    /**
     * @var ReactionRepository
     */
    private $reactionRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        ReactionRepository $reactionRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        ArticleRepository $articleRepository)
    {

        $this->reactionRepository = $reactionRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->articleRepository = $articleRepository;
    }

   public function getAllReactions():JsonResponse
   {
       return $this->json($this->reactionRepository->findAll(),200,[],[
           'skip_null_values' => true,
           'ignored_attributes'=> ['role','consultations','consultationsOfUser','__isInitialized__','articles','roles','posts',
               'comments','abonnes','abonnants','reacts','personne','reactions'],
       ]);
   }

   public function Like(int $id_personne, int $id_article): JsonResponse
   {
       $newReaction = new Reaction();
       $personne = $this->userRepository->find($id_personne);
       $article = $this->articleRepository->find(($id_article));
       $newReaction->setIsLike(1);
       $this->entityManager->persist($newReaction);
       $newReaction->setUser($personne);
       $newReaction->setArticle($article);
       $this->entityManager->flush();

       return $this->json($newReaction,201,[],[
           'ignored_attributes'=> ['role','roles','consultations','consultationsOfUser','articles','comments'],
           'skip_null_values' => true,
       ]);
   }

    public function Dislike(int $id_personne, int $id_article): JsonResponse
    {
        $personne = $this->userRepository->find($id_personne);
        $article = $this->articleRepository->find(($id_article));
        $reaction = $this->reactionRepository->findByPersonneArticle($personne,$article);
        $this->entityManager->remove($reaction);
        $this->entityManager->flush();

        return $this->json($this->reactionRepository->findAll(),200,[],[
            'ignored_attributes'=> ['role','roles','consultations','consultationsOfUser','articles','comments'],
            'skip_null_values' => true,
        ]);
    }
}
