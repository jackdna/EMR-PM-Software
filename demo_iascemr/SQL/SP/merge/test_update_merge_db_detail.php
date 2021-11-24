<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

	$masterDB 	= 	'demo_surgerycenter';
	$childDB1	= 	'demo_scemr_besc';
	$childDB2	= 	'demo_scemr_cesc';
	$childDB3 	= 	'demo_scemr_resc';
		
	$childDB 	= 	$childDB1;
	$IDOC_DB	=	'demo_idocdb';
	
	$DB_ARRAY	=	array($masterDB => $childDB1, $childDB1 => $childDB2, $childDB2 => $childDB3 );
	
	$inc		=	1000000;
	if($childDB == $childDB1)  	{$inc  = $inc * 2; }
	if($childDB == $childDB2)  	{$inc  = $inc * 3; }
	if($childDB == $childDB3) 	{$inc  = $inc * 4; }
	
	if($sv_file_name == "merge_patient_in_waiting_tbl.csv")
	{
		$inc = $inc + 100000; 	
	}
	
	$_postfix = array($childDB1 => '_1', $childDB2 => '_2', $childDB3 => '_3');
	die("You are not authorized to access this file. Please contact Administrator");
?>