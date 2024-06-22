<?php
/**
 * Task list filters DTO.
 */

namespace App\Dto;

use App\Entity\Category;
use App\Entity\Enum\RecipeStatus;
use App\Entity\Tag;

/**
 * Class TaskListFiltersDto.
 */
class RecipeListFiltersDto
{
    /**
     * Constructor.
     *
     * @param Category|null $category   Category entity
     * @param Tag|null      $tag        Tag entity
     * @param RecipeStatus    $taskStatus Task status
     */
    public function __construct(public readonly ?Category $category, public readonly ?Tag $tag, public readonly RecipeStatus $taskStatus)
    {
    }
}