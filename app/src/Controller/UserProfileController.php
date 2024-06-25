<?php
namespace App\Controller;

use App\Form\Type\UserEditType;
use App\Form\Type\UserPasswordType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

//use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/profile')]
class UserProfileController extends AbstractController
    {
    public function __construct(private UserPasswordHasherInterface $passwordHasher, private Security $security,private readonly TranslatorInterface $translator)
    {
    }
    #[Route(name: 'profile_index')]
    public function index(Request $request): Response
    {
        return $this->render('profile/user/index.html.twig');
    }
    #[Route('/edit', name: 'profile_edit')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', $this->translator->trans('message.created_successfully'));
            return $this->redirectToRoute('profile_edit');
        }

        return $this->render('profile/user/edit.html.twig', [
        'form' => $form->createView(),
        ]);
    }

    #[Route('/change-password', name: 'profile_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($this->passwordHasher->isPasswordValid($user, $data['currentPassword']) && $data['newPassword'] === $data['confirmNewPassword']) {
                $user->setPassword($passwordHasher->hashPassword($user, $data['newPassword']));
                $em->flush();
                $this->addFlash('success', $this->translator->trans('message.created_successfully'));

                return $this->redirectToRoute('profile_password');
            }
            else{
                $this->addFlash('warning', $this->translator->trans('message.record_not_found'));
            }
        }

        return $this->render('profile/user/password.html.twig', [
        'form' => $form->createView(),
        ]);
    }
}
