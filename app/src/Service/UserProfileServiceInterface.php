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
     * @param User $user Entity User
     *
     * @return void Void
     */
    public function updateUser(User $user): void;

    /**
     * validateAndChangePassword.
     *
     * @param User   $user Entity User
     * @param string $currentPassword Current Password
     * @param string $newPassword New Password
     * @param string $confirmNewPassword Confirm New
     *
     * @return bool Result
     */
    public function validateAndChangePassword(User $user, string $currentPassword, string $newPassword, string $confirmNewPassword): bool;
}
