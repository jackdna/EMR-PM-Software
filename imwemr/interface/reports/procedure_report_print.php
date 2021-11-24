<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
$noRecord = false;
$global_date_format = phpDateFormat();
$report_generator_name = "";
if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
	$op_name_arr = preg_split("/, /",$_SESSION["authProviderName"]);
	$report_generator_name = $op_name_arr[1][0];
	$report_generator_name .= $op_name_arr[0][0];
}
if($_POST['form_submitted']<>""){
		
	$curDate = get_date_format(date('Y-m-d'))." ".date('h:i A');
	if($global_date_format == "d-m-Y")
	{
		list($d,$m,$y) = explode('-',$_POST['rep_date_dy']);
	}
	else
	{
		list($m,$d,$y) = explode('-',$_POST['rep_date_dy']);
	}
	//list($m,$d,$y) = explode('-',$_POST['rep_date_dy']);
	$_POST['rep_date_dy'] = $y.'-'.$m.'-'.$d.'|'.$y.'-'.$m.'-'.$d;
	
	switch($_POST['rep_type']){
		case "1":
			$dts_arr=explode("|",$_POST['rep_date_dy']);	
			$rep_type="Date";  	  
			break;    
		case "2":
			$dts_arr=explode("|",$_POST['rep_date_wk']);	  	  
			$rep_type="Weekly";
			break;
		case "3":
			$dts_arr=explode("|",$_POST['rep_date_mn']);	  	  
			$rep_type="Monthly";
			break;	
		case "4":
			$dts_arr=explode("|",$_POST['rep_date_qa']);
			$rep_type="Quarterly";
			break;	
		case "5":
			$dts_arr=explode("|",$_POST['rep_date_ya']);	  	  
			$rep_type="Yearly";
			break;
		case "date_range":
			$dts_arr[0]=getDateFormatDB($_POST['date_from']);
			$dts_arr[1]=getDateFormatDB($_POST['date_to']);
			$rep_type="Date Range";
			break;
		default : break;
	}
	
	$dispFrom= date($global_date_format, strtotime($dts_arr[0]));
	$dispTo=date($global_date_format, strtotime($dts_arr[1]));
	
	$dts_st = getDateFormatDB($dts_arr[0]);
	$dts_en = getDateFormatDB($dts_arr[1]);


	//ALL PROCEDURES
	$qry = "Select id, proc from slot_procedures where proc != ''";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$arrAllProcedures[$res['id']]=$res['proc'];
	}unset($rs);
	
	//ALL FACILITES
	$arrAllFacilites=array();
	$qry = "Select id, name from facility";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$arrAllFacilites[$res['id']]=$res['name'];
	}unset($rs);

	//GET ALL PROVIDER NAME
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
	}

	//GET ALL INSURANCE COMAPNIES
	$qry="Select id as insCompId,in_house_code as insCompINHouseCode,name as insCompName FROM insurance_companies";
	$rs=imw_query($qry);
	$arrAllInsCompanies=array();
	while($res=imw_fetch_assoc($rs)){
		$id = $res['insCompId'];
		$insName = $res['insCompINHouseCode'];
		$arrAllInsCompanies[$id] = $insName;
	}
	//$arr_summary = sr_get_procedure_report_summary($dts_st, $dts_en, $_REQUEST['rep_proc'],$_REQUEST['facility_name'],$_REQUEST['phyId'], $_REQUEST['viewBy']);
	
	$proc_ids_str=implode(',',$_REQUEST['rep_proc']);
	if($proc_ids_str){
		$proc_qry=" AND sa.procedureid in ('".str_replace(",","','",$proc_ids_str)."')";
	}
	$pos_facility_id = implode(',',$_REQUEST['facility_name']);	
	$provider_id = implode(',',$_REQUEST['phyId']);
	
	if($pos_facility_id){
		$fac_whr=" and sa.sa_facility_id IN (".$pos_facility_id.")";
	}
	if($provider_id){
		$prov_whr=" and sa.sa_doctor_id IN (".$provider_id.")";
	}

	$qry="SELECT sa.id, sa.sa_patient_app_status_id, sa.procedureid, sa.sa_facility_id, 
	COUNT(sa.procedureid) AS cprocs, COUNT(sa.sa_patient_app_status_id) as stsCount, sp.proc, sp.acronym,
	u.fname, u.lname , sa.sa_doctor_id
	FROM schedule_appointments sa 
	LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid 
	LEFT JOIN users u ON u.id = sa.sa_doctor_id 
	LEFT JOIN facility ON facility.id= sa.sa_facility_id 
	WHERE (sa.sa_app_start_date BETWEEN '".$dts_st."' AND '".$dts_en."')  
	and sa.sch_template_id != '0'
	$proc_qry
	$fac_whr
	$prov_whr";
	if($display_cancelled=='1'){
		$qry.=" and sa.sa_patient_app_status_id NOT IN (203)";
	}else{
		$qry.=" and sa.sa_patient_app_status_id NOT IN (203,18)";
	}
	
	if($_REQUEST['viewBy']=='physician'){
		$qry.=" GROUP BY sa.procedureid, sa.sa_patient_app_status_id, sa.sa_doctor_id ORDER BY u.lname, u.fname, sp.proc";
	}else{
		$qry.=" GROUP BY sa.procedureid, sa.sa_patient_app_status_id, sa.sa_facility_id ORDER BY facility.name, sp.proc";
	}		
	
	$sql = imw_query($qry);
	$result_array = array();	
	while($res = imw_fetch_array($sql)){
		$arr_summary[] = $res;
	}	
	$int_summary = count($arr_summary);
	
	$cssHTML ='
	<style>'.file_get_contents('css/reports_pdf.css').'</style>
	<page backtop="5mm" backbottom="5mm">			
	<page_footer>
		<table style="width:100%">
			<tr>
				<td style="text-align:center;width:100%;">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>';
	$strHTML ='';
	if($int_summary > 0){
		$arrSummary=array();
		$arrProcDetail=array();
		//getting proc count
		for($c = 0; $c < $int_summary; $c++){
			$docId = $arr_summary[$c]['sa_doctor_id'];
			$facId = $arr_summary[$c]['sa_facility_id'];
			$procId = $arr_summary[$c]['procedureid'];

			$grpbyid=$docId;
			$grpname=$arr_summary[$c]['fname'].' '.$arr_summary[$c]['lname'];
			if($viewBy=='facility'){
				$grpbyid=$facId;
				$grpname=$arrAllFacilites[$facId];
			}
			
			$arrProcDetail[$procId] = $arr_summary[$c]['proc'];
			$arrSummaryDoc[$grpbyid] = $grpname;
			$arrSummaryDet[$grpbyid][$procId]['PROC'] = $arr_summary[$c]['proc'];
			$arrSummaryDet[$grpbyid][$procId]['PROC_COUNT']+= $arr_summary[$c]['cprocs'];
			
			if($arr_summary[$c]['sa_patient_app_status_id'] == 202){
				if($arr_summary[$c]['stsCount']>0){
					$arrSummaryDet[$grpbyid][$procId]['RESCHEDULED'] = $arr_summary[$c]['stsCount'];
				}
			}else if($arr_summary[$c]['sa_patient_app_status_id'] == 3){
				if($arr_summary[$c]['stsCount']>0){
					$arrSummaryDet[$grpbyid][$procId]['NOSHOW'] = $arr_summary[$c]['stsCount'];
				}
			}else if($arr_summary[$c]['sa_patient_app_status_id'] == 18){
				if($arr_summary[$c]['stsCount']>0){
					$arrSummaryDet[$grpbyid][$procId]['CANCELLED'] = $arr_summary[$c]['stsCount'];
				}
			}
		}
		
		if($process!="Detail"){
		$arrGrandTot=array();

		//MAKING CSV OUTPUT DATA
		$file_name="procedures_report_".time().".csv";
		$csv_file_name= write_html("", $file_name);
		//CSV FILE NAME
		if(file_exists($csv_file_name)){
			unlink($csv_file_name);
		}
		$fp = fopen ($csv_file_name, 'a+');
	
		$arr=array();
		$arr[]='Procedures Report (Summary)';
		$arr[]='Date Range From '.$dispFrom.' To '.$dispTo;
		$arr[]='Created by '.$report_generator_name.'  on  '.$curDate;
		fputcsv($fp,$arr, ",","\"");
	
		$arr=array();
		$arr[]="Procedure Name (Primary)";
		$arr[]="Total Count";
		$arr[]="No Shows";
		$arr[]="Re-Scheduled";
		if($display_cancelled=='1'){
			$arr[]="Cancelled";
		}
		$arr[]="Actual Seen";
		fputcsv($fp,$arr, ",","\"");
		
		$colspan=5;
		$col_width=210;
		$td_lbl_cancelled='';
		if($display_cancelled=='1'){
			$td_lbl_cancelled='<td align="left" width="175" class="text_b_w">Cancelled</td>';
			$colspan=6;
			$col_width=175;
		}
		
		$strHTML .='<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
						<tr class="rpt_headers">
							<td class="rptbx1" style="width:350px;" align="left">
								Procedures Report (Summary)
							</td>
							<td class="rptbx2" style="width:350px;" align="left">
								Date Range From '.$dispFrom.' To '.$dispTo.'
							</td>
							<td class="rptbx3" style="width:350px;" align="left">
								Created by '.$report_generator_name.'  on  '.$curDate.'
							</td>	
					</tr>
				</table>
				<table class="rpt rpt_table rpt_table-bordered" width="100%" bgcolor="#FFF3E8">		
					<tr height="20">
						<td align="left" width="'.$col_width.'" class="text_b_w">Procedure Name (Primary)</td>				
						<td align="left" width="'.$col_width.'" class="text_b_w">Total Count</td>
						<td align="left" width="'.$col_width.'" class="text_b_w">No Shows</td>
						<td align="left" width="'.$col_width.'" class="text_b_w">Re-Scheduled</td>
						'.$td_lbl_cancelled.'
						<td align="left" width="'.$col_width.'" class="text_b_w">Actual Seen</td>
					</tr>';
		$arrDocVals  = array_keys($arrSummaryDoc);
		//for($s = 0; $s < count($arrDocVals); $s++){
		foreach($arrSummaryDet as $grp_id => $arrGrpData){
			$arrSubTot=array();
			
			$groupTitle='Physician';
			$grpname=$providerNameArr[$grp_id];
			if($viewBy=='facility'){
				$grpname=$arrAllFacilites[$grp_id];
				$groupTitle='Facility';
			}

			$strHTML .='<tr>
				<td align="left" class="text_b_w" colspan="'.$colspan.'">&nbsp;&nbsp;&nbsp;'.$groupTitle.' :'.$grpname.'</td>				
			</tr>';	  

			foreach($arrGrpData as $procId => $dataDetail){	
				
				$tot_cont=$dataDetail['PROC_COUNT'] - ($dataDetail['RESCHEDULED'] + $dataDetail['NOSHOW'] + $dataDetail['CANCELLED']);
				$noshow = ($dataDetail['NOSHOW']=='') ? '0' : $dataDetail['NOSHOW']; 
				$rescheduled = ($dataDetail['RESCHEDULED']=='') ? '0' : $dataDetail['RESCHEDULED']; 
				$cancelled = ($dataDetail['CANCELLED']=='') ? '0' : $dataDetail['CANCELLED']; 
				
				$arrSubTot['PROC_COUNT']+= $dataDetail['PROC_COUNT'];
				$arrSubTot['NOSHOW']+= $noshow;
				$arrSubTot['RESCHEDULED']+= $rescheduled;
				$arrSubTot['CANCELLED']+= $cancelled;
				$arrSubTot['TOT_COUNT']+= $tot_cont;
				
				$td_val_cancelled='';
				if($display_cancelled=='1'){
					$td_val_cancelled='<td align="left" class="text_12" bgcolor="#FFFFFF" style="width:175px">&nbsp;&nbsp;&nbsp;'.$cancelled.'</td>';
				}
				
				$strHTML .='<tr>
					<td align="left" class="text_12" bgcolor="#FFFFFF" style="width:175px">&nbsp;&nbsp;&nbsp;'.$dataDetail['PROC'].'</td>				
					<td align="left" class="text_12" bgcolor="#FFFFFF" style="width:175px">&nbsp;&nbsp;&nbsp;'.$dataDetail['PROC_COUNT'].'</td>
					<td align="left" class="text_12" bgcolor="#FFFFFF" style="width:175px">&nbsp;&nbsp;&nbsp;'.$noshow.'</td>
					<td align="left" class="text_12" bgcolor="#FFFFFF" style="width:175px">&nbsp;&nbsp;&nbsp;'.$rescheduled.'</td>
					'.$td_val_cancelled.'
					<td align="left" class="text_12" bgcolor="#FFFFFF" style="width:175px">&nbsp;&nbsp;&nbsp;'.$tot_cont.'</td>
				</tr>';	 

				//FOR CSV
				$arr=array();
				$arr[]=$dataDetail['PROC'];
				$arr[]=$dataDetail['PROC_COUNT'];;
				$arr[]=$noshow;
				$arr[]=$rescheduled;
				if($display_cancelled=='1'){
					$arr[]=$cancelled;
				}
				$arr[]=$tot_cont;
				fputcsv($fp,$arr, ",","\"");
				 
			}

			
			$arrGrandTot['PROC_COUNT']+= $arrSubTot['PROC_COUNT'];
			$arrGrandTot['NOSHOW']+= $arrSubTot['NOSHOW'];
			$arrGrandTot['RESCHEDULED']+= $arrSubTot['RESCHEDULED'];
			$arrGrandTot['CANCELLED']+= $arrSubTot['CANCELLED'];
			$arrGrandTot['TOT_COUNT']+= $arrSubTot['TOT_COUNT'];			
			
			$td_val_cancelled='';
			if($display_cancelled=='1'){
				$td_val_cancelled='<td align="left" class="text_12b" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$arrSubTot['CANCELLED'].'</td>';
			}
			
			//SUB TOTAL
			$strHTML .='
			<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>	
			<tr>
				<td align="right" class="text_12b" bgcolor="#FFFFFF">Sub Total: </td>				
				<td align="left" class="text_12b" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$arrSubTot['PROC_COUNT'].'</td>
				<td align="left" class="text_12b" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$arrSubTot['NOSHOW'].'</td>
				<td align="left" class="text_12b" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$arrSubTot['RESCHEDULED'].'</td>
				'.$td_val_cancelled.'
				<td align="left" class="text_12b" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$arrSubTot['TOT_COUNT'].'</td>
			</tr>
			<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';	  
			
		}
		
		//GRAND TOTAL
		$td_val_cancelled='';
		if($display_cancelled=='1'){
			$td_val_cancelled='<td align="left" class="text_12b" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$arrGrandTot['CANCELLED'].'</td>';
		}
		$strHTML .='
		<tr>
			<td align="right" class="text_12b" bgcolor="#FFFFFF">Grand Total: </td>				
			<td align="left" class="text_12b" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$arrGrandTot['PROC_COUNT'].'</td>
			<td align="left" class="text_12b" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$arrGrandTot['NOSHOW'].'</td>
			<td align="left" class="text_12b" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$arrGrandTot['RESCHEDULED'].'</td>
			'.$td_val_cancelled.'
			<td align="left" class="text_12b" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;'.$arrGrandTot['TOT_COUNT'].'</td>
		</tr>
		<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';	  

		//FOR CSV
		$arr=array();
		$arr[]="Grand Total:";
		$arr[]=$arrGrandTot['PROC_COUNT'];;
		$arr[]=$arrGrandTot['NOSHOW'];
		$arr[]=$arrGrandTot['RESCHEDULED'];
		if($display_cancelled=='1'){
			$arr[]=$arrGrandTot['CANCELLED'];
		}
		$arr[]=$arrGrandTot['TOT_COUNT'];
		fputcsv($fp,$arr, ",","\"");
		
		fclose($fp);
		$strHTML .='</table></page>';

		}else{
			
		// DETAIL	
		$strHTML ='
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px" >
						<tr class="rpt_headers">
						<td class="rptbx1" style="width:350px;" align="left">
							Procedures Report (Detail)
						</td>
						<td class="rptbx2" style="width:350px;" align="left">
							Date Range From '.$dispFrom.' To '.$dispTo.'
						</td>
						<td class="rptbx3" style="width:350px;" align="left">
							Created by '.$report_generator_name.'  on  '.$curDate.'
						</td>	
					</tr>
				</table>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050px">
		';
		$arrProcDetailKeys = array_keys($arrProcDetail);
		
		for($d = 0; $d < count($arrProcDetailKeys); $d++){
			$procId = $arrProcDetailKeys[$d];
			$procName = $arrProcDetail[$procId];
			
			if(!$noshow_proc[$arr_summary[$d]["procedureid"]]){ 
				$noshow_proc[$arr_summary[$d]["procedureid"]] = 0; 
			}
			if(!$reschedule_proc[$arr_summary[$d]["procedureid"]]){ 
				$reschedule_proc[$arr_summary[$d]["procedureid"]] = 0; 
			}
			if(!$cancel_proc[$arr_summary[$d]["procedureid"]]){ 
				$cancel_proc[$arr_summary[$d]["procedureid"]] = 0; 
			}
					$strHTML .='
					<tr>
						<td colspan="10" align="left" class="text_b_w">Procedure (Primary): '.$procName.'</td>				
					</tr>	
					<tr>
						<td align="left" class="text_b_w">Patient Name</td>
						<td align="left" class="text_b_w">Date of Birth</td>
						<td align="left" class="text_b_w">Date Sched.</td>
						<td align="left" class="text_b_w">Sec. Proc.</td>
						<td align="left" class="text_b_w">Ter. Proc.</td>
						<td align="left" class="text_b_w">Status</td>
						<td align="left" class="text_b_w">Date&nbsp;Re-Sched.</td>
						<td align="left" class="text_b_w">Co-Pay </td>
						<td align="left" class="text_b_w">Ref. Physician</td>
						<td align="left" class="text_b_w">Insurance</td>
					</tr>';
			$arr_detail = array();
					
			$pos_facility_id = implode(',',$_REQUEST['facility_name']);	
			$provider_id = implode(',',$_REQUEST['phyId']);
			
			if($pos_facility_id){
				$fac_whr=" and sa.sa_facility_id IN (".$pos_facility_id.")";
			}
			if($provider_id){
				$prov_whr=" and sa.sa_doctor_id IN (".$provider_id.")";
			}
			if($display_cancelled=='1'){
				$status_whr=" and sa.sa_patient_app_status_id NOT IN (203)";
			}else{
				$status_whr=" and sa.sa_patient_app_status_id NOT IN (203,18)";
			}
			
			$sql = imw_query("SELECT sa.case_type_id,sa.sa_patient_app_status_id,
			sa.sa_patient_id, sa.sa_patient_name, sa.procedureid, sa.sec_procedureid, sa.tertiary_procedureid,
			sa.sa_app_start_date, DATE_FORMAT(sa.sa_app_starttime,'%l:%i %p') as sa_app_starttime, u.fname, u.lname, ss.status_name,
			sa.id, sa.sa_doctor_id, pd.DOB  
			FROM schedule_appointments sa 
			LEFT JOIN users u ON u.id = sa.sa_doctor_id 
			LEFT JOIN schedule_status ss ON ss.id = sa.sa_patient_app_status_id 
			LEFT JOIN patient_data pd ON pd.id=sa.sa_patient_id 
			WHERE (sa.sa_app_start_date BETWEEN '".$dts_st."' AND '".$dts_en."') 
			and sa.sch_template_id != 0 
			AND sa.procedureid = '".$procId."' 
			$fac_whr
			$prov_whr
			$status_whr
			ORDER BY sa.sa_doctor_id
			");
			while($res = imw_fetch_array($sql)){
				$arr_detail[] = $res;
				$arr_case_ids[$res['case_type_id']]=$res['case_type_id'];
			}

			//GETTING INSURANCE COMPANY NAMES
			$arr_case_info=array();
			if(sizeof($arr_case_ids)>0){
				$str_case_ids=implode(',', $arr_case_ids);
				$qry="Select ins_caseid, provider FROM insurance_data WHERE ins_caseid IN($str_case_ids) AND LOWER(type)='primary'";
				$rs=imw_query($qry);
				while($res = imw_fetch_assoc($rs)){
					$arr_case_info[$res['ins_caseid']]= $arrAllInsCompanies[$res['provider']];
				}				
			}				
			
			$int_detail = count($arr_detail);
			$countReschedule= $countNoShow = $countCancel =0;
			$tot_cont=0;
			$docId=$oldDocId = 0;
			for($dtl = 0; $dtl < $int_detail; $dtl++){
				$tot_cont++;
				$docId = $arr_detail[$dtl]['sa_doctor_id'];
				$phyName = $arr_detail[$dtl]['fname'].' '.$arr_detail[$dtl]['lname'];
				$ef_dy = get_date_format(date('Y-m-d',strtotime($arr_detail[$dtl]['sa_app_start_date'])));
				$ef_time =  $arr_detail[$dtl]['sa_app_starttime'];
				$dob= get_date_format(date('Y-m-d',strtotime($arr_detail[$dtl]['DOB'])));
				
				//getting date resch
				$arr_status_date = array();
				$status_name="";
				if($arr_detail[$dtl]['sa_patient_app_status_id'] == 202){
					$arr_status_date = sr_get_appt_prev_status_time($arr_detail[$dtl]['id'], $arr_detail[$dtl]['sa_patient_app_status_id'],'');
					$status_name="Reschedule";
					$countReschedule++;
				}
				
				if($arr_detail[$dtl]['sa_patient_app_status_id'] == 3){
					$status_name="No Show";
					$countNoShow++;
				}
				if($arr_detail[$dtl]['sa_patient_app_status_id'] == 18){
					$status_name="Cancelled";
					$countCancel++;
				}

				//gettting copay
				$int_pt_copay = re_get_pt_copay($arr_detail[$dtl]['sa_patient_id'], $arr_detail[$dtl]['case_type_id']);

				//gettign ref phy
				$arr_pt_ref_phy = sr_get_pt_ref_phy($arr_detail[$dtl]['sa_patient_id']);
				
				if($docId!=$oldDocId){
					$strHTML .='<tr class="text_10" height="20" bgcolor="#FFFFFF">
							<td class="text_b_w" align="left" colspan="10">Physician : '.$phyName.'</td>				
						</tr>';
				}					
				$strHTML.='<tr class="text_10" height="20" bgcolor="#FFFFFF">
						<td class="text_12" style="width:160px;" align="left" bgcolor="#FFFFFF">&nbsp;'.$arr_detail[$dtl]['sa_patient_name'] .' - '.$arr_detail[$dtl]['sa_patient_id'].'</td>
						<td class="text_12" style="width:70px;" align="left" bgcolor="#FFFFFF">&nbsp;'.$dob.'</td>
						<td class="text_12" style="width:110px;" align="left" bgcolor="#FFFFFF">&nbsp;'.$ef_dy.'&nbsp;&nbsp;'.$ef_time.'</td>
						<td class="text_12" style="width:90px;" align="left" bgcolor="#FFFFFF">&nbsp;'.$arrAllProcedures[$arr_detail[$dtl]['sec_procedureid']].'</td>
						<td class="text_12" style="width:90px;" align="left" bgcolor="#FFFFFF">&nbsp;'.$arrAllProcedures[$arr_detail[$dtl]['tertiary_procedureid']].'</td>
						<td class="text_12" style="width:80px;"  align="left" bgcolor="#FFFFFF">&nbsp;'.ucfirst($status_name).'</td>
						<td class="text_12" style="width:70px;"  align="left" bgcolor="#FFFFFF">&nbsp;'.get_date_format($arr_status_date[0]["status_date"],'mm-dd-yyyy').'&nbsp;&nbsp;'.$arr_status_date[0]["appCheckTime"].'</td>
						<td class="text_12" style="width:70px;"  align="left" bgcolor="#FFFFFF">&nbsp;'.numberFormat($int_pt_copay,2,'yes','','yes').'</td>
						<td class="text_12" style="width:100px;" align="left" bgcolor="#FFFFFF">&nbsp;'.$arr_pt_ref_phy["primary_care"].'</td>
						<td class="text_12" style="width:100px;" align="left" bgcolor="#FFFFFF">&nbsp;'.$arr_case_info[$arr_detail[$dtl]['case_type_id']].'</td>
					</tr>';
				$oldDocId=$docId;					
			}
			$tot_detail_cont="";
			$tot_detail_cont = $arr_summary[$d]['cprocs'] - ($reschedule_proc[$arr_summary[$d]["procedureid"]] + $noshow_proc[$arr_summary[$d]["procedureid"]] + $cancel_proc[$arr_summary[$d]["procedureid"]]);
			
			$cancelled_val='';
			if($display_cancelled=='1'){
				$cancelled_val=',&nbsp;Cancelled : '.$countCancel;
			}
			
			$strHTML .='									
					<tr class="text_10" height="20">
						<td colspan="10" align="left" class="text_12b" bgcolor="#FFFFFF">
							Total Patient: '.$tot_cont.',&nbsp;
							Re-Sched : '.$countReschedule.'
							,&nbsp;No Show : '.$countNoShow.
							$cancelled_val.'
							</td>				
					</tr>
					<tr class="text_10" height="20">
						<td colspan="10" align="left" class="text_12b" bgcolor="#FFFFFF">Actual Patient Seen : '.$tot_detail_cont.'</td>				
					</tr>';
			}
			$strHTML .='</table></page>';
		}
	}
}

if(trim($strHTML) != "" && $int_summary>0){
	$PdfText = $cssHTML.$strHTML;
	$printFile = 1;
	$file_location = write_html($PdfText);
	$styleHTML = '<style>' . file_get_contents('css/reports_html.css') . '</style>';
	echo $csv_file_data = $styleHTML . $strHTML;
} else{
	echo '<div class="text-center alert alert-info">No record found.</div>';
}
?>
