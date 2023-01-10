<?php
namespace Thpglobal\Classes;
///
class Table { // These are public for now but may eventually be private with setters
	protected $db; // database connection
	public $contents=array(array()); // main 2d array
	public $hidelink=FALSE; // Option to put href on next column
	public $rowspan=0; // If>0, then start rowspan with column this many columns
	public $backmap=array(); // Create backpointers to the array after pivot
	public $rowspans=array(); // >1 means start a rowspan
	public $extra=array(); // extra headers
	public $classes=array(); // Used for special coloring of rows
	public $ntext=1; // number of columns to not be formatted
	public $groups=array(); // headers
	public $showGroupID=TRUE; //Print ID and name in the column (rowspanned) header. Set False for not displaying ID.
	public $extraheader=""; // Optional Extra headers string
	public $infocol=array(); // Definitions of column headers
	public $inforow=array(); // Definitions of rows
	public $errormsg=""; // used in map_query
    public $infoMatchWithID=FALSE; //Definitions array index will match against concat(id, '_', name) for more unique combination
	public $href="";
	public $dpoints=0; // Decimal points
	public $rowspan2=0; // Note fields need a rowspan mid-row for studies etc.

	public function start($db){
		$this->db=$db;
	}
	
	/* dynamic property setter/getter for this class */
	public function get($prop){
    	if(isset($this->$prop)) return $this->$prop;
        return NULL;
	}
	public function set($prop, $value){
    	if(isset($this->$prop)) $this->$prop = $value;
	}
    
    public function info($definition){ // return a string function with info symbol and title
	    if($definition) return "<span title='$definition' class='fa fa-info-circle'></span>";
		return '';
    }
	public function rowspan($n=2){ // set number of columns to include in rowspan
		$this->rowspan=$n;
	}
	
	/* It just returns the result of a query,
	 * Donesn't updates contents variable
	 * Helpful to get the result of any queries
	 */
	public function fetchRecords($query){
		if(empty($query)){return false;}
		$pdo_stmt=$this->db->query($query);
		if(!is_object($pdo_stmt)) echo("<div> Error: bad query: $query.</div>");
		$data=$pdo_stmt->fetch(2);
		return $data;
    }
		
	public function query($query){ // load results of a query into the grid
		$pdo_stmt=$this->db->query($query);
		foreach(range(0, $pdo_stmt->columnCount() - 1) as $column_index)
			{
				$meta = $pdo_stmt->getColumnMeta($column_index);
				$this->contents[0][$column_index]=$meta["name"];
			}
		while($row = $pdo_stmt->fetch(3)) $this->contents[]=$row;
	}
	public function column($id_col,$dest_col,$array){
		if(sizeof($this->backmap)==0) $this->backmap($id_col);
		foreach( (array) $array as $key=>$value){
			$j=substr($key,-1); // Get the digit after the_
			$id=substr($key,0,-2); // Get the indicator name before the _
			if(array_key_exists($id,$this->backmap)) {
				$this->contents[$this->backmap[$id]+$j-1][$dest_col]=$value;
				if($_COOKIE["debug"]) echo("<p>$j $id $value</p>\n");
			}
		}
	}
	public function map($id_col,$dest_col,$array){
		if(sizeof($this->backmap)==0) $this->backmap($id_col);
		foreach( (array) $array as $key=>$value) {
			$map=str_replace("_",".",$key);
			if(array_key_exists($map,$this->backmap))
				$this->contents[$this->backmap[$map]][$dest_col]=$value;
		}
	}
	public function map_query($id_col,$dest_col,$query) {
		// This will map multiple columns directly from the array
		if(sizeof($this->backmap)==0) $this->backmap($id_col);
		$pdo_stmt=$this->db->query($query);
		$nrows=sizeof($this->contents);
		while($row = $pdo_stmt->fetch(\PDO::FETCH_NUM)) {
			$n=sizeof($row);
			$i=$this->backmap[$row[0]??0]??0; // into which row do we plant this?
			if($i>0 and $i<$nrows) {
				for($j=1;$j<$n;$j++) $this->contents[$i][$j+$dest_col-1]=$row[$j];
			}else{$this->errormsg.="<br>Map_query error:".print_r($row,TRUE);}
		}
	}
				
