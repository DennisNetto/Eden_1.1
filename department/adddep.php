<?php
/*  Developer:   Justin Alho
 *  File Name:   adddep.php
 *  Description: Allows coordinators to add new department records into the database
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
    <title>Add New Department</title>
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
			//Starting a session and initializing variables needed
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
			$code = '';
			$name = '';
			$desc = '';
			
			$coderr = '';
			$namerr = '';

            //connect to the database
			include "../dbseckey.php";
            $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);
		
			//if the form has been submitted
			if(isset($_POST['submit']))
			{	
				//set error counter to 0
				$err = 0;
				
				//set variables to submitted values
				$code = $_POST['code'];
				$name = $_POST['name'];
				$desc = $_POST['desc'];
				
				//if the code is blank or longer than 3 characters,
				//increment the error counter and set the code error message
				if($code == '' || strlen($code) > 3)
				{
					$coderr = '<div class="badge badge-warning">Please enter a valid 3-character code.</div>';
					$err ++;
				}
				
				//if the name of the department is blank,
				//increment the error counter and set the name error message
				if($name == '')
				{
					$namerr = '<div class="badge badge-warning">Please enter a name for the department.</div>';
					$err ++;
				}
				
				//check to see if department already exists
				$checkSql = $conn->prepare("SELECT * FROM DEPARTMENT
				WHERE DEP_CODE = '$code'
				OR DEP_NAME = '$name'");
				$checkSql->execute();
				$checkRow = $checkSql->fetchAll();
				
				//if there is a department with the same code or the same name,
				//increment error counter and set corresponding error message
				if(sizeof($checkRow) > 0)
				{
					if($checkRow[0]['DEP_CODE'] == $code)
					{
						$err++;
						$coderr = '<div class="badge badge-warning">That code is already in use for another department.</div>';
					}
					if($checkRow[0]['DEP_NAME'] == $name)
					{
						$err++;
						$namerr = '<div class="badge badge-warning">That name is already in use for another department.</div>';
					}
				}
				
				//if there are no errors, add information into the database
				if($err == 0)
				{	
					
					$sql = $conn->prepare("INSERT INTO DEPARTMENT (DEP_CODE, DEP_NAME, DEP_DESC) VALUES ('$code', '$name', '$desc')");
					
					$sql->execute();
					
					//log whenever the database is updated
					date_default_timezone_set("US/Mountain");
					//F j, Y, g:i a
					$dateString = date("r");
					file_put_contents("../logs/departmentAddLog.txt", "\n" . "Department " . $name . " was added on: " . $dateString . " by " . $_SESSION['userName'] . ".", FILE_APPEND | LOCK_EX) ;
					
					//echo implode(":",$sql->errorInfo());
					
					//send the user back to this page with an empty form and a success message
					header('Location: adddep.php?s=1');
				}
			}	
			
			//include navbar
include "../includes/scripts/navBar.php";
			echo'<div class="conb">';
			echo'<div class="row justify-content-sm-center">';
			
			
			//display the form
			printf("

				

				<form class='form-con' method='post' action='adddep.php'>
				");
				
					//if there is a successful database entry, display message
			if(isset($_REQUEST['s']))
				echo "<div class='alert alert-success'>Record added successfully.</div>";	
				
					printf("
				
				<h1>Add New Department</h1>
						
					Department Code:<div class='form-row'><div class='col-12'>
						<input class='form-fan' type='text' name='code' value='$code'>$coderr<br />
						<p class='text-danger'>Department codes must be 3 characters or less</p>\n	
						
					</div></div>Department Name:<div class='form-row'><div class='col-12'>
						<input class='form-fan' type='text' name='name' value='$name'>$namerr<br /><br />\n

					</div></div>Department Description:<br />
						<textarea class='form-fan' name='desc' rows='3' cols='30'></textarea><br /><br />\n

					<input  type='submit' name='submit' value='Submit' class='btn btn-primary'>\n
					<a href='/land.php' class='btn btn-danger'>Cancel</a>
				</form>
				
				<!--cancel button that returns user to previous page-->
				
			");
						
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
			
echo'
            


	</div>
</div>';
	include "../includes/scripts/footer.php";
	echo'
</body>
</html>
    ';
	?>
	

</html>