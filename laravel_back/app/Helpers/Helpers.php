<?php
function formatted ($number)
{
	if(is_null($number)) {
		return null;
	}

	return number_format($number, (int) $number == $number ? 0 : 2, '.', ' ');
}
