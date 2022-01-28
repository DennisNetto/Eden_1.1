<?php 

		/*  Developer:   Beryon Clark
	 *  File Name:   verLogin.php
	 *  Description: Verifies that user is logged in upon entering page restricted to authenticated users.
	 *  Date Start:  20/02/2020
	 *  Date End:    26/02/2020
	 *  TODO:        -Test functionality
	 */

	function verLogin(){ // If user is logged in, nothing happens. If they are not, they are thrown back to login page.
		if(isset($_SESSION['staffID']) == false) {
			header('Location: schedule.edenbridge.ca');
		}
	}

?>