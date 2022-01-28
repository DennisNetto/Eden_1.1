<?php
/*  Developer:   Justin Alho
 *  File Name:   moddep.php
 *  Description: Allows coordinators to modify existing department records
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
                   <title>Modify Department</title>
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
			$code = '';
			$status = '';
			$name = '';
			$desc = '';
			
			$namerr = '';
		
			//if the form has been submitted
			if(isset($_POST['submit']))
			{	
				//set error counter to 0
				$err = 0;
				
				//set variables to submitted values
				$code = $_POST['code'];
				$status = $_POST['status'];
				$name = $_POST['name'];
				$desc = $_POST['desc'];
				
				//if name is blank, increment error counter and set name error message
				if($name == '')
				{
					$namerr = '<div class="badge badge-warning">Please enter a name for the department.</div>';
					$err++;
				}
				
				//if there are no errors, add information into database
				if($err == 0)
				{
					$sql = $conn->prepare("UPDATE DEPARTMENT SET DEP_STATUS = '$status', DEP_NAME = '$name', DEP_DESC = '$desc' WHERE DEP_CODE = '$code'");
					
					$sql->execute();
					
					//log whenever the database is updated
					date_default_timezone_set("US/Mountain");
					//F j, Y, g:i a
					$dateString = date("r");
					file_put_contents("../logs/departmentModLog.txt", "\n" . "Department " . $name . " was modified on: " . $dateString . " by " . $_SESSION['userName'] . ".", FILE_APPEND | LOCK_EX) ;
					
					//echo implode(":",$sql->errorInfo());
					
					//send user back to list of departments with a success message
					header("Location: viewdep.php?p=1");
				}
			}
			
			//set code variable to code sent by viewgh.php
			$code = $_REQUEST['code'];
			
			//retrieve selected group home's information from database
			$sql = $conn->prepare("SELECT DEP_CODE, DEP_STATUS, C_S_STATUS_NAME, DEP_NAME, DEP_DESC
			FROM DEPARTMENT
			LEFT JOIN C_S_STATUS
			ON DEPARTMENT.DEP_STATUS = C_S_STATUS.C_S_STATUS_CODE
			WHERE DEP_CODE = '$code'");
			
			$sql->execute();
			
			$row = $sql->fetch();
			
			//retrieve list of statuses from database
			$stasql = $conn->prepare("SELECT * FROM C_S_STATUS
			WHERE C_S_STATUS_CODE != '{$row['DEP_STATUS']}'");
			$stasql->execute();
			$starow = $stasql->fetchAll();
			
						//include navbar
			include "../includes/scripts/navBar.php";
			echo'<div class="conb">';
			echo'<div class="row justify-content-sm-center">';
			
			//display the form
			printf("

				

				<form class='form-con' method='post' action='moddep.php'>
				<h1>Modify Department</h1>
						
					Department Code: $code<br /><br />\n
					<input class='form-fan' type='hidden' name='code' value='$code'>
						
					Department Name:
						<input class='form-fan' type='text' name='name' value='{$row['DEP_NAME']}'>$namerr<br /><br />\n
						
					<!--display selection of statuses-->
					Status:
						<select class='fanc' name='status'>
							<option value='{$row['DEP_STATUS']}'>{$row['C_S_STATUS_NAME']}</option>");
			foreach($starow as $data)
				echo "<option value='{$data['C_S_STATUS_CODE']}'>{$data['C_S_STATUS_NAME']}</option>";
			printf("
						</select><br /><br />\n
					
					Department Description:<br />
						<textarea name='desc' rows='3' cols='30'>{$row['DEP_DESC']}</textarea><br /><br />\n

					<input  type='submit' name='submit' value='Update' class='btn btn-primary'>\n
									<!--cancel button that returns user to previous page-->
				<a href='viewdep.php' class='btn btn-danger'>Cancel</a>

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