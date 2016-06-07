<?php
set_time_limit(100000);
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

//                $address
                $userInfoBlock = getStringBetween($e->innertext, "class=\"bcar_contact\">", "</div>") . "endString";
                $addressBlock = getStringBetween($userInfoBlock, "<br>", "endString");
                $address = substr($addressBlock, 21, strlen($addressBlock));
//                echo $address."<br>";

//                name
                $contactBlock = trim(getStringBetween($e->innertext, "class=\"bcar_contact\">", "<br>"));
                $ogrigin = strip_tags($contactBlock);
                $name = trim(getStringBetween($ogrigin, "Liên hệ:", "- ĐT"));
//                echo $name."<br>";

//                phone
                $phone = trim(getStringBetween($ogrigin, "- ĐT:", "."));
//                echo $phone . "<br>";

//                city
                $city = getStringBetween($e->innertext, "class=\"bcar_city\">", "<br>");
//                echo $city."<br>";

                if (filter("user", "name", $name)) {
                    pushDB(
                        "user",
                        "name,address,phone,city,roles",
                        $name . "','" . $address . "','" . $phone . "','" . $city. "','" ."a:0:{}"
                    );
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