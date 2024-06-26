<?php
/**
 * AdminPasswordType.
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AdminPasswordType.
 */
class AdminPasswordType extends AbstractType
{
    /**
     * Builds form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('newPasswordAdmin', PasswordType::class, [
                'label' => 'label.password_new',
                'required' => true,
            ])
            ->add('confirmNewPasswordAdmin', PasswordType::class, [
                'label' => 'label.password_repeat',
                'required' => true,
            ])
            ->add('save', SubmitType::class, ['label' => 'action.edit_password']);
    }

    /**
     * Configures options.
     *
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
