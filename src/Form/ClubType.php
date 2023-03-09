<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Club;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;



class ClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank()]
            ])
            ->add('Prix')
            ->add('localisation')
            ->add('longitude')
            ->add('latitude')

            ->add('image', FileType::class, [
                'label' => 'image (image file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image ',
                    ])
                ],
            ])
            ->add('type_activite', ChoiceType::class, [
                'choices' => [
                    'Danse' => 'Danse',
                    'Boxe' => 'Boxe',
                    'Natation' => 'Natation',
                    'Musculation' => 'Musculation',
                    'Arts de défense' => 'Arts de défense',
                    'Salle de sport' => 'Salle de sport',
                ],
            ])
            ->add('telephone', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank()]
            ])
            ->add('Description')
            ->add("submit", SubmitType::class, [
                'label' => 'Enregistrer',
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Club::class,
        ]);
    }
}
