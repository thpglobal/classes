<?php
// This creates a very simple xlsx file based on the precisely square $contents array
// 2018-11 revised to use internal ZipArchive assuming it works in GAE/PHP7
// This deals only with files in /tmp to be consistent with GAE

$contents=$_SESSION["contents"];
if(sizeof($contents)==0) header("Location:/?reply=Error:+Nothing+to+export.");
$fname=$_COOKIE["fname"];
if($fname=="") $fname=date('Y-m-d')."_Export";
$fname=$fname.".xlsx";

// Convert the contents array into two xml files - one for numerics and one for strings
$strings=array();
$nstrings=0;
$nunique=0;
$sheet1b="";
$ncols=sizeof($contents[0]);
$nrows=sizeof($contents);
for($i=0;$i<$nrows;$i++){
	$sheet1b .= '<row r="'.($i+1).'">';
	for($j=0;$j<$ncols;$j++) {
		$value=$contents[$i][$j]??'';
		$col_name=($j<26 ? chr(65+$j) : chr(64+intdiv($j,26)).chr(65+($j % 26)) );
		if(!is_numeric($value)) {
			$value=htmlspecialchars($value);
			if(boolval($strings[$value]??FALSE)) { $nstrings++; $value=$strings[$value];}
			else{$key=$value; $value=$nunique; $strings[$key]=$value; $nunique++; $nstrings++;}
			$sheet1b .= '<c r="'.$col_name.($i+1).'" s="1" t="s"><v>'.$value.'</v></c>';
		}else{
			$sheet1b .= '<c r="'.$col_name.($i+1).'" s="1"><v>'.$value.'</v></c>';
		}
	}
	$sheet1b .= '</row>';
}
// Create the $sharedstrings file
$sharedstrings='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
.'<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"';
if($nunique>0) $sharedstrings .= ' count="'.$nstrings.'" uniqueCount="'.$nunique.'">';
foreach($strings as $key=>$value) $sharedstrings .='<si><t>'.$key.'</t></si>';
$sharedstrings .= '</sst>';

// set up the rest of the 9 file contents

$rels='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
.'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';
$contenttypes='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
.'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default ContentType="application/xml" Extension="xml"/><Default ContentType="application/vnd.openxmlformats-package.relationships+xml" Extension="rels"/><Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml" PartName="/xl/worksheets/sheet1.xml"/><Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml" PartName="/xl/sharedStrings.xml"/><Override ContentType="application/vnd.openxmlformats-officedocument.drawing+xml" PartName="/xl/drawings/drawing1.xml"/><Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml" PartName="/xl/styles.xml"/><Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml" PartName="/xl/workbook.xml"/></Types>';
$drawing='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
.'<xdr:wsDr xmlns:xdr="http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:c="http://schemas.openxmlformats.org/drawingml/2006/chart" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:dgm="http://schemas.openxmlformats.org/drawingml/2006/diagram"/>';

$sheet1a='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
.'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:mx="http://schemas.microsoft.com/office/mac/excel/2008/main" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:mv="urn:schemas-microsoft-com:mac:vml" xmlns:x14="http://schemas.microsoft.com/office/spreadsheetml/2009/9/main" xmlns:x14ac="http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac" xmlns:xm="http://schemas.microsoft.com/office/excel/2006/main">'
.'<sheetViews><sheetView workbookViewId="0"/></sheetViews>'
.'<sheetFormatPr customHeight="1" defaultColWidth="14.43" defaultRowHeight="15.75"/>'
.'<sheetData>';
$sheet1c='</sheetData><drawing r:id="rId1"/></worksheet>';
$sheet1=$sheet1a.$sheet1b.$sheet1c;

$sheet1rels='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
.'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing" Target="../drawings/drawing1.xml"/></Relationships>';
$style='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
.'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:x14ac="http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006"><fonts count="2"><font><sz val="10.0"/><color rgb="FF000000"/><name val="Arial"/></font><font/></fonts><fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="lightGray"/></fill></fills><borders count="1"><border/></borders><cellStyleXfs count="1"><xf borderId="0" fillId="0" fontId="0" numFmtId="0" applyAlignment="1" applyFont="1"/></cellStyleXfs><cellXfs count="2"><xf borderId="0" fillId="0" fontId="0" numFmtId="0" xfId="0" applyAlignment="1" applyFont="1"><alignment readingOrder="0" shrinkToFit="0" vertical="bottom" wrapText="0"/></xf><xf borderId="0" fillId="0" fontId="1" numFmtId="0" xfId="0" applyAlignment="1" applyFont="1"><alignment readingOrder="0"/></xf></cellXfs><cellStyles count="1"><cellStyle xfId="0" name="Normal" builtinId="0"/></cellStyles><dxfs count="0"/></styleSheet>';
$workbook='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
.'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:mx="http://schemas.microsoft.com/office/mac/excel/2008/main" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:mv="urn:schemas-microsoft-com:mac:vml" xmlns:x14="http://schemas.microsoft.com/office/spreadsheetml/2009/9/main" xmlns:x14ac="http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac" xmlns:xm="http://schemas.microsoft.com/office/excel/2006/main"><workbookPr/><sheets><sheet state="visible" name="Sheet1" sheetId="1" r:id="rId3"/></sheets><definedNames/><calcPr/></workbook>';
$workbookrels='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n"
.'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>';

$dir = sys_get_temp_dir();
$tmp = tempnam($dir, $fname);


$zip=new ZipArchive; 
$zip->open($tmp,ZipArchive::OVERWRITE);
$zip->addFromString("xl/drawings/drawing1.xml", $drawing );
$zip->addFromString("xl/worksheets/sheet1.xml", $sheet1 );
$zip->addFromString("xl/worksheets/_rels/sheet1.xml.rels", $sheet1rels );
$zip->addFromString("xl/sharedStrings.xml", $sharedstrings );
$zip->addFromString("xl/styles.xml", $style );
$zip->addFromString("xl/workbook.xml" ,$workbook );
$zip->addFromString("xl/_rels/workbook.xml.rels", $workbookrels );
$zip->addFromString("_rels/.rels", $rels);
$zip->addFromString("[Content_Types].xml" , $contenttypes );
$zip->close();

file_get_contents($tmp); // download the file contents to the browser
header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Cache-Control: no-store, no-cache");
header('Content-Disposition: attachment; filename="'.$fname.'"');
copy($tmp,'php://output');
