# Purpose

* To provide a lightweight, simple codebase for PHP/MySQL data entry, display and analysis.
* THP uses this primarily in Google App Engine 

# Structure

To create a web app using these classes you need to create two files in an /includes folder
1. thpsecurity.php -- any authentication, database connection and basic setup
2. menu.php -- an associative array of links for the main menu

You then need to add this repository using composer.

## Example file structure

```
index.php
includes/thpsecurity.php
includes/menu.php
static/
vendor/
app/index.php
app/{others}.php
app/{category}/index.php
app/{category}/{others}.php
app.yaml
composer.json
composer.lock
deploy
go
```

## Example index.php -- the router

PHP7+ typically sends every call to this file, which sets everything upand then includes other chunks of code.

```
<?php
require_once __DIR__ . '/vendor/autoload.php';

session_start();
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
$errors=$_COOKIE["errors"]??"off";
if($errors=="on") {error_reporting(E_ALL & ~E_NOTICE);} else {error_reporting(E_ERROR | E_PARSE);};

function getcookie($x) {
	if( array_key_exists($x,$_COOKIE)) return $_COOKIE[$x];
	return "";
}

require_once __DIR__.'/includes/thpsecurity.php';
require_once __DIR__.'/includes/menu.php';

$url=$_SERVER['REQUEST_URI'];
$path=parse_url($url, PHP_URL_PATH);
$generics=['/cookies', '/dump', '/edit', '/export', '/import', '/insert_table', '/list', '/logout',  '/query', '/update','/upload']; // standard routines defined in the classes

// modify to run locally with a local database without logging in!

if(in_array($path,$generics)){
	include("./vendor/thpglobal/classes/app$path.php");
}elseif($path=='/') {
	include("./app/index.php");
}else{	
	$success=include("./app$path.php");
	if(!$success) Header("Location:/?reply=Error+Command+$path+Not+Found");
}
```
