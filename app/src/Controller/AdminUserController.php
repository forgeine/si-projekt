<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserEditType;
use App\Form\Type\UserPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/admin/user')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'admin_user_index')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('admin/user/index.html.twig', ['users' => $users]);
    }

    #[Route('/edit/{id}', name: 'admin_user_edit')]
    public function edit(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();
        $this->addFlash('success', 'User updated successfully.');
        return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
        'form' => $form->createView(),
        ]);
    }

    #[Route('/change-password/{id}', name: 'admin_user_change_password')]
    public function changePassword(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em): Response
    {
    $form = $this->createForm(UserPasswordType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $user->setPassword($passwordEncoder->encodePassword($user, $data['newPassword']));
        $em->flush();
        $this->addFlash('success', 'Password changed successfully.');
        return $this->redirectToRoute('admin_user_index');
    }

    return $this->render('admin/user/change_password.html.twig', [
    'form' => $form->createView(),
    ]);
    }
}
