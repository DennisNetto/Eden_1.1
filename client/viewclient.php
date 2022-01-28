<?php
echo'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Client Information</title>
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
					function cliSearch(search) {
						if (window.XMLHttpRequest)
						{
							xmlhttp = new XMLHttpRequest();
						}
						xmlhttp.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								document.getElementById("cliSel").innerHTML = this.responseText;
							}
						};
						xmlhttp.open("GET","../includes/scripts/search2.php?c="+search,true);
						xmlhttp.send();
					}

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
echo'
<div class="conb">
	<div class="row justify-content-center">

    
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
			
			//varables
			$staff = 1000000;
			$low = 0;
			$name = '';
			if(isset($_POST['gen']))
			{
				$staff = $_POST['client'];
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

			$stm1 = "SELECT CLIENT_ID FROM CLIENT";
					
			$stm = "SELECT CLIENT_ID, C_S_STATUS_NAME, GH_NAME, CLIENT_FNAME, CLIENT_LNAME, CLIENT_PHONE, CLIENT_ADDRESS, CLIENT_CITY, CLIENT_MAX_HOURS, CLIENT_KM, CLIENT_NOTES
			FROM CLIENT
			LEFT JOIN C_S_STATUS
			ON CLIENT.CLIENT_STATUS = C_S_STATUS.C_S_STATUS_CODE
			LEFT JOIN GROUP_HOME
			ON CLIENT.GH_ID = GROUP_HOME.GH_ID
			";

			if ($staff != 1000000){
			$OD = "WHERE CLIENT_ID = $staff";
			$stm = $stm . $OD;
			}

			if ($_SESSION['order'] == null){
				$orderBy = " ORDER BY CLIENT_LNAME LIMIT $start_from,$entries_per_page";
				}
				$limit_eentries = " LIMIT $start_from,$entries_per_page";
			
			//sorting/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			if( (isset($_REQUEST['sortBy']) ) && (!isset($_SESSION['sortBy']) ) )
			{
				switch($_REQUEST['sortBy'])
				{
					case 'client':
						$_SESSION['sortBy'] = 'client';
						$_SESSION['order'] = 1;
						break;

					case 'status':
						$_SESSION['sortBy'] = 'status';
						$_SESSION['order'] = 2;
						break;

					case 'gHome':
						$_SESSION['sortBy'] = 'type';
						$_SESSION['order'] = 3;
						break;
						
					case 'city':
						$_SESSION['sortBy'] = 'city';
						$_SESSION['order'] = 4;
						
						break;
						
					case 'hours':
						$_SESSION['sortBy'] = 'child';
						$_SESSION['order'] = 5;
						
						break;
						
					case 'dist':
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
					case 'client':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -1;
						break;

					case 'status':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -2;
						break;

					case 'gHome':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -3;
						break;
						
					case 'city':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -4;
					
						break;
						
					case 'hours':
						unset($_SESSION['sortBy']);
						$_SESSION['order'] = -5;
					
						break;
						
					case 'dist':
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
	
					$orderBy = " ORDER BY CLIENT_LNAME DESC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == 2){
	
					$orderBy = " ORDER BY C_S_STATUS_NAME DESC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == 3){
	
					$orderBy = " ORDER BY GH_NAME DESC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == 4){
	
					$orderBy = " ORDER BY CLIENT_CITY DESC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == 5){
	
					$orderBy = " ORDER BY CLIENT_MAX_HOURS DESC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == 6){
	
					$orderBy = " ORDER BY CLIENT_KM DESC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == 7){
	
					$orderBy = " ORDER BY CAN_DRIVE DESC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == -1){
	
					$orderBy = " ORDER BY CLIENT_LNAME ASC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == -2){
	
					$orderBy = " ORDER BY C_S_STATUS_NAME ASC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == -3){
	
					$orderBy = " ORDER BY GH_NAME ASC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == -4){
	
					$orderBy = " ORDER BY CLIENT_CITY ASC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == -5){
	
					$orderBy = " ORDER BY CLIENT_MAX_HOURS ASC";
					$stm = $stm . $orderBy . $limit_eentries;
	
				}
	
				if ($_SESSION['order'] == -6){
	
					$orderBy = " ORDER BY CLIENT_KM ASC";
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

			$conn = new PDO("mysql:host=localhost; dbname=oldcount_edenbridge", $username, $password);
				
			$sql = $conn->prepare($stm);
			
			$sql->execute();
			
			$row = $sql->fetchAll();
			
		echo'
		
		<div class="form-con">';
			
			if(isset($_REQUEST['s']))
				echo "<div class='alert alert-success'>Client updated successfully.</div>";
			//set up table headers
			printf( "
			<div class='alert alert-light'><form method='post' action='viewclient.php'>
				Search for Clients:
						<input class='form-fan' type='text' name='' value='' onkeyup='cliSearch(this.value)'>\n

					<!--display selection of clients members-->
					Client results:
						<select class='fanc' name='client' id='cliSel'>
							<option value='1000000'>Search All:</option>");
							foreach($clirow as $data)
						echo "<option value='{$data['CLIENT_ID']}'>CID({$data['CLIENT_ID']}) {$data['CLIENT_LNAME']}, {$data['CLIENT_FNAME']}</option><option value='1000000'>Search All:</option>";
			printf("
						</select>$staerr\n
				<input type='submit' name='gen' class='btn btn-primary btn-sm' value='Find Client'>
			</form></div>\n");
			echo
			"<table border='1'>
				<form action='viewclient.php' method='get' id='clientForm'>
					<input type='hidden' name='sortBy' value='client'></input>
				</form>
				<form action='viewclient.php' method='get' id='statForm'>
					<input type='hidden' name='sortBy' value='status'></input>
				</form>
				<form action='viewclient.php' method='get' id='gHomeForm'>
					<input type='hidden' name='sortBy' value='gHome'></input>
				</form>
				<form action='viewclient.php' method='get' id='cityForm'>
					<input type='hidden' name='sortBy' value='city'></input>
				</form>
				<form action='viewclient.php' method='get' id='hourForm'>
					<input type='hidden' name='sortBy' value='hours'></input>
				</form>
				<form action='viewclient.php' method='get' id='distForm'>
					<input type='hidden' name='sortBy' value='dist'></input>
				</form>
				
				<tr>
					<th>
						<button style='background: none;
										border: none;
										padding: 0;
										color: white;
										font-weight: bold;
										text-decoration: none;
										cursor: pointer;' type='submit' form='clientForm' value='client'>Client
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
										cursor: pointer;' type='submit' form='gHomeForm' value='gHome'>Group Home
						</button>
					</th>
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
										cursor: pointer;' type='submit' form='hourForm' value='hours'>Max Hours per Month
						</button>
					</th>
					<th>
						<button style='background: none;
										border: none;
										padding: 0;
										color: white;
										font-weight: bold;
										text-decoration: none;
										cursor: pointer;' type='submit' form='distForm' value='dist'>Distance (KM)
						</button>
					</th>
					<th>Notes</th>
					<th></th>
				</tr>
			";
			
			foreach ($row as $data)
			{	
				echo "<tr>";
				echo "<td>CID({$data['CLIENT_ID']}) {$data['CLIENT_LNAME']}, {$data['CLIENT_FNAME']}</td>";
				echo "<td>{$data['C_S_STATUS_NAME']}</td>";
				echo "<td>{$data['GH_NAME']}</td>";
				echo "<td>{$data['CLIENT_PHONE']}</td>";
				echo "<td>{$data['CLIENT_ADDRESS']}</td>";
				echo "<td>{$data['CLIENT_CITY']}</td>";
				echo "<td>{$data['CLIENT_MAX_HOURS']}</td>";
				echo "<td>{$data['CLIENT_KM']}</td>";
				echo "<td style='width: 17%;><div class ='scro'><textarea class ='scro'>{$data['CLIENT_NOTES']}</textarea></div></td>";
				echo "<td><a href='modclient.php?id={$data['CLIENT_ID']}'class='btn btn-info'>modify</a></td>";
				echo "</tr>";
			}
				
			echo "</table><br />\n";

			$totalpages = ceil($entries/$entries_per_page);
			//Adds the paging buttons
			echo'<span class="badge bg-primary">';
			echo '<h3>Page:'.$page;
			echo '</h3><ul class="pagination">';
			if ($page != 1){
				//Adds previous and first buttons if the page is not the fist page.
				$pre = $page - 1;
				echo "<a href='viewclient.php?order=".$_SESSION['order']."&&page=".'1'."' class='page-link'>First</a>";
				echo "<a href='viewclient.php?order=".$_SESSION['order']."&&page=".$pre."' class='page-link'>Previous</a>";
			
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
				echo "<a href='viewclient.php?order=".$_SESSION['order']."&&page=".$t."' class='page-link'>$t</a>";
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
				echo "<a href='viewclient.php?order=".$_SESSION['order']."&&page=".$t."' class='page-link'>$t</a>";
			}

		}
			//adds next and last page buttons if the page is not the last page.
			if ($page != $totalpages){

				$nex = $page + 1;
			echo "<a href='viewclient.php?order=".$_SESSION['order']."&&page=".$nex."' class='page-link disabled'>Next</a>";
			echo "<a href='viewclient.php?order=".$_SESSION['order']."&&page=".$totalpages."' class='page-link disabled'>Last</a>";
			}

			echo '<div class="dropdown">
			<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">Skip To <span class="caret"></span></button>
			<ul class="dropdown-menu scrollable-menu" role="menu">';
			for($t=1;$t<=$totalpages;$t++)
			{
				echo"<li><a class='dropdown-item' href='viewclient.php?order=".$_SESSION['order']."&&page=".$t."'>Page:$t</a></li>";
			}
			echo'</ul>
		  </div>';
			
			echo "<a href='/land.php' class='btn btn-secondary'>Back</a><br /><br />";

			
			echo'</div>';
		
			
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