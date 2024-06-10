<?php
/**
 * Tag service interface.
 */

namespace App\Service;

use App\Entity\Tag;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface TagServiceInterface.
 */
interface TagServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;
    public function save(Tag $tag): void;

    public function delete(Tag $tag): void;

    /**
     * Can Category be deleted?
     *
     * @param Tag $tag Tag entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Tag $tag): bool;

}