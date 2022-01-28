<?php
/*  Author: Beryon C.
 *  Date: 17/06/2020
 *  Purpose: Collection of functions aimed at the calculations of whether clients have worked overtime.
 *  File: overtime.php
 *
 */

function allOvertime($startOfRange)
{
    // Initialization of required variables.
    $startDate = "";
    $endDate = "";

    // Capturing date and building range for SQL.
    $dateArray = explode("/", $startOfRange);
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $dateArray[1], $dateArray[0]); // Grabbing # of days in specified month, sidesteps issues with DST.

    // YYYY-MM-DD
    $startDate = $dateArray[0] . "-" . $dateArray[1] . "-01";
    $endDate = $dateArray[0] . "-" . $dateArray[1] . "-" . $daysInMonth;


    // Setting up SQL, variant for pulling all entries                     WHERE SHIFT.CLIENT_ID = '$shiftClient';
    $reportSQL =
        "SELECT S.CLIENT_ID AS CLIENT_ID, 
         S.SCHEDULED_START AS S_START, 
         S.SCHEDULED_END AS S_END, 
         S.CLAIMED_START AS C_START, 
         S.CLAIMED_END AS C_END, 
         S.APPROVED_START AS A_START, 
         S.APPROVED_END AS A_END, 
         S.SHIFT_DATE AS SHIFT_DATE, 
         C.CLIENT_MAX_HOURS AS CLIENT_MAX_HOURS
         FROM SHIFT AS S
         RIGHT JOIN CLIENT AS C ON C.CLIENT_ID=S.CLIENT_ID
         BETWEEN $startDate AND $endDate
         ORDER BY S.CLIENT_ID DESC;";
    // Connection information
    include "../dbseckey.php";
    $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);

    $reportInfo = $conn->prepare($reportSQL);
    $reportInfo->execute();
    $reportResults = $reportInfo->fetchAll();

    if (sizeof($reportResults) > 0) {

        // Approved > Claimed > Scheduled. Reverts to
        if($reportResults['A_START'] != NULL && $reportResults['A_END'] != NULL)
        {
            $sqlStartTime = $reportInfo['A_START'];
            $sqlEndTime = $reportInfo['A_END'];
        }
        elseif ($reportResults['C_START'] != NULL && $reportResults['C_END'] != NULL)
        {
            $sqlStartTime = $reportInfo['C_START'];
            $sqlEndTime = $reportInfo['C_END'];
        }
        else
        {
            $sqlStartTime = $reportInfo['S_START'];
            $sqlEndTime = $reportInfo['S_END'];
        }

        $overtimeArr = array();
        $overtimeList = array();
        $sqlDate = $reportInfo['SHIFT_DATE'];

        for($i = 0; $i < sizeof($reportResults); $i++) {
            if(array_search($reportInfo['CLIENT_ID'], $overtimeArr) == false) // Should the client's ID not be present in the array, it creates a new entry with which to assign hours to.
            {
                $overtimeArr[$reportInfo['CLIENT_ID']] = 0;
            }
            $shiftStartTime = date_create_from_format("Y-m-d H:i:s", $sqlDate . " " . $sqlStartTime);
            $shiftEndTime = date_create_from_format("Y-m-d H:i:s", $sqlDate . " " . $sqlEndTime);

            $shiftRuntime = $shiftStartTime -> diff($shiftEndTime, true);
            $overtimeArr[$reportInfo['CLIENT_ID']] += $shiftRuntime -> h;
            // If current hours exceed max hours and ID is not already present in array, flag is set for that client.
            if(($reportInfo['CLIENT_ID'] > $reportInfo['CLIENT_MAX_HOURS']) && (array_search($reportInfo['CLIENT_ID'], $overtimeArr) == false))
            {
                $overtimeList['CLIENT_ID'] = true;
            }
        }
        $conn = null;
        return $overtimeList;
    } else {
        $conn = null;
        return $noShiftsErr = "noShiftFound";
    }
}

