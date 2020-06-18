<?php

require 'lib/db.php';
require 'lib/check.php';

# We close a DB connection because it's considered a good practice and we don't need it here
pg_close($db_connection);

# We check if get parameter is set
if(isset($_GET['n'])) {
    # We fetch the query parameter and convert it to integer (in order to do math operations below)
    $query_parameter = intval($_GET['n']);

    # We perform the complex math here and return the result
    $result = $query_parameter * $query_parameter;
    echo $result;
    exit;
} else {
    # Set the status code and return the error as JSON because it looks nice
    returnError("Query parameter 'n' is not passed", 400);
}
