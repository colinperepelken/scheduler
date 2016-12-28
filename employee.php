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
	/* VIEWING AND EDITING A CURRENT EMPLOYEE */

	$id = $_GET['id'];
	echo "<h2>Editing Employee ID: $id</h2>";
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
	
	$cmd = "update";

} else if (isset($_GET['cmd'])) {
	/* SAVE BUTTON HAS BEEN PRESSED */
	$cmd = $_GET['cmd'];
	if(!empty($_GET['firstname']) && !empty($_GET['lastname'])) {
		if ($cmd == "update") {
			echo "updating existing emp";
		} else if ($cmd == "add") {
			echo "adding new emp";
		}
	} else { // invalid! database requires first name and last name
		$message = "Invalid! Firstname and Lastname must not be empty.";
		echo "<script type='text/javascript'>alert('$message');</script>";
		
	}
	
	
	
} else {
	/* ADDING A NEW EMPLOYEE */
	echo "<h2>Add an Employee</h2>";
	$firstname = $lastname = $email = $username = $phone = "";
	$id = -1;
	
	$cmd = "add";
}

echo "<form method=\"get\" action=\"employee.php\">
		<table>
		<tr><td align =\"left\">First Name:</td><td align =\"left\"><input type=\"text\" name=\"firstname\" size=\"30\" value=\"$firstname\"></td></td>
		<tr><td  align =\"left\">Last Name:</td><td  align =\"left\"><input type=\"text\" name=\"lastname\" size=\"30\" value=\"$lastname\"></td></td>
		<tr><td align =\"left\">Email:</td><td align =\"left\"><input type=\"text\" name=\"email\" size=\"30\" value=\"$email\"></td></td>
		<tr><td align =\"left\">Site Username:</td><td align =\"left\"><input type=\"text\" name=\"wid\" size=\"30\" value=\"$username\"></td></td>
		<tr><td align =\"left\">Phone:</td><td align =\"left\"><input type=\"text\" name=\"phone\" size=\"10\" value=\"$phone\"></td></td>
		</table>
		<input type=\"hidden\" name=\"cmd\" value=\"$cmd\">
		<br><br>
		<input type=\"submit\" name=\"submit\" value=\"Save\" id=\"submit\" />
		</form>";
?>

<body>
</html>