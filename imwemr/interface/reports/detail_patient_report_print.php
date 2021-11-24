<?php

/*--RECEIVING SUBMITTED DATA--*/
$arr_columns = array(); 		$arr_headers = array();
$arr_app_columns = array(); 	$arr_app_headers = array();
$arr_ins_columns = array(); 	$arr_ins_headers = array();
$insComp=array();

$str_columns = '';
$insTabQry = $insSel = $ins1Col =$insWhere='';

/*--CSV FILE NAME--*/
$csv_file_name = data_path().'users/UserId_'.$_SESSION['authId'].'/patient_detail'.session_id().'.csv';
$fileInfo = pathinfo($csv_file_name);
if(!is_dir($fileInfo['dirname'])) mkdir($fileInfo['dirname'], 0777, true);
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}

$str_provider='';
if(empty($provider)==false){
	$str_provider=implode(',', $provider);
}

$qry="Select id,in_house_code from insurance_companies";
$insRs = imw_query($qry) or die(imw_error());
while($insRes = imw_fetch_assoc($insRs))
{
	$insComp[$insRes['id']] =$insRes['in_house_code']; 
}

if(isset($_REQUEST['st'])){
	$_REQUEST['showCols'] = unserialize(html_entity_decode($_REQUEST['showCols']));
//	htmlentities(serialize($_REQUEST['showCols']))
}

$arr_ins_cols = array('id.provider as priInsProvider');

/*--SEPARATING FIELDS WHICH WILL NOT BE USED IN SQL QUERY (THESE FIELDS WILL BE RETRIEVED SEPARATELY)--*/
$insWhere ="";
foreach($_REQUEST['showCols'] as $val){
	$result[0][0]='';
	$tempCols = explode('|~~|',$val);

	if(!strstr($tempCols[0], 'primary_care')){
		$preg="/^secondary|^primary/";
		preg_match_all($preg, $tempCols[0], $result);
	}
	
	if($result[0][0]!=''){
		$insTypeArr[$result[0][0]] = $result[0][0];
		$colNam = explode("-",$tempCols[0]);
		$insSelArr[$colNam[1]]=$colNam[1];
		
		$ins_headers[$tempCols[1]] = $tempCols[1];
		if($result[0][0]=='primary'){
			$ins_matchPri[$colNam[1]] = $colNam[1]; 	
		}
		if($result[0][0]=='secondary'){
			$ins_matchSec[$colNam[1]] = $colNam[1]; 	
		}

	}else{
		$arr_columns[] = $tempCols[0];
		$arr_headers[] = $tempCols[1];
	}
}

//$_REQUEST['showApptCols'] = unserialize(html_entity_decode($_REQUEST['showApptCols']));
foreach($_REQUEST['showApptCols'] as $val){
	$result[0][0]='';
	$temp_Cols = explode('|~~|',$val);
	if(strstr($temp_Cols[0], 'prv.')){
		$arr_prv_columns[] = $temp_Cols[0];
		$arr_prv_headers[] = $temp_Cols[1];
	}else{
		$arr_app_columns[] = $temp_Cols[0];
		$arr_app_headers[] = $temp_Cols[1];
	}
}

$insWhere=" 1=1 ";

if(sizeof($insSelArr)>0){ $insWhere.=" AND ("; }

if(in_array('primary',$insTypeArr)){
	$insWhere.="insD.type='primary'";
	$prim=1;
}
if(in_array('secondary',$insTypeArr)){
	if($prim==1){
		$insWhere.=" OR insD.type='secondary'";
	}else{
		$insWhere.=" insD.type='secondary'";
	}
}
if(sizeof($insSelArr)>0){ $insWhere.=")"; }


if(sizeof($insSelArr)>0){
	foreach($insSelArr as $val){
		$insSel.= "insD.".$val.", ";
	}
	$insSel=substr($insSel,0,strlen($insSel)-2);
}


if(count($arr_columns)==0){
	$str_columns = "pd.id AS pid, lname, fname, DOB, ss, sex, CONCAT(street,', ',city,' ',state,', ',postal_code) AS ptFullAddress, phone_home, email";
}

