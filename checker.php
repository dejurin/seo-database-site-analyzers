#!/usr/bin/php
<?php

/*
*   Simple urls checker from list
*/

echo "Path for list: \n";

$path = trim(fgets(STDIN));

echo "Input find url: \n";

$url = trim(fgets(STDIN));

$root = substr($path, 0, -(strlen(strrchr($path, '/'))) + 1);

if (is_file($path)) {
    $raw = file_get_contents($path, true);
    $arr = explode("\n", $raw);

    foreach ($arr as $str) {
        $get = get_web_page(trim($str), $root);
        if ($get['http_code'] === 200) {
            echo $str.' OK ';
            if (strpos($get['content'], $url)) {
                echo "find \n";
                file_put_contents($root.'successful.txt', $str."\n", FILE_APPEND | LOCK_EX);
            } else {
                echo "no find \n";
            }
        } else {
            echo $str." BAD\n";
        }
    }
}

function get_web_page($url, $root)
{
    $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

    $options = array(
            CURLOPT_CUSTOMREQUEST => 'GET',        //set request type post or get
            CURLOPT_POST => false,        //set to GET
            CURLOPT_USERAGENT => $user_agent, //set user agent
            CURLOPT_COOKIEFILE => $root.'cookie.txt', //set cookie file
            CURLOPT_COOKIEJAR => $root.'cookie.txt', //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING => '',       // handle all encodings
            CURLOPT_AUTOREFERER => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT => 120,      // timeout on response
            CURLOPT_MAXREDIRS => 10,       // stop after 10 redirects
        );

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    $err = curl_errno($ch);
    $errmsg = curl_error($ch);
    $header = curl_getinfo($ch);
    curl_close($ch);

    $header['errno'] = $err;
    $header['errmsg'] = $errmsg;
    $header['content'] = $content;

    return $header;
}
