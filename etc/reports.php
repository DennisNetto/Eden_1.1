<?php
/*  Developer:   Justin Alho, Harley Lenton, Evan Guest
 *  File Name:   reports.php
 *  Description: Allows bookkeepers to view approved hours for a range of dates
 *  Date Start:  08/04/2020
 *  Date End:    TBD
 */
 ?>
<html>

<head>

    <title>View Reports</title>
    <?php
	error_reporting(0);
    //Starting a session and initializing variables needed
    session_start();
    $userType = $_SESSION['userType'];

    include "../includes/scripts/headLinks2.0.php";
    include "../includes/functions/getHours.php"
    ?>
    <script>
        //this function should take in an id and a total and set the total to the spot with that id

        function setTotal(id, total)
        {
            var total = total.toFixed(1);
            document.getElementById(id).innerHTML = total;
        }
						//fill dropdown with client results

				//fill dropdown with staff results
				function staSearch(search) {
					if (window.XMLHttpRequest)
					{
						xmlhttp = new XMLHttpRequest();
					}
					xmlhttp.onreadystatechange = function() {
						if (this.readyState == 4 && this.status == 200) {
							document.getElementById('staSel').innerHTML = this.responseText;
						}
					};
					xmlhttp.open('GET','../includes/scripts/search1.php?s='+search,true);
					xmlhttp.send();
				}
				
				//retrieve list of available staff

    </script>
</head>

<body>

<?php

//level of authorization required to access page
$authLevel = "B";

//to verify the user
include "../includes/functions/verLogin.php";
verLogin();

//to verify the user's type
include "../includes/functions/valUserType.php";
valUserType($authLevel);

//initialize variables
$st = '';
$end = '';
$staff = '';
$low = 0;
$stErr = '';
$endErr = '';
$name = '';

//if request to generate report has been sent
if(isset($_POST['gen']))
{
	$staff = $_POST['staff'];
	$st = $_POST['start'];
	$end = $_POST['end'];
	if ($staff != 1000000){
	$low = $staff;}
	//initialize error counter
	$err = 0;
	
	//check to make sure dates are correct
	if($st == '')
	{
		$stErr = '<div class="badge badge-warning">Please enter a start date.</div>';
		$err++;
	}
	if($end == '')
	{
		$endErr = '<div class="badge badge-warning">Please enter an end date.</div>';
		$err++;
	if($staff == '')
	{
		$staerr = '<div class="badge badge-warning">Please select a staff member.</div>';
		$err++;
	}
	}
	if($end < $st)
	{
		$endErr = '<div class="badge badge-warning">Please enter a valid end date.</div>';
		$err++;
	}
	
	if($err == 0)
	{
		$good = 1;
	}
	
}

//include navbar
include "../includes/scripts/navBar.php";
echo'<div class="conb">';
			echo'<div class="row justify-content-sm-center">';
