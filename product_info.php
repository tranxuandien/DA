<?php
set_time_limit(10000000);
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
            foreach ($html->find("div.tabbertab") as $e) {

                $thongso = strip_tags(getStringBetween($e->innertext,"<h2>Thông số cơ bản</h2>","<h3>Thông tin mô tả</h3>"));
                $xuatxu = getStringBetween($thongso, "Xuất xứ:", "Tình trạng:");
                $tinhtrang = getStringBetween($thongso, "Tình trạng:", "Dòng xe:");
                $dongxe = getStringBetween($thongso, "Dòng xe:", "Số Km đã đi:");
                $dadi = getStringBetween($thongso, "Số Km đã đi:", "Màu ngoại thất:");
                $ngoaithat = getStringBetween($thongso, "Màu ngoại thất:", "Tình trạng:");
//                $kind = getStringBetween($e->innertext, "title=\"", "\"");
//                $price = strip_tags(getStringBetween($e->innertext, "class=\"cb3\">", "</div>"));
//                $city = strip_tags(getStringBetween($e->innertext, "class=\"cb4\">", "</div>"));
//                $info = strip_tags(getStringBetween($e->innertext, "class=\"cb6_02\">", "</div>"));
//                $contact = strip_tags(getStringBetween($e->innertext, "class=\"cb7\">", "</div>"), "<br>");
//                $linkProduct=getStringBetween($e->innertext,"href=\"","\">");
                echo $xuatxu;
                echo $tinhtrang;
                echo $dongxe;
                echo $dadi;
//                if (filter("product_link", "link", $linkProduct)) {
//                    pushDB("product_link", "link", $linkProduct);
//                }
            }
        }
        $html->clear();
        unset($html);
        echo $lb;
        flush();
    }
}

//for ($i = 1; $i <10; $i++) {
    $crawler = new MyCrawler();
    $crawler->setURL("http://www.bonbanh.com/Xe-Hyundai-Grand_Starex-VGT-2013-271312.html");
    $crawler->setPageLimit(1);
    $crawler->go();
//    echo $i;
//}
?>