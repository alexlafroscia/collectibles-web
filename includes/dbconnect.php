<?php

DEFINE('DB_USER', 'cs334l');
DEFINE('DB_PASSWORD', 'biglots');
DEFINE('DB_HOST', 'cs334dbhostname.educationvault.com');
DEFINE('DB_NAME', 'cs334db_l');

$dbc = @mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
        OR die ('Could not connect to MySQL: ' . mysqli_connect_error());