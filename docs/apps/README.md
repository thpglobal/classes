# Generic Applications
* The repository includes a group of PHP include files that can be included in your applications main index.php file -- the router.
* The router looks for an array called $generics that contains a list of the names of these generic scripts and includes them from the vendor subdirectory rather than from the applications own directory.
There are a number of pages that virtually all application will want to use, particularly for admins, and these can be automatically referenced with this git submodule.

# The apps
* /dump - list $_SESSION[“contents”] array
* /edit?table=xxx&id=y - generic record editor (create a new record if id=0
* /export - export $_SESSION[“contents”] array to an xlsx file
* /list?table=xxx - generic list of contents of specified table, with links to edit records if $can_edit
* /query - if $admin, accept and run any database query
* /upload - if $admin, upload an xlsx file and import it into $_SESSION[“contents”];
* /update – take the $_POST data and update a specified record in a table (or create a new one, if id=0. table and id must be includes as avariables in $_POST
