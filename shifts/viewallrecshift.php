<?php
/*  Developer:   Justin Alho
 *  File Name:   viewallrecshift.php
 *  Description: Allows coordinators to view all recurring shifts and select to modify them
 *  Date Start:  27/02/2020
 *  Date End:    TBD
 */?>
<html>

    <head>

        <title>View Recurring Shift Information</title>
		<style>
		/*
			button 
			{
			  background: none!important;
			  border: none;
			  padding: 0!important;
			  
			  color: white;
			  font-weight: bold;
			  text-decoration: none;
			  cursor: pointer;
			}
		*/
		
			.bodD
			{
				min-height: 85%;
			}
			html, body
			{
				height: 100%;
			}
		</style>
		<?php 
			//Starting a session and initializing variables needed
			session_start(); 
			$userType = $_SESSION['userType'];
			
			//include css, javascript, etc.
			 ?>
			
    </head>

    <body>

        <?php
		
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

			//varables
			$staff = 1000000;
			$low = 0;
			$name = '';
			if(isset($_POST['gen']))
			{
				$staff = $_POST['staff'];
				if ($staff != 1000000){
					$low = $staff;}
			}

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
			$start_from = ($page-1)*4;
			$entries_per_page = 4;
			$entries = 0;

			$stm1 = "SELECT REC_ID FROM REC_SHIFT";
			
			$stm = "SELECT REC_ID, DEP_NAME, REC_SHIFT.CLIENT_ID, CLIENT_FNAME, CLIENT_LNAME, REC_SHIFT.STAFF_ID, STAFF_FNAME, STAFF_LNAME, REC_DAY, REC_START, REC_END, REC_SUPER, REC_NOTES
			FROM REC_SHIFT
			LEFT JOIN DEPARTMENT
			ON REC_SHIFT.DEP_CODE = DEPARTMENT.DEP_CODE
			LEFT JOIN CLIENT
			ON REC_SHIFT.CLIENT_ID = CLIENT.CLIENT_ID
			LEFT JOIN STAFF
			ON REC_SHIFT.STAFF_ID = STAFF.STAFF_ID";

			if ($staff != 1000000){
			$OD = "WHERE REC_ID = $staff";
			$stm = $stm . $OD;
			}
			

			if ($_SESSION['order'] == null){
				$orderBy = " ORDER BY CLIENT_LNAME LIMIT $start_from,$entries_per_page";
				}
				$limit_eentries = " LIMIT $start_from,$entries_per_page";
			
			//sorting
			if( (isset($_REQUEST['sortBy']) ) && (!isset($_SESSION['sortBy']) ) )
			{
				switch($_REQUEST['sortBy'])
				{
					case 'cli':
						$_SESSION['sortBy'] = 'cli';
						$_SESSION['order'] = 1;
					break;
					
					case 'staff':
						$_SESSION['sortBy'] = 'staff';
						$_SESSION['order'] = 2;
					break;
					
					case 'dpt':
						$_SESSION['sortBy'] = 'dpt';
						$_SESSION['order'] = 3;
					break;
					
					case 'day':
						$_SESSION['sortBy'] = 'day';
						$_SESSION['order'] = 4;
					break;
					
					default:
					
					break;
				}
			}
			else if( (isset($_REQUEST['sortBy']) ) && (isset($_SESSION['sortBy']) ) )
			{
				switch($_REQUEST['sortBy'])
				{
					case 'cli':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -1;
					break;
					
					case 'staff':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -2;
					break;
					
					case 'dpt':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -3;
					break;
					
					case 'day':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -4;
					break;
					
					default:
					
					break;
				}
			}
			else
			{
				$stm = $stm . $orderBy;
			}

			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//sets sorting. This has been added to work with paging.
			if ($staff == 1000000){
				if ($_SESSION['order'] == 1){
	
					$orderBy = " ORDER BY CLIENT_FNAME DESC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == 2){
	
					$orderBy = " ORDER BY STAFF_FNAME DESC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == 3){
	
					$orderBy = " ORDER BY DEP_NAME DESC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == 4){
	
					$orderBy = " ORDER BY REC_DAY DESC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == -1){
	
					$orderBy = " ORDER BY CLIENT_FNAME ASC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == -2){
	
					$orderBy = " ORDER BY STAFF_FNAME ASC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == -3){
	
					$orderBy = " ORDER BY DEP_NAME ASC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == -4){
	
					$orderBy = " ORDER BY REC_DAY ASC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}

			}
					
			//retrieve recurring shift information from database
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

			$conn = new PDO("mysql:host=localhost; dbname=oldcount_edenbridge", $username, $password);
				
			$sql = $conn->prepare($stm);
			
			$sql->execute();
			
			$row = $sql->fetchAll();
			
			//include navbar
			include "../includes/scripts/navBar.php";
			include "../includes/scripts/headLinks2.0.php";
			echo'
					 <div class="conb">
<div class="row justify-content-center">
<form class="form-con">
    <form>
';
			
			//if recurring shift has been updated successfully, display success message
			if(isset($_REQUEST['p']))
				echo "Recurring shifts updated successfully.";
			
			//set up table headers
			echo
			"
			<table class='table-sm' border='1'>
				<form action='viewallrecshift.php' method='get' id='cliForm'>
					<input type='hidden' name='sortBy' value='cli'></input>
				</form>
				<form action='viewallrecshift.php' method='get' id='staffForm'>
					<input type='hidden' name='sortBy' value='staff'></input>
				</form>
				<form action='viewallrecshift.php' method='get' id='dptForm'>
					<input type='hidden' name='sortBy' value='dpt'></input>
				</form>
				<form action='viewallrecshift.php' method='get' id='dayForm'>
					<input type='hidden' name='sortBy' value='day'></input>
				</form>
				
				<tr>
					<th><button style='background: none;
										border: none;
										padding: 0;
			  
										color: white;
										font-weight: bold;
										text-decoration: none;
										cursor: pointer;' type='submit' form='cliForm' value='Client'>Client</button></th>
					<th><button style='background: none;
										border: none;
										padding: 0;
			  
										color: white;
										font-weight: bold;
										text-decoration: none;
										cursor: pointer;' type='submit' form='staffForm' value='Client'>Staff</button></th>
					<th><button style='background: none;
										border: none;
										padding: 0;
			  
										color: white;
										font-weight: bold;
										text-decoration: none;
										cursor: pointer;' type='submit' form='dptForm' value='Client'>Department</button></th>
					<th><button style='background: none;
										border: none;
										padding: 0;
			  
										color: white;
										font-weight: bold;
										text-decoration: none;
										cursor: pointer;' type='submit' form='dayForm' value='Client'>Day</button></th>
					<th>Time</th>
					<th>Staff is the Supervisor</th>
					<th>Notes</th>
					<th></th>
				</tr>
			";
			
			//fill table with records from the database
			foreach ($row as $data)
			{	
				echo "<tr>";
				echo "<td>CID({$data['CLIENT_ID']}) {$data['CLIENT_LNAME']}, {$data['CLIENT_FNAME']}</td>";
				echo "<td>ID({$data['STAFF_ID']}) {$data['STAFF_LNAME']}, {$data['STAFF_FNAME']}</td>";
				echo "<td>{$data['DEP_NAME']}</td>";
				echo "<td>{$data['REC_DAY']}</td>";
				echo "<td>{$data['REC_START']} - {$data['REC_END']}</td>";
				if($data['REC_SUPER'] == 1)
					echo "<td>Yes</td>";
				else
					echo "<td>No</td>";
				echo "<td style='width: 27%;><div class ='scro'><textarea class ='scro'>{$data['REC_NOTES']}</textarea></div></td>";
				echo "<td><a class='btn btn-info' href='modrecshift.php?id={$data['REC_ID']}'>modify</a></td>";
				echo "</tr>";
			}
				
			//end table
			echo "</table><br />\n";

			$totalpages = ceil($entries/$entries_per_page);
			//Adds the paging buttons
			echo'<span class="badge bg-primary">';
			echo '<h3>Page:'.$page;
			echo '</h3><ul class="pagination">';
			if ($page != 1){
				//Adds previous and first buttons if the page is not the fist page.
				$pre = $page - 1;
				echo "<a href='viewallrecshift.php?order=".$_SESSION['order']."&&page=".'1'."' class='page-link'>First</a>";
				echo "<a href='viewallrecshift.php?order=".$_SESSION['order']."&&page=".$pre."' class='page-link'>Previous</a>";
			
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
				echo "<a href='viewallrecshift.php?order=".$_SESSION['order']."&&page=".$t."' class='page-link'>$t</a>";
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
				echo "<a href='viewallrecshift.php?order=".$_SESSION['order']."&&page=".$t."' class='page-link'>$t</a>";
			}

		}
			//adds next and last page buttons if the page is not the last page.
			if ($page != $totalpages){

				$nex = $page + 1;
			echo "<a href='viewallrecshift.php?order=".$_SESSION['order']."&&page=".$nex."' class='page-link disabled'>Next</a>";
			echo "<a href='viewallrecshift.php?order=".$_SESSION['order']."&&page=".$totalpages."' class='page-link disabled'>Last</a>";
			}

			echo '<div class="dropdown">
			<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">Skip To <span class="caret"></span></button>
			<ul class="dropdown-menu scrollable-menu" role="menu">';
			for($t=1;$t<=$totalpages;$t++)
			{
				echo"<li><a class='dropdown-item' href='viewallrecshift.php?order=".$_SESSION['order']."&&page=".$t."'>Page:$t</a></li>";
			}
			echo'</ul>
		  </div>';
			
			echo "<a href='/land.php' class='btn btn-secondary'>Back</a><br /><br />";

			
			echo'</div>';
			
			//include footer
			include "../includes/scripts/footer.php";
			
		?>
	
	</body>
</html>