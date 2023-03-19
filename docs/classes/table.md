# Table Class
* This formats and outputs the results of database queries into tables.
* This class is designed to take a 2-D array of data and display it. It can do very basic things or very complex things. Those are separated here to minimize confusion.

## Basic Attributes
* Table::contents - a two-d array. $this->contents[$i][$j] is a cell in row $i column $j. contents[0] is a header row. It is public to your app.
&& Basic Methods
* Table::query($select_query) - run the query, filling in the field names in row 0, and all the records starting at row 1.
* Table::show($href) - display the query, linking the values of column 0. EG if $href=”edit?id=” and the value of contents[1][0] is 5, then you would see a hyperlink on the 5 that goes to “edit?id=5.” A copy of contents is saved to $_SESSION[“contents”] for handy next steps such as export.
* Table::header($array) assigns the array to contents[0].
* Table::row($array) assigns the array to contents[] (the next unassigned row).
* Table::smartquery($table,$where,$yearfilter); // loads data via foreign keys. If $yearfilter>”” and there is a xxx_Date field, it filters year(xxx_Date)=$yearfilter
* Table::thead($first_column) - Part of show() - Output the header starting in $first_column
## Mapping and Rowpans
* Mapping: You may have a number of sparse columns that you want to map against a specific column - like a database outer join. It is often much easier to let this class do the joins than to try and build a single massive and complex query.
* Rowspans: You may have multiple subvalues mapped to a column - for example, you may have indicators “Voter Participation” and “Meeting Participation” and within those you might disaggregate it into Women, Men, Total_Participants where you want Total to be computed by the class rather than in the database.
## Advanced Methods Prior to “show()”
* Table::newtab=TRUE; // Option to open links in a new tab. Default is FALSE
* Table::rowspan - an integer value for how many columns should have their rows merged, say when there is diaggregated data.
* Table::hidelink=FALSE; // Option to put href on next column
* Table::href=””; // Link text preceding the value in the 0 cell
* Table::dpoints=0; // Show how many decimal points?
* Table::extraheader=””; // Additional text added above default header, for multirow headers
* Table::infocol($assoc_array); // Popup info linked to each column header
* Table::inforow($assoc_array); // Popup info linked to first visible column
* Table::groups($assoc_array); // Add subheaders on column zero (invisible when set)
* Table::rowspan($n); // When the first $n columns match, just output that data once
* Table::sumcols($i1,$j1); // Sum last $n columns into a Total column - starting with cell $i $j
* Table::sumrows($i1,$j1); // Sum rows starting with cell $i $j
# Really advanced loading functions
* When indicators have disaggregated values, you need to basically pivot information into contents. For example, We use indicator tables that list a sequence of labels for each disaggregate. [need to add figure]
* Table::pivot($query,$position_of_rowspan,$num_of_disag_cols,$max_disag)
* Table::column($id_col, $dest_col, $assoc_array); // map it into column joined on tag
