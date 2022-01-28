<?php
	/*  Developer:   Evan Guest, Harley Lenton, Justin Alho
	 *  File Name:   navBar.php
	 *  Description: Displays the navbar that appears on other pages
	 *  Date Start:  04/03/2020
	 *  Date End:    TBD
	 */
	 
	//based on the user's type, they will get different options
	session_start(); 
	$user = $_SESSION['userName'];

	switch($userType)
	{
		//coordinators are able to access almost every page
		case "C":
		
			print('
			
				<nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-primary">
					<a class="navbar-brand" href="/land.php">
						<img src="../includes/images/Logo.png" width="60" height="30" class="d-inline-block align-top" alt="">
						Edenbridge Employee Portal
					</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>

					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<ul class="navbar-nav mr-auto">
							<li class="nav-item active">
								<a class="nav-link" href="/land.php">Home <span class="sr-only">(current)</span></a>
							</li>
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Staff/Client Management
								</a>
								<div class="dropdown-menu" aria-labelledby="navbarDropdown">
									<a class="dropdown-item" href="/staff/addstaff.php">Add New Staff Member</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/staff/viewstaff.php">Manage Staff</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/client/addclient.php">Add Client</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/client/viewclient.php">Manage Clients</a>
								</div>

							</li>
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Shift Management
								</a>
								<div class="dropdown-menu" aria-labelledby="navbarDropdown">
									<a class="dropdown-item" href="/shifts/schedshift.php">Schedule Shift</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/shifts/viewshift.php">Manage Shifts</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/shifts/viewrecshift.php">Manage Recurring Shifts</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/shifts/schedrecshift.php">Schedule Recurring Shifts</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/shifts/approvetime.php">Approve Hours</a>
								</div>

							</li>
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Department/Group Home Management
								</a>
								<div class="dropdown-menu" aria-labelledby="navbarDropdown">
									<a class="dropdown-item" href="/grouphome/addgh.php">Add Group Home</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/grouphome/viewgh.php">Manage Group Homes</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/department/adddep.php">Add Department</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/department/viewdep.php">Manage Departments</a>

								</div>

							</li>
							</li>
														<li class="nav-item dropdown">
								<a class="nav-link" href="../staff/help.php">Help</a>

							</li>
						</ul>
						<form class="form-inline my-2 my-lg-0" method="post" action="/includes/scripts/logOut.php">
							<h5>Logged In as:'); echo $user; print('</h5> &nbsp &nbsp
							<button type="submit" class="btn btn-danger">Logout</button>
						</form>
					</div>
				</nav>	
			');
		
		break;
		
		//for the workers, they only get the option of either checking their schedule or submitting their hours
		case "W":
		
			print('
			
				<nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-primary">
					<a class="navbar-brand" href="/land.php">
						<img src="../includes/images/Logo.png" width="60" height="30" class="d-inline-block align-top" alt="">
						Edenbridge Employee Portal
					</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>

					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<ul class="navbar-nav mr-auto">
						
							<li class="nav-item active">
								<a class="nav-link" href="/land.php">Home <span class="sr-only">(current)</span></a>
							</li>
							
							<li class="nav-item dropdown">
								<a class="nav-link" href="/shifts/viewSched.php">Check Schedule</a>

							</li>
							
							<li class="nav-item dropdown">
								<a class="nav-link" href="/shifts/timesheet.php">Submit Timesheet</a>

							</li>
							</li>
														<li class="nav-item dropdown">
								<a class="nav-link" href="../staff/help.php">Help</a>

							</li>
						 
						</ul>
						<form class="form-inline my-2 my-lg-0" method="post" action="/includes/scripts/logOut.php">
							<h5>Logged In as:'); echo $user; print('</h5> &nbsp &nbsp
							<button type="submit" class="btn btn-danger">Logout</button>
						</form>
					</div>

				</nav>
			
			');
			
			break;
			
		//currently, bookkeepers can only view reports.
		//they should be able to do everything a coordinator can as well
		case "B":
		
			print('
			
				<nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-primary">
					<a class="navbar-brand" href="/land.php">
						<img src="../includes/images/Logo.png" width="60" height="30" class="d-inline-block align-top" alt="">
						Edenbridge Employee Portal
					</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>

					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<ul class="navbar-nav mr-auto">
							<li class="nav-item active">
								<a class="nav-link" href="/land.php">Home <span class="sr-only">(current)</span></a>
							</li>
							
							<li class="nav-item dropdown">
								<a class="nav-link" href="/etc/reports.php">View Reports</a>

							</li>
							
							
							
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Staff/Client Management
								</a>
								<div class="dropdown-menu" aria-labelledby="navbarDropdown">
									<a class="dropdown-item" href="/staff/addstaff.php">Add New Staff Member</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/staff/viewstaff.php">Manage Staff</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/client/addclient.php">Add Client</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/client/viewclient.php">Manage Clients</a>
								</div>

							</li>
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Shift Management
								</a>
								<div class="dropdown-menu" aria-labelledby="navbarDropdown">
									<a class="dropdown-item" href="/shifts/schedshift.php">Schedule Shift</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/shifts/viewshift.php">Manage Shifts</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/shifts/viewrecshift.php">Manage Recurring Shifts</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/shifts/schedrecshift.php">Schedule Recurring Shifts</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/shifts/approvetime.php">Approve Hours</a>
								</div>

							</li>
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Department/GH Management
								</a>
								<div class="dropdown-menu" aria-labelledby="navbarDropdown">
									<a class="dropdown-item" href="/grouphome/addgh.php">Add Group Home</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/grouphome/viewgh.php">Manage Group Homes</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/department/adddep.php">Add Department</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="/department/viewdep.php">Manage Departments</a>

								</div>

							</li>
														<li class="nav-item dropdown">
								<a class="nav-link" href="../staff/help.php">Help</a>

							</li>
							
						</ul>
						<form class="form-inline my-2 my-lg-0" method="post" action="/includes/scripts/logOut.php">
							<h5>Logged In as:'); echo $user; print('</h5> &nbsp &nbsp
							<button type="submit" class="btn btn-danger">Logout</button>
						</form>
					</div>

				</nav>
			
			');
			
			break;
			
		//supervisors are able to change the workers for shifts in group homes that they supervise
		case "S":
			
			print('
			
				<nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-primary">
					<a class="navbar-brand" href="/land.php">
						<img src="../includes/images/Logo.png" width="60" height="30" class="d-inline-block align-top" alt="">
						Edenbridge Employee Portal
					</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>

					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<ul class="navbar-nav mr-auto">
							<li class="nav-item active">
								<a class="nav-link" href="/land.php">Home <span class="sr-only">(current)</span></a>
							</li>

							<li class="nav-item dropdown">
								<a class="nav-link" href="/shifts/viewSched.php">Check Schedule</a>

							</li>
							
							<li class="nav-item dropdown">
								<a class="nav-link" href="/shifts/timesheet.php">Submit Timesheet</a>

							</li>
							
							<li class="nav-item dropdown">
								<a class="nav-link" href="/shifts/supermod.php">Manage Shifts <span class="sr-only">(current)</span></a>
							</li>
							</li>
														<li class="nav-item dropdown">
								<a class="nav-link" href="../staff/help.php">Help</a>

							</li>
						</ul>
						<form class="form-inline my-2 my-lg-0" method="post" action="/includes/scripts/logOut.php">
							<h5>Logged In as:'); echo $user; print('</h5> &nbsp &nbsp
							<button type="submit" class="btn btn-danger">Logout</button>
						</form>
					</div>

				</nav>
			
			');
			
			break;
		
		//if the user is not logged in, there will be no options available
		//this variant should only appear on the login screen
		default:
		
			print('
			
				<nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-primary">
					<a class="navbar-brand" href="/land.php">
						<img src="../includes/images/Logo.png" width="60" height="30" class="d-inline-block align-top" alt="">
						Edenbridge Employee Portal
					</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>

				</nav>
			
			');
			
	}

?>