<!DOCTYPE html>
<html>
<head>
<title>KVLiquor - Report</title>
<link rel='stylesheet' type='text/css' href='style/kvliquor.css'/>
<?php
// CONNECT TO DATABASE
require 'vendor/autoload.php';
use App\SQLiteConnection;

$pdo = (new SQLiteConnection())->connect();

date_default_timezone_set('America/Los_Angeles'); // set default time zone to PST


function alert($message) {
	echo "<script type='text/javascript'>alert('$message');</script>";
}

/**
 * Get all employees
 */
function getEmployees() {
	global $pdo;
	$sql = "SELECT id, firstname, lastname FROM Employee WHERE employed = 'true' ORDER BY firstname ASC";
	$stmt = $pdo->query($sql);
	$employees = [];
	while ($employee = $stmt->fetch(\PDO::FETCH_ASSOC)) {
		$employees[] = [
			'id' => $employee['id'],
			'firstname' => $employee['firstname'],
			'lastname' => $employee['lastname']
		];
	}
	return $employees; // return list off employees
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
<h3>Generate Report</h3>
<p>Determine hours worked over period. Includes start day. Does NOT include finish day.</p>
<p>Once button is pressed... Click on box below then press CTRL+A to select all. Then copy into a document or email.</p>
<form method="get" action="report.php">
<table>
<tbody>
<tr><th>Start Date</th><th>Finish Date</th></tr>
<tr><td><input type="date" name="start" value="<?php if(isset($start_date)){echo $start_date;} ?>">
	</td><td><input type="date" name="finish" value="<?php if(isset($finish_date)){echo $finish_date;} ?>"></td></tr>
</tbody>
</table>
<input type="submit" name="submit" value="Generate Report" id="submit" />
</form>
<textarea cols=150 rows = 150 readonly>
<?php
	if(isset($_GET['submit'])) {

		$start_date = $_GET['start'];
		$finish_date = $_GET['finish'];

		foreach(getEmployees() as $employee) { // for each employee display the shifts they are working in the date range
			$id = $employee['id'];
			$firstname = $employee['firstname'];
			$lastname = $employee['lastname'];


			$sql = "SELECT start_date, finish_date FROM Shift WHERE eid = :eid ORDER BY start_date ASC;";
			$stmt = $pdo->prepare($sql);

			// pass value to the parameter
			$stmt->bindValue(':eid', $id);

			$stmt->execute(); // execute the statement

			$hours = 0;
			$shifts = [];
			$shifts_hours = [];
			
			
			// print employee info
			$body = "$firstname $lastname:&#10;&#10;";
			
			$body .= "Shifts from $start_date until $finish_date.&#13;&#10;&#13;&#10;";
			
			// calculate hours worked
			while ($row = $stmt->fetchObject()) {
				$s = new DateTime($row->start_date);
				$f = new DateTime($row->finish_date);
				
				// exclusive on start day, inclusive of finish day
				if ($s > new DateTime($start_date) && $f <= new DateTime($finish_date)) {
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
			foreach ($shifts as $shift) {
				$start = $shift->start_date;
				$start = date('Y/m/d h:i a', strtotime($start));
				$finish = $shift->finish_date;
				$finish = date('Y/m/d h:i a', strtotime($finish));
				$s_hours = $shifts_hours[$count];
				$count++;
				$body .= "&#09;Start: $start &#09; Finish: $finish &#09; Hours: $s_hours &#13;&#10;";
			}
			$body .= "&#09;Total Hours: $hours";
			echo $body;
			echo "&#10;&#10;--------------------------------------------------------------------------------------&#10;&#10;";
		}
	}

?>
</textarea>
</div>
<div id="panel">

</div>
</body>
<div id="footer">
<!-- intentionally left blank -->
</div>
</html>