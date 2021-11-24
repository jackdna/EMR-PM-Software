<?php
if($package_notes_option == 'consent_package_id') {
	include_once('consent_package_print.php');
}else if($package_notes_option == 'surgical_package_id' ) {
	include_once('surgery_appointment_facesheet_print.php');
}else if($package_notes_option == 'visit_notes_id') {
	include_once('visit_notes_sheet.php');
}else{
	include_once('surgery_appointment_report_print.php');	
}

function HeadingTable($titleName,$flgret="0"){
	$ret = '
	<table style="width:100%;"   class="paddingTop" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td class="tb_heading" align="left" style="width:100%;"><b>'.strtoupper($titleName).'</b></td>
		</tr>
	</table>';
	if($flgret==1){return $ret;}else{print($ret);}
}
function HeadingTableHr($flgret="0"){
	//$ret='<table border="0" width="100%"><tr><td>&nbsp;</td></tr></table>';
	//print($ret);
	//if($flgret==1){return $ret;}else{print($ret);}
}

function SameWidthLable($Numchars,$flgret="0"){
	$str="";
	for($i=0;$i<$Numchars;$i++){
		$str=$str."A";
	}
	$ret='<font color="white">'.$str.'</font>';
 	if($flgret==1){return $ret;}else{print($ret);}
}

function get_time_difference( $start, $end ){
	$uts['start']      =    strtotime( $start );
	$uts['end']        =    strtotime( $end );
	if( $uts['start']!==-1 && $uts['end']!==-1 ){
		if( $uts['end'] >= $uts['start'] ){
			$diff    =    $uts['end'] - $uts['start'];
			if( $days=intval((floor($diff/86400))) )
				$diff = $diff % 86400;
			if( $hours=intval((floor($diff/3600))) )
				$diff = $diff % 3600;
			if( $minutes=intval((floor($diff/60))) )
				$diff = $diff % 60;
			$diff    =    intval( $diff );
			return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
		}
		else{
			trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
		}
	}
	else{
		trigger_error( "Invalid date/time data detected", E_USER_WARNING );
	}
	return( false );
}

function changeDates($date=""){
	$getDate = explode("-",$date);
	$curDate = $getDate[1].'-'.$getDate[2].'-'.$getDate[0];	
	return $curDate;
}

function getFormulaHeadArr(){
	$returnArr = array();
	$getFormulaheadingsStr = "SELECT formula_id, formula_heading_name FROM formulaheadings";
	$getFormulaheadingsQry = imw_query($getFormulaheadingsStr);
	if($getFormulaheadingsQry && imw_num_rows($getFormulaheadingsQry) > 0){
		while($rowFetch = imw_fetch_assoc($getFormulaheadingsQry)){
			$returnArr[$rowFetch['formula_id']] = $rowFetch['formula_heading_name'];
		}
	}
	return $returnArr;
}

function isAppletModified($sign,$bag="0"){
	$sign = trim($sign);

	if(empty($sign)){
		$ret = false;
	}else if(!empty($sign) && ((strpos($sign, "/") !== false) ||
		(strpos($sign, "-") === false) || (strpos($sign, ":") === false) || (strpos($sign, ";") === false)) ){
		// for new applet image string, return true
		$ret = true;
	}else{
		$signLength = strlen($sign);
		$bag = ($bag == "0") ? "0-:;" : $bag;
		$ret = false;
		for($i=0;$i<$signLength;$i++)
		{
			$signChar =  substr($sign,$i,1);

			$pos = strpos($bag, $signChar);
			if($pos === false)
			{
				//return true;
				$ret = true;
				break;
			}
		}
	}
	return $ret;
}

?>




