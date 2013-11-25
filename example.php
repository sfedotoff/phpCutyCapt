<?php
/**
 * Project:     phpCutyCapt
 * File:        example.php
 *
 * This class is free software for generating website screenshots
 * using CutyCapt(http://cutycapt.sourceforge.net) via X virtual
 * framebuffer (xvfb) on linux systems
 *
 * @link http://moneyseeker.ru/
 * @copyright 2013 Stanislav Fedotov
 * @author Stanislav Fedotov <me at moneyseeker dot ru>
 * @version 1.0
 */

require_once 'phpCutyCapt.php';

$directory = "tmpimages"; #Directory to save screenshots
$flashenable = true; #true - enable Flash and plugins; false - disable
$delay = 2; #Delay before taking screenshot
$goodcodes = array(200, 301, 302); #accepted HTTP-status array

//Random text generation

function gen_pass($m) {
    $m = intval($m);
    $pass = "";
    for ($i = 0; $i < $m; $i++) {
        $te = mt_rand(48, 122);
        if (($te > 57 && $te < 65) || ($te > 90 && $te < 97)) $te = $te - 9;
        $pass .= chr($te);
    }
    return $pass;
}

//Checking domain existance (requires nslookup command to exist on server)

function domain_exists($url) {
    $urlarr = parse_url($url);
    $checkcmd = "nslookup ".$urlarr['host'];
    exec($checkcmd, $output);
    $out = implode(" ", $output);
    if(strpos($out, "can't find") === false)
        return true;
    else
        return false;
}

//Checking domain to resolve

function check_resolve($url) {
    global $goodcodes;
    $headers = @get_headers($url);
    $error = 0;
    for($i=0; $i<count($goodcodes);$i++){
        if(strpos($headers[0], $goodcodes[$i]) === false)
            $error++;
    }
    if($error==count($goodcodes))
        return false;
    else
        return true;
}

//Preparing URL (just converting russian chars from query)

function prepare_url($url) {
    $array_rus = array("а", "б", "в", "г", "д", "е", "ё", "ж", "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ы", "ь", "э", "ю", "я",
                       "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж", "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ы", "Ь", "Э", "Ю", "Я");
    $array_encoded = array("%D0", "%B0", "%0A", "%D0", "%B1", "%0A", "%D0", "%B2", "%0A", "%D0", "%B3", "%0A", "%D0", "%B4", "%0A", "%D0", "%B5", "%0A", "%D1", "%91", "%0A", "%D0", "%B6", "%0A", "%D0", "%B7", "%0A", "%D0", "%B8", "%0A", "%D0", "%B9", "%0A", "%D0", "%BA", "%0A", "%D0", "%BB", "%0A", "%D0", "%BC", "%0A", "%D0", "%BD", "%0A", "%D0", "%BE", "%0A", "%D0", "%BF", "%0A", "%D1", "%80", "%0A", "%D1", "%81", "%0A", "%D1", "%82", "%0A", "%D1", "%83", "%0A", "%D1", "%84", "%0A", "%D1", "%85", "%0A", "%D1", "%86", "%0A", "%D1", "%87", "%0A", "%D1", "%88", "%0A", "%D1", "%89", "%0A", "%D1", "%8A", "%0A", "%D1", "%8B", "%0A", "%D1", "%8C", "%0A", "%D1", "%8D", "%0A", "%D1", "%8E", "%0A", "%D1", "%8F",
                           "%D0", "%90", "%0A", "%D0", "%91", "%0A", "%D0", "%92", "%0A", "%D0", "%93", "%0A", "%D0", "%94", "%0A", "%D0", "%95", "%0A", "%D0", "%81", "%0A", "%D0", "%96", "%0A", "%D0", "%97", "%0A", "%D0", "%98", "%0A", "%D0", "%99", "%0A", "%D0", "%9A", "%0A", "%D0", "%9B", "%0A", "%D0", "%9C", "%0A", "%D0", "%9D", "%0A", "%D0", "%9E", "%0A", "%D0", "%9F", "%0A", "%D0", "%A0", "%0A", "%D0", "%A1", "%0A", "%D0", "%A2", "%0A", "%D0", "%A3", "%0A", "%D0", "%A4", "%0A", "%D0", "%A5", "%0A", "%D0", "%A6", "%0A", "%D0", "%A7", "%0A", "%D0", "%A8", "%0A", "%D0", "%A9", "%0A", "%D0", "%AA", "%0A", "%D0", "%AB", "%0A", "%D0", "%AC", "%0A", "%D0", "%AD", "%0A", "%D0", "%AE", "%0A", "%D0", "%AF");
    $parts = parse_url($url);
    $dom = $parts['scheme']."://".$parts['host'];
    $path_prepared = str_replace("%2F", "/", urlencode($parts['path']));
    $query = (strlen($parts['query'])>0) ? "%3F".urlencode($parts['query']) : "";
    $prepared = $dom.$path_prepared.$query;
    return $prepared;
}

//Getting URL from query string

$screenurl = urldecode($_GET['website']);
$screenurl = prepare_url($screenurl);

//Checking URL

if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$screenurl) OR !domain_exists($screenurl) OR !check_resolve($screenurl)) {
    header('Content-type: image/jpeg');
    readfile("nosite.jpg");
    exit;
}

//Generating screenshot name

$screenname = gen_pass(32).".jpg";

//Capturing screenshot via CutyCapt and phpCutyCapt

$capture = new \phpCutyCapt\phpCutyCapt($screenurl, "tmpimages/".$screenname, 1280, $flashenable, $delay);
if($capture->screenshot()) {
    //I'm just echoing the path to the screenshot. But in real life you can do anything you need (for example, use a thumbnail creator and output an image it created)
    echo $directory."/".$screenname;
    //Cleaning capturer (deleting img we've got)
    $capture->clean();
}
