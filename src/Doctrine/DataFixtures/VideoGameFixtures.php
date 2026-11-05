<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Review;
use App\Model\Entity\Tag;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $faker,
        private readonly CalculateAverageRating $calculateAverageRating,
        private readonly CountRatingsPerValue $countRatingsPerValue,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $tags = $manager->getRepository(Tag::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        /** @var array<int, VideoGame> $videoGames */
        $videoGames = array_map(
            fn (int $index): VideoGame => (new VideoGame())
                ->setTitle(sprintf('Jeu vidéo %d', $index))
                ->setDescription($this->faker->paragraphs(10, true))
                ->setReleaseDate(new \DateTimeImmutable())
                ->setTest($this->faker->paragraphs(6, true))
                ->setRating(($index % 5) + 1)
                ->setImageName(sprintf('video_game_%d.png', $index))
                ->setImageSize(2_098_872),
            range(0, 49)
        );

        // Ajouter des tags aux vidéos
        foreach ($videoGames as $index => $videoGame) {
            // Assure des tags prédictibles pour les premiers jeux vidéo (pour les tests)
            if (0 === $index) {
                // Le premier jeu a le tag "Action"
                $videoGame->getTags()->add($tags[0]);
            } elseif (1 === $index) {
                // Le deuxième jeu a les tags "Action" et "Sport"
                $videoGame->getTags()->add($tags[0]);
                $videoGame->getTags()->add($tags[1]);
            } elseif (2 === $index) {
                // Le troisième jeu a le tag "Sport"
                $videoGame->getTags()->add($tags[1]);
            } else {
                // Pour les autres jeux, utilisez le random comme avant
                $numberOfTags = rand(1, 2);

                $tagIndices = array_rand(
                    $tags,
                    min($numberOfTags, count($tags))
                );

                // S'assure que $tagIndices est toujours un tableau
                if (!is_array($tagIndices)) {
                    $tagIndices = [$tagIndices];
                }

                foreach ($tagIndices as $tagIndex) {
                    $videoGame->getTags()->add($tags[$tagIndex]);
                }
            }
        }

        foreach ($videoGames as $videoGame) {
            $manager->persist($videoGame);
        }

        $manager->flush();

        // Ajouter des reviews aux vidéos

        foreach ($videoGames as $videoGame) {
            $randomUsersChoice = array_rand($users, rand(1, count($users)));

            // S'assure que $randomUsersChoice est toujours un tableau
            if (!is_array($randomUsersChoice)) {
                $randomUsersChoice = [$randomUsersChoice];
            }
            foreach ($randomUsersChoice as $index) {
                $review = new Review();
                $review->setUser($users[$index]);
                $review->setVideoGame($videoGame);
                $review->setRating(rand(1, 5));
                $review->setComment($this->faker->paragraphs(1, true));

                $videoGame->getReviews()->add($review);
                $manager->persist($review);
            }

            $this->calculateAverageRating->calculateAverage($videoGame);
            $this->countRatingsPerValue->countRatingsPerValue($videoGame);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class, TagFixtures::class];
    }
}
