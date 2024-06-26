<?php
/**
 * RatingType.
 */

namespace App\Form\Type;

use App\Entity\Rating;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RatingType.
 */
class RatingType extends AbstractType
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
        $builder
            ->add('value', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                ],
                'expanded' => true,
                'label' => 'label.rate',
            ])
            ->add('save', SubmitType::class, ['label' => 'action.rating_add']);
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
            'data_class' => Rating::class,
        ]);
    }
}
