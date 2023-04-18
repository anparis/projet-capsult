<?php

namespace App\Controller;

use App\Service\SendMailService;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailerController extends AbstractController
{
    // #[Route('/email', name: 'email')]
    // public function sendEmail(MailerInterface $mailer, Request $request,SendMailService $mail): Response
    // {
    //     $post = $request->request;
    //     $collaboratorEmail = $post->get('email');
    //     // $email = (new Email())
    //     //     ->from('noreply@example.org')
    //     //     ->to($collaboratorEmail)
    //     //     ->subject('Time for Symfony Mailer!')
    //     //     ->text('Sending emails is fun again!')
    //     //     ->html('<p>See Twig integration for better HTML integration!</p>');
            
    //     // $mailer->send($email);
    //     $mail->send(
    //       $this->getUser()->getEmail(),
    //       $collaboratorEmail,
    //       'Activation de votre compte sur le capsult',
    //       'connection_email',
    //       compact('user')
    //     );

    //     return new Response('success');
    // }

  // private EmailVerifier $emailVerifier;

  // public function __construct(EmailVerifier $emailVerifier)
  // {
  //   $this->emailVerifier = $emailVerifier;
  // }

  // #[Route('/collaboration', name: 'app_collaboration')]
  // public function collaboration(
  //   Request $request, 
  //   UserPasswordHasherInterface $userPasswordHasher, 
  //   UserAuthenticatorInterface $userAuthenticator, 
  //   UserAuthenticator $authenticator, 
  //   EntityManagerInterface $entityManager, 
  //   SluggerInterface $sluger, 
  //   SendMailService $mail): Response
  // {
  //   $user = new User();

  //   if ($form->isSubmitted() && $form->isValid()) {
  //     // encode the plain password
  //     $user->setSlug($sluger->slug($form->get('username')->getData())->lower());
  //     $user->setPassword(
  //       $userPasswordHasher->hashPassword(
  //         $user,
  //         $form->get('plainPassword')->getData()
  //       )
  //     );

  //     $entityManager->persist($user);
  //     $entityManager->flush();

  //     $this->addFlash('success', 'Bienvenue sur Capsult, veuillez vérifier vos e-mails pour activer votre compte.');

  //     //send email
  //     $this->emailVerifier->sendEmailConfirmation(
  //       'app_verify_email',
  //       $user,
  //       (new TemplatedEmail())
  //         ->from(new Address('admin@capsult.com', 'Admin'))
  //         ->to($user->getEmail())
  //         ->subject('Activation de votre compte sur Capsult')
  //         ->htmlTemplate('email/register.html.twig'),
  //     );

  //     return $userAuthenticator->authenticateUser(
  //       $user,
  //       $authenticator,
  //       $request
  //     );
  //   }

  //   return $this->render('registration/register.html.twig', [
  //     'registrationForm' => $form->createView(),
  //   ]);
  // }
  
  // #[Route('/verify/email', name: 'app_verify_email')]
  // public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
  // {
  //   $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

  //   // validate email confirmation link, sets User::isVerified=true and persists
  //   try {
  //     $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
  //   } catch (VerifyEmailExceptionInterface $exception) {
  //     $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

  //     return $this->redirectToRoute('app_register');
  //   }

  //   // @TODO Change the redirect on success and handle or remove the flash message in your templates
  //   $this->addFlash('success', 'Votre adresse a été validé');

  //   return $this->redirectToRoute('profile_index', ['slug' => $this->getUser()->getSlug()]);
  // }
}