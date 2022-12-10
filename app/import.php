<?php
// Import an xlsx file into $contents based on ZipArchive
$into=$_COOKIE["into"];
if($into=="") $into="/dump";
$tmpname=$_FILES['userfile']['tmp_name'];
debug("Files:",$_FILES);
$name=$_FILES['userfile']['name'];
$dir=sys_get_temp_dir();
$dest=$dir.'/'.$name;
debug("Temp:",$tmpname);
debug("Dest:",$dest);
rename($tmpname,$dest); // Move the file from uploads to the regular temp area.
// extract everything to the temporary file system
$zip = new ZipArchive;
$zip->open($dest);
$zip->extractTo($dir);
debug("Unzipped xl folder",scandir($dir.'/xl/'));
// Open up shared strings & the first worksheet
$strings=array();
if(file_exists($dir . '/xl/sharedStrings.xml')) {
	$sxml=file_get_contents($dir . '/xl/sharedStrings.xml');
	$strings = simplexml_load_string($sxml);
}
debug("Shared strings:",$strings);
$xml=file_get_contents($dir . '/xl/worksheets/sheet1.xml');
debug("Sheet as XML",htmlentities($xml));
$sheet   = simplexml_load_string($xml);
// Parse the rows into the $contents array
$xlrows = $sheet->sheetData->row; // this is an iterative object
foreach ($xlrows as $xlrow) {
	$line=array(); // clear out the array
    foreach ($xlrow->c as $cell) {
		$t=(string)$cell['t'];
		$v=(string)$cell->v;
		$si = $strings->si[(int) $v];
		if($t=="s") $v=(string)$si->t;
		if($t=="inlineStr") $v=(string)$cell->is->t;
		$line[]=$v;
	}
	debug("Line",$line);
	$contents[]=$line;
}
$_SESSION["contents"]=$contents;
$reply="Success importing $nrows from $name";
if(!$_COOKIE["debug"]) header("Location:$into?reply=$reply");
echo("<p><a href=$into?reply=$reply>Click here for $into</a></p>");
