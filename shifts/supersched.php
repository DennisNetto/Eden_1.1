<?php
/*  Developer:   Justin Alho, Harley Lenton
 *  File Name:   supersched.php
 *  Description: Allows coordinators to view the schedule for a group home that they supervise
 *  Date Start:  15/03/2020
 *  Date End:    TBD
 */?>
<html lang="en">
    
    <head>
        <title>View Shifts</title>
		<?php 
			//start session, set userType variable
			session_start(); 
			$userType = $_SESSION['userType'];
			
			//include links to css, javascript, etc.
			include "../includes/scripts/headLinks2.0.php"; ?>
		
		<style>
			.calendar, td, tr
			{
				border: 1px solid black;
						
			}
			
			th
			{
				text-align: center;
			}
			
			td
			{
				vertical-align: top;
				text-align: left;
				word-break: break-word;
			}
			
			table
			{
				table-layout: fixed;
			}
			
			.calendar
			{
				width: 100%;
				height: 100%;
			}

			.header
			{
				
			}

			.day
			{
				
			}
			
			a
			{
				display: block;
			}
			
			html, body
			{
				height: 100%;
				width: 100%; 
			}
			
			.bodyDiv
			{
				height: 100%;
				width: 100%;
				text-align: center;
			}
			
			.childBodyDiv
			{
				height: 100%;
				width: 85%;
				display: inline-block;
			}
		</style>

    </head>
    
    <body>

        <?php
			//level of authorization to view the page
			$authLevel = "S";
			
			//to verify the user
			include "../includes/functions/verLogin.php";
			verLogin();
			
			//to verify the user's type
			include "../includes/functions/valUserType.php";
			valUserType($authLevel);
			
			//include calendar function
			include "../includes/functions/calendar.php";
			
			//if ID has been sent from supermod.php
			if(isset($_POST['id']))
			{
				//if the ID variable is blank, send the use back to supermod.php with an error message
				if($_POST['id'] == '')
					header("Location: supermod.php?b=1");
				//if the ID variable isn't blank, set the ID variable
				else
					$id = $_POST['id'];
			}
			//if an ID has not been sent, send the user back to supermod.php with an error message
			else
				header("Location: supermod.php?b=1");
			
			//include navbar
			include "../includes/scripts/navBar.php";
			
			//print divs
			print("
				<div class='bodyDiv'>
					<div class='childBodyDiv'>
			");

            //connect to the database
			include "../dbseckey.php";
            $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);
			
			$stm = $conn->prepare("SELECT SHIFT_DATE, SCHEDULED_START, SCHEDULED_END, SHIFT_ID
			FROM SHIFT
			WHERE DEP_CODE = ?
			
			");
			
			//Binding parameters
			$statusCode = "S";
			
			$exeParams = array($id);

            $stm->execute($exeParams);

            $dataArray = $stm->fetchAll(PDO::FETCH_ASSOC);

			//test
			//$conn->connection = null;
			
			//$array = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31);
			
			//$shiftID = $dataArray[][];

            //Date information
            $numODays = date('t');
            $year = date('Y');
            $month = date('m');

            //Test print values
			
           // print($numODays);
           // print($year);
            //print($month);
			
			//test
			//print("<div id='ele'></div>");
			
			//test
            //print_r($dataArray);
			
			//print($staffID);
			
			
			$dateComponents = getdate();

			$month = $dateComponents['mon'];
			if(!isset($_REQUEST['submit']))
			{
				$_SESSION['month'] = $month;
			}
			
			$year = $dateComponents['year'];
			
			if( (isset($_REQUEST['submit'])) && ($_REQUEST['nextMonth'] == 1) )
			{
				
				$month = $_SESSION['month'] + 1;
				$_SESSION['month'] = $_SESSION['month'] + 1;
			}
			else if( (isset($_REQUEST['submit'])) && ($_REQUEST['nextMonth'] == -1) )
			{
				$month = $_SESSION['month'] - 1;
				$_SESSION['month'] = $_SESSION['month'] - 1;
			}
			
			//This will print out the calendar
			echo build_calendar($month,$year);
			
			//close divs
			print("
					</div>
				</div><br />
			");
			
			//back button
			echo "<a href='supermod.php' class='btn btn-secondary'>Back</a>
			</div>";
			
			//include footer
			include "../includes/scripts/footer.php";
			
        ?>
        
			<script>

				//////////////////////Calendar population script///////////////////////////
				////////////////////////////////////////////////////////////////
				var index;
				
				//Passing data from PHP to JS
				var array = <?php echo json_encode($dataArray); ?>;
				var numODays = <?php echo $numODays; ?>;

				
				var year = <?php echo $year; ?>;
				var month = <?php echo $month; ?>;
				
				//giving the month back it's leading zero if it needs one because JS gets rid of it when passing from PHP
				if (month < 10)
				{
					month = "0" + month;
				}
				
				var date = year + "-" + month + "-";
				
				
				//This is the length of the fetchAll array 
				var lofa = <?php echo count($dataArray); ?>;
				var i;
				
				for(index = 1; index <= numODays; index++)
				{
					
					if(index < 10)
					{
						i = "0" + index;
						//test
						//document.getElementById("2020-03-01").innerHTML = "Index < 10";
					}
					else
					{
						i = index;
						//test
						//document.getElementById("2020-03-01").innerHTML = "Index !< 10";
					}
					
					//Getting the current day by id
					var parent = document.getElementById(date + i);
					var newChild;
					
					//This will iterate through all rows of the fetch array checking if the current index is scheduled for the current day
					for(var j = 0; j < lofa; j++)
					{
						if( (array[j]['SHIFT_DATE']) == (date + i) )
						{
							//test
							//document.getElementById(date + i).innerHTML = array[j]['shift_date'];

							//This will convert 24hr to 12hr time///////////////////////////////////////////////////////////////////
							var start = array[j]['SCHEDULED_START'];
							var end = array[j]['SCHEDULED_END'];
							
							var startArray = start.split(":");
							var endArray = end.split(":");
							
							var hour;
							var endHour;
	
							
							//Converting shift start time to 12hr format
							if( (startArray[0] < 12) && (startArray[0] != 0) )
							{
								//Getting rid of the leading zero of the first hour
								var startHour = startArray[0];
								startHour = parseInt(startHour, 10);
								start = startHour + ":" + startArray[1] + "AM";
							}
							else if(startArray[0] == 12)
							{
								start = startArray[0] + ":" + startArray[1] + "PM";
							}
							else if(startArray[0] > 12)
							{
								hour = startArray[0] - 12;
								start = hour + ":" + startArray[1] + "PM";
							}
							else if(startArray[0] == 0)
							{
								hour = 12;
								start = hour + ":" + startArray[1] + "AM";
							}
							
							//Converting shift end time to 12hr format
							if( (endArray[0] < 12)  && (endArray[0] != 0) )
							{
								//Removing leading zero
								var endHour = endArray[0];
								endHour = parseInt(endHour, 10);
								end = endHour + ":" + endArray[1] + "AM";
							}//Noon will be changed into 12PM by this
							else if(endArray[0] == 12)
							{
								end = endArray[0] + ":" + endArray[1] + "PM";
							}
							else if(endArray[0] > 12)
							{
								endHour = endArray[0] - 12;
								end = endHour + ":" + endArray[1] + "PM";
							}//Midnight 00 will be changed into 12AM by this
							else if(endArray[0] == 0)
							{
								endHour = 12;
								end = endHour + ":" + endArray[1] + "AM";
							}
							/////////////////////////////////////////////////////////////////////////
							
							let newChild = document.createElement('div');
							newChild.innerHTML = "<a href='supermodshift.php?id=" + array[j]['SHIFT_ID'] + "'>" + start + "-" + end + "</a>";
							parent.appendChild(newChild.firstChild);
							
						}
						else
						{
							//document.getElementById("2020-03-25").innerHTML = "shift date not matching";
						}
						
					}
					
				}
						
            
        </script>
		<?php		
			//releasing database resources
			if(isset($conn) )
			{
				$conn = null;
			}
		?>
    </body>
	
</html>