<?php
/*  Developer:   Justin Alho
 *  File Name:   modshift.php
 *  Description: Allows coordinators to modify existing shift records
 *  Date Start:  26/02/2020
 *  Date End:    TBD
 */?>
    <?php
echo'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
                   <title>Modify Shift</title>
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
        
        //retrieve list of available staff
        function checkAvail() {
            
            da = document.getElementById("dateInput").value;
            st = document.getElementById("stInput").value;
            en = document.getElementById("endInput").value;
            
            if(da == \'\' || st == \'\' || en == \'\' || en <= st) {
                alert("Please enter a valid date and time.");
            }
            
            else {
                if (window.XMLHttpRequest)
                {
                    xmlhttp = new XMLHttpRequest();
                }
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById(\'staSel\').innerHTML = this.responseText;
                    }
                };
                xmlhttp.open(\'GET\',\'getavail.php?date=\'+da+\'&start=\'+st+\'&end=\'+en,true);
                xmlhttp.send();
            }
        }
    </script>
    
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
			$status = '';
			$dep = '';
			$client = '';
			$staff = '';
			$date = '';
			$schStart = '';
			$schEnd = '';
			$claStart = '';
			$claEnd = '';
			$appStart = '';
			$appEnd = '';
			$super = '';
			$notes = '';
			
			$daterr = '';
			$sserr = '';
			$seerr = '';
			$cserr = '';
			$ceerr = '';
			$aserr = '';
			$aeerr = '';
			
			//if form has been submitted
			if(isset($_POST['submit']))
			{
				//set error counter to 0
				$err = 0;
				
				//set variables to submitted values
				$id = $_POST['id'];
				$status = $_POST['status'];
				$dep = $_POST['dep'];
				$client = $_POST['client'];
				$staff = $_POST['staff'];
				$date = $_POST['date'];
				$schStart = $_POST['schStart'];
				$schEnd = $_POST['schEnd'];
				$claStart = $_POST['claStart'];
				$claEnd = $_POST['claEnd'];
				$appStart = $_POST['appStart'];
				$appEnd = $_POST['appEnd'];
				if(isset($_POST['super']))
					$super = 1;
				else
					$super = 0;
				$notes = $_POST['notes'];
				
				//if required fields are blank, set the corresponding error message and increment error counter
				if($date == '')
				{
					$daterr = 'please enter a valid date.';
					$err++;
				}
				if($schStart == '')
				{
					$sserr = 'Please enter a valid start time.';
					$err++;
				}
				if($schEnd == '' || strtotime($schEnd) < strtotime($schStart))
				{
					$seerr = 'Please enter a valid end time.';
					$err++;
				}
				//claimed and approved shifts need claimed times
				if($status == 'C' || $status == 'A')
				{
					if($claStart == '')
					{
						$cserr = 'Please enter a valid start time.';
						$err++;
					}
					if($claEnd == '' || strtotime($claEnd) < strtotime($claStart))
					{
						$ceerr = 'Please enter a valid end time.';
						$err++;
					}
				}
				//approved shifts need approved times
				if($status == 'A')
				{
					if($appStart == '')
					{
						$aserr = 'Please enter a valid start time.';
						$err++;
					}
					if($appEnd == '' || strtotime($appEnd) < strtotime($appStart))
					{
						$aeerr = 'Please enter a valid end time.';
						$err++;
					}
				}
				
				//if there are no errors, add information into database
				if($err == 0)
				{
					$sql = $conn->prepare("UPDATE SHIFT SET STATUS_CODE = '$status', DEP_CODE = '$dep', CLIENT_ID = '$client', STAFF_ID = '$staff', SHIFT_DATE = '$date', SCHEDULED_START = '$schStart', SCHEDULED_END = '$schEnd',
					CLAIMED_START = '$claStart', CLAIMED_END = '$claEnd', APPROVED_START = '$appStart', APPROVED_END = '$appEnd', SHIFT_SUPER = '$super', SHIFT_NOTES = '$notes'
					WHERE SHIFT_ID = '$id'");
					$exeParams = array($client);
					
					//retrieve client information from database
					$sql2 = $conn->prepare("
											SELECT CLIENT_FNAME, CLIENT_LNAME
											FROM CLIENT
											WHERE CLIENT_ID = ?
										");
					
					$sql->execute();
					$sql2->execute($exeParams);
					$row = $sql2->fetch();
					
					//log when database is updated
					date_default_timezone_set("US/Mountain");
					//F j, Y, g:i a
					$dateString = date("r");
					file_put_contents("../logs/shiftModLog.txt", "\n" . "Shift for " . $row['CLIENT_FNAME'] . " " . $row['CLIENT_LNAME'] . " on " . $date . " from " . $schStart . " to " . $schEnd . " was modified on: " . $dateString . " by " . $_SESSION['userName'] . ".", FILE_APPEND | LOCK_EX) ;
					
					//echo implode(":",$sql->errorInfo()) . "<br>";
					
					//send user back to viewshift page with success message
					header("Location: viewshift.php?p=1");
				}
			}
			
			//set ID variable to ID sent by viewrecshift.php
			$id = $_REQUEST['id'];
			
			//retrieve shift information from database
			$sql = $conn->prepare("SELECT SHIFT_ID, SHIFT.STATUS_CODE, STATUS_NAME, SHIFT.DEP_CODE, DEP_NAME, SHIFT.CLIENT_ID, CLIENT_FNAME, CLIENT_LNAME, SHIFT.STAFF_ID, STAFF_FNAME, STAFF_LNAME, SHIFT_DATE,
			SCHEDULED_START, SCHEDULED_END, CLAIMED_START, CLAIMED_END, APPROVED_START, APPROVED_END, SHIFT_SUPER, SHIFT_NOTES
			FROM SHIFT
			LEFT JOIN SHIFT_STATUS
			ON SHIFT.STATUS_CODE = SHIFT_STATUS.STATUS_CODE
			LEFT JOIN DEPARTMENT
			ON SHIFT.DEP_CODE = DEPARTMENT.DEP_CODE
			LEFT JOIN CLIENT
			ON SHIFT.CLIENT_ID = CLIENT.CLIENT_ID
			LEFT JOIN STAFF
			ON SHIFT.STAFF_ID = STAFF.STAFF_ID
			WHERE SHIFT_ID = '$id'");
				
			$sql->execute();
			
			$row = $sql->fetch();
			//echo implode(":",$sql->errorInfo()) . "<br>";
			
			//retrieve status information from database
			$stasql = $conn->prepare("SELECT * FROM SHIFT_STATUS WHERE STATUS_CODE != '{$row['STATUS_CODE']}'");
			
			$stasql->execute();
			
			$starow = $stasql->fetchAll();
			
			//retrieve department information from database
			$depsql = $conn->prepare("SELECT DEP_CODE, DEP_NAME FROM DEPARTMENT WHERE DEP_CODE != '{$row['DEP_CODE']}'");
			
			$depsql->execute();
			
			$deprow = $depsql->fetchAll();
			
			//retrieve client information from database
			$clisql = $conn->prepare("SELECT CLIENT_ID, CLIENT_FNAME, CLIENT_LNAME FROM CLIENT
			WHERE CLIENT_ID != '{$row['CLIENT_ID']}'
			AND CLIENT_STATUS='A'");
			
			$clisql->execute();
			
			$clirow = $clisql->fetchAll();
			
			//retrieve staff information from database
			$stfsql = $conn->prepare("SELECT STAFF_ID, STAFF_FNAME, STAFF_LNAME FROM STAFF
			WHERE STAFF_ID != '{$row['STAFF_ID']}'
			AND STAFF_STATUS = 'A'
			AND (TYPE_CODE = 'S'
			OR TYPE_CODE = 'W')");
			
			$stfsql->execute();
			
			$stfrow = $stfsql->fetchAll();
			
			//include navbar
			include "../includes/scripts/navBar.php";
			echo'<div class="container">';
			echo'<div class="row justify-content-sm-center">';
			
			//display form
			printf("

				<form class='form-con' method='post' action='modshift.php'>
				<h1>Modify a Shift &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp</h1>
					<input class='form-fan' type='hidden' name='id' value='$id'>
					Search for Client:
						<input class='form-fan' type='' onkeyup='cliSearch(this.value)'><br /><br />\n

					<!--display selection of clients-->
					Client Results:
						<select class='fanc' id='cliSel' name='client'>
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
						
					<!--display selection of statuses-->
					Shift Status:
						<select class='fanc' name='status'>
							<option value='{$row['STATUS_CODE']}'>{$row['STATUS_NAME']}</option>");
			foreach($starow as $data)
				echo "<option value='{$data['STATUS_CODE']}'>{$data['STATUS_NAME']}</option>";
			print("
						</select><br /><br />\n
					
					Shift Date:
						<input class='form-fan' id='dateInput' type='date' name='date' value='{$row['SHIFT_DATE']}'>$daterr<br /><br />\n

					Scheduled Start Time:
						<input class='form-fan' id='stInput' type='time' name='schStart' value='{$row['SCHEDULED_START']}'>$sserr<br /><br />\n

					Scheduled End Time:
						<input class='form-fan' id='endInput' type='time' name='schEnd' value='{$row['SCHEDULED_END']}'>$seerr<br /><br />\n");
						
			if($row['STATUS_CODE'] == 'C' || $row['STATUS_CODE'] == 'A')
			{
				echo "
					Claimed Start Time:
						<input class='form-fan' type='hidden' name='claStart' value='{$row['CLAIMED_START']}'>
						<input class='form-fan' type='time' name='cStart' value='{$row['CLAIMED_START']}' disabled>$cserr<br /><br />\n

					Claimed End Time:
						<input class='form-fan' type='hidden' name='claEnd' value='{$row['CLAIMED_END']}'>
						<input class='form-fan' type='time' name='cEnd' value='{$row['CLAIMED_END']}' disabled>$ceerr<br /><br />\n";
			}
			
			else
			{
				echo "
				<input type='hidden' name='claStart' value='{$row['CLAIMED_START']}'>
				<input type='hidden' name='claEnd' value='{$row['CLAIMED_END']}'>";
			}
			
			if($row['STATUS_CODE'] == 'A')
			{
				echo "
					Approved Start Time:
						<input class='form-fan' type='time' name='appStart' value='{$row['APPROVED_START']}'>$aserr<br /><br />\n

					Approved End Time:
						<input class='form-fan' type='time' name='appEnd' value='{$row['APPROVED_END']}'>$aeerr<br /><br />\n";
			}
			
			else
			{
				echo "
				<input type='hidden' name='appStart' value='{$row['APPROVED_START']}'>
				<input type='hidden' name='appEnd' value='{$row['APPROVED_END']}'>";
			}
			
			print("
					<!--button to find available staff-->
					<button class='btn btn-primary' id='availBttn' onclick='checkAvail();return false;'>View Available Staff</button><br /><br />
					
					Search for staff:
						<input class='form-fan' type='text' onkeyup='staSearch(this.value)'><br /><br />\n

					<!--display selection of staff-->
					Staff results:
						<select class='fanc' id='staSel' name='staff'>
							<option value='{$row['STAFF_ID']}'>ID({$row['STAFF_ID']}) {$row['STAFF_FNAME']} {$row['STAFF_LNAME']}</option>");
			foreach($stfrow as $data)
				echo "<option value='{$data['STAFF_ID']}'>ID({$data['STAFF_ID']}) {$data['STAFF_FNAME']} {$data['STAFF_LNAME']}</option>";
			printf("
						</select><br /><br />\n

					Supervisor: ");
			if($row['SHIFT_SUPER'] == 1)
				echo "<input type='checkbox' name='super' checked><br /><br />";
			else
				echo "<input type='checkbox' name='super'><br /><br />";
			printf("
			
					Shift Notes:<br />
						<textarea name='notes' rows='3' cols='30'>{$row['SHIFT_NOTES']}</textarea><br /><br />
					
					<input  type='submit' name='submit' value='Submit' class='btn btn-primary'>
								<!--cancel button that returns user to previous page-->
				<a href='viewshift.php' class='btn btn-danger'>Cancel</a>

				</form>
			");
						
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
			
			//include footer
echo'
</div></div>';
	include "../includes/scripts/footer.php";
	echo'
</body>
</html>
    ';
	?>