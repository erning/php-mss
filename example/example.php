<?php
header("content-type: text/plain; charset=utf-8");

function load_dict($mss, $filename) {
    $fp = fopen($filename, "r");
    while (!feof($fp)) {
        $line = trim(fgets($fp));
        $pos = strpos($line, "#");
        if ($pos !== false) {
            $line = strstr($line, 0, $pos);
        }
        if (!$line) {
            continue;
        }
        $line = explode(":", $line, 2);
        if (count($line) == 2) {
            mss_add($mss, trim($line[0]), trim($line[1]));
        } else {
            mss_add($mss, trim($line[0]));
        }
    }
}


$mss = mss_create("example");
$timestamp = mss_timestamp($mss);
$is_ready = mss_is_ready($mss);
if ($is_ready) {
    $stat = stat("example.dic");
    if ($stat['mtime'] > $timestamp) {
        mss_destroy($mss);
        $mss = mss_create("example");
        $timestamp = mss_timestamp($mss);
        $is_ready = mss_is_ready($mss);
    }
}
if (!$is_ready) {
    echo "Load dict\n";
    load_dict($mss, "example.dic");
}

$text = file_get_contents("example.txt");

//
//
echo "mss_creation: " . date("Y-m-d H:i:s", $timestamp) . "\n";

//
//
echo "mms_match(): original text\n";
$ret = mss_match($mss, $text);
echo "    ", ($ret ? "matched" : "not matched"), "\n";
echo "\n";

//
//
echo "mms_search() array:\n";
$ret = mss_search($mss, $text);
echo "    ", "count: ", count($ret), "\n";
echo "\n";

//
//
echo "mms_search() callback: \n";
$count = 0;
$ret = mss_search($mss, $text, function($kw, $idx, $type, $ext) {
    $ext[0]++;
    for ($i = strlen($kw) - 1; $i >= 0; $i--) {
        $ext[1][$idx + $i] = '*';
    }
}, array(&$count, &$text));
echo "    ", "count: ", $count, "\n";
echo "\n";

//
//
echo "mms_match(): modified text\n";
$ret = mss_match($mss, $text);
echo "    ", ($ret ? "matched" : "not matched"), "\n";
echo "\n";

