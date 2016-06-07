<?php
set_time_limit(10000);
include("libs/PHPCrawler.class.php");
include("simple_html_dom.php");
include("functions.php");

class MyCrawler extends PHPCrawler
{
    function handleDocumentInfo($DocInfo)
    {
        if (PHP_SAPI == "cli") $lb = "\n";
        else $lb = "<br />";

        $html = file_get_html($DocInfo->url);
        if (is_object($html)) {
//Get salon link
            foreach ($html->find("div.s_i1") as $e) {
                $shopLink=getStringBetween($e->innertext,"href=\"","\"");
                $shopName=trim(getStringBetween($e->innertext,"title=\"","\">"));
                $shopImage=str_replace("\"","",getStringBetween($e->innertext,"data-original=\"","src="));
//                echo "<img src=\"".$shopImage."\">" . "<br>";
                    pushDB("shop_link", "link_shop,name,image", $shopLink."','".$shopName."','".$shopImage);
            }
        }
        $html->clear();
        unset($html);
        echo $lb;
        flush();
    }
}
//For get link
$crawler = new MyCrawler();
$crawler->setURL("http://www.bonbanh.com/salon-oto/");
$crawler->setPageLimit(1);
$crawler->go();
echo "<img  src=\"http://www.bonbanh.com/uploads/users/129562/salon/s_1439974457.jpg\">";
?>