<?php
	error_reporting(-1); // report all PHP errors 
	ini_set('display_errors', 1);
	
	
	class MyDB extends SQLite3 {
		function __construct() {
			$this->open('kvliquor.db');
		}
	}

	$db = new MyDB();
	
	$result = $db->query('SELECT * FROM Employee');
	var_dump($result->fetchArray());
?>