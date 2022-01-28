<?php
	/*
		Author: Harley Lenton
		File: isComplex.php
		Author: Harley Lenton
		Date: 03/03/20
		Brief: This function will determine whether the string used as an argument is complex.
	*/

    function isComplex($password)
    {
        $upper = false;
        $lower = false;
        $number = false;
        $special = false;
        $length = "";

        $passwordArray = str_split($password);

        $length = count($passwordArray);

        foreach($passwordArray as $ph)
        {
            if(ctype_upper($ph))
            {
                $upper = true;
            }
            if(ctype_lower($ph))
            {
                $lower = true;
            }
            if(ctype_digit($ph))
            {
                $number = true;
            }
            if(isSpecial($ph))
            {
                $special = true;
            }
        }

        if( ($upper) && ($lower) && ($number) && ($special) && ($length >= 8) )
        {
            return true;
        }
        else
        {
            return false;
        }

    }

?>
