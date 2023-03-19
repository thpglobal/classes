# Chart Class
* This class formats data into simple charts
## Attributes
* Chart::width - the number of columns in a grid of charts
* Chart::fill - the color for filling bars etc… default 50% transparent green
* Chart::color - the font color - default is white, and defaults the background to black. If set to black, then background is set to white
* Chart::options change our defaults - defaults to x and y ticks start at 0 and are yellow, except for “radar” type chart, which remove settings for x and y axes and forces the radial scale to start at 0
## Methods
* Chart::start($db, $color=’$white’) - loads the chart.js package and sets global defaults, and opens a div grid container.
* Chart::make($n, $title, $type, $xarray, $yarray) - creates a <canvas id=chart{$n}> and a new chart that points to it.
* Chart::show($title,$type,$data) - increments an $n counter, and divides and associative array $data into a $label(or $x) array and a $yarray - then passes on to make()
* Chart::query($title,$type,$query) - like show, but first runs the query into an associative $key=>$value array for X,Y
* Chart::end() – close the div grid container
