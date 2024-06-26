<?php
/*  Developer:   Harley Lenton
 *  File Name:   calendar2.php
 *  Description: Defines function to create and display calendar
 *  Date Start:  25/02/2020
 *  Date End:    TBD
 */
 
	function build_calendar($month,$year,$dateArray) {

		// Create array containing abbreviations of days of week.
		$daysOfWeek = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');

		// What is the first day of the month in question?
		$firstDayOfMonth = mktime(0,0,0,$month,1,$year);

		// How many days does this month contain?
		$numberDays = date('t',$firstDayOfMonth);

		// Retrieve some information about the first day of the
		// month in question.
		$dateComponents = getdate($firstDayOfMonth);

		// What is the name of the month in question?
		$monthName = $dateComponents['month'];

		// What is the index value (0-6) of the first day of the
		// month in question.
		$dayOfWeek = $dateComponents['wday'];

		// Create the table tag opener and day headers

		$calendar = "<table class='calendar'>";
		$calendar .= "<tr><th colspan='7'><h2>$monthName $year</h2></th></tr>";
		$calendar .= "<tr>";

		// Create the calendar headers

		foreach($daysOfWeek as $day) {
			$calendar .= "<th class='header'>$day</th>";
		}

		// Create the rest of the calendar

		// Initiate the day counter, starting with the 1st.

		$currentDay = 1;

		$calendar .= "</tr><tr>";

		// The variable $dayOfWeek is used to
		// ensure that the calendar
		// display consists of exactly 7 columns.          from td tag ///colspan='$dayOfWeek'

		if ($dayOfWeek > 0) {
			$calendar .= "<td >&nbsp;</td>";
		}

		$month = str_pad($month, 2, "0", STR_PAD_LEFT);

		while ($currentDay <= $numberDays) {

			// Seventh column (Saturday) reached. Start a new row.

			if ($dayOfWeek == 7) {

				$dayOfWeek = 0;
				$calendar .= "</tr><tr>";

			}

			$currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);

			$date = "$year-$month-$currentDayRel";

			$calendar .= "<td class='day' id='$date'>$currentDay</td>";

			// Increment counters

			$currentDay++;
			$dayOfWeek++;

		}



		// Complete the row of the last week in month, if necessary

		if ($dayOfWeek != 7) {

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