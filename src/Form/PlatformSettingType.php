<?php

namespace App\Form;

use App\Entity\PlatformSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlatformSettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('commissionRate', NumberType::class, [
            'label' => 'Taux de commission (%)',
            'scale' => 2,
            'html5' => true,
            'attr' => ['min' => 0, 'max' => 100, 'step' => '0.01'],
            'help' => 'S\'applique aux nouvelles commandes uniquement.',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PlatformSetting::class,
        ]);
    }
}
