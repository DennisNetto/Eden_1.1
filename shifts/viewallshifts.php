<?php
/*  Developer:   Justin Alho
 *  File Name:   viewallshifts.php
 *  Description: Allows coordinators to view existing shifts and select to modify them
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
	        <title>View Shift Information</title>
    <title>Table</title>
	<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="../css/table.css" rel="stylesheet" type="text/css">

</head>
<body>
';
		
			#Starting a session and initilizing variables needed
			session_start(); 
			$userType = $_SESSION['userType'];
		
		
		 include "../includes/scripts/headLinks2.0.php";
		include "../includes/scripts/navBar.php";
		 echo'

					 <div class="conb">
<div class="row justify-content-center">
<form class="form-con">
    <form>

';

		
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

			//This includes the setup for paging
			$feels = "";
			$sorta = "";
			if(isset($_GET['page']))
			{
				$page = $_GET['page'];
			}
			else
			{
				$page = 1;
			}
			$start_from = ($page-1)*5;
			$entries_per_page = 5;
			$entries = 0;
			
			

			$stm1 = "SELECT * FROM SHIFT";
			

			$sql = $conn->prepare($stm1);
				
			$sql->execute();
			
			$row1 = $sql->fetchAll();
			//finds the number entries to calculate paging
			foreach ($row1 as $data)
			{

				$newws = $data;
				$entries++;
				

			}

			$conn = null;
			$sql = null;
			
			//retrieve shift records from database
			$conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);
			$sql = $conn->prepare("SELECT SHIFT_ID, SHIFT.STATUS_CODE, STATUS_NAME, DEP_NAME, SHIFT.CLIENT_ID, CLIENT_FNAME, CLIENT_LNAME, SHIFT.STAFF_ID, STAFF_FNAME, STAFF_LNAME, SHIFT_DATE, SCHEDULED_START, SCHEDULED_END, CLAIMED_START, CLAIMED_END, APPROVED_START, APPROVED_END, SHIFT_SUPER, SHIFT_NOTES
			FROM SHIFT
			LEFT JOIN SHIFT_STATUS
			ON SHIFT.STATUS_CODE = SHIFT_STATUS.STATUS_CODE
			LEFT JOIN DEPARTMENT
			ON SHIFT.DEP_CODE = DEPARTMENT.DEP_CODE
			LEFT JOIN CLIENT
			ON SHIFT.CLIENT_ID = CLIENT.CLIENT_ID
			LEFT JOIN STAFF
			ON SHIFT.STAFF_ID = STAFF.STAFF_ID
			ORDER BY SHIFT_DATE DESC LIMIT $start_from,$entries_per_page");
				
			$sql->execute();
			
			$row = $sql->fetchAll();
			
			//include navbar
			
			
			//set up table headers
			echo
			"<table class='table-sm' border='1'>
				<tr>
					<th>Client</th>
					<th>Staff</th>
					<th>Department</th>
					<th>Date</th>
					<th>Status</th>
					<th>Scheduled Time</th>
					<th>Claimed Time</th>
					<th>Approved Time</th>
					<th>Staff is the Supervisor</th>
					<th>Notes</th>
					<th></th>
				</tr>
			";
			
			//fill the table with records from the database
			foreach ($row as $data)
			{
				echo "<tr>";
				echo "<td>CID({$data['CLIENT_ID']}) {$data['CLIENT_LNAME']}, {$data['CLIENT_FNAME']}</td>";
				echo "<td>ID({$data['STAFF_ID']}) {$data['STAFF_LNAME']}, {$data['STAFF_FNAME']}</td>";
				echo "<td>{$data['DEP_NAME']}</td>";
				echo "<td>{$data['SHIFT_DATE']}</td>";
				echo "<td>{$data['STATUS_NAME']}</td>";
				echo "<td>" . date('h:i a', strtotime($data['SCHEDULED_START'])) . '-' . date('h:i a', strtotime($data['SCHEDULED_END'])) . "</td>";
				if($data['STATUS_CODE'] == 'C' || $data['STATUS_CODE'] == 'A')
					echo "<td>" . date('h:i a', strtotime($data['CLAIMED_START'])) . '-' . date('h:i a', strtotime($data['CLAIMED_END'])) . "</td>";
				else
					echo "<td></td>";
				if($data['STATUS_CODE'] == 'A')
					echo "<td>" . date('h:i a', strtotime($data['APPROVED_START'])) . '-' . date('h:i a', strtotime($data['APPROVED_END'])) . "</td>";
				else
					echo "<td></td>";
				
				if($data['SHIFT_SUPER'] == 1)
					echo "<td>Yes</td>";
				else
					echo "<td>No</td>";
				echo "<td style='width: 27%;><div class ='scro'><textarea class ='scro'>{$data['SHIFT_NOTES']}</textarea></div></td>";
				echo "<td><a href='modshift.php?id={$data['SHIFT_ID']}'class='btn btn-info'>modify</a></td>";
				echo "</tr>";
			}
			
			//end the table
			echo "</table><br /><br />";



			
			$totalpages = ceil($entries/$entries_per_page);
			//Adds the paging buttons
			echo'<span class="badge bg-primary">';
			echo '<h3>Page:'.$page;
			echo '</h3><ul class="pagination">';
			if ($page != 1){
				//Adds previous and first buttons if the page is not the fist page.
				$pre = $page - 1;
				echo "<a href='viewallshifts.php?page=".'1'."' class='page-link'>First</a>";
				echo "<a href='viewallshifts.php?page=".$pre."' class='page-link'>Previous</a>";
			
			}
			//fixes pages from one to six, if there are more pages after they will show after 6 else it only gose to five.
			if ($page <= 5){

			if ($totalpages <= 5)
			{
				$z = $totalpages;
			}

			if ($totalpages > 5){
				$z = 6;
			}

		
			for($t=1;$t<=$z;$t++)
			{
				echo "<a href='viewallshifts.php?page=".$t."' class='page-link'>$t</a>";
			}
		}
		

		else
		{
			$en = $page + 2;
			if ($en > $totalpages)
			{
				$en = $totalpages;
			}
			for($t=$page - 2;$t<=$en;$t++)
			{
				echo "<a href='viewallshifts.php?page=".$t."' class='page-link'>$t</a>";
			}

		}
			//adds next and last page buttons if the page is not the last page.
			if ($page != $totalpages){

				$nex = $page + 1;
			echo "<a href='viewallshifts.php?page=".$nex."' class='page-link disabled'>Next</a>";
			echo "<a href='viewallshifts.php?page=".$totalpages."' class='page-link disabled'>Last</a>";
			}

		  echo '<div class="dropdown">
			<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">Skip To <span class="caret"></span></button>
			<ul class="dropdown-menu scrollable-menu" role="menu">';
			for($t=1;$t<=$totalpages;$t++)
			{
				echo"<li><a class='dropdown-item' href='viewallshifts.php?page=".$t."'>Page:$t</a></li>";
			}
			echo'</ul>
		  </div>';




			echo"<a href='viewshift.php' class='btn btn-primary'>Back</a><br /><br />";
							
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
			
			//include navbar
echo'
            

    </form>
</form>
</div></div></div></div>';
	include "../includes/scripts/footer.php";
	echo'
</body>
</html>
    ';
	?>