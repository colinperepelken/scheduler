<?php
// CONNECT TO DATABASE
require 'vendor/autoload.php';
use App\SQLiteConnection;

$pdo = (new SQLiteConnection())->connect();

// alert function
function alert($message) {
	echo "<script type='text/javascript'>alert('$message');</script>";
}


/* UPDATE HOURS */
if (isset($_GET['submit']) && $_GET['submit'] == "Save") {
	for($i = 0; $i < 7; $i++) {
		$open_times[] = $_GET["o$i"];
		$close_times[] = $_GET["c$i"];
	}

	// delete all hours stored in db
	$sql = "DELETE FROM Hours;";
	$pdo->query($sql);
	
	for($i = 0; $i < 7; $i++) {		
		// insert hours into db
		$sql = "INSERT INTO Hours VALUES (:weekday, :open_time, :close_time);";
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':weekday', $i);
		$stmt->bindValue(':open_time', $open_times[$i]);
		$stmt->bindValue(':close_time', $close_times[$i]);
		$stmt->execute();
	}
	alert("Settings saved.");

}

// query to find store hours
$sql = "SELECT * FROM Hours;";
$stmt = $pdo->query($sql);
$days = [];
while ($day = $stmt->fetchObject()) {
	$days[] = $day;
}
?>

<head>
<title>KVLiquor - Settings</title>
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
<h3>Store Hours</h3>
<p>Used for colouring the calendar and checking if a work day is fully staffed.</p>
<form method="get" action="settings.php">
<table>
<tbody>
<tr><td></td><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th><th>Saturday</th><th>Sunday</th></tr>
<tr><td>Open</td>
<?php 
$count = 0;
foreach ($days as $day) {
	$time = $day->open_time;
	echo "<td><input type=\"time\" name=o$count value=\"$time\"></td>";
	$count++;
}

// fill in data if it is not set
for ($i = $count; $i < 7; $i++) {
	echo "<td><input type=\"time\" name=o$i value=\"\"</td>";
}
?>
</tr>
<tr><td>Close</td>
<?php 
$count = 0;
foreach ($days as $day) {
	$time = $day->close_time;
	echo "<td><input type=\"time\" name=c$count value=\"$time\"></td>";
	$count++;
}

// fill in data if it is not set
for ($i = $count; $i < 7; $i++) {
	echo "<td><input type=\"time\" name=c$i value=\"\"</td>";
}
?>
</tr>
</tbody>
</table>
<input type="submit" name="submit" value="Save" id="submit" />
</form>
</div>
</body>
<div id="footer">
<p>Colin Bernard 2016</p>
</div>
