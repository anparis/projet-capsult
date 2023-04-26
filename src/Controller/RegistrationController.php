<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Service\SendMailService;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;


class RegistrationController extends AbstractController
{
  private EmailVerifier $emailVerifier;

  public function __construct(EmailVerifier $emailVerifier)
  {
    $this->emailVerifier = $emailVerifier;
  }

  #[Route('/register', name: 'app_register')]
  public function register(
    Request $request,
    UserPasswordHasherInterface $userPasswordHasher,
    UserAuthenticatorInterface $userAuthenticator,
    UserAuthenticator $authenticator,
    EntityManagerInterface $entityManager,
    SluggerInterface $sluger,
    SendMailService $mail
  ): Response {
    $user = new User();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $user->setSlug($sluger->slug($form->get('username')->getData())->lower());
      // encode the plain password
      $user->setPassword(
        $userPasswordHasher->hashPassword(
          $user,
          $form->get('plainPassword')->getData()
        )
      );
      $entityManager->persist($user);
      $entityManager->flush();

      $this->addFlash('success', 'Bienvenue sur Capsult, veuillez vérifier vos e-mails pour activer votre compte.');

      //send email
      $this->emailVerifier->sendEmailConfirmation(
        'app_verify_email',
        $user,
        (new TemplatedEmail())
          ->from(new Address('admin@capsult.com', 'Admin'))
          ->to($user->getEmail())
          ->subject('Activation de votre compte sur Capsult')
          ->htmlTemplate('email/register.html.twig'),
      );

      return $userAuthenticator->authenticateUser(
        $user,
        $authenticator,
        $request
      );
    } 

    return $this->render('registration/register.html.twig', [
      'registrationForm' => $form->createView(),
    ]);
  }

  #[Route('/verify/email', name: 'app_verify_email')]
  public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    // validate email confirmation link, sets User::isVerified=true and persists
    try {
      $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
    } catch (VerifyEmailExceptionInterface $exception) {
      $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

      return $this->redirectToRoute('app_register');
    }

    // @TODO Change the redirect on success and handle or remove the flash message in your templates
    $this->addFlash('success', 'Votre adresse a été validé');

    return $this->redirectToRoute('profile_index', ['slug' => $this->getUser()->getSlug()]);
  }

  #[Route('/resend/email', name: 'resend_verif')]
  public function resendEmail()
  {
    $user = $this->getUser();

    if (!$user) {
      $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
      return $this->redirectToRoute('app_login');
    }

    if ($user->getIsVerified()) {
      $this->addFlash('warning', 'Cet utilisateur est déjà vérifié');
      return $this->redirectToRoute('profile_index', ['slug' => $user->getSlug()]);
    }

    //send email
    $this->emailVerifier->sendEmailConfirmation(
      'app_verify_email',
      $user,
      (new TemplatedEmail())
        ->from(new Address('admin@capsult.com', 'Admin'))
        ->to($user->getEmail())
        ->subject('Activation de votre compte sur Capsult')
        ->htmlTemplate('email/register.html.twig'),
    );
    $this->addFlash('success', 'Email de vérification envoyé');
    return $this->redirectToRoute('app_login');
  }
}
