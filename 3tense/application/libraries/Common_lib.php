<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Common_lib {
    //to truncate a string upto limited chaaracters
    function truncate($string, $length, $dots = "...") {
        return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
    }

    function moneyFormatIndia($num){
        $explrestunits = "" ;
        $num=preg_replace('/,+/', '', $num);
        $minus = false;
        //if its a negative value, strip the minus sign
        if (strpos($num, '-') !== false) {
            $minus = true;
            $num = preg_replace('/-/','',$num);
        }
        $words = explode(".", $num);
        $des="00";
        if(count($words)<=2){
            $num=$words[0];
            if(count($words)>=2){$des=$words[1];}
            if(strlen($des)<2){$des.="0";}else{$des=substr($des,0,2);}
        }
        if(strlen($num)>3){
            $lastthree = substr($num, strlen($num)-3, strlen($num));
            $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
            $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
            $expunit = str_split($restunits, 2);
            for($i=0; $i<sizeof($expunit); $i++){
                // creates each of the 2's group and adds a comma to the end
                if($i==0)
                {
                    $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
                }else{
                    $explrestunits .= $expunit[$i].",";
                }
            }
            $thecash = $explrestunits.$lastthree;
        } else {
            $thecash = $num;
        }
        //check if value was negative, if yes then add the minus sign before Rs. sign
        if ($minus) {
            $final = "-&#8377;".$thecash;
        } else {
            $final = "&#8377;".$thecash;
        }
        //check if decimal values were present, if yes, add them
        if($des != "00") {
            return "$final.$des"; // writes the final format where $currency is the currency symbol.
        } else {
            return $final;
        }
    }

    function moneyFormatIndiaClient($num)
    {
        $explrestunits = "" ;
        $num=preg_replace('/,+/', '', $num);
        $minus = false;
        //if its a negative value, strip the minus sign
        if (strpos($num, '-') !== false) {
            $minus = true;
            $num = preg_replace('/-/','',$num);
        }
        $words = explode(".", $num);
        $des="00";
        if(count($words)<=2){
            $num=$words[0];
            if(count($words)>=2){$des=$words[1];}
            if(strlen($des)<2){$des.="0";}else{$des=substr($des,0,2);}
        }
        if(strlen($num)>3){
            $lastthree = substr($num, strlen($num)-3, strlen($num));
            $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
            $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
            $expunit = str_split($restunits, 2);
            for($i=0; $i<sizeof($expunit); $i++){
                // creates each of the 2's group and adds a comma to the end
                if($i==0)
                {
                    $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
                }else{
                    $explrestunits .= $expunit[$i].",";
                }
            }
            $thecash = $explrestunits.$lastthree;
        } else {
            $thecash = $num;
        }
        //check if value was negative, if yes then add the minus sign before Rs. sign
        if ($minus) {
            $final = "Rs.-".$thecash;
        } else {
            $final = "Rs.".$thecash;
        }
        //check if decimal values were present, if yes, add them
        if($des != "00") {
            return "$final.$des"; // writes the final format where $currency is the currency symbol.
        } else {
            return $final;
        }
    }

    public function money_formatter($format = '%.2n', $number)
	{ 
		$regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'. 
				  '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/'; 
		//code below added by Salmaan - 10/03/16
		// set locale to India, and also set the currency format to Rs.
		setlocale(LC_MONETARY, 'en_IN.UTF-8');
		//$format = '%!i';
		
		if (setlocale(LC_MONETARY, 0) == 'C') { 
			setlocale(LC_MONETARY, ''); 
		} 
		$locale = localeconv(); 
		preg_match_all($regex, $format, $matches, PREG_SET_ORDER); 
		foreach ($matches as $fmatch) { 
			$value = floatval($number); 
			$flags = array( 
				'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ? 
							   $match[1] : ' ', 
				'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0, 
				'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ? 
							   $match[0] : '+', 
				'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0, 
				'isleft'    => preg_match('/\-/', $fmatch[1]) > 0 
			); 
			$width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0; 
			$left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0; 
			$right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits']; 
			$conversion = $fmatch[5]; 

			$positive = true; 
			if ($value < 0) { 
				$positive = false; 
				$value  *= -1; 
			} 
			$letter = $positive ? 'p' : 'n'; 

			$prefix = $suffix = $cprefix = $csuffix = $signal = ''; 

			$signal = $positive ? $locale['positive_sign'] : $locale['negative_sign']; 
			switch (true) { 
				case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+': 
					$prefix = $signal; 
					break; 
				case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+': 
					$suffix = $signal; 
					break; 
				case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+': 
					$cprefix = $signal; 
					break; 
				case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+': 
					$csuffix = $signal; 
					break; 
				case $flags['usesignal'] == '(': 
				case $locale["{$letter}_sign_posn"] == 0: 
					$prefix = '('; 
					$suffix = ')'; 
					break; 
			} 
			if (!$flags['nosimbol']) { 
				$currency = $cprefix . 
							($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) . 
							$csuffix; 
			} else { 
				$currency = ''; 
			} 
			$space  = $locale["{$letter}_sep_by_space"] ? ' ' : ''; 

			$value = number_format($value, $right, $locale['mon_decimal_point'], 
					 $flags['nogroup'] ? '' : $locale['mon_thousands_sep']); 
			$value = @explode($locale['mon_decimal_point'], $value);

			$n = strlen($prefix) + strlen($currency) + strlen($value[0]); 
			if ($left > 0 && $left > $n) { 
				$value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0]; 
			}
            //return $value;
			$value = implode($locale['mon_decimal_point'], $value); 
			if ($locale["{$letter}_cs_precedes"]) { 
				$value = $prefix . $currency . $space . $value . $suffix; 
			} else { 
				$value = $prefix . $value . $space . $currency . $suffix; 
			} 
			if ($width > 0) { 
				$value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ? 
						 STR_PAD_RIGHT : STR_PAD_LEFT); 
			} 

			$format = str_replace($fmatch[0], $value, $format); 
		} 
		return $format; 
	} 
}
