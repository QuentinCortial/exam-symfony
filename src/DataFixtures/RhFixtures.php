<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RhFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $rh=new User();
        $rh->setEmail('rh@humanbooster.com');
        $rh->setPassword('$2y$10$UxW6HvwwggAW4MF1NlssNOBexhOKP6cHFJkxjvQf0LXLZywC3hBkK');
        $rh->setRoles(['ROLE_RH']);
        $rh->setNom('rh');
        $rh->setPrenom('rh');
        $rh->setPhoto('rh.png');
        $rh->setSecteur('RH');
        $rh->setContrat('CDI');
        $manager->persist($rh);
        $manager->flush();
    }
}
