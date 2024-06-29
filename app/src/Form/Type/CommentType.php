<?php
/**
 * CommentType.
 */

namespace App\Form\Type;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CommentType.
 */
class CommentType extends AbstractType
{
    /**
     * Builds form.
     *
     * @param FormBuilderInterface $builder Form Builder Interface
     * @param array                $options Options
     *
     * @return void Void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('content', TextareaType::class, [
            'label' => 'label.comment',
            'required' => true,
        ]);
    }

    /**
     * Configures options.
     *
     * @param OptionsResolver $resolver Options Resolver
     *
     * @return void Void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
