<?php
// CONNECT TO DATABASE
require 'vendor/autoload.php';
use App\SQLiteConnection;

$pdo = (new SQLiteConnection())->connect();


function alert($message) {
	echo "<script type='text/javascript'>alert('$message');</script>";
}

/**
 * Get all employees
 */
function getEmployees() {
	global $pdo;
	$sql = "SELECT id, firstname, lastname FROM Employee ORDER BY firstname ASC";
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

/* GENERATE REPORT BUTTON CLICKED */
if(isset($_GET['submit']) && $_GET['submit'] == "Generate Report") {
	$start_date = $_GET['start'];
	$finish_date = $_GET['finish'];
	$employee = $_GET['employee'];
	
	// validate
	if(!empty($employee) && (preg_match('/\\d/', $employee) > 0)) {
		
		// retrieve employee id (could just use name but there could be conflicts if two employees have the same name)
		$eid = preg_replace("/[^0-9]/", "", $employee); // get numbers from string and store in eid
		// retrieve employee name (no id)
		$emp_name = preg_replace('/[^\p{L}\p{N}\s]/u', '', $employee); // strip symbols
		$emp_name = preg_replace('/[0-9]+/', '', $emp_name); // strip numbers
		$emp_name = rtrim($emp_name, " "); // removes space from end of name
		
		// prepare SQL select statement
		$sql = 	"SELECT start_date, finish_date"
				. " FROM Shift S"
				. " WHERE S.eid = :eid";
		$stmt = $pdo->prepare($sql);
			
		// passing values to the parameters
		$stmt->bindValue(':eid', $eid);
			
		$stmt->execute(); // execute the statement
		
		$hours = 0;
		$shifts = [];
		$shifts_hours = [];
		
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
		
	} else {
		alert("Please select an employee.");
	}
	
	
}
?>

<head>
<title>KVLiquor - Report</title>
<link rel='stylesheet' type='text/css' href='style/kvliquor.css'/>
</head>
<body>
<div id = "header">
<img src="http://i.imgur.com/rCYjjsD.jpg" style="float:left">

<div id="links">

<table id="top"><tr>
<td><a href = "admin.php?year=<?php echo date("Y"); ?>&month=<?php echo date("m"); ?>&day=<?php echo date("d"); ?>"> Schedule a Shift </a></td>
<td><a href = "report.php"> Generate Report </a></td>
<td><a href = "admin.php?showemp=1"> Employees </a></td>
<td><a href = "settings.php"> Settings </a></td></tr></table>
</div>
</div>
<div id="settings">
<h3>Generate Report</h3>
<p>Determine hours worked over period. Does not include start day. Includes finish day.</p>
<form method="get" action="report.php">
<table>
<tbody>
<tr><th>Start Date</th><th>Finish Date</th><th>Employee</th></tr>
<tr><td><input type="date" name="start" value="<?php if(isset($start_date)){echo $start_date;} ?>">
	</td><td><input type="date" name="finish" value="<?php if(isset($finish_date)){echo $finish_date;} ?>"></td>
	<td><input list="employees" name="employee" value="<?php if(isset($employee)){echo $employee;} ?>">
		<datalist id="employees">
		<?php
			// get list of employees for drop down input form
			foreach(getEmployees() as $employee) {
				$firstname = $employee['firstname'];
				$lastname = $employee['lastname'];
				$id = $employee['id'];
				echo "<option value=\"$firstname $lastname ($id)\">";
			}
		?>
		</datalist>
	</td></tr>
</tbody>
</table>
<input type="submit" name="submit" value="Generate Report" id="submit" />
</form>
<?php
if(isset($_GET['submit']) && !empty($_GET['employee'])) {
	echo "<h5>Hours worked from $start_date to $finish_date by $emp_name:</h5>";
	echo "<table id=\"report\"><tbody><tr><th>Start</th><th>Finish</th><th>Hours</th></tr>";
	
	$count = 0;
	foreach($shifts as $shift) {
		$start = $shift->start_date;
		$finish = $shift->finish_date;
		$s_hours = $shifts_hours[$count];
		$count++;
		echo "<tr><td>$start</td><td>$finish</td><td>$s_hours</td></tr>";
	}
	echo "<tr></tr><tr><td></td><td></td><td id=\"total\"><b>Total: $hours</b></td></tr>";
	echo "</tbody></table>";
}
?>
</div>
<div id="panel">

</div>
</body>
<div id="footer">
<p>Colin Bernard 2016</p>
</div>
