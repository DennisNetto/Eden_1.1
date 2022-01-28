<?php
	//this destroys the session, logging the user out
	session_start();
	session_destroy();
	header("Location: /index.php");	
?>