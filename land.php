<?php
/*  Developer:   Harley Lenton, Justin Alho
 *  File Name:   land.php
 *  Description: Acts as a homepage for users with links to other pages
 *  Date Start:  03/03/2020
 *  Date End:    TBD
 */
 session_start(); ?>
<html>
    
    <head>

        <title>Home Page</title>
		
		<?php
		//include links to css, javascript, etc.
		include "./includes/scripts/headLinks2.0.php"; ?>
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

        <?php			
			//Setting the userType var
			$userType = $_SESSION['userType'];
			
			//Include navbar
			include "./includes/scripts/navBar.php";
			
			echo "<br />";
			
			//to verify the user 
			include "./includes/functions/verLogin.php";
			verLogin();
			
            if(isset($_SESSION['userType']))
            {

                //set userType variable to the user's type
                $userType = $_SESSION['userType'];

				//different home page based on user type
                switch($userType)
                {
					//for the workers, they only get the option of either checking their schedule or submitting their hours
                    case "W":
					
                        print('

  <div class="conb">
							
						  <div class="row row-cols-1 row-cols-md-3">

  <div class="col mb-4">
<div class="card text-white bg-dark ">
     
      <div class="card-body">
        <h5 class="card-title">Schedule / Timesheet Submission</h5>
        <p class="card-text"></p>
      </div>
	  <div class="card-deck">
	  	  <div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">View Schedule</p>
    <a href="/shifts/viewSched.php" class="btn btn-primary">Check Schedule</a>
  </div>
</div>
<div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Submit Your Timesheet</p>
   <a href="/shifts/timesheet.php" class="btn btn-primary">Submit Timesheet</a>
  </div>
</div>
    </div>
  </div>
  </div>
<div class="col mb-4">
<div class="card text-white bg-dark ">
     
      <div class="card-body">
        <h5 class="card-title">Manage Account</h5>
       
      </div>
	  	  <div class="card-deck">
	  	  <div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Change Your Account Password</p>
   <a href="/staff/changepass.php" class="btn btn-primary">Change Password</a>
  </div>
</div>
    </div>
  </div>
  </div>
    </div>
  </div>
                        ');
						
						break;

					//currently, bookkeepers can only view reports.
					//they should be able to do everything a coordinator can as well
                    case "B":

                        print('
						<div class="conb">
							
						  <div class="row row-cols-1 row-cols-md-3">

  <div class="col mb-4">
<div class="card text-white bg-dark ">
     
      <div class="card-body">
        <h5 class="card-title">Group Homes Creation and Modification</h5>
        
      </div>
	  <div class="card-deck">
	  	  <div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Add A New Group Home</p>
    <a href="/grouphome/addgh.php" class="btn btn-primary">Add Group Home</a>
  </div>
</div>
<div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Modify A Existing Group Home</p>
    <a href="/grouphome/viewgh.php" class="btn btn-primary">Manage Group Homes</a>
  </div>
</div>
    </div>
  </div>
  </div>
  <div class="col mb-4">
    <div class="card text-white bg-dark ">
      <div class="card-body">
        <h5 class="card-title">Staff Creation and Modification</h5>
        
      </div>
	  <div class="card-deck">
	  	  <div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Add A New Staff Member</p>
   <a href="/staff/addstaff.php" class="btn btn-primary">Add New Staff Member</a>
  </div>
</div>
<div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Modify An Existing Staff Member</p>
    <a href="/staff/viewstaff.php" class="btn btn-primary">Manage Staff</a>
  </div>
</div>
    </div>
	</div>
  </div>
 <div class="col mb-4">
<div class="card text-white bg-dark ">
     
      <div class="card-body">
        <h5 class="card-title">Client Creation and Modification</h5>
      
      </div>
	  <div class="card-deck">
	  	  <div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Add A New Client</p>
       <a href="/client/addclient.php" class="btn btn-primary">Add Client</a>
  </div>
</div>
<div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Modify An Existing Client</p>
    <a href="/client/viewclient.php" class="btn btn-primary">Manage Clients</a>
  </div>
</div>
    </div>
	</div>
  </div>

   <div class="col mb-4">
    <div class="card text-white bg-dark ">
      <div class="card-body">
        <h6 class="card-title">Options for shift Creation and Modification and authorization of workers hours</h6>
        <div class="col mb-4">
        <div class="col mb-4">
      </div>
	  <div class="card-deck">
<div class="row row-cols-1 row-cols">
  <div class="col mb">
    <div class="card">
      <div class="card-body bg-light text-dark">
        <p class="card-text">Schedule A Shift</p>
		<a href="/shifts/schedshift.php" class="btn btn-primary">Schedule Shift</a>
      </div>
    </div>
  </div>
  <div class="col mb">
    <div class="card">
      <div class="card-body bg-light text-dark">
        <p style = "font-size:14px" class="card-text">Schedule Department Recurring Shift</p>
		<a href="/shifts/schedrecshift.php" class="btn btn-primary">Schedule Recurring Shifts</a>
      </div>
    </div>
  </div>

  <div class="col mb">
    <div class="card">
      <div class="card-body bg-light text-dark">
       <p class="card-text">Manage Existing Shifts</p>
       <a href="/shifts/viewshift.php" class="btn btn-primary">Manage Shifts</a>
		<a href="/shifts/viewrecshift.php" class="btn btn-primary">Manage Recurring Shifts</a>
      </div>
    </div>
    </div>
<div class="col mb-4">
    <div class="card">
     <div class="card-body bg-light text-dark">
        <p class="card-text">Approve a Workers Hours</p>
		<a href="/shifts/approvetime.php" class="btn btn-primary">Approve Hours</a><br />
      </div>
    </div>
  </div>
  </div>
    </div>
	</div>
  </div>
  </div>
  </div>
<div class="col mb-4">
<div class="card text-white bg-dark ">
      <div class="card-body">
        <h5 class="card-title">Department Creation and Modification</h5>
        
      </div>
	  	  <div class="card-deck">
	  	  <div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Add A New Department</p>
    <a href="/department/adddep.php" class="btn btn-primary">Add Department</a><br />
  </div>
</div>
<div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Edit An Existing Department.</p>
    <a href="/department/viewdep.php" class="btn btn-primary">Manage Departments</a>
  </div>
</div>
    </div>
  </div>
  </div>
<div class="col mb-4">
<div class="card text-white bg-dark ">
      <div class="card-body">
         <h5 class="card-title">Reports / Manage Account</h5>
        
      </div>
	  	  <div class="card-deck">
	  	  <div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
   <p class="card-text">View Reports</p>
   <a href="/etc/reports.php" class="btn btn-primary">View Reports</a>
  </div>
</div>
<div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Change Your Account Password</p>
   <a href="/staff/changepass.php" class="btn btn-primary">Change Password</a>
  </div>
</div>
    </div>
  </div>
  </div>
    </div>
  </div>
                        ');

                        break;

					//coordinators are able to access almost every page
                    case "C":
                          /// min-height: 80%; width: 50%;
                        print('
						<div class="conb">
							
						  <div class="row row-cols-1 row-cols-md-3">

  <div class="col mb-4">
<div class="card text-white bg-dark ">
     
      <div class="card-body">
        <h5 class="card-title">Group Homes Creation and Modification</h5>
        <p class="card-text"></p>
      </div>
	  <div class="card-deck">
	  	  <div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Add A New Group Home</p>
    <a href="/grouphome/addgh.php" class="btn btn-primary">Add Group Home</a>
  </div>
</div>
<div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Modify A Existing Group Home</p>
    <a href="/grouphome/viewgh.php" class="btn btn-primary">Manage Group Homes</a>
  </div>
</div>
    </div>
  </div>
  </div>
  <div class="col mb-4">
    <div class="card text-white bg-dark ">
      <div class="card-body">
        <h5 class="card-title">Staff Creation and Modification</h5>
        <p class="card-text"></p>
      </div>
	  <div class="card-deck">
	  	  <div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Add A New Staff Member</p>
   <a href="/staff/addstaff.php" class="btn btn-primary">Add New Staff Member</a>
  </div>
</div>
<div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Modify An Existing Staff Member</p>
    <a href="/staff/viewstaff.php" class="btn btn-primary">Manage Staff</a>
  </div>
</div>
    </div>
	</div>
  </div>
 <div class="col mb-4">
<div class="card text-white bg-dark ">
     
      <div class="card-body">
        <h5 class="card-title">Client Creation and Modification</h5>
        <p class="card-text"></p>
      </div>
	  <div class="card-deck">
	  	  <div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Add A New Client</p>
       <a href="/client/addclient.php" class="btn btn-primary">Add Client</a>
  </div>
</div>
<div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Modify An Existing Client</p>
    <a href="/client/viewclient.php" class="btn btn-primary">Manage Clients</a>
  </div>
</div>
    </div>
	</div>
  </div>

  <div class="col mb-4">
  <div class="card text-white bg-dark ">
    <div class="card-body">
      <h6 class="card-title">Options for shift Creation and Modification and authorization of workers hours</h6>
      <div class="col mb-4">
      <div class="col mb-4">
    </div>
  <div class="card-deck">
<div class="row row-cols-1 row-cols">
<div class="col mb">
  <div class="card">
    <div class="card-body bg-light text-dark">
      <p class="card-text">Schedule A Shift</p>
  <a href="/shifts/schedshift.php" class="btn btn-primary">Schedule Shift</a>
    </div>
  </div>
</div>
<div class="col mb">
  <div class="card">
    <div class="card-body bg-light text-dark">
      <p style = "font-size:14px" class="card-text">Schedule Department Recurring Shift</p>
  <a href="/shifts/schedrecshift.php" class="btn btn-primary">Schedule Recurring Shifts</a>
    </div>
  </div>
</div>

<div class="col mb">
  <div class="card">
    <div class="card-body bg-light text-dark">
     <p class="card-text">Manage Existing Shifts</p>
     <a href="/shifts/viewshift.php" class="btn btn-primary">Manage Shifts</a>
  <a href="/shifts/viewrecshift.php" class="btn btn-primary">Manage Recurring Shifts</a>
    </div>
  </div>
  </div>
<div class="col mb-4">
  <div class="card">
   <div class="card-body bg-light text-dark">
      <p class="card-text">Approve a Workers Hours</p>
  <a href="/shifts/approvetime.php" class="btn btn-primary">Approve Hours</a><br />
    </div>
  </div>
</div>
</div>
  </div>
</div>
</div>
</div>
</div>
<div class="col mb-4">
<div class="card text-white bg-dark ">
      <div class="card-body">
        <h5 class="card-title">Department Creation and Modification</h5>
        <p class="card-text"></p>
      </div>
	  	  <div class="card-deck">
	  	  <div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Add A New Department</p>
    <a href="/department/adddep.php" class="btn btn-primary">Add Department</a><br />
  </div>
</div>
<div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Edit An Existing Department.</p>
    <a href="/department/viewdep.php" class="btn btn-primary">Manage Departments</a>
  </div>
</div>
    </div>
  </div>
  </div>
<div class="col mb-4">
<div class="card text-white bg-dark ">
     
      <div class="card-body">
        <h5 class="card-title">Manage Account</h5>
        <p class="card-text"></p>
      </div>
	  	  <div class="card-deck">
	  	  <div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Change Your Account Password</p>
   <a href="/staff/changepass.php" class="btn btn-primary">Change Password</a>
  </div>
</div>
    </div>
  </div>
  </div>
    </div>
  </div>
						

                        ');

                        break;

					//supervisors are able to change the workers for shifts in group homes that they supervise
                    case "S":

                        print('
  <div class="conb">
							
						  <div class="row row-cols-1 row-cols-md-3">
<div class="col mb-4">
<div class="card text-white bg-dark ">
      <div class="card-body">
        <h5 class="card-title">Schedule / Time Sheet Submission</h5>
       
      </div>
	  	  <div class="card-deck">
	  	  <div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Check Your Schedule</p>
    <a href="/shifts/viewSched.php" class="btn btn-primary">Check Schedule</a><br />
  </div>
</div>
<div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Submit Your Timesheet.</p>
    <a href="/shifts/timesheet.php" class="btn btn-primary">Submit Timesheet</a><br /><br />
  </div>
</div>
    </div>
  </div>
  </div>
<div class="col mb-4">
<div class="card text-white bg-dark ">
      <div class="card-body">
         <h5 class="card-title">Group Home Shifts / Account Management  </h5>
       
      </div>
	  	  <div class="card-deck">
	  	  <div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
   <p class="card-text">Manage Your Group Home Shifts</p>
    <a href="/shifts/supermod.php" class="btn btn-primary">Manage Group Home Shifts</a>
  </div>
</div>
<div class="card text-center" style="width: 18rem;">
<div class="card-body bg-light text-dark">
    <h5 class="card-title"></h5>
    <p class="card-text">Change Your Account Password</p>
   <a href="/staff/changepass.php" class="btn btn-primary">Change Password</a>
  </div>
</div>
    </div>
  </div>
  </div>
    </div>
  </div>
                        ');

                        break;

					//if their user type does not match somehow, the user is redirected to the login page
                    default:
					
						header("Location: index.php");
						
                        break;
                }

				//if the user's password has been changed, alert them
				if(isset($_REQUEST['p']))
					echo '<script>alert("Your password has been changed.")</script>';
				
                //Include footer
                include "./includes/scripts/footer.php";

            }
            else
            {
				//if the user is not logged in, they are redirected to the login page
                header("Location: index.php");

            }

        ?>

    </body>

</html>