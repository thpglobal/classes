<?php
namespace Thpglobal\Classes;


class Chart{
	public $ncharts=0; // count
	public $color='white'; // default text color, defines regular page from dashboard dark page
	public $background='black'; // default for dashboard
	public $fill="rgba(0,100,0,0.3)"; // 50% transparent green
	public $width=3; // put 3 across unless changed
	public $db=NULL; // only used with query method
	public $options="scales:{xAxes:[{gridLines:{color:'yellow'}}],
	yAxes:[{ticks:{beginAtZero:true},gridLines:{color:'yellow'}}]}\n"; // default for black dashboard - auto changes to blue

	public function start($db=NULL, $color='white'){ // loads chart.js and sets some basic defaults
		$this->db=$db;
		$this->color=$color;
		$this->background=($color=='white' ? 'black' : 'white');
		if($color=='black') $this->options=str_replace('yellow','blue',$this->options);
		echo("<script src=https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js></script>\n");
		echo("<script>\n");
		echo("Chart.defaults.global.responsive = true;\n");
		echo("Chart.defaults.global.defaultColor = '{$this->color}';\n");
		echo("Chart.defaults.global.defaultFontColor = '{$this->color}';\n");
		echo("var ChartOptions = {".$this->options."};\n");
		echo("var RadarOptions = { scale: { ticks: { beginAtZero: true } } };\n"); // different for radar charts
		echo("</script>\n");
		echo("<div class=pure-g>\n"); // open a grid of sections.
	}

	public function end() { echo("</div>\n"); } // close the grid

	// This is the main working method that spits out the necessary javascript for a chart.
	public function query($n,$title,$query) { // run a query direct into a barchart
		if($this->db==NULL) Die("You forgot the Chart::start($db) method.");
		$pdo_stmt=$this->db->query($query);
		while($line=$pdo_stmt->fetch(3)){ $x[]=$line[0];$y[]=$line[1];}
		$this->make($n,$title,'bar',$x,$y);
	}

	public function make($n,$ctitle,$ctype,$x,$y){
		$width=$this->width;
		$title=str_replace("'","&apos;",$ctitle);
		echo("<div class='pure-u-1-1 pure-u-md-1-$width'><h3>$title</h3><canvas id=chart$n width=500 height=350></canvas></div>\n");
		echo("<script>\n");
		echo("var data$n = { \n");
		echo("  labels : ".json_encode($x).",\n");
		echo("  datasets : [{\n");
		echo("	label: '$title',\n");
		if( 'line' == $ctype ){
			echo("	fill: false,\n");
    	}else{
    		echo("	fill: true,\n");
    	}
		echo("	backgroundColor: ".json_encode($this->fill).",\n");
		echo("	data: ".json_encode($y)."\n	} \n], \n}; \n");
		echo("var c$n = document.getElementById('chart".$n."').getContext('2d');\n");
 		echo("var cc$n = new Chart(c$n,{ type: '$ctype', data: data$n");
			if($ctype<>'radar' and $ctype<>'pie') echo(", options: ChartOptions");
			if($ctype=='radar') echo(", options: RadarOptions");
			echo("} );\n");
 		echo("</script>\n");
	}
	// "show" was earlier used on various scripts and now just calls make
	// it autoincrements a count, and accepts an associative array of data instead of separate X Y arrays.
	public function show($title="Sample",$type="radar",$data=array("A"=>1,"B"=>2,"C"=>3)) {
		$this->ncharts++;
		foreach($data as $key=>$value) { $x[]=$key; $y[]=$value; };
		$this->make($this->ncharts,$title,$type,$x,$y);
	}
}
