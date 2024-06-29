<?php
/**
 * Tag type.
 */

namespace App\Form\Type;

use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TagType.
 */
class TagType extends AbstractType
{
    /**
     * Builds form.
     *
     * @param FormBuilderInterface $builder Form Builder
     * @param array                $options Options
     *
     * @return void Void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'title',
            TextType::class,
            [
                'label' => 'label.title',
                'required' => true,
                'attr' => ['max_length' => 64],
            ]
        );
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
        $resolver->setDefaults(['data_class' => Tag::class]);
    }

    /**
     * getBlockPrefix.
     *
     * @return string tag
     */
    public function getBlockPrefix(): string
    {
        return 'tag';
    }
}
