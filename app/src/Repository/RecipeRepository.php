<?php
/**
 * Recipe repository.
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

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
 * @return \Doctrine\ORM\QueryBuilder Query builder
 */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial recipe.{id, createdAt, updatedAt, title, content}',
                'partial category.{id, title}'
            )
            ->join('recipe.category', 'category')
            ->orderBy('recipe.updatedAt', 'DESC');
    }
    public function countByCategory(Category $category): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('recipe.id'))
            ->where('recipe.category = :category')
            ->setParameter(':category', $category)
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
     * Query tasks by author.
     *
     * @param User $user User entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthor(User $user): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        $queryBuilder->andWhere('recipe.author = :author')
            ->setParameter('author', $user);

        return $queryBuilder;
    }
}
