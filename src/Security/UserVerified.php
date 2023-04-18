<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserVerified implements UserCheckerInterface
{
  public function checkPreAuth(UserInterface $user): void
  {
    if (!$user instanceof User) {
      return;
    }
  }

  public function checkPostAuth(UserInterface $user): void
  {
    if (!$user instanceof User) {
      return;
    }

    if (!$user->getIsVerified()) {
      // the message passed to this exception is meant to be displayed to the user
      throw new CustomUserMessageAccountStatusException("Votre compte n'est pas vérifié, 
          vous devez confirmer la création de votre compte par mail.");
    }
  }
}
