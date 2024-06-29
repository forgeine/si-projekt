<?php
/**
 * Recipe service interface.
 */

namespace App\Service;

use App\Dto\RecipeListInputFiltersDto;
use App\Entity\Comment;
use App\Entity\Recipe;
use App\Entity\Tag;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface RecipeServiceInterface.
 */
interface RecipeServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int                       $page Page
     * @param User                      $author Author
     * @param RecipeListInputFiltersDto $filters Filters
     *
     * @return PaginationInterface Pagination
     */
    public function getPaginatedList(int $page, User $author, RecipeListInputFiltersDto $filters): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Recipe $recipe Recipe entity
     */
    public function save(Recipe $recipe): void;

    /**
     * Delete entity.
     *
     * @param Recipe $recipe Recipe entity
     */
    public function delete(Recipe $recipe): void;

    /**
     * Find one by title.
     *
     * @param string $title title
     *
     * @return Tag|null Tag
     */
    public function findOneByTitle(string $title): ?Tag;

    /**
     * Save comment.
     *
     * @param Comment $comment Entity comment
     *
     * @return void Void
     */
    public function saveComment(Comment $comment): void;

    /**
     * Delete comment.
     *
     * @param Comment $comment Entity comment
     *
     * @return void Void
     */
    public function deleteComment(Comment $comment): void;
}