function clientOvertime($specifiedClient, $startOfRange)
{
    // Initialization of required variables.
    $startDate = "";
    $endDate = "";

    // Capturing date and building range for SQL.
    $dateArray = explode("/", $startOfRange);
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $dateArray[1], $dateArray[0]); // Grabbing # of days in specified month, sidesteps issues with DST.

    // YYYY-MM-DD
    $startDate = $dateArray[0] . "-" . $dateArray[1] . "-01";
    $endDate = $dateArray[0] . "-" . $dateArray[1] . "-" . $daysInMonth;


    // Setting up SQL, variant for pulling all entries                     WHERE SHIFT.CLIENT_ID = '$shiftClient';
    $reportSQL =
        "SELECT S.CLIENT_ID AS CLIENT_ID, 
         S.SCHEDULED_START AS S_START, 
         S.SCHEDULED_END AS S_END, 
         S.CLAIMED_START AS C_START, 
         S.CLAIMED_END AS C_END, 
         S.APPROVED_START AS A_START, 
         S.APPROVED_END AS A_END, 
         S.SHIFT_DATE AS SHIFT_DATE, 
         C.CLIENT_MAX_HOURS AS CLIENT_MAX_HOURS
         FROM SHIFT AS S
         RIGHT JOIN CLIENT AS C ON C.CLIENT_ID=S.CLIENT_ID
         BETWEEN $startDate AND $endDate
         WHERE C.CLIENT_ID='$specifiedClient';";
    // Connection information
    include "../dbseckey.php";
    $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);

    $reportInfo = $conn->prepare($reportSQL);
    $reportInfo->execute();
    $reportResults = $reportInfo->fetchAll();

    if (sizeof($reportResults) > 0) {

        // Approved > Claimed > Scheduled. Reverts to
        if($reportResults['A_START'] != NULL && $reportResults['A_END'] != NULL)
        {
            $sqlStartTime = $reportInfo['A_START'];
            $sqlEndTime = $reportInfo['A_END'];
        }
        elseif ($reportResults['C_START'] != NULL && $reportResults['C_END'] != NULL)
        {
            $sqlStartTime = $reportInfo['C_START'];
            $sqlEndTime = $reportInfo['C_END'];
        }
        else
        {
            $sqlStartTime = $reportInfo['S_START'];
            $sqlEndTime = $reportInfo['S_END'];
        }

        $overtimeArr = array();
        $overtimeList = array();
        $sqlDate = $reportInfo['SHIFT_DATE'];

        for($i = 0; $i < sizeof($reportResults); $i++) {
            if(array_search($reportInfo['CLIENT_ID'], $overtimeArr) == false) // Should the client's ID not be present in the array, it creates a new entry with which to assign hours to.
            {
                $overtimeArr[$reportInfo['CLIENT_ID']] = 0;
            }
            $shiftStartTime = date_create_from_format("Y-m-d H:i:s", $sqlDate . " " . $sqlStartTime);
            $shiftEndTime = date_create_from_format("Y-m-d H:i:s", $sqlDate . " " . $sqlEndTime);

            $shiftRuntime = $shiftStartTime -> diff($shiftEndTime, true);
            $overtimeArr[$reportInfo['CLIENT_ID']] += $shiftRuntime -> h;
            // If current hours exceed max hours and ID is not already present in array, flag is set for that client.
            if(($reportInfo['CLIENT_ID'] > $reportInfo['CLIENT_MAX_HOURS']) && (array_search($reportInfo['CLIENT_ID'], $overtimeArr) == false))
            {
                $overtimeList['CLIENT_ID'] = true;
            }
        }

        $conn = null;
        return $overtimeList;
    } else {
        $conn = null;
        $errMsg = "noShiftFound";
        return $errMsg;
    }
}

function allHours($startOfRange)
{
    // Initialization of required variables.
    $startDate = "";
    $endDate = "";

    // Capturing date and building range for SQL.
    $dateArray = explode("/", $startOfRange);
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $dateArray[1], $dateArray[0]); // Grabbing # of days in specified month, sidesteps issues with DST.

    // YYYY-MM-DD
    $startDate = $dateArray[0] . "-" . $dateArray[1] . "-01";
    $endDate = $dateArray[0] . "-" . $dateArray[1] . "-" . $daysInMonth;


    // Setting up SQL, variant for pulling all entries                     WHERE SHIFT.CLIENT_ID = '$shiftClient';
    $reportSQL =
        "SELECT S.CLIENT_ID AS CLIENT_ID, 
         S.SCHEDULED_START AS S_START, 
         S.SCHEDULED_END AS S_END, 
         S.CLAIMED_START AS C_START, 
         S.CLAIMED_END AS C_END, 
         S.APPROVED_START AS A_START, 
         S.APPROVED_END AS A_END, 
         S.SHIFT_DATE AS SHIFT_DATE, 
         C.CLIENT_MAX_HOURS AS CLIENT_MAX_HOURS
         FROM SHIFT AS S
         RIGHT JOIN CLIENT AS C ON C.CLIENT_ID=S.CLIENT_ID
         BETWEEN $startDate AND $endDate
         ORDER BY S.CLIENT_ID DESC;";
    // Connection information
    include "../dbseckey.php";
    $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);

    $reportInfo = $conn->prepare($reportSQL);
    $reportInfo->execute();
    $reportResults = $reportInfo->fetchAll();

    if (sizeof($reportResults) > 0) {

        // Approved > Claimed > Scheduled. Reverts to
        if($reportResults['A_START'] != NULL && $reportResults['A_END'] != NULL)
        {
            $sqlStartTime = $reportInfo['A_START'];
            $sqlEndTime = $reportInfo['A_END'];
        }
        elseif ($reportResults['C_START'] != NULL && $reportResults['C_END'] != NULL)
        {
            $sqlStartTime = $reportInfo['C_START'];
            $sqlEndTime = $reportInfo['C_END'];
        }
        else
        {
            $sqlStartTime = $reportInfo['S_START'];
            $sqlEndTime = $reportInfo['S_END'];
        }

        $overtimeArr = array();
        $overtimeList = array();
        $sqlDate = $reportInfo['SHIFT_DATE'];

        for($i = 0; $i < sizeof($reportResults); $i++) {
            if(array_search($reportInfo['CLIENT_ID'], $overtimeArr) == false) // Should the client's ID not be present in the array, it creates a new entry with which to assign hours to.
            {
                $overtimeArr[$reportInfo['CLIENT_ID']] = 0;
            }
            $shiftStartTime = date_create_from_format("Y-m-d H:i:s", $sqlDate . " " . $sqlStartTime);
            $shiftEndTime = date_create_from_format("Y-m-d H:i:s", $sqlDate . " " . $sqlEndTime);

            $shiftRuntime = $shiftStartTime -> diff($shiftEndTime, true);
            $overtimeArr[$reportInfo['CLIENT_ID']] += $shiftRuntime -> h;
        }
        $conn = null;
        return $overtimeArr;

    } else {
        $conn = null;
        return $noShiftsErr = "noShiftFound";
    }
}

