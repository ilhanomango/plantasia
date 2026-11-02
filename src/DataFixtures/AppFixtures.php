<?php

namespace App\DataFixtures;

use App\Entity\Plantes;
use App\Entity\User;
use App\Entity\UserPlante;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@plantasia.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // Création d'un utilisateur classique
        $user = new User();
        $user->setEmail('user@plantasia.fr');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->hasher->hashPassword($user, 'user123'));
        $manager->persist($user);

        // Création de quelques plantes du catalogue
        $plantesData = [
            [
                'nom' => 'Monstera Deliciosa',
                'image' => 'https://images.unsplash.com/photo-1614594975525-e45190c55d0b?w=500',
                'description' => 'Plante tropicale aux grandes feuilles perforées.'
            ],
            [
                'nom' => 'Ficus Lyrata',
                'image' => 'https://images.unsplash.com/photo-1545241047-6083a3684587?w=500',
                'description' => 'Aussi appelé figuier lyre, adore la lumière indirecte.'
            ],
            [
                'nom' => 'Pothos Doré',
                'image' => 'https://images.unsplash.com/photo-1620127682919-26a428637738?w=500',
                'description' => 'Plante tombante très facile d\'entretien.'
            ]
        ];

        $plantesEntities = [];
        foreach ($plantesData as $data) {
            $plante = new Plantes();
            $plante->setNom($data['nom']);
            $plante->setImage($data['image']);
            $plante->setDescription($data['description']);
            $manager->persist($plante);
            $plantesEntities[] = $plante;
        }

        // Ajouter une plante à la collection du user
        $userPlante = new UserPlante();
        $userPlante->setUser($user);
        $userPlante->setPlante($plantesEntities[0]); // Monstera
        $userPlante->setSurnom('Monstie');
        $userPlante->setDernierArrosage(new \DateTimeImmutable('-2 days'));
        $manager->persist($userPlante);

        $manager->flush();
    }
}
