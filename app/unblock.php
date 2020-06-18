<?php

# This file is there mostly for integration tests
# so that after integration tests, IP is still unblocked
require 'lib/db.php';

$current_ip = $_SERVER['REMOTE_ADDR'];

$query = "DELETE FROM blacklist WHERE ip_address = '".$current_ip."';";
$result = pg_query($db_connection, $query);

echo "Succesfully unblocked";

pg_close($db_connection);
