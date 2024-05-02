<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostsRepository::class)]

class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column]
    public int $userId;

    #[ORM\Column(length: 255)]
    public string $name;

    #[ORM\Column(length: 255)]
    public string $postTitle;

    #[ORM\Column(type: 'text')]
    public string $postBody;

    public function setUserId($userId): static
    {
        $this->userId = $userId;
        return $this;
    }
    public function setName($name): static
    {
        $this->name = $name;
        return $this;
    }
    public function setPostTitle($title): static
    {
        $this->postTitle = $title;
        return $this;
    }
    public function setPostBody($body): static
    {
        $this->postBody = $body;
        return $this;
    }

}
