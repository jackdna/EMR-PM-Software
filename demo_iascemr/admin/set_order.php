<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.


include_once("../globalsSurgeryCenter.php");
include_once("logout.php");

$task = $_REQUEST['task'];

switch($task){
	case 'upMacRegSort':
		$records = $_POST['record'];
		if( is_array($records) && count($records) > 0 ){
			foreach($records as $key=>$data) {
				$qry = "Update predefine_mac_regional_questions Set sort_id = ".($key+1)." Where id = ".$data;
				imw_query($qry) or die($qry.': '.imw_error());
			}
		}
	break;
}