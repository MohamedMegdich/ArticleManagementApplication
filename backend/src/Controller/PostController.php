<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\ArticleRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class PostController extends AbstractController
{
    /**
     * @var PostRepository
     */
    private $postRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var ArticleRepository
     */
    private $articleRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        PostRepository $postRepository,
        UserRepository $userRepository,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager)
    {
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
        $this->articleRepository = $articleRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllPosts():JsonResponse
    {
        return $this->json($this->postRepository->findAll(),200,[],[
            'skip_null_values' => true,
            'ignored_attributes'=> ['role','consultations','consultationsOfUser','__isInitialized__','articles','roles','posts',
                'comments','abonnes','abonnants','reacts','personne','reactions'],
        ]);
    }

    public function addCommentaire(int $id_personne, int $id_article , Request $request): JsonResponse{
        $newPost = new Post();
        $commentant = $this->userRepository->find($id_personne);
        $article = $this->articleRepository->find(($id_article));
        $newPost->setDescription($request->query->get('description'));
        $this->entityManager->persist($newPost);
        $newPost->setUser($commentant);
        $newPost->setArticle($article);
        $this->entityManager->flush();

        return $this->json($newPost,201,[],[
            'ignored_attributes'=> ['role','roles','consultations','consultationsOfUser','articles','comments'],
            'skip_null_values' => true,
        ]);
    }

    public function updateCommentaire(int $id_post , Request $request): JsonResponse
    {
        $postToUpdate = $this->postRepository->find($id_post);
        $postToUpdate->setDescription($request->query->get('description'));
        $this->entityManager->flush();
        return $this->json($postToUpdate,200,[],[
            'ignored_attributes'=> ['role','articles','consultations','consultationsOfUser','__isInitialized__'],
            'skip_null_values' => true,
        ]);

    }

    public function deleteCommentaire(int $id_post ): JsonResponse
    {
        $postToRemove = $this->postRepository->find($id_post);
        $this->entityManager->remove($postToRemove);
        $this->entityManager->flush();
        return $this->json($this->postRepository->findAll(),200,[],[
            'ignored_attributes'=> ['role','consultations','consultationsOfUser','__isInitialized__','articles','roles','posts',
                'comments','abonnes','abonnants','reacts','personne','reactions'],
            'skip_null_values' => true,
        ]);
    }
}
