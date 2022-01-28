<?php
/*  Developer:   Justin Alho
 *  File Name:   viewgh.php
 *  Description: Allows coordinators to view existing group homes and select to modify them
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
	        <title>View Group Home Information</title>
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
			$start_from = ($page-1)*6;
			$entries_per_page = 6;
			$entries = 0;
					
			//retrieve group home information from the database
			$stm1 = "SELECT GH_ID FROM GROUP_HOME";

			$stm = "SELECT GH_ID, GH_NAME, GH_STATUS, STAFF_FNAME, STAFF_LNAME, GH_PHONE, GH_ADDRESS, GH_CITY
			FROM GROUP_HOME
			LEFT JOIN STAFF
			ON GROUP_HOME.STAFF_ID = STAFF.STAFF_ID";

			$limit_eentries = " LIMIT $start_from,$entries_per_page";

			$stm = $stm . $limit_eentries;

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
			
			
			//if there is a successful update from modgh.php, display a success message
			if(isset($_REQUEST['p']))
				echo "<div class='alert alert-success'>Group home has been updated successfully.</div>";
			
			//set up table headers
			echo
			"<table border='1'>
				<tr>
					<th>Group Home</th>
					<th>Status</th>
					<th>Supervisor</th>
					<th>Phone Number</th>
					<th>Address</th>
					<th>City</th>
					<th></th>
				</tr>
			";
			
			//fill table with records from database
			foreach ($row as $data)
			{	
				echo "<tr>";
				echo "<td>{$data['GH_NAME']}</td>";
				echo "<td>{$data['GH_STATUS']}</td>";
				echo "<td>{$data['STAFF_FNAME']} {$data['STAFF_LNAME']}</td>";
				echo "<td>{$data['GH_PHONE']}</td>";
				echo "<td>{$data['GH_ADDRESS']}</td>";
				echo "<td>{$data['GH_CITY']}</td>";
				echo "<td><a href='modgh.php?id={$data['GH_ID']}'class='btn btn-info'>modify</a></td>";
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
				echo "<a href='viewgh.php?page=".'1'."' class='page-link'>First</a>";
				echo "<a href='viewgh.php?page=".$pre."' class='page-link'>Previous</a>";
			
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
				echo "<a href='viewgh.php?page=".$t."' class='page-link'>$t</a>";
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
				echo "<a href='viewgh.php?page=".$t."' class='page-link'>$t</a>";
			}

		}
			//adds next and last page buttons if the page is not the last page.
			if ($page != $totalpages){

				$nex = $page + 1;
			echo "<a href='viewgh.php?page=".$nex."' class='page-link disabled'>Next</a>";
			echo "<a href='viewgh.php?page=".$totalpages."' class='page-link disabled'>Last</a>";
			}

		  echo '<div class="dropdown">
		  <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">Skip To <span class="caret"></span></button>
		  <ul class="dropdown-menu scrollable-menu" role="menu">';
		  for($t=1;$t<=$totalpages;$t++)
		  {
			  echo"<li><a class='dropdown-item' href='viewgh.php?page=".$t."'>Page:$t</a></li>";
		  }
		  echo'</ul>
		  </div>';


			echo "<a href='/land.php' class='btn btn-info'>Back</a>
			</div></span>";
						
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
			
			//include footer\
		
echo'
            

    
			</form>
		</div>';
		
echo'</div>
</div>';
include "../includes/scripts/footer.php";

	echo'
</body>
</html>
    ';
	?>