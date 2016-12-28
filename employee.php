<?php
// CONNECT TO DATABASE
require 'vendor/autoload.php';
use App\SQLiteConnection;

$pdo = (new SQLiteConnection())->connect();
?>

<head>
<title>KVLiquor - Employee</title>
</head>
<body>

<?php

if(isset($_GET['id'])) {
	$id = $_GET['id'];
	// query db using id
	// prepare select statement
	$sql = "SELECT firstname, lastname, email, phone, username FROM Employee WHERE id = :id;";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([':id' => $id]);
	$employee = $stmt->fetchObject(); // only one employee should be returned
	$firstname = $employee->firstname;
	$lastname = $employee->lastname;
	$email = $employee->email;
	$username = $employee->username;
	$phone = $employee->phone;
	

	echo "<form method=\"get\" action=\"admin.php\">
			<table>
			<tr><td align =\"left\">First Name:</td><td align =\"left\"><input type=\"text\" name=\"firstname\" size=\"30\" value=\"$firstname\"></td></td>
			<tr><td  align =\"left\">Last Name:</td><td  align =\"left\"><input type=\"text\" name=\"lastname\" size=\"30\" value=\"$lastname\"></td></td>
			<tr><td align =\"left\">Email:</td><td align =\"left\"><input type=\"text\" name=\"email\" size=\"30\" value=\"$email\"></td></td>
			<tr><td align =\"left\">Site Username:</td><td align =\"left\"><input type=\"text\" name=\"wid\" size=\"30\" value=\"$username\"></td></td>
			<tr><td align =\"left\">Phone:</td><td align =\"left\"><input type=\"text\" name=\"phone\" size=\"10\" value=\"$phone\"></td></td>
			</table>
			<br><br>
			<input type=\"submit\" name=\"submit\" value=\"Save\" id=\"submit\" />
			</form>";
} else {
	echo "Error. No employee selected. Please go back.";
}
?>

<body>
</html>