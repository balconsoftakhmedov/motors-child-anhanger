<?php

// The provided CSV data as a string.
$csvData   = <<<EOD
"trailer","from","until","calculated","correct","test","weekend ?","explanation of wrong calculation","solution";
"8","10.03 . , 18:00","12.03 . , 21:00","151","57","","Fr - So","2 x weekend + 1 day","weekend";
"6a","11.03 . , 10:00","11.03 . , 17:00","42","31","","Sa","weekend","1 day";
"8","17.03 . , 16:00","20.03 . , 08:00","151","57","","Fr - Mo","2 x weekend + 1 day","weekend";
"1","31.05 . , 16:00","05.06 . , 09:00","180","130","","Mi - Mo","3 days + 2 x weekend ( ? ) .","2 days + 4 hours ( fr ) weekend";
"5","24.03 . , 10:19","25.03 . , 10:19","36","26","","Fr - Sa","weekend","1 day";
"5","31.03 . , 12:00","01.04 . , 12:00","36","26","","Fr - Sa","weekend","1 day";
"8","24.03 . , 16:00","25.03 . , 16:00","57","37","","Fr - Sa","weekend","1 day";
"1","10.07 . , 17:38","19.07 . , 17:38","300","250","","Mo -- Mi","10 days ( ? )","4 days + 4 hours ( fr ) + weekend +
3 days";
"13","26.05 . , 13:00","29.05 . , 15:00","470","320","","Fr - Mo","day + 2 x weekend + day","weekend + 1 day";
"6a","01.04 . , 09:00","01.04 . , 19:00","42","31","","Sa","weekend","1 day";
"8","06.04 . , 13:00","11.04 . , 18:00","262","168","","Do - Di","2 days + 2 x weekend + 2 days","1 day weekend + 2 days";
"18","07.04 . , 20:26","08.04 . , 20:26","59","38","","Fr - Sa","weekend","1 day";
"1","14.07 . , 13:00","24.07 . , 11:00","330","260","","Fr -- Mo","11x day","weekend + 4 days + 4 hours ( fr ) +
weekend + 4 hours";
"1","28.04.2023 08:03","30.04.2023 08:03","90","70","","Sa - So","Ek weekend","4 hours ( fr ) + weekend";
"6a","30.06 . , 08:00","01.07 . , 08:00","42","31","","Fr - Sa","weekend","1 day";
"10","12.05 . , 17:00","13.05 . , 17:00","80","43","","Fr - Sa","weekend","1 day";
"9","29.04.2023 08:00","29.04.2023 17:00","62","42","","Sa","weekend","1 day";
"11","04.08 . , 09:00","07.08 . , 09:00","200","101","","Fr - Mo","???","4 hours ( fr ) + weekend";
"1","04.05 . , 08:00","05.05 . , 10:00","60","55","","Do - Fr","↓ Jump to latest message","";
EOD;
$products  = [
	"1"   => [ "4 Std." => "25,00 €", "24 Std." => "30,00 €", "Wochenende" => "45,00 €" ],
	"2"   => [ "4 Std." => "16,00 €", "24 Std." => "20,00 €", "Wochenende" => "30,00 €" ],
	"3"   => [ "4 Std." => "22,00 €", "24 Std." => "28,00 €", "Wochenende" => "39,00 €" ],
	"4"   => [ "4 Std." => "25,00 €", "24 Std." => "40,00 €", "Wochenende" => "60,00 €" ],
	"5"   => [ "4 Std." => "19,00 €", "24 Std." => "26,00 €", "Wochenende" => "36,00 €" ],
	"6"   => [ "4 Std." => "22,00 €", "24 Std." => "31,00 €", "Wochenende" => "42,00 €" ],
	"6A"  => [ "4 Std." => "22,00 €", "24 Std." => "31,00 €", "Wochenende" => "42,00 €" ],
	"7"   => [ "4 Std." => "29,00 €", "24 Std." => "39,00 €", "Wochenende" => "59,00 €" ],
	"8"   => [ "4 Std." => "27,00 €", "24 Std." => "37,00 €", "Wochenende" => "57,00 €" ],
	"9"   => [ "4 Std." => "32,00 €", "24 Std." => "42,00 €", "Wochenende" => "62,00 €" ],
	"10"  => [ "4 Std." => "0", "24 Std." => "43,00 €", "Wochenende" => "80,00 €" ],
	"11"  => [ "4 Std." => "27,00 €", "24 Std." => "52,00 €", "Wochenende" => "74,00 €" ],
	"11A" => [ "4 Std." => "30,00 €", "24 Std." => "58,00 €", "Wochenende" => "78,00 €" ],
	"13"  => [ "4 Std." => "0", "24 Std." => "85,00 €", "Wochenende" => "150,00 €" ],
	"14"  => [ "4 Std." => "20,00 €", "24 Std." => "38,00 €", "Wochenende" => "59,00 €" ],
	"17"  => [ "4 Std." => "35,00 €", "24 Std." => "70,00 €", "Wochenende" => "130,00 €" ],
	"18"  => [ "4 Std." => "24,00 €", "24 Std." => "38,00 €", "Wochenende" => "59,00 €" ]
];
$csvData   = preg_replace( '/(\d{2}\.\d{2}) \. , (\d{2}:\d{2})/', '$1.2023 $2', $csvData );
$lines     = explode( ";", $csvData );
$headers   = str_getcsv( array_shift( $lines ) );
$dataArray = [];
foreach ( $lines as $line ) {
	if ( ! empty( trim( $line ) ) ) { // To make sure we're not processing empty lines
		$dataArray[] = array_combine( $headers, str_getcsv( $line ) );
	}
}
foreach ( $dataArray as &$item ) {
	if ( isset( $products[ strtoupper( $item["trailer"] ) ] ) ) {
		$id = strtoupper( $item["trailer"] );
		if ( $id > 0 && '14.07.2023 13:00' == $item["from"] ) {
			$prices       = $products[ strtoupper( $item["trailer"] ) ];
			$hour4Price   = intval( $prices['4 Std.'] );
			$dayPrice     = intval( $prices['24 Std.'] );
			$weekendPrice = intval( $prices['Wochenende'] );
			$correct      = $item["correct"];
			$totalCost    = calculateRentalCost( $item["from"], $item["until"], $hour4Price, $dayPrice, $weekendPrice );
			$solution     = $item['solution'];
			$from         = $item["from"];
			$until        = $item["until"];
			if ( $correct == $totalCost ) {
				//echo " Correct price - $totalCost ";
				//	echo "<br/> id=$id, hour4Price=$hour4Price, dayPrice=$dayPrice, weekendPrice=$weekendPrice, Total cost is $totalCost<br/><br/>";
			} else {
				echo "from $from - to $until, Calculated Wrong price $totalCost - correct price is $correct -- Solution is $solution";
				echo "<br/> Trailer=$id, hour4Price=$hour4Price, dayPrice=$dayPrice, weekendPrice=$weekendPrice, Total cost is $totalCost<br/><br/>";
				//break;
			}

		} else {
			$item['calculatedCost'] = "Product pricing not found!";
		}
	}
}
function diff_time( $pickupDateTime, $returnDateTime ) {
	$duration   = $pickupDateTime->diff( $returnDateTime );
	$totalHours = $duration->days * 24 + $duration->h;

	return $totalHours;
}

