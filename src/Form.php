<?php
// CLASS FORM - EDIT A RECORD
namespace Thpglobal\Classes;

class Form {
	protected $db;
	private $div1="<div class='pure-control-group'>\n<label for=";
	public $data=array();
	public $minNumAll;
	public $maxNumAll;
	public $hidden=array("id");
	public $ignore=array();
	public $where=array(); // ability to add filters to dropdowns inside ->record
	private function debug($name,$item){
		if(boolval($_COOKIE["debug"] ?? FALSE)) {
			echo("<p>$name: "); print_r($item); echo("</p>\n");
		}
	}
	
	public function start($db=NULL,$action="/update"){
		$this->db=$db; // reference database connection
		echo("<form class='pure-form pure-form-aligned' method='post'");
		if($action>'') echo (" action='$action'");
		echo(">\n<Fieldset>\n");
	}
	public function end($submit="Save Data"){
		echo("\n\n<div class='pure-controls'>".
		'<button type="submit" class="pure-button pure-button-primary">'.$submit.'</button>'.
		"</div>\n</fieldset>\n</form>\n");
	}
	public function hidden($array) {
		$this->hidden=$array;
	}
	public function ignore($array){
		$this->ignore=$array;
	}
	public function data($array) { // these are the existing values
		$this->data=$array;
	}
	public function toggle($name) {
		echo($this->div1."'$name'>$name:</label>");
		echo('<input type=hidden name='.$name.' value=0>');
		echo('<label class=switch><input type=checkbox name='.$name);
		if($this->data[$name]>0) echo(" checked");
		echo ("><span class=slider></span></label></div>\n");
	}
	public function rename($name,$showname) {
		$value=$this->data[$name];
		if($value=='') $value=0;
		$label=ucwords($showname);
		if($min<>NULL) $label .= "$min to $max";
		echo($this->div1."'$name'>".ucwords($name).":</label>");
		echo("<input type=number name='$name' value='$value'");
		if($min<>NULL) echo(" min='$min'");
		if($max<>NULL) echo(" max='$max'");
		if($min<>NULL) echo("><span class=status></span");
		echo("></div>\n");
	}
	
	public function num($name,$min=NULL,$max=NULL){
		$value=$this->data[$name];
		if(isset($this->minNumAll)) $min=$this->minNumAll;
		if(isset($this->maxNumAll)) $max=$this->maxNumAll;
		if($value=='') $value=0;
		$label=ucwords($name);
		if($min<>NULL) $label .= "$min to $max";
		echo($this->div1."'$name'>".ucwords($name).":</label>");
		echo("<input type=number name='$name' value='$value'");
		if($min<>NULL) echo(" min='$min'");
		if($max<>NULL) echo(" max='$max'");
		if($min<>NULL) echo("><span class=status></span");
		echo("></div>\n");
	}
	public function text($name,$rename='',$minlength=0){
		$label=($rename>'' ? $rename : $name);
		echo($this->div1."'$name'>".ucwords($label).":</label>");
		echo("<input type=text name='$name' value='".$this->data[$name]."'");
		if($minlength>0) echo(' required><span class=status></span');
		echo("></div>\n");
	}
	public function date($name,$required=0){ // This restricts daterange to mindate/maxdate if set
		$preset=$this->data[$name]??"";
		echo($this->div1."'$name'>".ucwords($name).":</label>");
		echo("<input type=date name='$name' value='$preset'");
		if(isset($_COOKIE["mindate"])) echo(" min='".$_COOKIE["mindate"]."'");
		if(isset($_COOKIE["maxdate"])) echo(" max='".$_COOKIE["maxdate"]."'");
		if($required) echo (' required');
		echo("><span class=status></span></div>\n");
	}
	public function textarea($name,$rename='',$required=0){
		$label=($rename>'' ? $rename : $name);
		echo($this->div1."'$name'>".ucwords($label).":</label>");
		echo("<textarea name=$name rows=5 cols=60");
		if($required) echo(" REQUIRED");
		echo(">".$this->data[$name]."</textarea>\n");
		if($required) echo("<span class=status></span>");
		echo("</div>\n");
	}
	public function hide($name,$value){
		echo("<input type=hidden name='$name' value='$value'>\n");
	}
	public function pairs($name,$array,$required=0){
        $requiredAttr=($required) ? ' required ' : '';
        //HtML5 requires required value to be empty (not zero) for validation
        $requiredVal=($required) ? '' : 0;
        echo($this->div1."'$name'>".ucwords($name).":</label>");
        echo("<select name='$name' $requiredAttr>\n<option value='$requiredVal'>(Select)\n");
        foreach($array as $key=>$value){
            echo("<option value='$key'");
            if($key==$this->data[$name]) echo(" selected");
            echo(">$value\n");
        }
        echo("</select>");
        if($required){echo "<span class=status></span>";}
        echo("</div>\n");
    }
	public function query($name,$query,$required=0){
		$pdo_stmt=$this->db->query($query);
		if(!is_object($pdo_stmt)) Die("Fatal Error - bad query - $query \n");
		$this->pairs($name,$pdo_stmt->fetchAll(12),$required);
	}
	public function record($table,$id){ // goes inside start and end
		// Also can create a new record if id==0
		// First pull in the list of field meta data
		if($id=='') $id=0;
		$this->hide("table",$table);
		$pdo_stmt=$this->db->query("select * from $table where id='$id'");
		if(!is_object($pdo_stmt)) Die("Fatal Error - bad query - $query \n");
		$this->data = $pdo_stmt->fetch(2);
		
		foreach(range(0, $pdo_stmt->columnCount() - 1) as $column_index)
		{ $meta[$column_index] = $pdo_stmt->getColumnMeta($column_index);}
		
		$this->debug("Meta",$meta);
		foreach(range(0, $pdo_stmt->columnCount() - 1) as $column_index) {
			$name=$meta[$column_index]["name"];
			$type=$meta[$column_index]["native_type"];
			$value=$this->data[$name];
			if($name=="id"){
				$this->hide($name,$id);
			}elseif(isset($this->hidden[$name])){
				$this->hide($name,$this->hidden[$name]);
			}elseif($name=='User_Email'){
				$this->hide($name,strtolower($_SERVER["USER_EMAIL"]));
			}elseif(substr($name,-3)=="_ID"){
				$subtable=strtolower(substr($name,0,-3));
				$where=$this->where[$name]??'';
				echo("\n<!-- where $where -->\n");
				$this->query($name,"select id,name from $subtable $where order by 2");
			}elseif($type=="TINY") {
				$this->toggle($name);
			}elseif($type=="LONG") {
				$this->num($name);
			}elseif($type=="BLOB") {
				$this->textarea($name);
			}elseif($type=='DATE'){
				$this->date($name);
			}elseif(!in_array($name,$this->ignore)) {
				$this->text($name);
			}
		}
	}
} // END OF CLASS FORM
