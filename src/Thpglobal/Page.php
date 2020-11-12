<?php
// START CLASS PAGE
namespace Thpglobal;
class Page {
	public $datatable = FALSE;
	public $addStickyHeader = TRUE;
	public $css=array("https://storage.googleapis.com/thp/thp.css"); // default used by all
	public $preh1=""; // used for dashboard colorbar etc
	public $time_start; // used to measure length for process
	public $links=array("print"=>"'javascript:window.print();'");
	public $hints=array("print"=>"Print this page");
	public $appendTitle='';
	public function debug($message,$values) {
		echo("<p>$message".":"); print_r($values); echo("</p>\n");
	}

	public function datatable(){
		$this->datatable=TRUE;
	}
	public function disableStickyHeader(){
		$this->addStickyHeader=FALSE;
	}

/* dynamic property setter/getter for this class */
	public function get($prop){
		if(isset($this->$prop)){
			return $this->$prop;
		}
		return NULL;
	}
	public function set($prop, $value){
		if(isset($this->$prop)){
			$this->$prop = $value;
		}
	}
	public function menu() { // new responsive version
		$menu=$_SESSION["menu"];
		if(isset($_SESSION["menu"]) and sizeof($menu)>0) { 
			echo("<div class='topnav hidden-print' id=myTopnav>\n");
			foreach($menu as $key=>$item){
				if(is_array($item) ){
					echo("<div class=dropdown>\n");
					echo("\t<button class=dropbtn>$key<i class='fa fa-caret-down'></i></button>\n"); 
					echo("\t<div class=dropdown-content>\n");
					foreach($item as $b=>$a) echo("\t\t<a href='$a'>$b</a>\n");
					echo("\t</div>\n</div>\n");
				} else echo("\t<a href='$item'>$key</a>\n");
			}
			echo('<a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myFunction()">&#9776;</a>');
			echo("\n</div>\n");
			echo("<script>\n");
			echo("function myFunction() {\n");
			echo('  var x = document.getElementById("myTopnav");'."\n");
			echo('  if (x.className === "topnav") { x.className += " responsive"; } else { x.className = "topnav"; }'."\n");
			echo("}\n</script>\n");
		}
	}
	
	public function start_light($title="THP",$lang="en") { // no menu, no icons, no datatable, no extras
		foreach($_GET as $key=>$value) $_SESSION[$key]=$value;
		$this->time_start=microtime(true);
		echo("<!DOCTYPE html>\n<html lang=$lang>\n<head>\n");
		echo("<meta name=viewport content='width=device-width, initial-scale=1'>\n");
		echo("<title>$title</title>\n");
		echo("<meta name='description' content='$title built on opensource github.com/thpglobal/thpclasses'/>\n");
		echo("<link rel='shortcut icon' href='/static/favicon.png'>\n");
		echo("<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/pure/1.0.0/pure-min.css'>\n");
		echo("<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/pure/1.0.0/grids-responsive.css'>\n");
		echo("<link rel='stylesheet' href='https://storage.googleapis.com/thp/thp.css'>\n");
		echo("<meta charset='utf-8'>\n");
        echo("</head>\n<body>\n");
        echo("<div class=container>\n");
		echo("<h1>$title</h1>\n");
	}

	public function start($title="THP",$lang="en"){
		$reply=$_COOKIE["reply"];
		setcookie("reply","",0,'/');
		$_SESSION["datatable"]=$this->datatable; // save for access by Table class
		foreach($_GET as $key=>$value) $_SESSION[$key]=$value;
		$this->time_start=microtime(true);
		echo("<!DOCTYPE html>\n<html lang=$lang>\n<head>\n");
		echo("<meta name=viewport content='width=device-width, initial-scale=1'>\n");
		echo("<title>$title</title>\n");
		echo("<meta name='description' content='$title built on opensource github.com/thpglobal/thpclasses'/>\n");
		echo("<link rel='shortcut icon' href='/static/favicon.png'>\n");
		if(file_exists($_SERVER['DOCUMENT_ROOT']."/static/purefa.css")) {
			echo("<link rel='stylesheet' href='/static/purefa.css'>\n");// avoid all CDNs for Bangladesh
		}else{
			echo("<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/pure/1.0.0/pure-min.css'>\n");
			echo("<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/pure/1.0.0/grids-responsive.css'>\n");
			echo("<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro&display=swap' rel='stylesheet'>");
			echo("<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>\n");
		}
		if(file_exists($_SERVER['DOCUMENT_ROOT']."/static/pure.thp.css")) 
			$this->css[0]="/static/pure.thp.css";
		if(!sizeof($this->css)) Die("</head><body>Error - there must be at least one $css entry</body></html>");
		foreach($this->css as $css) echo("<link rel='stylesheet' href='$css'>\n");
		if($this->datatable=="1"){ // Additional setup for using DataTables
?>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/css/jquery.dataTables.min.css'>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.slim.min.js"></script> 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#datatable tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" size=4 placeholder="'+title+'" />' );
    } );
 
    // DataTable
    var table = $('#datatable').DataTable( {
        "order": [[ 0, "desc" ]]
    });
 
    // Apply the search
    table.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );
} );
</script>
<?php
        }
        echo("<meta charset='utf-8'>\n");
        echo("</head>\n<body>\n");
		$this->menu();
        echo("<div class=container>\n");
		echo($this->preh1); //used for dashboard colorbar or whatever
        echo("<h1>$title <span class=hidden-print>");
        //include("../includes/translate.php");
		foreach($this->links as $key=>$link) {
            $hint=$this->hints[$key];
            echo("<a href=$link class='fa fa-$key' title='$hint'></a>\n");
        }
        echo($this->appendTitle."</span></h1>\n");
		if($reply){
			$color="green";
			if(substr($reply,0,5)=="Error") $color="red";
			echo("<p style='text-align:center;color:white;background-color:".$color."'>$reply</p>\n");
		}
		
		echo("<div id='google_translate_element' style='position:absolute; top:4em; right:1em;'></div> 
			<script type='text/javascript'> 
				function googleTranslateElementInit() { 
					new google.translate.TranslateElement({pageLanguage: '$lang'}, 'google_translate_element'); 
				} 
			</script> 
		<script type='text/javascript' src='//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit'></script>");
	
	
	}
	public function icon($type="edit",$link="/edit",$hint="Edit this record"){
		$this->links[$type]=$link;
		$this->hints[$type]=$hint;
	}
	public function toggle($name,$on_msg='On',$off_msg='Off'){
		$now=$_COOKIE[$name];
		if($now<>'off') $now='on'; // default is ON
		$then=($now=='on' ? 'off' : 'on');
		$this->appendTitle.="<a class='fa fa-toggle-$now' href='?$name=$then'></a> ";
		$this->appendTitle .= ($now=='on' ? $on_msg : $off_msg) ;
	}
	
	public function end($message=""){
		$time=microtime(true)-($this->time_start);
		echo("<p><i>$message Run time: $time</i></p>\n");
		echo("</div>\n");
        echo("</body></html>\n");
    }
}
// END CLASS PAGE
