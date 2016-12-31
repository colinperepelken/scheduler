<?php
// CONNECT TO DATABASE
require 'vendor/autoload.php';
use App\SQLiteConnection;

$pdo = (new SQLiteConnection())->connect();

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
	
	// retrieve employee id (could just use name but there could be conflicts if two employees have the same name)
	$eid = preg_replace("/[^0-9]/", "", $employee); // get numbers from string and store in eid
	// retrieve employee name (no id)
	$emp_name = preg_replace('/[^\p{L}\p{N}\s]/u', '', $employee); // strip symbols
	$emp_name = preg_replace('/[0-9]+/', '', $emp_name); // strip numbers
	
	// prepare SQL select statement
	$sql = 	"SELECT SUM(strftime('%H %M %s',finish_date) - strftime('%H %M %s',start_date)) AS hours"
			. " FROM Shift S, Employee E"
			. " WHERE S.eid = E.id AND S.eid = :eid AND start_date >= :start_date AND start_date <= :finish_date";
	$stmt = $pdo->prepare($sql);
		
	// passing values to the parameters
	$stmt->bindValue(':eid', $eid);
	$stmt->bindValue(':start_date', $start_date);
	$stmt->bindValue(':finish_date', $finish_date);
		
	$stmt->execute(); // execute the statement
	
	$hours = 0;
	// assuming only one row returned
	while($row = $stmt->fetchObject()) {
		$hours = $row->hours;
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
<p>Determine hours worked over period.</p>
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
</div>
<div id="panel">
<?php
	echo "<h3>Hours worked from $start_date to $finish_date by $emp_name</h3>";
	echo $hours;
?>
</div>
</body>
<div id="footer">
<p>Colin Bernard 2016</p>
</div>