	public function loadrows($result) { // load from the output of a pdo query
		while($row=$result->fetch(3)) $this->row($row);
	}
	public function dump() {
		print_r($this->contents);
	}
	public function record($table,$id){ // display one record horizontally
		$this->contents[0]=array("Field","Value");
		$pdo_stmt=$this->db->query("select * from $table where id='$id'");
		if(!is_object($pdo_stmt)) Die("</div>Fatal Error: bad query in Table: $query.");
		$data=$pdo_stmt->fetch(2);
		foreach( (array) $data as $key=>$value) {
			$row[0]=$key; $row[1]=$value;
			$this->contents[]=$row;
		}
	}
    public function header($row) {
        $this->contents[0]=$row;
    }
	public function backmap($id_col) {
		//Identify the first row of a rowspan set of rows
		$nrows=sizeof($this->contents); // how many rows total?
		$last="";
		for($i=1;$i<$nrows;$i++) {
			$id=$this->contents[$i][$id_col];
			if($id<>$last) {
				$last=$id;
				$this->backmap[$id]=$i;
			}
		}
	}
    public function row($row){ // append array to contents
        $this->contents[]=$row;
//		debug("Row",$row);
//		debug("Size",sizeof($this->contents));
    }
	public function ntext($n=1){ // set the number of text columns
		$this->ntext=$n;
	}
    public function groups($row,$showGroupID=TRUE) {
        $this->groups=$row;
        $this->showGroupID=$showGroupID;
    }
    public function inforow($array,$infoMatchWithID=FALSE) {
        $this->inforow=$array;
        if($infoMatchWithID){ $this->infoMatchWithID=TRUE; }
    }
    public function infocol($array,$infoMatchWithID=FALSE) {
        $this->infocol=$array;
        if($infoMatchWithID){ $this->infoMatchWithID=TRUE; }
    }
    
// SUM UP THE $contents from column $col1 onwards (counting from zero)
	public function totals($col1=2,$row1=1){ // basically an alias
		$this->sumrows($col1);
	}
	public function sumrows($col1=2,$row1=1){
		$nrows=sizeof($this->contents);
		$ncols=sizeof($this->contents[0]);
		for($j=0;$j<$col1;$j++) $sums[$j]='';
		$sums[$col1-1]="TOTALS:";
		for($j=$col1;$j<$ncols;$j++){
			$sums[$j]=0;
			for($i=$row1;$i<$nrows;$i++) {
				$v=$this->contents[$i][$j]??'';
				if(is_numeric($v)) $sums[$j] += $v;
			}
		}
	    $this->contents[]=$sums;
    }
	public function sumcols($col1=2,$row1=1){ // sum the last columns starting with $n to a new Total column, startin with row 1
		$nrows=sizeof($this->contents);
		$ncols=sizeof($this->contents[0]);
		$this->contents[0][$ncols]="Total";
		for($i=$row1;$i<$nrows;$i++){
			$this->contents[$i][$ncols]=0;
			for($j=$col1;$j<$ncols;$j++) {
				$v=$this->contents[$i][$j]??'';
				if(is_numeric($v)) $this->contents[$i][$ncols] += $v;
			}
		}
	}
	
    ### replace totals with dash for non-numeric columnns
    ### call this method after calling totals or sumrows
	public function replaceNonNumericSums($ntextN,$replaceStr=' — '){
		$lastLine=array_pop($this->contents);
		$lastLine[1]=count($this->contents)-1;
		for($i=2;$i<$ntextN;$i++){
   			$lastLine[$i]=$replaceStr;
		}
		$this->contents[]=$lastLine;
	}
    
