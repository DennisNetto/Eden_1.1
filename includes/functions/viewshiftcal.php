<?php
/*  Developer:   Harley Lenton
 *  File Name:   viewshiftcal.php
 *  Description: Defines function used to create and display the calendar for viewschedshift
 *  Date Start:  03/04/2020
 *  Date End:    TBD
 */
 
	function build_calendar($month,$year,$id,$type)
	{

		// Create array containing abbreviations of days of week.
		$daysOfWeek = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');

		// What is the first day of the month in question?
		$firstDayOfMonth = mktime(0,0,0,$month,1,$year);
		
		//test////////////////////////////////////////////////////////////////////////////
		/////////////////////////////////////////////////////////////////////////////////////
		//print($firstDayOfMonth . "<br />");
		

		// How many days does this month contain?
		$numberDays = date('t',$firstDayOfMonth);

		// Retrieve some information about the first day of the
		// month in question.
		$dateComponents = getdate($firstDayOfMonth);
		
		//test
		//print("<br />");
		//print_r($dateComponents);
		
		//test/////////////////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////////////
		//print($dateComponents['wday']);

		// What is the name of the month in question?
		$monthName = $dateComponents['month'];

		// What is the index value (0-6) of the first day of the
		// month in question.
		$dayOfWeek = $dateComponents['wday'];

		// Create the table tag opener and day headers
		
		//If you go back or forward enough months you the year will reflect this only one year before and one in the future work with this current algorithm//////////////////////////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////////////////////
		if($month <= 0)
		{
			$year = $year - 1;
		}
		if($month > 12)
		{
			$year = $year + 1;
		}

		//Og class was calendar
		$calendar = "<table class='calendar'>";
		
		$calendar .=	"<tr >
							<td colspan='7' style='height: 3%;'>
								<form action='/shifts/viewschedshift.php' method='post' style='display: inline;'>
									<input type='hidden' name='nextMonth' value='-1'>
									<input type='hidden' name='id' value='$id'>
									<input type='hidden' name='type' value='$type'>
									<input type='submit' name='submit' value='Previous month'>
								</form>
							
								<form action='/shifts/viewschedshift.php' method='post' style='display: inline;'>
									<input type='hidden' name='nextMonth' value='1'>
									<input type='hidden' name='id' value='$id'>
									<input type='hidden' name='type' value='$type'>
									<input style='float: right;' type='submit' name='submit' value='Next month'>
								</form>
							</td>
						</tr>";
					
		$calendar .= "<tr><th colspan='7'><h2>$monthName" . " " . $dateComponents['year'] . "</h2></th></tr>";
		$calendar .= "<tr>";

		// Create the calendar headers

		foreach($daysOfWeek as $day) 
		{
			$calendar .= "<th class='header'>$day</th>";
		}

		// Create the rest of the calendar

		// Initiate the day counter, starting with the 1st.

		$currentDay = 1;

		$calendar .= "</tr><tr>";

		// The variable $dayOfWeek is used to
		// ensure that the calendar
		// display consists of exactly 7 columns.          from td tag ///colspan='$dayOfWeek'
		
		////New version makes first day start on right day of week
		$i = 0;
		while ( ($dayOfWeek > 0) && ($i < $dayOfWeek) ) {
			$calendar .= "<td >&nbsp;</td>";
			$i++;
		}

		$month = str_pad($month, 2, "0", STR_PAD_LEFT);

		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		while ($currentDay <= $numberDays) 
		{

			// Seventh column (Saturday) reached. Start a new row.

			if ( ($dayOfWeek == 7) && ($currentDay != 1) ) 
			{

				$dayOfWeek = 0;
				$calendar .= "</tr><tr>";

			}
			
			/*
			if($currentDay == 1)
			{
				
				$currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);

				$date = "$year-$month-$currentDayRel";

				$calendar .= "<td class='day' id='$date'>$currentDay<br /></td>";

				// Increment counters

				$currentDay++;
				$dayOfWeek++;
				
			}
			*/

			$currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);

			$date = "$year-$month-$currentDayRel";

			$calendar .= "<td class='day' id='$date'>$currentDay<br /></td>";

			// Increment counters

			$currentDay++;
			$dayOfWeek++;

		}
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


		// Complete the row of the last week in month, if necessary

		if ($dayOfWeek != 7) 
		{

			$remainingDays = 7 - $dayOfWeek;
			$calendar .= "<td colspan='$remainingDays'>&nbsp;</td>";

		}

		$calendar .= "</tr>";

		$calendar .= "</table>";

		return $calendar;

	}


	/*
	$dateComponents = getdate();

	$month = $dateComponents['mon'];
	$year = $dateComponents['year'];

	echo build_calendar($month,$year,$dateArray);
	*/

?>