<?php
/*  Developer:   Justin Alho, Harley Lenton
 *  File Name:   addstaff.php
 *  Description: Allows coordinators to add new staff records into the database
 *  Date Start:  23/02/2020
 *  Date End:    TBD
 */?>
<?php
echo'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Add New Staff Member</title>
<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="../css/table.css" rel="stylesheet" type="text/css">
				<script>
				//change whether or nor availability is displayed depending on staff type
				function showAvail(type) {
					if (window.XMLHttpRequest)
					{
						xmlhttp = new XMLHttpRequest();
					}
					xmlhttp.onreadystatechange = function() {
						if (this.readyState == 4 && this.status == 200) {
							document.getElementById("availBox").innerHTML = this.responseText;
						}
					};
					xmlhttp.open("GET","addstaff.php?change="+type,true);
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
			#Starting a session and initilizing variables needed
	
			session_start();
			$userType = $_SESSION['userType'];
					include "../includes/scripts/headLinks2.0.php"; 
					include "../includes/functions/isSpecial.php";
					include "../includes/functions/isComplex.php";






        
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
			$type = '';
			$uname = '';
			$pass1 = '';
			$pass2 = '';
			$fname = '';
			$lname = '';
			$phone = '';
			$address = '';
			$city = '';
			$child = '';
			$pc = '';
			$drive = '';
		

			
			$sunAvail = '';
			$sunSt = '';
			$sunEnd = '';
			$monAvail = '';
			$monSt = '';
			$monEnd = '';
			$tueAvail = '';
			$tueSt = '';
			$tueEnd = '';
			$wedAvail = '';
			$wedSt = '';
			$wedEnd = '';
			$thuAvail = '';
			$thuSt = '';
			$thuEnd = '';
			$friAvail = '';
			$friSt = '';
			$friEnd = '';
			$satAvail = '';
			$satSt = '';
			$satEnd = '';
			$notes = '';
			
			$typerr = '';
			$unerr = '';
			$paserr = '';
			$fnerr = '';
			$lnerr = '';
		
			//if the form has been submitted
			if(isset($_POST['submit']))
			{	
				//set the error counter to 0
				$err = 0;
				
				//set variables to submitted values
				$type = $_POST['type'];
				$uname = $_POST['uname'];
				$pass1 = $_POST['pass1'];
				$pass2 = $_POST['pass2'];
				$fname = $_POST['fname'];
				$lname = $_POST['lname'];
				$phone = $_POST['phone'];
				$address = $_POST['address'];
				$city = $_POST['city'];
				if(isset($_POST['child']))
					$child = 1;
				else
					$child = 0;
				if(isset($_POST['pc']))
					$pc = 1;
				else
					$pc = 0;
				if(isset($_POST['drive']))
					$drive = 1;
				else
					$drive = 0;
				//if the staff is a worker or coordinator, save their availability
				if($_POST['type'] == 'W' || $_POST['type'] == 'S')
				{
					//start and end times are submitted separately, but stored in the database as one field
					$sunSt = $_POST['sunSt'];
					$sunEnd = $_POST['sunEnd'];
					$sunAvail = $sunSt . " - " . $sunEnd;
					$monSt = $_POST['monSt'];
					$monEnd = $_POST['monEnd'];
					$monAvail = $monSt . " - " . $monEnd;
					$tueSt = $_POST['tueSt'];
					$tueEnd = $_POST['tueEnd'];
					$tueAvail = $tueSt . " - " . $tueEnd;
					$wedSt = $_POST['wedSt'];
					$wedEnd = $_POST['wedEnd'];
					$wedAvail = $wedSt . " - " . $wedEnd;
					$thuSt = $_POST['thuSt'];
					$thuEnd = $_POST['thuEnd'];
					$thuAvail = $thuSt . " - " . $thuEnd;
					$friSt = $_POST['friSt'];
					$friEnd = $_POST['friEnd'];
					$friAvail = $friSt . " - " . $friEnd;
					$satSt = $_POST['satSt'];
					$satEnd = $_POST['satEnd'];
					$satAvail = $satSt . " - " . $satEnd;
				}
				$notes = $_POST['notes'];
				
				//if required fields are blank, set the corresponding error message and increment error counter
				if($type == '')
				{
					$typerr = '<div class="badge badge-warning">Please specify a user type.</div>';
					$err++;
				}
				if($uname == '')
				{
					$unerr = '<div class="badge badge-warning">Please enter a username.</div>';
					$err++;
				}
				if($pass1 == '')
				{
					$paserr = '<div class="badge badge-warning">Please enter a password.</div>';
					$err++;
				}
				else if($pass1 != $pass2)
				{
					$paserr = '<div class="badge badge-warning">The passwords did not match.</div>';
					$err++;
				}
				
				//if the password is not complex enough,
				//increment error counter and set different error message
				else if(!isComplex($pass2))
				{
					$paserr = "<div class='badge badge-warning'>Password is not complex enough.</div>";
					$err++;
				}
				if($fname == '')
				{
					$fnerr = '<div class="badge badge-warning">Please enter a first name.</div>';
					$err++;
				}
				if($lname == '')
				{
					$lnerr = '<div class="badge badge-warning">Please enter a last name.</div>';
					$err++;
				}
				
				//To determine whether the staff member's username is unique//////////////////////////////////
				
				$qry = $conn->prepare("SELECT * FROM STAFF WHERE USER_NAME = '$uname'");
				
				$qry->execute();
				
				$qryArray = $qry->fetchAll();
				
				if(sizeof($qryArray) > 0)
				{
					$err++;
					$unerr = '<div class="badge badge-warning">That username is already in use.</div>';
				}
				
				//////////////////////////////////////////////////////////////////////////////////////////////
				
				//if there are no errors, add information into the database
				if($err == 0)
				{
					//hash the password
					$pass = password_hash($pass1, PASSWORD_BCRYPT);
					
					$sql = $conn->prepare("INSERT INTO STAFF (TYPE_CODE, USER_NAME, USER_PASS, STAFF_FNAME, STAFF_LNAME, STAFF_PHONE, STAFF_ADDRESS, STAFF_CITY, CAN_CHILD, CAN_PC, CAN_DRIVE, SUN_AVAIL, MON_AVAIL, TUE_AVAIL, WED_AVAIL, THU_AVAIL, FRI_AVAIL, SAT_AVAIL, STAFF_NOTES)
					VALUES ('$type', '$uname', '$pass', '$fname', '$lname', '$phone', '$address', '$city', '$child', '$pc', '$drive', '$sunAvail', '$monAvail', '$tueAvail', '$wedAvail', '$thuAvail', '$friAvail', '$satAvail', '$notes')");
					
					$sql->execute();
					
					//echo implode(":",$sql->errorInfo());
					
					//based on user type, set the typeName variable
					switch($type)
					{
						case "C":
							$typeName = "Coordinator";
						break;
						case "W":
							$typeName = "Worker";
						break;
						case "S":
							$typeName = "Supervisor";
						break;
						case "B";
							$typeName = "Bookkeeper";
						break;
						default:
							$typeName = "Unknown type";
						break;
						
					}
					
					//log whenever database is updated
					date_default_timezone_set("US/Mountain");
					//F j, Y, g:i a
					$dateString = date("r");
					file_put_contents("../logs/staffAddLog.txt", "\n" . $fname . " " . $lname . " was added as a " . $typeName . " with the User Name " . $uname .  " on: " . $dateString, FILE_APPEND | LOCK_EX);
					
					//send the user back to this page with an empty form and a success message
					header ("Location: addstaff.php?p=1");
				}
			}
			
			//retrieve user type records from database
			$sql = $conn->prepare("SELECT * FROM USER_TYPE");
				
			$sql->execute();
			
			$row = $sql->fetchAll();
			
			//if the AJAX call to show availability or not has been called
			if(isset($_REQUEST['change']))
			{
				//if the staff member is a worker or supervisor, display the worker-exclusive fields
				if($_REQUEST['change'] == 'W' || $_REQUEST['change'] == 'S')
				{
					printf("Availability: <br /><br />
							Sunday:
								Start:
									<input class='form-fan' type='time' name='sunSt' value='$sunSt'>
								End:
									<input class='form-fan' type='time' name='sunEnd' value='$sunEnd'><br /><br />
							Monday:
								Start:
									<input class='form-fan' type='time' name='monSt' value='$monSt'>
								End:
									<input class='form-fan' type='time' name='monEnd' value='$monEnd'><br /><br />

							Tuesday:
								Start:
									<input class='form-fan' type='time' name='tueSt' value='$tueSt'>
								End:
									<input class='form-fan' type='time' name='tueEnd' value='$tueEnd'><br /><br />

							 Wednesday:
								Start:
									<input class='form-fan' type='time' name='wedSt' value='$wedSt'>
								End:
									<input class='form-fan' type='time' name='wedEnd' value='$wedEnd'><br /><br />

							 Thursday:
								Start:
									<input class='form-fan' type='time' name='thuSt' value='$thuSt'>
								End:
									<input class='form-fan' type='time' name='thuEnd' value='$thuEnd'><br /><br />

							 Friday:
								Start:
									<input class='form-fan' type='time' name='friSt' value='$friSt'>
								End:
									<input class='form-fan' type='time' name='friEnd' value='$friEnd'><br /><br />

							 Saturday:
								Start:
									<input class='form-fan' type='time' name='satSt' value='$satSt'>
								End:
									<input class='form-fan' type='time' name='satEnd' value='$satEnd'><br /><br />

						Able to Drive:");
					if(isset($_POST['drive']))
						echo "<input class='form-fan' type='checkbox' name='drive' checked><br /><br />";
					else
						echo "<input class='form-fan' type='checkbox' name='drive'><br /><br />";
					
						echo "Can Provide Personal Care:";
					if(isset($_POST['pc']))
						echo "<input class='form-fan' type='checkbox' name='pc' checked><br /><br />";
					else
						echo "<input class='form-fan' type='checkbox' name='pc'><br /><br />";
					printf("
								Can work with Children:");
					if(isset($_POST['child']))
						echo "<input class='form-fan' type='checkbox' name='child' checked><br /><br />";
					else
						echo "<input class='form-fan' type='checkbox' name='child'><br /><br />";
				}
				
				//end the script here so nothing else is shown
				die();
			}
			
			//include navbar
			
								include "../includes/scripts/navBar.php";
			
			echo'<div class="conb">';
			echo'<div class="row justify-content-sm-center">';
				
		
			
			//display the form
			
			printf("
				
			
			
				<form class='form-con' method='post' action='addstaff.php'>
				");
				//if there is a successful database entry, display message
							if(isset($_REQUEST['p']))
				echo"<div class='alert alert-success'>Staff member added successfully.</div>";
				
				printf("
				<h1>Add New Staff Member</h1>

					First Name:
						<input class='form-fan' type='text' name='fname' value='$fname'>$fnerr
					Last Name:
						<input class='form-fan' type='text' name='lname' value='$lname'>$lnerr<br /><br />\n

					Full Address:
						<input class='form-fan' type='text' name='address' value='$address'>
					City:
						<input class='form-fan' type='text' name='city' value='$city'><br /><br />\n

					Primary Phone Number:
						<input class='form-fan' type='tel' name='phone' pattern='[0-9]{3}-[0-9]{3}-[0-9]{4}' value='$phone'><br />
						<p class='text-danger'>Format: 000-000-0000</p>\n\n

					<!--display selection of user types-->
					User Type:
						<select class='fanc' name='type' onchange='showAvail(this.value)'>
							<option value=''>Select one:</option>
			");
			foreach($row as $data)
				echo "<option value='{$data['TYPE_CODE']}'>{$data['TYPE_NAME']}</option>";
			printf("
						</select>$typerr<br /><br />\n");
						
					if(isset($_REQUEST['message']))
						{
							echo "<p style='color: red;'>" . $_REQUEST['message'] . "</p>";
						}
			printf("
					User Name:<div class='form-row'><div class='col-12'>
						<input class='form-fan' type='text' name='uname' value='$uname'>$unerr<br /><br />
									
						
						</div></div>Enter password:<div class='form-row'><div class='col-12'> <input class='form-fan' type='password' name='pass1' value='$pass1'>$paserr<br /><br />
						</div></div>Confirm password:<div class='form-row'><div class='col-12'> <input class='form-fan' type='password' name='pass2' value=''><br /><br />
						</div></div><p class='text-danger'>Passwords need to be at least 8 characters long and include a number,<br />
						a lowercase letter, an uppercase letter, and a special character.</p>

					<div id='availBox'>
					</div>");
					
					
			
			printf("	
					Notes:<br />
						<textarea input class='form-fan' name='notes' rows='3' cols='30'>$notes</textarea><br /><br />
						
					<input  type='submit' name='submit' value='Submit' class='btn btn-primary'>\n
					
					<a href='/land.php' class='btn btn-danger'>Cancel</a>

				</form>
				
				<!--cancel button that returns user to previous page-->
				
			</div>

			");
			
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
			
	
echo'
            

    
</form>
</div></div></div>';
include "../includes/scripts/footer.php";
	echo'
</body>
</html>
    ';
	?>