	// Link any foreign keys to their dependent table name field
	public function smartquery($table,$where="",$yearfilter=""){ // option to limit a date to a year
		$yearclause='';
		$whereclause='';
		$from=" from $table a";
		$alias=97; // ascii for lowercase a
		$pdo_stmt=$this->db->query("select * from $table limit 0"); // we need the names of the fields
		$query="select ";
		foreach(range(0, $pdo_stmt->columnCount() - 1) as $column_index) {
			$name=$pdo_stmt->getColumnMeta($column_index)["name"];
			if($yearfilter>"" and substr($name,-5)=="_Date") $yearclause=" year($name)='$yearfilter' ";
			if(substr($name,-3)=="_ID") {
				$alias++; // go to the next lowercase letter
				$from .=" left outer join ".strtolower(substr($name,0,-3))." ".chr($alias)." on a.$name=".chr($alias).".id ";
				$query .= chr($alias).".name as ".substr($name,0,-3).", ";
			}else{
				$query .= "a.$name, ";
			}
		}
		if($where>"" or $yearclause>"") $whereclause=" where ";
		if($where>"") $whereclause.=$where;
		if($where>"" and $yearclause>"") $yearclause=" and $yearclause";
		$query=substr($query,0,-2).$from.$whereclause.$yearclause." order by 1 desc limit 1500";
		$this->query($query);
	}

	// Replaces the need for several lines in any page using an indicator table - defaults for Africa
	public function indicators($table="af_indicator",$where="Source_ID=2",$start_row=1) { // set up standard disaggregated indicators
		// load them into contents, rowspan, inforow, classes arrays
		$query="select * from $table ".($where>"" ? "where" : "")." $where order by tag";
		$this->rowspan=array(); // Empty the array
		$pdo_stmt=$this->db->query($query);
		$i=$start_row;
		while($line=$pdo_stmt->fetch()){ // run through all lines in the query
			$rs=$line["rowspan"]; $id=$line["tag"];
			if(!$rs) $rs=1; // default if zero or null
			$this->rowspan[$id]=$rs;
			if($line["SR"]) $this->classes[$id]="sr";
			$row=array(substr($id,0,1),$id,$line["name"]);
			$this->inforow[$id]=$line["Definition"];
			$this->backmap[$id]=$i; // start of disaggregated indicator set
			for($k=1;$k<=$rs;$k++) {
				$row[3]=$line["L".$k];
				$this->contents[$i]=$row;
				$i++;
			}
		}
	}
	// Pivot data into the table after column j=$ni, for k=$nc groups of m=$nd columns
	public function pivot($query,$ni,$nc,$nd,$nsums=0){
		// $ni indicates the position of the rowspan field, typically column 3 counting from zero
		// $nsums adds fields beyond rowspan before the pivot used for summing (eg, 3)
		// the math here is really complicated as we parse one single row into rowspan rows,
		// and then go back and sum it up
		// the "pointer" to a disaggregate is $ni+$nsums+($nd*$j)+$i
		// it now also fills the backmap array
		if(!($result=$this->db->query($query))) Die($query);
		while($row=$result->fetch(3)){ // fold each row into rowspan rows
			$n=$row[$ni]??0; // rowspan
			for($i=1;$i<=$n;$i++){
				$line=array(); // first, empty it
				for($j=0;$j<$ni;$j++) $line[$j]=$row[$j]; // set up the pre-fold columns
				for($j=0;$j<$nc;$j++) $line[$ni+$j]=$row[$ni+$nsums+$i+($nd*$j)]; // number of disaggregated columns
				$this->row($line);
//				debug("Line-a",$line);
			}
			$sump=$row[$ni+1]??0; // Do we sum anything for this indicator?
			if($nsums and (($sump>0) or ($sump<0))){ // do we add summing rows?
				$label=$row[$ni+3]; // L8 is the label for the sum
				if($label=="") $label="Total # participants";
				for($j=0;$j<$ni;$j++) $line[$j]=$row[$j];
				$line[$ni]=$label;
				for($j=1;$j<$nc;$j++) { // loop through columns to be summed
					$participants=0;
					if($sump>0) { // normal sum
						for($i=1;$i<=$sump;$i++) $participants += $row[$ni+$nsums+($nd*$j)+$i];
					}elseif($sump==-2) {
						$participants=$row[$ni+($nd*$j)+4]-$row[$ni+($nd*$j)+5];
					}
					$line[]=$participants; // append to line
				}
				$this->row($line); // append to grid
//				debug("Line-b",$line);

				$sumw=$row[$ni+2]??0;
				if($sumw) { // sum up number of workshop for SumW>0
					for($j=0;$j<$ni;$j++) $line[$j]=$row[$j];
					$line[$ni]="Total # workshops";
					for($j=1;$j<$nc;$j++) { // loop through columns to be summed
						$workshops=0;
						for($i=$sumw;$i<=$n;$i++) $workshops += $row[$ni+$nsums+($nd*$j)+$i];
						$line[]=$workshops;
					}
					$this->row($line); // append to grid
//					debug("Line-c",$line);
				}
			}
		}
		$this->rowspan($ni-1);
	}
	// Turn columns into input fields in a range of columns
	public function edit_cols($id_col,$v_col,$n_col){
		$lastid=""; // last id rowspan
		for($i=1;$i<sizeof($this->contents);$i++){
			$id=$this->contents[$i][$id_col];
			if($id<>$lastid){
				$j=1;
				$lastid=$id;
			}
			for($k=0;$k<$n_col;$k++){
				$v=$this->contents[$i][$v_col+$k];
				$field="V{$j}-{$k}-".str_replace(".","_",$id);
				$this->contents[$i][$v_col+$k]="<input name='$field' size=5 value='$v'>";
			}
			$j++;
		}
	}
	
