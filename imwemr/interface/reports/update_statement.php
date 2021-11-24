<?php
if(count($pat_statements)>0){
	if($_GET['st']<=0){
		imw_query("delete p from previous_statement_detail p inner join previous_statement ps on p.previous_statement_id = ps.previous_statement_id and ps.statement_acc_status='0' and ps.operator_id='".$_SESSION['authId']."')");
		imw_query("delete from previous_statement where statement_acc_status='0' and operator_id='".$_SESSION['authId']."'");
	}
	$search_option_arr['from']=$_REQUEST['from'];
	$search_option_arr['grp_id']=$grp_id;
	$search_option_arr['startLname']=$_REQUEST['startLname'];
	$search_option_arr['endLname']=$_REQUEST['endLname'];
	$search_option_arr['send_email']=$_REQUEST['send_email'];
	$search_option_arr['exclude_sent_email']=$_REQUEST['exclude_sent_email'];
	$search_option_arr['rePrint']=$_REQUEST['rePrint'];
	$search_option_arr['fully_paid']=$_REQUEST['fully_paid'];
	$search_option_arr['text_print']=$_REQUEST['text_print'];
	$search_option_arr['force_cond']=$_REQUEST['force_cond'];
	$search_option_arr['inc_chr_amt']=$_REQUEST['inc_chr_amt'];
	$search_option_arr['show_min_amt']=$_REQUEST['show_min_amt'];
	$search_option_arr['show_new_statements']=$_REQUEST['show_new_statements'];
	$search_options_serz=serialize($search_option_arr);
	foreach($pat_statements as $pat_statements_key=>$pat_statements_val){
		$pat_statements_id = array_keys($pat_statements[$pat_statements_key]);
		for($i=0;$i<count($pat_statements_id);$i++){			
			$dataArr['patient_id'] = $pat_statements_id[$i];
			$dataArr['statement_balance'] = $patient_id_bal[$pat_statements_key][$pat_statements_id[$i]];
			$dataArr['created_date'] = date('Y-m-d');
			$dataArr['created_time'] = date('H:i:s');
			$dataArr['operator_id'] = $_SESSION['authId'];
			if($force_cond=="yes"){
				$force_cond_val=1;
			}else{
				$force_cond_val=0;
			}	
			$dataArr['force_cond'] = $force_cond_val;	
			if($text_print){
				$dataArr['statement_default_print']="text";
			}else{
				$dataArr['statement_default_print']="pdf";
			}
			$dataArr['search_options'] = $search_options_serz;
			if(count($statementUpDetail[$pat_statements_id[$i]])>0){
				$dataArr['statement_acc_status'] = '0';
			}else{
				$dataArr['statement_acc_status'] = '1';
			}
			if($statement_base>0){
				$pat_statement_date_unserz=$pat_chl_ids=array();
				$pat_key=$pat_statements_id[$i];
				foreach($statementUpDetail[$pat_key] as $chl_key => $chl_val){
					$statementUpData= $statementUpDetail[$pat_key][$chl_key];
					$statement_count = $statementUpData['count'];
					$pat_chl_ids[$chl_key] = $statement_count;
					$pat_statement_date = unserialize($statementUpData['pat_date']);
					foreach($pat_statement_date as $psd_key => $psd_val){
						$pat_statement_date_unserz[$psd_key]=$pat_statement_date[$psd_key];
					}
				}
				$acc_statement_date_serz=serialize($pat_statement_date_unserz);
				$dataArr['statement_acc_count'] = $statement_count;
				$dataArr['statement_acc_date'] = $acc_statement_date_serz;
				$dataArr['statement_acc_chl'] = serialize($pat_chl_ids);
			}else{
				foreach($statementCntArr as $chl_key => $chl_val){
					if($statementCntArr[$chl_key]['patient_id']==$pat_statements_id[$i]){
						$statement_count = $statementCntArr[$chl_key]['statement_count'];
						if($statement_count>0){
							$pat_chl_ids[$chl_key] = $statement_count;
						}
					}
				}
				$dataArr['statement_acc_count'] = '';
				$dataArr['statement_acc_date'] = '';
				$dataArr['statement_acc_chl'] = serialize($pat_chl_ids);
			}
			
			$st_insert_id=AddRecords($dataArr,'previous_statement');
			$affected_id=$st_insert_id;
			$detail_dataArr=array();
			$detail_dataArr['statement_data'] = $pat_statements[$pat_statements_key][$pat_statements_id[$i]];
			$detail_dataArr['statement_txt_data'] = $txt_data[$pat_statements_key][$pat_statements_id[$i]];
			$detail_dataArr['previous_statement_id'] = $st_insert_id;
			AddRecords($detail_dataArr,'previous_statement_detail');
		}
	}
}
if($update_pat_statement=="yes"){
	$chl_statement_date = date('Y-m-d');
	$qry = imw_query("Select previous_statement_id,patient_id,statement_acc_count,statement_acc_date,statement_acc_chl from previous_statement where operator_id='".$_SESSION['authId']."' and statement_acc_status='0'");
	while($res = imw_fetch_array($qry)){
		$statement_acc_date=html_entity_decode($res['statement_acc_date']);
		if($statement_acc_date!=""){
			imw_query("update patient_data set acc_statement_count='".$res['statement_acc_count']."',acc_statement_date='$statement_acc_date' where id='".$res['patient_id']."'");
		}
		$statement_acc_chl_unserz=unserialize($res['statement_acc_chl']);
		foreach($statement_acc_chl_unserz as $chl_key=> $chl_val){
			imw_query("update patient_charge_list set statement_status = '1',statement_count = '".$res['statement_acc_count']."',statement_date='$chl_statement_date' where charge_list_id = '$chl_key'");
			if($statement_acc_date==""){
				echo "delete from statement_tbl where charge_list_id = '$chl_key'";
				imw_query("delete from statement_tbl where charge_list_id = '$chl_key'");
				imw_query("insert into statement_tbl set charge_list_id = '$chl_key',statement_date = '$chl_statement_date'");
			}
		}
		imw_query("update previous_statement set statement_acc_status='1' where previous_statement_id='".$res['previous_statement_id']."'");
		
		$affected_id=$res['previous_statement_id'];
	}
	if($_REQUEST['print_pdf']=='email'){
		if($_GET['total_mails'] != ""){
			if($_GET['mails_error']!=""){
				echo "<script type='text/javascript'>alert('".$_GET['mails_error']."');</script>";	
			}else{
				echo "<script type='text/javascript'>alert('".$_GET['total_mails_sent']."/".$_GET['total_mails']." emails sent successfully');</script>";
			}
		}
		$txt_filePath='';
	}
}

