<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Rental;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class MakeRentalForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startTimestamp', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'input' => 'datetime', // Tworzy obiekty DateTime, chociaż potrzebuję tylko Date
            ])
            ->add('endTimestamp', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'input' => 'datetime',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rental::class,
        ]);
    }
}
