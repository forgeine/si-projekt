<?php
/**
 * Recipe repository.
 */

namespace App\Repository;

use App\Dto\RecipeListFiltersDto;
use App\Entity\Category;
use App\Entity\Enum\RecipeStatus;
use App\Entity\Recipe;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class RecipeRepository.
 *
 * @method recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method recipe[]    findAll()
 * @method recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<recipe>
 *
 * @psalm-suppress LessSpecificImplementedReturnType
 */
class RecipeRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in configuration files.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, recipe::class);
    }
 /**
 * Query all records.
 *
 * @return QueryBuilder Query builder
 */
    public function queryAll(RecipeListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->getOrCreateQueryBuilder()
            ->select(
                'partial recipe.{id, createdAt, updatedAt, title, content}',
                'partial category.{id, title}',
                'partial tags.{id, title}',
                'partial author.{id, email}'
            )
            ->join('recipe.category', 'category')
            ->leftJoin('recipe.tags', 'tags')
            ->leftJoin('recipe.author', 'author')
            ->orderBy('recipe.updatedAt', 'DESC');

        return $this->applyFiltersToList($queryBuilder, $filters);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countByCategory(Category $category): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('recipe.id'))
            ->where('recipe.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function countByTag(Tag $tag): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('recipe.id'))
            ->join('recipe.tags', 't')
            ->where('t = :tag')
            ->setParameter('tag', $tag)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Save entity.
     *
     * @param Recipe $recipe Recipe entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Recipe $recipe): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($recipe);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Recipe $recipe Recipe entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(recipe $recipe): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($recipe);
        $this->_em->flush();
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('recipe');
    }
    /**
     * Query recipes by author.
     *
     * @param User $user User entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthor(UserInterface $user, RecipeListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->queryAll($filters);

        $queryBuilder->andWhere('recipe.author = :author')
            ->setParameter('author', $user);

        return $queryBuilder;
    }
    private function applyFiltersToList(QueryBuilder $queryBuilder, RecipeListFiltersDto $filters): QueryBuilder
    {
        if ($filters->category instanceof Category) {
            $queryBuilder->andWhere('category = :category')
                ->setParameter('category', $filters->category);
        }

        if ($filters->tag instanceof Tag) {
            $queryBuilder->andWhere('tags IN (:tag)')
                ->setParameter('tag', $filters->tag);
        }
        return $queryBuilder;
    }
}
