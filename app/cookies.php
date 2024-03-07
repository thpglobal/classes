<?php

$page=new \Thpglobal\Classes\Page;
$page->start("Cookies");
echo("<h2>Constants</h1>\n");
echo("<pre>".get_defined_constants(TRUE)."</pre>\n");
echo("<pre>".print_r($_COOKIE,TRUE)."</pre>");
$page->end();
