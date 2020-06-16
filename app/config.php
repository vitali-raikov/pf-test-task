<?php

if(count(get_included_files()) == 1) returnError("Direct access to file is not permitted", 401);

# Initialize the DB connection
$db_connection = pg_connect("host=".$_SERVER['DB_HOST']." port=5432 dbname=".$_SERVER['DB_NAME']." user=".$_SERVER['DB_USERNAME']." password=".$_SERVER['DB_PASSWORD']);

if(!$db_connection){
    returnError(print_r($_ENV), 500);
}

# Create schema if not exists
$query = "CREATE TABLE IF NOT EXISTS blacklist (
    id SERIAL PRIMARY KEY,
    ip_address VARCHAR(45) UNIQUE NOT NULL,
    blocked_at DATE DEFAULT CURRENT_DATE NOT NULL
    );";

pg_query($db_connection, $query);

# Get current IP
$current_ip = $_SERVER['REMOTE_ADDR'];

# Form and execute a query, SERVER['REMOTE_ADDR'] is safe so no sanitizing is needed here
$query = "SELECT blocked_at FROM blacklist WHERE ip_address = '" . $current_ip . "';";
$result = pg_query($db_connection, $query);

$row = pg_fetch_row($result);

if (!empty($row)) {
    $error_message = "Your IP has been blocked at " . $row[0];
    returnError($error_message, 401);
}

# Custom function to return error as JSON because it looks nice
function returnError($message, $error_code) {
    header('Content-Type: application/json');
    echo json_encode(array(
        'error' => array(
            'msg' => $message,
            'code' => $error_code
        )
    ));

    http_response_code($error_code);
    exit;
}
