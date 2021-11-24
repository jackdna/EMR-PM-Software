<?php
set_time_limit(0);
$without_pat = "yes";
require_once("reports_header.php");
require_once("../../library/classes/acc_functions.php");
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
require_once("../../library/classes/SaveFile.php");
$dbtemp_name="New Statement";
$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

$imp_g_code_proc=implode("','",$arr_g_code_proc);
if($_REQUEST['grp_id']!=""){
	$grp_id=$_REQUEST['grp_id'];
	$_REQUEST['groups']= explode(',',$grp_id);
}else{
	$grp_id=implode(',',$_REQUEST['groups']);
}

$qry = "select Statement_Elapsed,fully_paid, min_balance_bill,statement_base,full_enc from copay_policies where policies_id = '1'";
$qryres=imw_query($qry);
$row=imw_fetch_array($qryres);
$Statement_Elapsed = $row['Statement_Elapsed'];
$min_balance_bill = $row['min_balance_bill'];
$statement_base = $row['statement_base'];
$full_enc = $row['full_enc'];
$Statement_Elapsed_date = date('Y-m-d',mktime(0,0,0,date('m'),date('d')-$Statement_Elapsed,date('Y')));
if(empty($fully_paid) == false){
	$fully_paid = $row['fully_paid'];
}
if($_REQUEST['print_pdf']!=""){
	$update_pat_statement="yes";
	require_once('update_statement.php');
}

