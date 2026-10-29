<?php

namespace App\Tests\Unit;

use App\Rating\RatingHandler;
use App\Model\Entity\VideoGame;
use App\Model\Entity\Review;
use PHPUnit\Framework\TestCase;

final class CalculateAverageRatingTest extends TestCase
{
    public function testCalculateAverageRatingWithReviews(): void
    {
        // Cas pour 2 reviews
        $videoGame = new VideoGame();
        $review1 = new Review();
        $review1->setRating(4);
        $review1->setVideoGame($videoGame);
        $videoGame->getReviews()->add($review1);

        $review2 = new Review();
        $review2->setRating(5);
        $review2->setVideoGame($videoGame);
        $videoGame->getReviews()->add($review2);

        $ratingHandler = new RatingHandler();
        $ratingHandler->calculateAverage($videoGame);

        $this->assertEquals(round(4.5), $videoGame->getAverageRating());
    }

    public function testCalculateAverageRatingWithoutReviews(): void
    {
        // Cas pour aucun review
        $videoGame = new VideoGame();
        $ratingHandler = new RatingHandler();
        $ratingHandler->calculateAverage($videoGame);

        $this->assertEquals(null, $videoGame->getAverageRating());
        
    }
}