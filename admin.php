<head>
<title>KVLiquor - Admin</title>
<style>
	#header{
		position: absolute;
		top: 0px;
		left: 0px;
		width: 100%;
		background-color: #232524;
		color: white;
		font-family: "Trebuchet MS", Verdana, sans-serif;
		font-size: 25;
		display: inline-block;
	}
	
	#title{
		font-size: 30;
		display: inline;
		whitespace: nowrap;
		horizontal-align: center;
	}
	
	#image{
		display: inline;
	}
	
	#links{
		display: inline;
		whitespace: nowrap;
	
	}
	
	#calendar{
		position: absolute;
		top: 110px;
		left: 20px;
		font-family: "Trebuchet MS", Verdana, sans-serif;
		font-size: 25;
	}
	
	a:link, a:visited, a:active {
		color: inherit;
		text-decoration: none;
	}

	a:hover{
		color: #d9373c;
	}
	
	#buttons{
		position: absolute;
		bottom: 10px;
		left: 10px;
	}
	
	table#top{
		border: none;
		position: abosolute;
		top: 0px;
		left: 260px;
		vertical-align: bottom;
		font: inherit;
		color: inherit;

	}
	
	table#top td{
		height: 70px;
		vertical-align: bottom;
		padding-right: 25px;
	}
	
	#panel{
		position: absolute;
		top: 110px;
		left: 500px;
		background-color: d9373c;
		width: 300px;
		height: 327px;
	}
	
	
	

</style>

</head>

<?php
// CONNECT TO DATABASE
require 'vendor/autoload.php';
use App\SQLiteConnection;

$pdo = (new SQLiteConnection())->connect();
?>

<body>

<div id = "header">
<img src="http://i.imgur.com/rCYjjsD.jpg" style="float:left">

<div id="links">

<table id="top"><tr>
<td><a href = "schedule.php"> Schedule a Shift </a></td>
<td><a href = "generate.php"> Generate Report </a></td>
<td><a href = "viewemp.php"> Employees </a></td></tr></table>
</div>
</div>


<div id = "calendar">
<script language="javascript" type="text/javascript">
var day_of_week = new Array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
var month_of_year = new Array('January','February','March','April','May','June','July','August','September','October','November','December');

//  DECLARE AND INITIALIZE VARIABLES
var Calendar = new Date();

var year = Calendar.getFullYear();     // Returns year
var month = Calendar.getMonth();    // Returns month (0-11)
var today = Calendar.getDate();    // Returns day (1-31)
var weekday = Calendar.getDay();    // Returns day (1-31)

var DAYS_OF_WEEK = 7;    // "constant" for number of days in a week
var DAYS_OF_MONTH = 31;    // "constant" for number of days in a month
var cal;    // Used for printing

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

/* BEGIN CODE FOR CALENDAR
NOTE: You can format the 'BORDER', 'BGCOLOR', 'CELLPADDING', 'BORDERCOLOR'
tags to customize your calendar's look.*/

cal =  '<TABLE BORDER=1 CELLSPACING=0 CELLPADDING=0 BORDERCOLOR=BBBBBB><TR><TD>';
cal += '<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>' + TR_start;
cal += '<TD COLSPAN="' + DAYS_OF_WEEK + '" BGCOLOR="#EFEFEF"><CENTER><B>';
cal += month_of_year[month]  + '   ' + year + '</B>' + TD_end + TR_end;
cal += TR_start;

//   DO NOT EDIT BELOW THIS POINT  //

// LOOPS FOR EACH DAY OF WEEK
for(index=0; index < DAYS_OF_WEEK; index++)
{

// BOLD TODAY'S DAY OF WEEK
if(weekday == index)
cal += TD_start + '<B>' + day_of_week[index] + '</B>' + TD_end;

// PRINTS DAY
else
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

  // HIGHLIGHT TODAY'S DATE
  if( today==Calendar.getDate() )
  cal += highlight_start + day + highlight_end + TD_end;

  // PRINTS DAY
  else
  cal += TD_start + day + TD_end;
  }

  // END ROW FOR LAST DAY OF WEEK
  if(week_day == DAYS_OF_WEEK)
  cal += TR_end;
  }

  // INCREMENTS UNTIL END OF THE MONTH
  Calendar.setDate(Calendar.getDate()+1);

}// end for loop

cal += '</TD></TR></TABLE></TABLE>';

//  PRINT CALENDAR
document.write(cal);

//  End -->
</script>
</div>

<div id="panel">
<?php
	// output list of employees in table format
	echo '<h3>Employee List</h3>';
	echo '<table><tbody>';
	
	// fetch list of employees
	$sql = "SELECT id, firstname, lastname FROM Employee;";
	$stmt = $pdo->query($sql);
	while ($employee = $stmt->fetchObject()) {
		$firstname = $employee->firstname;
		$lastname = $employee->lastname;
		$id = $employee->id;
		echo "<tr><td><a href=\"employee.php?id=$id\">View/Edit</a></td><td>$firstname $lastname</td></tr>";
	}
	echo "<tr><td><a href=\"google.com\">+ Add an Employee</a></td></tr></tbody></table>";
	

?>
</div>
</body>