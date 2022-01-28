<?php
/*  Developer:   Justin Alho
 *  File Name:   search.php
 *  Description: searches the database for specified records and populates a dropdown
 *  Date Start:  20/03/2020
 *  Date End:    TBD
 */

//if a request is sent, set the get variable to the type of request
if(isset($_REQUEST['c']))
{
	$get = 'c';
}
else if(isset($_REQUEST['s']))
{
	$get = 's';
}

//if a search was sent
if(isset($get))
{
    //connect to the database
	include "../../dbseckey.php";
    $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);
	
	//if the request is for clients
	if($get == 'c')
	{
		//set the search parameter to the data sent from another page
		$sParam = '%' . $_REQUEST['c'] . '%';
		$params = array($sParam, $sParam);
		
		//retrieve client information from database
		$sql = $conn->prepare("SELECT CLIENT_ID, CLIENT_FNAME, CLIENT_LNAME
		FROM CLIENT
		WHERE (CLIENT_FNAME LIKE ?
		OR CLIENT_LNAME LIKE ?)
		AND CLIENT_STATUS = 'A'
		ORDER BY CLIENT_LNAME");
		
		$sql->execute($params);

		$row = $sql->fetchAll();

		//if there are no records
		if(sizeof($row) == 0)
		{
			echo "<option value=''>The search matched no client records</option>
			</select>";
			echo implode(":",$sql->errorInfo());
		}
		//if there are records
		else
		{
			//fill desired select with records from database
			foreach ($row as $data)
			{	
				echo "<option value='{$data['CLIENT_ID']}'>ID({$data['CLIENT_ID']})  {$data['CLIENT_LNAME']}, {$data['CLIENT_FNAME']}</option>";
			}
		}
	}
	
	//if the request is for staff
	if($get == 's')
	{
		//set the search parameter to the data sent from another page
		$sParam = '%' . $_REQUEST['s'] . '%';
		$params = array($sParam, $sParam);
		
		//retrieve client information from database
		$sql = $conn->prepare("SELECT STAFF_ID, STAFF_FNAME, STAFF_LNAME
		FROM STAFF
		WHERE (STAFF_FNAME LIKE ?
		OR STAFF_LNAME LIKE ?)
		AND (TYPE_CODE = 'S'
		OR TYPE_CODE = 'W')
		ORDER BY STAFF_LNAME");

			
		$sql->execute($params);

		$row = $sql->fetchAll();
		
		//if there are no records
		if(sizeof($row) == 0)
		{
			echo "<option value=''>The search matched no records</option>";
		}
		//if there are records
		else
		{
			//fill desired select with records from database
			foreach ($row as $data)
			{	
				echo "<option value='{$data['STAFF_ID']}'>ID({$data['STAFF_ID']})  {$data['STAFF_LNAME']}, {$data['STAFF_FNAME']}</option>";
			}
		}
	}
	
	//releasing database resources
	if(isset($conn))
	{
		$conn = null;
	}
}

//if someone is trying to access this file directly, send them back to the home page
else
{
	header("Location: /land.php");
}

?>