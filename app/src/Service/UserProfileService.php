<?php
/**
 * User Profile Service.
 */

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserProfileService.
 */
class UserProfileService implements UserProfileServiceInterface
{
    /**
     * Construct
     *
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface      $em
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly EntityManagerInterface $em)
    {
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
     * Change password.
     *
     * @param User   $user
     * @param string $currentPassword
     * @param string $newPassword
     * @param string $confirmNewPassword
     *
     * @return bool
     */
    public function validateAndChangePassword(User $user, string $currentPassword, string $newPassword, string $confirmNewPassword): bool
    {
        if ($this->passwordHasher->isPasswordValid($user, $currentPassword) && $newPassword === $confirmNewPassword) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
            $this->em->flush();

            return true;
        }

        return false;
    }
}
