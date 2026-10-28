<?php
namespace App\Doctrine\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Tag;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Game genres
        $tags = ['Action', 'Sport', 'Adventure', 'RPG', 'Simulation', 'Strategy', 'Puzzle', 'Shooter', 'Fighting', 'Racing'];

        foreach ($tags as $tagName) {
            $tag = new Tag();
            $tag->setName($tagName);
            $manager->persist($tag);
        }

        $manager->flush();
    }
}