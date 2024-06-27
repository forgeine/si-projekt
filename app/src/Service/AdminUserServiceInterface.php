<?php
/**
 * Admin User Service Interface.
 */

namespace App\Service;

use App\Entity\User;

/**
 * interface AdminUserServiceInterface.
 */
interface AdminUserServiceInterface
{
    /**
     * getAllUsers.
     *
     * @return array
     */
    public function getAllUsers(): array;

    /**
     * updateUser.
     *
     * @param User $user
     *
     * @return void
     */
    public function updateUser(User $user): void;

    /**
     * changePassword.
     *
     * @param User   $user
     * @param string $newPassword
     *
     * @return void
     */
    public function changePassword(User $user, string $newPassword): void;

    /**
     * deleteUser.
     *
     * @param User $user
     *
     * @return void
     */
    public function deleteUser(User $user): void;
}
