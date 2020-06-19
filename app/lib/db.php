<?php

# This file performs connection and initial schema setup for DB
if(count(get_included_files()) == 1) returnError("Direct access to file is not permitted", 401);

# Initialize the DB connection
$db_connection = pg_connect("host=".$_SERVER['DB_HOST']." port=5432 dbname=".$_SERVER['DB_NAME']." user=".$_SERVER['DB_USERNAME']." password=".$_SERVER['DB_PASSWORD']);

if(!$db_connection){
    returnError("Problem connecting to database", 500);
}

# Create table if not exists
$query = "CREATE TABLE IF NOT EXISTS blacklist (
    id SERIAL PRIMARY KEY,
    path VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) UNIQUE NOT NULL,
    blocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
    );";

pg_query($db_connection, $query);
