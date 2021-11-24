<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");
include(dirname(__FILE__)."/../../../../library/classes/class.tests.php");
$objTests				= new Tests;
$activeTests = $objTests->get_active_tests();
$correct = (isset($_GET['correct']) && $_GET['correct']!='') ? trim($_GET['correct']) : false;
foreach($activeTests as $tn_id=>$tn_rs){
/*
    [id] => 1
    [test_name] => A/Scan
    [test_table] => surgical_tbl
    [test_table_pk_id] => surgical_id
    [patient_key] => patient_id
    [phy_id_key] => signedById
    [exam_date_key] => examDate
    [performed_key] => performedByOD
    [script_file] => ascan.php
    [test_imaging] => 0
    [del_status] => 0
    [t_manager] => 1
    [status] => 1
    [test_type] => 0
    [version] => 0
    [temp_name] => A/Scan
*/
	$test_pkcol = $tn_rs['test_table_pk_id'];
	$test_name = $tn_rs['test_name'];
	$test_table = $tn_rs['test_table'];
	$chk_key	= $tn_rs['phy_id_key'];
	$pt_key	= $tn_rs['patient_key'];	
	$exam_key	= $tn_rs['exam_date_key'];
	$order_key	= $tn_rs['performed_key'];
	echo '<h3>'.$test_name.'</h3>';
	$q = "SELECT $test_pkcol as test_tbl_pkid, $pt_key AS pt_id_key,DATE_FORMAT($exam_key,'%m-%d-%Y') as exam_dt_key,$order_key as order_by_key
			FROM $test_table 
			WHERE ($chk_key='0' OR $chk_key='') 
				  AND ($pt_key!='' AND $pt_key!='0') 
				  AND $exam_key >= '2018-03-01'  
				  AND finished='1'";
	$res = imw_query($q);
	if($res && imw_num_rows($res)>0){
		echo '<table style="width:600px; border-collapse:collapse;" border="1">
				<tr>
					<th>S.No.</th>
					<th>Patient Name</th>
					<th>Exam Date</th>
					<th>Performed By</th> 
				</tr>
		';
		$sn = 1;
		while($rs = imw_fetch_assoc($res)){
			$test_tbl_pkid = $rs['test_tbl_pkid'];
			$pt_name_arr = core_get_patient_name($rs['pt_id_key']);
			$table_pkid = $rs['test_tbl_pkid'];
			//pre($pt_name_arr,1);
			echo '<tr>
					<td>'.$sn.'</td>
					<td>'.$pt_name_arr[4].'</td>
					<td>'.$rs['exam_dt_key'].'</td>
					<td>'.getUserFirstName($rs['order_by_key'],3).'</td>
				  </tr>';
			$sn++;
			
			if(strtolower($correct)=='yes'){
				$q = "UPDATE $test_table SET finished='0' WHERE $test_pkcol = '$test_tbl_pkid' LIMIT 1";
				//echo $q.'<br>';
				imw_query($q);
			}
		}
		echo '</table>';
	}else if($res && imw_num_rows($res)==0){
		echo '<div>No record.</div>';		
	}else if(!$res){
		echo '<div style="color:#ff0000">'.imw_error().'</div>';
	}
	
}

?>