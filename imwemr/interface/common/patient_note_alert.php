<?php
$sql_notes_column="";
switch($patient_notes_tab)
{
	case "accounting":
		$sql_notes_column = "chk_notes_accounting AS chk";
	break;
	case "chart_notes":
		$sql_notes_column = "chk_notes_chart_notes AS chk";
	break;
	case "scheduler":
		$sql_notes_column = "chk_notes_scheduler AS chk";
	break;
	default:
		$sql_notes_column = "";
}
if(!empty($sql_notes_column) && $sql_notes_column!="")
{
	$sql_notes_alert = "select patient_notes AS notes, ".$sql_notes_column." FROM patient_data Where id='".$_SESSION["patient"]."'";
	$resource = imw_query($sql_notes_alert);
	$notes_array = imw_fetch_array($resource);
	if($notes_array["chk"]==1 && trim($notes_array["notes"])!="")
	{
		//this variable is being used in scheduler
		$patient_notes_message = preg_replace("/[\n\r]/","<br/>",$notes_array["notes"]);	//removing '\n' characters from the string

		if($patient_notes_tab != "scheduler"){
			//code for other modules than scheduler will goes here
		}
	}
}
?>

		
