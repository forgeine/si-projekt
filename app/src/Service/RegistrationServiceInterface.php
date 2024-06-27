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
     * @param User $user
     *
     * @return void
     */
    public function registerUser(User $user): void;

    /**
     * Get success message.
     *
     * @return string
     */
    public function getSuccessMessage(): string;
}
