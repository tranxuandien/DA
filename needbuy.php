<?php
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
//            echo $html;
            foreach ($html->find("div.buy_car_item") as $e) {
                $code = getStringBetween($e->innertext, "<br>", "</div>");
                $title =getStringBetween($e->innertext, "class=\"bcar_title\">", "<font");
                $price = getStringBetween($e->innertext, "13\">", "</font>");
                $content = getStringBetween($e->innertext, "</span>", "</div>");
//                $address = getStringBetween($e->innertext, "class=\"bcar_contact\">", "</div>");
                $address = trim(getStringBetween($e->innertext, "class=\"bcar_contact\">", "<br>"));
//                $city = getStringBetween($e->innertext, "class=\"bcar_city\">", "<br>");
                $date = substr(getStringBetween($e->innertext, "style=\"font-weight:normal;color:#666;font-style:italic\">", "</span>"), 6);
                $ogrigin = strip_tags($address);
                $contact=trim(getStringBetween($ogrigin,"Liên hệ:","- ĐT"));
                $userId=searchUserId($contact);
//                echo $userId."+".$contact."<br>";

//                $phone = getStringBetween($e->innertext, "?T:0", ".");
                if (filter("buy_news", "code", $code)) {
                pushDB("buy_news", "user_id,code,title,content,price,date", $userId. "','" .$code . "','" . $title . "','" . $content . "','" . $price . "','". $date);
                }
            }
        }
        $html->clear();
        unset($html);
        echo $lb;
        flush();
    }
}

for ($i = 1; $i < 5; $i++) {
    $crawler = new MyCrawler();
    $crawler->setURL("http://www.bonbanh.com/tin-mua-xe/page," . $i . "/");
    $crawler->setPageLimit(1);
    $crawler->go();
}
?>