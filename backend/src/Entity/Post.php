<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'title', length: 180)]
    private string $title = '';

    #[ORM\Column(name: 'slug', length: 200, unique: true)]
    private string $slug = '';

    #[ORM\Column(name: 'excerpt', type: 'text')]
    private string $excerpt = '';

    #[ORM\Column(name: 'content', type: 'text')]
    private string $content = '';

    #[ORM\Column(name: 'status', length: 20)]
    private string $status = 'draft';

    #[ORM\Column(name: 'cover_image', length: 255, nullable: true)]
    private ?string $coverImage = null;

    #[ORM\Column(name: 'views')]
    private int $views = 0;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'published_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(name: 'category_id', nullable: false, onDelete: 'RESTRICT')]
    private Category $category;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'posts')]
    #[ORM\JoinTable(
        name: 'post_tags',
        joinColumns: [new ORM\JoinColumn(name: 'post_id', referencedColumnName: 'id', onDelete: 'CASCADE')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'tag_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    )]
    private Collection $tags;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'author_id', nullable: false, onDelete: 'RESTRICT')]
    private User $author;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->tags = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if (!$this->createdAt) $this->createdAt = new \DateTimeImmutable();
        if ($this->status === 'published' && !$this->publishedAt) $this->publishedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = trim($title); return $this; }
    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): self { $this->slug = trim($slug); return $this; }
    public function getExcerpt(): string { return $this->excerpt; }
    public function setExcerpt(string $excerpt): self { $this->excerpt = trim($excerpt); return $this; }
    public function getContent(): string { return $this->content; }
    public function setContent(string $content): self { $this->content = $content; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; if ($status === 'published' && !$this->publishedAt) $this->publishedAt = new \DateTimeImmutable(); if ($status !== 'published') $this->publishedAt = null; return $this; }
    public function getCoverImage(): ?string { return $this->coverImage; }
    public function setCoverImage(?string $coverImage): self { $this->coverImage = $coverImage; return $this; }
    public function getViews(): int { return $this->views; }
    public function setViews(int $views): self { $this->views = max(0, $views); return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getPublishedAt(): ?\DateTimeImmutable { return $this->publishedAt; }
    public function getCategory(): Category { return $this->category; }
    public function setCategory(Category $category): self { $this->category = $category; return $this; }
    public function getTags(): Collection { return $this->tags; }
    public function addTag(Tag $tag): self { if (!$this->tags->contains($tag)) $this->tags->add($tag); return $this; }
    public function removeTag(Tag $tag): self { $this->tags->removeElement($tag); return $this; }
    public function clearTags(): self { $this->tags->clear(); return $this; }
    public function getAuthor(): User { return $this->author; }
    public function setAuthor(User $author): self { $this->author = $author; return $this; }
}