//ALL PATIENT STATUS
$arr_status_options = core_pt_status_list();
$pt_status_all = '';
for($i=0; $i<count($arr_status_options); $i++){
	$pt_status_all .= $arr_status_options[$i]['pt_status_name']."','";
}
if($pt_status_all != ""){$pt_status_all = substr($pt_status_all,0,-3);}
//------------------

if(sizeof($_REQUEST['ptstatus'])>0){
	$pt_status = implode("','", $_REQUEST['ptstatus']);
}
if($pt_status == ""){
	$pt_status = $pt_status_all;
}

$sch_date = isset($_REQUEST['sch_date']) ? intval($_REQUEST['sch_date']) : 0;
$from_date = getDateFormatDB($_REQUEST['from_date']);
$to_date = getDateFormatDB($_REQUEST['to_date']);

//pre($providerNameArr);
/* pre($fac_id_arr);
pre($providerNameArr); */

if($str_columns==''){
	$str_columns = implode(", ",$arr_columns);
}
$comma = '';
if(count($arr_app_columns)>0){
 $comma = ',';	
}

if($sch_date==1 && count($_REQUEST['showApptCols'])==0){/*--IF SCHEDULE DATE CHECKBOX IS CHECKED--*/
	$query_schWise = " JOIN schedule_appointments sa ON(pd.id = sa.sa_patient_id AND (sa_app_start_date BETWEEN '".$from_date."' AND '".$to_date."'))";
	$cols_schWise = ",DATE_FORMAT(sa_app_start_date, '".get_sql_date_format()."') as AppDate, 
					 TIME_FORMAT(sa_app_starttime, '%h:%i %p') as AppTime, sa_comments as saAppCom";
	$arr_headers[] = 'Appt. Date';
	$arr_headers[] = 'Appt. Time';	
	$arr_headers[] = 'Appt. Comments';
}elseif($sch_date=="" && count($_REQUEST['showApptCols'])>0){/*--IF SCHEDULE DATE CHECKBOX IS CHECKED--*/
	$query_schWise = " JOIN schedule_appointments sa ON pd.id = sa.sa_patient_id";
	$appt_columns = implode(", ",$arr_app_columns);
	$cols_schWise = ", sa.id as appID $comma $appt_columns";
	if(empty($arr_app_headers)== false){
		$arr_headers = array_merge($arr_headers, $arr_app_headers);
	}
}elseif($sch_date==1 && count($_REQUEST['showApptCols'])>0){/*--IF SCHEDULE DATE CHECKBOX IS CHECKED--*/
	$query_schWise = " JOIN schedule_appointments sa ON(pd.id = sa.sa_patient_id AND (sa_app_start_date BETWEEN '".$from_date."' AND '".$to_date."'))";
	$appt_columns = implode(", ",$arr_app_columns);
	$cols_schWise = ", sa.id as appID $comma $appt_columns";
	if(empty($arr_app_headers)== false){
		$arr_headers = array_merge($arr_headers, $arr_app_headers);
	}
}else{
	$query_schWise = '';$cols_schWise = '';
}

	if(sizeof($insSelArr)>0){
		$insTabQry=" LEFT JOIN insurance_case insC ON insC.patient_id=pd.pid 
		LEFT JOIN insurance_data insD ON insD.ins_caseid=insC.ins_caseid ";
	}
