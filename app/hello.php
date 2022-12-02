<?php
// Test app will go here
require_once("../classes/thp_classes.php");
$page=new Page;
$page->start("Hello World!");
if(!file_exists('../includes/menu.php')){
	?>
<p>To create a simple CRUD app using SQLITE, simply:</p>
<ul>
	<li>Rename includes/menu_demo.php to menu.php</li>
	<li>Come back to this page</li>
</ul>
<?php }
$page->end();
?>
