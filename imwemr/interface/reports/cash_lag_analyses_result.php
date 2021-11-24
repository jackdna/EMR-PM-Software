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

/*
FILE : ProductivityResult.php
PURPOSE : PRODUCTIVITY RESULT FOR PHYSICIAN 
ACCESS TYPE : DIRECT
*/

ini_set("memory_limit","3072M");
set_time_limit (0);

$dateFormat= get_sql_date_format();
$curDate = date($phpDateFormat.' h:i A');

$FCName= $_SESSION['authId'];

$printFile = true;
if($_POST['form_submitted']){

	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	$arrDateRange= $CLSCommonFunction->changeDateSelection();

	if($dayReport=='Daily'){
		$Start_date = $End_date= date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$Start_date = $arrDateRange['WEEK_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$Start_date = $arrDateRange['MONTH_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$Start_date = $arrDateRange['QUARTER_DATE_START'];
		$End_date = $arrDateRange['QUARTER_DATE_END'];
	}

	
	$printFile = false;
	$Sdate = $Start_date;
	$Edate = $End_date;
	$writte_off_arr = array();
	$arrInsOfEnc=array();

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($sc_name)<=0 && isPosFacGroupEnabled()){
		$sc_name = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($sc_name)<=0){
			$sc_name[0]='NULL';
		}
	}
	
	//MAKE COMMA SEPEARATED OF ALL SEARCH CRITERIA
	$grp_id= (sizeof($grp_id)>0) ? implode(',',$grp_id) : '';
	$facility_name= (sizeof($sc_name)>0) ? implode(',',$sc_name) : '';
	$Physician= (sizeof($Physician)>0) ? implode(',',$Physician) : '';
	$credit_physician= (sizeof($crediting_provider)>0) ? implode(',',$crediting_provider) : '';
	//---------------------------------------

	//--- CHANGE DATE FORMAT -------
	if($Start_date != '' && $End_date != ''){
		$Start_date = getDateFormatDB($Start_date);
		$End_date = getDateFormatDB($End_date);	
	}

	
	//--- GET GROUP NAME ---
	$group_name = "All Selected";
	if(empty($grp_id) === false){
		$group_query = "select name from groups_new where gro_id = '$grp_id'";
		$groupQryRs = imw_query($group_query);		
		$groupQryRes = imw_fetch_assoc($groupQryRs);
		if(count($groupQryRes)>0){
			$group_name = $groupQryRes[0]['name'];
		}
	}
	
	// -- GET ALL POS-FACILITIES
	$arrAllFacilities=array();
	$arrAllFacilities[0] = 'No Facility';
	$qry = "select pos_facilityies_tbl.facilityPracCode as name,
		pos_facilityies_tbl.pos_facility_id as id,
		pos_tbl.pos_prac_code
		from pos_facilityies_tbl
		left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
		order by pos_facilityies_tbl.headquarter desc,
		pos_facilityies_tbl.facilityPracCode";
	$qryRs = imw_query($qry);
	while($qryRes  =imw_fetch_assoc($qryRs)){
		$id = $qryRes['id'];
		$name = $qryRes['name'];
		$pos_prac_code = $qryRes['pos_prac_code'];
		$arrAllFacilities[$id] = $name.' - '.$pos_prac_code;
	}						
	// ------------------------------
		
	
	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
		
		// two character array
		$operatorInitial = substr($providerResArr['fname'],0,1);
		$operatorInitial .= substr($providerResArr['lname'],0,1);
		$userNameTwoCharArr[$id] = strtoupper($operatorInitial);
	}

	
	//GETTING MAIN DATA
	$mainResArr = array();
	//--- GET ALL CHARGES ----
	$qry = "Select main.charge_list_id, main.encounter_id, main.charge_list_detail_id, 
	(main.charges * main.units) as totalAmt, main.proc_balance, main.over_payment,
	DATE_FORMAT(main.date_of_service, '%b-%Y') as 'dos' 
	FROM report_enc_detail main 
	WHERE (main.date_of_service between '$Start_date' and '$End_date') AND main.del_status='0'";
	
	if(empty($facility_name) == false){
		$qry.= " and main.facility_id IN ($facility_name)";	
	}
	if(empty($grp_id) == false){
		$qry.= " and main.gro_id IN ($grp_id)";
	}
	if(empty($Physician) === false){
		$qry.= " and main.primary_provider_id_for_reports IN ($Physician)";
	}
	if(empty($credit_physician) === false){
		$qry.= " and main.sec_prov_id IN ($credit_physician)";
	}
	if($chksamebillingcredittingproviders==1){
		$qry.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
	}	
	$qry .=" ORDER BY main.date_of_service";
	$res=imw_query($qry);

	$main_encounter_id_arr = array();
	$facilityNameArr = array();
	$physician_initial_arr = array();
	$arrPatNoFacility=array();
	$arrMapEncDOS=array();
	$chargeids_for_temp_query='';
	while($rs = imw_fetch_assoc($res)){
		$encounter_id = $rs['encounter_id'];
		$chgDetId = $rs['charge_list_detail_id'];
		
		$mainResultArr[$rs['dos']]+= $rs['totalAmt'];
		$arrBalanceAmt[$rs['dos']]+= $rs['proc_balance']-$rs['over_payment'];
		
		$arrMapEncDOS[$encounter_id]=$rs['dos'];
		$arrChgDetIds[$chgDetId] = $chgDetId;
		$chargeids_for_temp_query.='('.$chgDetId.'),';
	}unset($rs);
	
	if(empty($chargeids_for_temp_query)==false){
		$chargeids_for_temp_query=substr($chargeids_for_temp_query,0, -1);
	}

	//TRANSACTIONS TABLE
	if(sizeof($arrChgDetIds)>0){
		
		//CREATE TEMP TABLE AND INSERT DATA
		$temp_join_part='';
		if(empty($chargeids_for_temp_query)==false){
			$tmp_table="IMWTEMP_reports_cash_lag_analysis_ids_".time().'_'.$_SESSION["authId"];
			imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);
			imw_query("CREATE TEMPORARY TABLE ".$tmp_table." (charge_id INT)");
			imw_query("INSERT INTO $tmp_table (charge_id) VALUES ".$chargeids_for_temp_query);
			$temp_join_part=" INNER JOIN ".$tmp_table." t_tbl ON trans.charge_list_detail_id = t_tbl.charge_id";
		}

		if(empty($temp_join_part)==false){
		
			$qry="Select trans.report_trans_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, 
			trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_qry_type, trans.parent_id,
			date_format(trans.trans_dot,'%Y-%m') as trans_dop, trans.trans_del_operator_id  
			FROM report_enc_trans trans 
			".$temp_join_part."
			WHERE LOWER(trans.trans_type)!='charges'  
			ORDER BY trans.trans_dot, trans.trans_dot_time";
			$rs=imw_query($qry);
			while($res = imw_fetch_assoc($rs)){
				$report_trans_id=$res['report_trans_id'];
				$encounter_id= $res['encounter_id'];
				$chgDetId= $res['charge_list_detail_id'];
				$trans_dop=$res['trans_dop'];
				$trans_type= strtolower($res['trans_type']);
				$trans_by= strtolower($res['trans_by']);
				
				$dos_month_year=$arrMapEncDOS[$encounter_id];
				$tempRecordData[$report_trans_id]=$res['trans_amount'];		
				
				switch($trans_type){
					case 'paid':
					case 'copay-paid':
					case 'deposit':
					case 'interest payment':
					case 'negative payment':
					case 'copay-negative payment':
						$paidForProc=$res['trans_amount'];
						if($trans_type=='negative payment' || $trans_type=='copay-negative payment' || $res['trans_del_operator_id']>0)$paidForProc="-".$res['trans_amount'];
						if(($trans_type=='negative payment' || $trans_type=='copay-negative payment') && $res['trans_del_operator_id']>0)$paidForProc=$res['trans_amount'];

						//IF parent_id >0 THEN IT MEANS RECORD IS UPDATED. THEN REMOVE PREVIOUS FETCHED AMOUNT.
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = ($trans_type=='negative payment' || $trans_type=='copay-negative payment') ? $tempRecordData[$res['parent_id']] : "-".$tempRecordData[$res['parent_id']];
						}
						$paidForProc+=$prevFetchedAmt; 
						
						$patPayDetArr[$dos_month_year][$trans_dop]+= $paidForProc;
					break;

					case 'credit':
					case 'debit':
						$crddbtamt=$res['trans_amount'];
						if($trans_type=='credit'){ 
							$crddbtamt= ($res['trans_del_operator_id']>0) ? "-".$res['trans_amount'] : $res['trans_amount'];							
						}else{  //debit
							$crddbtamt= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];				
						}

						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = ($trans_type=='credit') ? "-".$tempRecordData[$res['parent_id']] : $tempRecordData[$res['parent_id']];
						}
						$crddbtamt+=$prevFetchedAmt; 

						$patPayDetArr[$dos_month_year][$trans_dop]+= $crddbtamt;

					break;
					
					case 'default_writeoff':
						$tempNormalWriteoff[$chgDetId]['amt']= $res['trans_amount'];
						$tempNormalWriteoff[$chgDetId]['dos']= $dos_month_year;
					break;
					
					case 'write off':
					case 'discount':
						if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];

						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
						}
						$res['trans_amount']+=$prevFetchedAmt;
						
						$arrAdjustmentAmt[$dos_month_year]+= $res['trans_amount'];
						
					break;
					case 'over adjustment':
						if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];

						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
						}
						$res['trans_amount']+=$prevFetchedAmt;
						
						$arrAdjustmentAmt[$dos_month_year]+= $res['trans_amount'];
						
					break;
					case 'adjustment':
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = $tempRecordData[$res['parent_id']];
						}
						$res['trans_amount']+=$prevFetchedAmt;
						
						$arrAdjustmentAmt[$dos_month_year]+= $res['trans_amount'];
					break;
					case 'returned check':
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = $tempRecordData[$res['parent_id']];
						}
						$res['trans_amount']+=$prevFetchedAmt;
						
						$arrAdjustmentAmt[$dos_month_year]+= $res['trans_amount'];
					break;
					case 'refund':
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = $tempRecordData[$res['parent_id']];
						}
						$res['trans_amount']+=$prevFetchedAmt;
						
						$arrAdjustmentAmt[$dos_month_year]+= $res['trans_amount'];
					break;					
				}
			}
			//DROP TEMP TABLE
			imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);
			
			//NORMAL WITEOFF FINAL ARRAY
			foreach($tempNormalWriteoff as $chgDetId =>$arrDetails){
				$dos_month_year=$arrDetails['dos'];
				$normalWriteOffAmt[$dos_month_year]+= $arrDetails['amt'];
			}
			unset($tempNormalWriteoff);
		}
	}
}

