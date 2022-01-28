<?php
/*  Developer:   Justin Alho
 *  File Name:   addgh.php
 *  Description: Allows coordinators to add new group home records into the database
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
	        <title>Add New Group Home</title>
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
			$super = '';
			$name = '';
			$phone = '';
			$address = '';
			$city = '';
			
			$newerr = '';
			$superr = '';
			$namerr = '';
			$adderr = '';

            //connect to the database
			include "../dbseckey.php";
            $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);
		
			//if the form has been submitted
			if(isset($_POST['submit']))
			{	
				//set the error counter to 0
				$err = 0;
				
				//set variables to submitted values
				$super = $_POST['super'];
				$name = $_POST['name'];
				$phone = $_POST['phone'];
				$address = $_POST['address'];
				$city = $_POST['city'];
				
				//if required fields are blank, set the corresponding error message and increment error counter
				if($name == '')
				{
					$namerr = '<div class="badge badge-warning">Please enter a name to identify the group home.</div>';
					$err++;
				}
				
				if($address == '')
				{
					$adderr = '<div class="badge badge-warning">Please enter an address for the group home.</div>';
					$err++;
				}
				
				if($super == '')
				{
					$superr = '<div class="badge badge-warning">Please select a supervisor for the group home.</div>';
					$err++;
				}
				
				//check to see if group home already exists
				$checkSql = $conn->prepare("SELECT * FROM GROUP_HOME
				WHERE GH_NAME = '$name'
				OR GH_ADDRESS = '$address'");
				$checkSql->execute();
				$checkRow = $checkSql->fetchall();
				
				//if records already exist, increment error counter and set corresponding error message
				if(sizeof($checkRow) > 0)
				{
					if($checkRow[0]['GH_NAME'] == $name)
					{
						$err++;
						$namerr = '<div class="badge badge-warning">There is already a group home with that name.</div>';
					}
					if($checkRow[0]['GH_ADDRESS'] == $address)
					{
						$err++;
						$adderr = '<div class="badge badge-warning">There is already a group home with that address.</div>';
					}
				}
				
				//if there are no errors, add information into the database
				if($err == 0)
				{
					$sql = $conn->prepare("INSERT INTO 	GROUP_HOME (STAFF_ID, GH_NAME, GH_PHONE, GH_ADDRESS, GH_CITY) VALUES ('$super', '$name', '$phone', '$address', '$city')");
					
					$sql->execute();

					$id = $conn->lastInsertId();
					$code = 'G' . $id;
					$desc = 'The department for ' . $name . '.';
					
					$depsql = $conn->prepare("INSERT INTO DEPARTMENT (DEP_CODE, GH_ID, DEP_NAME, DEP_DESC) VALUES ('$code', '$id', '$name', '$desc')");
					$depsql->execute();
					
					//log whenever database is updated
					date_default_timezone_set("US/Mountain");
					//F j, Y, g:i a
					$dateString = date("r");
					file_put_contents("../logs/groupHomeAddLog.txt", "\n" . "Group home " . $name . " was added on: " . $dateString . " by " . $_SESSION['userName'] . ".", FILE_APPEND | LOCK_EX) ;
					
					//echo implode(":",$sql->errorInfo());
					
					//send the user back to this page with an empty form and a success message
					header('Location: addgh.php?s=1');
				}
			}
					
			//retrieve supervisor records from database
			$sql = $conn->prepare("SELECT STAFF_ID, STAFF_FNAME, STAFF_LNAME FROM STAFF WHERE TYPE_CODE = 'S'");
				
			$sql->execute();
			//echo implode(":",$sql->errorInfo());
			
			$row = $sql->fetchAll();
			
			
			//include navbar
			include "../includes/scripts/navBar.php";
			
			echo'<div class="conb">';
			echo'<div class="row justify-content-sm-center">';
	
			//if there is a successful database entry, display message

			
			
			//display the form
			printf("

				

				<form class='form-con' method='post' action='addgh.php'>
				");
				if(isset($_REQUEST['s']))
				echo "<div class='alert alert-success'>Record added successfully.</div>";	
				printf("
				
				<h1>Add New Group Home</h1>

					<!--display selection of supervisors-->
					Supervisor:<div class='form-row'>
						<select class='fanc' name='super'>
							<option value=''>Select a supervisor:</option>");
			foreach($row as $data)
				echo "<option value='{$data['STAFF_ID']}'>{$data['STAFF_FNAME']} {$data['STAFF_LNAME']}</option>";
			printf("
						</select>$superr<br /><br />\n
						
					</div>Group Home Name:<div class='form-row'><div class='col-12'>
						<input class='form-fan' type='text' name='name' value='$name'>$namerr<br /><br />\n	
					
					</div></div>Group Home Phone Number:<div class='form-row'><div class='col-12'>
						<input class='form-fan' type='tel' name='phone' pattern='[0-9]{3}-[0-9]{3}-[0-9]{4}' value='$phone'><br />
						<p class='text-danger'>Format: 000-000-0000</p>\n
												
					</div></div>Group Home Address:<div class='form-row'><div class='col-12'>
						<input class='form-fan' type='text' name='address' value='$address'>$adderr<br /><br />\n
						
					</div></div>Group Home City:<div class='form-row'><div class='col-12'>
						<input class='form-fan' type='text' name='city' value='$city'><br /><br />\n
					
					</div></div><input  type='submit' name='submit' value='Submit' class='btn btn-primary'>\n
					<a href='/land.php' class='btn btn-danger'>Cancel</a>

				</form>

				<!--cancel button that returns user to previous page-->
			
			");
						
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
			
			//include footer
			echo'
	</div>
</div>';
	include "../includes/scripts/footer.php";
	echo'
</body>
</html>
    ';
	?>