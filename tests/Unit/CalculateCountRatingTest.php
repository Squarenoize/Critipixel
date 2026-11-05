<?php

namespace App\Tests\Unit;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

final class CalculateCountRatingTest extends TestCase
{
    /**
     * @dataProvider provideCountRatingTestCases
     *
     * @param array<int, int> $ratings
     * @param array<int, int> $expectedCounts
     */
    public function testCalculateCountRatingReviews(array $ratings, array $expectedCounts, string $description): void
    {
        $videoGame = new VideoGame();
        foreach ($ratings as $rating) {
            $review = new Review();
            $review->setRating($rating);
            $review->setVideoGame($videoGame);
            $videoGame->getReviews()->add($review);
        }

        $ratingHandler = new RatingHandler();
        $ratingHandler->countRatingsPerValue($videoGame);

        $ratings = $videoGame->getNumberOfRatingsPerValue();
        $this->assertEquals($expectedCounts[1], $ratings->getNumberOfOne());
        $this->assertEquals($expectedCounts[2], $ratings->getNumberOfTwo());
        $this->assertEquals($expectedCounts[3], $ratings->getNumberOfThree());
        $this->assertEquals($expectedCounts[4], $ratings->getNumberOfFour());
        $this->assertEquals($expectedCounts[5], $ratings->getNumberOfFive());
    }

    /**
     * @dataProvider provideCountRatingTestCases
     *
     * @param array<int, int> $ratings
     * @param array<int, int> $expectedCounts
     */
    public function testCalculateCountRatingWithoutReviews(array $ratings, array $expectedCounts, string $description): void
    {
        $videoGame = new VideoGame();
        foreach ($ratings as $rating) {
            $review = new Review();
            $review->setRating($rating);
            $review->setVideoGame($videoGame);
            $videoGame->getReviews()->add($review);
        }

        $ratingHandler = new RatingHandler();
        $ratingHandler->countRatingsPerValue($videoGame);

        $ratings = $videoGame->getNumberOfRatingsPerValue();
        $this->assertEquals($expectedCounts[1], $ratings->getNumberOfOne());
        $this->assertEquals($expectedCounts[2], $ratings->getNumberOfTwo());
        $this->assertEquals($expectedCounts[3], $ratings->getNumberOfThree());
        $this->assertEquals($expectedCounts[4], $ratings->getNumberOfFour());
        $this->assertEquals($expectedCounts[5], $ratings->getNumberOfFive());
    }

    /**
     * @return array<string, array{0: array<int, int>, 1: array<int, int>, 2: string}>
     */
    public static function provideCountRatingTestCases(): array
    {
        return [
            'two reviews with ratings 4 and 5' => [
                [4, 5],
                [1 => 0, 2 => 0, 3 => 0, 4 => 1, 5 => 1],
                'Should count ratings correctly for 4 and 5',
            ],
            'no reviews' => [
                [],
                [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'Should return zero counts when no reviews',
            ],
        ];
    }
}
