<?php

$page=new \Thpglobal\Classes\Page;
$page->start("Cookies");
echo("<pre>".print_r($_COOKIE,TRUE)."</pre>");
$page->end();
