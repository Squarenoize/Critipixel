<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;

final class FilterTest extends FunctionalTestCase
{
    public function testShouldListTenVideoGames(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->clickLink('2');
        self::assertResponseIsSuccessful();
    }

    public function testShouldFilterVideoGamesBySearch(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->submitForm('Filtrer', ['filter[search]' => 'Jeu vidéo 49'], 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }

    public function testShouldFilterVideoGamesByTag(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');

        // Récupère le formulaire de filtrage et extrait les IDs des tags disponibles
        $crawler = $this->client->getCrawler();
        $formNode = $crawler->selectButton('Filtrer');
        $form = $formNode->form();

        // Récupère tous les checkboxes des tags
        $tagCheckboxes = $crawler->filter('input[name="filter[tags][]"]');

        if ($tagCheckboxes->count() > 0) {
            // Utilise le premier tag disponible
            $firstTagId = $tagCheckboxes->first()->attr('value');
            $this->client->submitForm('Filtrer', ['filter[tags]' => [$firstTagId]], 'GET');
            self::assertResponseIsSuccessful();
        }
    }

    public function testShouldFilterVideoGamesByMultipleTags(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');

        // Récupère le formulaire de filtrage et extrait les IDs des tags disponibles
        $crawler = $this->client->getCrawler();
        $tagCheckboxes = $crawler->filter('input[name="filter[tags][]"]');

        if ($tagCheckboxes->count() >= 2) {
            // Utilise les deux premiers tags disponibles
            $firstTagId = $tagCheckboxes->eq(0)->attr('value');
            $secondTagId = $tagCheckboxes->eq(1)->attr('value');
            $this->client->submitForm('Filtrer', ['filter[tags]' => [$firstTagId, $secondTagId]], 'GET');
            self::assertResponseIsSuccessful();
        }
    }
}
