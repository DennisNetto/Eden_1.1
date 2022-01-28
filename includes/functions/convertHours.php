<?php
/*
		Author: Harley Lenton
		File: convertHours.php
		Author: Harley Lenton
		Date: 03/03/20
		Brief: This function takes a string in the format HH:MM-HH:MM representing a range of hours and converts the time from 24 to 12hr format.
*/
	function convertTime($avail)
	{
		////////////////////// Time conversion 24 to 12hr format /////////////////////////////////////////
		
		$availArray = explode("-", $avail);
		
		
		if( (isset($availArray[0]) ) && (isset($availArray[1]) ) )
		{
		
			$hour = "";
			$endHour = "";
			
			$startArray = explode(":", $availArray[0]);
			$endArray = explode(":", $availArray[1]);
			
			//Converting shift start time to 12hr format
			if( ($startArray[0] < 12) && ($startArray[0] != 0) )
			{
				//Removing the leading zero 
				$hour = $startArray[0];
				$hour = intVal($hour, 10);
				$start = $hour . ":" . $startArray[1] . "AM";
			}
			else if($startArray[0] == 12)
			{
				$start = $startArray[0] . ":" . $startArray[1] . "PM";
			}
			else if($startArray[0] > 12)
			{
				$hour = $startArray[0] - 12;
				$start = $hour . ":" . $startArray[1] . "PM";
			}
			else if($startArray[0] == 0)
			{
				if(isset($startArray[1]) )
				{
					$hour = 12;
					$start = $hour . ":" . $startArray[1] . "AM";
				}
				else
				{
					$start = "";
				}
			}
			
			//Converting shift end time to 12hr format
			if( ($endArray[0] < 12) && ($endArray[0] != 0) )
			{
				//Removing the leading zero
				$endHour = $endArray[0];
				$endHour = intVal($endHour, 10);
				$end = $endHour . ":" . $endArray[1] . "AM";
			}
			else if($endArray[0] == 12)
			{
				$end = $endArray[0] . ":" . $endArray[1] . "PM";
			}
			else if($endArray[0] > 12)
			{
				$endHour = $endArray[0] - 12;
				$end = $endHour . ":" . $endArray[1] . "PM";
			}
			else if($endArray[0] == 0)
			{
				if(isset($endArray[1]) )
				{
					$endHour = 12;
					$end = $endHour . ":" . $endArray[1] . "AM";
				}
				else
				{
					$end = "";
				}
			}
			
			$availTime = $start . "-" . $end;
			
	
			return $availTime;
		}
		else
		{
			return "";
		}
	}
?>