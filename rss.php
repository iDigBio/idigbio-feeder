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
        $dsstat = stat($dataset["File"]);
        $rssfeed .= '<item>';
        $rssfeed .= '<title>' . $dataset["Title"] . '</title>';
        $rssfeed .= '<id>' . $dataset["ID"] . '</id>';
        $rssfeed .= '<type>' . $dataset["Type"] . '</type>';
        $rssfeed .= '<recordtype>' . $dataset["Record Type"] . '</recordtype>';
        $rssfeed .= '<description>' . $dataset["Description"] . '</description>';
        $rssfeed .= '<link>' . $base . "/". $dataset["File"] . '</link>';
        $rssfeed .= '<ipt:eml>' . $base . "/". $dataset["EMLFile"] . '</ipt:eml>';
        $rssfeed .= '<pubDate>' . date("D, d M Y H:i:s O", $dsstat["mtime"]) . '</pubDate>';
        $rssfeed .= '</item>';
    }
 
    $rssfeed .= '</channel>';
    $rssfeed .= '</rss>';
 
    echo $rssfeed;
?>
