<?php
/**
 * User Profile Service Interface.
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface UserProfileServiceInterface.
 */
interface UserProfileServiceInterface
{
    /**
     * updateUser.
     *
     * @param User $user
     *
     * @return void
     */
    public function updateUser(User $user): void;

    /**
     * validateAndChangePassword.
     *
     * @param User   $user
     * @param string $currentPassword
     * @param string $newPassword
     * @param string $confirmNewPassword
     *
     * @return bool
     */
    public function validateAndChangePassword(User $user, string $currentPassword, string $newPassword, string $confirmNewPassword): bool;
}
