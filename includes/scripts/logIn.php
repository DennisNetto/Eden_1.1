<?php
	/*  Developer:   Harley Lenton
	 *  File Name:   logIn.php
	 *  Description: validates submitted credentials to either allow or deny access to users trying to log in
	 *  Date Start:  03/03/2020
	 *  Date End:    TBD
	 */
    //session_start();

    if(isset($_REQUEST['userName']))
    {
        $userName = $_POST['userName'];
        $password = $_POST['password'];
		
    }
	else
	{
		//echo "There was no userName post";
	}

    try
    {
		$dbUsername = 'username';
		$dbPassword = 'password';
		$conn = new PDO("mysql:host=localhost; dbname=oldcount_edenbridge", $dbUsername, $dbPassword);

        //$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //echo "Connection successful<br />";
        //phpinfo();

        $stm = $conn->prepare("SELECT USER_NAME, USER_PASS, TYPE_CODE, STAFF_ID, STAFF_STATUS FROM STAFF WHERE USER_NAME = ? ");

        if (isset($userName))
        {
            $exeParams = array($userName);

            $stm->execute($exeParams);

            $row = $stm->fetch();

            $hash = $row['USER_PASS'];
        }
		else
		{
			//echo "There is no userName variable";
		}

        if (isset($password))
        {
            if ($row['STAFF_STATUS'] == 'I' && password_verify($password, $hash)) {
                echo "
            <section class='d-flex justify-content-center'>
            <div class='form-con'><p class='text-danger'> Your account is deactivated</p></div></section>";
            quit();
            }
            if (password_verify($password, $hash))
            {
                session_regenerate_id(true);
            
                $_SESSION['userType'] = $row['TYPE_CODE'];
                $_SESSION['staffID'] = $row['STAFF_ID'];
				$_SESSION['userName'] = $row['USER_NAME'];
				
				date_default_timezone_set("US/Mountain");
				//F j, Y, g:i a
				$dateString = date("r");
				file_put_contents("./logs/loginLog.txt", "\n" . $row['USER_NAME'] . " logged in on: " . $dateString, FILE_APPEND | LOCK_EX);
				
				header('Location: /land.php');
            }
            else
            {
				
				//test
				//$array = array(1,2,3,4,5,6,7,8,9);
				//print_r($row);
				//print($hash);
				
				echo "
				<section class='d-flex justify-content-center'>
				<div class='form-con'><p class='text-danger'> The username or password was not valid</p></div></section>";
                //header("Location: index.php?message=invalidCredentials");
            }
        }
        else
        {
			//echo "Password variable not set.";
            //header("Location: index.php?message=invalidCredentials");
        }
    }
    catch(PDOException $e)
    {
        echo "Connection failed: " . $e->getMessage();
    }

?>
