<?php
/*  Developer:   Justin Alho
 *  File Name:   approvetime.php
 *  Description: Allows coordinators to view list of staff with claimed hours, show hours scheduled/claimed
 *  Date Start:  04/03/2020
 *  Date End:    TBD
 */
 ?>
 <?php
echo'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	        <title>Approve Hours</title>
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
		
	</style>

</head>
<body>
';

			//Starting a session and initializing variables needed
			session_start(); 
			$userType = $_SESSION['userType'];
			
			//include links to css, javascript, etc.
			include "../includes/scripts/headLinks2.0.php";
			
			//include function to get number of hours
			include "../includes/functions/getHours.php";
		
			//level of authorization required to access page
			$authLevel = "C";
			
			//to verify the user 
			include "../includes/functions/verLogin.php";
			verLogin();
			
			//to verify the users type
			include "../includes/functions/valUserType.php";
			valUserType($authLevel);

            //connect to the database
			include "../dbseckey.php";
            $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);
			
			//retrieve information about claimed shifts from the database
			$sql = $conn->prepare("SELECT SHIFT_ID, SHIFT.STATUS_CODE, SHIFT.STAFF_ID, STAFF_FNAME, STAFF_LNAME, SHIFT_DATE, SCHEDULED_START, SCHEDULED_END, CLAIMED_START, CLAIMED_END
			FROM SHIFT
			LEFT JOIN SHIFT_STATUS
			ON SHIFT.STATUS_CODE = SHIFT_STATUS.STATUS_CODE
			LEFT JOIN STAFF
			ON SHIFT.STAFF_ID = STAFF.STAFF_ID
			WHERE SHIFT.STATUS_CODE = 'C'
			ORDER BY STAFF_ID");
				
			$sql->execute();			
			
			$row = $sql->fetchAll();
			
			//include navbar
            include "../includes/scripts/navBar.php";
		
						echo'<div class="conb">';
			echo'				<div class="row justify-content-sm-center">
									<div class="form-con">';
			
			echo "						<br />";
			
			//if there are no submitted hours to approve
			if (sizeof($row) == 0)
			{
				echo "					There are no submitted hours to show.<br /><br />";
				echo "					<a href='../land.php' class='btn btn-secondary'>Back to Home</a>";
				
				echo "				</div>
								</div>
							</div>";
				
				//include footer
				include "../includes/scripts/footer.php";
				die();
			}
			//if there are submitted hours to approve
			else
			{
				//set up table headers
				echo
				"<table border='1'>
					<tr>
						<th>Staff Name:</th>
						<th>Total Hours Scheduled/Claimed:</th>
						<th>Review Timesheet</th>
					</tr>
				";
				
				//initialize variables
				$i = 0;
				$st = 0;
				$ct = 0;
				
				//while there are still records to go through
				while ($i < sizeof($row))
				{
					//if this record is not the last record
					if (($i + 1) < sizeof($row))
					{
						//if this record is for the same staff member as the next record
						if ($row[$i]['STAFF_ID'] == $row[$i+1]['STAFF_ID'])
						{
							//add number of claimed and scheduled hours to claimed total and scheduled total
							$st += getHours($row[$i]['SCHEDULED_START'], $row[$i]['SCHEDULED_END']);
							$ct += getHours($row[$i]['CLAIMED_START'], $row[$i]['CLAIMED_END']);
						}
						//if this record is for a different staff member than the next record
						else
						{
							//add number of claimed and scheduled hours to claimed total and scheduled total
							$st += getHours($row[$i]['SCHEDULED_START'], $row[$i]['SCHEDULED_END']);
							$ct += getHours($row[$i]['CLAIMED_START'], $row[$i]['CLAIMED_END']);
							
							//if scheduled and claimed hours match, set background for record to green
							if ($st == $ct)
							{
								echo "<tr style='background-color: #AAFFAA'>";
							}
							//if scheduled and claimed hours are different, set background for record to red
							else
							{
								echo "<tr style='background-color: #FFAAAA'>";
							}
							
							//make row with staff name, number of hours scheduled/claimed, and option to review timesheet
							echo "<td>{$row[$i]['STAFF_LNAME']}, {$row[$i]['STAFF_FNAME']}</td>";
							echo "<td>$st/$ct</td>";

							$id = $row[$i]['STAFF_ID'];
                            $date = $row[$i]['SHIFT_DATE'];
							echo "<td><a href='revtime.php?id=$id&date=$date'>Review</a></td>";
							
							//reset scheduled and claimed totals
							$st = 0;
							$ct = 0;
							echo "</tr>";
						}
					}
					//if this record is the last record
					else
					{
						//add number of claimed and scheduled hours to claimed total and scheduled total
						$st += getHours($row[$i]['SCHEDULED_START'], $row[$i]['SCHEDULED_END']);
						$ct += getHours($row[$i]['CLAIMED_START'], $row[$i]['CLAIMED_END']);
						
						//if scheduled and claimed hours match, set background for record to green
						if ($st == $ct)
						{
							echo "<tr style='background-color: #AAFFAA'>";
						}
						
						//if scheduled and claimed hours are different, set background for record to red
						else
						{
							echo "<tr style='background-color: #FFAAAA'>";
						}
						
						//make row with staff name, number of hours scheduled/claimed, and option to review timesheet
						echo "<td>{$row[$i]['STAFF_LNAME']}, {$row[$i]['STAFF_FNAME']}</td>";
						echo "<td>$st/$ct</td>";

                        $id = $row[$i]['STAFF_ID'];
                        $date = $row[$i]['SHIFT_DATE'];
                        echo "<td><a href='revtime.php?id=$id&date=$date'>Review</a></td>";
						
						//reset scheduled and claimed totals
						$st = 0;
						$ct = 0;
						echo "</tr>";
					}
					//increment counter and move on to the next record
					$i++;
				}
				echo "</table><br />";
			}

            echo "<a href='/shifts/' class='btn btn-secondary'>Back</a><br /><br />";
			
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
			
			//include footer
			include "../includes/scripts/footer2.php";
			echo "</div>
			</div>
			</div>";
			
			//if there is a successful approval from revtime.php, display a success message
			if(isset($_REQUEST['s']))
				echo "<script>alert('Timesheet has been approved.')</script>";

	echo'
</body>
</html>
    ';
	