<?php
/**
 * Recipe service.
 */

namespace App\Service;

use App\Dto\RecipeListFiltersDto;
use App\Dto\RecipeListInputFiltersDto;
use App\Entity\Comment;
use App\Entity\Enum\RecipeStatus;
use App\Entity\Rating;
use App\Entity\Recipe;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\RatingRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class RecipeService.
 */
class RecipeService implements RecipeServiceInterface
{
    /**
     * Items per page.
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor
     *
     * @param CategoryServiceInterface $categoryService
     * @param PaginatorInterface       $paginator
     * @param TagServiceInterface      $tagService
     * @param CommentRepository        $commentRepository
     * @param RecipeRepository         $recipeRepository
     * @param RatingRepository         $ratingRepository
     */
    public function __construct(private readonly CategoryServiceInterface $categoryService, private readonly PaginatorInterface $paginator, private readonly TagServiceInterface $tagService, private readonly CommentRepository $commentRepository, private readonly RecipeRepository $recipeRepository, private readonly RatingRepository $ratingRepository)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int                       $page
     * @param User|null                 $author
     * @param RecipeListInputFiltersDto $filters
     *
     * @return PaginationInterface
     *
     * @throws NonUniqueResultException
     */
    public function getPaginatedList(int $page, ?User $author, RecipeListInputFiltersDto $filters): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);
        if (null === $author) {
            return $this->paginator->paginate(
                $this->recipeRepository->queryAll($filters),
                $page,
                self::PAGINATOR_ITEMS_PER_PAGE
            );
        }

        return $this->paginator->paginate(
            $this->recipeRepository->queryByAuthor($author, $filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Recipe $recipe
     *
     * @return void
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Recipe $recipe): void
    {
        $this->recipeRepository->save($recipe);
    }

    /**
     * Delete entity.
     *
     * @param Recipe $recipe
     *
     * @return void
     */
    public function delete(Recipe $recipe): void
    {
        $this->recipeRepository->delete($recipe);
    }

    /**
     * Find by title.
     *
     * @param string $title
     *
     * @return Tag|null
     */
    public function findOneByTitle(string $title): ?Tag
    {
        return $this->tagRepository->findOneByTitle($title);
    }

    /**
     * Save comment.
     *
     * @param Comment $comment
     *
     * @return void
     */
    public function saveComment(Comment $comment): void
    {
        $this->commentRepository->save($comment);
    }

    /**
     * Delete comment.
     *
     * @param Comment $comment
     *
     * @return void
     */
    public function deleteComment(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }

    /**
     * Save rating.
     *
     * @param Rating $rating
     *
     * @return void
     */
    public function saveRating(Rating $rating): void
    {
        $this->ratingRepository->save($rating);
    }

    /**
     * Prepare filters for list.
     *
     * @param RecipeListInputFiltersDto $filters
     *
     * @return RecipeListFiltersDto
     *
     * @throws NonUniqueResultException
     */
    private function prepareFilters(RecipeListInputFiltersDto $filters): RecipeListFiltersDto
    {
        return new RecipeListFiltersDto(
            null !== $filters->categoryId ? $this->categoryService->findOneById($filters->categoryId) : null,
            null !== $filters->tagId ? $this->tagService->findOneById($filters->tagId) : null,
            RecipeStatus::tryFrom($filters->statusId)
        );
    }
}
