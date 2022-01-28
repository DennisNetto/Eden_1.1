<?php
/*  Developer:   Justin Alho
 *  File Name:   modshift.php
 *  Description: Allows supervisors to select a group home they supervise to modify shifts for that group home
 *  Date Start:  10/03/2020
 *  Date End:    TBD
 */?>
   <?php
echo'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
             <title>Modify Schedule</title>
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
			$authLevel = "S";
			
			//to verify the user 
			include "../includes/functions/verLogin.php";
			verLogin();
			
			//to verify the users type
			include "../includes/functions/valUserType.php";
			valUserType($authLevel);

            //connect to the database
			include "../dbseckey.php";
            $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);
			
			//retrieve ID from session
			$id = $_SESSION['staffID'];
			
			//include navbar
			include "../includes/scripts/navBar.php";
			
					echo'<div class="bodD">';
			echo'<div class="row justify-content-sm-center">';
			
			echo "<br />";
			

			
			//display the form
			echo "<form class='form-con' action='supersched.php' method='post'> ";
			//if a shift was updated successfully, display success message
			if(isset($_REQUEST['p']))
				echo "<div class='badge badge-success'>Shift updated successfully.</div><br /><br />";
			
			//if the form was submitted without a group home selected, display an error message
			if(isset($_REQUEST['b']))
				echo "Please make a selection.<br /><br />";
			echo'<h2>Manage Group Home Shifts</h2>';
			echo'<h5>View shifts for:</h5><br /><br />';
			
				;
					
					//retrieve department/group home information from database
					$sql = $conn->prepare("SELECT DEP_CODE, DEP_NAME
					FROM DEPARTMENT
					LEFT JOIN GROUP_HOME
					ON DEPARTMENT.GH_ID = GROUP_HOME.GH_ID
					LEFT JOIN STAFF
					ON GROUP_HOME.STAFF_ID = STAFF.STAFF_ID
					WHERE GROUP_HOME.STAFF_ID = '$id'");
					$sql->execute();
					$row = $sql->fetchAll();
					
					//echo implode(":",$sql->errorInfo());
					
					//display selection of group homes
					echo "<select class='fanc'  name='id'>
						<option value=''>Select Group Home</option>";
					foreach($row as $data)
						echo "<option value='{$data['DEP_CODE']}'>{$data['DEP_NAME']}</option>";
				
					echo "</select><br /><br />
					
					<input class='btn btn-primary' type='submit' name='view' value='View Shifts' />
					<a href='/land.php' class='btn btn-secondary'>Back</a>
				</form><br />
				
			";
						
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
			
			//include footer
echo'
</div></div>';
	include "../includes/scripts/footer2.php";
	echo'
</body>
</html>
    ';
	?>