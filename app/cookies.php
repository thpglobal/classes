<?php
require_once(__DIR__."/../classes/thp_classes.php");
$page=new Page;
$page->start("Cookies");
echo("<pre>".print_r($_COOKIE,TRUE)."</pre>");
$page->end();
