
<?php
/*  Developer:   Justin Alho
 *  File Name:   modrecshift.php
 *  Description: Allows coordinators to modify existing recurring shift records
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
             <title>Modify Recurring Shifts</title>
	<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="../css/table.css" rel="stylesheet" type="text/css">

			<script>
				//fill dropdown with client results
				function cliSearch(search) {
					if (window.XMLHttpRequest)
					{
						xmlhttp = new XMLHttpRequest();
					}
					xmlhttp.onreadystatechange = function() {
						if (this.readyState == 4 && this.status == 200) {
							document.getElementById(\'cliSel\').innerHTML = this.responseText;
						}
					};
					xmlhttp.open(\'GET\',\'../includes/scripts/search.php?c=\'+search,true);
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
							document.getElementById(\'staSel\').innerHTML = this.responseText;
						}
					};
					xmlhttp.open(\'GET\',\'../includes/scripts/search.php?s=\'+search,true);
					xmlhttp.send();
				}
			</script>
			
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
<body>';
        
		 
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
		
			//initialize variables
			$id = '';
			$dep = '';
			$client = '';
			$staff = '';
			$start = '';
			$end = '';
			$super = '';
			$notes = '';
			
			$starterr = '';
			$enderr = '';
			
			//if the form has been submitted
			if(isset($_POST['submit']))
			{	
				//set error counter to 0
				$err = 0;
				
				//set variables to submitted values
				$id = $_POST['id'];
				$dep = $_POST['dep'];
				$client = $_POST['client'];
				$staff = $_POST['staff'];
				$start = $_POST['start'];
				$end = $_POST['end'];
				if(isset($_POST['super']))
					$super = 1;
				else
					$super = 0;
				$notes = $_POST['notes'];
				
				//if start or end times are blank, set the corresponding error message and increment error counter
				if($start == '')
				{
					$starterr = 'Please enter a valid start time.';
					$err++;
				}
				if($end == '' || strtotime($end) < strtotime($start))
				{
					$starterr = 'Please enter a valid end time.';
					$err++;
				}

				//if there are no errors, add information into database
				if($err == 0)
				{
					//update recurring shift record
					$sql = $conn->prepare("UPDATE REC_SHIFT SET DEP_CODE = '$dep', CLIENT_ID = '$client', STAFF_ID = '$staff', REC_START = '$start', REC_END = '$end', REC_SUPER = '$super', REC_NOTES = '$notes'
					WHERE REC_ID = '$id'");

					$sql->execute();

					//update every scheduled shift record created from that recurring shift record
					$shsql = $conn->prepare("UPDATE SHIFT SET DEP_CODE = '$dep', CLIENT_ID = '$client', STAFF_ID = '$staff', SCHEDULED_START = '$start', SCHEDULED_END = '$end', SHIFT_SUPER = '$super', SHIFT_NOTES = '$notes'
					WHERE REC_ID = '$id' AND STATUS_CODE = 'S'");
					
					//retrieve client information from database for logging
					$exeParams = array($client);
					$sql2 = $conn->prepare("
											SELECT CLIENT_FNAME, CLIENT_LNAME
											FROM CLIENT
											WHERE CLIENT_ID = ?
										");
					
					$shsql->execute();
					$sql2->execute($exeParams);
					
					$row = $sql2->fetch();
					
					//log when database is updated
					date_default_timezone_set("US/Mountain");
					//F j, Y, g:i a
					$dateString = date("r");
					file_put_contents("../logs/recShiftModLog.txt", "\n" . "Recurring shift for " . $row['CLIENT_FNAME'] . " " . $row['CLIENT_LNAME'] . " from " . $start . " to " . $end . " was modified on: " . $dateString . " by " . $_SESSION['userName'] . ".", FILE_APPEND | LOCK_EX) ;
					
					//echo implode(":",$sql->errorInfo()) . "<br>";
					
					//send user back to list of recurring shifts with success message
					header("Location: viewrecshift.php?p=1");
				}
			}
			
			//set ID variable to ID sent by viewrecshift.php
			$id = $_REQUEST['id'];
					
			//retrieve information for selected recurring shift from database
			$sql = $conn->prepare("SELECT REC_ID, REC_SHIFT.DEP_CODE, DEP_NAME, REC_SHIFT.CLIENT_ID, CLIENT_FNAME, CLIENT_LNAME, REC_SHIFT.STAFF_ID, STAFF_FNAME, STAFF_LNAME, REC_DAY, REC_START, REC_END, REC_SUPER, REC_NOTES
			FROM REC_SHIFT
			LEFT JOIN DEPARTMENT
			ON REC_SHIFT.DEP_CODE = DEPARTMENT.DEP_CODE
			LEFT JOIN CLIENT
			ON REC_SHIFT.CLIENT_ID = CLIENT.CLIENT_ID
			LEFT JOIN STAFF
			ON REC_SHIFT.STAFF_ID = STAFF.STAFF_ID
			WHERE REC_ID = '$id'");
				
			$sql->execute();
			
			$row = $sql->fetch();
			//echo implode(":",$sql->errorInfo()) . "<br>";
			
			//retrieve list of departments from database
			$depsql = $conn->prepare("SELECT DEP_CODE, DEP_NAME FROM DEPARTMENT WHERE DEP_CODE != '{$row['DEP_CODE']}'");
			
			$depsql->execute();
			
			$deprow = $depsql->fetchAll();
			
			//retrieve list of clients from database
			$clisql = $conn->prepare("SELECT CLIENT_ID, CLIENT_FNAME, CLIENT_LNAME FROM CLIENT
			WHERE CLIENT_ID != '{$row['CLIENT_ID']}'
			AND CLIENT_STATUS='A'");
			
			$clisql->execute();
			
			$clirow = $clisql->fetchAll();
			
			//retrieve list of staff from database
			$stfsql = $conn->prepare("SELECT STAFF_ID, STAFF_FNAME, STAFF_LNAME FROM STAFF WHERE STAFF_ID != '{$row['STAFF_ID']}'
			AND STAFF_STATUS = 'A'
			AND (TYPE_CODE = 'S'
			OR TYPE_CODE = 'W')");
			
			$stfsql->execute();
			
			$stfrow = $stfsql->fetchAll();
			
			//include navbar
			include "../includes/scripts/navBar.php";
				echo'<div class="conb">';
			echo'<div class="row justify-content-sm-center">';
			
			//display the form
			printf("

				

				<form class='form-con' method='post' action='modrecshift.php'>
				<h1>Modify a Recurring Shift</h1>

					<input class='form-fan' type='hidden' name='id' value='$id'>
					
					Shift Day: {$row['REC_DAY']} <br /><br />
						
					Search for Client:
						<input class='form-fan' onkeyup='cliSearch(this.value)'><br /><br />\n

					<!--display selection of clients-->
					Client Results:
						<select class='fanc' name='client' id='cliSel'>
							<option value='{$row['CLIENT_ID']}'>CID({$row['CLIENT_ID']}) {$row['CLIENT_FNAME']} {$row['CLIENT_LNAME']}</option>");
			foreach($clirow as $data)
				echo "<option value='{$data['CLIENT_ID']}'>CID({$data['CLIENT_ID']}) {$data['CLIENT_FNAME']} {$data['CLIENT_LNAME']}</option>";
			printf("
						</select><br /><br />\n

					<!--display selection of departments-->
					Department:
						<select class='fanc' name='dep'>
							<option value='{$row['DEP_CODE']}'>{$row['DEP_NAME']}</option>");
			foreach($deprow as $data)
				echo "<option value='{$data['DEP_CODE']}'>{$data['DEP_NAME']}</option>";
			printf("
						</select><br /><br />\n

					Scheduled Start Time:
						<input class='form-fan' type='time' name='start' value='{$row['REC_START']}'><br /><br />\n

					Scheduled End Time:
						<input class='form-fan' type='time' name='end' value='{$row['REC_END']}'><br /><br />\n
						
					Search for staff:
						<input class='form-fan' onkeyup='staSearch(this.value)'><br /><br />\n

					<!--display selection of staff members-->
					Staff results:
						<select class='fanc' name='staff' id='staSel'>
							<option value='{$row['STAFF_ID']}'>ID({$row['STAFF_ID']}) {$row['STAFF_FNAME']} {$row['STAFF_LNAME']}</option>");
			foreach($stfrow as $data)
				echo "<option value='{$data['STAFF_ID']}'>ID({$data['STAFF_ID']}) {$data['STAFF_FNAME']} {$data['STAFF_LNAME']}</option>";
			printf("
						</select><br /><br />\n

					Supervisor:");
			if($row['REC_SUPER'] == 1)
				echo "<input type='checkbox' name='super' checked><br /><br />";
			else
				echo "<input type='checkbox' name='super'><br /><br />";
			printf("
			
					Shift Notes:<br />
						<textarea  name='notes' rows='3' cols='30'>{$row['REC_NOTES']}</textarea class='form-fan'><br /><br />
					
					<input type='submit' name='submit' value='Submit' class='btn btn-primary'>
					<a href='viewrecshift.php' class='btn btn-danger'>Cancel</a>

				</form>
				
				<!--cancel button that returns user to previous page-->
				
			");
						
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
			
echo'
</div>';
	include "../includes/scripts/footer.php";
	echo'
</body>
</html>
    ';
	?>