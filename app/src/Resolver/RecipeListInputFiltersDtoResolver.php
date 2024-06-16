<?php
/**
 * RecipeListInputFiltersDto resolver.
 */

namespace App\Resolver;

use App\Dto\recipeListInputFiltersDto;
use App\Entity\Enum\RecipeStatus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * RecipeListInputFiltersDtoResolver class.
 */
class RecipeListInputFiltersDtoResolver implements ValueResolverInterface
{
    /**
     * Returns the possible value(s).
     *
     * @param Request          $request  HTTP Request
     * @param ArgumentMetadata $argument Argument metadata
     *
     * @return iterable Iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();

        if (!$argumentType || !is_a($argumentType, recipeListInputFiltersDto::class, true)) {
            return [];
        }

        $categoryId = $request->query->get('categoryId');
        $tagId = $request->query->get('tagId');
        $statusId = $request->query->get('statusId', RecipeStatus::ACTIVE->value);

        return [new recipeListInputFiltersDto($categoryId, $tagId, $statusId)];
    }
}
