<?php
// Generic Spreadsheet upload - forwarding to generic import
$into=$_GET["into"];
if($into=="") $into="/dump"; // Default to show $contents
$page=new \Thpglobal\Classes\Page;
$page->start("Spreadsheet Upload into $into");
echo("<p>You may only upload Excel files generated from this system.</p>\n");
echo("<form action=import enctype='multipart/form-data' method='post'>"); 
echo("<input name='userfile' type='file'>\n");
echo("<input class='pure-button pure-button-primary' type=submit value='Upload Excel Spreadsheet File'>\n");
echo("</form>\n");
$page->end();
