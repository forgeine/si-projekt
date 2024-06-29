<?php
/**
 * Registration Service Interface.
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface RegistrationServiceInterface.
 */
interface RegistrationServiceInterface
{
    /**
     * Register User.
     *
     * @param User $user Entity User
     *
     * @return void Void
     */
    public function registerUser(User $user): void;

    /**
     * Get success message.
     *
     * @return string Success
     */
    public function getSuccessMessage(): string;
}
