<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label'=>'Adresse email : '
            ])
            ->add('password', PasswordType::class, [
                'label'=>'Mot de passe : '
            ])
            ->add('confirm_password', PasswordType::class, [
                'mapped' => false, 
                'label' => 'confirmation du mot de passe :'
            ])
            ->add('checkSeller', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
            ])
            
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
