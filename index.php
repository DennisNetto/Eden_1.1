<?php
/*  Developer: 	 Harley Lenton
 *  File Name:   index.php
 *  Description: Allows users to log in
 *  Date Start:  14/03/2020
 *  Date End:    TBD
 */?>
 <?php
echo'<html>

    <head>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>Log in</title>
		
<style>



#cap {display:none;color:red}
</style>

		
<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="../css/table.css" rel="stylesheet" type="text/css">
	<link href="../css/footer.css" rel="stylesheet" type="text/css">
    </head>



    <body>';


        
			//start the session
            session_start();
			error_reporting(0);
			//include links to css, javascript, etc.
		include "./includes/scripts/headLinks.php"; 
			
			//if staff are already logged in, they are redirected to their homepage
			if(isset($_SESSION['staffID']))
				header('Location: /land.php');
			
			//initialize variables
			$userType = "";
			$userName = '';
			$password = '';
			
			//include functions for checking passwords
			include "./includes/functions/isSpecial.php";
			include "./includes/functions/isComplex.php";

            //Include header
            include "./includes/scripts/navBar.php";
			echo "<br /><br />";

			//if the login request has been submitted
			if (isset($_REQUEST['login']))
			{
				//Log in will give them the proper session variables if they are legit
				include "./includes/scripts/logIn.php";
			}
			
			//print login form
			print("
					<section class='container-fluid'>
					<section class='row justify-content-center'>
					<section class='col-12 col-sm-6 col-lg-3'>
					<div class='bodyDiv'>
						<div class='form-con'>
							<br />
							<form method='post' action='index.php'>
								Username<br />
								<input type='text' name='userName' value='$userName' class='form-control' placeholder='User Name' /><br /><br />
								Password<br />
								<input type='password' id='pas' name='password' class='form-control' /><br /><br />
								<p id='cap'>Caps lock is ON.</p>
								<input type='submit' name='login' value='Login' class='btn btn-success btn-lg'>
							</form>
						</div>
					</div>
					</section>
					</section>
					</section>
			");
			
			//include footer
			
            include "./includes/scripts/footer4.php";
			//This script tells is caps lock is on
        echo'
		<script>
		var input = document.getElementById("pas");
		var text = document.getElementById("cap");
		input.addEventListener("keyup", function(event) {
		
		if (event.getModifierState("CapsLock")) {
			text.style.display = "block";
		  } else {
			text.style.display = "none"
		  }
		});
		</script>
    </body>


</html>
';
?>