//--- GET Groups SELECT BOX ----
$selArrGroups = array_combine($_REQUEST['groups'],$_REQUEST['groups']);
$group_query = "Select  gro_id,name,del_status from groups_new order by name";
$group_query_res = imw_query($group_query);
$group_id_arr = array();
$groupName = "";
while ($group_res = imw_fetch_array($group_query_res)) {
	$sel='';
    $group_id = $group_res['gro_id'];
    $group_id_arr[$group_id] = $group_res['name'];
	if($selArrGroups[$group_id])$sel='SELECTED';
	$groupName .= '<option value="'.$group_res['gro_id'].'" '.$sel.'>' . $group_res['name'] . '</option>';
	
	$group_color = trim($group_res['group_color']);
	if(empty($group_color) == true){
		$group_color = '#FFFFFF';
	}
	$groupColorArr[$group_id] = $group_color;
}
?> 
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>:: imwemr ::</title>
        <!-- Bootstrap -->
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
              <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
              <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
            <![endif]-->
		<link href="css/reports_html.css" type="text/css" rel="stylesheet">
        <style>
            .pd5.report-content {
                position:relative;
                margin-left:40px;

                background-color: #EAEFF5;
            }
            .fltimg {
                position:absolute;
            }
            .fltimg span.glyphicon {
                position: absolute;
                top: 170px;
                left: 10px;
                color: #fff;
            }
            .reportlft .btn.btn-mkdef {
                padding-top: 6px;
                padding-bottom: 6px;
            }
            #content1{
                background-color:#EAEFF5;
            }
			.total-row {
				height: 1px;
				padding: 0px;
				background: #009933;
			}	
		</style>
    </head>
    <body>
        <form name="sch_report_form" id="sch_report_form" method="post"  action="new_statement.php">
            <input type="hidden" name="Submit" id="Submit" value="get reports">
            <input type="hidden" name="form_submitted" id="form_submitted" value="submit">
            <input type="hidden" name="print_pdf" id="print_pdf" value="">
            <input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
            <div class=" container-fluid">
                <div class="anatreport">
                    <div class="row" id="row-main">
                        <div class="col-md-3" id="sidebar">
                            <div class="reportlft" style="height:100%; min-height:<?php echo $_SESSION['wn_height']-300; ?>px;">
                                <div class="practbox">
                                    <div class="anatreport"><h2>Practice Filter</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        <div class="row">
                                        	<div class="col-sm-4">
                                                <label>Groups</label>
                                                <select name="groups[]" id="groups" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $groupName; ?>
												</select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label>Last Name From</label>
                                                <input type="text" name="startLname" id="startLname" value="<?php echo ($_REQUEST['startLname']!="")?$_REQUEST['startLname']:''; ?>" class="form-control">
                                            </div>
                                            <div class="col-sm-4">
                                                <label>To</label>
                                                <input type="text" name="endLname" id="endLname" value="<?php echo ($_REQUEST['endLname']!="")?$_REQUEST['endLname']:''; ?>" class="form-control">
                                            </div>
                                         </div> 
                                         <div class="row">       
                                            <div class="col-sm-12">
												<div class="">
													<!-- Pt. Search -->
													<div class="col-sm-12"><label>Patient</label></div>
													<div class="col-sm-5">
														<input type="hidden" name="patientId" id="patientId" value="<?php echo $_REQUEST['patientId'];?>">
														<input class="form-control" type="text" id="txt_patient_name" value="<?php echo $_REQUEST['txt_patient_name'];?>" name="txt_patient_name" onkeypress="{if (event.keyCode==13)return searchPatient()}" >
													</div> 
													<div class="col-sm-5">
														<select name="txt_findBy" id="txt_findBy" onkeypress="{if (event.keyCode==13)return searchPatient()}" class="form-control minimal">
															<option value="Active">Active</option>
															<option value="Inactive">Inactive</option>
															<option value="Deceased">Deceased</option> 
															<option value="Resp.LN">Resp.LN</option> 
															<option value="Ins.Policy">Ins.Policy</option>
														</select>
													</div> 
													<div class="col-sm-2 text-center">
														<button class="btn btn-success" type="button" onclick="searchPatient();"><span class="glyphicon glyphicon-search"></span></button>
													</div> 	
												</div>
                                            </div>
										</div>
                                        <div class="row"> 
                                         	<div class="col-sm-3">
                                                <label class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="rePrint" id="rePrint" value="1" <?php if ($_REQUEST['rePrint'] == '1') echo 'CHECKED'; ?>/>
                                                    <label for="rePrint">Re-Print</label>
                                                </label>
                                         	</div>
                                            <div class="col-sm-3">
                                                <label class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="fully_paid" id="fully_paid" value="1" <?php if ($_REQUEST['fully_paid'] == '1') echo 'CHECKED'; ?>/>
                                                    <label for="fully_paid">Fully Paid</label>
                                                </label>
                                         	</div>
                                            <div class="col-sm-3">
                                                <label class="checkbox checkbox-inline pointer">
                                                    <input onclick="txt_fun();" style="cursor:pointer;" type="checkbox" name="text_print" id="text_print" value="1" <?php if ($_REQUEST['text_print'] == '1') echo 'CHECKED'; ?>/>
                                                    <label for="text_print">Text File</label>
                                                </label>
                                         	</div>
                                            <div class="col-sm-3">
                                                <label class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="send_email" id="send_email" value="1" <?php if ($_REQUEST['send_email'] == '1') echo 'CHECKED'; ?>/>
                                                    <label for="send_email">Send Email</label>
                                                </label>
                                         	</div>       
                                         </div>
                                         <div class="row">
                                         	<div class="col-sm-6">
                                                <label class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="force_cond" id="force_cond" value="yes" <?php if ($_REQUEST['force_cond'] == 'yes') echo 'CHECKED'; ?>/>
                                                    <label for="force_cond">Forcefully Print</label>
                                                </label>
                                         	</div>
                                            <div class="col-sm-6">
                                                <label class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="inc_chr_amt" id="inc_chr_amt" value="yes" <?php if ($_REQUEST['inc_chr_amt'] == 'yes') echo 'CHECKED'; ?>/>
                                                    <label for="inc_chr_amt">Inc. Charges Comment</label>
                                                </label>
                                         	</div>    
                                         </div>  
                                         <div class="row">
                                            <div class="col-sm-6">
                                                <label class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="show_min_amt" id="show_min_amt" value="yes" <?php if ($_REQUEST['show_min_amt'] == 'yes') echo 'CHECKED'; ?>/>
                                                    <label for="show_min_amt">Show Pt Less Than Min Bal</label>
                                                </label>
                                         	</div>
                                             <div class="col-sm-6">
                                                <label class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="show_new_statements" id="show_new_statements" value="yes" <?php if ($_REQUEST['form_submitted']=="" || $_REQUEST['show_new_statements'] == 'yes') echo 'CHECKED'; ?>/>
                                                    <label for="show_new_statements">Show Only New Statements</label>
                                                </label>
                                         	</div>       
                                         </div>  
                                    </div>
                                </div>
								<div class="grpara">
									<div id="module_buttons" class="ad_modal_footer text-center" style="position:absolute; bottom:0;width:100%;">
                                        <button class="savesrch" type="button" onClick="top.fmain.get_sch_report()">Search</button>
                                    </div>
                                </div>                                                                                        
                            </div>
                        </div>
                        <div class="col-md-9" id="content1">
                            <div class="btn-group fltimg" role="group" aria-label="Controls">
                                <img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="glyphicon glyphicon-chevron-left"></span>
                            </div>
							<div class="pd5 report-content">
                                <div class="rptbox">
                                    <div id="html_data_div" class="row">
										<?php
                                            if(empty($_REQUEST['form_submitted']) === false){
                                                $printFile = false;	
                                                if($txt_patient_name>0){
													$patientId=$txt_patient_name;
												}
												if($startLname){
													$qry = "select patient_data.id,patient_data.lname from patient_data 
															join patient_charge_list on patient_charge_list.patient_id = patient_data.id 
															where patient_charge_list.del_status='0' and patient_data.lname > '$startLname'";
													if($endLname){
														$qry .= " and (patient_data.lname < '$endLname' or patient_data.lname like '$endLname%')";
													}
													if($send_email)
													{
														$qry .= " and TRIM(patient_data.email) != ''";
													}
													if($patientId){
														$qry .= " or patient_data.id = '$patientId'";
													}
													$qry .= "group by patient_data.id order by lname,fname";
													$qryres=imw_query($qry);
													while($row=imw_fetch_array($qryres)){
														$patId[] = $row['id'];
													}
													if(count($patId)>0){
														$patientId = implode(",",$patId);
													}
												}
												
												//--- GET ALL PATIENT ID UNDER COLLECTION -----------
												$collectionPatientIdArr = array();
												$qry = "select patient_id from patient_charge_list where del_status='0' and collection = 'true'";
												$qryres=imw_query($qry);
												while($collectionQryRes=imw_fetch_array($qryres)){
													$collectionPatientIdArr[] = $collectionQryRes['patient_id'];
												}
												$collectionPatientIdStr = join(',',$collectionPatientIdArr);
												
												//--- GET SELF PAY INSURANCE COMPANY DETAILS -----
												$selfPayArr = array();
												$qry = "select id from insurance_companies where in_house_code = 'self pay'";
												$qryres=imw_query($qry);
												while($insQryRes=imw_fetch_array($qryres)){
													$selfPayArr[$insQryRes['id']] = $insQryRes['id'];
												}
												
												//--- GET ALL CHARGE LIST ID -------
												$chk_total_balance = $charge_list_id_arr = array();
												$qry = "select encounter_id,charge_list_id,primary_paid,primaryInsuranceCoId,secondaryInsuranceCoId,
														tertiaryInsuranceCoId,secondary_paid,tertiary_paid,totalBalance,overPayment,patient_id,date_of_service from patient_charge_list
														where del_status='0' and patient_id in($patientId)";
												if($vip_bill_not_pat>0){
													$qry .= " and vipStatus='false'";
												}		
												if($fully_paid == 0){
													$qry .= " and (patient_charge_list.totalBalance > 0 or patient_charge_list.overPayment>0)";
												}
												if(empty($collectionPatientIdStr) == false){
													$qry .= " and patient_id not in($collectionPatientIdStr)";
												}
												if(empty($grp_id) == false){
													$qry .= " and gro_id in($grp_id)";
												}
												if($startLname != '' && $rePrint == '' && $statement_base==0){
													$qry .= " and (patient_charge_list.statement_status = 0 or patient_charge_list.statement_date < '$Statement_Elapsed_date')";
												}
												$qry .=" order by patient_charge_list.patient_id asc,patient_charge_list.overPayment asc";
												$qryres=imw_query($qry);
												while($patientChargeListRes=imw_fetch_array($qryres)){
													$patientChargeListRes2[]=$patientChargeListRes;
													$chk_total_balance[$patientChargeListRes['patient_id']][] = $patientChargeListRes['totalBalance'];
												}
												
												foreach($patientChargeListRes2 as $chl_key => $chl_val){
													if(array_sum($chk_total_balance[$patientChargeListRes2[$chl_key]['patient_id']])>0){
														$charge_list_id_arr[] = $patientChargeListRes2[$chl_key]['charge_list_id'];
													}
												}
												
												$charge_list_id_str = join(',',$charge_list_id_arr);
												$charge_list_id_arr = $self_proc_all = array();
												$qry = "select charge_list_id,differ_insurance_bill,proc_selfpay,newBalance,pri_due,sec_due,tri_due,pat_due from patient_charge_list_details 
														where del_status='0' and charge_list_id in($charge_list_id_str)";
												$qryres=imw_query($qry);
												while($detailQryRes=imw_fetch_array($qryres)){
													$differ_insurance_bill = $detailQryRes['differ_insurance_bill'];
													$charge_list_id = $detailQryRes['charge_list_id'];
													if($differ_insurance_bill == 'true'){
														$charge_list_id_arr[$charge_list_id] = $charge_list_id;
													}
													$self_proc_all[$charge_list_id][] = $detailQryRes['proc_selfpay'];
													if($detailQryRes['newBalance']!=($detailQryRes['pri_due']+$detailQryRes['sec_due']+$detailQryRes['tri_due']+$detailQryRes['pat_due'])){
														foreach($patientChargeListRes2 as $chl_key => $chl_val){
															if($patientChargeListRes2[$chl_key]['date_of_service']>'2017-01-01' && $patientChargeListRes2[$chl_key]['charge_list_id']==$charge_list_id && array_sum($chk_total_balance[$patientChargeListRes2[$chl_key]['patient_id']])>0){
																set_payment_trans($patientChargeListRes2[$chl_key]['encounter_id'],'',1);
															}
														}
													}
												}
												$diff_charge_list_id_str = join(',',$charge_list_id_arr);
												
												$get_charge_list_id = array();
												foreach($patientChargeListRes2 as $chl_key => $chl_val){	
													if(array_sum($chk_total_balance[$patientChargeListRes2[$chl_key]['patient_id']])>0){
														$ins_comp_paid = false;
														$all_proc_self_chk=0;
														$encounter_id = $patientChargeListRes2[$chl_key]['encounter_id'];
														$charge_list_id = $patientChargeListRes2[$chl_key]['charge_list_id'];
														$primaryInsuranceCoId = $patientChargeListRes2[$chl_key]['primaryInsuranceCoId'];	
														$primary_paid = $patientChargeListRes2[$chl_key]['primary_paid'];
														$secondaryInsuranceCoId = $patientChargeListRes2[$chl_key]['secondaryInsuranceCoId'];
														$secondary_paid = $patientChargeListRes2[$chl_key]['secondary_paid'];
														$tertiaryInsuranceCoId = $patientChargeListRes2[$chl_key]['tertiaryInsuranceCoId'];
														$tertiary_paid = $patientChargeListRes2[$chl_key]['tertiary_paid'];
														$differ_id = $charge_list_id_arr[$charge_list_id];
														if($differ_id){
															$ins_comp_paid = true;	
														}
														if(array_sum($self_proc_all[$charge_list_id])>0){
															if(array_sum($self_proc_all[$charge_list_id])>=count($self_proc_all[$charge_list_id])){
																$all_proc_self_chk=1;
															}
														}
														
														preg_match("/$charge_list_id/",$diff_charge_list_id_str,$insDiffChkArr);	
														
														if($primaryInsuranceCoId > 0 && count($insDiffChkArr) == 0 && $force_cond!='yes' && $all_proc_self_chk==0){
															$priSel = $selfPayArr[$primaryInsuranceCoId];
															if($priSel != '' || $primary_paid == 'true'){
																$ins_comp_paid = true;
															}
															else{
																$ins_comp_paid = false;
															}
															if($secondaryInsuranceCoId > 0 && $ins_comp_paid == true){			
																$secSel = $selfPayArr[$secondaryInsuranceCoId];			
																if($secSel != '' || $secondary_paid == 'true'){
																	$ins_comp_paid = true;
																}
																else{
																	$ins_comp_paid = false;
																}
															}
															if($tertiaryInsuranceCoId > 0 && $ins_comp_paid == true){
																$terSel = $selfPayArr[$tertiaryInsuranceCoId];
																if($terSel != '' || $tertiary_paid == 'true'){
																	$ins_comp_paid = true;
																}
																else{
																	$ins_comp_paid = false;
																}
															}
														}else{
															$ins_comp_paid = true;
														}
														$get_charge_list_id[$charge_list_id] = $charge_list_id;
													}
												}
												$get_charge_list_id = array_values($get_charge_list_id);
												$get_charge_listId = join(',',$get_charge_list_id);
												
												//---- FETCH ALL NEW STATEMENTS FROM CHARGE LIST TABLE --------
												if(empty($get_charge_listId) === false){
													
													if($imp_g_code_proc!=""){
														$imp_g_code_proc="'".$imp_g_code_proc."'";
														$exclude_g_code_proc=" and cpt_fee_tbl.cpt_prac_code not in($imp_g_code_proc)";
													}
													
													//--- GET PQRI AND G CODE PROCEDURE ID -----
													$cptCodeArr = array();
													$qry = "select cpt_fee_tbl.cpt_fee_id from cpt_category_tbl join cpt_fee_tbl
															on cpt_fee_tbl.cpt_cat_id = cpt_category_tbl.cpt_cat_id
															where (cpt_category_tbl.cpt_category like 'PQRI%' 
															or cpt_fee_tbl.cpt_prac_code like 'G%') $exclude_g_code_proc";
													//or lower(cpt_fee_tbl.cpt_desc) like '%comanage%'		
													$qryres=imw_query($qry);
													while($cptQryRes=imw_fetch_array($qryres)){
														$cptCodeArr[] = $cptQryRes['cpt_fee_id'];
													}
													$cptCodeStr = join(',',$cptCodeArr);
													
													$full_enc_cond="";
													$full_enc_chl_arr=array();
													$mainResult = array();
													if($force_cond!='yes'){
														if($full_enc>0){
															$chk_full_enc_qry=imw_query("select charge_list_id from patient_charge_list_details where charge_list_id in ($get_charge_listId) and pat_due>0 and del_status='0'");
															while($chk_full_enc_row=imw_fetch_array($chk_full_enc_qry)){
																$full_enc_chl_arr[]=$chk_full_enc_row['charge_list_id'];
															}
															if(count($full_enc_chl_arr)>0){
																$full_enc_chl_exp=implode(',',$full_enc_chl_arr);
																$full_enc_cond=" or patient_charge_list.charge_list_id in($full_enc_chl_exp)";
															}
														}
														$whr_pt_due=" and (patient_charge_list_details.pat_due>0 or patient_charge_list_details.overPaymentForProc>0 $full_enc_cond)";
													}else{
														$whr_pt_due=" and (patient_charge_list_details.newBalance>0 or patient_charge_list_details.overPaymentForProc>0)";
													}
													$qry = "select patient_charge_list.charge_list_id,patient_charge_list.patient_id,
															date_format(patient_charge_list.date_of_service, '".get_sql_date_format()."') as date_of_service,
															patient_charge_list.totalBalance,patient_charge_list.gro_id, patient_charge_list.encounter_id,
															patient_charge_list_details.charge_list_detail_id,
															patient_charge_list_details.diagnosis_id1,patient_charge_list_details.diagnosis_id2,
															patient_charge_list_details.diagnosis_id3,patient_charge_list_details.diagnosis_id4,
															patient_charge_list_details.modifier_id1,patient_charge_list_details.modifier_id2,
															patient_charge_list_details.modifier_id3,patient_charge_list_details.pat_due,
															patient_charge_list_details.overPaymentForProc,patient_charge_list_details.newBalance,
															patient_data.lname,patient_data.fname,patient_data.mname,patient_data.acc_statement_date,patient_data.username,patient_data.password,
															cpt_fee_tbl.cpt4_code,pos_facilityies_tbl.facilityPracCode,patient_data.stmt_iportal
															from patient_charge_list
															join patient_data on patient_data.id = patient_charge_list.patient_id
															join patient_charge_list_details on patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
															join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
															left join pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = patient_charge_list.facility_id
															where patient_charge_list_details.del_status='0' and patient_charge_list.charge_list_id in ($get_charge_listId) 
															and patient_charge_list_details.differ_patient_bill != 'true'
															and patient_data.hold_statement='0'
															$whr_pt_due";
													if(empty($cptCodeStr) === false){
														$qry .= " and cpt_fee_tbl.cpt_fee_id not in ($cptCodeStr)";
													}
															
													$qry .= " order by patient_data.lname,patient_data.fname,patient_charge_list.overPayment asc,patient_charge_list.date_of_service asc";
													$qryres=imw_query($qry);
													while($patientChargeListRes=imw_fetch_array($qryres)){
														if($force_cond!='yes' && $patientChargeListRes['overPaymentForProc']>0 && !in_array($patientChargeListRes['charge_list_id'],$full_enc_chl_arr)){
														}else{
															$acc_statement_date_unserz=unserialize($patientChargeListRes['acc_statement_date']);
															$acc_statement_date_grp=$acc_statement_date_unserz[$patientChargeListRes['gro_id']];
															if(strlen($patientChargeListRes['acc_statement_date'])==10){
																$acc_statement_date_grp=$patientChargeListRes['acc_statement_date'];
															}
															if($show_new_statements!="" && $acc_statement_date_grp > $Statement_Elapsed_date){
															}else{
																$charge_list_detail_id = $patientChargeListRes['charge_list_detail_id'];
																$charge_list_id = $patientChargeListRes['charge_list_id'];
																$mainResult['charge_list_id'][$charge_list_id] = $patientChargeListRes;	
																$mainResult[$charge_list_id]['charge_list_detail_id'][] = $charge_list_detail_id;
																$mainResult[$charge_list_id][$charge_list_detail_id] = $patientChargeListRes;
																$mainResult['pt_due_chk'][$patientChargeListRes['patient_id']][]= $patientChargeListRes['pat_due']-$patientChargeListRes['overPaymentForProc'];
																$mainResult['chl_pat_id'][$charge_list_id] = $patientChargeListRes['patient_id'];	
																$mainResult['pt_tot_bal_chk'][$patientChargeListRes['patient_id']][]= $patientChargeListRes['newBalance']-$patientChargeListRes['overPaymentForProc'];
																$mainResult['patient_id'][$patientChargeListRes['patient_id']] = $patientChargeListRes['patient_id'];	
																$mainResult['gro_id'][$patientChargeListRes['patient_id']]=$patientChargeListRes['gro_id'];	
																$mainResult['facilityPracCode'][$patientChargeListRes['patient_id']]=$patientChargeListRes['facilityPracCode'];	
																$mainResult['patient_lname'][$patientChargeListRes['patient_id']] = $patientChargeListRes['lname'];
																$mainResult['patient_fname'][$patientChargeListRes['patient_id']] = $patientChargeListRes['fname'];
																$mainResult['patient_mname'][$patientChargeListRes['patient_id']] = $patientChargeListRes['mname'];
																$mainResult['iportal_usr'][$patientChargeListRes['patient_id']] = $patientChargeListRes['username'];
																$mainResult['iportal_pass'][$patientChargeListRes['patient_id']] = $patientChargeListRes['password'];
																$mainResult['arr_chl_id'][$patientChargeListRes['patient_id']][$charge_list_id] = $charge_list_id;	
																$mainResult['acc_statement_date'][$patientChargeListRes['patient_id']] = $acc_statement_date_grp;
																$mainResult['stmt_iportal'][$patientChargeListRes['patient_id']] = $patientChargeListRes['stmt_iportal'];
															}
														}
													}
												}
												
												if($text_print){
													$limit = 10000;
												}else{
													$limit = 50;
												}
												if($pageNum==""){
													$pageNum=1;
												}
												$set_page_num=1;
												
												if(count($mainResult)>0){
													$patient_id_arr = $mainResult['patient_id'];
													$page_data_arr = array();
													if(count($patient_id_arr)>0){
														$chk_count_arr=0;
														foreach($patient_id_arr as $main_pat_id => $detailArr){
															$chk_fully_enc="";
															$pt_due_chk_sum = array_sum($mainResult['pt_due_chk'][$main_pat_id]);
															$pt_tot_bal_chk_sum = array_sum($mainResult['pt_tot_bal_chk'][$main_pat_id]);
															$arr_interset_res=array_intersect($mainResult['arr_chl_id'][$main_pat_id],$full_enc_chl_arr);
															if(count($arr_interset_res)>0){
																$chk_fully_enc="1";
															}
															if($pt_due_chk_sum>0 || ($force_cond=='yes' && $pt_tot_bal_chk_sum>0) || $chk_fully_enc!=""){
																if($force_cond!='yes' && $chk_fully_enc==""){
																	$totalBalance=$pt_due_chk_sum;
																}else{
																	$totalBalance=$pt_tot_bal_chk_sum;
																}
																$totalBalance2=numberFormat($totalBalance,2);
																$gro_id = $mainResult['gro_id'][$main_pat_id];
																$bgColor = $groupColorArr[$gro_id];
																$facilityPracCode = $mainResult['facilityPracCode'][$main_pat_id];
																$bgColor = $groupColorArr[$gro_id];
																$patientName = core_name_format($mainResult['patient_lname'][$main_pat_id], $mainResult['patient_fname'][$main_pat_id], $mainResult['patient_mname'][$main_pat_id]);
																$iportal_star="";	
																if($mainResult['stmt_iportal'][$main_pat_id]>0){
																	$iportal_star="*";
																}
																$patientName .= ' - '.$main_pat_id.$iportal_star;
																$fileDataArr = array();
																$fileDataArr['bg_color'] = "#ffffff";
																if($mainResult['acc_statement_date'][$main_pat_id] > $Statement_Elapsed_date){
																	$fileDataArr['bg_color'] = "#A2A2E6";
																}
																$fileDataArr['patient_name'] = $patientName;
																$fileDataArr['balance'] = $totalBalance2;
																$fileDataArr['arr_pat_chl_id'] = implode('---',$mainResult['arr_chl_id'][$main_pat_id]);
																if($show_new_statements!="" && $mainResult['acc_statement_date'][$main_pat_id] > $Statement_Elapsed_date){
																}else{
																	if($mainResult['stmt_iportal'][$main_pat_id]>0){
																		if($totalBalance >= $min_balance_bill || $totalBalance<0){
																			$page_data_arr['masterGreater_iportal'][$set_page_num][] = $fileDataArr;
																			$total_balance_arr[$set_page_num][] = $totalBalance;
																			$chk_count_arr++;
																			$final_chk_paging=$limit*$set_page_num;
																			if($chk_count_arr>$final_chk_paging){
																				$set_page_num=$set_page_num+1;
																			}
																			$tot_paging_arr[]=$fileDataArr;	
																			$printFile = true;
																		}
																		else{
																			if($show_min_amt!=''){
																				$page_data_arr['masterLess_iportal'][$set_page_num][] = $fileDataArr;	
																				$total_balance_arr[$set_page_num][] = $totalBalance;
																				$chk_count_arr++;
																				$final_chk_paging=$limit*$set_page_num;
																				if($chk_count_arr>$final_chk_paging){
																					$set_page_num=$set_page_num+1;
																				}
																				$tot_paging_arr[]=$fileDataArr;	
																				$printFile = true;
																			}
																		}
																	}else{
																		if($totalBalance >= $min_balance_bill || $totalBalance<0){
																			$page_data_arr['masterGreater'][$set_page_num][] = $fileDataArr;
																			$total_balance_arr[$set_page_num][] = $totalBalance;
																			$chk_count_arr++;
																			$final_chk_paging=$limit*$set_page_num;
																			if($chk_count_arr>$final_chk_paging){
																				$set_page_num=$set_page_num+1;
																			}
																			$tot_paging_arr[]=$fileDataArr;	
																			$printFile = true;
																		}
																		else{
																			if($show_min_amt!=''){
																				$page_data_arr['masterLess'][$set_page_num][] = $fileDataArr;
																				$total_balance_arr[$set_page_num][] = $totalBalance;
																				$chk_count_arr++;
																				$final_chk_paging=$limit*$set_page_num;
																				if($chk_count_arr>$final_chk_paging){
																					$set_page_num=$set_page_num+1;
																				}
																				$tot_paging_arr[]=$fileDataArr;	
																				$printFile = true;
																			}
																		}
																	}	
																}
															}
														}
													}
												}
												$stat_type_arr=array("masterGreater","masterLess","masterGreater_iportal","masterLess_iportal");
												if(count($tot_paging_arr) > 0){
											?>
                                            	<table class="rpt_table rpt rpt_table-bordered table" style="width:100%">
                                                    <tr>
                                                        <td class="text_b_w"></td>
                                                        <td class="text_b_w">Patient - ID</td>
                                                        <td class="text_b_w text-center">Balance</td>
                                                    </tr>
                                            <?php	
													foreach($stat_type_arr as $key=>$val){
														if($val=="masterGreater" || $val=="masterGreater_iportal"){
															$label="Balance >= ".$min_balance_bill;
														}
														if($val=="masterLess" || $val=="masterLess_iportal"){
															$label="Balance < ".$min_balance_bill;
														}
														if(count($page_data_arr[$val][$pageNum]) > 0){
											?>
                                            				<tr>
                                                                <td class="text_b_w" colspan="3">
                                                                    <label class="checkbox checkbox-inline pointer">
                                                                        <input style="cursor:pointer;" type="checkbox" name="<?php echo $val; ?>" id="<?php echo $val; ?>" onclick="selAll(this.id);">
                                                                        <label for="<?php echo $val; ?>"><?php echo $label;?></label>
                                                                    </label>
                                                                </td>
                                                            </tr>
                                            <?php				
														}
														foreach($page_data_arr[$val][$pageNum] as $data_key => $data_val){
															$page_content=$page_data_arr[$val][$pageNum][$data_key];
											?>
                                                             <tr bgcolor="<?php echo $page_content['bg_color']; ?>">
                                                                <td class="text_10">
                                                                    <label class="checkbox checkbox-inline pointer">
                                                                        <input style="cursor:pointer;" type="checkbox" name="chargeList[]" id="chargeList<?php echo $page_content['arr_pat_chl_id']; ?>" class="chk_box_css <?php echo $val; ?>" value="<?php echo $page_content['arr_pat_chl_id'] ?>">
                                                                        <label for="chargeList<?php echo $page_content['arr_pat_chl_id']; ?>"></label>
                                                                    </label>
                                                                </td>
                                                                <td class="text_10">
                                                                    <?php echo $page_content['patient_name']; ?>
                                                                </td>
                                                                <td class="text_10" style="text-align:right;"><?php echo $page_content['balance']; ?></td>	
                                                            </tr>
                                            <?php				 
														}
													}
											?>
                                                    <tr>
                                                        <td class="text_10b" style="text-align:right;">Total : </td>
                                                        <td class="text_10b" style="text-align:left;"><?php echo count($total_balance_arr[$pageNum]); ?></td>
                                                        <td class="text_10b" style="text-align:right;"><?php echo numberFormat(array_sum($total_balance_arr[$pageNum]),2); ?></td>
                                                    </tr>
                                        	</table>
                                            <?php
												$count = count($tot_paging_arr);
												$totalPage = ceil($count / $limit);
												$pageLimit = 30;
												$startPage = $pageNum - $pageLimit;
												if($startPage < 1){
													$startPage =  1 ;
												}		
												$endPage = $pageLimit + $pageNum;
												
												if($endPage > $totalPage){
													$endPage = $totalPage;
												}
													
												for($i = $startPage;$i <= $endPage; $i++){
													if($i == $pageNum){
														$pageLink .= '<a class="text_10b">['.$i.']</a>&nbsp;&nbsp;';
													}
													else{
														$pageLink .= '<a href="new_statement.php?form_submitted=submit&grp_id='.$grp_id.'&startLname='.$startLname.'&endLname='.$endLname.'&rePrint='.$rePrint.'&send_email='.$send_email.'&exclude_sent_email='.$exclude_sent_email.'&fully_paid='.$fully_paid.'&text_print='.$text_print.'&show_min_amt='.$show_min_amt.'&show_new_statements='.$show_new_statements.'&force_cond='.$force_cond.'&inc_chr_amt='.$inc_chr_amt.'&pageNum='.$i.'" class="text_10b_purpule">'.$i.'</a>&nbsp;&nbsp;';
													}
												}
												//------ Start Next Pagination --------------
												if($count > $limit + $start){
													$pageNumber = $pageNum + 1;
													//$startLink = '<a href="statementsResult.php?Submit=submit&grp_id='.$grp_id.'&startLname='.$startLname.'&endLname='.$endLname.'&rePrint='.$rePrint.'&fully_paid='.$fully_paid.'&text_print='.$text_print.'&force_cond='.$force_cond.'&inc_chr_amt='.$inc_chr_amt.'&pageNum='.$i.'" class="text_10b_purpule">Next</a>';
												}
												
												if(0 < $start - $pageNum){
													$pageNumber = $pageNum - 1;
													//$previousLink = '<a href="statementsResult.php?Submit=submit&grp_id='.$grp_id.'&startLname='.$startLname.'&endLname='.$endLname.'&rePrint='.$rePrint.'&fully_paid='.$fully_paid.'&text_print='.$text_print.'&force_cond='.$force_cond.'&inc_chr_amt='.$inc_chr_amt.'&pageNum='.$i.'" class="text_10b_purpule">Previous</a>';
												}
												
												if($startLink){
													$startLink = '<td style="width:100px" class="tblBg alignCenter">'.$startLink.'</td>';
												}
												else{
													$startLink = '<td style="width:100px" class="tblBg alignCenter"></td>';
												}
												
												if($previousLink){
													$previousLink = '<td style="width:100px" class="tblBg alignCenter">'.$previousLink.'</td>';
												}
												else{
													$previousLink = '<td style="width:100px" class="tblBg alignCenter"></td>';
												}
												
												//------ End Next Pagination --------------
												
												if($pageLink){
													$pageLinks = '
														'.$previousLink.'
															<td class="tblBg alignCenter" style="width:100px;">'.$pageLink.'</td>
														'.$startLink.'
													';
												}
											?> 
                                            <table id="stateRpt" width="100%" height="40px" bgcolor="#FFF3E8" cellpadding="1" cellspacing="1" border="0">
                                                <tr style="border:none;">
                                                    <?php echo $pageLinks; ?>
                                                </tr>
                                            </table>       
											<?php
												}else{
													echo '<div class="text-center alert alert-info">No Record Found.</div>';
												}
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <iframe src="" id="txt_iframe" name="txt_iframe" height="0" width="0" allowtransparency="1" class="hide"></iframe>  
                        </div>
                    </div>
				</div>
            </div>
        </form>
<script type="text/javascript">
	var file_location = '<?php echo $file_location; ?>';
	var conditionChk = '<?php echo $printFile; ?>';
	var send_emailChk = '<?php echo $send_email; ?>';
	var mainBtnArr = new Array();
	var btncnt=0;
	
	if(conditionChk==true){
		if(send_emailChk>0){
			mainBtnArr[btncnt] = new Array("sendEmail", "Send Email", "top.fmain.generate_pdf('email');");
		}else{
			mainBtnArr[btncnt] = new Array("print", "Print", "top.fmain.generate_pdf('print');");
		}
	}
	top.btn_show("PPR", mainBtnArr);
	
	function generate_pdf(arg) {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		var sel = false;
		var obj = document.getElementsByName("chargeList[]");
		for(i=0;i<obj.length;i++){
			if(obj[i].checked == true){
				sel = true;
				break;
			}
		}
		if(sel == false){
			top.show_loading_image("hide");
			var msg = "Please select any patient to print the statement.";
			if(arg=="email"){
				msg = "Please select any patient to send the statement via email."; 
			}
			alert(msg);
			return false;
		}else{
			$('#print_pdf').val(arg);
			//top.$("#print").prop("disabled",true);
			//document.sch_report_form.submit();
			//st,lt,call_from
			process_statements(0,10,'Report');
		}
	}
	function get_sch_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		$('#print_pdf').val('');
		
		var patientCheck = false;
		
		if($('#startLname').val()!=""){
			$('#txt_patient_name').val('');
			$('#patientId').val('');
		}
		else{
			patientCheck = true;
			$('#startLname').val('');
		}
		
		if($('#endLname').val()!=""){
			if($('#startLname').val()==""){
				alert('Please enter start last name.');
				top.show_loading_image('hide');
				return false;
			}
		}else{
			patientCheck = true;
			$('#endLname').val('');
		}	
		
		if(patientCheck == true && $('#txt_patient_name').val() == ''){
			alert('Please select patient to get reports.');
			top.show_loading_image('hide');
			return false;
		}
		
		document.sch_report_form.submit();
	}

	$(document).ready(function () {
		$(".toggle-sidebar").click(function () {
			$("#sidebar").toggleClass("collapsed");
			$("#content1").toggleClass("col-md-12 col-md-9");

			if ($('.fltimg').find('span').hasClass('glyphicon glyphicon-chevron-left')) {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-left').addClass('glyphicon glyphicon-chevron-right');
			} else {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-right').addClass('glyphicon glyphicon-chevron-left');
			}
			return false;
		});
	});

	function set_container_height(){
		$('.reportlft').css({
			'height':(window.innerHeight),
			'max-height':(window.innerHeight),
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
	} 
		
	function selAll(master){
		var obj = $('#'+master);
		if(obj.is(':checked')){
			$('.'+master).prop('checked',true);
		}else{
			$('.'+master).prop('checked',false);
		}
	}
	
	function searchPatient(){
		var name = document.getElementById("txt_patient_name").value;
		var findBy = document.getElementById("txt_findBy").value;
		var validate = true;
		  if(name.indexOf('-') != -1){
			name = name.replace(' ','');
			name = name.split('-');
			name = name[0]
			validate = false;
		  }
		  if(validate){
			if(isNaN(name)){
				pt_win = window.open("../../interface/scheduler/search_patient_popup.php?btn_enter="+findBy+"&btn_sub="+name+"&call_from=physician_console","mywindow","width=800,height=500,scrollbars=yes");
			}
			else{
				getPatientName(name);
			}
		  }
		return false;
	}
	function getPatientName(id,obj){
		$.ajax({
			type: "POST",
			url: top.JS_WEB_ROOT_PATH+"/interface/physician_console/ajax_html.php?from=console&task=pt_details_ajax&return_data=yes&ptid="+id,
			dataType:'JSON',
			success: function(r){
				if(r.id){
					if(obj){
						set_xml_modal_values(r.id,r.pt_name);
					}else{
						$("#txt_patient_name").val(r.pt_name);
						$("#patientId").val(r.id);
					}
				}else{
					fAlert("Patient not exists");
					$("#txt_patient_name").val('');
					return false;
				}	
			}
		});
	}
	
	//previous name was getvalue
	function physician_console(id,name){
		document.getElementById("txt_patient_name").value = name;
		document.getElementById("patientId").value = id;
	}
	
	function txt_fun(){
		if($('#Submit').val()!=""){
			top.show_loading_image('show');
			get_sch_report();
		}
	}
	
$(window).load(function(){
	set_container_height();
});

$(window).resize(function(){
	set_container_height();
});
function process_statements(st,lt,call_from){
	top.show_loading_image('hide');
	var st_limit = lt;
	var st_chl_ids=[];
	var elements = $('#html_data_div input.chk_box_css[name="chargeList[]"]:checkbox:checked');
	var tot_pat_len=elements.length;
	elements = elements.slice(st,lt);
	$(elements).each(function(){
		st_chl_ids.push($(this).val());
	});
	var grp_id = $("#groups").val();
	var data_var = "&from="+call_from;
	if(typeof(grp_id)!="undefined" && grp_id!=null){data_var +="&grp_id="+$("#groups").val();}
	if(typeof($("#startLname").val())!="undefined"){data_var +="&startLname="+$("#startLname").val();}
	if(typeof($("#endLname").val())!="undefined"){data_var +="&endLname="+$("#endLname").val();}
	if($('#rePrint').is(':checked')==true){data_var +="&rePrint="+$("#rePrint").val();}
	if($("#fully_paid").is(':checked')==true){data_var +="&fully_paid="+$("#fully_paid").val();}
	if($("#text_print").is(':checked')==true){data_var +="&text_print="+$("#text_print").val();}
	if($("#send_email").is(':checked')==true){data_var +="&send_email="+$("#send_email").val();}
	if($("#force_cond").is(':checked')==true){data_var +="&force_cond="+$("#force_cond").val();}
	if($("#inc_chr_amt").is(':checked')==true){data_var +="&inc_chr_amt="+$("#inc_chr_amt").val();}
	if($("#show_min_amt").is(':checked')==true){data_var +="&show_min_amt="+$("#show_min_amt").val();}
	if($("#show_new_statements").is(':checked')==true){data_var +="&show_new_statements="+$("#show_new_statements").val();}
	if($("#exclude_sent_email").is(':checked')==true){data_var +="&exclude_sent_email="+$("#exclude_sent_email").val();}
	if(typeof($("#print_pdf").val())!="undefined"){data_var +="&print_pdf="+$("#print_pdf").val();}
	html = '<div style="width:600px;"><span id="st_process_id"><p>Please do not close this dialog box until process complete.</p></span><iframe frameborder=0 framespacing=0 style="width:600px; height:150px;" src="../reports/process_statements.php?st_chl_ids='+st_chl_ids+'&st='+st+'&slt='+lt+'&st_limit='+st_limit+'&tot_pat_len='+tot_pat_len+data_var+'"></iframe></div>';
	fancyModal(html,'Process New Statement','650px','150px');
}
var page_heading = "<?php echo $dbtemp_name; ?>";
set_header_title(page_heading);
<?php if(empty($_REQUEST['form_submitted']) === false && $_REQUEST['txt_filePath']!=""){ ?>
	$("#txt_iframe").attr('src','<?php echo "downloadtxt.php?txt_filePath=".$_REQUEST['txt_filePath'];?>');
<?php } ?>
</script> 
</body>
</html>