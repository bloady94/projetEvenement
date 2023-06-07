<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $utilisateur = $options['utilisateur'];
        $builder
            ->add('nom', TextType::class)
            ->add('dateHeureDebut', DateTimeType::class, [
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('duree', TextType::class)
            ->add('dateLimiteInscription', DateType::class, [
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('nbInscriptionsMax', TextType::class)
            ->add('infosSortie',TextAreaType::class)
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez un lieu',
                'query_builder' => function(LieuRepository $lieuRepository){
                $qb = $lieuRepository->createQueryBuilder('l');
                $qb->addOrderBy('l.nom', 'ASC');
                return $qb;
                }
            ])

            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'data' => $utilisateur->getCampus(),
                'query_builder' => function(CampusRepository $campusRepository){
                $qb = $campusRepository->createQueryBuilder('c');
                $qb->addOrderBy('c.nom', 'ASC');
                return $qb;
                }
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);

        $resolver->setRequired('utilisateur');
    }
}
