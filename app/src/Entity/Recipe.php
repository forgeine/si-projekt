<?php
/**
 * Recipe entity.
 */

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Recipe.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[ORM\Table(name: 'recipes')]
class Recipe
{
    /**
     * Primary key.
     *
     * @var int|null Id
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Title.
     *
     * @var string|null Title
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $title = null;

    /**
     * Created at.
     *
     * @var \DateTimeImmutable|null created At
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Updated at.
     *
     * @var \DateTimeImmutable|null Updated At
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Content.
     *
     * @var string|null Content
     */
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)] // do zmiany
    private ?string $content = null;

    /**
     * Category.
     *
     * @var Category|null Category
     */
    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EXTRA_LAZY')]
    #[Assert\Type(Category::class)]
    #[Assert\NotBlank]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * Slug.
     *
     * @var string|null Slug
     */
    #[ORM\Column(length: 255)]
    #[Gedmo\Slug(fields: ['title'])]
    private ?string $slug = null;

    /**
     * Tags.
     *
     * @var Collection<int, Tag> Tags
     */
    #[Assert\Valid]
    #[ORM\ManyToMany(targetEntity: Tag::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\JoinTable(name: 'recipes_tags')]
    private Collection $tags;

    /**
     * Author.
     *
     * @var User|null Author
     */
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(User::class)]
    private ?User $author;

    /**
     * Comments.
     *
     * @var Collection<int, Comment> Comments
     */
    #[Assert\Valid]
    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Comment::class, cascade: ['persist', 'remove'], orphanRemoval: true)] #[ORM\JoinTable(name: 'recipes_comments')]
    private Collection $comments;

    /**
     * Ratings.
     *
     * @var Collection<int, Rating> Ratings
     */
    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Rating::class, cascade: ['remove'])]
    private Collection $ratings;

    /**
     * AverageRating.
     *
     * @var float|null Average Rating
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $averageRating = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->ratings = new ArrayCollection();
    }

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for Title.
     *
     * @return string|null Title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for title.
     *
     * @param string $title Title
     *
     * @return $this Title
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Setter for id.
     *
     * @param int $id id
     *
     * @return $this id
     */
    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Getter for createdAt.
     *
     * @return \DateTimeImmutable|null createdAt
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Setter for createdAt.
     *
     * @param \DateTimeImmutable $createdAt createdAt
     *
     * @return $this createdAt
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Getter for updatedAt.
     *
     * @return \DateTimeImmutable|null updatedAt
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Setter for updatedAt.
     *
     * @param \DateTimeImmutable $updatedAt updatedAt
     *
     * @return $this updatedAt
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Getter for content.
     *
     * @return string|null content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Setter for content.
     *
     * @param string $content content
     *
     * @return $this content
     */
    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Getter for category.
     *
     * @return Category|null category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Setter for category.
     *
     * @param Category|null $category category
     *
     * @return $this category
     */
    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Getter for slug.
     *
     * @return string|null slug
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Setter for slug.
     *
     * @param string $slug slug
     *
     * @return $this slug
     */
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Getter for tags.
     *
     * @return Collection<int, Tag> tags
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Add for tags.
     *
     * @param Tag $tag tags
     *
     * @return $this tags
     */
    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    /**
     * Remove for tag.
     *
     * @param Tag $tag Remove tag
     *
     * @return $this Remove tag
     */
    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * Getter for author.
     *
     * @return User|null Author
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Setter for author.
     *
     * @param User|null $author author
     *
     * @return $this author
     */
    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Getter for comments.
     *
     * @return Collection comments
     */
    public function getComments(): Collection
    {
        $criteria = Criteria::create()
            ->orderBy(['createdAt' => Order::Descending]);

        return $this->comments->matching($criteria);
    }

    /**
     * Add for comments.
     *
     * @param Comment $comment add comment
     *
     * @return $this add comment
     */
    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
        }

        return $this;
    }

    /**
     * Remove for comments.
     *
     * @param Comment $comment remove comment
     *
     * @return $this remove comment
     */
    public function removeComment(Comment $comment): static
    {
        $this->comments->removeElement($comment);

        return $this;
    }

    /**
     * Getter for averageRating.
     *
     * @return float|null average Rating
     */
    public function getAverageRating(): ?float
    {
        return $this->averageRating;
    }

    /**
     * Setter for averageRating.
     *
     * @param float $averageRating average Rating
     *
     * @return $this average Rating
     */
    public function setAverageRating(float $averageRating): self
    {
        $this->averageRating = $averageRating;

        return $this;
    }

    /**
     * Getter for ratings.
     *
     * @return Collection ratings
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    /**
     * Add for ratings.
     *
     * @param Rating $rating ratings
     *
     * @return $this ratings
     */
    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setRecipe($this);
        }

        return $this;
    }

    /**
     * Remove for ratings.
     *
     * @param Rating $rating ratings
     *
     * @return $this ratings
     */
    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->removeElement($rating)) {
            if ($rating->getRecipe() === $this) {
                $rating->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * Operation for averageRating.
     *
     * @return void averageRating
     */
    public function calculateAverageRating(): void
    {
        $sum = 0;
        $count = 0;
        if (null !== $this->ratings) {
            foreach ($this->ratings as $rating) {
                $sum += $rating->getValue();
                ++$count;
            }
        }
        if ($count > 0) {
            $averageRating = $sum / $count;
            $this->setAverageRating($averageRating);
        } else {
            $this->setAverageRating(0);
        }
    }
}
