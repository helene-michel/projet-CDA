<?php

namespace App\Form;

use App\Entity\Color;
use App\Entity\Products;
use App\Repository\ColorRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom')
        ->add('prix_ht')
        ->add('TVA')
        ->add('colors', EntityType::class, [
            'class' => Color::class,
            'choice_label' => 'nom',
            'multiple' => true,
            'expanded' => true,
            'choice_attr' => fn(Color $color) => [
                'data-code' => $color->getCode(),
                'data-nom' => $color->getNom(),
            ]
        ])
        ->add('description', TextareaType::class, [
            'label' => 'Description'
        ])
                ->add('image', FileType::class, [
            'label' => 'Image produit',
            'mapped' => false,
            'required' => false,
            'multiple' => true,
            'constraints' => [
                new Assert\All(
                    constraints: [
                        new Assert\File(
                            maxSize: '1024k',
                            extensions: ['png', 'jpeg', 'jpg'],
                            extensionsMessage: 'Veuillez télécharger une photo valide (PNG, JPEG ou JPG).',
                        ),
                    ],
                ),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
