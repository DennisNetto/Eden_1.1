<?php
/*  Developer:   Justin Alho
 *  File Name:   revtime.php
 *  Description: Allows coordinators to review submitted timesheets and approve the hours
 *  Date Start:  11/03/2020
 *  Date End:    TBD
 */?>
 <?php
echo'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	         <title>Approve Timesheet</title>
	<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="../css/table.css" rel="stylesheet" type="text/css">
	


</head>
<body>
';

		
			//Starting a session and initializing variables needed
			session_start(); 
			$userType = $_SESSION['userType'];
		
			//include links to css, javascript, etc.
			include "../includes/scripts/headLinks2.0.php"; 
 
 
		//level of authorization required to access page
		$authLevel = "C";
		
		//to verify the user 
		include "../includes/functions/verLogin.php";
		verLogin();
		
		//to verify the user's type
		include "../includes/functions/valUserType.php";
		valUserType($authLevel);

		//connect to the database
		include "../dbseckey.php";
		$conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);
		
		//set ID and date variables to sent values
		$id = $_REQUEST['id'];
        $enDateSend = $_REQUEST['date'];
	    $enDate = strtotime($enDateSend);

		//gets the number of days in the month
		$days = date('t', $enDate);
	
		//if the date is before or equal to the 15th, set min to 1 and max to 15
		if(date('d', $enDate) <= 15)
		{
			$max = 15;
			$maxdate = date('Y-m-', $enDate) . '15';
			$min = 1;
			$mindate = date('Y-m-', $enDate) . '01';
		}
		//if the date is after the 15th, set max to $days and min to 16
		else
		{
			$max = $days;
			$maxdate  = date('Y-m-t', $enDate);
			$min = 16;
			$mindate = date('Y-m-', $enDate) . '16';
		}
	
		//if the final approve request has been submitted
		if (isset($_POST['submit']))
		{
			$id = $_REQUEST['id'];

			//select all the claimed shifts for the staff member
			$appSql = $conn->prepare("SELECT SHIFT_ID, STATUS_CODE, STAFF_ID
			FROM SHIFT
			WHERE STATUS_CODE = 'C'
			AND STAFF_ID = '$id'
			AND SHIFT_DATE >= '$mindate'
			AND SHIFT_DATE <= '$maxdate'");
			
			$appSql->execute();
			
			$row = $appSql->fetchAll();
			
			//update submitted shifts
			foreach ($row as $data)
			{
				$addSql = $conn->prepare("UPDATE SHIFT SET STATUS_CODE = 'A'
				WHERE SHIFT_ID = '{$data['SHIFT_ID']}'");
				
				$addSql->execute();
			}
			
			//after shifts have been approved, send the user back to approvetime.php with a success message
			header ("Location: approvetime.php?s=1");
		}
	
		//if the request to save approved hours has been submitted
		if(isset($_POST['save']))
		{
			//set variables to submitted values
			$sid = $_POST['sid'];
			$start = $_POST['appSt'];
			$end = $_POST['appEnd'];

			//if the submitted times are not proper, set variable to display error message
			if(strtotime($end) <= strtotime($start))
			{
				$t = 1;
			}
			//if the submitted times are proper
			else
			{
				//set approved hours, but ensure sift is still only claimed, not approved
				$addSql = $conn->prepare("UPDATE SHIFT SET APPROVED_START = '$start', APPROVED_END = '$end', STATUS_CODE = 'C'
				WHERE SHIFT_ID = '$sid'");
				$addSql->execute();
				
				//set s to 1 to send a success message to display to the user
				$s = 1;
			}
		}
	
		//include navbar
		include "../includes/scripts/navBar.php";
		echo'<div class="form-con"><div class="row justify-content-sm-center"><div class="form-con">';

        //select basic information about shifts, ordered first by client, then by date,
        //then by start time in case somehow the same staff is scheduled with the same client multiple times in one day
        $sql = $conn->prepare("SELECT SHIFT_ID, SHIFT_DATE, SCHEDULED_START, SCHEDULED_END, CLAIMED_START, CLAIMED_END, APPROVED_START, APPROVED_END, SHIFT.CLIENT_ID, CLIENT_LNAME, CLIENT_FNAME, SHIFT.STAFF_ID, STAFF_FNAME, STAFF_LNAME
					FROM SHIFT
					LEFT JOIN CLIENT
					ON SHIFT.CLIENT_ID = CLIENT.CLIENT_ID
					LEFT JOIN STAFF
					ON SHIFT.STAFF_ID = STAFF.STAFF_ID
					WHERE SHIFT_DATE >= '$mindate'
					AND SHIFT_DATE <= '$maxdate'
					AND SHIFT.STAFF_ID = '$id'
					AND STATUS_CODE = 'C'
					ORDER BY CLIENT_LNAME ASC, CLIENT_FNAME ASC, SHIFT_DATE ASC, SCHEDULED_START ASC");

		$sql->execute();

        $row = $sql->fetchAll();

		//if hours have been saved, display success message
		if(isset($s))
			echo "<div class='alert alert-success'>Approved hours saved successfully.</div>";
		//if approved hours are incorrect, display error message
		if(isset($t))
			echo "<div class='alert alert-warning'>Please enter a valid time.</div><br /><br />";

		//caption timesheet with staff name, month
		echo "<h1>Staff: ID({$row[0]['STAFF_ID']}) {$row[0]['STAFF_FNAME']} {$row[0]['STAFF_LNAME']} &nbsp &nbsp &nbsp &nbsp Month/Year: " . date('F/Y', $enDate) . "</h1>";
	
		//set approved counter to 0
		$app = 0;
		//for each record, if approved start or end are not set, increment approved counter
		foreach($row as $data)
		{
			if($data['APPROVED_START'] == '' || $data['APPROVED_END'] == '' || $data['APPROVED_START'] == '00:00:00' && $data['APPROVED_END'] == '00:00:00')
				$app++;
		}

		//echo implode(":",$sql->errorInfo());

		//if there are no shifts, don't show any shifts
		if(sizeof($row) == 0)
		{
			echo "There are no submitted hours.<br /><br />";
			echo "<a href='approvetime.php'><button>Back</button></a>";
			include "../includes/scripts/footer.php";
		}
		//if there are shifts to show
		else
		{
			//set up the table
			echo "<h5>Scheduled/Claimed Time:</h5>
					<table border='1'>
					<tr>
					<th>Individual Served</th>
					<th>Specifics</th>";
			
			//if the date is before the 15th, print the first half of the month

			for($i = $min; $i <= $max; $i++)
			{
				echo "<th>$i</th>";
			}

			echo "</tr>";
			
			$lastcli = '';	//lastcli keeps track of the last client that had shifts printed
			$tempmin = 0;	//tempmin keeps track of the earliest date for the timesheet
			$i = 0;			//i is used to iterate through records
			while ($i < sizeof($row))
			{
				//if the record is for a different client than the last record
				if($lastcli != $row[$i]['CLIENT_ID'])
				{
					$tempmin = $min;
					
					//set up the row
					echo "<tr>";
					echo "<td><b>Last:</b> {$row[$i]['CLIENT_LNAME']}</td>";
					echo "<td>Start Time:</td>";
					
					$j = $i;
					//starting with the current record, until the client is different
					do
					{
						//set the date for the shift
						$date = strtotime($row[$j]['SHIFT_DATE']);
						//until the shift date, fill the timesheet with blank table data
						while($tempmin < date('d', $date))
						{
							echo "<td></td>";
							$tempmin++;
						}
						
						//once the date is reached, print the start time
						
						//format times
						$schSt = strtotime($row[$j]['SCHEDULED_START']);
						$schSt = date('g:i A', $schSt);
						$claSt = strtotime($row[$j]['CLAIMED_START']);
						$claSt = date('g:i A', $claSt);
						
						echo "<td style='background-color: ";						
						//if shift is approved, set background colour to indicate that
						if($row[$j]['APPROVED_START'] && $row[$j]['APPROVED_END'] != '' && $row[$j]['APPROVED_START'] && $row[$j]['APPROVED_END'] != '00:00:00') 
							echo "#FFFFAA'";
						
						else{
							//if scheduled and claimed hours are the same, set background for record to green
							if ($schSt == $claSt)
							{
								echo "#AAFFAA'";
							}
							//if scheduled and claimed hours are different, set background for record to red
							else
							{
								echo "#FFAAAA'";
							}
						}
						
						//link back to this form with a request to approve hours
						echo "><a href='revtime.php?app={$row[$j]['SHIFT_ID']}&id=$id&date=$enDateSend'>$schSt/$claSt</a></td>";
						$tempmin++;
						
						$j++;
						//if the next record is the last one, break out of the loop
						if($j >= sizeof($row))
							break;
					} while ($row[$j]['CLIENT_ID'] == $row[$j-1]['CLIENT_ID']);
					
					//fill the timesheet with blank table data until the end of the table
					while($tempmin <= $max)
					{
						echo "<td></td>";
						$tempmin++;
					}
					//when all of the start times for that client are filled in, end the row 
					echo "</tr>";
					
					//reset tempmin, set up the next row
					$tempmin = $min;
					echo "<tr>";
					echo "<td><b>First:</b> {$row[$i]['CLIENT_FNAME']}</td>";
					echo "<td>End Time:</td>";
					
					$j = $i;
					//this loop is the same as the start time, except it prints the end time
					do
					{
						$date = strtotime($row[$j]['SHIFT_DATE']);
						while($tempmin < date('d', $date))
						{
							echo "<td></td>";
							$tempmin++;
						}
						
						//format times
						$schEnd = strtotime($row[$j]['SCHEDULED_END']);
						$schEnd = date('g:i A', $schEnd);
						$claEnd = strtotime($row[$j]['CLAIMED_END']);
						$claEnd = date('g:i A', $claEnd);
						
						echo "<td style='background-color: ";						
						//if shift is approved, set background colour to indicate that
						if($row[$j]['APPROVED_START'] && $row[$j]['APPROVED_END'] != '' && $row[$j]['APPROVED_START'] && $row[$j]['APPROVED_END'] != '00:00:00')
							echo "#FFFFAA'";
							
						else{
							//if scheduled and claimed hours are the same, set background for record to green
							if ($schEnd == $claEnd)
							{
								echo "#AAFFAA'";
							}
							//if scheduled and claimed hours are different, set background for record to red
							else
							{
								echo "#FFAAAA'";
							}
						}

						
						
						
						//link back to this form with a request to approve hours
						echo "><a href='revtime.php?app={$row[$j]['SHIFT_ID']}&id=$id&date=$enDateSend'>$schEnd/$claEnd</a></td>";
						$tempmin++;
						
						$j++;
						if($j >= sizeof($row))
							break;
					} while ($row[$j]['CLIENT_ID'] == $row[$j-1]['CLIENT_ID']);
					while($tempmin <= $max)
					{
						echo "<td></td>";
						$tempmin++;
					}
					echo "</tr>";
					
					//set the last client as lastcli, so that their shifts won't be displayed again
					$lastcli = $row[$i]['CLIENT_ID'];
				}
				
				$i++;
			}
			
			//end the table
			echo "</table><br /><br />\n";
			
			//if the request is set to approve hours
			if(isset($_REQUEST['app']))
			{
				$sid = $_REQUEST['app'];
				$t = 1;
			}
			
			//if the hours for a specific shift need to be approved
			if(isset($t))
			{
				//retrieve hours for requested shift from database
				$shsql = $conn->prepare("SELECT SHIFT_ID, SCHEDULED_START, SCHEDULED_END, CLAIMED_START, CLAIMED_END, APPROVED_START, APPROVED_END, SHIFT_NOTES
					FROM SHIFT
					WHERE SHIFT_ID = $sid");
					
				$shsql->execute();
				$row = $shsql->fetch();
				
				//format times
				$schSt = strtotime($row['SCHEDULED_START']);
				$schSt = date('g:i A', $schSt);
				$schEnd = strtotime($row['SCHEDULED_END']);
				$schEnd = date('g:i A', $schEnd);
				$claSt = strtotime($row['CLAIMED_START']);
				$claSt = date('g:i A', $claSt);
				$claEnd = strtotime($row['CLAIMED_END']);
				$claEnd = date('g:i A', $claEnd);
				
				//display table with scheduled, claimed, and a spot for approved hours
				echo "<form action='revtime.php' method='post'>
					<input type='hidden' name='id' value='$id'>
					<input type='hidden' name='sid' value='$sid'>
					<input type='hidden' name='date' value='$enDateSend'>
					<table>
						<tr>
							<th></th>
							<th>Scheduled:</th>
							<th>Claimed:</th>
							<th>Approved:</th>
						</tr>
						<tr>
							<th>Start:</th>
							<td>$schSt</td>
							<td>$claSt</td>
							<td><input type='time' name='appSt' value='{$row['APPROVED_START']}'></td>
						</tr>
						<tr>
							<th>End:</th>
							<td>$schEnd</td>
							<td>$claEnd</td>
							<td><input type='time' name='appEnd' value='{$row['APPROVED_END']}'></td>
						</tr>
						<tr>
							<th>Notes:</th>
							<td colspan='3'>{$row['SHIFT_NOTES']}</td>
					</table><br />
					
					<input type='submit' name='save' value='Save' class='btn btn-primary'><br />
				</form><br />";
			}
			
			//set up the form to do the final hour submission
			echo "<form action='revtime.php' method='post'>
				<input type='hidden' name='id' value='$id'>
			    <input type='hidden' name='date' value='$enDateSend'>";
				
			//This is the final approval button, can only be presses once all shifts have approved hours
			if($app == 0)
				echo "<input type='submit' name='submit' value='Approve Hours' class='btn btn-primary'/>";
			else
				echo "<input type='submit' name='notSubmit' value='Approve Hours' disabled/>";
			
			echo "</form><br /><br />";
			
			//cancel button
			echo "<a class='btn btn-danger' href='approvetime.php'>Cancel</a>";
			
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
			
			//include footer
		}
echo'
         

    </div>
</div>';
	include "../includes/scripts/footer2.php";
	echo'
</body>
</html>
    ';
	?>
