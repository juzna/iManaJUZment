#!/bin/bash

for i in $(find app/ -name '*.php');
do
  if ! grep -q Copyright $i
  then
    php add-licence.php $i
  fi
done

