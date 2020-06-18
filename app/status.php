<?php

require 'lib/db.php';

# The purpose of this health check is to verify connection to DB for liveness and readiness probe
echo "Healthy";

pg_close($db_connection);
