<?php
/*  Developer:   Justin Alho
 *  File Name:   changepass.php
 *  Description: Allows workers to change their password
 *  Date Start:  20/03/2020
 *  Date End:    TBD
 */?>
  <?php
echo'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	               <title>Change Password</title>
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

			//Starting a session and initializing variables needed
			session_start(); 
			$userType = $_SESSION['userType'];
			
			//include links to css, javascript, etc.
			include "../includes/scripts/headLinks2.0.php";
			
			//include functions used for checking password complexity
			include "../includes/functions/isComplex.php";
			include "../includes/functions/isSpecial.php";
			
			//to verify the user 
			include "../includes/functions/verLogin.php";
			verLogin();

            //connect to the database
			include "../dbseckey.php";
            $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);
		
			//initialize variables
			$id = $_SESSION['staffID'];
			$oPass = '';
			$pass1 = '';
			$pass2 = '';			
			$operr = '';
			$paserr = '';
			
			//if the form has been submitted
			if(isset($_POST['submit']))
			{	
				//set the error counter to 0
				$err = 0;
				
				//set variables to submitted values
	
				$oPass = $_POST['op'];
				$pass1 = $_POST['pass1'];
				$pass2 = $_POST['pass2'];
				
				$pasql = $conn->prepare("SELECT USER_PASS FROM STAFF WHERE STAFF_ID = ?");
				$params = array($id);
				$pasql->execute($params);
				$row = $pasql->fetch();
				$hash = $row['USER_PASS'];
				
				//validate old password, set old password error message and increment error counter if not
				if($oPass == '')
				{
					$operr = '<div class="badge badge-warning">Please enter your old password.</div>';
					$err++;
				}
				else if(!password_verify($oPass, $hash))
				{
					$operr = '<div class="badge badge-warning">Your old password was incorrect.</div>';
					$err++;
				}
				
				//check to make sure the new password is valid,
				//set password error message and increment error counter if not
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
				else if(!isComplex($pass2))
				{
					$paserr = '<div class="badge badge-warning">Password is not complex enough.</div>';
					$err++;
				}
				else
				$pass = $pass1;
				
				//if there are no errors, add information into the database
				if($err == 0)
				{
				
					//hash the new password, update database record
					$pass = password_hash($pass, PASSWORD_BCRYPT);
					$sql = $conn->prepare("UPDATE STAFF SET USER_PASS = '$pass' WHERE STAFF_ID = ?");
				
					$sql->execute($params);
					
					//echo implode(":",$sql->errorInfo());
					
					//send the user back to home with a success message
					header ("Location: ../land.php?p=1");
				}
			}
			
			//set ID variable to worker's ID session variable
			
			
			//include navbar
			include "../includes/scripts/navBar.php";
						echo'<div class="bodD">';
			echo'<div class="row justify-content-sm-center">';
			
			//display the form
			printf("

			

				<form class='form-con' method='post' action='changepass.php'>
				<h1>Change Password</h1><br />

					
					Old Password:<div class='form-row'><div class='col-12'> <input class='form-fan' type='password' name='op' value='$oPass'> $operr<br /><br />

					</div></div>New password:<div class='form-row'><div class='col-12'> <input class='form-fan' type='password' name='pass1' value='$pass1'><br />
					</div></div>Confirm password:<div class='form-row'><div class='col-12'> <input class='form-fan' type='password' name='pass2' value=''>$paserr<br /><br /></div></div>
					<p class='text-danger'>Passwords need to be at least 8 characters long and include a number,<br />
						a lowercase letter, an uppercase letter, and a special character.</p>
					
					<input class='btn btn-primary' type='submit' name='submit' value='Submit' class='btn btn-primary'>
									<a href='viewstaff.php' class='btn btn-danger'>Cancel</a>

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
            

			</form>
		</form>
	</div>
</div>';

include "../includes/scripts/footer.php";

	echo'
</body>
</html>
    ';
	?>