<?php
/*  Developer:   Justin Alho
 *  File Name:   schedrecshift.php
 *  Description: Allows coordinators to create individual shift records from a master recurring shift record
 *  Date Start:  27/02/2020
 *  Date End:    TBD
 */?>
<?php
echo'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	        <title>Scedule Recurring Shift</title>
    <title>Table</title>
	<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="../css/table.css" rel="stylesheet" type="text/css">
	
	<style>
		.bodD
		{
			min-height: 85%;
		}
		html, body
		{
			height: 100%;
		}
		
	</style>

</head>
<body>
';
			//Starting a session and initilizing variables needed
			session_start(); 
			$userType = $_SESSION['userType'];
		
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

			//initialize error messages
			$sterr = '';
			$enderr = '';
			$deperr = '';

			date_default_timezone_set("US/Mountain");


			
			//schedule shifts for a department
			if(isset($_POST['submit']) && isset($_POST['dep']))
			{
				//set error counter to 0
				$err = 0;
				
				//set variables to submitted values
				$dep = $_POST['dep'];
				$start = $_POST['start'];
				$end = $_POST['end'];
				echo $start;
				echo $end;
				//if required fields are blank, set the corresponding error message and increment error counter
				if($dep == '')
				{
					$deperr = '<div class="badge badge-warning">Please select a department.</div>';
					$err++;
				}
				if($start == '')
				{
					$sterr = '<div class="badge badge-warning">Please enter a valid start date.</div>';
					$err++;
				}
				if($end == '' || $end < $start)
				{
					$enderr = '<div class="badge badge-warning">Please enter a valid end date.</div>';
					$err++;
				}
				//if there are no errors, create shifts
				if($err == 0)
				{
					//retrieve recurring shift information from database
					$sql = $conn->prepare("SELECT * FROM REC_SHIFT
					WHERE DEP_CODE = '$dep'");

					$sql->execute();
					
					$row = $sql->fetchAll();
					
					//for each recurring shift in that department
					foreach($row as $data)
					{
						//set variables to values from database
						$id = $data['REC_ID'];
						$dep = $data['DEP_CODE'];
						$client = $data['CLIENT_ID'];
						$staff = $data['STAFF_ID'];
						$timeSt = $data['REC_START'];
						$timeEnd = $data['REC_END'];
						$super = $data['REC_SUPER'];
						$notes = $data['REC_NOTES'];
						$day = $data['REC_DAY'];
						
						$date = $start;
						$last = $end;
						//While the date isn't the correct day of the week, add 1 day
						while(date('D', $date) != $day)
						{
							$date = strtotime('+1 day', $date);
						}
						//until the end date, schedule shifts
						while($date < $end)
						{
							//check to see if shift has already been created
							$shDate = date('y-m-d', $date);
							$checkSql = $conn->prepare("SELECT * FROM SHIFT
							WHERE REC_ID = '$id'
							AND SHIFT_DATE = '$shDate'");
							
							$checkSql->execute();
							$checkArr = $checkSql->fetchAll();
							
							//if there are no shifts that are already scheduled for that day
							if(sizeof($checkArr) == 0)
							{
								$shsql = $conn->prepare("INSERT INTO SHIFT (REC_ID, DEP_CODE, CLIENT_ID, STAFF_ID, SHIFT_DATE, SCHEDULED_START, SCHEDULED_END, SHIFT_SUPER, SHIFT_NOTES)
								VALUES ('$id', '$dep', '$client', '$staff', '$shDate', '$timeSt', '$timeEnd', '$super', '$notes')");
								
								//retrieve client information from database
								$exeParams = array($client);
								$sql2 = $conn->prepare("
													SELECT CLIENT_FNAME, CLIENT_LNAME
													FROM CLIENT
													WHERE CLIENT_ID = ?
												");

								$shsql->execute();
								$sql2->execute($exeParams);
							
								$row = $sql2->fetch();
								
								//log whenever database is updated
								//date_default_timezone_set("US/Mountain");
								//F j, Y, g:i a
								$dateString = date("r");
								file_put_contents("../logs/recShiftSchedLog.txt", "\n" . "Recurring shift for " . $row['CLIENT_FNAME'] . " " . $row['CLIENT_LNAME'] . " on " . $shDate . " from " . $timeSt . " to " . $timeEnd . " was scheduled on: " . $dateString . " by " . $_SESSION['userName'] . ".", FILE_APPEND | LOCK_EX) ;
								
								//echo implode(":",$shsql->errorInfo()) . "<br>";
							}
							
							//increment the date by a week for the next shift
							$date = strtotime('+1 week', $date);
						}
					}
					
					//send the user back to schedrecshift.php with an empty form and a success message
					//header("Location: schedrecshift.php?s=1");
				}
			}
			//include navbar
						include "../includes/scripts/navBar.php";
			echo'<div class="conb">';
			echo'<div class="row justify-content-sm-center">';
			
			//if recurring shift ID and start date are sent from schedshift.php
			if(isset($_REQUEST['recid']) && isset($_REQUEST['st']))
			{
				//set ID to ID sent from schedshift, start is set to date from schedshift
				$id = $_REQUEST['recid'];
				$start = $_REQUEST['st'];

				//display form to schedule recurring shifts
				printf("
				

					<form method='post' action='schedrecshift.php' class='form-con'>
					<h1>Schedule a Recurring Shift</h1>
						<input class='form-fan' type='hidden' name='id' value=''>
						
						Schedule Shifts From Start Date:
							<input class='form-fan' type='date' name='start' value=''><br /><br />\n

						To End Date:
							<input class='form-fan' type='date' name='end' value=''><br /><br />\n

						<input class='btn btn-primary' type='submit' name='submit' value='Submit' >
					
						<!--cancel button that returns user to previous page-->
						<a href='schedshift.php?c=1' class='btn btn-danger'>Cancel</a>
					</form>

				");
				
				//releasing database resources
				if(isset($conn) )
				{
					$conn = null;
				}
				
				//include footer
				include "../includes/scripts/footer.php";
				//end page
				die();
			}
			
			//select list of departments from database
			$depsql = $conn->prepare("SELECT DEP_CODE, DEP_NAME FROM DEPARTMENT");
			
			$depsql->execute();
			
			$deprow = $depsql->fetchAll();
			
			
			
			//display form to select department
			printf("
			
				<form class='form-con' method='post' action='schedrecshift.php'>
				");
				
				//if recurring shifts have been scheduled successfully, display message
			if(isset($_REQUEST['s']))
				echo "<div class='alert alert-success'>Shift Scheduled successfully.</div>";
				
				
				printf("
				<h1>Schedule Recurring Shifts</h1>

					<!--display selection of departments-->
					Select a Department to Schedule Shifts For:
						<select class='fanc' name='dep'>
							<option value=''>Choose a Department</option>");
			foreach($deprow as $data)
				echo "<option value='{$data['DEP_CODE']}'>{$data['DEP_NAME']}</option>";
			printf("
						</select>$deperr<br /><br />\n
					
					From Start Date:
						<input class='form-fan' type='date' name='start' value='$start'>$sterr<br /><br />\n

					To End Date:
						<input class='form-fan' type='date' name='end' value='$end'>$enderr<br /><br />\n

					<input  type='submit' name='submit' value='Submit' class='btn btn-primary'>\n
					
								
					<!--cancel button that returns user to previous page-->
					<a href='/land.php' class='btn btn-danger'>Cancel</a>

				</form>
				
			");
						
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
			
echo'
            
</div>
</div>';
	include "../includes/scripts/footer.php";
	echo'
</body>
</html>
    ';