function calculateRentalCost( $pickupDate, $returnDate, $hour4Price, $dayPrice, $weekendPrice ) {
	$totalCost      = 0;
	$pickupDateTime = new DateTime( $pickupDate );
	$startDateTime  = new DateTime( $pickupDate );;
	$returnDateTime = new DateTime( $returnDate );
	$duration       = $pickupDateTime->diff( $returnDateTime );
	$totalHours = diff_time( $pickupDateTime, $returnDateTime );
	if ( $totalHours <= 4 ) {
		return $hour4Price;
	}
	while ( $pickupDateTime <= $returnDateTime ) {

		$dayOfWeek = $pickupDateTime->format( 'w' );
		$hourOfDay = (int) $pickupDateTime->format( 'H' );
		$currenttotalHours = diff_time( $pickupDateTime, $returnDateTime );
		//stm_show ( "dayOfWeek = $dayOfWeek, pickupDateTime == " . $pickupDateTime->format( 'Y-m-d H:i:s' ) . " totalHours= $currenttotalHours" . " <br/>  ");
		if ( ( ( $dayOfWeek == 5 && $hourOfDay >= 12 ) ) && ( $currenttotalHours > 24 ) ) {
			$nextInterval = new DateTime( $pickupDateTime->format( 'Y-m-d H:i:s' ) );
			if ( $nextInterval <= $returnDateTime ) {

				if ( $startDateTime < $pickupDateTime ) {
					$fdayOfWeek = $startDateTime->format( 'w' );
					$fhourOfDay = (int) $startDateTime->format( 'H' );
					$totalCost  += $hour4Price;
					stm_show( " friady 4hrs pay $hour4Price " );
				}
				$pickupDateTime->modify( '+48 hours' );
				$totalCost += $weekendPrice;
				stm_show( " friady hourOfDay $hourOfDay $weekendPrice" );
			} else {
				$pickupDateTime->modify( '+48 hours' );
			}
			//	stm_show ("weekendPrice =$weekendPrice <br/> ");
		} else if ( ( ( $dayOfWeek == 5 && ( $hourOfDay < 12 ) ) ) && ( $currenttotalHours > 24 ) ) {
			$nextInterval = new DateTime( $pickupDateTime->format( 'Y-m-d H:i:s' ) );
			if ( $nextInterval <= $returnDateTime ) {

				if ( $startDateTime <= $pickupDateTime ) {
					$fdayOfWeek = $startDateTime->format( 'w' );
					$fhourOfDay = (int) $startDateTime->format( 'H' );
					$totalCost  += $hour4Price;
				}
				$pickupDateTime->modify( '+48 hours' );
				$totalCost += $weekendPrice;
				stm_show( " rrrr hourOfDay $hourOfDay $weekendPrice" );
			} else {
				$pickupDateTime->modify( '+48 hours' );
			}
		} else if ( ( ( $dayOfWeek == 1 && ( $hourOfDay < 12 ) ) ) && ( $currenttotalHours > 24 ) ) {
			$totalCost += $hour4Price;
			$pickupDateTime->modify( '+24 hours' );
		} else if ( $dayOfWeek != 0 ) {

			if ( $currenttotalHours > 4 ) {
				$totalCost += $dayPrice;
				stm_show( "day 1 " . $dayPrice . " <br/>  " );
			} elseif ( $currenttotalHours <= 4 && $currenttotalHours > 0 ) {
				$totalCost += $hour4Price;
				stm_show( "hour4Price == $hour4Price <br/> " );
			}
			$pickupDateTime->modify( '+24 hours' );

		} else {
			$pickupDateTime->modify( '+24 hours' );
		}

	}

	return $totalCost;
}

function stm_show( $val ) {
	//echo $val." <br />";
}

?>




