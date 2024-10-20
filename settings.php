<?php
	$host = "feenix-mariadb.swin.edu.au";
	$user = "s105236884"; // your user name
	$pwd = "270500"; // your password (date of birth ddmmyy unless changed)
	$sql_db = "s105236884_db"; // your database

	// Create connection
    $conn = new mysqli($host, $user, $pwd, $sql_db);

    // Check connection
    if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
?>