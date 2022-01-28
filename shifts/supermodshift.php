<?php
/*  Developer:   Justin Alho
 *  File Name:   supermodshift.php
 *  Description: Allows supervisors to select a group home they supervise to modify shifts for that group home
 *  Date Start:  15/03/2020
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
			$authLevel = "S";
			
			//to verify the user 
			include "../includes/functions/verLogin.php";
			verLogin();
			
			//to verify the user's type
			include "../includes/functions/valUserType.php";
			valUserType($authLevel);

            //connect to the database
			include "../dbseckey.php";
            $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);
		
			//initialize the ID variable
			$id = '';
			
			//if the form has been submitted
			if(isset($_POST['submit']))
			{
				//set variables to submitted values
				$id = $_POST['id'];
				$staff = $_POST['staff'];
				$notes = $_POST['notes'];
				
				$sql = $conn->prepare("UPDATE SHIFT SET STAFF_ID = '$staff', SHIFT_NOTES = '$notes' WHERE SHIFT_ID = '$id'");
				$sql->execute();
				
				//retrieve client info from the database
				$sql2 = $conn->prepare("
										SELECT CLIENT_FNAME, CLIENT_LNAME
										FROM CLIENT
										RIGHT JOIN SHIFT
										ON CLIENT.CLIENT_ID = SHIFT.CLIENT_ID
										WHERE SHIFT_ID = ?
									");
				$exeParams = array($id);
				$sql2->execute($exeParams);
				$row = $sql2->fetch();
				
				//log when database is updated
				date_default_timezone_set("US/Mountain");
				//F j, Y, g:i a
				$dateString = date("r");
				file_put_contents("../logs/shiftModLog.txt", "\n" . "Shift for " . $row['CLIENT_FNAME'] . " " . $row['CLIENT_LNAME'] . " on " . $date . " from " . $schStart . " to " . $schEnd . " was modified on: " . $dateString . " by " . $_SESSION['userName'] . ".", FILE_APPEND | LOCK_EX) ;
				
				//echo implode(":",$sql->errorInfo()) . "<br>";
				
				//send user to department selection screen with a success message
				header("Location: supermod.php?p=1");
			}
			
			//set ID variable to ID sent from supersched.php
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
			
			//retrieve list of staff from database
			$stfsql = $conn->prepare("SELECT STAFF_ID, STAFF_FNAME, STAFF_LNAME FROM STAFF
			WHERE STAFF_ID != '{$row['STAFF_ID']}'
			AND STAFF_STATUS = 'A'
			AND (TYPE_CODE = 'S'
			OR TYPE_CODE = 'W')");
			
			$stfsql->execute();
			
			$stfrow = $stfsql->fetchAll();
			
			//include navbar
			include "../includes/scripts/navBar.php";
			
			//display form, like regular modshift but only option is to change staff
					echo'<div class="conb">';
			echo'<div class="row justify-content-sm-center">
			<div class="form-con">';
		
			printf("
			

				

				<form  method='post' action='supermodshift.php'>
				<h1>Modify a Shift</h1>

					<input type='hidden' name='id' value='$id'>

					Group Home: {$row['DEP_NAME']}<br /><br />\n");
					
					//make the date more readable
					$dispdate = strtotime($row['SHIFT_DATE']);
                    echo "<input type='hidden' id='dateInput' value='{$row['SHIFT_DATE']}'>";
					echo "Shift Date: " . date('F d, Y', $dispdate) . "<br /><br />\n";
					
					//make start and end times more readable
					$dispst = strtotime($row['SCHEDULED_START']);
					echo "<input type='hidden' id='stInput' value='{$row['SCHEDULED_START']}'>";
					echo "Scheduled Start Time: " . date('g:i A', $dispst) . "<br /><br />\n";

					$dispend = strtotime($row['SCHEDULED_END']);
                    echo "<input type='hidden' id='endInput' value='{$row['SCHEDULED_END']}'>";
					echo "Scheduled End Time: " . date('g:i A', $dispend) . "<br /><br />\n";

                    print("
                    <!--button to find available staff-->
					<button class='btn btn-primary' id='availBttn' onclick='checkAvail();return false;'>View Available Staff</button><br /><br />

                       Search for staff:
						<input type='text' name='' value='' onkeyup='staSearch(this.value)'><br /><br />\n

					<!--display selection of staff-->
					Staff results:
						<select name='staff' id='staSel'>
							<option value='{$row['STAFF_ID']}'>{$row['STAFF_FNAME']} {$row['STAFF_LNAME']}</option>");

			foreach($stfrow as $data)
				echo "<option value='{$data['STAFF_ID']}'>{$data['STAFF_FNAME']} {$data['STAFF_LNAME']}</option>";
			printf("
						</select><br /><br />\n

					Supervisor: ");
			if($row['SHIFT_SUPER'] == 1)
				echo "<input type='checkbox' name='super' checked disabled><br /><br />";
			else
				echo "<input type='checkbox' name='super' disabled><br /><br />";
			printf("
			
					Shift Notes:<br />
						<textarea name='notes' rows='3' cols='30'>{$row['SHIFT_NOTES']}</textarea><br /><br />
					
					<input class='btn btn-primary' type='submit' name='submit' value='Submit'>

				</form><br />
				<form action='supersched.php' method='post'>
					<input type='hidden' name='id' value='{$row['DEP_CODE']}' />
				<input type='submit' name='cancel' value='Cancel' class='btn btn-danger' />
				</form>
			");
				echo'</div></div>
					</div>';
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
			
			//include footer
	echo'
</div>';
	include "../includes/scripts/footer2.php";
	echo'
</body>
</html>
    ';
	?>