<?php
$start = microtime(true);
set_time_limit(1000000000);
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
            foreach ($html->find("div.car-item") as $e) {
                //city
                $city = strip_tags(getStringBetween($e->innertext, "class=\"cb4\">", "</div>"));
                //name
                $contactBlock = strip_tags(getStringBetween($e->innertext, "class=\"cb7\">", "</div>"), "<br>");
                $name = trim(getStringBetween($contactBlock, "Liên hệ:", "<br>"));
                //phone
                $ogrigin = htmlentities($contactBlock);
                $poision = stripos($ogrigin, "ĐT:");
                $phone = trim(substr($ogrigin, $poision + 5, strlen($ogrigin)));
                //address
                $address = getStringBetween($contactBlock, "<br>", "<br>");

                if (filter("user","name",$name)) {
                    pushDB("user", "name,address,phone,city,roles",$name . "','" . $address . "','" . $phone . "','" . $city. "','" ."a:0:{}");
                }
            }
        }
        $html->clear();
        unset($html);
        echo $lb;
        flush();
    }
}

for ($i = 1; $i < 1083; $i++) {
    $crawler = new MyCrawler();
    $crawler->setURL("http://www.bonbanh.com/oto/page," . $i . "/");
    $crawler->setPageLimit(1);
    $crawler->go();
//    echo $i;
}
$time_elapsed_secs = microtime(true) - $start;
echo "done!".$time_elapsed_secs;
?>