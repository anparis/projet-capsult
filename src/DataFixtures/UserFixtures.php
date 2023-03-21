<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{
  //Using SlugerInterface to construct my slugs
  public function __construct(private SluggerInterface $sluger, private UserPasswordHasherInterface $hasher)
  {
  }

  public function load(ObjectManager $manager): void
  {
    $me = new User();
    $me->setEmail('kentaro@test.com');
    $me->setUsername('Kentaro Myura');
    $me->setPassword($this->hasher->hashPassword($me, 'kentaro'));
    $me->setSlug($this->sluger->slug($me->getUsername()))->lower();

    $manager->persist($me);
    
    $faker = Factory::create('fr_FR');
    
    for ($u = 1; $u <= 4; $u++) {
      $user = new User();
      $user->setEmail($faker->email);
      $user->setUsername($faker->name);
      $user->setPassword($this->hasher->hashPassword($user, 'secret'));
      $user->setSlug($this->sluger->slug($user->getUsername()))->lower();
      
      $manager->persist($user);
    }
    
    $manager->flush();
    $this->addReference('myself', $me);
  }
}
