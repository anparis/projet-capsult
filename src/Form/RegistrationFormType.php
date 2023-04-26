<?php

namespace App\Form;

use Error;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('username', TextType::class, [
        'label' => 'Nom d\'utilisateur'
      ])
      ->add('email', EmailType::class, [
        'label' => 'E-mail',
        'constraints' => [
          new NotBlank([
            'message' => 'Veuillez saisir un email s\'il vous plaît.',
          ]),
          new Email([
            'message' => 'L\'email "{{ value }}" n\'est pas valide.',
          ]),
        ],
      ])
      ->add('RGPDConsent', CheckboxType::class, [
        'mapped' => false,
        'constraints' => [
          new IsTrue([
            'message' => 'Vous devez accepter les termes pour vous enregistrer.',
          ]),
        ],
      ])
      ->add('plainPassword', RepeatedType::class, [
        // instead of being set onto the object directly,
        // this is read and encoded in the controller
        'mapped' => false,
        'type' => PasswordType::class,
        'first_options' => ['label' => 'Mot de passe'],
        'second_options' => ['label' => 'Confirmer le mot de passe'],
        'constraints' => [
          new NotBlank([
            'message' => 'Veuillez entrer un mot de passe s\'il vous plaît.',
          ]),
          new Length([
            'min' => 12,
            'minMessage' => 'Le mot de passe doit faire au minimum {{ limit }} charactères',
            // max length allowed by Symfony for security reasons
            'max' => 4096,
          ]),
          new Regex([
            //regex to force user choosing a password with high entropy
            'pattern' => "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/",
            'message' => "Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial"
          ])
        ],
        'label' => 'Mot de passe'
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}
