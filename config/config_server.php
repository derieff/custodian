<?php
	date_default_timezone_set('Asia/Jakarta');

	$host = "10.20.1.180";
	// $username = "custodian";
	$username = 'root';
	$password = "tap123";
	// $databasename = "custodian";

	mysql_connect($host,$username,$password) or die ("Cannot Connect To Database");
	// mysql_select_db($databasename,$link);

?>
