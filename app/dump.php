<?php
require(__DIR__."/../classes/thp_classes.php"); // Load the classes
$page=new \Thpglobal\Classes\Page;
$page->icon("download","export","Export recent contents");
$page->start("Dump Recent Contents");
$grid=new \Thpglobal\Classes\Table;
$grid->contents=$_SESSION["contents"];
$grid->show();
$page->end();
?>
