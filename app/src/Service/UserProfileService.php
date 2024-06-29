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
     * Construct.
     *
     * @param UserPasswordHasherInterface $passwordHasher Password Hasher
     * @param EntityManagerInterface      $em             Entity Manager
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly EntityManagerInterface $em)
    {
    }

    /**
     * updateUser.
     *
     * @param User $user Entity User
     *
     * @return void Void
     */
    public function updateUser(User $user): void
    {
        $this->em->flush();
    }

    /**
     * Change password.
     *
     * @param User   $user               Entity User
     * @param string $currentPassword    Current Password
     * @param string $newPassword        New Password
     * @param string $confirmNewPassword Confirm New
     *
     * @return bool Result
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
