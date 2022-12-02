<?php
require(__DIR__."/../classes/thp_classes.php"); // Load the classes
$page=new Page;
$page->icon("upload","upload","Import and excel spreadsheet");
$page->icon("download","export","Export data as an excel spreadsheet");
$page->start("Demo thp_classes");
$grid=new Table;
$grid->header(array("Able","Baker","Charlie","Delta"));
for($i=1;$i<100;$i++) $grid->row(array($i,$i+1,$i+2,$i+3));
$grid->show();
print_r($_SERVER);
$page->end();
