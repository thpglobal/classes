# Page Class

Create a page with the standard styles, menu, title and icon controls

## Methods

* icon($type=”edit”,$link=”/edit”,$hint=”Edit this record”) – add icons in addition to the default print - these are defined 
* consistent with FontAwesome icons, eg selecting type=”edit” will set the class of the icon to be “fa fa-edit”
* start($title=”THP”,$lang=”en”) – send out the HTML to start the page, including the header, menu and title.
* end($message=””) – send the HTML to end the page, including a footer message and the runtime.