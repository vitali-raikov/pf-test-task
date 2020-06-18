<?php

require 'lib/db.php';
require 'lib/check.php';

# If we gotten this far, then user is not blacklisted and we can insert a record to DB
$query = "INSERT INTO blacklist (ip_address, path) VALUES ('".$current_ip."', '".$_SERVER['REQUEST_URI']."')";
$result = pg_query($db_connection, $query);

# As per requirements, we return here 444 just in case as well
http_response_code(444);
echo "Your IP was succesfully blacklisted";

# Send an email
mail("test@domain.com​","New IP Blocked", $current_ip);

# We close a DB connection because it's considered a good practice
pg_close($db_connection);
