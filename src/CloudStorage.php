<?php 
namespace Thpglobal\Classes;
use Google\Cloud\Storage\StorageClient; 
Class CloudStorage extends StorageClient {
	function __construct() {
		parent::__construct();
		parent::registerStreamWrapper();
	}
	public function download($fullpath){
		$ext=strtolower(substr($fullpath,-4));
		$ct='binary/octet-stream'; // default for unknown type (downloads)
		$mode='attachment';
		$name=basename($fullpath);
		// Figure out the content type, as uploads set this incorrectly
		$cts=array(".pdf"=>"application/pdf","jpeg"=>"image/jpeg",".jpg"=>"image/jpg");
		$cts["xlsx"]="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
		$cts["docx"]="application/vnd.openxmlformats-officedocument.wordprocessingml.document";
		$cts["pptx"]="application/vnd.openxmlformats-officedocument.presentationml.presentation";
		$cts[".doc"]="application/msword";
		if(boolval($cts[$ext]??FALSE)){ $ct=$cts[$ext]; $mode='inline'; }
		header("Content-Type:".$ct);
		header("Content-Disposition:$mode; filename=$name");
		header("Content-Transfer-Encoding: binary");
		readfile($fullpath);
	}
}
