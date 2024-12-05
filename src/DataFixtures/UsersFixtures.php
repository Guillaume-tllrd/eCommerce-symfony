<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class UsersFixtures extends Fixture
{
    // on a besoin du hasher de mdp et du slugger
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
    ) {}

    public function load(ObjectManager $manager): void
    {

        $admin = new Users();
        $admin->setEmail('admin@demo.fr');
        $admin->setLastname('Gambier');
        $admin->setFirstname('Benoit');
        $admin->setAddress("12 rue du code");
        $admin->setZipcode("65000");
        $admin->setCity("Nantes");
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, "azerty"));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        //pour créer des fausse données à la francaise on utilise le faker que l'on a installé avec la commande : composer require fakerphp/faker
        $faker = Faker\Factory::create('fr_FR');

        for ($usr = 1; $usr < +5; $usr++) {
            $user = new Users();
            $user->setEmail($faker->email);
            $user->setLastname($faker->lastname);
            $user->setFirstname($faker->firstname);
            $user->setAddress($faker->streetAddress);
            $user->setZipcode(str_replace(' ', '', $faker->postcode));
            $user->setCity($faker->city);
            $user->setPassword($this->passwordEncoder->hashPassword($user, "azerty"));
            // pour le postcode on remplace les espace par rien sinon bug
        }
        $manager->flush();
    }
}
