<?php
//header("Refresh:30 ;url='http://localhost/cr/example.php'");
set_time_limit(1000);
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
//            foreach ($html->find("div.cb2") as $e) {
//                $linkToCrawl = getStringBetween($e->innertext, $start, $end);
//                if (strstr($linkToCrawl, "Xe")) {
////                    echo $linkToCrawl . $lb;
//                    if (filter("link", "link", $linkToCrawl)) {
//                        pushDB("link", "link", $linkToCrawl);
//                    }
//                }
//            }
            //Get data brand
            foreach ($html->find("li.menuparent") as $e) {
                $brand = getStringBetween($e->innertext, ">", "</a>");
                if (!strstr($brand, "Hãng khác")) {
                    pushDB("brands", "name", $brand);
                }
            }
            //Get data car_shop table
//            foreach ($html->find("div.cb7") as $e) {
////                echo $e;
//                $shopName = getStringBetween($e->innertext, "<b>", "</b>");
//                $shopAdress = getStringBetween($e->innertext, "<br>", "<br>");
//                $phone = substr(strstr($e->innertext, "ĐT:"), 4);
//                echo $shopName . "-" . $shopAdress . "-" . $phone . "<br>";
//                if (filter("car_shop", "name", $shopName)) {
//                    pushDB("car_shop", "name,address,phone", $shopName . "','" . $shopAdress . "','" . $phone);
//                }
//            }
        }
        $html->clear();
        unset($html);
        echo $lb;
        flush();
    }
}

$crawler = new MyCrawler();
$crawler->setURL("bonbanh.com");
$crawler->setPageLimit(1);
//$crawler->addContentTypeReceiveRule("#text/html#");
//$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i");
$crawler->go();
?>