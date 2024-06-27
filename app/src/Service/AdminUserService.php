<?php
/**
 * AdminUserService
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class AdminUserService.
 */
class AdminUserService implements AdminUserServiceInterface
{
    /**
     * Constructor
     *
     * @param UserRepository              $userRepository
     * @param RecipeRepository            $recipeRepository
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface      $em
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly RecipeRepository $recipeRepository, private readonly UserPasswordHasherInterface $passwordHasher, private readonly EntityManagerInterface $em)
    {
    }

    /**
     * getAllUsers.
     *
     * @return array
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * updateUser.
     *
     * @param User $user
     *
     * @return void
     */
    public function updateUser(User $user): void
    {
        $this->em->flush();
    }

    /**
     * changePassword.
     *
     * @param User   $user
     * @param string $newPassword
     *
     * @return void
     */
    public function changePassword(User $user, string $newPassword): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        $this->em->flush();
    }

    /**
     * deleteUser.
     *
     * @param User $user
     *
     * @return void
     */
    public function deleteUser(User $user): void
    {
        $recipes = $this->recipeRepository->findByAuthor($user);
        foreach ($recipes as $recipe) {
            $this->em->remove($recipe);
        }
        $this->em->remove($user);
        $this->em->flush();
    }
}
