<?php
require_once __DIR__.'/../vendor/autoload.php';
use Thpglobal\Page;
$page=new Page;
$page->start("Hello World");
$page->end();

