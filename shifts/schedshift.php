
<?php
/*  Developer:   Justin Alho
 *  File Name:   schedshift.php
 *  Description: Allows coordinators to schedule new shifts
 *  Date Start:  24/02/2020
 *  Date End:    TBD
 */?>
<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Schedule a New Shift</title>
		<?php
			//Starting a session and initializing variables needed
			session_start();
			$userType = $_SESSION['userType'];
		
			//include links to css, javascript, etc.
			include "../includes/scripts/headLinks2.0.php"; ?>
			
			<script>
				//fill dropdown with client results
				function cliSearch(search) {
					if (window.XMLHttpRequest)
					{
						xmlhttp = new XMLHttpRequest();
					}
					xmlhttp.onreadystatechange = function() {
						if (this.readyState == 4 && this.status == 200) {
							document.getElementById('cliSel').innerHTML = this.responseText;
						}
					};
					xmlhttp.open('GET','../includes/scripts/search.php?c='+search,true);
					xmlhttp.send();
				}
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
					xmlhttp.open('GET','../includes/scripts/search.php?s='+search,true);
					xmlhttp.send();
				}
				
				//retrieve list of available staff
				function checkAvail() {
					
					da = document.getElementById("dateInput").value;
					st = document.getElementById("stInput").value;
					en = document.getElementById("endInput").value;
					
					if(da == '' || st == '' || en == '' || en <= st) {
						alert("Please enter a valid date and time.");
					}
					
					else {
						if (window.XMLHttpRequest)
						{
							xmlhttp = new XMLHttpRequest();
						}
						xmlhttp.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								document.getElementById('staSel').innerHTML = this.responseText;
							}
						};
						xmlhttp.open('GET','getavail.php?date='+da+'&start='+st+'&end='+en,true);
						xmlhttp.send();
					}
				}
			</script>

    </head>

    <body>

        <?php
		
			//level of authorization required to access page
			$authLevel = "C";
			
			//to verify the user 
			include "../includes/functions/verLogin.php";
			verLogin();
			
			//to verify the user's type
			include "../includes/functions/valUserType.php";
			valUserType($authLevel);
			
			//initialize variables
			$dep = '';
			$client = '';
			$staff = '';
			$date = '';
			$start = '';
			$end = '';
			$super = '';
			$notes = '';
			
			$schedErr = '';
			$deperr = '';
			$clierr = '';
			$staerr = '';
			$daterr = '';
			$sterr = '';
			$enderr = '';

            //connect to the database
			include "../dbseckey.php";
            $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);
		
			//if the form has been submitted
			if(isset($_POST['submit']))
			{
				//set error counter to 0
				$err = 0;
				
				//if the box for recurring shift has been checked
				if(isset($_POST['rec']))
				{
					//set variables to submitted values
					$dep = $_POST['dep'];
					$client = $_POST['client'];
					$staff = $_POST['staff'];
					$date = strtotime($_POST['date']);
					$stdate = $_POST['date'];
					$day = date('D', $date);
					$start = $_POST['start'];
					$end = $_POST['end'];
					if(isset($_POST['super']))
						$super = 1;
					else
						$super = 0;
					$notes = $_POST['notes'];
					
					//if required fields are blank, set the corresponding error message and increment error counter
					if($dep == '')
					{
						$deperr = '<div class="badge badge-warning">Please select a department.</div>';
						$err++;
					}
					if($client == '')
					{
						$clierr = '<div class="badge badge-warning">Please select a client.</div>';
						$err++;
					}
					if($staff == '')
					{
						$staerr = '<div class="badge badge-warning">Please select a staff member.</div>';
						$err++;
					}
					if($date == '')
					{
						$daterr = '<div class="badge badge-warning">Please select a date.</div>';
						$err++;
					}
					if($start == '')
					{
						$sterr = '<div class="badge badge-warning">Please choose a start time.</div>';
						$err++;
					}
					if($end == '' || strtotime($end) < strtotime($start))
					{
						$enderr = '<div class="badge badge-warning">Please choose a valid end time.</div>';
						$err++;
					}
					
					//Ensuring that a recurring shift is unique before inserting it into the database.///////////////////////////////////////
					$qry = $conn->prepare("SELECT * FROM REC_SHIFT
					WHERE REC_DAY = '$day'
					AND REC_START = '$start'
					AND REC_END = '$end'
					AND STAFF_ID = '$staff'
					AND CLIENT_ID = '$client'
					");
				
					$qry->execute();
					
					$qryArray = $qry->fetchAll();
					
					//if there is already at least one shift with the same details, increment error counter and set error message
					if(sizeof($qryArray) > 0)
					{
						$err++;
						$schedErr = 'The recurring shift you are trying to schedule has already been created.<br /><br />';
					}
					
					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					
					//if there are no errors, create recurring shift record
					if($err == 0)
					{
						$sql = $conn->prepare("INSERT INTO REC_SHIFT (DEP_CODE, CLIENT_ID, STAFF_ID, REC_DAY, REC_START, REC_END, REC_SUPER, REC_NOTES)
						VALUES ('$dep', '$client', '$staff', '$day', '$start', '$end', '$super', '$notes')");
						
						$sql->execute();
						//echo implode(":",$sql->errorInfo());
						//retrieve the auto-generated recurrence ID
						$recid = $conn->lastInsertId();
						
						//send the user to schedrecshift.php with the ID of the recurring shift as well as the start date
						header("Location: schedrecshift.php?recid=$recid&st=$stdate");
						
						$exeParams = array($client);
						
						//retrieve client information from database
						$sql2 = $conn->prepare("
											SELECT CLIENT_FNAME, CLIENT_LNAME
											FROM CLIENT
											WHERE CLIENT_ID = ?
										");
						
						$sql2->execute($exeParams);
						
						$row = $sql2->fetch();
						
						//log every time database is updated
						date_default_timezone_set("US/Mountain");
						//F j, Y, g:i a
						$dateString = date("r");
						file_put_contents("../logs/shiftSchedLog.txt", "\n" . "Recurring shift for " . $row['CLIENT_FNAME'] . " " . $row['CLIENT_LNAME'] . " on " . $date . " from " . $start . " to " . $end . " was scheduled on: " . $dateString . " by " . $_SESSION['userName'] . ".", FILE_APPEND | LOCK_EX) ;
						
					}
				}
				//if the shift is not a recurring shift
				else
				{
					//set variables to submitted values
					$dep = $_POST['dep'];
					$client = $_POST['client'];
					$staff = $_POST['staff'];
					$date = $_POST['date'];
					$start = $_POST['start'];
					$end = $_POST['end'];
					if(isset($_POST['super']))
						$super = 1;
					else
						$super = 0;
					$notes = $_POST['notes'];

					//if required fields are blank, set the corresponding error message and increment error counter
					if($dep == '')
					{
						$deperr = '<div class="badge badge-warning">Please select a department.</div>';
						$err++;
					}
					if($client == '')
					{
						$clierr = '<div class="badge badge-warning">Please select a client.</div>';
						$err++;
					}
					if($staff == '')
					{
						$staerr = '<div class="badge badge-warning">Please select a staff member.</div>';
						$err++;
					}
					if($date == '')
					{
						$daterr = '<div class="badge badge-warning">Please select a date.</div>';
						$err++;
					}
					if($start == '')
					{
						$sterr = '<div class="badge badge-warning">Please choose a start time.</div>';
						$err++;
					}
					if($end == '' || strtotime($end) <= strtotime($start))
					{
						$enderr = '<div class="badge badge-warning">Please choose a valid end time.</div>';
						$err++;
					}
					
					//Ensuring that an individual shift is unique before inserting it into the database.///////////////////////////////////////
					$qry = $conn->prepare("SELECT * FROM SHIFT
					WHERE SHIFT_DATE = '$date'
					AND SCHEDULED_START = '$start'
					AND SCHEDULED_END = '$end'
					AND STAFF_ID = '$staff'
					AND CLIENT_ID = '$client'
					");
				
					$qry->execute();
					
					$qryArray = $qry->fetchAll();
					
					//if there is already at least one shift with the same details, increment error counter and set error message
					if(sizeof($qryArray) > 0)
					{
						$err++;
						$schedErr = '<div class="badge badge-warning">The shift you are trying to schedule has already been scheduled.</div><br /><br />';
					}
					
					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

					//if there are no errors, create shift record
					if($err == 0)
					{
						$sql = $conn->prepare("INSERT INTO SHIFT (DEP_CODE, CLIENT_ID, STAFF_ID, SHIFT_DATE, SCHEDULED_START, SCHEDULED_END, SHIFT_SUPER, SHIFT_NOTES)
						VALUES ('$dep', '$client', '$staff', '$date', '$start', '$end', '$super', '$notes')");
						$sql->execute();
						
						//retrieve client information from database
						$sql2 = $conn->prepare("
											SELECT CLIENT_FNAME, CLIENT_LNAME
											FROM CLIENT
											WHERE CLIENT_ID = ?
										");
										
						$exeParams = array($client);
						$sql2->execute($exeParams);
						
						$row = $sql2->fetch();
						//$day = date('D', $date);
						
						//log whenever database is updated
						date_default_timezone_set("US/Mountain");
						//F j, Y, g:i a
						$dateString = date("r");
						file_put_contents("../logs/shiftSchedLog.txt", "\n" . "Shift for " . $row['CLIENT_FNAME'] . " " . $row['CLIENT_LNAME'] . " on " . $date . " from " . $start . " to " . $end . " was scheduled on: " . $dateString . " by " . $_SESSION['userName'] . ".", FILE_APPEND | LOCK_EX) ;
						
						//echo implode(":",$sql->errorInfo());
						
						//send users back to this page with an empty form and a success message
						header("Location: schedshift.php?s=1");
					}
				}
			}
			
			//retrieve list of departments from database
			$depsql = $conn->prepare("SELECT * FROM DEPARTMENT");
			
			//retrieve list of clients from database
			$clisql = $conn->prepare("SELECT * FROM CLIENT
				where CLIENT_STATUS = 'A'
				ORDER BY CLIENT_LNAME ASC");
			
			//retrieve list of staff from database
			$stasql = $conn->prepare("SELECT * FROM STAFF
				where STAFF_STATUS = 'A'
				AND (TYPE_CODE = 'W'
				OR TYPE_CODE = 'S')
				ORDER BY STAFF_LNAME ASC");
				
			$depsql->execute();
			$deprow = $depsql->fetchAll();
			$clisql->execute();
			$clirow = $clisql->fetchAll();
			$stasql->execute();
			$starow = $stasql->fetchAll();
			
			//include navbar

					include "../includes/scripts/navBar.php";
					
			echo "<div class='conb'>";
			echo "<div class='row justify-content-md-center'>";

			echo "<br />";
			

					  
			//Test		  
			//print_r($qryArray2);
			
			//display the form
			printf("				

				<form class='form-con' method='post' action='schedshift.php'>");
				
				
							//if an individual shift was created successfully, display success message
			if(isset($_REQUEST['s']))
				echo "<div class='alert alert-success'>Shift scheduled successfully.</div>";
			//if recurring shifts were created successfully, display success message
			if(isset($_REQUEST['r']))
				echo "<div class='alert alert-success'>Shifts scheduled successfully.</div>";
			//if recurring shift record was created but shhifts were not created from it, display this message
			if(isset($_REQUEST['c']))
				echo "<div class='alert alert-success'>Recurring Shift Record created successfully.</div>
					  To schedule shifts based on it, go to 'Schedule Recurring Shifts'<br />
					  and select the department it was scheduled for.<br /><br />";
				
				printf("
				<h1>Schedule a New Shift</h1>
				$schedErr

				<form method='post' action='schedshift.php'>

					Search for Client:
						<input class='form-fan' type='text' name='' value='' onkeyup='cliSearch(this.value)'><br /><br />\n

					<!--display selection of clients-->
					Client Results:
						<select class='fanc' name='client' id='cliSel'>
							<option value=''>Select a Client:</option>");
			foreach($clirow as $data)
				echo "<option value='{$data['CLIENT_ID']}'>CID({$data['CLIENT_ID']}) {$data['CLIENT_LNAME']}, {$data['CLIENT_FNAME']}</option>";
			printf("
						</select>$clierr<br /><br />\n

					<!--display selection of departments-->
					Department:
						<select class='fanc' name='dep'>
							<option value=''>Select a Department:</option>");
			foreach($deprow as $data)
				echo "<option value='{$data['DEP_CODE']}'>{$data['DEP_NAME']}</option>";
			printf("
						</select>$deperr<br /><br />\n

					Shift Date:<div class='form-row'><div class='col-12'>
						<input class='form-fan' id='dateInput' type='date' name='date' value='$date'>$daterr<br /><br />\n
	
					</div></div>Recurring Shift:");
				if(isset($_POST['rec']))
					echo "<input class='form-fan' type='checkbox' name='rec' checked />";
				else
					echo "<input class='form-fan' type='checkbox' name='rec' /><br />";
				printf("
					A recurring shift will repeat once a week on the weekday selected.<br /><br />

					Start Time:<div class='form-row'><div class='col-12'>
						<input class='form-fan' id='stInput' type='time' name='start' value='$start'>$sterr<br /><br />\n

					</div></div>End Time:<div class='form-row'><div class='col-12'>
						<input class='form-fan' id='endInput' type='time' name='end' id='endInput' value='$end'>$enderr<br /><br />\n
						
					<!--button to find available staff-->
					</div></div><button class='btn btn-primary' id='availBttn' onclick='checkAvail();return false;'>View Available Staff</button><br /><br />

					Search for Staff:
						<input class='form-fan' type='text' name='' value='' onkeyup='staSearch(this.value)'><br /><br />\n

					<!--display selection of staff members-->
					Staff results:
						<select class='fanc' name='staff' id='staSel'>
							<option value=''>Select a Staff Member:</option>");
			foreach($starow as $data)
				echo "<option value='{$data['STAFF_ID']}'>ID({$data['STAFF_ID']}) {$data['STAFF_LNAME']}, {$data['STAFF_FNAME']}</option>";
			printf("
						</select>$staerr<br /><br />\n

						Supervisor: ");
				if(isset($_POST['super']))
					echo "<input class='form-fan' type='checkbox' name='super' checked><br /><br />";
				else
					echo "<input class='form-fan' type='checkbox' name='super'><br /><br />

					Shift Notes:<br />
						<textarea input class='form-fan' name='notes' rows='3' cols='30'>$notes</textarea><br /><br />\n

					<input class='form-fan' type='submit' name='submit' value='Submit' class='btn btn-primary'><br /><br />
					<a href='/land.php' class='btn btn-danger'>Cancel</a>

				</form><br />";
				
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
			
			print("</form>
            </div>");
			
			//include footer
			include "../includes/scripts/footer.php";
        ?>

    </body>

</html>