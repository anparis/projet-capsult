<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Capsule;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class CapsuleFixtures extends Fixture implements DependentFixtureInterface
{
  public function __construct(private SluggerInterface $sluger){}

  public function load(ObjectManager $manager): void
  {
    
    $user = $this->getReference('myself');

    $this->createCapsule('Berserk Fanclub', 0, $user, $manager);
    $this->createCapsule('September feelings', 0, $user, $manager);
    $this->createCapsule('Océan bleu', 0, $user, $manager);

    $manager->flush();
  }

  public function createCapsule(string $titre, bool $open, User $user = null, ObjectManager $manager)
  {
    $capsule = new Capsule();
    $capsule->setTitre($titre);
    $capsule->setSlug($this->sluger->slug($capsule->getTitre()));
    $capsule->setOpen($open);
    if ($capsule->isOpen()) {
      $capsule->setStatus('sealed');
    } else {
      $capsule->setStatus('open');
    }
    $capsule->setUser($user);

    $manager->persist($capsule);
  }

  public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
