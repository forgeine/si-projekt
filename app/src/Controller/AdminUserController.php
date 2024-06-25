<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\Type\AdminEditType;
use App\Form\Type\AdminPasswordType;
use App\Repository\UserRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/users')]
class AdminUserController extends AbstractController
{
    private TranslatorInterface $translator;
    private RecipeRepository $recipeRepository;

    public function __construct(TranslatorInterface $translator, RecipeRepository $recipeRepository)
    {
        $this->translator = $translator;
        $this->recipeRepository = $recipeRepository;
    }
    #[Route(name: 'edit_users')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('profile/admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit_user')]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AdminEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('edit_users');
        }

        return $this->render('profile/admin/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @param User $user
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/change-password/{id}', name: 'admin_change_password')]
    public function changePassword(User $user, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AdminPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $data['newPasswordAdmin']));
            $em->flush();
            $this->addFlash('success', $this->translator->trans('message.password_updated_successfully'));

            return $this->redirectToRoute('edit_users');
        }

        return $this->render('profile/admin/password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
    #[Route('/delete/{id}', name: 'delete_user', methods: ['POST'])]
    public function delete(User $user, EntityManagerInterface $em): Response
    {
        $currentUser = $this->getUser();

        if ($currentUser->getId() === $user->getId()) {
            $this->addFlash('warning', $this->translator->trans('message.cannot_delete_yourself'));
            return $this->redirectToRoute('edit_users');
        }

        $recipes = $this->recipeRepository->findBy(['author' => $user]);

        foreach ($recipes as $recipe) {
            $em->remove($recipe);
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

        return $this->redirectToRoute('edit_users');
    }
}
