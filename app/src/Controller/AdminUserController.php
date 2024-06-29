<?php
/**
 * AdminUserController.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\AdminEditType;
use App\Form\Type\AdminPasswordType;
use App\Service\AdminUserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AdminUserController.
 */
#[Route('/users')]
class AdminUserController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TranslatorInterface       $translator       translations
     * @param AdminUserServiceInterface $adminUserService interface
     */
    public function __construct(private TranslatorInterface $translator, private AdminUserServiceInterface $adminUserService)
    {
    }

    /**
     * Index for admin tools.
     *
     * @return Response edit user
     */
    #[Route(name: 'edit_users')]
    public function index(): Response
    {
        $users = $this->adminUserService->getAllUsers();

        return $this->render('profile/admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * Admin tool for changing email of users.
     *
     * @param User    $user    entity
     * @param Request $request request
     *
     * @return Response edit user of id
     */
    #[Route('/edit/{id}', name: 'edit_user')]
    public function edit(User $user, Request $request): Response
    {
        $form = $this->createForm(AdminEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->adminUserService->updateUser($user);
            $this->addFlash('success', $this->translator->trans('message.user_updated_successfully'));

            return $this->redirectToRoute('edit_users');
        }

        return $this->render('profile/admin/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Admin tool for changing passwords.
     *
     * @param User    $user    entity
     * @param Request $request request
     *
     * @return Response change password of id
     */
    #[Route('/change-password/{id}', name: 'admin_change_password')]
    public function changePassword(User $user, Request $request): Response
    {
        $form = $this->createForm(AdminPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->adminUserService->changePassword($user, $form->getData()['newPasswordAdmin']);
            $this->addFlash('success', $this->translator->trans('message.password_updated_successfully'));

            return $this->redirectToRoute('edit_users');
        }

        return $this->render('profile/admin/password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Admin tool for deleting users and their content.
     *
     * @param User $user entity
     *
     * @return Response delete user of id
     */
    #[Route('/delete/{id}', name: 'delete_user', methods: ['POST'])]
    public function delete(User $user): Response
    {
        $currentUser = $this->getUser();
        if ($currentUser->getId() === $user->getId()) {
            $this->addFlash('danger', $this->translator->trans('message.cannot_delete_yourself'));

            return $this->redirectToRoute('edit_users');
        }
        $this->adminUserService->deleteUser($user);
        $this->addFlash('success', $this->translator->trans('message.user_deleted_successfully'));

        return $this->redirectToRoute('edit_users');
    }
}
