<?php
$next_path=$_COOKIE["path"];
if($next_path="/index.php") $next_path="";
if(!$next_path) $next_path="/";
$checkendpoint = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=";
$checkurl= $checkendpoint.$_POST['idtoken'];
$v=file_get_contents($checkurl);
$vp=json_decode($v,true);
setcookie("name",$vp["name"],0,'/');
setcookie("user",$vp["email"],0,'/');
header("Location:".$next_path);
