<?php
// Display a chart for whatever data is in the Contents array.
// Initial step simply sets up a line chart but then we will add more complexity

$page=new \Thpglobal\Classes\Page;

$types=array("line"=>"Line","bar"=>"Bar","radar"=>"Radar","scatter"=>"Scatter","pie"=>"Pie","doughnut"=>"Doughnut");

$page->start("Chart based on last query");
$contents=$_SESSION["contents"];
$nrows=sizeof($contents);
if(!$nrows) Die("No data.");

$filter=new \Thpglobal\Classes\Filter;
$filter->start();
$ctype=$filter->pairs("chart_type",$types);
$filter->end();
$ctitle=$contents[0][1];
for($i=1;$i<$nrows;$i++) {
	$x[]=$contents[$i][0];
	$y[]=$contents[$i][1];
}
echo("<p>Query: ".$_SESSION["query"]."</p>\n");
$chart=new \Thpglobal\Classes\Chart;
$chart->color='black'; 
$chart->background='white';
$chart->fill='#ff8787';
$chart->start($db, 'black');
//$chart->make(1,"Test","$type",$x,$y);
?>
<div class=pure-u-1-1><canvas id=chart1 width=600 height=300></canvas></div>
<script>
var data1 = {
<?php
echo("  labels : ".json_encode($x).",\n");
echo("  datasets : [{\n"); 
echo("	label: 'Chart',\n");
if( 'line' == $ctype ){
	echo("	fill: false,\n");
}else{
	echo("	fill: true,\n");
}
echo("	backgroundColor: '".$chart->fill."',\n");
echo("	data: ".json_encode($y)."\n	} \n], \n}; \n");
echo("var c1 = document.getElementById('chart1').getContext('2d');\n");
echo("var cc1 = new Chart(c1,{ type: '$ctype', data: data1");
if($ctype<>'radar') echo(", options: ChartOptions");
if($ctype=='radar') echo(", options: RadarOptions");
echo("} );\n");
echo("</script>\n");
$chart->end();
$page->end();