if($delete_pat_statement=="yes"){
	//imw_query("delete from previous_statement_detail where previous_statement_id in(Select previous_statement_id from previous_statement where operator_id='".$_SESSION['authId']."' and statement_acc_status='0')");
	//imw_query("delete from previous_statement where operator_id='".$_SESSION['authId']."' and statement_acc_status='0'");
}

/*$ins_qry_trans="insert into previous_statement_detail (statement_data,statement_txt_data,previous_statement_id) value";
$ins_qry_trans.="('".$detail_dataArr['statement_data']."','".$detail_dataArr['statement_txt_data']."','".$detail_dataArr['previous_statement_id']."'),";
$ins_qry_run=substr($ins_qry_trans,0,-1).';';
imw_query($ins_qry_run);*/

/*if($statement_base>0){
	foreach($statementUpDetail as $pat_key => $pat_val){
		$pat_statement_date_unserz=array();
		foreach($statementUpDetail[$pat_key] as $chl_key => $chl_val){
			$statementUpData= $statementUpDetail[$pat_key][$chl_key];
			$statement_count = $statementUpData['count'];
			$chl_statement_date = date('Y-m-d');
			$pat_statement_date = unserialize($statementUpData['pat_date']);
			foreach($pat_statement_date as $psd_key => $psd_val){
				$pat_statement_date_unserz[$psd_key]=$pat_statement_date[$psd_key];
			}
			imw_query("update patient_charge_list set statement_status = '1',statement_count = '$statement_count',statement_date='$chl_statement_date' where charge_list_id = '$chl_key'");
		}
		$acc_statement_date_serz=serialize($pat_statement_date_unserz);
		imw_query("update patient_data set acc_statement_count='$statement_count',acc_statement_date='$acc_statement_date_serz' where id='$pat_key'");
	}
}else{
	$chl_statement_date = date('Y-m-d');
	foreach($statementCntArr as $chl_key => $chl_val){
		$statement_count = $statementCntArr[$chl_key]['statement_count'];
		if($statement_count>0){
			imw_query("delete from statement_tbl where charge_list_id = '$chl_key'");
			imw_query("insert into statement_tbl set charge_list_id = '$chl_key',statement_date = '$chl_statement_date'");
			imw_query("update patient_charge_list set statement_status = '1',statement_date = '$chl_statement_date',statement_count = '$statement_count' where charge_list_id = '$chl_key'");
		}
	}
}*/
?>
