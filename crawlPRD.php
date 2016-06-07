<?php
/**
 * Created by PhpStorm.
 * User: Dien Tran
 * Date: 21-Jan-16
 * Time: 7:13 PM
 */
set_time_limit(1000);
include("libs/PHPCrawler.class.php");
include("simple_html_dom.php");

function getStringBetween($str,$from,$to)
{
    $sub = substr($str, strpos($str,$from)+strlen($from),strlen($str));
    return substr($sub,0,strpos($sub,$to));
}

class Crawler extends PHPCrawler
{
    function handleDocumentInfo($DocInfo)
    {
        if (PHP_SAPI == "cli") $lb = "\n";
        else $lb = "<br />";
        $html = file_get_html($DocInfo->url);
        if (is_object($html)) {
            $start = "<span class=\"inp\">";
            $end = "</span>";
            foreach ($html->find("span.inp") as $e) {
                $linkToCrawl = getStringBetween($e->innertext, $start, $end);
//                if (strstr($linkToCrawl, "Xe")) {
          echo $linkToCrawl.$lb;
//                    putTest($linkToCrawl);
//                }
//        $crawlerElement = new MyCrawler();
//        $crawlerElement->setURL($linkToCrawl);
//        $crawlerElement->addContentTypeReceiveRule("#text/html#");
//        $crawlerElement->go();

            }
        }
        $html->clear();
        unset($html);
//        var_dump($arrayLink);
    }
}

$crawler = new Crawler();
$crawler->setURL("http://www.bonbanh.com/Xe-Mercedes_Benz-CLA_class-CLA-250-4Matic-2016-225274.html");
$crawler->addContentTypeReceiveRule("#text/html#");
$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i");
$crawler->go();
