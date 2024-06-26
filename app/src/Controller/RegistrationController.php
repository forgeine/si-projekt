<?php
/**
 * RegistrationController.
 */

namespace App\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Form\Type\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class RegistrationController.
 */
#[Route('/register')]
class RegistrationController extends AbstractController
{
    /**
     * Construct
     *
     * @param UserPasswordHasherInterface $passwordHasher
     * @param TranslatorInterface         $translator
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Registration of users.
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    #[Route(name: 'user_register')]
    public function register(Request $request, EntityManagerInterface $em): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $user->setRoles([UserRole::ROLE_USER->value]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', $this->translator->trans('message.registration_successful'));

            return $this->redirectToRoute('profile_index');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
