<?php
set_time_limit(10000);
include("libs/PHPCrawler.class.php");
include("simple_html_dom.php");
include("functions.php");
class MyCrawler extends PHPCrawler
{
    public $id;
    public $name;
    public $image;

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    function handleDocumentInfo($DocInfo)
    {
        if (PHP_SAPI == "cli") $lb = "\n";
        else $lb = "<br />";

        $html = file_get_html($DocInfo->url);
        if (is_object($html)) {
//Get data of salon from salon link
            foreach ($html->find("div.g-box-content") as $e) {
                $name = rtrim(getStringBetween($e->innertext, "title=\"", "\""),"class=");
                $address=strip_tags(getStringBetween($e->innertext, "<b>Địa chỉ</b>:", "<b>Điện thoại</b>"),"<br>");
                $about = getStringBetween($e->innertext, "style=\"padding:0px 10px 10px 10px;line-height:18px;font-size:12px;color:#555;\">", "<!-- end content salon -->");
                $phone = getStringBetween(strip_tags(getStringBetween($e->innertext, "<br>", "<br>")),":",".");
                if ($address!=null) {
                pushDB("shop_info", "name,image,address,phone,about,shop_link_id", $this->getName() . "','" .$this->getImage() . "','" .$address . "','" . $phone . "','" . $about  . "','" .$this->getId());
                }
            }
        }
        $html->clear();
        unset($html);
        echo $lb;
        flush();
    }
}
//For get information
$pagenum=10;
$ret=getDB();
while ($row = mysql_fetch_array($ret, MYSQL_ASSOC&&$pagenum>0)) {
    $url=$row['link_shop'];
    $name=$row['name'];
    $image=$row['image'];
    $id=$row['id'];
    if($url==null)
        continue;
    $crawler = new MyCrawler();
    $crawler->setURL($url);
    $crawler->setId($id);
    $crawler->setName($name);
    $crawler->setImage($image);
echo $row['link_shop']."<br>";
    $crawler->setPageLimit(1);
    $crawler->go();
    $pagenum=$pagenum-1;
}
?>