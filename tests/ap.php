<?php
require_once __DIR__ . '/bootstrap.php';

$ap = $what::find(5);

$p = new ReflectionProperty('AP', 'Tags');
$p->setAccessible(true);
dump($p->getValue($ap));exit;

dump($ap->Tags->count());

var_dump($ap->getTagList());
if(isset($_GET['tag'])) {
  echo "\n-----\n";
  $ap->addTag($_GET['tag']);
  $em->flush();
  
  dump($ap->Tags->count());  
  var_dump($ap->getTagList());
}

exit;


$ap->delete();
$em->flush();
exit;

$ap = new AP;
$ap->name = 'pepek';
$ap->mode = 'route';
$ap->IP = '1.2.3.4';
$ap->persist();

//dump2("FIELD NAMES", AP::getFieldNames());
//dump2("FIELD def", AP::getFieldDefinitions());

//$ap->pepa('ahoj');
//AP::pepa('co je'); 
dump2('item->toArray()', $ap->toArray(true));
//dump2('Entity', $ap);



echo "NAME: $ap->name\n";
$ap->name .= 'x';
echo "----- changed namen\n";
$em->flush();
echo "----- flushed\n";
exit;


