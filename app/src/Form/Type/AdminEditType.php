<?php
/**
 * AdminEditType.
 */

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AdminEditType.
 */
class AdminEditType extends AbstractType
{
    /**
     * Build the form.
     *
     * @param FormBuilderInterface $builder Form Builder Interface
     * @param array                $options options
     *
     * @return void void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'label.email'])
            ->add('save', SubmitType::class, ['label' => 'action.edit']);
    }

    /**
     * Configures the options.
     *
     * @param OptionsResolver $resolver Options Resolver
     *
     * @return void void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
