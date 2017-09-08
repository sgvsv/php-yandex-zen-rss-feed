# YandexZenFeed
Simple PHP class for making RSS feed in Yandex zen format
## Usage example
````php
require "YandexZenFeed\YandexZenFeed.php";
require "YandexZenFeed\YandexZenFeedItem.php";

//New feed with global parameters in constructor
$feed = new YandexZenFeed\YandexZenFeed('My feeds title', 'Description text here', 'https://mysite.com', 'https:/mysite.com/rss.xml');

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