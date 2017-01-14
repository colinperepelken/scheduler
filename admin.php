<!DOCTYPE html>
<html>
<head>
<title>KVLiquor - Admin</title>
<link rel='stylesheet' type='text/css' href='style/kvliquor.css'/>
</head>

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

function alert($message) {
	echo "<script type='text/javascript'>alert('$message');</script>";
}

?>

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
<?php
if(isset($_GET['day'])) {
	$day = $_GET['day'];
} else {
	$day = 0;
}
?>
<div id = "calendar">
<script language="javascript" type="text/javascript">
var cal = ""; // used for printing


var day_of_week = new Array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
var month_of_year = new Array('January','February','March','April','May','June','July','August','September','October','November','December');

//  DECLARE AND INITIALIZE VARIABLES
var Calendar = new Date();

var year = <?php echo $_GET['year']; ?>;     // Returns year
var month = <?php echo $_GET['month'] - 1; ?>;    // Returns month (0-11)
var today = Calendar.getDate();    // Returns day (1-31)
var weekday = <?php echo $_GET['day']; ?>    // Returns day (1-31)

// next and previous month buttons
if(month == 0) {
	cal += "<button><a href=\"admin.php?year=" + (year-1) +"&month=" + 12 + "&day=1\">Last Month</a></button>"; // previous month
	cal += "<button style=\"float: right;\"><a href=\"admin.php?year=" + year +"&month=" + (month+2) + "&day=1\">Next Month</a></button>"; // next month
} else if(month == 11) {
	cal = "<button><a href=\"admin.php?year=" + year +"&month=" + (month) + "&day=1\">Last Month</a></button>"; // previous month
	cal += "<button style=\"float: right;\"><a href=\"admin.php?year=" + (year+1) +"&month=" + 1 + "&day=1\">Next Month</a></button>"; // next month
} else {
	cal = "<button><a href=\"admin.php?year=" + year +"&month=" + (month) + "&day=1\">Last Month</a></button>"; // previous month
	cal += "<button style=\"float: right;\"><a href=\"admin.php?year=" + year +"&month=" + (month+2) + "&day=1\">Next Month</a></button>"; // next month
}

var DAYS_OF_WEEK = 7;    // "constant" for number of days in a week
var DAYS_OF_MONTH = 31;    // "constant" for number of days in a month

var actualMonth = Calendar.getMonth();
Calendar.setDate(1);    // Start the calendar day at '1'
Calendar.setMonth(month);    // Start the calendar month at now





/* VARIABLES FOR FORMATTING
NOTE: You can format the 'BORDER', 'BGCOLOR', 'CELLPADDING', 'BORDERCOLOR'
      tags to customize your caledanr's look. */

var TR_start = '<TR>';
var TR_end = '</TR>';
var highlight_start = '<TD WIDTH="60" HEIGHT="50"><TABLE CELLSPACING=0 BORDER=1 BGCOLOR=DEDEFF BORDERCOLOR=CCCCCC><TR><TD WIDTH=40 HEIGHT="20"><B><CENTER>';
var highlight_end   = '</CENTER></TD></TR></TABLE></B>';
var TD_start = '<TD WIDTH="60" HEIGHT="50"><CENTER>';
var TD_end = '</CENTER></TD>';
var selected_start = "<div id=\"selected\">";
var selected_end = "</div>";

/* BEGIN CODE FOR CALENDAR
NOTE: You can format the 'BORDER', 'BGCOLOR', 'CELLPADDING', 'BORDERCOLOR'
tags to customize your calendar's look.*/

cal +=  '<TABLE BORDER=1 CELLSPACING=0 CELLPADDING=0 BORDERCOLOR=BBBBBB><TR><TD>';
cal += '<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>' + TR_start;
cal += '<TD COLSPAN="' + DAYS_OF_WEEK + '" BGCOLOR="#EFEFEF"><CENTER><B>';
cal += month_of_year[month]  + '   ' + year + '</B>' + TD_end + TR_end;
cal += TR_start;

//   DO NOT EDIT BELOW THIS POINT  //

