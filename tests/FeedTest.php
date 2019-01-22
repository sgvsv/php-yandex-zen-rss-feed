<?php
declare(strict_types=1);

final class Feed extends PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $this->assertIsObject(new \sgvsv\Yandex\Zen\Feed('Feed', 'Feed', 'Feed', 'Feed'));
        $this->assertIsObject(new \sgvsv\Yandex\Zen\Feed('Feed', 'Feed', 'Feed', 'Feed', 'en'));
        $this->assertIsObject(new \sgvsv\Yandex\Zen\Feed('На', 'русском', 'языке', 'проверка', 'ru'));
    }


    private function createDemoFeed()
    {
        $this->assertIsObject($feed = new \sgvsv\Yandex\Zen\Feed(
            "Лента новостей",
            "Все новости нашего сайта в одной удобной ленте на яндекс дзене",
            "https://mysitename.org",
            'https://mysitename.org/rss.xml',
            'ru'
        ));
        $this->assertIsObject($item = $feed->newItem());
        $item->link = "https://mysitename.org/news/1.html";
        $item->author = "Редактор сайта";
        $item->title = "Заголовок материала";
        $item->category = "news";
        $item->content = "<p>Содержимое новости</p>";
        $item->description = "Первая новость сайта";
        $item->pubDate = "Fri, 13 Aug 2059 16:12:01 +0000";
        $item->rating = "adult";

        $this->assertIsObject($item = $feed->newItem());
        $item->title = "News 2";
        $item->link = "https://mysite.com/news/2";
        $item->pubDate = "Fri, 12 Aug 2013 15:52:01 +0000";
        $item->author = 'editor@mysite.com (my site)';
        $item->category = 'software';
        $item->description = "Description's text";
        $item->content = "Content of my news";
        $item->addImage("https://mysite.com/images/news1.jpg", "Image's description", 1234);


        $this->assertIsObject($item = $feed->newItem());
        $item->title = "News 3";
        $item->link = "https://mysite.com/news/3";
        $item->pubDate = "Fri, 12 Aug 2013 16:52:01 +0000";
        $item->author = 'editor@mysite.com (my site)';
        $item->category = 'software';
        $item->description = "Description's text";
        $item->content = "Content of my news";
        $item->addImage("https://mysite.com/images/news2.jpg", "Image's description", 4567);

        return $feed;

    }


    public function testValidXML()
    {
        $feed = $this->createDemoFeed();
        $this->assertIsString($result = $feed->getXML());
        libxml_use_internal_errors(true);
        $this->assertIsObject($this->xml = simplexml_load_string($result));
    }

    public function testAddingItems()
    {
        $feed = $this->createDemoFeed();
        $this->assertIsArray($items = $feed->getItems());
        $this->assertCount(3, $items);
    }

    public function testContainItem1()
    {
        $feed = $this->createDemoFeed();
        $this->assertIsString($xml = $feed->getXML());
        $this->assertContains('https://mysitename.org/news/1.html', $xml);
        $this->assertContains('Редактор сайта', $xml);
        $this->assertContains('Заголовок материала', $xml);
        $this->assertContains('news', $xml);
        $this->assertContains('<p>Содержимое новости</p>', $xml);
        $this->assertContains("Первая новость сайта", $xml);
        $this->assertContains("Fri, 13 Aug 2059 16:12:01 +0000", $xml);
        $this->assertContains("adult", $xml);
    }

    public function testContainItem2()
    {
        $feed = $this->createDemoFeed();
        $this->assertIsString($xml = $feed->getXML());
        $this->assertContains('News 2', $xml);
        $this->assertContains('https://mysite.com/news/2', $xml);
        $this->assertContains("https://mysite.com/images/news2.jpg", $xml);
        $this->assertContains("Image's description", $xml);
        $this->assertContains("1234", $xml);
    }

    public function testContainItem3()
    {
        $feed = $this->createDemoFeed();
        $this->assertIsString($xml = $feed->getXML());
        $this->assertContains('News 3', $xml);
        $this->assertContains('https://mysite.com/news/3', $xml);
        $this->assertContains('Fri, 12 Aug 2013 16:52:01 +0000', $xml);
        $this->assertContains('editor@mysite.com (my site)', $xml);
        $this->assertContains('software', $xml);
        $this->assertContains("Description's text", $xml);
        $this->assertContains("Content of my news", $xml);
        $this->assertContains("https://mysite.com/images/news2.jpg", $xml);
        $this->assertContains("Image's description", $xml);
        $this->assertContains("4567", $xml);
    }

    public function testContainComminData()
    {
        $feed = $this->createDemoFeed();
        $this->assertIsString($xml = $feed->getXML());
        $this->assertContains('Лента новостей', $xml);
        $this->assertContains('Все новости нашего сайта в одной удобной ленте на яндекс дзене', $xml);
        $this->assertContains('https://mysitename.org', $xml);
        $this->assertContains('https://mysitename.org/rss.xml', $xml);
    }
}