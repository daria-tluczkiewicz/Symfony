<?php

namespace App\Command;

use App\Entity\Posts;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'import:posts',
    description: 'Imports posts from JSONPlaceholder API and saves them to the database.',
)]
class ImportPostsCommand extends Command
{
    private HttpClientInterface $httpClient;
    private EntityManagerInterface $entityManager;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $posts = $this->fetchPosts();

        foreach ($posts as $post) {
            $userId = $post['userId'];
            $authorInfo = $this->fetchUser($userId);
            var_dump($authorInfo['name']);
            $updatedPost = (object) [
                'userId' => $post['userId'],
                'name' => $authorInfo['name'],
                'title' => $post['title'],
                'body' => $post['body'],
            ];
            $this->insertPost($updatedPost);
        }

        return Command::SUCCESS;
    }

    private function fetchPosts(): array
    {
        try {
            $response = $this->httpClient->request('GET', 'https://jsonplaceholder.typicode.com/posts');
            return $response->toArray();
        } catch (TransportExceptionInterface $e) {
            return [];
        }

    }

    private function fetchUser($userId): array
    {
        $response = $this->httpClient->request('GET', "https://jsonplaceholder.typicode.com/users/{$userId}");
        return $response->toArray();
    }

    private function insertPost($postData): void
    {
        $post = new Posts();
        $post->setUserId($postData->userId);
        $post->setName($postData->name);
        $post->setPostTitle($postData->title);
        $post->setPostBody($postData->body);
        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }
}
