<?php

			#Starting a session and initilizing variables needed
	
			session_start();
			$userType = $_SESSION['userType'];
					include "../includes/scripts/headLinks2.0.php"; 
					include "../includes/functions/isSpecial.php";
					include "../includes/functions/isComplex.php";
					
        

			
			//to verify the user 
			include "../includes/functions/verLogin.php";
			verLogin();

			echo'
<html>
<head>
<title>Help</title>
<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="../css/table.css" rel="stylesheet" type="text/css">
	<link href="css/footer.css" rel="stylesheet" type="text/css">
</head>
<body>';


						include "../includes/scripts/navBar.php";



			switch($userType)
                {
					//Worker's videos
                    case "W":
					
						echo'
						<div>
						<h3>Change Password</h3>
						<div class="embed-responsive">
						</div>
						<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/changepwworker.mp4" type="video/mp4"> </video>
						<br></br>
						';

						echo'
<div>
<h3>Submit Hours</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/submithours.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>View Personal Shifts</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/viewshiftworker.mp4" type="video/mp4"> </video>
<br></br>
';
						
						break;

					//Bookeeper's vedeo
                    case "B":

                       

						echo'
<div>
<h3>Add Clients</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/addclient.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Modify Clients</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/modclient.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Add Worker</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/addworker.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Modify Worker</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/editworker.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Add Group Home</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/addgh.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Modify Group Home</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/editgrouphome.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Add Department</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/adddepartmenmmt.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Modify Department</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/editdepartment.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Schedule Shift</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/makeshift.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Modify Shift</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/editshift.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Schedule Recurring Shift For Single Worker</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/addclient.mp4" type="video/mp4"> </video>
<br></br>
';


echo'
<div>
<h3>Manage Recurring Shifts</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/editrecshift.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Approve Hours</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/aprovehours.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Recurring Shifts By Department</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/addclient.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>View Reports</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/viewreports.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Change Password</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/changePWbook.mp4" type="video/mp4"> </video>
<br></br>
';



                        

                        break;

					//Coordinator's videos
                    case "C":
                          /// min-height: 80%; width: 50%;
                    

						echo'
						<div>
						<h3>Add Clients</h3>
						<div class="embed-responsive">
						</div>
						<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/addclient.mp4" type="video/mp4"> </video>
						<br></br>
						';
						
						echo'
						<div>
						<h3>Modify Clients</h3>
						<div class="embed-responsive">
						</div>
						<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/modclient.mp4" type="video/mp4"> </video>
						<br></br>
						';
						
						echo'
						<div>
						<h3>Add Worker</h3>
						<div class="embed-responsive">
						</div>
						<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/addworker.mp4" type="video/mp4"> </video>
						<br></br>
						';
						
						echo'
						<div>
						<h3>Modify Worker</h3>
						<div class="embed-responsive">
						</div>
						<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/editworker.mp4" type="video/mp4"> </video>
						<br></br>
						';
						
						echo'
						<div>
						<h3>Add Group Home</h3>
						<div class="embed-responsive">
						</div>
						<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/addgh.mp4" type="video/mp4"> </video>
						<br></br>
						';
						
						echo'
						<div>
						<h3>Modify Group Home</h3>
						<div class="embed-responsive">
						</div>
						<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/editgrouphome.mp4" type="video/mp4"> </video>
						<br></br>
						';
						
						echo'
						<div>
						<h3>Add Department</h3>
						<div class="embed-responsive">
						</div>
						<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/adddepartmenmmt.mp4" type="video/mp4"> </video>
						<br></br>
						';
						
						echo'
						<div>
						<h3>Modify Department</h3>
						<div class="embed-responsive">
						</div>
						<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/editdepartment.mp4" type="video/mp4"> </video>
						<br></br>
						';
						
						echo'
						<div>
						<h3>Schedule Shift</h3>
						<div class="embed-responsive">
						</div>
						<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/makeshift.mp4" type="video/mp4"> </video>
						<br></br>
						';
						
						echo'
						<div>
						<h3>Modify Shift</h3>
						<div class="embed-responsive">
						</div>
						<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/editshift.mp4" type="video/mp4"> </video>
						<br></br>
						';

						echo'
						<div>
						<h3>Schedule Recurring Shift For Single Worker</h3>
						<div class="embed-responsive">
						</div>
						<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/addclient.mp4" type="video/mp4"> </video>
						<br></br>
						';
										  
						echo'
<div>
<h3>Manage Recurring Shifts</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/editrecshift.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Approve Hours</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/aprovehours.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Recurring Shifts By Department</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/madrecshift.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Change Password</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/changePWbook.mp4" type="video/mp4"> </video>
<br></br>
';



                        break;

					//supervisor's vedios
                    case "S":

						echo'
						<div>
						<h3>Change Password</h3>
						<div class="embed-responsive">
						</div>
						<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/changepwworker.mp4" type="video/mp4"> </video>
						<br></br>
						';

						echo'
<div>
<h3>Submit Hours</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/submithours.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>View Personal Shifts</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/viewshiftworker.mp4" type="video/mp4"> </video>
<br></br>
';

echo'
<div>
<h3>Supervisor View GroupHome Shifts</h3>
<div class="embed-responsive">
</div>
<video width="320" height="240" controls> <source src="../includes/images/instruktvideo/modshiftsuper.mp4" type="video/mp4"> </video>
<br></br>
';


                        break;

					//if they not cool
                    default:
					
						header("Location: index.php");
						
                        break;
                }




echo"</body></html>"


?>