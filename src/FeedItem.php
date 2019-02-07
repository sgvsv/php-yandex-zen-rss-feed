<?php

namespace sgvsv\Yandex\Zen;

/**
 * Item's properties
 *
 * @property string $title
 * @property string $link
 * @property string $pubDate
 * @property string $author
 * @property string $category
 * @property string $description
 * @property string $content
 * @property string $rating
 */
class FeedItem
{
    /** Configuration: list of elements in each feed's element
     * @var array
     */
    private $elements = [
        'title' => ['cdata' => false,
            'defaultValue' => ''
        ],
        'link' => ['cdata' => false,
            'defaultValue' => ''
        ],
        'pubDate' => ['cdata' => false,
            'defaultValue' => ''
        ],
        'author' => ['cdata' => false,
            'defaultValue' => ''
        ],
        'category' => ['cdata' => false,
            'defaultValue' => ''
        ],
        'description' => ['cdata' => true,
            'defaultValue' => ''
        ],
        'content' => ['openTag' => 'content:encoded',
            'closeTag' => 'content:encoded',
            'cdata' => true,
            'defaultValue' => '',
            'filterTags' => [
                '<p>',
                '<figure>',
                '<img>',
                '<figcaption>',
                '<video>',
                '<source>',
                '<media:content>',
                '<media:description>',
                '<media:copyright>',
                '<span>',
            ],
        ],
        'rating' => ['openTag' => 'media:rating scheme="urn:simple"',
            'closeTag' => 'media:rating',
            'cdata' => false,
            'defaultValue' => 'nonadult'
        ],

    ];
    private $images = [];

    /** Setter for elements value
     * @param $name - name of element to set
     * @param $value - value to set
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->elements))
            $this->elements[$name]['value'] = $value;
    }

    /** Getter for elements value
     * @param string $name - name of element
     * @return string|null - value or null if there is no such element
     */
    public function __get(string $name)
    {
        $result = null;
        if (array_key_exists($name, $this->elements))
            $result = isset($this->elements[$name]['value']) ?
                $this->elements[$name]['value'] :
                $this->elements[$name]['defaultValue'];
        return $result;
    }

    /** Private method that generates XML part of each element depending of it's configuration
     * @param string $name - name of element
     * @return null|string - XML string or null
     */
    private function generateElement(string $name)
    {
        $result = $this->__get($name);
        $element = $this->elements[$name];
        if (isset($element['filterTags'])&&is_array($element['filterTags']))
            $result = strip_tags($result, implode('', $element['filterTags']));
        if ($element['cdata'])
            $result = "<![CDATA[$result]]>";
        $result = (isset($element['openTag']) ? "<${element['openTag']}>" : "<$name>") . $result .
            (isset($element['closeTag']) ? "</${element['closeTag']}>" : "</$name>");
        return $result;
    }

    /** Generates complete XML string of this feed item
     * @return string
     */
    public function getXML()
    {
        $this->elements['guid'] = $this->elements['link'];
        $result = "";
        foreach (array_keys($this->elements) as $element)
            $result .= $this->generateElement($element) . "\n";
        $result .= $this->getImagesXML(true);
        $result .= $this->getImagesXML();
        $result = "<item>\n$result</item>";
        return $result;
    }

    private function getImagesXML(bool $enclosure = false)
    {

        $result = "";
        foreach ($this->images as $image)
            if ($enclosure)
                $result .= "<enclosure url=\"{$image['location']}\" type=\"" . $this->imageMime($image['location']) . "\" length=\"" . $image['length'] . "\"/>\n";
            else
                $result .= "
            <media:content type=\"" . $this->imageMime($image['location']) . "\" medium=\"image\"
                url=\"{$image['location']}\">
            <media:description type=\"plain\">{$image['description']}</media:description>
        </media:content>";

        return $result;
    }

    /**
     * Adds image to feed element
     * @param string $location url of image
     * @param string $description text description of image
     * @param int $length size of image in bytes
     */
    public function addImage(string $location, string $description, int $length)
    {
        $this->images[] = ['location' => $location, 'description' => $description, 'length' => $length];
    }

    private function imageMime(string $filename)
    {
        $mime_types = [
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
        ];
        $aTmp = explode('.', $filename);
        $ext = strtolower(array_pop($aTmp));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        } else {
            return 'application/octet-stream';
        }
    }
}