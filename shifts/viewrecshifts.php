<?php
/*  Developer:   Justin Alho
 *  File Name:   viewrecshift.php
 *  Description: Allows coordinators to view recurring shifts and select to modify them
 *  Date Start:  27/02/2020
 *  Date End:    TBD
 */?>
 <?php
echo'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	        <title>View Recurring Shift Imformation</title>
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
		.calendar, td, tr
		{
			border: 1px solid black;		
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
<div class="container">
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
			
			//if there is an ID sent from viewrecshift.php
			if(isset($_POST['id']))
			{
				//if the ID is blank, send user back to viewrecshift with an error message
				if($_POST['id'] == '')
					header("Location: viewrecshift.php?b=1");
				//if the ID isn't blank, set the ID and type variables
				else
				{
					$id = $_POST['id'];
					$type = $_POST['type'];
				}
			}
			//if there is no ID sent, send the user back to viewrecshift with an error message
			else
				header("Location: viewrecshift.php?b=1");

            //connect to the database
			include "../dbseckey.php";
            $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);
			
			$stm = "SELECT REC_ID, DEP_NAME, REC_SHIFT.CLIENT_ID, CLIENT_FNAME, CLIENT_LNAME, REC_SHIFT.STAFF_ID, STAFF_FNAME, STAFF_LNAME, REC_DAY, REC_START, REC_END, REC_SUPER, REC_NOTES
			FROM REC_SHIFT
			LEFT JOIN DEPARTMENT
			ON REC_SHIFT.DEP_CODE = DEPARTMENT.DEP_CODE
			LEFT JOIN CLIENT
			ON REC_SHIFT.CLIENT_ID = CLIENT.CLIENT_ID
			LEFT JOIN STAFF
			ON REC_SHIFT.STAFF_ID = STAFF.STAFF_ID";
			
			//if the type is 's', select shifts with the same staff ID
			if($type == 's')
			{
				$idSelect = " WHERE REC_SHIFT.STAFF_ID = ?";
			}
			
			//if the type is 'c', select shifts with the same client ID
			else if($type == 'c')
			{
				$idSelect = " WHERE REC_SHIFT.CLIENT_ID = ?";
			}
			
			//if the type is 'd', select shifts with the same department code
			else if($type == 'd')
			{
				$idSelect = " WHERE REC_SHIFT.DEP_CODE = ?";
			}
			
			$orderBy = " ORDER BY CLIENT_FNAME";
			
			//sorting
			if( (isset($_REQUEST['sortBy']) ) && (!isset($_SESSION['sortBy']) ) )
			{
				switch($_REQUEST['sortBy'])
				{
					case 'cli':
						$_SESSION['sortBy'] = 'cli';
						$orderBy = " ORDER BY CLIENT_FNAME DESC";
						$stm = $stm . $orderBy;
					break;
					
					case 'staff':
						$_SESSION['sortBy'] = 'staff';
						$orderBy = " ORDER BY STAFF_FNAME DESC";
						$stm = $stm . $orderBy;
					break;
					
					case 'dpt':
						$_SESSION['sortBy'] = 'dpt';
						$orderBy = " ORDER BY DEP_NAME DESC";
						$stm = $stm . $orderBy;
					break;
					
					case 'date':
						$_SESSION['sortBy'] = 'day';
						$orderBy = " ORDER BY REC_DAY DESC";
						$stm = $stm . $orderBy;
					break;
					
					case 'stat':
						$_SESSION['sortBy'] = 'stat';
						$orderBy = " ORDER BY STATUS_NAME DESC";
						$stm = $stm . $orderBy;
					break;
					
					case 'schTime':
						$_SESSION['sortBy'] = 'schTime';
					break;
					
					case 'clmTime':
						$_SESSION['sortBy'] = 'clmTime';
					break;
					
					case 'appTime':
						$_SESSION['sortBy'] = 'appTime';
					break;
					
					case 'sup':
						$_SESSION['sortBy'] = 'sup';
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
						$orderBy = " ORDER BY CLIENT_FNAME ASC";
						$stm = $stm . $orderBy;
					break;
					
					case 'staff':
						unset($_SESSION['sortBy']);
						$orderBy = " ORDER BY STAFF_FNAME ASC";
						$stm = $stm . $orderBy;
					break;
					
					case 'dpt':
						unset($_SESSION['sortBy']);
						$orderBy = " ORDER BY DEP_NAME ASC";
						$stm = $stm . $orderBy;
					break;
					
					case 'day':
						unset($_SESSION['sortBy']);
						$orderBy = " ORDER BY REC_DAY ASC";
						$stm = $stm . $orderBy;
					break;
					
					case 'stat':
						unset($_SESSION['sortBy']);
						$orderBy = " ORDER BY STATUS_NAME ASC";
						$stm = $stm . $orderBy;
					break;
					
					case 'schTime':
						unset($_SESSION['sortBy']);
						break;
						
					case 'clmTime':
						unset($_SESSION['sortBy']);
					break;
					
					case 'appTime':
						unset($_SESSION['sortBy']);
					break;
					
					case 'sup':
						unset($_SESSION['sortBy']);
					break;
					
					default:
					
					break;
				}
			}
			else
			{
				$stm = $stm . $idSelect . $orderBy;
			}
			
			//retrieve recurring shift information from database
			$sql = $conn->prepare($stm);
			
			$exeparams = array($id);
			
			$sql->execute($exeparams);
			
			$row = $sql->fetchAll();
			
			//include navbar

			//set up table headers
			echo
			"
			<table border='1'>
				<form action='viewrecshifts.php' method='post' id='cliForm'>
					<input type='hidden' name='sortBy' value='cli'>
					<input type='hidden' name='id' value='$id'>
					<input type='hidden' name='type' value='$type'>
				</form>
				<form action='viewrecshifts.php' method='post' id='staffForm'>
					<input type='hidden' name='sortBy' value='staff'>
					<input type='hidden' name='id' value='$id'>
					<input type='hidden' name='type' value='$type'>
				</form>
				<form action='viewrecshifts.php' method='post' id='dptForm'>
					<input type='hidden' name='sortBy' value='dpt'>
					<input type='hidden' name='id' value='$id'>
					<input type='hidden' name='type' value='$type'>
				</form>
				<form action='viewrecshifts.php' method='post' id='dayForm'>
					<input type='hidden' name='sortBy' value='day'>
					<input type='hidden' name='id' value='$id'>
					<input type='hidden' name='type' value='$type'>
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
				echo "<td>ID({$data['CLIENT_ID']}) {$data['CLIENT_LNAME']}, {$data['CLIENT_FNAME']}</td>";
				echo "<td>ID({$data['STAFF_ID']}) {$data['STAFF_LNAME']}, {$data['STAFF_FNAME']}</td>";
				echo "<td>{$data['DEP_NAME']}</td>";
				echo "<td>{$data['REC_DAY']}</td>";
				echo "<td>{$data['REC_START']} - {$data['REC_END']}</td>";
				if($data['REC_SUPER'] == 1)
					echo "<td>Yes</td>";
				else
					echo "<td>No</td>";
				echo "<td style='width: 110%;><div class ='scro'><textarea class ='scro'>{$data['REC_NOTES']}</textarea></div></td>";
				echo "<td><a href='modrecshift.php?id={$data['REC_ID']}'class='btn btn-info'>modify</a></td>";
				echo "</tr>";
			}
				
			//end table
			echo "	</table><br />\n";
			
			//back button
			echo "	<a href='viewrecshift.php' class='btn btn-secondary'>Back</a><br /><br />
				</div>
			</div>";
			
			
echo'
            

    </form>
</form></div>

</div></div></div>';
	//include footer
	include "../includes/scripts/footer.php";

	echo'
</body>
</html>
    ';
	?>