<?php
require_once(__DIR__."/../classes/thp_classes.php");
$page=new Page;
$page->start("Cascade Test");
$filter=new Filter;
$filter->start($db);
$a=$filter->table("a");
if($a) $b=$filter->table("b","aid=$a");
$filter->end();
echo("<pre>".print_r($_COOKIE,TRUE)."</pre>");
$page->end();