	public function thead($jstart=1){
		$row=$this->contents[0];
		$ncols=sizeof($row)??0;
		
		$nclasses=sizeof($this->classes);
		$striped=($nclasses>0 ? "" : "pure-table-striped");
		$tid=($_SESSION["datatable"] ? "id='datatable'" : "");
		$sticky=($_SESSION["datatable"] ? "" : "style='position: sticky; top: -1px;'");
		echo("<table $tid class='pure-table $striped pure-table-bordered'>\n<thead>\n");
		if(strlen($this->extraheader)>0) echo($this->extraheader);
		for($j=$jstart;$j<$ncols;$j++){
            $infoIndex=($this->infoMatchWithID) ? $row[$j-1].'_'.$row[$j] : $row[$j];
			if( isset($this->infocol[$infoIndex]) ){$infoc=$this->info($this->infocol[$infoIndex]);}else{$infoc='';}
			echo("<th $sticky>".str_replace("_"," ",$row[$j])."$infoc</th>");
		}
		echo("</tr>\n</thead>\n<tbody>\n");
		return $ncols;
	}

	// refactor rowspans to be part of the class
	public function create_rowspans($j1=0){
		$first="";
		$nrows=sizeof($this->contents)??0;
		for($i=1;$i<$nrows;$i++){
			$iden=$this->contents[$i][$j1];
			$this->rowspans[$i]=0;
			if($iden<>$first) {
				$first=$iden;
				$firstid=$i;
				$this->rowspans[$i]=1;
			} else {
				$this->rowspans[$firstid]++;
			}
		}		
	}
	// this outputs non-fancy cells
	public function putcell($i,$j=1){
		$cell=$this->contents[$i][$j]??'';
		if ( is_numeric($cell) and ($j>=($this->ntext)) ) {
			$v=$cell;
			$dp=(strpos($v,'.') ? $this->dpoints : 0);
			echo("<td>".number_format($v,$dp)."</td>");
		}else{
			echo("<td>$cell</td>");
		}
	}

