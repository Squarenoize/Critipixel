<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

final class CalculateAverageRatingTest extends TestCase
{
    /**
     * @dataProvider provideAverageRatingTestCases
     *
     * @param array<int, int> $ratings
     */
    public function testCalculateAverageRating(array $ratings, ?int $expectedAverage, string $description): void
    {
        $videoGame = new VideoGame();

        foreach ($ratings as $rating) {
            $review = new Review();
            $review->setRating($rating);
            $review->setVideoGame($videoGame);
            $videoGame->getReviews()->add($review);
        }

        $ratingHandler = new RatingHandler();
        $ratingHandler->calculateAverage($videoGame);

        $this->assertEquals($expectedAverage, $videoGame->getAverageRating(), $description);
    }

    /**
     * @return array<string, array{0: array<int, int>, 1: int|null, 2: string}>
     */
    public static function provideAverageRatingTestCases(): array
    {
        return [
            'two reviews with ratings 4 and 5' => [
                [4, 5],
                5, // Expected: round(4.5) = 5
                'Should calculate average of 4 and 5',
            ],
            'no reviews' => [
                [],
                null,
                'Should return null when no reviews',
            ],
        ];
    }
}