//print a date range selection
printf( "<div class='container'>
		<div class='form-con'>
			<form method='post' action='reports.php'>
				<h2>Create Report</h2><br />
				View hours from:<div class='form-row'><div class='col-12'> <input class='form-fan' type='date' name='start' value='$st'> $stErr<br /><br />
				</div></div>To:<div class='form-row'><div class='col-12'> <input class='form-fan' type='date' name='end' value='$end'>  $endErr<br /><br />
				</div></div>Search for Staff:<div class='form-row'><div class='col-12'>
						<input class='form-fan' type='text' name='' value='' onkeyup='staSearch(this.value)'><br /><br />\n

					<!--display selection of staff members-->
					</div></div>Staff results:<div class='form-row'><div class='col-12'>
						<select class='fanc' name='staff' id='staSel'>
							<option value='1000000'>Search All:</option>");
							foreach($starow as $data)
				echo "<option value='{$data['STAFF_ID']}'>{$data['STAFF_LNAME']}, {$data['STAFF_FNAME']}</option>";
			printf("
						</select>$staerr<br /><br />\n
				</div></div><input type='submit' name='gen' class='btn btn-primary' value='View Report'>
			</form><br /><br />\n");

if(isset($good))
{
	//connect to the database
    include "../dbseckey.php";
	$conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);

	//select basic information about shifts, ordered first by client, then by date,
	//then by start time in case somehow the same staff is scheduled with the same client multiple times in one day
	$stm = "SELECT SHIFT_ID, SHIFT_DATE, APPROVED_START, APPROVED_END, SHIFT_DATE, DEP_NAME,
							SHIFT.CLIENT_ID, SHIFT.STAFF_ID, STAFF_LNAME, STAFF_FNAME, CLIENT_LNAME, CLIENT_FNAME
							FROM SHIFT
							LEFT JOIN STAFF
							ON SHIFT.STAFF_ID = STAFF.STAFF_ID
							LEFT JOIN CLIENT
							ON SHIFT.CLIENT_ID = CLIENT.CLIENT_ID
							LEFT JOIN DEPARTMENT
							ON SHIFT.DEP_CODE = DEPARTMENT.DEP_CODE
							WHERE SHIFT_DATE >= '$st'
							AND SHIFT_DATE <= '$end'
							AND STATUS_CODE = 'A'
							AND SHIFT.STAFF_ID between '$low' and '$staff'
							  ";


	$orderBy = " ORDER BY STAFF_LNAME ASC, STAFF_FNAME ASC, CLIENT_LNAME ASC, CLIENT_FNAME ASC, SHIFT_DATE ASC, APPROVED_START ASC";

	//sorting/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if( (isset($_REQUEST['sortBy']) ) && (!isset($_SESSION['sortBy']) ) )
	{
		switch($_REQUEST['sortBy'])
		{
			case 'staff':
				$_SESSION['sortBy'] = 'staff';
				$orderBy = " ORDER BY  STAFF_LNAME DESC, STAFF_FNAME DESC, CLIENT_LNAME, CLIENT_FNAME, SHIFT_DATE, APPROVED_START";
				$stm = $stm . $orderBy;
				break;

			case 'client':
				$_SESSION['sortBy'] = 'client';
				$orderBy = " ORDER BY CLIENT_LNAME DESC, CLIENT_FNAME DESC, STAFF_LNAME, STAFF_FNAME, SHIFT_DATE, APPROVED_START";
				$stm = $stm . $orderBy;
				break;

			case 'dep':
				$_SESSION['sortBy'] = 'dep';
				$orderBy = " ORDER BY DEP_NAME DESC, CLIENT_LNAME, CLIENT_FNAME, STAFF_LNAME, STAFF_FNAME, SHIFT_DATE, APPROVED_START";
				$stm = $stm . $orderBy;
				break;

			default:

				break;
		}
	}
	else if( (isset($_REQUEST['sortBy']) ) && (isset($_SESSION['sortBy']) ) )
	{
		switch($_REQUEST['sortBy'])
		{
			case 'staff':
				unset($_SESSION['sortBy']);
				$orderBy = " ORDER BY STAFF_LNAME ASC, STAFF_FNAME ASC, CLIENT_LNAME, CLIENT_FNAME, SHIFT_DATE, APPROVED_START";
				$stm = $stm . $orderBy;
				break;

			case 'client':
				unset($_SESSION['sortBy']);
				$orderBy = " ORDER BY CLIENT_LNAME ASC, CLIENT_FNAME ASC, STAFF_LNAME, STAFF_FNAME, SHIFT_DATE, APPROVED_START";
				$stm = $stm . $orderBy;
				break;

			case 'dep':
				unset($_SESSION['sortBy']);
				$orderBy = " ORDER BY DEP_NAME ASC, CLIENT_LNAME, CLIENT_FNAME, STAFF_LNAME, STAFF_FNAME, SHIFT_DATE, APPROVED_START";
				$stm = $stm . $orderBy;
				break;

			default:

				break;
		}
	}
	else
	{
		$stm = $stm . $orderBy;
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

				//retrieve list of staff from database
			$stasql = $conn->prepare("SELECT * FROM STAFF
				where STAFF_STATUS = 'A'
				AND (TYPE_CODE = 'W'
				OR TYPE_CODE = 'S')
				ORDER BY STAFF_LNAME ASC");
				
				$stasql->execute();
			$starow = $stasql->fetchAll();
			
	$sql = $conn->prepare($stm);

	$sql->execute();

	$row = $sql->fetchAll();
	
			
	
	if(sizeof($row) == 0)
	{
		echo "No records found.<br /><br />";
	}
	
	else
	{
		$stDate = strtotime($st);
		$enDate = strtotime($end);

		echo "<div class='container' style='min-height: 45%; overflow: auto; ' >
					<h1>Hours for: " . date('M d Y', $stDate) . "-" . date('M d Y', $enDate) . "</h1><br />";

		//set up the table
		echo "<table border='1' class='table-sm' style='table-layout: '>
					
							<form action='reports.php' method='post' id='staffForm'>
								<input type='hidden' name='sortBy' value='staff' />
								<input type='hidden' name='start' value='$st' />
								<input type='hidden' name='end' value='$end' />
								<input type='hidden' name='staff' value='$staff' />
							</form>
							<form action='reports.php' method='post' id='clientForm'>
								<input type='hidden' name='sortBy' value='client' />
								<input type='hidden' name='start' value='$st' />
								<input type='hidden' name='end' value='$end' />
								<input type='hidden' name='staff' value='$staff' />
							</form>
							<form action='reports.php' method='post' id='depForm'>
								<input type='hidden' name='sortBy' value='dep' />
								<input type='hidden' name='start' value='$st' />
								<input type='hidden' name='end' value='$end' />
								<input type='hidden' name='staff' value='$staff' />
							</form>
							
							<tr>
							<th><button style='background: none;
												border: none;
												padding: 0;
												color: white;
												font-weight: bold;
												text-decoration: none;
												cursor: pointer;' type='submit' form='staffForm' name='gen' value='Client'>Staff</button></th>
							<th><button style='background: none;
												border: none;
												padding: 0;
												color: white;
												font-weight: bold;
												text-decoration: none;
												cursor: pointer;' type='submit' form='clientForm' name='gen' value='Client'>Client</button></th>
							<th><button style='background: none;
												border: none;
												padding: 0;
												color: white;
												font-weight: bold;
												text-decoration: none;
												cursor: pointer;' type='submit' form='depForm' name='gen' value='Client'>Department</button></th>
							<th>Total Hours</th>";

		$min = $stDate;
		$max = $enDate;
		
		//set up the headings for the table
		$d = $min;
		while($d <= $max)
		{
			$num = date('d', $d);
			echo "<th>$num</th>";
			$d = strtotime('+1 day', $d);
		}
	
		//if there are no shifts, don't show any shifts
		if(sizeof($row) == 0)
		{
			echo "There are no shifts to show.";
		}
		
		else
		{
			$lastcli = '';	//lastcli keeps track of the last client that had shifts printed
			$laststa = '';	//laststa keeps track of the last staff that had shifts printed
			$tempmin = 0;	//tempmin keeps track of the earliest date for the timesheet
			$i = 0;			//i is used to iterate through records
			while ($i < sizeof($row))
			{
			
				//if the record is for a different client than the last record
			if(($lastcli != $row[$i]['CLIENT_ID']) || ($laststa != $row[$i]['STAFF_ID']))
				{
					$tempmin = $min;
					$totHours = 0;

					//set up the row
					echo "<tr>";
					echo "<td>ID({$row[$i]['STAFF_ID']}) {$row[$i]['STAFF_LNAME']}, {$row[$i]['STAFF_FNAME']}</td>";
					echo "<td>CID({$row[$i]['CLIENT_ID']}) {$row[$i]['CLIENT_LNAME']}, {$row[$i]['CLIENT_FNAME']}</td>";
					echo "<td>{$row[$i]['DEP_NAME']}</td>";
					$totalId = $row[$i]['STAFF_ID'] . '_' . $row[$i]['CLIENT_ID'] . 'tot';
					echo "<td id='$totalId'></td>";
					
					

					$j = $i;
					//starting with the current record, until the client is different
					do
					{
						//set the date for the shift
						$date = strtotime($row[$j]['SHIFT_DATE']);
						//until the shift date, fill the timesheet with blank table data
						while($tempmin < $date)
						{
							echo "<td></td>";
							$tempmin = strtotime('+1 day', $tempmin);
						}
						//once the date is reached, print the number of hours
						$numHours = getHours($row[$j]['APPROVED_START'], $row[$j]['APPROVED_END']);
						
						if($j < sizeof($row))
						{
							while ($date == strtotime($row[$j+1]['SHIFT_DATE']))
							{
								$date = strtotime($row[$j+1]['SHIFT_DATE']);
								$numHours = $numHours + getHours($row[$j+1]['APPROVED_START'], $row[$j+1]['APPROVED_END']);
								$j++;
							}
						}

						$totHours = $totHours + $numHours;
						echo "<td>$numHours</td>";
						
						$tempmin = strtotime('+1 day', $tempmin);

						$j++;
						//if the next record is the last one, break out of the loop
						if($j >= sizeof($row))
							break;
					} while (($row[$j]['CLIENT_ID'] == $row[$j-1]['CLIENT_ID']) && ($row[$j]['STAFF_ID'] == $row[$j-1]['STAFF_ID']));

					//fill the timesheet with blank table data until the end of the table
					while($tempmin <= $max)
					{
						echo "<td></td>";
						$tempmin = strtotime('+1 day', $tempmin);
					}
					//when all of the start times for that client are filled in, end the row
					echo "</tr>";

					//Fill in the total field
					?>
					<script>
						var totalId = <?php echo "'$totalId'"; ?>;
						var totHours = <?php echo $totHours; ?>;
						setTotal(totalId, totHours);

						//test
						//document.getElementById("17_6tot").innerHTML = "Test";
					</script>
					<?php

					//set the last client as lastcli, so that their shifts won't be displayed again
					$lastcli = $row[$i]['CLIENT_ID'];
					$laststa = $row[$i]['STAFF_ID'];
				}

				$i++;
			}
		}
		
		//end the table
		echo "</table><br />\n";
		
		//releasing database resources
		if(isset($conn) )
		{
			$conn = null;
		}
	}
}

echo "<a href='../land.php' class='btn btn-secondary'>Back to Home</a>
	</div>
</div></div>
</div>";

include "../includes/scripts/footer.php";
?>
</body>
</html>
