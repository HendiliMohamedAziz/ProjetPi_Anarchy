<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use App\Entity\Club;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Participation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\IsTrue;


class ParticipationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_club', EntityType::class, array(
                'class' => Club::class,
                'choice_label' => 'nom',
            ))

            ->add('DateDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(), // Remplir automatiquement le champ avec la date actuelle
                'empty_data' => new \DateTime(), // Utiliser la date actuelle si le champ est laissé vide
            ])
            ->add('DateFin', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => (new \DateTime())->add(new \DateInterval('P1M')), // Remplir automatiquement le champ avec la date actuelle + 1 mois
                'empty_data' => (new \DateTime())->add(new \DateInterval('P1M')), // Utiliser la date actuelle + 1 mois si le champ est laissé vide
            ])



            /*
            ->add('accept_terms', CheckboxType::class, [
                'label' => 'J\'ai lu et j\'accepte les termes et conditions',
                'mapped' => false,
                'constraints' => new IsTrue([
                    'message' => 'Vous devez accepter les termes et conditions pour continuer.',
                ])])*/
            ->add("submit", SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participation::class,
            'id_club' => null
        ]);
    }
}
