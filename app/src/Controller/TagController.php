<?php
/**
 * tag controller.
 */

namespace App\Controller;

use App\Entity\Tag;
use App\Form\Type\TagType;
use App\Service\TagService;
use App\Service\TagServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TagController.
 */
#[Route('/tag')]
class TagController extends AbstractController
{
    /**
     * @param TagServiceInterface $tagService
     * @param TranslatorInterface $translator
     */
    public function __construct(private readonly TagServiceInterface $tagService, private readonly TranslatorInterface $translator)
    {
    }


    #[Route('/{id}/edit', name: 'tag_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    public function edit(Request $request, Tag $tag): Response
    {
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN') || !$user){
            return $this->redirectToRoute('tag_index');
        }
        $form = $this->createForm(
            TagType::class,
            $tag,
            [
                'method' => 'POST',
                'action' => $this->generateUrl('tag_edit', ['id' => $tag->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->save($tag);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('tag_index');
        }

        return $this->render(
            'tag/edit.html.twig',
            [
                'form' => $form->createView(),
                'tag' => $tag,
            ]
        );
    }

    /**
     * Index action.
     *
     * @return Response HTTP response
     */
    #[Route(name: 'tag_index', methods: 'GET')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->tagService->getPaginatedList($page);

        return $this->render('tag/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Tag $tag Tag
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'tag_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Tag $tag): Response
    {
        return $this->render('tag/show.html.twig', ['tag' => $tag]);
    }

    // ...
    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create',
        name: 'tag_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user){
            return $this->redirectToRoute('tag_index');
        }
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->save($tag);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('tag_index');
        }

        return $this->render(
            'tag/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @param Request $request
     * @param Tag $tag
     * @return Response
     */
    #[Route('/{id}/delete', name: 'tag_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    public function delete(Request $request, Tag $tag): Response
    {
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN') || !$user){
            return $this->redirectToRoute('tag_index');
        }
        if(!$this->tagService->canBeDeleted($tag)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.tag_contains_recipes')
            );

            return $this->redirectToRoute('tag_index');
        }

        $form = $this->createForm(
            FormType::class,
            $tag,
            [
                'method' => 'POST',
                'action' => $this->generateUrl('tag_delete', ['id' => $tag->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->delete($tag);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('tag_index');
        }

        return $this->render(
            'tag/delete.html.twig',
            [
                'form' => $form->createView(),
                'tag' => $tag,
            ]
        );
    }
}