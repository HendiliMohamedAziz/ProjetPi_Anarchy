<?php

namespace App\Form;

use App\Entity\Seance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SeanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre',TextType::class)
            ->add('nbr_grp',IntegerType::class, [
                'label' => 'Nombre group',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le champ nombre ne doit pas être vide'
                    ])]])
            ->add('nbr_seance',IntegerType::class, [
                'label' => 'Nombre séance',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le champ séance ne doit pas être vide'
                    ])]])
            ->add('description',null, [
                'label' => 'Description',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le champ description ne doit pas être vide'
                    ])]])
            ->add('color',ColorType::class)
           
            ->add("submit", SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seance::class,
        ]);
    }
}
