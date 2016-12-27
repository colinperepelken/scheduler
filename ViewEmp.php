<?php
require 'vendor/autoload.php';
use App\SQLiteConnection;

$pdo = (new SQLiteConnection())->connect();
if( $pdo != null)
	echo 'Successfully connected to database.';
else
	echo 'ERROR: Could not connect to the database.';
		
$result = $pdo->query('SELECT * FROM Employee');
foreach($result as $row) {
	echo $row[1];
}

?>