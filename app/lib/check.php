<?php

# This file performs a check whethever the user is blacklisted or not
if(count(get_included_files()) == 1) returnError("Direct access to file is not permitted", 401);

require 'db.php';

# Get current IP
$current_ip = $_SERVER['REMOTE_ADDR'];

# Form and execute a query, SERVER['REMOTE_ADDR'] is safe so no sanitizing is needed here
$query = "SELECT blocked_at FROM blacklist WHERE ip_address = '" . $current_ip . "';";
$result = pg_query($db_connection, $query);

$row = pg_fetch_row($result);

if (!empty($row)) {
    $error_message = "Your IP has been blocked at " . $row[0];

    # I do know that 444 means "Connection Closed Without Response" but
    # I thought it's better to have understanding what application just did
    returnError($error_message, 444);
    exit;
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
