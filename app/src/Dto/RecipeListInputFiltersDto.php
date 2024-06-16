<?php
/**
 * Recipe list input filters DTO.
 */

namespace App\Dto;

/**
 * Class RecipeListInputFiltersDto.
 */
class RecipeListInputFiltersDto
{
    /**
     * Constructor.
     *
     * @param int|null $categoryId Category identifier
     * @param int|null $tagId      Tag identifier
     * @param int      $statusId   Status identifier
     */
    public function __construct(public readonly ?int $categoryId = null, public readonly ?int $tagId = null, public readonly int $statusId = 1)
    {
    }
}