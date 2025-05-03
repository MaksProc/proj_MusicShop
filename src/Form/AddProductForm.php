<?php

namespace App\Form;

use App\Entity\Product;
use App\Enum\ProductType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Buy' => ProductType::BUY,
                    'Rent' => ProductType::RENT,
                    'Both' => ProductType::BOTH
                ]
            ])
            ->add('base_price')
            ->add('base_rent_per_day')
            ->add('base_rent_per_week')
            ->add('stock')
            ->add('availability')
            ->add('category')
            ->add('imageFile', FileType::class, [
                'label' => 'Product Image',
                'mapped' => false, // nie zmapowany automatycznie do pola Product
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Załaduj plik w rozszerzeniu jpeg, png lub webp',
                    ])
                ],
                'attr' => ['accept' => 'image/*'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
