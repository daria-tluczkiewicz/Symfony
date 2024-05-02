<?php

namespace App\Controller;

use App\Entity\Posts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/lista', name: 'posts_list')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $posts = $this->getAllPosts()->getContent();
        $data = [];
        $json = json_decode($posts);

        foreach ($json as $post) {
            $data[] = [
                'id' => $post->id,
                'userId' => $post->userId,
                'name' => $post->name,
                'title' => $post->title,
                'body' => $post->body
            ];
        }

        return $this->render('posts/index.html.twig', [
            'controller_name' => 'PostsController',
            'posts' => $data
        ]);
    }

    #[Route('/posts', name: 'list_all_posts', methods: ['GET'])]
    public function getAllPosts(): Response
    {
        $posts = $this->entityManager->getRepository(Posts::class)->findAll();

        $data = [];

        foreach ($posts as $post) {
            $data[] = [
                'id' => $post->id,
                'userId' => $post->userId,
                'name' => $post->name,
                'title' => $post->postTitle,
                'body' => $post->postBody
            ];
        }
        return new Response(json_encode($data));
    }

    #[Route('/delete/{id}', name: 'delete_post', methods: ['POST'])]
    public function deletePostById(int $id): Response {
        $post = $this->entityManager->getRepository(Posts::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $this->entityManager->remove($post);
        $this->entityManager->flush();

        return new Response("Post with ID $id deleted successfully", Response::HTTP_OK);
    }
}