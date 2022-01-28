<?php
	/*  Developer:   Beryon Clark, Harley Lenton
	 *  File Name:   valUserType.php
	 *  Description: Verifies the user type of any user, second phase of restricting access to pages.
	 *  Date Start:  20/02/2020
	 *  Date End:    TBD
	 */

	function valUserType(string $authLevel)
	{
		
		switch ($authLevel) {
			case "W": // Case specified for worker-only pages.
			 if ( ($_SESSION['userType'] != 'W') && ($_SESSION['userType'] != 'S') ) {
				//$prevPage = $_SESSION['page'] . "?message=invalCred";
				 header('Location: ../land.php');
			 }
				break;
			case "C":  // Case specified for coordinator-only pages.
				/* Redirect through hidden form with previous page information, append to end of URL error message for
				 * invalid user type.
				 */
				if ( ($_SESSION['userType'] != 'C') && ($_SESSION['userType'] != 'B') ) {
					//$prevPage = $_SESSION['page'] . "?message=invalCred";
					header('Location: ../land.php');
				}
				break;
			case "S":  // Case specified for supervisor-only pages.
				/* Redirect through hidden form with previous page information, append to end of URL error message for
				 * invalid user type.
				 */
				if ($_SESSION['userType'] != 'S') {
					//$prevPage = $_SESSION['page'] . "?message=invalCred";
					header('Location: ../land.php');
				}
				break;
			case "B":  // Case specified for bookkeeper-only pages.
				/* Redirect through hidden form with previous page information, append to end of URL error message for
				 * invalid user type.
				 */
				if ($_SESSION['userType'] != 'B') {
					//$prevPage = $_SESSION['page'] . "?message=invalCred";
					header('Location: ../land.php');
				}
				break;
			case "A":  // Case specified for administrator-only pages. Not used elsewhere presently.
				/* Redirect through hidden form with previous page information, append to end of URL error message for
				 * invalid user type.
				 */
				if ($_SESSION['userType'] != 'A') {
					//$prevPage = $_SESSION['page'] . "?message=invalCred";
					header('Location: ../land.php');
				}
				break;
			default: { // Case specified for when no match to the user type is found. Control module in event of session poisoning.
				session_unset();
				session_destroy();
				header('Location: schedule.edenbridge.ca');
			}
		}
	}
?>