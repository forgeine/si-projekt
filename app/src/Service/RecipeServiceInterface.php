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
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
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
    public function findOneByTitle(string $title): ?Tag;
    public function saveComment(Comment $comment): void;
    public function deleteComment(Comment $comment): void;

}
