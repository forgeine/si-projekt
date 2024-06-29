<?php
/**
 * UserProfileController.
 */

namespace App\Controller;

use App\Form\Type\UserEditType;
use App\Form\Type\UserPasswordType;
use App\Service\UserProfileServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserProfileController.
 */
#[Route('/profile')]
class UserProfileController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserProfileServiceInterface $userProfileService User Profile Service Interface
     * @param TranslatorInterface         $translator         Translator
     */
    public function __construct(private UserProfileServiceInterface $userProfileService, private TranslatorInterface $translator)
    {
    }

    /**
     * User edit index.
     *
     * @param Request $request Request
     *
     * @return Response Index
     */
    #[Route(name: 'profile_index')]
    public function index(Request $request): Response
    {
        return $this->render('profile/user/index.html.twig');
    }

    /**
     * Changing own email, action edit.
     *
     * @param Request $request Request
     *
     * @return Response Edit
     */
    #[Route('/edit', name: 'profile_edit')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userProfileService->updateUser($user);
            $this->addFlash('success', $this->translator->trans('message.user_updated_successfully'));

            return $this->redirectToRoute('profile_edit');
        }

        return $this->render('profile/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Changing your own password, action changePassword.
     *
     * @param Request $request Request
     *
     * @return Response Change password
     */
    #[Route('/change-password', name: 'profile_password')]
    public function changePassword(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($this->userProfileService->validateAndChangePassword($user, $data['currentPassword'], $data['newPassword'], $data['confirmNewPassword'])) {
                $this->addFlash('success', $this->translator->trans('message.password_updated_successfully'));

                return $this->redirectToRoute('profile_password');
            }
            $this->addFlash('warning', $this->translator->trans('message.password_error'));
        }

        return $this->render('profile/user/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
