<?php

namespace App\test\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\BookRepository;

class BookControllerTest extends WebTestCase
{
    public function testIndex()
    {
       $client = static::createClient();
	    
       $crawler = $client->request('GET', '/books-list');
	    
       $countPages = $crawler->filter('.pagination, .page')->count();
       $crawler = $client->request('GET', '/books-list?page=2');
       $pages = $crawler->filter('.pagination, .current')->each(function ($node) {
           return [
      	    	     'url' => $node->attr('href'),
      	    	     'numPage' => trim($node->text()),
      	    	     'html' => trim($node->html()),
    	    	  ];
	     });

       $numPage = $pages[1]['numPage'];

       $link = $crawler->filter('.pagination, .page, a')->link();
       $crawler = $client->click($link);

       $this->assertSame("2", $numPage);
       $this->assertSame(3, $countPages);
       $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreate()
    {
        $bookTitle = "test1";
        $bookAuthor = "test1";
	    
        $client = static::createClient();
	    
        $crawler = $client->request('GET', '/book/create');

        $title = 'test1';
        $author = 'test1';

        $form = $crawler->selectButton('Save')->form([
          'book[title]' => $title,
          'book[author]' => $author,
        ]);

        $client->submit($form);
	
	$book = self::$container->get(BookRepository::class)->findOneByTitle($bookTitle);

        $this->assertResponseRedirects('/books-list', Response::HTTP_FOUND);
        $this->assertNotNull($book);
        $this->assertSame($bookTitle, $book->getTitle());
        $this->assertSame($bookAuthor, $book->getAuthor());
    }

    public function testEdit()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/book/edit/1');

        $newTitle = 'test1';

        $form = $crawler->selectButton('Save')->form([
          'book[title]' => $newTitle
        ]);

        $client->submit($form);
	
	$book = self::$container->get(BookRepository::class)->find(1);

        $this->assertResponseRedirects('/books-list', Response::HTTP_FOUND);
        $this->assertSame($newTitle , $book->getTitle());
    }

    public function testDelete()
    {
        $client = static::createClient();
	    
        $crawler = $client->request('GET', '/book/remove/1');

        $link = $crawler->filter('#deleteBook, a')->link();
        $crawler = $client->click($link);

        $book = self::$container->get(BookRepository::class)->find(1);
        $this->assertNull($book);
    }
}
