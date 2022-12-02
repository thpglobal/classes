# Generic applications
Nearly every app based on thp_classes requires a basic set of generic pages for basic database functions. These include:
* demo - a basic landing page to make certain things are working
* list?table=x - list the contents of a table x with links to edit records if $can_edit==TRUE.
* edit?table=x&id=n - edit record n of table x.
* upload?into=x - upload a spreadsheet as a temporary file and pass it along to import and then a chosen task
* import?into=x - read the passed 
* dump - simply list the $_SESSION["contents"] table;
