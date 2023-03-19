# Filter Class
* This class formats controls for creating filtered queries by setting values for various fields that are sticky in that they get stored in cookies
## nstantiate
Nearly all methods have a return value that is also copied into $_COOKIE[$name] for the “stickyness” of settings.
## Methods

* $filter= new Filter;
* $filter->start($db); // Starts the grid of filters - link it to the database $filter->end(); // This is really important as it closes the div. To create rows, you might want to “end” and “start” the filter.
##Methods that don’t access $db
* $int_value=$filter->range($name,$n1,$n2); returns selected number within a range
* $key_value=$filter->pairs($name, $pairs); // nicely formatted dropdown from an associative array, when changed it passes the select in $_GET[$name] and restarts the page (which puts it into $_COOKIE[$name] and returns the value). This is the mother ship function - table calls query which calls pairs.
* $key_value=$filter->toggle($name,$on_msg=’on’,$off_msg=’off’); returns ‘on’ or ‘off’
## Methods that query the database into the above
* $key_value=$filter->query($name,$query); dropdown based on any query
*$int_value=$filter->table($name,$where=””); select id,name from $name $where order by 2 - mirrors the function of our earlier dropdown function
