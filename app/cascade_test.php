<?php

$page=new \Thpglobal\Classes\Page;
$page->start("Cascade Test");
$filter=new \Thpglobal\Classes\Filter;
$filter->start($db);
$a=$filter->table("a");
if($a) $b=$filter->table("b","aid=$a");
$filter->end();
echo("<pre>".print_r($_COOKIE,TRUE)."</pre>");
$page->end();
