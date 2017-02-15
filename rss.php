<?php
$feedhandle = fopen("feed.csv","r");
$fh_headers = fgetcsv($feedhandle);
$fh_config = fgetcsv($feedhandle);
fclose($feedhandle);

$config = Array();
for($i = 0; $i < count($fh_headers); $i++) {
  $config[trim($fh_headers[$i])] = trim($fh_config[$i]);
}

$dshandle = fopen("datasets.csv","r");
$ds_headers = fgetcsv($dshandle);

$datasets = Array();
while($rv = fgetcsv($dshandle)){
  $ds_arr = Array();
  for($i = 0; $i < count($ds_headers); $i++) {
    $ds_arr[trim($ds_headers[$i])] = trim($rv[$i]);
  }
  $datasets[] = $ds_arr;
}

$base = join("/",array_slice(explode("/",$config["Link"]), 0, -1));

// read pubDates from a file that is maintained separately
$pubdates = array();
$pubdateshandle = fopen("pubdates.csv","r");
while (($line = fgetcsv($pubdateshandle, $enclosure = '"')) !== FALSE ) 
  { 
    $pubdates[$line[0]] = $line[1];
  }
fclose($pubdateshandle);

header("Content-Type: text/xml; charset=UTF-8");

$rssfeed = '<?xml version="1.0" encoding="UTF-8"?>';
$rssfeed .= '<rss  xmlns:ipt="http://ipt.gbif.org/" version="2.0">';
$rssfeed .= '<channel>';
$rssfeed .= '<title>' . $config["Title"] . '</title>';
$rssfeed .= '<link>' . $config["Link"] . '</link>';
$rssfeed .= '<description>' . $config["Description"] . '</description>';
$rssfeed .= '<language>en-us</language>';
 
foreach($datasets as $dataset)
  {
    $rssfeed .= '<item>';
    $rssfeed .= '<title>' . $dataset["Title"] . '</title>';
    $rssfeed .= '<id>' . $dataset["ID"] . '</id>';
    $rssfeed .= '<type>' . $dataset["Type"] . '</type>';
    $rssfeed .= '<recordtype>' . $dataset["Record Type"] . '</recordtype>';
    $rssfeed .= '<description>' . $dataset["Description"] . '</description>';

    // Allow *this* feed to link to dataset and metadata (.eml) files outside *this* webserver 
    // when the locations are specified as http(s) location (e.g. starts with "http")
    if (substr($dataset["File"], 0, 4 ) === "http") {
      $rssfeed .= '<link>' . $dataset["File"] . '</link>';
    } else {
      $rssfeed .= '<link>' . $base . "/". $dataset["File"] . '</link>';
    }
    if (substr($dataset["EMLFile"], 0, 4 ) === "http") {
      $rssfeed .= '<emllink>' . $dataset["EMLFile"] . '</emllink>';
    } else {
      $rssfeed .= '<emllink>' . $base . "/". $dataset["EMLFile"] . '</emllink>';
    }

    // For http file links, we check to see if the external pubdates.csv has a timestamp value.
    // For local files and http file links that are not in pubdates.csv, use "stat" to get file modified datetime.
    // For http file links that are not in the .csv, this will lead to a PHP warning (and the "zero" timestamp appearing in the feed).
    if (array_key_exists($dataset["ID"], $pubdates))
      {
	$pubdate = $pubdates[$dataset["ID"]];
      } else 
      {
	$dsstat = stat($dataset["File"]);
	$pubdate = date("D, d M Y H:i:s O", $dsstat["mtime"]);
      }
    $rssfeed .= '<pubDate>' . $pubdate  . '</pubDate>';
    $rssfeed .= '</item>';
  }

$rssfeed .= '</channel>';
$rssfeed .= '</rss>';
echo $rssfeed;
?>
