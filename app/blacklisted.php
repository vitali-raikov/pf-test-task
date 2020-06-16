<?php

# This file contains connection to DB
# Created initial DB structure if not present
# And checks if user in blacklist already and if so returns error
require 'config.php';

# If we gotten this far, then user is not blacklisted and we can insert a record to DB
$query = "INSERT INTO blacklist (ip_address) VALUES ('".$current_ip."')";
$result = pg_query($db_connection, $query);

echo "Your IP was succesfully blacklisted";

# We close a DB connection because it's considered a good practice
pg_close($db_connection);
