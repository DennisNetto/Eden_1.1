<?php
/*  Developer:   Justin Alho, Harley Lenton, Evan Guest
 *  File Name:   viewstaff.php
 *  Description: Allows coordinators to view existing staff members and select to modify them
 *  Date Start:  25/02/2020
 *  Date End:    TBD
 *  TODO:    	 - add sorting, filtering
 */?>
<?php
echo'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	        <title>View Staff Information</title>
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

		.scrollable-menu {
			height: auto;
			max-height: 200px;
			overflow-x: hidden;
		  }		
		
	</style>
	<script>
	//this function should take in an id and a total and set the total to the spot with that id

	function setTotal(id, total)
	{
		var total = total.toFixed(1);
		document.getElementById(id).innerHTML = total;
	}
					//fill dropdown with client results

			//fill dropdown with staff results
			function staSearch(search) {
				if (window.XMLHttpRequest)
				{
					xmlhttp = new XMLHttpRequest();
				}
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("staSel").innerHTML = this.responseText;
					}
				};
				xmlhttp.open("GET","../includes/scripts/search2.php?s="+search,true);
				xmlhttp.send();
			}
			
			//retrieve list of available staff

</script>
</head>
<body>
';
			error_reporting(0);
			#Starting a session and initilizing variables needed
			session_start(); 
			$userType = $_SESSION['userType'];
		
		
		 include "../includes/scripts/headLinks2.0.php";
		include "../includes/scripts/navBar.php";


		
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
			//retrieve staff information from the database
			$stm1 = "SELECT STAFF_ID FROM STAFF";

			$stm = "SELECT STAFF_ID, C_S_STATUS_NAME, TYPE_NAME, USER_NAME, STAFF_FNAME, STAFF_LNAME, STAFF_PHONE, STAFF_ADDRESS, STAFF_CITY, CAN_CHILD, CAN_PC, CAN_DRIVE, SUN_AVAIL, MON_AVAIL, TUE_AVAIL, WED_AVAIL, THU_AVAIL, FRI_AVAIL, SAT_AVAIL, STAFF_NOTES
			FROM STAFF
			LEFT JOIN C_S_STATUS
			ON STAFF.STAFF_STATUS = C_S_STATUS.C_S_STATUS_CODE
			LEFT JOIN USER_TYPE
			ON STAFF.TYPE_CODE = USER_TYPE.TYPE_CODE
			
			";
			//if no sorting has been done use the defualt
			if ($staff != 1000000){
				$OD = "WHERE STAFF_ID = $staff";
				$stm = $stm . $OD;
			}
			if ($_SESSION['order'] == null){
			$orderBy = " ORDER BY STAFF_LNAME LIMIT $start_from,$entries_per_page";
			}
			$limit_eentries = " LIMIT $start_from,$entries_per_page";
			
			//sorting/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			if( (isset($_REQUEST['sortBy']) ) && (!isset($_SESSION['sortBy']) ) )
			{
				switch($_REQUEST['sortBy'])
				{
					case 'staff':
						$_SESSION['sortBy'] = 'staff';
						$_SESSION['order'] = 1;


						break;

					case 'status':
						$_SESSION['sortBy'] = 'status';
						$_SESSION['order'] = 2;


						break;

					case 'type':
						$_SESSION['sortBy'] = 'type';
						$_SESSION['order'] = 3;


						break;
						
					case 'city':
						$_SESSION['sortBy'] = 'city';
						$_SESSION['order'] = 4;


						break;
						
					case 'child':
						$_SESSION['sortBy'] = 'child';
						$_SESSION['order'] = 5;


						break;
						
					case 'person':
						$_SESSION['sortBy'] = 'person';
						$_SESSION['order'] = 6;


						break;
						
					case 'drive':
						$_SESSION['sortBy'] = 'drive';
						$_SESSION['order'] = 7;


						break;

					default:

						break;
				}
			}
			else if( (isset($_REQUEST['sortBy']) ) && (isset($_SESSION['sortBy']) ) )
			{
				switch($_REQUEST['sortBy'])
				{
					case 'staff':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -1;

						break;

					case 'status':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -2;

						break;

					case 'type':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -3;

						break;
						
					case 'city':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -4;

					
						break;
						
					case 'child':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -5;

					
						break;
						
					case 'person':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -6;

					
						break;
						
					case 'drive':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -7;
						
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

				$orderBy = " ORDER BY STAFF_LNAME DESC";
				$stm = $stm . $orderBy . $limit_eentries;

			}

			if ($_SESSION['order'] == 2){

				$orderBy = " ORDER BY C_S_STATUS_NAME DESC";
				$stm = $stm . $orderBy . $limit_eentries;

			}

			if ($_SESSION['order'] == 3){

				$orderBy = " ORDER BY TYPE_NAME DESC";
				$stm = $stm . $orderBy . $limit_eentries;

			}

			if ($_SESSION['order'] == 4){

				$orderBy = " ORDER BY STAFF_CITY DESC";
				$stm = $stm . $orderBy . $limit_eentries;

			}

			if ($_SESSION['order'] == 5){

				$orderBy = " ORDER BY CAN_CHILD DESC";
				$stm = $stm . $orderBy . $limit_eentries;

			}

			if ($_SESSION['order'] == 6){

				$orderBy = " ORDER BY CAN_PC DESC";
				$stm = $stm . $orderBy . $limit_eentries;

			}

			if ($_SESSION['order'] == 7){

				$orderBy = " ORDER BY CAN_DRIVE DESC";
				$stm = $stm . $orderBy . $limit_eentries;

			}

			if ($_SESSION['order'] == -1){

				$orderBy = " ORDER BY STAFF_LNAME ASC";
				$stm = $stm . $orderBy . $limit_eentries;

			}

			if ($_SESSION['order'] == -2){

				$orderBy = " ORDER BY C_S_STATUS_NAME ASC";
				$stm = $stm . $orderBy . $limit_eentries;

			}

			if ($_SESSION['order'] == -3){

				$orderBy = " ORDER BY TYPE_NAME ASC";
				$stm = $stm . $orderBy . $limit_eentries;

			}

			if ($_SESSION['order'] == -4){

				$orderBy = " ORDER BY STAFF_CITY ASC";
				$stm = $stm . $orderBy . $limit_eentries;

			}

			if ($_SESSION['order'] == -5){

				$orderBy = " ORDER BY CAN_CHILD ASC";
				$stm = $stm . $orderBy . $limit_eentries;

			}

			if ($_SESSION['order'] == -6){

				$orderBy = " ORDER BY CAN_PC ASC";
				$stm = $stm . $orderBy . $limit_eentries;

			}

			if ($_SESSION['order'] == -7){

				$orderBy = " ORDER BY CAN_DRIVE ASC";
				$stm = $stm . $orderBy . $limit_eentries;

			}
		}


		

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

			$conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);

			$sql = $conn->prepare($stm);
				
			$sql->execute();
			
			$row = $sql->fetchAll();


			//print_r($sql);
			

			
			//if there is a successful update from modstaff.php, display a success message
			if(isset($_REQUEST['p']))
			echo"<div class='alert alert-success'>Staff member modified successfully.</div>";
			
			//set up table headers
			printf( "
			<div class='alert alert-light'><form method='post' action='viewstaff.php'>
				Search for Staff:
						<input class='form-fan' type='text' name='' value='' onkeyup='staSearch(this.value)'>\n

					<!--display selection of staff members-->
					Staff results:
						<select class='fanc' name='staff' id='staSel'>
							<option value='1000000'>Search All:</option>");
							foreach($starow as $data)
				echo "<option value='{$data['STAFF_ID']}'>{$data['STAFF_LNAME']}, {$data['STAFF_FNAME']}</option><option value='1000000'>Search All:</option>";
			printf("
						</select>$staerr\n
				<input type='submit' name='gen' class='btn btn-primary btn-sm' value='Find Staff'>
			</form></div>\n");

			echo
			"
			<div class='bodD'>
			<table class='table-sm' border='1'>
				<form action='viewstaff.php' method='get' id='staffForm'>
					<input type='hidden' name='sortBy' value='staff'></input>
				</form>
				<form action='viewstaff.php' method='get' id='statForm'>
					<input type='hidden' name='sortBy' value='status'></input>
				</form>
				<form action='viewstaff.php' method='get' id='typeForm'>
					<input type='hidden' name='sortBy' value='type'></input>
				</form>
				<form action='viewstaff.php' method='get' id='cityForm'>
					<input type='hidden' name='sortBy' value='city'></input>
				</form>
				<form action='viewstaff.php' method='get' id='childForm'>
					<input type='hidden' name='sortBy' value='child'></input>
				</form>
				<form action='viewstaff.php' method='get' id='personForm'>
					<input type='hidden' name='sortBy' value='person'></input>
				</form>
				<form action='viewstaff.php' method='get' id='driveForm'>
					<input type='hidden' name='sortBy' value='drive'></input>
				</form>
				
					
				<tr>
					<th>
						<button style='background: none;
										border: none;
										padding: 0;
										color: white;
										font-weight: bold;
										text-decoration: none;
										cursor: pointer;' type='submit' form='staffForm' value='Client'>Staff
						</button>
					</th>
					<th>
						<button style='background: none;
										border: none;
										padding: 0;
										color: white;
										font-weight: bold;
										text-decoration: none;
										cursor: pointer;' type='submit' form='statForm' value='status'>Status
						</button>
					</th>
					<th>
						<button style='background: none;
										border: none;
										padding: 0;
										color: white;
										font-weight: bold;
										text-decoration: none;
										cursor: pointer;' type='submit' form='typeForm' value='type'>Staff Type
						</button>
					</th>
					<th>Username</th>
					<th>Phone Number</th>
					<th>Address</th>
					<th>
						<button style='background: none;
										border: none;
										padding: 0;
										color: white;
										font-weight: bold;
										text-decoration: none;
										cursor: pointer;' type='submit' form='cityForm' value='city'>City
						</button>
					</th>
					<th>
						<button style='background: none;
										border: none;
										padding: 0;
										color: white;
										font-weight: bold;
										text-decoration: none;
										cursor: pointer;' type='submit' form='childForm' value='child'>Can Work With Children
						</button>
					</th>
					<th>
						<button style='background: none;
										border: none;
										padding: 0;
										color: white;
										font-weight: bold;
										text-decoration: none;
										cursor: pointer;' type='submit' form='personForm' value='person'>Can Provide Personal Care
						</button>
					</th>
					<th>
						<button style='background: none;
										border: none;
										padding: 0;
										color: white;
										font-weight: bold;
										text-decoration: none;
										cursor: pointer;' type='submit' form='driveForm' value='drive'>Can Drive
						</button>
					</th>
					<th>Sunday Availability:</th>
					<th>Monday Availability:</th>
					<th>Tuesday Availability:</th>
					<th>Wednesday Availability:</th>
					<th>Thursday Availability:</th>
					<th>Friday Availability:</th>
					<th>Saturday Availability:</th>
					<th>Notes</th>
					<th></th>
				</tr>
			";
			
			//include function to convert availability to 12 hour time
			include "../includes/functions/convertHours.php";
			
			//fill table with records from database
			foreach ($row as $data)
			{	
				echo "<tr>";
				echo "<td>ID({$data['STAFF_ID']}) {$data['STAFF_LNAME']}, {$data['STAFF_FNAME']}</td>";
				echo "<td>{$data['C_S_STATUS_NAME']}</td>";
				echo "<td>{$data['TYPE_NAME']}</td>";
				echo "<td>{$data['USER_NAME']}</td>";
				echo "<td style='min-width: 110px;'>{$data['STAFF_PHONE']}</td>";
				echo "<td style='min-width: 135px;'>{$data['STAFF_ADDRESS']}</td>";
				echo "<td>{$data['STAFF_CITY']}</td>";
				if($data['CAN_CHILD'] == 1)
					echo "<td>Yes</td>";
				else
					echo "<td>No</td>";
				if($data['CAN_PC'] == 1)
					echo "<td>Yes</td>";
				else
					echo "<td>No</td>";
				if($data['CAN_DRIVE'] == 1)
					echo "<td>Yes</td>";
				else
					echo "<td>No</td>";
				
				/////////////////////////////////////////////////////
				/////////////Converting hour format here///////////
				
				$sunAvail = convertTime($data['SUN_AVAIL']);
				$monAvail = convertTime($data['MON_AVAIL']);
				$tueAvail = convertTime($data['TUE_AVAIL']);
				$wedAvail = convertTime($data['WED_AVAIL']);
				$thuAvail = convertTime($data['THU_AVAIL']);
				$friAvail = convertTime($data['FRI_AVAIL']);
				$satAvail = convertTime($data['SAT_AVAIL']);
				
				
				//test values
				//print($sunAvail . "<br />");
				//print($monAvail . "<br />");
				//print($tueAvail . "<br />");
				//print($wedAvail . "<br />");
				//print($thuAvail . "<br />");
				//print($friAvail . "<br />");
				//print($satAvail . "<br />");
				
				//var_dump(function_exists('convert'));
				//test
				//echo "<td>{$data['SUN_AVAIL']}</td>";
				
				echo "<td>" . $sunAvail . "</td>";
				echo "<td>" . $monAvail . "</td>";
				echo "<td>" . $tueAvail . "</td>";
				echo "<td>" . $wedAvail . "</td>";
				echo "<td>" . $thuAvail . "</td>";
				echo "<td>" . $friAvail . "</td>";
				echo "<td>" . $satAvail . "</td>";
				echo "<td style='width: 110%;><div class ='scro'><textarea class ='scro'>{$data['STAFF_NOTES']}</textarea></div></td>";
				echo "<td><a href='modstaff.php?id={$data['STAFF_ID']}' class='btn btn-info'>modify</a></td>";
				echo "</tr>";
				
			}
			echo "</table><br/>\n";

			


			$totalpages = ceil($entries/$entries_per_page);
			//Adds the paging buttons
			echo'<span class="badge bg-primary">';
			echo '<h3>Page:'.$page;
			echo '</h3><ul class="pagination">';
			if ($page != 1){
				//Adds previous and first buttons if the page is not the fist page.
				$pre = $page - 1;
				echo "<a href='viewstaff.php?order=".$_SESSION['order']."&&page=".'1'."' class='page-link'>First</a>";
				echo "<a href='viewstaff.php?order=".$_SESSION['order']."&&page=".$pre."' class='page-link'>Previous</a>";
			
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
				echo "<a href='viewstaff.php?order=".$_SESSION['order']."&&page=".$t."' class='page-link'>$t</a>";
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
				echo "<a href='viewstaff.php?order=".$_SESSION['order']."&&page=".$t."' class='page-link'>$t</a>";
			}

		}
			//adds next and last page buttons if the page is not the last page.
			if ($page != $totalpages){

				$nex = $page + 1;
			echo "<a href='viewstaff.php?order=".$_SESSION['order']."&&page=".$nex."' class='page-link disabled'>Next</a>";
			echo "<a href='viewstaff.php?order=".$_SESSION['order']."&&page=".$totalpages."' class='page-link disabled'>Last</a>";
			}

			echo '<div class="dropdown">
			<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">Skip To <span class="caret"></span></button>
			<ul class="dropdown-menu scrollable-menu" role="menu">';
			for($t=1;$t<=$totalpages;$t++)
			{
				echo"<li><a class='dropdown-item' href='viewstaff.php?order=".$_SESSION['order']."&&page=".$t."'>Page:$t</a></li>";
			}
			echo'</ul>
		  </div>';


			echo "<a href='/land.php' class='btn btn-info'>Back</a>
			</div></span>";

			
			//Releasing database resources
			$conn = null;
			$sql = null;
			include "../includes/scripts/footer.php";
			
echo'
            

    </form>
</form>
</div>';

	echo'
</body>
</html>
    ';
	?>
	