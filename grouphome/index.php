<?php
/*  Developer:   Justin Alho
 *  File Name:   grouphome/index.php
 *  Description: The index for the group home directory, allows access to files in the directory
 *  Date Start:  14/03/2020
 *  Date End:    TBD
 */
session_start(); ?>
<html>
    
    <head>

        <title>Group Home Management</title>
		
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

				<h1>Group Homes</h1>
				<a href="/grouphome/addgh.php" class="btn btn-primary">Add Group Home</a><br />
				<a href="/grouphome/viewgh.php" class="btn btn-primary">Manage Group Homes</a><br /><br />
				
				<a href="/land.php" class="btn btn-secondary">Back to Home</a><br /><br />
			');
			
			//include footer
			include "../includes/scripts/footer.php";
			
        ?>

    </body>

</html>