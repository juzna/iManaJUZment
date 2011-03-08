#!/bin/bash

rm -rf doc/
mkdir doc

clear
php 3rdParty/apigen/apigen.php -s app/ -l libs/ -d doc -t iManaJUZment

echo 'FINISHED'
