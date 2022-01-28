<?php
	/*  Developer:   Justin Alho
	 *  File Name:   getHours.php
	 *  Description: Defines the getHours function
	 *  Date Start:  02/03/2020
	 *  Date End:    02/03/2020
	 */
	 
	//This function takes a start and end time and returns the number of hours between the two as a decimal.
	function getHours($start, $end)
	{
		$stTime = strtotime($start);
		$enTime = strtotime($end);

		$hours = date('H', $enTime) - date('H', $stTime);
		$mins = date('i', $enTime) - date('i', $stTime);

		$diff = ($hours + ($mins / 60));

		$diff = number_format($diff, 1);
		return $diff;
	}
?>