<?php
/**
 * Created by PhpStorm.
 * User: GOLDENLOTUS
 * Date: 5/7/2016
 * Time: 4:03 PM
 */
function getStringBetween($str, $from, $to)
{
    $sub = substr($str, strpos($str, $from) + strlen($from), strlen($str));
    return substr($sub, 0, strpos($sub, $to));
}

function connect()
{
    $c = mysql_connect('127.0.0.1', 'root', '');
    mysql_select_db('demo');
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
//xem lai ham nay
function filterUser($table, $col1,$col2,$val1,$val2)
{
    $c = connect();
    $query = "SELECT $col1,$col2 FROM $table";
    mysql_query("SET NAMES 'UTF8'");
    $retval = mysql_query($query, $c);
    while ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        if (($row[$col1] == $val1)&&($row[$col2] == $val2)) {
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

function getDB()
{
    $c = connect();
    $query = "SELECT * FROM shop_link";
    mysql_query("SET NAMES 'UTF8'");
    $ret=mysql_query($query, $c);
    mysql_close($c);
    return $ret;
}
function searchShopId($contact)
{
    $c=connect();
    $query="SELECT id FROM shop_link WHERE name='$contact'";
    mysql_query("SET NAMES 'UTF8'");
    $retval = mysql_query($query, $c);
    if($retval!=null)
    {
        while ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
            mysql_close($c);
           return $row['id'];
        }
    }
    mysql_close($c);
    return 0;
}
function searchBrandId($brand)
{
    $c=connect();
    $query="SELECT id FROM brands WHERE name='$brand'";
    mysql_query("SET NAMES 'UTF8'");
    $retval = mysql_query($query, $c);
    if($retval!=null)
    {
        while ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
            mysql_close($c);
            return $row['id'];
        }
    }
    mysql_close($c);
    return 0;
}
function searchUserId($userName){
    $c=connect();
    $query="SELECT id FROM user WHERE username_canonical ='$userName'";
    mysql_query("SET NAMES 'UTF8'");
    $retval = mysql_query($query, $c);
    if($retval!=null)
    {
        while ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
            mysql_close($c);
            return $row['id'];
        }
    }
    mysql_close($c);
    return 0;
}