$cols=23;
$w_cols=100/23;
$w_cols.='%';

//HTML CREATION
if(sizeof($mainResultArr)>0){

	//GET SELECTED
	$selgroup = $CLSReports->report_display_selected($grp_id,'group',1, $allGrpCount);
	$selFac = $CLSReports->report_display_selected($sc_name,'practice',1, $allFacCount);	
	$selPhy = $CLSReports->report_display_selected($Physician,'physician',1, $allPhyCount);
	$selCrPhy = $CLSReports->report_display_selected($credit_physician,'physician',1, $allCrPhyCount);
	$selInsurance = $CLSReports->report_display_selected($insuranceName,'insurance',1, $insurance_cnt);
	$selCPT = $CLSReports->report_display_selected($cpt_code_id,'cpt_code',1, $allCPTCount);	
	
	//MAKING OUTPUT DATA
	$file_name="cash_lag_analysis_report.csv";
	$csv_file_name= write_html("", $file_name);

	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');

	$arr[]="Cash Lag Analysis Report";
	$arr[]="$dayReport (DOS) From : $Sdate To : $Edate"."";
	$arr[]="Created by $opInitial on $curDate";
	$arr[]="Group : $selgroup";
	$arr[]="Facility : $selFac";
	$arr[]="Physician : $selPhy";
	$arr[]="Crediting Physician : $selCrPhy";
	$arr[]="\n";
	fputcsv($fp,$arr, ",","\"");

	$arr=array();
	$arr[]="";
	$arr[]="";
	$arr[]="Collection by Date of Payment";
	
	fputcsv($fp,$arr, ",","\"");
	
	$arr=array();
	$arr[]="DOS";
	$arr[]="Charges";
	$arr[]="Adjustment/Wrireoffs";
	$arr[]="Remaining Charges";
	for($i=1;$i<=20;$i++){
		$arr[]="Month ".$i;
	}
	$arr[]="\n";
	fputcsv($fp,$arr, ",","\"");
	
	foreach($mainResultArr as $dos_month_year => $charge_amt){

		$printFile=true;
		
		$html_part.='<tr style="height:35px;">
		<td class="text_10" style="background:#FFFFFF;">'.$dos_month_year.'</td>
		<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$CLSReports->numberFormat($charge_amt,2).'</td>
		<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$CLSReports->numberFormat($arrAdjustmentAmt[$dos_month_year]+$normalWriteOffAmt[$dos_month_year],2).'</td>
		<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$CLSReports->numberFormat($arrBalanceAmt[$dos_month_year],2).'</td>';
		
		$arr=array();
		$arr[]=$dos_month_year;
		$arr[]=$CLSReports->numberFormat($charge_amt,2);
		
		$paid_date=date('Y-m', strtotime($dos_month_year));
		
		for($i=1;$i<=20;$i++){
			
			$paid_for_month=$patPayDetArr[$dos_month_year][$paid_date];

			$html_part.='<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$CLSReports->numberFormat($paid_for_month,2).'</td>';
			$arr[]=$CLSReports->numberFormat($paid_for_month,2);
			
			$paid_date=date('Y-m', strtotime("+1 month", strtotime($paid_date)));
		}
		$html_part.='<tr>';
		$arr[]="\n";
		fputcsv($fp,$arr, ",","\"");						
	}
}
fclose($fp);

