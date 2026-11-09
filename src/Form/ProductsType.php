<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Color;
use App\Entity\Products;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['placeholder' => 'Ex. : Robe portefeuille en lin', 'maxlength' => 255],
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Category::class,
                'choice_label' => 'nom',
                'group_by' => fn (Category $category) => $category->getCategoryGroup()?->getNom() ?? 'Autres',
                'query_builder' => fn (EntityRepository $repository) => $repository->createQueryBuilder('c')->orderBy('c.nom', 'ASC'),
                'placeholder' => 'Choisir une catégorie',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'help' => 'Matière, coupe, conseils d\'entretien… 20 caractères minimum.',
                'attr' => ['rows' => 6],
            ])
            ->add('prix_ht', MoneyType::class, [
                'label' => 'Prix HT',
                'currency' => 'EUR',
                'html5' => true,
                'attr' => ['min' => '0.01', 'step' => '0.01', 'placeholder' => '0,00'],
            ])
            ->add('TVA', ChoiceType::class, [
                'label' => 'TVA',
                // Mêmes valeurs que Products::TVA_RATES (validées côté entité)
                'choices' => [
                    '20 % (taux normal, vêtements et accessoires)' => 20.0,
                    '10 %' => 10.0,
                    '5,5 %' => 5.5,
                    '2,1 %' => 2.1,
                ],
                'placeholder' => false,
                // Taux lisible côté JS pour l'aperçu du prix TTC (contrôleur Stimulus "product-form")
                'choice_attr' => fn (float $rate) => ['data-rate' => $rate],
            ])
            ->add('colors', EntityType::class, [
                'label' => 'Coloris disponibles',
                'class' => Color::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'choice_attr' => fn (Color $color) => [
                    'data-code' => $color->getCode(),
                    'data-nom' => $color->getNom(),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Photos du produit',
                'help' => 'JPEG ou PNG, 1 Mo maximum par photo. Vous pouvez en sélectionner plusieurs.',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'attr' => ['accept' => 'image/jpeg,image/png'],
                'constraints' => [
                    new Assert\All(
                        constraints: [
                            new Assert\Image(
                                maxSize: '1024k',
                                mimeTypes: ['image/jpeg', 'image/png'],
                                mimeTypesMessage: 'Veuillez télécharger une photo valide (PNG ou JPEG).',
                                maxSizeMessage: 'La photo est trop lourde ({{ size }} {{ suffix }}), le maximum est {{ limit }} {{ suffix }}.',
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
