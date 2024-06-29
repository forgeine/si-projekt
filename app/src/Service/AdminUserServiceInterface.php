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
     * @return array All users
     */
    public function getAllUsers(): array;

    /**
     * updateUser.
     *
     * @param User $user Entity User
     *
     * @return void Void
     */
    public function updateUser(User $user): void;

    /**
     * changePassword.
     *
     * @param User   $user        Entity User
     * @param string $newPassword New Password
     *
     * @return void Void
     */
    public function changePassword(User $user, string $newPassword): void;

    /**
     * deleteUser.
     *
     * @param User $user Entity User
     *
     * @return void Void
     */
    public function deleteUser(User $user): void;
}
