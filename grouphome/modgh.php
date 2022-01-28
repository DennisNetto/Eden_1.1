<?php
/*  Developer:   Justin Alho
 *  File Name:   modgh.php
 *  Description: Allows coordinators to modify existing group home records
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
             <title>Modify Group Home</title>
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
			$super = '';
			$name = '';
			$phone = '';
			$address = '';
			$city = '';
			
			$namerr = '';
		
			//if the form has been submitted
			if(isset($_POST['submit']))
			{
				//set error counter to 0
				$err = 0;
				
				//set variables to submitted values
				$id = $_POST['id'];
				$status = $_POST['status'];
				$super = $_POST['super'];
				$name = $_POST['name'];
				$phone = $_POST['phone'];
				$address = $_POST['address'];
				$city = $_POST['city'];
				
				//if name is blank, increment error counter and set name error message

				$id2 = $_REQUEST['id'];
				$qry = $conn->prepare("SELECT GH_NAME, GH_ID FROM group_home WHERE GH_NAME = '$name'");
				
				$qry->execute();
				
				$qryArray = $qry->fetchAll();
				
				if(sizeof($qryArray) > 0)
				{
					if($qryArray[0]['GH_ID'] != $id2)
					$err++;
					$namerr = '<div class="badge badge-warning">That group home name is already in use.</div>';
				}


				if($name == '')
				{
					$namerr = '<div class="badge badge-warning">Please enter a name to identify the group home.</div>';
					$err++;
				}
				
				//if there are no errors, add information into database
				if($err == 0)
				{
					$sql = $conn->prepare("UPDATE GROUP_HOME SET GH_STATUS = '$status', STAFF_ID = '$super', GH_NAME = '$name', GH_PHONE = '$phone', GH_ADDRESS = '$address', GH_CITY = '$city' WHERE GH_ID = '$id'");
					
					//set the description for the corresponding department record
					$desc = 'This is the department for ' . $name . '.';
					
					//update department record
					$depsql = $conn->prepare("UPDATE DEPARTMENT SET DEP_STATUS = '$status', DEP_NAME = '$name', DEP_DESC = '$desc' WHERE GH_ID = '$id'");
					
					$sql->execute();
					$depsql->execute();
					
					//log whenever the database is updated
					date_default_timezone_set("US/Mountain");
					//F j, Y, g:i a
					$dateString = date("r");
					file_put_contents("../logs/groupHomeModLog.txt", "\n" . "Group home " . $name . " was modified on: " . $dateString . " by " . $_SESSION['userName'] . ".", FILE_APPEND | LOCK_EX) ;
					
					//echo implode(":",$sql->errorInfo()) . "<br>";
					
					//send user back to list of group homes with a success message
					header("Location: viewgh.php?p=1");
				}
			}
			
			//set ID variable to ID sent by viewgh.php
			$id = $_REQUEST['id'];
					
			//retrieve selected group home's information from database
			$sql = $conn->prepare("SELECT GH_ID, GROUP_HOME.STAFF_ID, GH_STATUS, C_S_STATUS_NAME, STAFF_FNAME, STAFF_LNAME, GH_NAME, GH_PHONE, GH_ADDRESS, GH_CITY
			FROM GROUP_HOME
			LEFT JOIN STAFF
			ON GROUP_HOME.STAFF_ID = STAFF.STAFF_ID
			LEFT JOIN C_S_STATUS
			ON GROUP_HOME.GH_STATUS = C_S_STATUS.C_S_STATUS_CODE
			WHERE GH_ID = '$id'");
				
			$sql->execute();
			
			$row = $sql->fetch();
			
			//retrieve list of statuses from database
			$stasql = $conn->prepare("SELECT * FROM C_S_STATUS
			WHERE C_S_STATUS_CODE != '{$row['GH_STATUS']}'");
			$stasql->execute();
			$starow = $stasql->fetchAll();
			
			//retrieve list of supervisors from database
			$supsql = $conn->prepare("SELECT STAFF_ID, STAFF_FNAME, STAFF_LNAME FROM STAFF
			WHERE TYPE_CODE = 'S'
			AND STAFF_ID != '{$row['STAFF_ID']}'");
			$supsql->execute();
			$suprow = $supsql->fetchAll();
			
			//include navbar
			include "../includes/scripts/navBar.php";
			echo'<div class="conb">';
			echo'<div class="row justify-content-sm-center">';
			
			
			//display the form
			printf("

				

				<form class='form-con' method='post' action='modgh.php'>'
				<h1>Modify Group Home</h1>
						
					<input class='form-fan' type='hidden' name='id' value='$id'>
						
					Group Home Name:<div class='form-row'><div class='col-12'>
						<input class='form-fan' type='text' name='name' value='{$row['GH_NAME']}'>$namerr<br /><br />\n
						
					<!--display selection of statuses-->
					</div></div>Status:
						<select class='fanc' name='status'>
							<option value='{$row['GH_STATUS']}'>{$row['C_S_STATUS_NAME']}</option>");
			foreach($starow as $data)
				echo "<option value='{$data['C_S_STATUS_CODE']}'>{$data['C_S_STATUS_NAME']}</option>";
			printf("
						</select><br /><br />\n
						
					<!--display selection of supervisors-->
					Supervisor:
						<select class='fanc' name='super'>
							<option value='{$row['STAFF_ID']}'>{$row['STAFF_FNAME']} {$row['STAFF_LNAME']}</option>");
			foreach($suprow as $data)
				echo "<option value='{$data['STAFF_ID']}'>{$data['STAFF_FNAME']} {$data['STAFF_LNAME']}</option>";
			printf("
						</select><br /><br />\n
						
					Group Home Phone:<div class='form-row'><div class='col-12'>
						<input class='form-fan' type='tel' name='phone' pattern='[0-9]{3}-[0-9]{3}-[0-9]{4}' value='{$row['GH_PHONE']}'><br />
						</div></div><p class=text-danger>Format: 000-000-0000</p>\n

					Group Home Address:<div class='form-row'><div class='col-12'>
						<input class='form-fan' type='text' name='address' value='{$row['GH_ADDRESS']}'>\n
						
					</div></div>Group Home City:<div class='form-row'><div class='col-12'>
						<input class='form-fan' type='text' name='city' value='{$row['GH_CITY']}'><br /><br />\n
						
					</div></div><input  type='submit' name='submit' value='Update' class='btn btn-primary'>\n
									<a href='viewgh.php' class='btn btn-danger'>Cancel</a>

				</form>

				<!--cancel button that returns user to previous page-->
			>
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