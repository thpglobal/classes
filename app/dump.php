<?php
// to more easily debug Table
// we do it manually
$page=new \Thpglobal\Classes\Page;
$page->icon("download","export","Export recent contents");
$page->start("Dump Recent Contents");
$grid=new \Thpglobal\Classes\Table;
$grid->contents=$_SESSION["contents"];
$grid->thead(0);
for($i=1;$i<sizeof($grid->contents);$i++) {
    echo("<tr>");
    foreach($grid->contents[$i] as $cell) echo("<td>$cell</td>");
    echo("</tr>");
}
echo("</tbody></table>\n");
$page->end();
