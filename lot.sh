#!/bin/bash

cd datasets/
for i in `cat ../lot.txt`; do
    file=$(basename $i)
    wget -q -nd $i
    unzip -qq $file meta.xml
    mv meta.xml $file.xml
    $(grep $file ../datasets.csv)
    if [ $?  -eq 1 ]; then
        echo "\"Title\", \"http://feeder.idigbio.org/datasets/$file\", \"Description\", \"DWCA\", \"DWCA\", \"datasets/$file\""
    fi
done
