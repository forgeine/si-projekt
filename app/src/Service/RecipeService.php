<?php
/**
 * Recipe service.
 */

namespace App\Service;

use App\Entity\Recipe;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\RecipeRepository;
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
    public function __construct(private readonly RecipeRepository $recipeRepository, private readonly PaginatorInterface $paginator)
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
    public function getPaginatedList(int $page, ?User $author = null): PaginationInterface
    {
        $queryBuilder = $author ? $this->recipeRepository->queryByAuthor($author) : $this->recipeRepository->queryAll();

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
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
}
