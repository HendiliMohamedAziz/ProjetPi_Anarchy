<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
class ArticleType extends AbstractType
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
                ]),
             
                new Assert\ExpressionLanguageSyntax([
                    'message' => 'Votre champ contient des caractére spec.'
                ]),
            ]
        ])
            ->add('image', FileType::class, array('label' => 'Brochure (image)','data_class' => null)
            ,null, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le champ nom ne doit pas être vide'
                    ]),
                ]
            ])
           
           
           
            ->add('description',null, [
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
                    ]),
        
                ]
            ])




            ->add('prix',IntegerType::class, [
                'label' => 'Quantité',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champs Quantité est obligatoire'
                    ] ),
                    new Positive([
                        'message' => 'Quantité doit etre positive'
                    ] )
                ]])

            ->add("submit",SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
