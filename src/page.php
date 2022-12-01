<?php
namespace thpglobal/classes;

class Page {
  public function start($title='Hello'){
?>  
<html lang="en">
	<head>
	<title><?php echo $title?></title>
	<link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
</head>
  <body>
	  <h1><?php echo $title?></title>
  </body>
</html>
<?php } // end of function ?>

