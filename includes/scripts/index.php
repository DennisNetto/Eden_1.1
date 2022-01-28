<?php
	//nobody should be able to access this directory,
	//so they get redirected to the login screen if they try
	header("Location: /index.php");
?>