	public function putgroup($group){
		$groupname=$this->groups[$group]??'';
		$width=sizeof($this->contents[0]);
		if($groupname) echo("<tr><th colspan=$width>$group. $groupname</th></tr>");
	}
	// all the magic happens in the first cell of the row
	public function firstcell($i,$j){
		$cell=$this->contents[$i][$j];
		// does his cell start a rowspan?
		$rs=$this->rowspans[$i]??0;
		$rss=($rs>1 ? " rowspan=$rs" : ""); // rowspan string

		// does this cell include a link?
		$link=($this->href ? "<a href=$this->href" : "");
		if($link) {
			$jlink=($this->hidelink ? $j-1 : $j);
			$link.=$this->contents[$i][$jlink].">";
		}
		$closelink=($link ? "</a>" : "");
		
		// Does the row include an info icon?
		$info="";
		$icon=$this->inforow[$cell] ?? "";
		$info=($icon ? $this->info($icon) : "");
		// ok, put them all together
		echo("<td$rss>$link$info$cell$closelink</td>");
		return $rss; // pass this to use it on subsequent cells
	}
// is there a special class (eg, color) for row i 
	public function getclass($i,$j1){
		$ntag=($this->hidelink ? $j1-1 : $j1);
		$tag=$this->contents[$i][$ntag]??''; // if there is an id here, this is it
		$class=$this->classes[$tag]??''; // is there a special class definition for this row?
		if($class>'') $class=" class=$class";
		if($_COOKIE["debug"]) echo("<tr>$i $ntag $tag $class</tr>\n");
		return $class; 
	}

	public function putrows($j1,$j2){
		// determine whether to consider group headers
		$ngroups=sizeof($this->groups);
		$previous_group=0;
		$nrows=sizeof($this->contents)??0;
		$ncols=sizeof($this->contents[0])??0;
		for($i=1;$i<$nrows;$i++) {
			// possible paint this row a different color?
			$class=$this->getclass($i,$j1);
			echo("<tr$class>");
			// Decide if a group header appears
			if($ngroups) {
				$group=$this->contents[$i][0];
				if($group<>$previous_group) {
					$previous_group=$group;
					$this->putgroup($group);
				}
			}
			// determine if we are starting a rowspan
			$rs=$this->rowspans[$i]??0;
			// if normal line or start of rowspan
			if(!$this->rowspan || $rs) {
				$rss=$this->firstcell($i,$j1); // this is the magic
				// output other rowspan columns
				if($rs) {
					for($j=$j1+1;$j<$j2;$j++) {
						echo("<td $rss>".$this->contents[$i][$j]."</td>");
					}
				}
			}
			// Then put the rest (non rs sets j2 to j1+1
			for($j=$j2;$j<$ncols;$j++){$this->putcell($i,$j);
			}
			echo("</tr>\n");
		}
	}

	// on datatables, add earchable footer fiels
	public function searchfooter($j1,$ncols) {
		echo("<tfoot><tr>");
		for($j=$j1; $j<$ncols; $j++) echo("<th>".$this->contents[0][$j]."</th>");
			echo("</tr></tfoot>\n");
	}

// SHOW THE TABLE - Including the id column on hrefs, but do skip the groups column
// this handles some really complex options:
//	* it might have group headers defined in column 0
//  * it might have a link defined by href
//  * it might hide that link or not
	public function show($href=''){ // experimental version
		// Set parameters appropriate to various options
		$_SESSION["contents"]=$this->contents; // put it first for easy debug!
		$ngroups=sizeof($this->groups); // Option to group rows with subheaders
		// j1 indicates which is the first column displayed
		$j1=($ngroups ? 1 : 0); // Do we skip over a group colum?
		$this->href=$href;
		if($_COOKIE["debug"]) echo("<p>Classes ".print_r($this->classes,TRUE)."</p>\n");
		
		$j1=(($this->href && $this->hidelink) ? $j1+1 : $j1);
		// we either output start right after j1 or after the rowspan cells
		$j2=$j1+($this->rowspan ? $this->rowspan : 1);
		if($_COOKIE["debug"])echo("<p>Hide $this->hidelink H $href J $j1 $j2 NG $ngroups</p>\n");
		$ncols=$this->thead($j1);
		if($this->rowspan) $this->create_rowspans($j1);
		$this->putrows($j1,$j2);
		echo "</tbody>\n";
		if($_SESSION["datatable"]) $this->searchfooter($j1,$ncols);
		echo "</table>";
	}
}
