<?php
// SMART GENERIC LIST - OOP Version - Show the individual records from any report
require(__DIR__."/../classes/thp_classes.php"); // Load the classes
$table=$_GET["table"];
setcookie("back","/list?table=$table");
$page=new Page;
if($can_edit) $page->icon("plus-circle","/edit?table=$table&id=0","Add new record");
if($table=='') Die("No table set");
$page->start($table);
$grid=new Table;
$grid->start($db);
$grid->smartquery($table,$_SESSION["where"]);
$grid->show("/edit?table={$table}&id=");
$page->end();
