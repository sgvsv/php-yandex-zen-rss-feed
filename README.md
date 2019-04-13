# YandexZenFeed
Simple PHP class for making RSS feed in Yandex zen format
## Requirements
 For providing modern PHP features such as setting types in method's signatures **PHP7.1 or higher** is required. Also you need to have some extensions:
* ext-fileinfo
* ext-libxml
* ext-simplexml

If you want to run tests you will need **phpunit** higher than 7.5
## Installation
Best way to use this library is install it via composer:
````bash
composer require sgvsv/php-yandex-zen-rss-feed
````
## Usage example
After installing you can create feed:
````php
require_once __DIR__.'/vendor/autoload.php';

//New feed with global parameters in constructor
$feed = new \sgvsv\Yandex\Zen\Feed('My feeds title', 'Description text here', 'https://mysite.com', 'https:/mysite.com/rss.xml');

//News 1 item
$item = $feed->newItem();
$item->title = "News 1";
$item->link = "https://mysite.com/news/1";
$item->pubDate = "Fri, 12 Aug 2013 15:52:01 +0000";
$item->author = 'editor@mysite.com (my site)';
$item->category = 'software';
$item->description = "Description's text";
$item->content = "Content of my news";
$item->addImage("https://mysite.com/images/news1.jpg", "Image's description", "1234");

//Set HTTP header 
$feed->setHTTPHeader();
//Output Feed 
echo $feed->getXML();
````