<?php

require_once("../../../../config/globals.php");

$rangeFromTime = $_REQUEST["rangeFromTime"];
$arrRangeFromTime = explode(":",$rangeFromTime);
$hidRangeFromHr = $arrRangeFromTime[0];
$hidRangeFromMn = $arrRangeFromTime[1];
$hidRangeFromAP = $arrRangeFromTime[2];  

$rangeToTime = $_REQUEST["rangeToTime"];
$arrRangeToTime = explode(":",$rangeToTime); 
$hidRangeToHr = $arrRangeToTime[0];
$hidRangeToMn = $arrRangeToTime[1];
$hidRangeToAP = $arrRangeToTime[2];

$thisFromTime = $_REQUEST["thisFromTime"];
$arrThisFromTime = explode(":",$thisFromTime);
$hidThisFromHr = $arrThisFromTime[0];
$hidThisFromMn = $arrThisFromTime[1];
$hidThisFromAP = $arrThisFromTime[2];

$thisToTime = $_REQUEST["thisToTime"];
$arrThisToTime = explode(":",$thisToTime);
$hidThisToHr = $arrThisToTime[0];
$hidThisToMn = $arrThisToTime[1];
$hidThisToAP = $arrThisToTime[2];

$returnValue = 1;
if($hidThisFromHr<>"" && $hidThisFromMn<>"" && $hidThisToHr<>"" && $hidThisToMn<>"" && $hidThisFromAP<>"" && $hidThisToAP<>"" && $hidThisToHr<>"" && $hidThisToMn<>"" && $hidThisToHr<>"" && $hidThisToMn<>"" && $hidThisToAP<>"" && $hidThisToAP<>""){
    
    if($hidRangeFromAP == "PM"){
        if($hidRangeFromHr < 12){
            $hidRangeFromHr += 12;
        }
    }    
    if($hidRangeFromAP == "AM"){
        if($hidRangeFromHr == 12){
            $hidRangeFromHr = "00";
        }
    }

    $hidRangeFromHr = (strlen($hidRangeFromHr) == 1) ? "0".$hidRangeFromHr : $hidRangeFromHr;
    
    if($hidRangeToAP == "PM"){
        if($hidRangeToHr < 12){
            $hidRangeToHr += 12;
        }
    }    
    if($hidRangeToAP == "AM"){
        if($hidRangeToHr == 12){
            $hidRangeToHr = "00";
        }
    }

    $hidRangeToHr = (strlen($hidRangeToHr) == 1) ? "0".$hidRangeToHr : $hidRangeToHr;
    
    if($hidThisFromAP == "PM"){
        if($hidThisFromHr < 12){
            $hidThisFromHr += 12;
        }
    }    
    if($hidThisFromAP == "AM"){
        if($hidThisFromHr == 12){
            $hidThisFromHr = "00";
        }
    }

    $hidThisFromHr = (strlen($hidThisFromHr) == 1) ? "0".$hidThisFromHr : $hidThisFromHr;
    
    if($hidThisToAP == "PM"){
        if($hidThisToHr < 12){
            $hidThisToHr += 12;
        }
    }    
    if($hidThisToAP == "AM"){
        if($hidThisToHr == 12){
            $hidThisToHr = "00";
        }
    }

    $hidThisToHr = (strlen($hidThisToHr) == 1) ? "0".$hidThisToHr : $hidThisToHr;
    
    $rangeFromTime = mktime($hidRangeFromHr, $hidRangeFromMn, 0);
    $rangeToTime = mktime($hidRangeToHr, $hidRangeToMn, 0);
    $thisFromTime = mktime($hidThisFromHr, $hidThisFromMn, 0);
    $thisToTime = mktime($hidThisToHr, $hidThisToMn, 0);
    
    if($thisFromTime < $rangeFromTime || $thisToTime > $rangeToTime){
          $returnValue = 1;
    }else{
          $returnValue = 0;
    }
}
echo $returnValue;
?>