<?php
/**
 * Recipe service.
 */

namespace App\Service;

use App\Dto\RecipeListFiltersDto;
use App\Repository\CommentRepository;
use App\Dto\RecipeListInputFiltersDto;
use App\Entity\Comment;
use App\Entity\Enum\RecipeStatus;
use App\Entity\Recipe;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\RecipeRepository;
use Doctrine\ORM\NonUniqueResultException;
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
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param RecipeRepository     $recipeRepository recipe repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(private readonly CategoryServiceInterface $categoryService, private readonly PaginatorInterface $paginator, private readonly TagServiceInterface $tagService, private readonly CommentRepository $commentRepository, private readonly RecipeRepository $recipeRepository)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int  $page   Page number
     * @param User $author Author
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, ?User $author, RecipeListInputFiltersDto $filters): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);
        if ($author == null) {
            return $this->paginator->paginate(
            $this->recipeRepository->queryAll($filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );}
        else {
            return $this->paginator->paginate(
                $this->recipeRepository->queryByAuthor($author, $filters),
                $page,
                self::PAGINATOR_ITEMS_PER_PAGE
            );
        }
    }
    /**
     * Save entity.
     *
     * @param Recipe $recipe Recipe entity
     */
    public function save(Recipe $recipe): void
    {
        $this->recipeRepository->save($recipe);
    }

    /**
     * Delete entity.
     *
     * @param Recipe $recipe recipe entity
     */
    public function delete(Recipe $recipe): void
    {
        $this->recipeRepository->delete($recipe);
    }
    /**
     * Find by title.
     *
     * @param string $title Tag title
     *
     * @return Tag|null Tag entity
     */
    public function findOneByTitle(string $title): ?Tag
    {
        return $this->tagRepository->findOneByTitle($title);
    }

    /**
     * Prepare filters for the recipe list.
     *
     * @param RecipeListInputFiltersDto $filters Raw filters from request
     *
     * @return RecipeListFiltersDto Result filters
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
    public function saveComment(Comment $comment): void
    {
        $this->commentRepository->save($comment);
    }
    public function deleteComment(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }
}
