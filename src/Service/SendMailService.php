<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

/* Generate Mail Service  */
class SendMailService
{
  private $mailer;

  public function __construct(MailerInterface $mailer)
  {
    $this->mailer = $mailer;
  }

  public function send(
    string $from,
    string $to,
    string $subject,
    string $template,
    array $context
  ): void {
    //mail creation
    $email = (new TemplatedEmail())
      ->from($from)
      ->to($to)
      ->subject($subject)
      ->htmlTemplate("emails/$template.html.twig")
      ->context($context);

    //send mail
    try{
      $this->mailer->send($email);
    }catch(TransportExceptionInterface $transportException){

      throw $transportException;
    }
  }
}
