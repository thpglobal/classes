<?php
// GENERIC UPDATE
// This is called from various EDIT pages
if ($_COOKIE["debug"]) {
    echo("<html lang=en><head><meta charset='utf-8'></head><body><h1>Debug Update</h1>\n");
    echo("Post:<pre>".print_r($_POST, TRUE)."</pre>\n");
}

//sometimes we need zero as default update value, set this variable from the app page
$id = $_POST["id"];
if ($id == '') {
    $id = 0;
}

$table = $_POST["table"];

if ($table == '') {
    $table = $_SESSION["table"];
}

if ($table == '') {
    goback("Error: Table not set in update.");
}

$prefix = ($id > 0 ? "update" : "insert into");
$suffix = ($id > 0 ? " where id=:id" : "");
$params = array(':id' => $id);

$query = "$prefix $table set ";

// Note use of prepared statements to avoid issues with special characters
foreach ($_POST as $key => $value) {
    $value = trim($value);
    if (substr($key, -5) == "_Date") {
        $_SESSION["lastdate"] = $value; // use last date entered as default
    }
    if ($value == "on") {
        $value = 1;
    }
    if ($value == "") {
        $value = NULL;
    }
    if ($key != "id" && $key != "table") {
        $query .= "$key=:$key, ";
        $params[":$key"] = $value;
    }
}

$query = rtrim($query, ', ') . $suffix;

if ($_COOKIE["debug"]) {
    echo("<p>$query</p>\n");
}

$stmt = $db->prepare($query);
$stmt->execute($params);

$error = $stmt->errorInfo();
if (!empty($error[2])) {
    $reply = "Error: ".$error[2]." with ".$query;
} else {
    if ($id == 0) {
        $id = $db->lastInsertId();
    }
    $reply = "Success with $prefix $table record for ID: $id";
}

if ($_COOKIE["debug"]) {
    echo("<p>Reply $reply</p>\n");
    echo("<p>Query $query</p>\n");
    echo("<a href=".$_COOKIE["back"].">Continue...</a>\n");
} else {
    goback($reply);
}
