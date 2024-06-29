<?php
/**
 * Recipe repository.
 */

namespace App\Repository;

use App\Dto\RecipeListFiltersDto;
use App\Entity\Category;
use App\Entity\Recipe;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
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
     * Paginator items.
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry        $registry      ManagerRegistry
     * @param EntityManagerInterface $entityManager EntityManagerInterface
     */
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Recipe::class);
        $this->entityManager = $entityManager;
    }

    /**
     * QueryAll.
     *
     * @param RecipeListFiltersDto $filters RecipeListFiltersDto
     *
     * @return QueryBuilder Aply filters
     */
    public function queryAll(RecipeListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->getOrCreateQueryBuilder()
            ->select(
                'partial recipe.{id, createdAt, updatedAt, title, content, averageRating}',
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
     * Count by category.
     *
     * @param Category $category Entity
     *
     * @return int Count by category
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
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

    /**
     * Count by tag.
     *
     * @param Tag $tag Entity
     *
     * @return int Count by tag
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
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
     * Entity save.
     *
     * @param Recipe $recipe Entity
     *
     * @return void Void
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
     * Entity delete.
     *
     * @param Recipe $recipe          Entity
     * @param bool   $cascadeComments Comments
     * @param bool   $cascadeRatings  Ratings
     */
    public function delete(Recipe $recipe, bool $cascadeComments = true, bool $cascadeRatings = true): void
    {
        $entityManager = $this->getEntityManager();
        if ($cascadeComments) {
            foreach ($recipe->getComments() as $comment) {
                $entityManager->remove($comment);
            }
        }
        if ($cascadeRatings) {
            foreach ($recipe->getRatings() as $rating) {
                $entityManager->remove($rating);
            }
        }
        $entityManager->remove($recipe);
        $entityManager->flush();
    }

    /**
     * Query by author.
     *
     * @param UserInterface        $user    User
     * @param RecipeListFiltersDto $filters Filters
     *
     * @return QueryBuilder QueryBuilder
     */
    public function queryByAuthor(UserInterface $user, RecipeListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->queryAll($filters);
        $queryBuilder->andWhere('recipe.author = :author')
            ->setParameter('author', $user);

        return $queryBuilder;
    }

    /**
     * Get or create query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder for recipe
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('recipe');
    }

    /**
     * Apply filters to list.
     *
     * @param QueryBuilder         $queryBuilder QueryBuilder
     * @param RecipeListFiltersDto $filters      Filters
     *
     * @return QueryBuilder Applying filters
     */
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

    /**
     * Calculate average rating.
     *
     * @param Recipe $recipe Entity
     *
     * @return void Void
     */
    private function calculateAverageRating(Recipe $recipe): void
    {
        $entityManager = $this->doctrine->getManager();
        $sum = 0;
        $ratings = $recipe->getRatings();
        $count = $ratings->count();
        if ($count > 0) {
            foreach ($ratings as $rating) {
                $sum += $rating->getValue();
            }
            $averageRating = $sum / $count;
        } else {
            $averageRating = null;
        }
        $recipe->setAverageRating($averageRating);
        $entityManager->persist($recipe);
        $entityManager->flush();
    }
}
