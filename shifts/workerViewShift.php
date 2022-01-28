<!DOCTYPE html>
<?php
/*  Developer:   Harley Lenton
 *  File Name:   workerViewShift.php
 *  Description: Allows workers to view details of their shifts
 *  Date Start:  08/03/2020
 *  Date End:    TBD
 */?>
 <html lang="en">

    <head>

        <title>View Shift</title>
		<?php 
			//Starting a session and initializing variables needed
			session_start(); 
			$userType = $_SESSION['userType'];
			//$sID = $_SESSION['staffID'];
			
			//include links to css, javascript, etc.
			include "../includes/scripts/headLinks2.0.php";
			print("
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
			");
		?>

    </head>

    <body>

        <?php
		
			//level of authorization required to access page
			$authLevel = "W";
			
			//to verify the user 
			include "../includes/functions/verLogin.php";
			verLogin();
			
			//to verify the user's type
			include "../includes/functions/valUserType.php";
			valUserType($authLevel);
			
			$id = $_REQUEST['id'];
			$sID = $_SESSION['staffID'];



            //connect to the database
			include "../dbseckey.php";
            $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);
			
			//retrieve shift information from database
			$sql = $conn->prepare("SELECT SHIFT_ID, SHIFT.STATUS_CODE, STATUS_NAME, SHIFT.DEP_CODE, DEP_NAME, SHIFT.CLIENT_ID, CLIENT_FNAME, CLIENT_LNAME, SHIFT.STAFF_ID, STAFF_FNAME, STAFF_LNAME, SHIFT_DATE,
			SCHEDULED_START, SCHEDULED_END, CLAIMED_START, CLAIMED_END, APPROVED_START, APPROVED_END, SHIFT_SUPER, SHIFT_NOTES
			FROM SHIFT
			LEFT JOIN SHIFT_STATUS
			ON SHIFT.STATUS_CODE = SHIFT_STATUS.STATUS_CODE
			LEFT JOIN DEPARTMENT
			ON SHIFT.DEP_CODE = DEPARTMENT.DEP_CODE
			LEFT JOIN CLIENT
			ON SHIFT.CLIENT_ID = CLIENT.CLIENT_ID
			LEFT JOIN STAFF
			ON SHIFT.STAFF_ID = STAFF.STAFF_ID
			WHERE SHIFT_ID = '$id' AND SHIFT.STAFF_ID = '$sID'
			");
				
			$sql->execute();
			
			$row = $sql->fetch();
			//echo implode(":",$sql->errorInfo()) . "<br>";
			
			//include navbar
			include "../includes/scripts/navBar.php";
			
			printf("
				<div class='conb'>
				<div class='row justify-content-md-center'><br />
					<form class='form-con'>

					<h1>View a Shift</h1>
				
				

						Client: " . $row['CLIENT_FNAME'] . " " . $row['CLIENT_LNAME'] . "<br /><br />\n
			");

			printf("
						Department: " . $row['DEP_NAME'] . "<br /><br />\n
			");

			printf("	
						Shift Status: " . $row['STATUS_NAME'] . "<br /><br />\n
			");
			
			//set date to time value
			$date = strtotime($row['SHIFT_DATE']);

			//print date in a more readable format
			printf("
						Shift Date: " . date('M j, Y', $date) . "<br /><br />\n	
			");
			
			//set start and end dates to time values
			$start = strtotime($row['SCHEDULED_START']);
			$end = strtotime($row['SCHEDULED_END']);
			
			//print time in a more readable format
			printf("
						<p>Hours: " . date('g:i A', $start) . "-" . date('g:i A', $end) . "</p>\n
			");
			
			if($row['SHIFT_SUPER'] == 1)
				echo "	<p>Supervisor: Yes</p>";
			else
				echo "	<p>Supervisor: No</p>";
			printf("
			
						Notes: " . $row['SHIFT_NOTES'] . "<br /><br />


			");
			
			//back button
			echo "		<a href='viewSched.php' class='btn btn-secondary'>Back</a>\n";
			echo "	</form>
				</div>
			</div>
			";
			
			//Releasing database connection and querys to save computational resourses
			$conn = null;
			$sql = null;
			
			//include footer
			include "../includes/scripts/footer.php";
        ?>

    </body>

</html>