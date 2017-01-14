<!DOCTYPE html>
<html>
<head>
<title>KVLiquor - Email</title>
<link rel='stylesheet' type='text/css' href='style/kvliquor.css'/>


<?php
	/* CONNECT TO DATABASE */
	require 'vendor/autoload.php';
	use App\SQLiteConnection;

	$pdo = (new SQLiteConnection())->connect();

	date_default_timezone_set('America/Los_Angeles'); // set default time zone to PST

	/* FUNCTIONS */

	/**
	 * Get all employees
	 */
	function getEmployees() {
		global $pdo;
		$sql = "SELECT id, firstname, lastname, email FROM Employee WHERE employed = 'true' ORDER BY firstname ASC";
		$stmt = $pdo->query($sql);
		$employees = [];
		while ($employee = $stmt->fetch(\PDO::FETCH_ASSOC)) {
			$employees[] = [
				'id' => $employee['id'],
				'firstname' => $employee['firstname'],
				'lastname' => $employee['lastname'],
				'email' => $employee['email']
			];
		}
		return $employees; // return list off employees
	}

	function alert($message) {
		echo "<script type='text/javascript'>alert('$message');</script>";
	}

?>
</head>

<body>
<div id = "header">
<img src="images/logo.jpg" style="float:left">

<div id="links">

<table id="top"><tr>
<td><a href = "admin.php?year=<?php echo date("Y"); ?>&month=<?php echo date("m"); ?>&day=<?php echo date("d"); ?>"> Schedule a Shift </a></td>
<td><a href = "report.php"> Generate Report </a></td>
<td><a href = "email.php"> Generate Emails </a></td>
<td><a href = "admin.php?showemp=1&year=<?php echo date("Y"); ?>&month=<?php echo date("m"); ?>&day=<?php echo date("d"); ?>"> Employees </a></td>
<td><a href = "settings.php"> Settings </a></td></tr></table>
</div>
</div>
<div id="settings">
<h3>Email Schedule</h3>
<p>Email schedules to employees. Includes start day. Does NOT include finish day.</p>
<form method="get" action="email.php">
<table>
<tbody>
<tr><th>Start Date</th><th>Finish Date</th></tr>
<tr><td><input type="date" name="start" value="<?php if(isset($start_date)){echo $start_date;} ?>">
	</td><td><input type="date" name="finish" value="<?php if(isset($finish_date)){echo $finish_date;} ?>"></td></tr>
</tbody>
</table>
<input type="submit" name="submit" value="Generate Emails" id="submit" />
</form>
<?php
	if(isset($_GET['submit'])) {

		$start_date = $_GET['start'];
		$finish_date = $_GET['finish'];

		foreach(getEmployees() as $employee) { // for each employee display the shifts they are working in the date range
			$id = $employee['id'];
			$firstname = $employee['firstname'];
			$lastname = $employee['lastname'];
			$email = $employee['email'];

			if(empty($email)) {
				$email = "Email not set!";
			}

			$sql = "SELECT start_date, finish_date FROM Shift WHERE eid = :eid;";
			$stmt = $pdo->prepare($sql);

			// pass value to the parameter
			$stmt->bindValue(':eid', $id);

			$stmt->execute(); // execute the statement

			$hours = 0;
			$shifts = [];
			$shifts_hours = [];

			// print employee info
			echo "<p>Schedule for <b>$firstname $lastname</b>:</p>";
			echo "<p>Email: $email</p>";
			echo "<textarea cols=\"100\" rows=\"15\" readonly>"; // open text area
			$body = "Hello $firstname,&#13;&#10;&#13;&#10;";
			$body .= "Here is your schedule from $start_date until $finish_date.&#13;&#10;&#13;&#10;";
			
			// calculate hours worked
			while($row = $stmt->fetchObject()) {
				$s = new DateTime($row->start_date);
				$f = new DateTime($row->finish_date);
				
				// exclusive on start day, inclusive of finish day
				if($s > new DateTime($start_date) && $f <= new DateTime($finish_date)) {
					$shifts[] = $row;
					$difference = $f->diff($s);
					$hours_to_add = floatval($difference->format('%H.%i'));
					$intpart = floor($hours_to_add);
					$fraction = $hours_to_add - $intpart;
					$minutes = (($fraction * 10) / 60) * 10;
					$shifts_hours[] = ($intpart + $minutes);
					$hours += ($intpart + $minutes);
					
				}
				
			}

			// print employee shift information inside textarea tag
			$count = 0;
			foreach($shifts as $shift) {
				$start = $shift->start_date;
				$start = date('Y/m/d h:i a', strtotime($start));
				$finish = $shift->finish_date;
				$finish = date('Y/m/d h:i a', strtotime($finish));
				$s_hours = $shifts_hours[$count];
				$count++;
				$body .= "&#09;Start: $start &#09; Finish: $finish &#09; Hours: $s_hours &#13;&#10;";
			}
			$body .= "&#09;Total Hours: $hours";
			$body .= "&#13;&#10;&#13;&#10;Thanks,&#13;&#10;Management";
			echo $body;
			echo "</textarea>"; // close text area
			// print "send email" button
			echo "<br>";
			// format body variable for mailto link
			$body = str_replace("&#13;&#10;", "%0D%0A", $body); // replace new lines with mailto acceptable new line codes
			$body = str_replace("&#09;", "          ", $body); // replace tabs to work for mailto
			echo "<a href=\"mailto:$email?Subject=KVLiquor Schedule&Body=$body\"><button type=\"button\">Send Email</button></a>";
			echo "<hr>"; // print horizontal line between employees
		}
	}

?>
</div>
<footer>

</footer>
</body>
<div id="footer">
<!-- intentionally removed as a simple fix -->
</div>
</html>