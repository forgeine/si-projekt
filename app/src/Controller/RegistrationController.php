<?php
/**
 * RegistrationController.
 */

namespace App\Controller;

use App\Form\Type\RegistrationFormType;
use App\Service\RegistrationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController.
 */
#[Route('/register')]
class RegistrationController extends AbstractController
{
    /**
     * Construct
     *
     * @param RegistrationServiceInterface $registrationService
     */
    public function __construct(private readonly RegistrationServiceInterface $registrationService)
    {
    }

    /**
     * Registration of users.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[Route(name: 'user_register')]
    public function register(Request $request): Response
    {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->registrationService->registerUser($form->getData());
            $this->addFlash('success', $this->registrationService->getSuccessMessage());

            return $this->redirectToRoute('profile_index');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
