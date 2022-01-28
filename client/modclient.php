<?php
/*  Developer:   Justin Alho
 *  File Name:   modclient.php
 *  Description: Allows coordinators to modify existing client records
 *  Date Start:  25/02/2020
 *  Date End:    TBD
 */?>
  <?php
echo'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
                     <title>Modify Client</title>
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
			$gh = '';
			$fname = '';
			$lname = '';
			$phone = '';
			$address = '';
			$city = '';
			$hours = '';
			$km = '';
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
			
			$fnamerr = '';
			$lnamerr = '';
			
			//if the form has been submitted
			if(isset($_POST['submit']))
			{	
				//set error counter to 0
				$err = 0;
				
				//set variables to submitted values
				$id = $_POST['id'];
				$status = $_POST['status'];
				$gh = $_POST['gh'];
				$fname = $_POST['fname'];
				$lname = $_POST['lname'];
				$phone = $_POST['phone'];
				$address = $_POST['address'];
				$city = $_POST['city'];
				$hours = $_POST['hours'];
				$km = $_POST['km'];
				$notes = $_POST['notes'];

				//if required fields are blank, set the corresponding error message and increment error counter
				if($fname == '')
				{
					$fnamerr = 'Please enter a first name.';
					$err++;
				}
				if($lname == '')
				{
					$lnamerr = 'Please enter a last name.';
					$err++;
				}
				
				//if there are no errors, add information into database
				if($err == 0)
				{
					$sql = $conn->prepare("SELECT CLIENT_ID, CLIENT_STATUS, C_S_STATUS_CODE, C_S_STATUS_NAME, CLIENT.GH_ID, GH_NAME, CLIENT_FNAME, CLIENT_LNAME, CLIENT_PHONE, CLIENT_ADDRESS, CLIENT_CITY, CLIENT_MAX_HOURS, CLIENT_KM, CLIENT_NOTES
					FROM CLIENT
					LEFT JOIN C_S_STATUS
					ON CLIENT.CLIENT_STATUS = C_S_STATUS.C_S_STATUS_CODE
					LEFT JOIN GROUP_HOME
					ON CLIENT.GH_ID = GROUP_HOME.GH_ID
					WHERE CLIENT.CLIENT_ID = '$id'");
					$sql->execute();
					$row = $sql->fetch();
					$cfname = $row['CLIENT_FNAME'];
					$cgh = $row['GH_ID'];
					$cclname = $row['CLIENT_LNAME'];
					$cadd = $row['CLIENT_ADDRESS'];
					$cty = $row['CLIENT_CITY'];
					$cphone = $row['CLIENT_PHONE'];
					$catus = $row['CLIENT_STATUS'];
					$ckm = $row['CLIENT_KM'];
					$chours = $row['CLIENT_MAX_HOURS'];

					if ($cphone != $phone) {

						$note8 = "Phone Number was changed to:" . $phone . " ";

					}

					if ($cgh != $gh) {

						$note9 = "Grouphome was changed to:" . $gh . " ";

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
					if ($ckm != $km) {

						$note6 = "KM was changed to:" . $km . " ";
						
					}
					if ($chours != $hours) {

						$note7 = "Hours was changed to:" . $hours . " ";
						
					}

					$modsql = $conn->prepare("UPDATE CLIENT SET CLIENT_STATUS = '$status', GH_ID = '$gh', CLIENT_FNAME = '$fname', CLIENT_LNAME = '$lname', CLIENT_PHONE = '$phone', CLIENT_ADDRESS = '$address', CLIENT_CITY = '$city',
					CLIENT_MAX_HOURS = '$hours', CLIENT_KM = '$km', CLIENT_NOTES = '$notes'
					WHERE CLIENT_ID = '$id'");
					
					$modsql->execute();
					
					//echo implode(":",$modsql->errorInfo());
					
					//log whenever the database is updated
					date_default_timezone_set("US/Mountain");
					//F j, Y, g:i a
					$dateString = date("r");
					file_put_contents("../logs/clientModLog.txt", "\n" . $cfname . " " . $cclname . " was modified on: " . $dateString . " by " . $_SESSION['userName'] . " " . $note1 . $note2 . $note3 . $note4 . $note5 . $note6 . $note7 . $note8 . $note9 . ".", FILE_APPEND | LOCK_EX) ;
					
					//send user back to list of clients with a success message
					header('Location: viewclient.php?s=1');
				}
			}
			
			//set ID variable to ID sent by viewclient.php
			$id = $_REQUEST['id'];
			
			//retrieve selected client's information from database
			$sql = $conn->prepare("SELECT CLIENT_ID, CLIENT_STATUS, C_S_STATUS_CODE, C_S_STATUS_NAME, CLIENT.GH_ID, GH_NAME, CLIENT_FNAME, CLIENT_LNAME, CLIENT_PHONE, CLIENT_ADDRESS, CLIENT_CITY, CLIENT_MAX_HOURS, CLIENT_KM, CLIENT_NOTES
			FROM CLIENT
			LEFT JOIN C_S_STATUS
			ON CLIENT.CLIENT_STATUS = C_S_STATUS.C_S_STATUS_CODE
			LEFT JOIN GROUP_HOME
			ON CLIENT.GH_ID = GROUP_HOME.GH_ID
			WHERE CLIENT.CLIENT_ID = '$id'");
				
			$sql->execute();
			
			$row = $sql->fetch();
			
			//retrieve other statuses from database
			$stasql = $conn->prepare("SELECT * FROM C_S_STATUS WHERE C_S_STATUS_CODE != '{$row['CLIENT_STATUS']}'");
			
			$stasql->execute();
			
			$starow = $stasql->fetchAll();
			
			//retrieve other group homes from database
			$ghsql = $conn->prepare("SELECT GH_ID, GH_NAME FROM GROUP_HOME WHERE GH_ID != '{$row['GH_ID']}'");
			
			$ghsql->execute();
			
			$ghrow = $ghsql->fetchAll();
			
			//include navbar
			include "../includes/scripts/navBar.php";
			echo'<div class="conb">';
			echo'<div class="row justify-content-sm-center">';
			//display the form
			printf("

				<form class='form-con' method='post' action='modclient.php'>
				<h1>Modify a Client</h1>

					<!--submit the ID as a hidden value-->
					<input class='form-fan' type='hidden' name='id' value='$id'>
					First Name:
						<input class='form-fan' type='text' name='fname' value='{$row['CLIENT_FNAME']}'> $fnamerr 
					Last Name:
						<input class='form-fan' type='text' name='lname' value='{$row['CLIENT_LNAME']}'> $lnamerr<br /><br />\n
						
					Full Address:
						<input class='form-fan' type='text' name='address' value='{$row['CLIENT_ADDRESS']}'>
					City:
						<input class='form-fan' type='text' name='city' value='{$row['CLIENT_CITY']}'><br /><br />\n

						Primary Phone Number:
						<input class='form-fan' type='tel' name='phone' pattern='[0-9]{3}-[0-9]{3}-[0-9]{4}' value='{$row['CLIENT_PHONE']}'><br />
						<p class='text-danger'>Format: 000-000-0000</p>\n
						
					<!--display selection of statuses-->
					Status:
						<select class='fanc' name='status'>
							<option value='{$row['C_S_STATUS_CODE']}'>{$row['C_S_STATUS_NAME']}</option>");
			foreach($starow as $data)
				echo "<option value='{$data['C_S_STATUS_CODE']}'>{$data['C_S_STATUS_NAME']}</option>";
			printf("
						</select><br /><br />\n
						
					<!--display selection of group homes-->	
					Group Home:
						<select class='fanc' name='gh'>
							<option value='{$row['GH_ID']}'>{$row['GH_NAME']}</option>");
			foreach($ghrow as $data)
				echo "<option value='{$data['GH_ID']}'>{$data['GH_NAME']}</option>";
			printf("
						</select><br /><br />\n

					Distance (in kilometers):<div class='form-row'><div class='col-12'>
						<input class='form-fan' type='text' name='km' value='{$row['CLIENT_KM']}'><br /><br />
						
					</div></div>Max Hours per Month:<div class='form-row'><div class='col-12'>
						<input class='form-fan' type='text' name='hours' value='{$row['CLIENT_MAX_HOURS']}'><br /><br />
						
					</div></div>Notes:<br />
						<textarea class='form-fan' name='notes' rows='3' cols='30'>{$row['CLIENT_NOTES']}</textarea><br /><br />
					
					<input  type='submit' name='submit' value='Submit' class='btn btn-primary'>
					<a href='viewclient.php' class='btn btn-danger'>Cancel</a>

				</form><br />
				
				<!--cancel button that returns user to previous page-->
				
			");
						
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
			
echo'
</div></div>';
	include "../includes/scripts/footer.php";
	echo'
</body>
</html>
    ';
	?>