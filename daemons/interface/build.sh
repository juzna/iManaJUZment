#!/bin/bash
DIR=`dirname $0`
T=/root/inst/thrift/compiler/cpp/thrift
T=thrift

rm -rf $DIR/gen-php

for i in $DIR/*.thrift; do
  $T --gen php:server=1,oop=1,namespace53=1 -o $DIR $i;
done
