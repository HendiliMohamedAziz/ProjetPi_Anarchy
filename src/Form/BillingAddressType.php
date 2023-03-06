<?php

namespace App\Form;

use App\Entity\BillingAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class BillingAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',null, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le champ nom ne doit pas être vide'
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le nom doit comporter au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas comporter plus de {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('Email',null, [
                'label' => 'Email',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le champ Email ne doit pas être vide'
                    ])
                ]
            ])
            ->add('Phone',IntegerType::class, [
                'label' => 'Phone',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champs Quantité est obligatoire'
                    ] ),
                    new Positive([
                        'message' => 'Quantité doit etre positive'
                    ] ),
                    
                    new Assert\Length([
                        'min' => 8,
                        'max' => 8,
                        'minMessage' => 'Le nombre doit comporter au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nombre ne peut pas comporter plus de {{ limit }} caractères'
                    ])
                ]])
            ->add('Address',null, [
                'label' => 'Address',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le champ Address ne doit pas être vide'
                    ])
                ]
            ])
            ->add('Description')
            ->add("submit",SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BillingAddress::class,
        ]);
    }
}
