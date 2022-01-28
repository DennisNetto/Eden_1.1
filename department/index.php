<?php
/*  Developer:   Justin Alho
 *  File Name:   department/index.php
 *  Description: The index for the department directory, allows access to files in the directory
 *  Date Start:  14/03/2020
 *  Date End:    TBD
 */
 session_start(); ?>
<html>
    
    <head>

        <title>Department Management</title>
		
		<?php 
		//include links to css, javascript, etc.
		include "../includes/scripts/headLinks2.0.php"; ?>

    </head>
    
    <body>

        <?php
			
			//level of authorization required to access page
			$authLevel = "C";
			
			//to verify the user 
			include "../includes/functions/verLogin.php";
			verLogin();
			
			//Setting the userType var
			$userType = $_SESSION['userType'];
			
			//to verify the user's type
			include "../includes/functions/valUserType.php";
			valUserType($authLevel);
			
			//include navbar
			include "../includes/scripts/navBar.php";
			
			echo "<br />";
			
			//display menu
			printf('
				
				<h1>Departments</h1>
				<a href="/department/adddep.php" class="btn btn-primary">Add Department</a><br />
				<a href="/department/viewdep.php" class="btn btn-primary">Manage Departments</a><br /><br />
				
				<a href="/land.php" class="btn btn-secondary">Back to Home</a><br /><br />
			');
			
			//include footer
			include "../includes/scripts/footer.php";

        ?>

    </body>

</html>