// LOOPS FOR EACH DAY OF WEEK
for(index=0; index < DAYS_OF_WEEK; index++)
{

// BOLD TODAY'S DAY OF WEEK
//if(weekday == index && actualMonth == month) {
//cal += TD_start + '<B>' + day_of_week[index] + '</B>' + TD_end;

// PRINTS DAY

cal += TD_start + day_of_week[index] + TD_end;

}

cal += TD_end + TR_end;
cal += TR_start;

// FILL IN BLANK GAPS UNTIL TODAY'S DAY
for(index=0; index < Calendar.getDay(); index++)
cal += TD_start + '  ' + TD_end;

// LOOPS FOR EACH DAY IN CALENDAR
for(index=0; index < DAYS_OF_MONTH; index++)
{
if( Calendar.getDate() > index )
{
  // RETURNS THE NEXT DAY TO PRINT
  week_day =Calendar.getDay();

  // START NEW ROW FOR FIRST DAY OF WEEK
  if(week_day == 0)
  cal += TR_start;

  if(week_day != DAYS_OF_WEEK)
  {

  // SET VARIABLE INSIDE LOOP FOR INCREMENTING PURPOSES
  var day  = Calendar.getDate();

  if(day == <?php echo $day; ?>) {
	  if( today==Calendar.getDate() && actualMonth == month) {
		  cal += highlight_start + '<a href=admin.php?year=' + year + '&month=' + (month+1) + '&day=' + day + '>' + selected_start + day + selected_end + '</a>' + highlight_end + TD_end;
	  } else {
		   cal += TD_start + '<a href=admin.php?year=' + year + '&month=' + (month+1) + '&day=' + day + '>' + selected_start + day + selected_end + '</a>' + TD_end;
		  
	  }
	 
	  
  // HIGHLIGHT TODAY'S DATE
  } else if( today==Calendar.getDate() && actualMonth == month) {
  cal += highlight_start + '<a href=admin.php?year=' + year + '&month=' + (month+1) + '&day=' + day + '>' + day + '</a>' + highlight_end + TD_end;
	
  // PRINTS DAY
  }else{
  cal += TD_start + '<a href=admin.php?year=' + year + '&month=' + (month+1) + '&day=' + day + '>' + day + '</a>' + TD_end;
  }

  // END ROW FOR LAST DAY OF WEEK
  if(week_day == DAYS_OF_WEEK)
  cal += TR_end;
  }

  // INCREMENTS UNTIL END OF THE MONTH
  Calendar.setDate(Calendar.getDate()+1);
}
}// end for loop

cal += '</TD></TR></TABLE></TABLE>';

//  PRINT CALENDAR
document.write(cal);

//  End -->
</script>
</div>

