<?php
/**
 * Recipe controller.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Rating;
use App\Entity\Recipe;
use App\Entity\User;
use App\Dto\RecipeListInputFiltersDto;
use App\Form\Type\CommentType;
use App\Form\Type\RatingType;
use App\Repository\CommentRepository;
use App\Repository\RatingRepository;
use App\Resolver\RecipeListInputFiltersDtoResolver;
use App\Form\Type\RecipeType;
use App\Service\RecipeService;
use App\Service\RecipeServiceInterface;
use App\Service\TagServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
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
    public function __construct(private readonly RecipeServiceInterface $recipeService, private readonly TagServiceInterface $tagService, private readonly TranslatorInterface $translator, private readonly Security $security, private readonly ManagerRegistry $doctrine)
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
        methods: 'GET|POST'
    )]
    public function show(Recipe $recipe, Request $request, EntityManagerInterface $em): Response
    {
        $comments = $recipe->getComments();

        $user = $this->security->getUser();
        $commentForm = null;

        if ($user instanceof User) {
            $comment = new Comment();
            $comment->setRecipe($recipe);
            $comment->setAuthor($user);

            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->recipeService->saveComment($comment);
                $this->addFlash('success', $this->translator->trans('message.created_successfully'));

                return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
            }

            $commentForm = $form->createView();
        }

        return $this->render('recipe/show.html.twig', [
            'id' => $recipe->getId(),
            'recipe' => $recipe,
            'comments' => $comments,
            'commentForm' => $commentForm,
        ]);
    }
    #[Route(
        '/{recipe_id}/comment/{id}/delete',
        name: 'comment_delete',
        requirements: ['id' => '[1-9]\d*', 'recipe_id' => '[1-9]\d*'],
        methods: 'POST')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteComment(Request $request,Recipe $recipe, Comment $comment, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user || ($comment->getAuthor() !== $user && !$this->isGranted('ROLE_ADMIN'))) {
            return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
        }
        $recipeId = $comment->getRecipe()->getId();
        $form = $this->createForm(FormType::class, $comment, [
            'method' => 'POST',
            'action' => $this->generateUrl('comment_delete', ['recipe_id' => $recipeId, 'id' => $comment->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recipeService->deleteComment($comment);
            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));
            return $this->redirectToRoute('recipe_show', ['id' => $recipeId]);
        }

        return $this->render('comment/delete.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment,
        ]);
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
            return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
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
    #[Route(
        '/{id}/rate',
        name: 'recipe_rate',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'POST']
    )]
    public function rateRecipe(Request $request, Recipe $recipe, RatingRepository $ratingRepository): Response
    {
        $user = $this->getUser();
        $existingRating = $ratingRepository->findOneBy([
            'recipe' => $recipe,
            'user' => $user,
        ]);
        if ($existingRating) {
            $rating = $existingRating;
        } else {
            $rating = new Rating();
            $rating->setRecipe($recipe);
            $rating->setUser($user);
        }
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $this->recipeService->saveRating($rating);

            $recipe->calculateAverageRating();
            $entityManager->persist($recipe);
            $entityManager->flush();

            $this->addFlash(
                'success',
                $this->translator->trans('message.rated_successfully')
            );
            return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
        }

        return $this->render('recipe/rate.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
        ]);
    }
}
