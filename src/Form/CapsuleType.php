<?php

namespace App\Form;

use App\Entity\Capsule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CapsuleType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('title', TextType::class,[
        'label' => false
      ])
      ->add('description', TextareaType::class,[
        'required'   => false
      ])
      ->add('status', ChoiceType::class, [
        'choices'  => [
          'Statut de la capsule' => [
            'statut > scellée' => 'sealed',
            'statut > ouverte' => 'open',
          ],
        ],
        'label' => 'Statut'
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Capsule::class,
    ]);
  }
}
