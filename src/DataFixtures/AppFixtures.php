<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Editor;
use App\Entity\User;
use App\Entity\VideoGame;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void   
    {
        // Création de 2 Users
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $hashedPwd = $this->passwordHasher->hashPassword($admin, 'adminpass');
        $admin->setPassword($hashedPwd);
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@example.com');
        $hashedPwd = $this->passwordHasher->hashPassword($user, 'userpass');
        $user->setPassword($hashedPwd);
        $manager->persist($user);

        // Quelques catégories
        $cat1 = (new Category())->setName('RPG');
        $cat2 = (new Category())->setName('FPS');
        $manager->persist($cat1);
        $manager->persist($cat2);

        // Quelques éditeurs
        $editor1 = (new Editor())->setName('Nintendo')->setCountry('Japon');
        $editor2 = (new Editor())->setName('Ubisoft')->setCountry('France');
        $manager->persist($editor1);
        $manager->persist($editor2);

        // Un jeu d’essai
        $game = (new VideoGame())
            ->setTitle('Zelda: Breath of the Wild')
            ->setReleaseDate(new \DateTime('2017-03-03'))
            ->setDescription('Un RPG en monde ouvert.')
            ->setCategory($cat1)
            ->setEditor($editor1);
        $manager->persist($game);

        $manager->flush();
    }
}
