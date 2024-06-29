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
     * @param UserPasswordHasherInterface $passwordHasher Password Hasher
     * @param TranslatorInterface         $translator Translator
     * @param EntityManagerInterface      $entityManager Entity Manager
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly TranslatorInterface $translator, private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Register user.
     *
     * @param User $user Entity user
     *
     * @return void Void
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
     * @return string Success
     */
    public function getSuccessMessage(): string
    {
        return $this->translator->trans('message.registration_successful');
    }
}