function clientHours($specifiedClient, $startOfRange)
{
    // Initialization of required variables.
    $startDate = "";
    $endDate = "";

    // Capturing date and building range for SQL.
    $dateArray = explode("/", $startOfRange);
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $dateArray[1], $dateArray[0]); // Grabbing # of days in specified month, sidesteps issues with DST.

    // YYYY-MM-DD
    $startDate = $dateArray[0] . "-" . $dateArray[1] . "-01";
    $endDate = $dateArray[0] . "-" . $dateArray[1] . "-" . $daysInMonth;


    // Setting up SQL, variant for pulling all entries                     WHERE SHIFT.CLIENT_ID = '$shiftClient';
    $reportSQL =
        "SELECT S.CLIENT_ID AS CLIENT_ID, 
         S.SCHEDULED_START AS S_START, 
         S.SCHEDULED_END AS S_END, 
         S.CLAIMED_START AS C_START, 
         S.CLAIMED_END AS C_END, 
         S.APPROVED_START AS A_START, 
         S.APPROVED_END AS A_END, 
         S.SHIFT_DATE AS SHIFT_DATE, 
         C.CLIENT_MAX_HOURS AS CLIENT_MAX_HOURS
         FROM SHIFT AS S
         RIGHT JOIN CLIENT AS C ON C.CLIENT_ID=S.CLIENT_ID
         BETWEEN $startDate AND $endDate
         WHERE C.CLIENT_ID='$specifiedClient';";
    // Connection information
    include "../dbseckey.php";
    $conn = new PDO("mysql:host=" . $host . "; dbname=" . $database . "", $username, $password);

    $reportInfo = $conn->prepare($reportSQL);
    $reportInfo->execute();
    $reportResults = $reportInfo->fetchAll();

    if (sizeof($reportResults) > 0) {

        // Approved > Claimed > Scheduled. Reverts to
        if($reportResults['A_START'] != NULL && $reportResults['A_END'] != NULL)
        {
            $sqlStartTime = $reportInfo['A_START'];
            $sqlEndTime = $reportInfo['A_END'];
        }
        elseif ($reportResults['C_START'] != NULL && $reportResults['C_END'] != NULL)
        {
            $sqlStartTime = $reportInfo['C_START'];
            $sqlEndTime = $reportInfo['C_END'];
        }
        else
        {
            $sqlStartTime = $reportInfo['S_START'];
            $sqlEndTime = $reportInfo['S_END'];
        }

        $overtimeArr = array();
        $sqlDate = $reportInfo['SHIFT_DATE'];

        for($i = 0; $i < sizeof($reportResults); $i++) {
            if(array_search($reportInfo['CLIENT_ID'], $overtimeArr) == false) // Should the client's ID not be present in the array, it creates a new entry with which to assign hours to.
            {
                $overtimeArr[$reportInfo['CLIENT_ID']] = 0;
            }
            $shiftStartTime = date_create_from_format("Y-m-d H:i:s", $sqlDate . " " . $sqlStartTime);
            $shiftEndTime = date_create_from_format("Y-m-d H:i:s", $sqlDate . " " . $sqlEndTime);

            $shiftRuntime = $shiftStartTime -> diff($shiftEndTime, true);
            $overtimeArr[$reportInfo['CLIENT_ID']] += $shiftRuntime -> h;
        }
        $conn = null;
        return $overtimeArr;
    } else {
        $errMsg = "noShiftFound";
        $conn = null;
        return $errMsg;

    }
}

?>