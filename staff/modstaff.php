<?php
/*  Developer:   Justin Alho
 *  File Name:   modstaff.php
 *  Description: Allows coordinators to modify existing staff records
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
             <title>Modify Staff</title>
	<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="../css/table.css" rel="stylesheet" type="text/css">
				<script>
				//change whether or nor availability is displayed depending on staff type
				function showAvail(type, id) {
					if (window.XMLHttpRequest)
					{
						xmlhttp = new XMLHttpRequest();
					}
					xmlhttp.onreadystatechange = function() {
						if (this.readyState == 4 && this.status == 200) {
							document.getElementById("availBox").innerHTML = this.responseText;
						}
					};
					xmlhttp.open("GET","modstaff.php?change="+type+"&id="+id,true);
					xmlhttp.send();
				}
			</script>

</head>
<body>';


        
			//Starting a session and initializing variables needed
			session_start(); 
			$userType = $_SESSION['userType'];
			
			//include links to css, javascript, etc.
			include "../includes/scripts/headLinks2.0.php";
			
			//include functions used for checking password complexity
			include "../includes/functions/isComplex.php";
			include "../includes/functions/isSpecial.php";



        
		
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
			$note1 = '';
			$note2 = '';
			$note3 = '';
			$note4 = '';
			$note5 = '';
			$note6 = '';
			$note7 = '';
			$note8 = '';
			$note9 = '';
			$note10 = '';
			$note12 = '';
			$note13 = '';
			$note14 = '';
			$note15 = '';
			$note16 = '';
			$note17 = '';
			$note18 = '';
			
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
				$id = $_POST['id'];
				$status = $_POST['status'];
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
				//if staff is a worker or a coordinator, set their availability
				if($_POST['type'] == 'C' || $_POST['type'] == 'W')
				{
					$sunAvail = $_POST['sunSt'] . " - " . $_POST['sunEnd'];
					$monAvail = $_POST['monSt'] . " - " . $_POST['monEnd'];
					$tueAvail = $_POST['tueSt'] . " - " . $_POST['tueEnd'];
					$wedAvail = $_POST['wedSt'] . " - " . $_POST['wedEnd'];
					$thuAvail = $_POST['thuSt'] . " - " . $_POST['thuEnd'];
					$friAvail = $_POST['friSt'] . " - " . $_POST['friEnd'];
					$satAvail = $_POST['satSt'] . " - " . $_POST['satEnd'];
				}
				$notes = addslashes($_POST['notes']);
				
				//if required fields are blank, set the corresponding error message and increment error counter
				if($uname == '')
				{
					$unerr = 'Please enter a username.';
					$err++;
				}
				
				//if password field has been entered into, check to make sure the password is valid,
				//set password error message and increment error counter if not
				if($pass1 != '')
				{
					if($pass1 != $pass2)
					{
						$paserr = 'The passwords did not match.';
						$err++;
					}
					else if(!isComplex($pass2))
					{
						$paserr = "Password is not complex enough.";
						$err++;
					}
					else
					$pass = $pass1;
				}

				//To determine whether the staff member's username is unique//////////////////////////////////
				$id2 = $_REQUEST['id'];
				$qry = $conn->prepare("SELECT USER_NAME, STAFF_ID FROM STAFF WHERE USER_NAME = '$uname'");
				
				$qry->execute();
				
				$qryArray = $qry->fetchAll();
				
				if(sizeof($qryArray) > 0)
				{
					if($qryArray[0]['STAFF_ID'] != $id2)
					$err++;
					$unerr = '<div class="badge badge-warning">That username is already in use.</div>';
				}
				
				if($fname == '')
				{
					$fnerr = 'Please enter a first name.';
					$err++;
				}
				if($lname == '')
				{
					$lnerr = 'Please enter a last name.';
					$err++;
				}
				
				//if there are no errors, add information into the database
				if($err == 0)
				{
					
					//retrieve selected staff member's information from database
					$sql = $conn->prepare("SELECT STAFF_ID, STAFF_STATUS, C_S_STATUS_CODE, C_S_STATUS_NAME, STAFF.TYPE_CODE, TYPE_NAME, USER_NAME, USER_PASS, STAFF_FNAME, STAFF_LNAME, STAFF_PHONE, STAFF_ADDRESS, STAFF_CITY, CAN_CHILD, CAN_PC, CAN_DRIVE, SUN_AVAIL, MON_AVAIL, TUE_AVAIL, WED_AVAIL, THU_AVAIL, FRI_AVAIL, SAT_AVAIL, STAFF_NOTES
					FROM STAFF
					LEFT JOIN C_S_STATUS
					ON STAFF.STAFF_STATUS = C_S_STATUS.C_S_STATUS_CODE
					LEFT JOIN USER_TYPE
					ON STAFF.TYPE_CODE = USER_TYPE.TYPE_CODE
					WHERE STAFF.STAFF_ID = '$id'");
					$sql->execute();
					$row = $sql->fetch();
					$cfname = $row['STAFF_FNAME'];
					$ctyp = $row['TYPE_CODE'];
					$cclname = $row['STAFF_LNAME'];
					$cphone = $row['STAFF_PHONE'];
					$cadd = $row['STAFF_ADDRESS'];
					$cty = $row['STAFF_CITY'];
					$catus = $row['STAFF_STATUS'];
					$cunnam = $row['USER_NAME'];
					$csuna = $row['SUN_AVAIL'];
					$cmona = $row['MON_AVAIL'];
					$ctusa = $row['TUE_AVAIL'];
					$cweda = $row['WED_AVAIL'];
					$cthua = $row['THU_AVAIL'];
					$cfria = $row['FRI_AVAIL'];
					$csata = $row['SAT_AVAIL'];
					$catd = $row['CAN_DRIVE'];
					$ccpc = $row['CAN_PC'];
					$ccwwc = $row['CAN_CHILD'];

					
					if ($cphone != $phone) {

						$note18 = "Phone Number was changed to:" . $phone . " ";

					}

					if ($ctyp != $type) {

						$note17 = "Type was changed to:" . $type . " ";

					}

					if ($cfname != $fname) {

						$note1 = "First name was changed to:" . $fname . " ";

					}
					if ($cclname != $lname) {

						$note2 = "Lastname was changed to:" . $lname . " ";
						
					}
					if ($cadd != $address) {

						$note3 = "Address was changed to:" . $address . " ";
						
					}
					if ($cty != $city) {

						$note4 = "City was changed to:" . $city . " ";
						
					}
					if ($catus != $status) {

						$note5 = "Status was changed to:" . $status . " ";
						
					}
					if ($cunnam != $uname) {

						$note6 = "Username was changed to:" . $uname . " ";
						
					}
					if ($csuna != $sunAvail) {

						$note7 = "Sunday avalibility was changed to:" . $sunAvail . " ";
						
					}
					if ($cmona != $monAvail) {

						$note8 = "Monday avalibility was changed to:" . $monAvail . " ";
						
					}
					if ($ctusa != $tueAvail) {

						$note9 = "Tuesday avalibility was changed to:" . $tueAvail . " ";
						
					}
					if ($cweda != $wedAvail) {

						$note10 = "Wednesday avalibility was changed to:" . $wedAvail . " ";
						
					}
					if ($cthua != $thuAvail) {

						$note11 = "Thursday avalibility was changed to:" . $thuAvail . " ";
						
					}
					if ($cfria != $friAvail) {

						$note12 = "Friday avalibility was changed to:" . $friAvail . " ";
						
					}
					if ($csata != $satAvail) {

						$note13 = "Saturday avalibility was changed to:" . $satAvail . " ";
						
					}
					if ($catd != $drive) {

						$note14 = "Ability to drive was changed to:" . $drive . " ";
						
					}
					if ($ccpc != $pc) {

						$note15 = "Can provide personal care was changed to:" . $pc . " ";
						
					}
					if ($ccwwc != $child) {

						$note16 = "Can work with childeren was changed to:" . $cild . " ";
						
					}
					//if the new password variable is set
					if(isset($pass))
					{
						//hash the new password, update database record
						$pass = password_hash($pass, PASSWORD_BCRYPT);
						$sql = $conn->prepare("UPDATE STAFF SET STAFF_STATUS = '$status', TYPE_CODE = '$type', USER_NAME = '$uname', USER_PASS = '$pass', STAFF_FNAME = '$fname', STAFF_LNAME = '$lname', STAFF_PHONE = '$phone',STAFF_ADDRESS = '$address', STAFF_CITY = '$city',
						CAN_CHILD = '$child', CAN_PC = '$pc', CAN_DRIVE = '$drive', SUN_AVAIL = '$sunAvail', MON_AVAIL = '$monAvail', TUE_AVAIL = '$tueAvail', WED_AVAIL = '$wedAvail', THU_AVAIL = '$thuAvail', FRI_AVAIL = '$friAvail', SAT_AVAIL = '$satAvail', STAFF_NOTES = '$notes'
						WHERE STAFF_ID = '$id'");
						$dateString = date("r");
						file_put_contents("../logs/staffModLog.txt", "\n" . $cfname . " " . $cclname . " was modified on: " . $dateString . " by " . $_SESSION['userName'] . " " . " PASSWORD WAS MODIFIED!!!!!! " . $note17 . $note1 . $note2 . $note3 . $note4 . $note5 . $note6 . $note7 . $note8 . $note9 . $note10 . $note11 . $note12 . $note13 . $note14 . $note15 . $note16 . $note18 . ".", FILE_APPEND | LOCK_EX) ;
					}
					
					//if no new password is set, update record without changing password
					else
					{
						$sql = $conn->prepare("UPDATE STAFF SET STAFF_STATUS = '$status', TYPE_CODE = '$type', USER_NAME = '$uname', STAFF_FNAME = '$fname', STAFF_LNAME = '$lname', STAFF_PHONE = '$phone', STAFF_ADDRESS = '$address', STAFF_CITY = '$city',
						CAN_CHILD = '$child', CAN_PC = '$pc', CAN_DRIVE = '$drive', SUN_AVAIL = '$sunAvail', MON_AVAIL = '$monAvail', TUE_AVAIL = '$tueAvail', WED_AVAIL = '$wedAvail', THU_AVAIL = '$thuAvail', FRI_AVAIL = '$friAvail', SAT_AVAIL = '$satAvail', STAFF_NOTES = '$notes'
						WHERE STAFF_ID = '$id'");
						$dateString = date("r");
						file_put_contents("../logs/staffModLog.txt", "\n" . $cfname . " " . $cclname . " was modified on: " . $dateString . " by " . $_SESSION['userName'] . " " . $note17 . $note1 . $note2 . $note3 . $note4 . $note5 . $note6 . $note7 . $note8 . $note9 . $note10 . $note11 . $note12 . $note13 . $note14 . $note15 . $note16 . $note18 . ".", FILE_APPEND | LOCK_EX) ;
					}
					
					$sql->execute();
					
					//echo implode(":",$sql->errorInfo());
					print_r($_POST);
					//send the user back to list of staff with a success message
					header ("Location: viewstaff.php?p=1");
				}
			}
			
			//set ID variable to ID sent by viewstaff.php
			$id = $_REQUEST['id'];
					
			//retrieve selected staff member's information from database
			$sql = $conn->prepare("SELECT STAFF_ID, STAFF_STATUS, C_S_STATUS_CODE, C_S_STATUS_NAME, STAFF.TYPE_CODE, TYPE_NAME, USER_NAME, USER_PASS, STAFF_FNAME, STAFF_LNAME, STAFF_PHONE, STAFF_ADDRESS, STAFF_CITY, CAN_CHILD, CAN_PC, CAN_DRIVE, SUN_AVAIL, MON_AVAIL, TUE_AVAIL, WED_AVAIL, THU_AVAIL, FRI_AVAIL, SAT_AVAIL, STAFF_NOTES
			FROM STAFF
			LEFT JOIN C_S_STATUS
			ON STAFF.STAFF_STATUS = C_S_STATUS.C_S_STATUS_CODE
			LEFT JOIN USER_TYPE
			ON STAFF.TYPE_CODE = USER_TYPE.TYPE_CODE
			WHERE STAFF.STAFF_ID = '$id'");
				
			$sql->execute();
			
			$row = $sql->fetch();
			//echo implode(":",$sql->errorInfo()) . "<br>";
			
			//retrieve list of statuses from database
			$stasql = $conn->prepare("SELECT * FROM C_S_STATUS WHERE C_S_STATUS_CODE != '{$row['STAFF_STATUS']}'");
			
			$stasql->execute();
			
			$starow = $stasql->fetchAll();
			
			//retrieve list of user types from database
			$typsql = $conn->prepare("SELECT * FROM USER_TYPE WHERE TYPE_CODE != '{$row['TYPE_CODE']}'");
			
			$typsql->execute();
			
			$typrow = $typsql->fetchAll();
			
			//if showAvail() has been executed
			if(isset($_REQUEST['change']))
			{
				//if the staff member is a worker or supervisor, display the worker-exclusive fields
				if($_REQUEST['change'] == 'W' || $_REQUEST['change'] == 'S')
				{
					//if availability is set, split field into start and end times to display in form
					if($row['SUN_AVAIL'] != '')
					{					
						$sunAvail = explode(" - ", $row['SUN_AVAIL']);
						$sunSt = $sunAvail[0];
						$sunEnd = $sunAvail[1];
					}
					if($row['MON_AVAIL'] != '')
					{					
						$monAvail = explode(" - ", $row['MON_AVAIL']);
						$monSt = $monAvail[0];
						$monEnd = $monAvail[1];
					}
					if($row['TUE_AVAIL'] != '')
					{					
						$tueAvail = explode(" - ", $row['TUE_AVAIL']);
						$tueSt = $tueAvail[0];
						$tueEnd = $tueAvail[1];
					}
					if($row['WED_AVAIL'] != '')
					{					
						$wedAvail = explode(" - ", $row['WED_AVAIL']);
						$wedSt = $wedAvail[0];
						$wedEnd = $wedAvail[1];
					}
					if($row['THU_AVAIL'] != '')
					{					
						$thuAvail = explode(" - ", $row['THU_AVAIL']);
						$thuSt = $thuAvail[0];
						$thuEnd = $thuAvail[1];
					}
					if($row['FRI_AVAIL'] != '')
					{					
						$friAvail = explode(" - ", $row['FRI_AVAIL']);
						$friSt = $friAvail[0];
						$friEnd = $friAvail[1];
					}
					if($row['SAT_AVAIL'] != '')
					{					
						$satAvail = explode(" - ", $row['SAT_AVAIL']);
						$satSt = $satAvail[0];
						$satEnd = $satAvail[1];
					}
							
					printf("
					Availability: <br /><br />
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
								<input class='form-fan' type='time' name='satEnd' value='$satEnd'><br /><br />");

					echo "Able to Drive: ";
					if($row['CAN_DRIVE'] == 1)
						echo "<input type='checkbox' name='drive' checked><br /><br />";
					else
						echo "<input type='checkbox' name='drive'><br /><br />";
					
					echo "Can Provide Personal Care: ";
					if($row['CAN_PC'] == 1)
						echo "<input type='checkbox' name='pc' checked><br /><br />";
					else
						echo "<input type='checkbox' name='pc'><br /><br />";

					echo "Can Work with Children: ";
					if($row['CAN_CHILD'] == 1)
						echo "<input type='checkbox' name='child' checked><br /><br />";
					else
						echo "<input type='checkbox' name='child'><br /><br />";
				}
				
				//end the script here so nothing else is shown
				die();
			}
			
			$type = $row['TYPE_CODE'];
			//execute showAvail() to display extra information for workers and supervisors
			?>
			<script>
				var uType = <?php echo "'$type'" ?>;
				var uID = <?php echo "'$id'"; ?>;
				showAvail(uType, uID);
			</script>
			<?php
			
			//include navbar
			include "../includes/scripts/navBar.php";
			echo'<div class="conb">';
			echo'<div class="row justify-content-sm-center">';
			
			//display the form
						
			printf("

				
				

				<form class='form-con' method='post' action='modstaff.php'>
				<h1>Modify a Staff Member</h1>

					<input class='form-fan' type='hidden' name='id' value='$id'>
					First Name:
						<input class='form-fan' type='text' name='fname' value='{$row['STAFF_FNAME']}'>$fnerr
					Last Name:
						<input class='form-fan' type='text' name='lname' value='{$row['STAFF_LNAME']}'>$lnerr<br /><br />\n

					Full Address:
						<input class='form-fan' type='text' name='address' value='{$row['STAFF_ADDRESS']}'>
					City:
						<input class='form-fan' type='text' name='city' value='{$row['STAFF_CITY']}'><br /><br />\n

					Primary Phone Number:
						<input class='form-fan' type='tel' name='phone' pattern='[0-9]{3}-[0-9]{3}-[0-9]{4}' value='{$row['STAFF_PHONE']}'><br />
						<p class=text-danger>Format: 000-000-0000</p>\n
						
					<!--display selection of statuses-->
					Status:
						<select class='fanc' name='status'>
							<option value='{$row['C_S_STATUS_CODE']}'>{$row['C_S_STATUS_NAME']}</option>");
			foreach($starow as $data)
				echo "<option value='{$data['C_S_STATUS_CODE']}'>{$data['C_S_STATUS_NAME']}</option>";
			printf("
						</select><br /><br />\n
								
					<!--display selection of user types-->
					User Type:
						<select class='fanc' name='type' onchange='showAvail(this.value, " . $id . ")'>
							<option value='{$row['TYPE_CODE']}'>{$row['TYPE_NAME']}</option>");
			foreach($typrow as $data)
				echo "<option value='{$data['TYPE_CODE']}'>{$data['TYPE_NAME']}</option>";
			printf("
						</select><br /><br />\n

					User Name:<div class='form-row'><div class='col-12'>
						<input class='form-fan' type='text' name='uname' value='{$row['USER_NAME']}'>$unerr<br /><br />
					</div></div>Change Password:<br />
						New password:<div class='form-row'><div class='col-12'> <input class='form-fan' type='password' name='pass1' value='$pass1'><br />
						</div></div>Confirm password:<div class='form-row'><div class='col-12'> <input class='form-fan' type='password' name='pass2' value=''>$paserr
						</div></div><p class='text-danger'>Passwords need to be at least 8 characters long<br /> and include a number,
						a lowercase letter, an uppercase letter,<br /> and a special character.</p>
					
					<!--if staff is a worker or supervisor, their availability information will appear here-->
					<div id='availBox'>
					</div>
					
					Notes:<br />
						<textarea class='form-fan' name='notes' rows='3' cols='30'>{$row['STAFF_NOTES']}</textarea><br /><br />
					
					<input  type='submit' name='submit' value='Submit' class='btn btn-primary'>
					<a href='viewstaff.php' class='btn btn-danger'>Cancel</a>

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