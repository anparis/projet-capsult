<?php

namespace App\Form;

use App\Entity\Bloc;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BlocType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('title', TextType::class)
      ->add('description', TextareaType::class, [
        'required'   => false
      ])
      ->add('submit', SubmitType::class, [
        'attr' => ['class' => 'btn']
      ]);
    $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
      $bloc = $event->getData();
      $form = $event->getForm();
      // checks if the Bloc content is null
      // If no data is passed to the form, the data is "null".
      if ($bloc->getContent()) {

        $form->add('content', TextareaType::class, [
          'label' => false
        ]);
      }
    });
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Bloc::class,
    ]);
  }
}
