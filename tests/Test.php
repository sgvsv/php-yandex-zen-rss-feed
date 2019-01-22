<?php
require_once __DIR__ . '/../vendor/autoload.php';

class Test extends PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $this->assertIsObject(new \sgvsv\Yandex\Zen\Feed('test', 'test', 'test', 'test'));
        $this->assertIsObject(new \sgvsv\Yandex\Zen\Feed('test', 'test', 'test', 'test', 'en'));
        $this->assertIsObject(new \sgvsv\Yandex\Zen\Feed('На', 'русском', 'языке', 'проверка', 'ru'));
    }

    public function testAddingItem()
    {
        $this->assertIsObject($feed = new \sgvsv\Yandex\Zen\Feed('My feeds title', 'Description text here', 'https://mysite.com', 'https:/mysite.com/rss.xml'));
        $this->assertIsObject($item = $feed->newItem());
        $item->title = "News 1";
        $item->link = "https://mysite.com/news/1";
        $item->pubDate = "Fri, 12 Aug 2013 15:52:01 +0000";
        $item->author = 'editor@mysite.com (my site)';
        $item->category = 'software';
        $item->description = "Description's text";
        $item->content = "Content of my news";
        $item->addImage("https://mysite.com/images/news1.jpg", "Image's description", "1234");


        $this->assertIsObject($item = $feed->newItem());
        $item->title = "News 2";
        $item->link = "https://mysite.com/news/2";
        $item->pubDate = "Fri, 12 Aug 2013 16:52:01 +0000";
        $item->author = 'editor@mysite.com (my site)';
        $item->category = 'software';
        $item->description = "Description's text";
        $item->content = "Content of my news";
        $item->addImage("https://mysite.com/images/news2.jpg", "Image's description", "4567");

        $this->assertIsString($result = $feed->getXML());


    }
}