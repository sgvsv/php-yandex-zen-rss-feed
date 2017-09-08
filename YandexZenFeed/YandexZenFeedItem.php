<?php
namespace YandexZenFeed;

class YandexZenFeedItem
{
    /** Configuration: list of elements in each feed's element
     * @var array
     */
    private $elements = array(
        'title' => array('cdata' => false,
            'defaultValue' => ''
        ),
        'link' => array('cdata' => false,
            'defaultValue' => ''
        ),
        'pubDate' => array('cdata' => false,
            'defaultValue' => ''
        ),
        'author' => array('cdata' => false,
            'defaultValue' => ''
        ),
        'category' => array('cdata' => false,
            'defaultValue' => ''
        ),
        'description' => array('cdata' => true,
            'defaultValue' => ''
        ),
        'content' => array('openTag' => 'content:encoded',
            'closeTag' => 'content:encoded',
            'cdata' => true,
            'defaultValue' => ''
        ),
        'rating' => array('openTag' => 'media:rating scheme="urn:simple"',
            'closeTag' => 'media:rating',
            'cdata' => false,
            'defaultValue' => 'nonadult'
        ),

    );
    private $images = Array();

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
     * @param $name - name of element
     * @return string|null - value or null if there is no such element
     */
    public function __get($name)
    {
        $result = null;
        if (array_key_exists($name, $this->elements))
            $result = isset($this->elements[$name]['value']) ?
                $this->elements[$name]['value'] :
                $this->elements[$name]['defaultValue'];
        return $result;
    }

    /** Private method that generates XML part of each element depending of it's configuration
     * @param $name - name of element
     * @return null|string - XML string or null
     */
    private function generateElement($name)
    {
        $result = $this->__get($name);
        $element = $this->elements[$name];
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

    private function getImagesXML($enclosure = false)
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
     * @param $location url of image
     * @param $description text description of image
     * @param $length size of image in bytes
     */
    public function addImage($location, $description, $length)
    {
        $this->images[] = Array('location' => $location, 'description' => $description, 'length' => $length);
    }

    private function imageMime($filename)
    {
        $mime_types = array(
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
        );

        $ext = strtolower(array_pop(explode('.', $filename)));
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