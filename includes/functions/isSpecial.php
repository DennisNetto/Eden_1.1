<?php
	/*
		Author: Harley Lenton
		File: isSpecial.php
		Author: Harley Lenton
		Date: 03/03/20
		Brief: This function takes a single character string as an argument and compares it to various "special" characters
		returning true if the character is "special".
	*/
     function isSpecial($string)
	{
		switch($string)
		{
			case "~":

				return true;

				break;
			case "`":

				return true;

				break;
			case "@":

				return true;

				break;
			case "#":

				return true;

				break;
			case "$":

				return true;

				break;
			case "%":

				return true;

				break;
			case "^":

				return true;

				break;
			case "&":

				return true;

				break;
			case "*":

				return true;

				break;
			case "(":

				return true;

				break;
			case ")":

				return true;

				break;
			case "_":

				return true;

				break;
			case "-":

				return true;

				break;
			case "=":

				return true;

				break;
			case "+":

				return true;

				break;
			case ">":

				return true;

				break;
			case "<":

				return true;

				break;
			case "?":

				return true;

				break;
			default:

				return false;
		}
	}
?>