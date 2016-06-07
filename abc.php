<?php
header("Refresh:30 ;url='http://localhost/cr/abc.php'");
set_time_limit(1000);
include("libs/PHPCrawler.class.php");
include("simple_html_dom.php");

function getStringBetween($str, $from, $to)
{
    $sub = substr($str, strpos($str, $from) + strlen($from), strlen($str));
    return substr($sub, 0, strpos($sub, $to));
}

function connect()
{
    $c = mysql_connect('127.0.0.1', 'root', '');
    mysql_select_db('cr');
    return $c;
}

function putTest($t)
{
    $c = connect();
    $query = "INSERT INTO link (link) VALUES ('$t')";
    mysql_query($query, $c);
    mysql_close($c);
}

function filter($table, $col, $val)
{
    $c = connect();
    $query = "SELECT $col FROM $table";
    mysql_query("SET NAMES 'UTF8'");
    $retval = mysql_query($query, $c);
    while ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        if ($row[$col] == $val) {
            mysql_close($c);
            return 0;
        }
    }
    mysql_close($c);
    return 1;
}

function pushDB($table, $col, $val)
{
    $c = connect();
    $query = "INSERT INTO $table ($col) VALUES ('$val')";
    mysql_query("SET NAMES 'UTF8'");
    mysql_query($query, $c);
    mysql_close($c);
}

class MyCrawler extends PHPCrawler
{
    function handleDocumentInfo($DocInfo)
    {
        if (PHP_SAPI == "cli") $lb = "\n";
        else $lb = "<br />";

        $html = file_get_html($DocInfo->url);
        if (is_object($html)) {
            echo $html;
//Get shop link
            $start = "href=\"";
            $end = "\"";
            foreach ($html->find("div.s_i1") as $e) {
                $linkToCrawl = getStringBetween($e->innertext, $start, $end);
                $shopName = getStringBetween($e->innertext, "title=\"", "\">");

                echo $linkToCrawl . $lb;
                if (strstr($linkToCrawl, ".bonbanh.com")) {
                    if (filter("shop_link", "link_shop", $linkToCrawl)) {
                        pushDB("shop_link", "link_shop,name", $linkToCrawl."','".$shopName);
                    }
                }
            }

//            foreach ($html->find("li.menuparent") as $e) {
////                echo $e;
//                $brand = getStringBetween($e->innertext, ">", "</a>");
//                if (!strstr($brand, "Hãng khác")) {
//                    pushDB("brands","name",$brand);
//                }
//            }
//Get shop name
//            foreach ($html->find("div.s_i1") as $e) {
////                echo $e;
//                echo $shopName . "<br>";
//                if (filter("shop_link", "name", $shopName)) {
//                    pushDB("shop_link", "name", $shopName);
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
$crawler->setURL("http://www.bonbanh.com/salon-oto/");
$crawler->setPageLimit(1);
//$crawler->addContentTypeReceiveRule("#text/html#");
//$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i");
$crawler->go();
?>