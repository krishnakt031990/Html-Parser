<?php

class HTMLParser
{
    public $htmlurl = "";
    public $pageHeader = "";
    public $imageHolder = array();
    public $bodyHolder = array();

    public function __construct($url)
    {
        $this->htmlurl = $url; //if we need to get the url sometime.

        $string = file_get_contents($url);

        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="UTF-8">' . $string);
        $elements = array('h1', 'img', 'p'); //tags we are searching for.

        foreach ($elements as $element) {
            $imageId = 0;
            $bodyId = 0;
            $domelements = $doc->getElementsByTagName($element);
            foreach ($domelements as $domelement) {
                if ($element == 'h1') {
                    $this->pageHeader = "<h1>" . $domelement->nodeValue . "</h1> <br>";
                }
                if ($element == 'img') {
                    $urlImg = $domelement->getAttribute('src');
                    $pattern = '/http.*/';          //regex to take the correct jpg image url.
                    $img_formats = array("jpg", "jpeg", "gif", "tiff"); //Not taking the png imaging because they are usually the logs.
                    preg_match($pattern, $urlImg, $url);
                    $urlPath = $url[0];
                    $path_info = pathinfo($urlPath);
                    if (in_array(strtolower($path_info['extension']), $img_formats)) {
                        // echo "$urlPath"."<br>";
                        $this->imageHolder[$imageId] = "<img src=\"" . $urlPath . "\">" . "</br>";
                        $imageId += 1;
                        // echo "<img src=\"". $urlPath ."\">"."</n>";
                    }
                }
                if ($element == 'p') {
                    $this->bodyHolder[$bodyId] = $domelement->nodeValue . "<br>";
                    $bodyId += 1;
                }
            }
        }
    }

    public function getHeader()
    {
        return $this->pageHeader;
    }

    public function getHtmlUrl()
    {
        return $this->htmlurl;
    }

    public function getImageHolder()
    {
        return $this->imageHolder;
    }

    public function getBodyHolder()
    {
        return $this->bodyHolder;
    }
}

$htmlParser = new HTMLParser($_POST["cnnurl"]);
// echo $object->getHtmlUrl();

echo $htmlParser->getHeader();

$imagePlaceHolder = $htmlParser->getImageHolder();
echo $imagePlaceHolder[0]; // Saw that only the first position image is relavent in most of the articles. Hence using only 0 instead of the for loop.

$bodyHolder = $htmlParser->getBodyHolder();
foreach ($bodyHolder as $content) {
    echo $content;
}
?>