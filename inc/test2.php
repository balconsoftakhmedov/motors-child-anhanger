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

$csvData   = preg_replace( '/(\d{2}\.\d{2}) \. , (\d{2}:\d{2})/', '$1.2023 $2', $csvData );