# idigbio-feeder

**This is the PRODUCTION branch of idigbio-feeder that includes references to the production datasets served from feeder.idigbio.org**

## overview

Quick and Dirty PHP RSS Feed Generator

rss.php uses as input a number of config files in order to generate an RSS 2.0 xml feed. rss.php can be used to generate a static file (by redirecting outuput to a file) or more commonly by dropping onto a webserver that is able to run php scripts.

A typical use of this feed generator is to maintain a list of published datasets (Darwin Core Archives) and when they were last updated.


## usage

```
$ php rss.php | xmllint --format - > myrssfeed.xml
PHP Warning:  stat(): stat failed for http://example.com/datasets/dwca-test2.zip in /home/dstoner/git/idigbio-feeder/rss.php on line 75
$ head myrssfeed.xml
<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:ipt="http://ipt.gbif.org/" version="2.0">
  <channel>
    <title>iDigBio Test Feed</title>
    <link>http://localhost/rss.php</link>
    <description>Test RSS Feed for iDigBio CSV Datasets.</description>
    <language>en-us</language>
    <item>
      <title>Test occurrence dataset</title>
      <id>http://localhost/datasets/test.csv</id>
      ...
```


## feed.csv

feed.csv contains configuration for the feed itself.

### Sample feed.csv:

```
Title, Link, Description
"iDigBio Test Feed", "http://localhost/rss.php", "Test RSS Feed for iDigBio CSV Datasets."
```

## datasets.csv

datasets.csv contains configuration for the list of datasets that will be mentioned in the feed.

### Important Fields

#### Title

Short title for the dataset.

#### ID

Globally Unique Identifier (GUID) for this dataset.  Best practice is to use a UUID but other identifiers such as the URL of the destination file are also sufficient.

#### Description

A longer human-readable description of the contents of the target dataset file.

#### File

A local relative path or http URL to the dataset file.

#### EMLFile

A local relative path or http URL to the dataset metadata file.


### Sample datasets.csv:

```
"Title","ID","Description","Type","Record Type","File","EMLFile"
"Test occurrence dataset","http://localhost/datasets/test.csv","A Test .csv Dataset","CSV", "occurrence","datasets/test.csv",""
"Test multimedia dataset","http://localhost/datasets/test2.csv","A Test .csv Dataset","CSV", "multimedia","datasets/test2.csv",""
"Test zipped dataset","http://localhost/datasets/test.csv.zip","A Test csv.zip Dataset","CSV-ZIP","occurrence","datasets/test.csv.zip",""
"Test archive","http://localhost/datasets/dwca-test.zip","A Test DwC-A Dataset","DWCA","DWCA","datasets/dwca-test.zip","eml/dwca-test.eml"
"Test archive relative path","THIS_COULD_BE_A_GUID_INSTEAD_datasets/dwca-test.zip","A Test DwC-A Dataset","DWCA","DWCA","datasets/dwca-test.zip","eml/dwca-test.eml"
"Test archive at remote http location","aa57903f-620a-416d-a669-75824d6b4b7b","Test DwC-A on remote webserver with a pubdate in pubdates.csv","DWCA","DWCA","http://example.com/datasets/dwca-test.zip","http://example.com/eml/dwca-test.eml"
"Test archive2 at remote http location","c588dad2-754c-452a-8ba3-7ca5e7f2fcb2","Test DwC-A on remote webserver with no entry in pubdates.csv","DWCA","DWCA","http://example.com/datasets/dwca-test2.zip","http://example.com/eml/dwca-test2.eml
```

## pubdates.csv

This is an optional file that is helpful when using datasets that are stored remotely (http) rather than on *this* webserver.

The pubDate field that appears in the finished rss feed is either determined dynamically from the local filesystem last modified date (via a "stat" operation), or by reading date values from pubdates.csv. If the "File" appears to be remote (starts with 'http') but the date is not specified in pubdates.csv, rss.php will issue a warning and will use the "zero date" as the pubDate.

The "ID" column is used to link back to the particular dataset in datasets.csv.

The "pubDate" column contains a timestamp in HTTP-date format and will be used as-is in the finished RSS feed.  At this time there is no relationship between the Last-Modified returned by the remote webserver and the value entered in pubDate column, unless a human keeps them in sync.

The Last-Modified header returned by the webserver is frequently a good value to use for pubDate.
```
$ curl -s -I http://feeder.idigbio.org/datasets/e4b33221-1e2c-405c-ac02-a39d93f9a69b.tsv | egrep '^Last-Modified:'
Last-Modified: Wed, 08 Feb 2017 15:35:18 GMT
```

### Sample pubdates.csv:

```
"ID","pubDate"
"http://example.com/datasets/dwca-test.zip","Wed, 08 Feb 2017 12:34:56 -0500"
"aa57903f-620a-416d-a669-75824d6b4b7b","Wed, 08 Feb 2017 12:34:56 -0500"
```
