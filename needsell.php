<?php
set_time_limit(10000000);
include("libs/PHPCrawler.class.php");
include("simple_html_dom.php");
include("functions.php");
function checkShop($table, $col, $val)
{
    $c = connect();
    $query = "SELECT * FROM $table WHERE $table.$col=$val";
    mysql_query("SET NAMES 'UTF8'");
    $retval=mysql_query($query, $c);
    if($retval!=null) {
        while ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
            if ($row[$col] == $val) {
                mysql_close($c);
                return $row['id'];
            }
        }
    }
    mysql_close($c);
    return null;
}

class MyCrawler extends PHPCrawler
{
    function handleDocumentInfo($DocInfo)
    {
        if (PHP_SAPI == "cli") $lb = "\n";
        else $lb = "<br />";

        $html = file_get_html($DocInfo->url);
        if (is_object($html)) {
            foreach ($html->find("div.car-item") as $e) {
                $div = getStringBetween($e->innertext, "car_code", "</span>");
                $code = substr($div, 7);
                $yearStatus = strip_tags(getStringBetween($e->innertext, "class=\"cb1\">", "</div>"));
                $mang=explode(" ",$yearStatus);
                $year=$mang[3];
                $status=$mang[1]." ".$mang[2];
                $kind = getStringBetween($e->innertext, "title=\"", "\"");
                $priceString = getStringBetween(strip_tags(getStringBetween($e->innertext, "class=\"cb3\">", "</div>")),"~","USD");
                $priceFloat= floatval(str_replace(",",".",$priceString))*1000;
                $city = strip_tags(getStringBetween($e->innertext, "class=\"cb4\">", "</div>"));
                $info = strip_tags(getStringBetween($e->innertext, "class=\"cb6_02\">", "</div>"));
                $contactBlock = strip_tags(getStringBetween($e->innertext, "class=\"cb7\">", "</div>"), "<br>");
//                Phan lien ket de lay shop_id
                $contact=trim(getStringBetween($contactBlock,"Liên hệ:","<br>"));
                $shopId=searchShopId($contact);//ok
//                echo $shopId."---<br>";
//                Phan lien ket de lay brand_id
                $brand=explode(' ', $kind)[0];
                if($brand!="Mercedes"&&$brand!="Aston"&&$brand!="Rolls")
                $brandId=searchBrandId($brand);
                else{
                    if($brand=="Mercedes")$brandId=16;
                    if($brand=="Aston")$brandId=28;
                    if($brand=="Rolls")$brandId=65;
                }
//                echo $code."--".$brandId."--".$shopId."--".$status."--".$year."--".$kind."--".$priceFloat."--".$city."--".$info."--".$contactBlock."<br>";

//                Phan lien ket de lay user_id
                if($shopId==0)
                {
                    $userId=searchUserId($contact);
                    $shopId=0;
//                    $userId=1;
//                    echo $userId."+".$contact."<br>";
                }
                else
                {
                    $userId=0;
                }
                $imgLink = strip_tags(getStringBetween($e->innertext, "class=\"cb5\">", "<span"),"<img>");
//                $contactName = strip_tags(getStringBetween($e->innertext, "class=\"cb7\">", "</div>"), "<br><b>");
//                $contactName1=getStringBetween($contactName,"<b>","</b>");
//                echo $contactName1."<br>";
//                $shopId=checkShop("shop_info","name",$contactName1);
//                echo $userId."+".$shopId."<br>";

                if (filter("sell_news", "code", $code)) {
                    pushDB(
                        "sell_news",
                        "code,user_id,brand_id,shop_id,status,kind,price,city,info,year,image_link",
                        $code ."','" .$userId."','" .$brandId. "','" .$shopId. "','" . $status . "','" . $kind . "','" . $priceFloat . "','" . $city . "','" . $info .  "','" .$year. "','" .$imgLink);
                }
            }
        }
        $html->clear();
        unset($html);
        echo $lb;
        flush();
    }
}

for ($i = 1; $i <5; $i++) {
    $crawler = new MyCrawler();
    $crawler->setURL("http://www.bonbanh.com/oto/page," . $i . "/");
    $crawler->setPageLimit(1);
    $crawler->go();
//    echo $i;
}
?>