<?php

namespace YandexZenFeed;

class YandexZenFeed
{
    /** Array of YandexZenFeedItem objects
     * @var array
     */
    private $items = array();

    /**
     * @property string $language Language of feed (default will be en)
     * @property string $description Description of feed (no tags)
     * @property string $link Link to feed's site
     * @property string $title Title of feed
     * @property string $encoding Encoding of feed (could be changed)
     */

    public $encoding = "UTF-8";
    public $title;
    public $link;
    public $description;
    public $language;
    public $rssLink;


    /**
     * YandexZenFeed constructor.
     * @param string $title
     * @param string $description
     * @param string $link
     * @param string $rssLink
     * @param string $language
     */
    public function __construct($title, $description, $link, $rssLink, $language = "en")
    {
        $this->title = $title;
        $this->description = $description;
        $this->link = $link;
        $this->rssLink = $rssLink;
        $this->language = $language;
    }

    public function & newItem()
    {
        $item = new YandexZenFeedItem();
        $this->items[] = $item;
        return $item;
    }

    /**
     * @param $name
     * @return string
     */
    private function generateElement($name)
    {
        return isset($this->$name) ? ("<$name>" . htmlspecialchars(trim($this->$name)) . "</$name>\n") : '';
    }

    /** Return RSS Feed
     * @return string
     */
    public function getXML()
    {
        $result = "<?xml version=\"1.0\" encoding=\"{$this->encoding}\"?>\n<rss version=\"2.0\" \nxmlns:content=\"http://purl.org/rss/1.0/modules/content/\" \nxmlns:dc=\"http://purl.org/dc/elements/1.1/\" \nxmlns:media=\"http://search.yahoo.com/mrss/\" \nxmlns:atom=\"http://www.w3.org/2005/Atom\"\nxmlns:georss=\"http://www.georss.org/georss\">\n";
        $channelElements = $this->generateElement('title') . $this->generateElement('description') . $this->generateElement('link') . $this->generateElement('language');
        $feedElements = "";
        foreach ($this->items as $item)
            $feedElements .= $item->getXML() . "\n";
        $result .= "<channel>\n$channelElements\n<atom:link href=\"{$this->rssLink}\" rel=\"self\" type=\"application/rss+xml\" />\n$feedElements\n</channel></rss>";
        return $result;
    }

    public function setHTTPHeader()
    {
        header("Content-Type: text/xml; charset={$this->encoding}", true);
    }
}