<div id="panel">
<?php
/* EMPLOYEE LIST */
if(isset($_GET['showemp'])) {
	// output list of employees in table format
	echo '<h3>Employee List</h3>';
	echo '<p align="left">(Click names to edit)</p>';
	echo '<table><tbody>';
	
	// fetch list of employees
	foreach (getEmployees() as $employee) {
		$firstname = $employee['firstname'];
		$lastname = $employee['lastname'];
		$id = $employee['id'];
		echo "<tr><td><a href=\"employee.php?id=$id\">$firstname $lastname</a></td></tr>";
	}
	echo "<tr></tr><tr><td><br><a href=\"employee.php\"><button type=\"button\">Add an Employee</button></a></td></tr></tbody></table>";
	
} else if(isset($_GET['day'])) {

	// get date info
	$year = $_GET['year'];
	$month = sprintf("%02d", $_GET['month']);
	$day = sprintf("%02d", $_GET['day']);
	$date = "$year-$month-$day";

	/* DELETE A SHIFT */
	if(isset($_GET['del'])) {
		$sid = $_GET['del'];
		
		$sql = "DELETE FROM Shift WHERE sid = :sid";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([':sid' => $sid]);
	}
	
	/* INSERT A SHIFT */
	if(isset($_GET['submit']) && $_GET['submit'] == "Add Shift") {
		// get
		$employee = $_GET['employee'];
		$start = $_GET['start'];
		$finish = $_GET['finish'];
		
		// validate
		if(new DateTime($start) < new DateTime($finish)) {
			if(!empty($employee) && (preg_match('/\\d/', $employee) > 0)) {
				
					
				// retrieve employee id (could just use name but there could be conflicts if two employees have the same name)
				$eid = preg_replace("/[^0-9]/", "", $employee); // get numbers from string and store in eid
					
				// format start_date and finish_date
				$start_date = $date . " " . $start;
				$finish_date = $date . " " . $finish;
					
				// prepare SQL insert statement
				$sql = 	"INSERT INTO Shift(eid, start_date, finish_date)" 
						. " VALUES (:eid, :start_date, :finish_date);";
				$stmt = $pdo->prepare($sql);
				
				// passing values to the parameters
				$stmt->bindValue(':eid', $eid);
				$stmt->bindValue(':start_date', $start_date);
				$stmt->bindValue(':finish_date', $finish_date);
				
				$stmt->execute(); // execute the statement
				
				
			} else {
				alert("Please select an employee.");
			}
		} else {
			alert("Start time must be before finish time!");
		}
		
	}
	
	
	/* VIEWING SCHEDULED SHIFTS */
	
	echo "<h3>Shifts on $date</h3>"; // output header
	echo "<table id=\"shifts\"><tbody><tr><th>Start</th><th>Finish</th><th>Employee</th></tr>";
	
	// query shifts on this day
	$sql = "SELECT start_date, finish_date, firstname, lastname, id, sid"
			. " FROM Employee E, Shift S"
			. " WHERE E.id = S.eid AND start_date LIKE :date"
			. " ORDER BY start_date ASC;";
	$stmt = $pdo->prepare($sql);
	$date = "%".$date." %"; // space i important! otherwise shifts on day 28 will show for day 2
	$stmt->execute([':date' => $date]);
	$count = 0;
	while ($shift = $stmt->fetchObject()) {
		// start and finish displayed as only hours and minutes
		$start = date("g:i a", strtotime($shift->start_date));
		$finish = date("g:i a", strtotime($shift->finish_date));
		$firstname = $shift->firstname;
		$lastname = $shift->lastname;
		$id = $shift->id;
		$sid = $shift->sid;
		
		echo "<tr><td>$start</td><td>$finish</td><td><a href=\"employee.php?id=$id\">$firstname $lastname</a></td>
				<td><a href=admin.php?del=$sid&day=$day&month=$month&year=$year><button type=\"button\">Delete</button></a></td></tr>";
		$count++;
	}
	
	/* OUTPUT FORM TO ADD A SHIFT */
	$date = str_replace("%","",$date); // remove % which were added for sql query.
	
	// output time inputs
	echo "<tr><form method=\"get\" action=\"admin.php\"><td><input type=\"time\" name=\"start\" value=\"10:00 AM\"></td>
			<td><input type=\"time\" name=\"finish\" value=\"11:00 PM\"></td>
			
			<input type=\"hidden\" name=\"day\" value=\"$day\">
			<input type=\"hidden\" name=\"year\" value=\"$year\">
			<input type=\"hidden\" name=\"month\" value=\"$month\">";
			
	
	// output employee selector
	echo "<td><input list=\"employees\" name=\"employee\" value=\"\"> 
			<datalist id=\"employees\">";
	
	// get list of employees for drop down input form
	foreach(getEmployees() as $employee) {
		$firstname = $employee['firstname'];
		$lastname = $employee['lastname'];
		$id = $employee['id'];
		echo "<option value=\"$firstname $lastname ($id)\">";
	}
	
	// close datalist and print submit button
	echo 	"</datalist>
			</td><td><input type=\"submit\" name=\"submit\" value=\"Add Shift\" id=\"submit\" /></td></form></tr>";
		
	// close table
	echo "</form></tbody></table>";
	if($count == 0) {
		echo "<p>No shifts scheduled.</p>"; // output message if there are no shifts
	}
}
?>
</div>
<div id="footer">
	<p>Colin Bernard 2016</p>
</div>
</html>
</body>