$HTMLCreated=0;
if($printFile == true){
	
	$html_file_data='<table class="rpt_table rpt rpt_table-bordered rpt_padding">
    <tr >
        <td style="text-align:left;" class="rptbx1" width="33%">Cash Lag Analysis Report</td>
        <td style="text-align:left;" class="rptbx2" width="34%">'.$dayReport.' (DOS) From : '.$Sdate.' To : '.$Edate.'</td>
        <td style="text-align:left;" class="rptbx3" width="33%">Created by '.$opInitial.' on '.$curDate.'</td>
    </tr>
    <tr>
        <td class="rptbx1">Group : '.$selgroup.'</td>
        <td class="rptbx2">Facility : '.$selFac.'</td>
        <td class="rptbx3">Physician : '.$selPhy.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cr. Phy.: '.$selCrPhy.'</td>
    </tr>
	</table>
	<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
	<tr>
		<td class="text_b_w" style="text-align:center;"></td>
		<td class="text_b_w" style="text-align:center;"></td>
		<td class="text_b_w" style="text-align:center;"></td>
		<td class="text_b_w" style="text-align:center;"></td>
		<td class="text_b_w" style="text-align:center;" colspan="20">Collection by Date of Payment</td>
	</tr>
	<tr>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">DOS</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Charges</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Adjustment/ Wrireoffs</td>
		<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Remaining Charges</td>';
		for($i=1;$i<=20;$i++){
			$arr[]="Month ".$i;
			$html_file_data.='<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Month '.$i.'</td>';
		}
	$html_file_data.='	
	</tr>
		'.$html_part.'
	</table>';
	
	
	$HTMLCreated=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$html_file_data= $styleHTML.$html_file_data;
	
	if($callFrom!='scheduled'){
		if($output_option=='output_csv'){
			echo '<div class="text-center alert alert-info">Please click on link near application bottom to download CSV file.</div>';
		}else{
			echo $html_file_data;	
		}	
	}
}else{
	if($callFrom!='scheduled'){
		echo $html_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}
?>
