<?php
/*  Developer:   Justin Alho
 *  File Name:   addclient.php
 *  Description: Allows coordinators to add new client records into the database
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
	        <title>View Group Home Information</title>
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
		
			#Starting a session and initilizing variables needed
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
			
			//initialize variables
			$gh = '';
			$fname = '';
			$lname = '';
			$phone = '';
			$address = '';
			$city = '';
			$hours = 0;
			$km = 0;
			$notes = '';
			
			$gherr = '';
			$fnamerr = '';
			$lnamerr = '';
			$newerr = '';

            //connect to the database
            include "../dbseckey.php";
            $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);
		
			//if the form has been submitted
			if(isset($_POST['submit']))
			{	
				//set error counter to 0
				$err = 0;
				
				//set variables to submitted values
				$gh = $_POST['gh'];
				$fname = $_POST['fname'];
				$lname = $_POST['lname'];
				$phone = $_POST['phone'];
				$address = $_POST['address'];
				$city = $_POST['city'];
				$hours = $_POST['hours'];
				$km = $_POST['km'];
				$notes = addslashes($_POST['notes']);
				
				//if required fields are blank, set the corresponding error message and increment error counter
				if($gh == '')
				{
					$gherr = '<div class="badge badge-warning">Please select a group home or N/A</div>';
					$err++;
				}
				if($fname == '')
				{
					$fnamerr = '<div class="badge badge-warning">Please enter a first name.</div>';
					$err++;
				}
				if($lname == '')
				{
					$lnamerr = '<div class="badge badge-warning">Please enter a last name.</div>';
					$err++;
				}
				
				//check database to see if record already exists
				$checkSql = $conn->prepare("SELECT * FROM CLIENT
				WHERE CLIENT_FNAME = '$fname'
				AND CLIENT_LNAME = '$lname'
				AND CLIENT_PHONE = '$phone'
				AND CLIENT_ADDRESS = '$address'
				");
				$checkSql->execute();
				$checkRow = $checkSql->fetchAll();
				
				//if record already exists, increment error counter and set error message
				if(sizeof($checkRow) > 0)
				{
					$err++;
					$newerr = 'The client you are trying to create is already in the system.<br /><br />';
				}
				
				//if there are no errors, add information into database
				if($err == 0)
				{
					$addsql = $conn->prepare("INSERT INTO CLIENT (GH_ID, CLIENT_FNAME, CLIENT_LNAME, CLIENT_PHONE, CLIENT_ADDRESS, CLIENT_CITY, CLIENT_MAX_HOURS, CLIENT_KM, CLIENT_NOTES)
					VALUES ('$gh', '$fname', '$lname', '$phone', '$address', '$city', '$hours', '$km', '$notes')");
					
					$addsql->execute();
					
					//echo implode(":",$addsql->errorInfo());
					
					//log whenever the database is updated
					date_default_timezone_set("US/Mountain");
					//F j, Y, g:i a
					$dateString = date("r");
					file_put_contents("../logs/clientAddLog.txt", "\n" . $fname . " " . $lname . " was added as a client on: " . $dateString, FILE_APPEND | LOCK_EX);
					
					//send the user back to this page with an empty form and a success message
					header('Location: addclient.php?s=1');
				}
			}
			
			//select group home records from database
			$sql = $conn->prepare("SELECT * FROM GROUP_HOME");
				
			$sql->execute();
			
			$row = $sql->fetchAll();
			
			//inlude navbar
					include "../includes/scripts/navBar.php";
			

			
			//display the form

			echo'<div class="conb">';
			echo'<div class="row justify-content-sm-center">';
			printf("
				
				

				<form class='form-con' method='post' action='addclient.php'>
				");
				
				//if there is a successful database entry, display message
			if(isset($_REQUEST['s']))
				echo "<div class='alert alert-success'>Record added successfully.</div>";
			
				printf("
				<h1>Add New Client</h1>
				
				$newerr
				
				First Name:
					<input class='form-fan' type='text' name='fname' value='$fname'>$fnamerr
				Last Name:
					<input class='form-fan' type='text' name='lname' value='$lname'>$lnamerr<br /><br />\n
				Full Address:
					<input class='form-fan' type='text' name='address' value='$address'>
				City:
					<input class='form-fan' type='text' name='city' value='$city'><br /><br />\n
				Phone Number:<div class='form-row'><div class='col-12'>
					<input class='form-fan' type='tel' name='phone' pattern='[0-9]{3}-[0-9]{3}-[0-9]{4}' value='$phone'><br />
					<p class='text-danger'>Format: 000-000-0000</p>\n
					
				<!--display selection of group homes-->
				</div></div>Group Home:
					<select class='fanc' name='gh'>
						<option value=''>Select a Group Home:</option>");
			foreach($row as $data)
				echo "<option value='{$data['GH_ID']}'>{$data['GH_NAME']}</option>";
			printf("
					</select>$gherr<br /><br />
				Hours Per Month:<div class='form-row'><div class='col-12'>
					<input class='form-fan' type='text' name='hours' value='$hours'><br /><br /><br />\n
				</div></div>Distance (in kilometers):<div class='form-row'><div class='col-12'>
					<input class='form-fan' type='text' name='km' value='$km'>\n
				</div></div>Notes:<br />
					<textarea input class='form-fan' name='notes' rows='3' cols='30'>$notes</textarea><br /><br />\n

					<input  type='submit' name='submit' value='Submit' class='btn btn-primary'>\n
					
					<a href='/land.php' class='btn btn-danger'>Cancel</a>

				</form><br />
				
				<!--cancel button that returns user to previous page--></div>
				
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