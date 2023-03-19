# Form Class
* This class formats controls for creating forms for creating or editing database records.
##Public Attributes
* Form::db - link to PDO object created in includes/thpsecurity.php
* Form::data - assoc. array of initial values for inputs in the form
* Form::minNumAll - string - optional minumum integer value for all ->num inputs
* Form::maxNumAll - string - optional minumum integer value for all ->num inputs
* Form::hidden - assoc. array $name=>$value for hidden fields
* Form::where - assoc array $name=>$string for an optional where clause inside ->record for xxx_ID foreign key fields

## Basic Methods
*$form=new Form;
* $form->start($db, $action=/update); // Set up an aligned form, method=POST,
* $form->hidden($array); // assoc array of preset fields to hide
* $form->record($table,$id); // edit this record
* $form->end($submit_button_message=”Save Data”);

## Advanced Methods
Note: either the current or default values are in $_SESSION[$name]. Validation is by HTML5 instead of a javascript plugin.
* ->data($array); // load initial data into the form
* ->num($name,$min,$max); // Optional numeric limits
* ->rename($name,$showname); // like num but with a different display name
* ->date($name,$min,$max); // Optional limits on pop-up calendar
* ->text($name);
* ->textarea($name);
* ->pairs($name,$assoc_array,$required=0); // simple dropdown based on an array - if $required than 0 not included
* ->query($name,$query that results in pairs,$required);