//	$resAppts = imw_query($strQry);
	// MAIN QUERY
	$reg_from = getDateFormatDB($_REQUEST['reg_from']);
	$reg_to = getDateFormatDB($_REQUEST['reg_to']);
	
	$query = "SELECT pd.id as patID, ".$str_columns.$cols_schWise." FROM patient_data pd
				 ".$query_schWise." 
				WHERE patientStatus IN('".$pt_status."')";
	
	if($_REQUEST['reg_from']!='' && $_REQUEST['reg_to']=='')
	{
		$query.=" AND DATE_FORMAT(pd.date, '%Y-%m-%d')>='".$reg_from."'";	
	}
	if($_REQUEST['reg_from']=='' && $_REQUEST['reg_to']!='')
	{
		$query.=" AND DATE_FORMAT(pd.date, '%Y-%m-%d')<='".$reg_to."'";	
	}
	if($_REQUEST['reg_from']!='' && $_REQUEST['reg_to']!='')
	{
		$query.=" AND (DATE_FORMAT(pd.date, '%Y-%m-%d') BETWEEN '".$reg_from."' AND '".$reg_to."')";	
	}
	if($_REQUEST['lname_from']!='')
	{
		$query.=" AND pd.lname >= '".$lname_from."'";	
	}
	if($_REQUEST['lname_to']!='')
	{
		$query.=" AND (pd.lname < '".$lname_to."' or (trim(pd.lname) like '".$lname_to."%'))";	
	}
	if($comboFac && count($comboFac)>0){
		$arrPosFac = $arrFac = array();
		foreach($comboFac as $pos_fac){
			$arrTmp = explode("_",$pos_fac);
			//if($arrTmp[0] != 0)
			$arrPosFac[] = $arrTmp[0];
			$arrFac[] = $arrTmp[1];
		}
		if(count($arrPosFac)>0){
			$str_posFac = implode(",",$arrPosFac);
			$query.= " AND pd.default_facility IN($str_posFac) AND pd.default_facility!=0";
		}
	}
	if(empty($str_provider)==false){
		if($sch_date){
			$query.= " AND if(pd.providerID>0 ,pd.providerID IN($str_provider), sa.sa_doctor_id IN($str_provider))";
		}else{
			$query.= " AND pd.providerID IN($str_provider)";
		}
	}
	
	$query.=" ORDER BY trim(lname), trim(fname)";
	$rs=imw_query($query);
	$resAppts = array();
	while($res=imw_fetch_assoc($rs)){
		$resAppts[] = $res;
	}
	$resAppSize = sizeof($resAppts);
	$arrPatIDS = $apppIds =  array();
	for($i=0; $i<=$resAppSize; $i++)
	{	$qry = "SELECT fac.name 
				FROM facility fac 
				JOIN patient_data pd ON fac.fac_prac_code = pd.default_facility 
				WHERE pd.id = '".$resAppts[$i]['patID']."' 
					  AND pd.default_facility!=0";
		if(count($arrFac)>0){			  
			$strFacIds = implode(",", $arrFac);
			$qry .=	" AND fac.id IN (".$strFacIds.")";
		}
		$res = imw_query($qry);
		$arrFacName = array();
		if(imw_num_rows($res)>0){
			while($row = imw_fetch_assoc($res))
			$arrFacName[] = $row['name'];
		}
		$fac_name = (count($arrFacName)>0)?implode(",    ",$arrFacName) : "";
		if($resAppts[$i]['patID']!=''){
			$arrPatIDS[]= $resAppts[$i]['patID'];
			$arrMainRes[$resAppts[$i]['patID']]=$resAppts[$i];
			$arrMainRes[$resAppts[$i]['patID']][] = $fac_name;
		}
		
		if(in_array("preferr_contact", $arr_columns)){
			$pre_contact = $resAppts[$i]['preferr_contact'];
			if($pre_contact == 0){
				$prefer_contact = "Home Phone";
			} else if($pre_contact == 1){
				$prefer_contact = "Biz Phone";				
			}else if($pre_contact == 2){
				$prefer_contact = "Cell Phone";				
			}
			if($prefer_contact){
				$arrMainRes[$resAppts[$i]['patID']]['preferr_contact']=$prefer_contact;
			}
		}
		
		if(in_array("sa.sa_facility_id", $arr_app_columns)){	
		$facName = $fac_arr[$resAppts[$i]['sa_facility_id']];
			if($facName){
				$arrMainRes[$resAppts[$i]['patID']]['sa_facility_id']=$facName;
			}
		}
		if(in_array("sa.sa_doctor_id", $arr_app_columns)){
		$appDocName = $providerNameArr[$resAppts[$i]['sa_doctor_id']];
			if($appDocName){
				$arrMainRes[$resAppts[$i]['patID']]['sa_doctor_id']=$appDocName;
			}
		}	
		
		if(in_array("DOB", $arr_columns)){	
			$pt_DOB = date_create($resAppts[$i]['DOB']);
			$pt_DOB = date_format($pt_DOB,"m/d/Y");
			if($pt_DOB){
				$arrMainRes[$resAppts[$i]['patID']]['DOB']=$pt_DOB;
			}
		}

		if(in_array("sa.sa_app_start_date", $arr_app_columns)){		
			$appt_start = date_create($resAppts[$i]['sa_app_start_date']);
			$appt_start = date_format($appt_start,"m/d/Y");
			if($appt_start){
				$arrMainRes[$resAppts[$i]['patID']]['sa_app_start_date']=$appt_start;
			}
		}
	
		if(in_array("sa.sa_app_time", $arr_app_columns)){	
			$appt_start_time = date_create($resAppts[$i]['sa_app_time']);
			$appt_start_time = date_format($appt_start_time,"m/d/Y");
			if($appt_start_time){
				$arrMainRes[$resAppts[$i]['patID']]['sa_app_time']=$appt_start_time;
			}
		}
		
		if(in_array("sa.procedureid", $arr_app_columns)){	
			$priProc = $proc_options_arr[$resAppts[$i]['procedureid']];
			if($priProc){
				$arrMainRes[$resAppts[$i]['patID']]['procedureid']=$priProc;
			}
		}
		
		if(in_array("sa.sec_procedureid", $arr_app_columns)){
			$secProc = $proc_options_arr[$resAppts[$i]['sec_procedureid']];
			if($secProc){
				$arrMainRes[$resAppts[$i]['patID']]['sec_procedureid']=$secProc;
			}
		}
		
		if(in_array("sa.tertiary_procedureid", $arr_app_columns)){	
			$terProc = $proc_options_arr[$resAppts[$i]['tertiary_procedureid']];
			if($terProc){
				$arrMainRes[$resAppts[$i]['patID']]['tertiary_procedureid']=$terProc;
			}
		}
		
		if($resAppts[$i]['appID']){
			$apppIds[]= $resAppts[$i]['appID'];
		}
		
		unset($arrMainRes[$resAppts[$i]['']]);
		unset($arrMainRes[$resAppts[$i]['patID']]['0']);
		unset($arrMainRes[$resAppts[$i]['patID']]['patID']);
		unset($arrMainRes[$resAppts[$i]['patID']]['appID']);
	}
	
	$strApptIds=implode(',', $apppIds);
	$prv_columns = implode(", ",$arr_prv_columns);
	if(empty($arr_prv_headers)==false){
		$arr_headers = array_merge($arr_headers, $arr_prv_headers);
	}
	
	$qry="SELECT prv.id, prv.sch_id, prv.patient_id, $prv_columns 
	FROM previous_status prv WHERE prv.sch_id IN(".$strApptIds.") ORDER BY prv.id DESC";
	$rs = imw_query($qry);
	while($res= imw_fetch_assoc($rs)){
		$prevStatusInfo[$res['sch_id']]=$res;
		if(in_array("prv.status_date", $arr_prv_columns)){
			$status_date = date_create($res['status_date']);
			$status_date = date_format($status_date,"m/d/Y");
			if($status_date){
				$arrMainRes[$res['patient_id']]['status_date']=$status_date;
			}
		}
	
		if(in_array("prv.oldMadeBy", $arr_prv_columns)){
			$UserName = $arrAllUsersUName[$res['oldMadeBy']];
			$madeBy = $providerNameArr[$UserName];
			if($madeBy){
				$arrMainRes[$res['patient_id']]['oldMadeBy']=$madeBy;
			} 
		}
		
		$status[$res['sch_id']][] = $res['appointmentStatus'];
		$finalStatus[$res['patient_id']] = $status[$res['sch_id']][0];
		if(in_array("prv.status AS appointmentStatus", $arr_prv_columns)){	
			if($finalStatus){
				$arrMainRes[$res['patient_id']]['appointmentStatus'] = $appt_opts_arr[$finalStatus[$res['patient_id']]];
			}else {
				$arrMainRes[$res['patient_id']]['appointmentStatus'] = "";
			}
		}
		
		if(in_array("prv.status_time AS check_in_time", $arr_prv_columns) || in_array("prv.status_time AS check_out_time", $arr_prv_columns)){
			$chk_time=array();
			
			$create_check_in_time = date(' h:i A', strtotime($res['check_in_time']));
			$createcheck_out_time = date(' h:i A', strtotime($res['check_out_time']));
			
			if($res['appointmentStatus'] == 13 && !isset($chk_time[13]) && $chk_time[13]==''){
				$chk_time[$res['appointmentStatus']]=$create_check_in_time;
			}elseif($res['appointmentStatus'] == 11 && !isset($chk_time[11]) && $chk_time[11]==''){
				$chk_time[$res['appointmentStatus']]=$createcheck_out_time;
			} 
			if($arrMainRes[$res['patient_id']]['check_in_time']=='')
				$arrMainRes[$res['patient_id']]['check_in_time'] = $chk_time[13];
			if($arrMainRes[$res['patient_id']]['check_out_time']=='')
				$arrMainRes[$res['patient_id']]['check_out_time'] = $chk_time[11];
		}
	}
	//pre($arrMainRes);
	//pre($appt_opts_arr);
	//appt_opts_arr
	$strPatIDS = implode(",",$arrPatIDS);
	$resAppTEMP=array();
	//INSURANCE QUERY
	if(sizeof($insSelArr)>0){ 
		$insData = array();
		$qry = "Select insC.patient_id, insD.type, ".$insSel." from insurance_case insC 
					LEFT JOIN insurance_case_types insT ON insT.case_id = insC.ins_case_type 
					LEFT JOIN insurance_data insD ON insD.ins_caseid=insC.ins_caseid WHERE ".$insWhere." AND insT.normal='1' 
					AND insC.patient_id IN(".$strPatIDS.")";
		$rs=imw_query($qry);
		$insRes = array();
		while($res=imw_fetch_assoc($rs)){
			$insRes[] = $res;
		}
		$insResSize = sizeof($insRes);
		for($i=0; $i<=$insResSize; $i++)
		{
			$pid = $insRes[$i]['patient_id'];
			if($insRes[$i]['type'] == 'primary'){
				$insData[$pid]['primary']= $insRes[$i];
			}
			if($insRes[$i]['type'] == 'secondary'){
				$insData[$pid]['secondary']= $insRes[$i];
			}
		}
	
	
		for($i=0;$i< $resAppSize; $i++)	{
			$arr_elem=array();
			if(isset($insData[$resAppts[$i]["patID"]]['primary'])){// echo "p";
				foreach($insData[$resAppts[$i]["patID"]]['primary'] as $key=>$val){
					if($key!='patient_id' && $key!='type'){
						if(in_array($key, $ins_matchPri)){
							if($key=='provider'){
								$arr_elem[$key] = $insComp[$val];	
							}else{
								$arr_elem[$key] = $val;
							}
						}
					}
				}
			}
			if(isset($insData[$resAppts[$i]["patID"]]['secondary'])){//echo "s";
				foreach($insData[$resAppts[$i]["patID"]]['secondary'] as $key=>$val2){
					if($key!='patient_id' && $key!='type'){
						if(in_array($key, $ins_matchSec)){
							if($key=='provider'){
								$arr_elem['sec_'.$key] = $insComp[$val2];	
							}else{
								$arr_elem['sec_'.$key] = $val2;
							}
						}
					}
				}
			}
			$resAppTEMP[$resAppts[$i]["patID"]] = array_merge($arrMainRes[$resAppts[$i]["patID"]], $arr_elem);
		}

	}else{
			$resAppTEMP = $arrMainRes;
	}
	
	//pre($resAppTEMP);
	
	/*--DELETEING OLD FILE IF REPORT IS GENERATED REPETEDELY IN SAME SESSION--*/

	$fp = fopen ($csv_file_name, 'a+');
	if(sizeof($resAppTEMP)>0){

		if(sizeof($insSelArr)>0){ 
			$arr_headers=array_merge($arr_headers,$ins_headers);
		}
		fputcsv($fp,$arr_headers, ",","\"");
		$fp = fopen ($csv_file_name, 'a+');
		
		foreach($resAppTEMP as $val){ 
			fputcsv($fp,$val, ",","\"");	
		}
		
		if($callFrom!='scheduled'){
			$show_msg= "<div class=\"text-center alert alert-info\"><span style='font-family:verdana;font-size:12px;font-weight:bold; padding-left:100px;'> Patient's Details have been exported successfully.</span>";
			$show_msg.="<b> <a href='file_save_export.php?fn=".$csv_file_name."' style='font-family:verdana;font-size:12px;font-weight:bold;color:#FF0000;' > Click here to download file</a></b></div>";
			echo $show_msg; 
		}
		
	}else{
		$totrec=0;
		if($callFrom!='scheduled'){
			$show_msg= '<div class="text-center alert alert-info">No record exists.</div>';
			echo $show_msg;
		}
	}
	fclose ($fp);
?>
