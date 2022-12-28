<?php
namespace Thpglobal\Classes;

// CLASS FILTER - dropdowns that - on change - restart the page and set $_COOKIE["name"];
class Filter {
	protected $db;
	public $width=4; // Filter denomiator, eg normally 1/4 of screen
	public $showOffLabel=false;
	
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
    public function start($db=NULL){
        echo("<div class=pure-g>\n");
		$this->db=$db;
    }
    public function end(){
        echo("</div>\n");
    }
	private function now($name) { // common first steps for all filter - returns default value
		$now=$_COOKIE[$name] ?? 0;
		echo "\n<form class='pure-form pure-u-1 pure-u-md-1-".$this->width."'>\n" .
		"<!-- $name now=$now -->\n" .
		"<div class='form-group'><label for='$name'>".ucfirst($name).":&nbsp;</label>" ;
		return $now;
	}

	public function range($name,$n1=1, $n2=4){
		for($i=$n1;$i<=$n2;$i++) $array[$i]=$i;
		return $this->pairs($name,$array,''); // eliminate All from options
	}

	public function date($name){
		$now=$this->now($name);
		echo("<input type=date name=$name value='$now' onchange=this.form.submit();></div></form>");
		return $now;
	}

	public function toggle($name,$on_msg='On',$off_msg='Off'){
		$now=$this->now($name);
		if($now<>'off') $now='on';
		$then=($now=='on' ? 'off' : 'on');
		echo("<a class='fa fa-3x fa-toggle-$now' href='?$name=$then'></a>");
		echo( ($now=='on' ? $on_msg : $off_msg)."</div></form>\n");
		return $now;
	}
	/* switch version of the toggle, shows both on/off labels */
	public function switchToggle($name,$on_msg='On',$off_msg='Off'){
		$now=$_COOKIE[$name] ?? 'off';
		if($now<>'off') $now='on';
		$then=($now=='on' ? 'off' : 'on');
		echo("<div class='pure-u-1 pure-u-md-1-4'>$name: ". ($this->showOffLabel ? $off_msg : '') .
			"<a class='fa fa-3x fa-toggle-$now' href='?$name=$then'></a>");
		echo($on_msg."</div>");
		return $now;
	}
	public function warn($msg='Error.'){
		echo("<div class='pure-u-1 pure-u-md-1-4' style='background-color:red !important; color:white !important;'>$msg</div>\n");
	}
	public function query($name,$query){
		if($this->db==NULL) Die("You forgot to pass $db in the start method.");
		return $this->pairs($name, $this->db->query($query)->fetchAll(12) );
	}
	public function table($name,$where=''){
		$where_clause=($where=='' ? "" : "where $where");
		return $this->query($name,"select id,name from $name $where_clause order by 2");
	}
	public function pairs($name,$array,$all='(All)'){
		$now=$this->now($name); // do first steps
		$selected=FALSE; // nothing selected so far
		echo "<select id='$name' name=$name onchange=this.form.submit(); >\n";
		if($all>'') echo("<option value=0>$all\n");
		foreach($array as $key=>$value) { // default to first if required
			echo("<option value=$key");
			if($key==$now) {$selected=TRUE; echo(" SELECTED");}
			echo(">$value\n");
		}
		echo("</select></div></form>\n");
		return $now;
	}
}
