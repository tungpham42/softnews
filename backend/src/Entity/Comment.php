<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class)]
    #[ORM\JoinColumn(name: 'post_id', nullable: false, onDelete: 'CASCADE')]
    private Post $post;

    #[ORM\Column(name: 'author_name', length: 120)]
    private string $authorName = '';

    #[ORM\Column(name: 'author_email', length: 180)]
    private string $authorEmail = '';

    #[ORM\Column(name: 'body', type: 'text')]
    private string $body = '';

    #[ORM\Column(name: 'status', length: 20)]
    private string $status = 'pending';

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct() { $this->createdAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; }
    public function getPost(): Post { return $this->post; }
    public function setPost(Post $post): self { $this->post = $post; return $this; }
    public function getAuthorName(): string { return $this->authorName; }
    public function setAuthorName(string $value): self { $this->authorName = trim($value); return $this; }
    public function getAuthorEmail(): string { return $this->authorEmail; }
    public function setAuthorEmail(string $value): self { $this->authorEmail = mb_strtolower(trim($value)); return $this; }
    public function getBody(): string { return $this->body; }
    public function setBody(string $value): self { $this->body = trim($value); return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $value): self { $this->status = $value; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
