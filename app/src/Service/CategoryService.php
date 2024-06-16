<?php
/**
 * Category service.
 */

namespace App\Service;

use App\Entity\Category;
//use App\Form\Type\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CategoryService.
 */
class CategoryService implements CategoryServiceInterface
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
     * @param CategoryRepository $categoryRepository Category repository
     * @param PaginatorInterface $paginator      Paginator
     * @param RecipeRepository $recipeRepository Task repository
     */
    public function __construct(private readonly CategoryRepository $categoryRepository,
                                private readonly PaginatorInterface $paginator,
                                private readonly RecipeRepository $recipeRepository)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->categoryRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Category $category Category entity
     * @throws ORMException
     */
    public function save(Category $category): void
    {
       // if (null == $category->getId()) {
        //    $category->setCreatedAt(new \DateTimeImmutable());
       // }
        //$category->setUpdatedAt(new \DateTimeImmutable());

        $this->categoryRepository->save($category);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }

    /**
     * Can Category be deleted?
     *
     * @param Category $category Category entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Category $category): bool
    {
        try {
            $result = $this-> recipeRepository ->countByCategory($category);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }





}