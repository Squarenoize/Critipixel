<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ShowTest extends FunctionalTestCase
{
    public function testShouldShowVideoGame(): void
    {
        $this->get('/jeu-video-0');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Jeu vidéo 0');
    }

    public function testUserPostedReview(): void
    {
        //Créer le client
        $this->login();
        
        //Accéder à la page du jeu vidéo avec le formulaire
        $crawler = $this->get('/jeu-video-10');
        self::assertResponseIsSuccessful();
        
        //Récupérer le formulaire de review et le soumettre
        $buttons = $crawler->selectButton('Poster');
        self::assertCount(1, $buttons);
        
        $form = $buttons->form();
        $form['review[rating]']->setValue('5');
        $form['review[comment]']->setValue('Super jeu !');
        $this->client->submit($form);
       
        //Vérifier le statut HTTP de la réponse après soumission du formulaire
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        //Vérifier que la review est affichée sur la page (redirection vers la page du jeu vidéo)
        $this->client->followRedirect();
        self::assertSelectorTextContains('div.list-group-item:last-child h3', 'user+0');
        self::assertSelectorTextContains('div.list-group-item:last-child p', 'Super jeu !');
        self::assertSelectorTextContains('div.list-group-item:last-child span.value', '5');

        //Vérifier que le formulaire n'est plus présent après la soumission
        $buttons = $this->client->getCrawler()->selectButton('Poster');
        self::assertCount(0, $buttons);
    }
}
