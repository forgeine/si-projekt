<?php
/**
 * Registration Service.
 */

namespace App\Service;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class RegistrationService.
 */
class RegistrationService implements RegistrationServiceInterface
{
    /**
     * Constructor
     *
     * @param UserPasswordHasherInterface $passwordHasher
     * @param TranslatorInterface         $translator
     * @param EntityManagerInterface      $entityManager
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly TranslatorInterface $translator, private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Register user.
     *
     * @param User $user
     *
     * @return void
     */
    public function registerUser(User $user): void
    {
        $user->setRoles([UserRole::ROLE_USER->value]);
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * Get success message
     *
     * @return string
     */
    public function getSuccessMessage(): string
    {
        return $this->translator->trans('message.registration_successful');
    }
}
