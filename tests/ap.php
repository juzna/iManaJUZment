<?php
require_once __DIR__ . '/bootstrap.php';


$ap = $what::find(1);

dump2("FIELD NAMES", AP::getFieldNames());
//dump2("FIELD def", AP::getFieldDefinitions());
dump2('item->toArray()', $ap->toArray(true));
dump2('Entity', $ap);


try {dump($ap->IPs->toArray());} catch(Exception $e) {}

