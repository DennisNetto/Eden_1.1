<?php
/*  Developer:   Justin Alho
 *  File Name:   viewshift.php
 *  Description: Allows coordinators to select staff, clients, or departments to view shifts for
 *  Date Start:  14/03/2020
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
				<script>
				//change whether staff, client, or department is selected
				function setSelect(selection) {
					if (window.XMLHttpRequest)
					{
						xmlhttp = new XMLHttpRequest();
					}
					xmlhttp.onreadystatechange = function() {
						if (this.readyState == 4 && this.status == 200) {
							document.getElementById("selectBox").innerHTML = this.responseText;
						}
					};
					xmlhttp.open("GET","viewshift.php?change="+selection,true);
					xmlhttp.send();
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
					xmlhttp.open("GET","../includes/scripts/search3.php?c="+search,true);
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
					xmlhttp.open("GET","../includes/scripts/search3.php?s="+search,true);
					xmlhttp.send();
				}
			</script>

</head>
<body>
';
		
			#Starting a session and initilizing variables needed
			session_start(); 
			$userType = $_SESSION['userType'];
		
		
		 include "../includes/scripts/headLinks2.0.php";

	

		
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
			
			//if a different select item is selected
			if(isset($_REQUEST['change']))
			{
				$change = $_REQUEST['change'];
				
				//if staff is selected
				if($change == 's')
				{
					//retrieve list of staff from database
					$stasql = $conn->prepare("SELECT STAFF_ID, STAFF_FNAME, STAFF_LNAME FROM STAFF");
					$stasql->execute();
					$starow = $stasql->fetchAll();
					
					//field to search for staff
					echo "Search for staff:<br />
					<input class='form-fan' type='text' onkeyup='staSearch(this.value)'><br /><br />";
					
					//display selection of staff
					echo "<select class='fanc' name='id' id='staSel'>
						<option value=''>Select Staff</option>";
					foreach($starow as $data)
						echo "<option value='{$data['STAFF_ID']}'>{$data['STAFF_LNAME']}, {$data['STAFF_FNAME']}</option>";
						
					echo "</select><br /><br />";
				}
				//if client is selected
				else if($change == 'c')
				{
					//retrieve list of clients from database
					$clisql = $conn->prepare("SELECT CLIENT_ID, CLIENT_FNAME, CLIENT_LNAME FROM CLIENT");
					$clisql->execute();
					$clirow = $clisql->fetchAll();
										
					//field to search for clients
					echo "Search for clients:<br />
					<input class='form-fan' type='text' onkeyup='cliSearch(this.value)'><br /><br />";
					
					//display selection of clients
					echo "<select class='fanc' name='id' id='cliSel'>
						<option value=''>Select Client</option>";
					foreach($clirow as $data)
						echo "<option value='{$data['CLIENT_ID']}'>{$data['CLIENT_LNAME']}, {$data['CLIENT_FNAME']}</option>";
					
					echo "</select><br /><br />";
				}
				//if department is selected
				else if($change == 'd')
				{
					//retrieve list of departments from database
					$depsql = $conn->prepare("SELECT DEP_CODE, DEP_NAME FROM DEPARTMENT");
					$depsql->execute();
					$deprow = $depsql->fetchAll();
				
					//display selection of departments
					echo "<select class='fanc' name='id'>
						<option value=''>Select Department</option>";
					foreach($deprow as $data)
						echo "<option value='{$data['DEP_CODE']}'>{$data['DEP_NAME']}</option>";
					
					echo "</select><br /><br />";
				}
				//stop script here
				die();
			}
			
			//include navbar
			include "../includes/scripts/navBar.php";
						echo'<div class="container">';
			echo'<div class="row justify-content-sm-center">';
							
		

			
			echo "<br />";
			

			
			//display the form
			echo "<form class='form-con' action='viewschedshift.php' method='post'>
				";
				
				//if shift was updated successfully, display success message
			if(isset($_REQUEST['p']))
				echo "<div class='alert alert-success'>Shift updated successfully.</div>";
			
			//if nothing was selected, display error message
			if(isset($_REQUEST['b']))
				echo "<div class='alert alert-warning'><strong>Please make a selection.</strong></div>";
					echo'<h5>View shifts for:</h5>';
					echo"
			
					
					
					<!--print a select that changes whether the staff, client, or department selections are printed-->
					<select class='fanc' id='viewBy' onchange='setSelect(this.value)' name='type'>
						<option value='s'>Staff</option>
						<option value='c'>Client</option>
						<option value='d'>Department</option>
					</select><br /><br /><br />\n
					
						<!--print staff selection by default-->
						<div id='selectBox'>";
							$stasql = $conn->prepare("SELECT STAFF_ID, STAFF_FNAME, STAFF_LNAME FROM STAFF
							WHERE STAFF_STATUS = 'A'
							AND (TYPE_CODE = 'S'
							OR TYPE_CODE = 'W')");
							$stasql->execute();
							$starow = $stasql->fetchAll();
					
							//field to search for staff
							echo "Search for staff:<br />
							<input class='form-fan' type='text' onkeyup='staSearch(this.value)'><br /><br />";
					
							echo "<select select class='fanc' name='id' id='staSel'>
								<option value=''>Select Staff</option>";
							foreach($starow as $data)
								echo "<option value='{$data['STAFF_ID']}'>{$data['STAFF_LNAME']}, {$data['STAFF_FNAME']}</option>";
						
							echo "</select><br /><br /> 
								</div>
						
						<input  type='submit' name='view' value='View Shifts' class='btn btn-primary' /><br /><br />
											<!--include option to view all shifts-->
					<a href='viewallshifts.php' class='btn btn-info'>View all shifts</a><br /><br />
					
				<!--back button-->
				<a href='/land.php' class='btn btn-danger'>Back</a>
					</form><br />

			";
						
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
			
			//include footer
echo'
            

    </form>
</form>
</div>';
	include "../includes/scripts/footer2.php";
	echo'
</body>
</html>
    ';
	?>