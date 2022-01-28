<?php
/*  Developer:   Justin Alho
 *  File Name:   getavail.php
 *  Description: given a date and time, searches the database for available staff and populates a dropdown
 *  Date Start:  04/04/2020
 *  Date End:    TBD
 */
 ?>

<html>
<head>
	<title>Get Available Workers</title>
</head>
<body>
<?php
    //connect to the database
    include "../dbseckey.php";
    $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);
	
	//set variables to sent values
	$date = $_REQUEST['date'];
	$datetime = strtotime($date);
	$day = date('D', $datetime);
	$start = $_REQUEST['start'];
	$end = $_REQUEST['end'];
	
	//based on the day, change the availability to search for
	switch($day)
	{
		case 'Sun':
			$dayAvail = 'SUN_AVAIL';
			break;
		case 'Mon':
			$dayAvail = 'MON_AVAIL';
			break;
		case 'Tue':
			$dayAvail = 'TUE_AVAIL';
			break;
		case 'Wed':
			$dayAvail = 'WED_AVAIL';
			break;
		case 'Thu':
			$dayAvail = 'THU_AVAIL';
			break;
		case 'Fri':
			$dayAvail = 'FRI_AVAIL';
			break;
		case 'Sat':
			$dayAvail = 'SAT_AVAIL';
			break;
		default:
			$dayAvail = '';
	}
	
	//if the dayAvail variable isn't blank
	if(!$dayAvail == '')
	{
		//retrieve staff records that have availability for the selected day
		$avsql = $conn->prepare("SELECT STAFF_ID, STAFF_FNAME, STAFF_LNAME, $dayAvail FROM STAFF
		WHERE $dayAvail != ''
		AND $dayAvail != ' - '
		AND STAFF_STATUS = 'A'");
		
		$avsql->execute();
		$avrow = $avsql->fetchAll();
		
		//if there are no staff records with that availability set
		if(sizeof($avrow) == 0)
			echo "<option value=''>There are no available staff</option>";
		
		//if there are staff records with that availability set
		else
		{
			//set the number of available staff to 0
			$availStaff = 0;
			
			//for each staff member with availability for the selected day
			foreach($avrow as $data)
			{
				//split availability into start and end times
				$availArr = explode(' - ', $data[$dayAvail]);
				$stAvail = $availArr[0];
				$endAvail = $availArr[1];
				//if the shift is within the staff member's availability
				if($stAvail <= $start && $endAvail >= $end)
				{
					$id = $data['STAFF_ID'];
					
					//check to see if available staff have other shifts booked at the same time
					$daysql = $conn->prepare("SELECT * FROM SHIFT
					WHERE STAFF_ID = '$id'
					AND SHIFT_DATE = '$date'
					AND ((SCHEDULED_START >= '$start' AND SCHEDULED_START <= '$end')					
					OR (SCHEDULED_END >= '$start' AND SCHEDULED_END <= '$end'))");
					$daysql->execute();
					$dayrow = $daysql->fetchAll();
					
					//if the staff has no shifts that will confilct with this shift
					if(sizeof($dayrow) == 0)
					{
						//echo out a dropdown item and increment the number of available staff
						echo "<option value='$id'>{$data['STAFF_LNAME']}, {$data['STAFF_FNAME']}</option>";
						$availStaff++;
					}
				}
			}
			
			//if there are no available staff, say so
			if($availStaff == 0)
				echo "<option value=''>There are no available staff</option>";
		}
	}
?>
</body>
</html>