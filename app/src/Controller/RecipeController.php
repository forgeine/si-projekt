<?php
/**
 * Recipe controller.
 */

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use App\Dto\RecipeListInputFiltersDto;
use App\Resolver\RecipeListInputFiltersDtoResolver;
use App\Form\Type\RecipeType;
use App\Service\RecipeService;
use App\Service\RecipeServiceInterface;
use App\Service\TagServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class RecipeController.
 */
#[Route('/recipe')]
class RecipeController extends AbstractController
{
    /**
     * @param RecipeServiceInterface $recipeService
     * @param TagServiceInterface $tagService
     * @param TranslatorInterface $translator
     */
    public function __construct(private readonly RecipeServiceInterface $recipeService, private readonly TagServiceInterface $tagService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route(name: 'recipe_index', methods: 'GET')]
    //#[IsGranted('ROLE_ADMIN')]
    public function index(#[MapQueryString(resolver: RecipeListInputFiltersDtoResolver::class)] RecipeListInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        $user = $this ->getUser();
        $pagination = $this->recipeService->getPaginatedList(
            $page,
            null,
            $filters
        );
        return $this->render('recipe/index.html.twig', ['pagination' => $pagination]);
    }

    #[Route(
        '/own',
        name: 'recipe_own',
        methods: 'GET'
    )]
    //#[IsGranted('ROLE_ADMIN')]
    public function own(#[MapQueryString(resolver: RecipeListInputFiltersDtoResolver::class)] RecipeListInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        $user = $this ->getUser();
        $pagination = $this->recipeService->getPaginatedList(
            $page,
            $user,
            $filters
        );
        return $this->render('recipe/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Recipe $recipe recipe
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'recipe_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    //#[IsGranted('ROLE_ADMIN')]
    public function show(Recipe $recipe): Response
    {
        return $this->render('recipe/show.html.twig', ['recipe' => $recipe]);
    }
    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'recipe_create', methods: 'GET|POST')]
    //#[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $recipe = new Recipe();
        $recipe->setAuthor($user);
        $form = $this->createForm(
            RecipeType::class,
            $recipe,
            ['action' => $this->generateUrl('recipe_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recipeService->save($recipe);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render(
            'recipe/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Recipe    $recipe    Recipe entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'recipe_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    //#[IsGranted('EDIT', subject: 'recipe')]
    public function edit(Request $request, Recipe $recipe): Response
    {
        $user = $this->getUser();
        if ($recipe->getAuthor() !== $user && !$this->isGranted('ROLE_ADMIN') || !$user){
            $this->addFlash(
                'warning',
                $this->translator->trans('message.record_not_found')
            );

            return $this->redirectToRoute('recipe_index');
        }
        $form = $this->createForm(
            RecipeType::class,
            $recipe,
            [
                'method' => 'POST',
                'action' => $this->generateUrl('recipe_edit', ['id' => $recipe->getId()])]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recipeService->save($recipe);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->render('recipe/show.html.twig', ['recipe' => $recipe]);
        }

        return $this->render(
            'recipe/edit.html.twig',
            [
                'form' => $form->createView(),
                'recipe' => $recipe,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Recipe    $recipe    Recipe entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'recipe_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    //#[IsGranted('DELETE', subject: 'recipe')]
    public function delete(Request $request, Recipe $recipe): Response
    {
        $user = $this->getUser();
        if ($recipe->getAuthor() !== $user && !$this->isGranted('ROLE_ADMIN') || !$user){
            $this->addFlash(
                'warning',
                $this->translator->trans('message.record_not_found')
            );

            return $this->redirectToRoute('recipe_index');
        }
        $form = $this->createForm(
            FormType::class,
            $recipe,
            [
                'method' => 'POST',
                'action' => $this->generateUrl('recipe_delete', ['id' => $recipe->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recipeService->delete($recipe);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render(
            'recipe/delete.html.twig',
            [
                'form' => $form->createView(),
                'recipe' => $recipe,
            ]
        );
    }
}
