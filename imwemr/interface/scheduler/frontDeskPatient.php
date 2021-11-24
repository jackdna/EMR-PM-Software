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
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_page_functions.php');
require_once($GLOBALS['fileroot']."/library/classes/scheduler/appt_cl_functions.php");
require_once($GLOBALS['fileroot']."/library/classes/scheduler/appt_ac_functions.php");
require_once($GLOBALS['fileroot']."/library/classes/scheduler/appt_cn_functions.php");
require_once($GLOBALS['fileroot']."/library/classes/cls_common_function.php");
require_once($GLOBALS['fileroot'].'/library/classes/class.app_base.php');
$app_base			= new app_base();	

$OBJCommonFunction = new CLSCommonFunction;
//class objects
$obj_scheduler = new appt_scheduler();
$obj_contactlens = new appt_contactlens();
$obj_accounting = new appt_accounting();
$obj_chartnotes = new appt_chartnotes($_SESSION['authId'], $_SESSION["patient"]);
//release locked chart notes if any
$obj_chartnotes->release_pt_cn_locks();
//get patient id
if(isset($_REQUEST["pat_id"]) && !empty($_REQUEST["pat_id"])){
	$pat_id = $_REQUEST["pat_id"];
	if((int)$pat_id != (int)$_SESSION["patient"]){
		//clear alertShowForThisSession session variable so that in new session alert could come
		$_SESSION['alertShowForThisSession'] = "";
		$_SESSION["alertShowForThisSession"] = NULL;
		unset($_SESSION["alertShowForThisSession"]);
	}
	//Clear Patient Session --
	clean_patient_session();

}else{
	$pat_id = $_SESSION["patient"];
}
imw_query("update schedule_appointments set ref_phy_changed=0, ref_phy_comments='' where sa_patient_id=$pat_id");
$_SESSION["patient"] = $pat_id;
$showAlert = $_REQUEST["showAlert"];

// Collection Alert
$coll_alt=0;
$activeId  =get_account_status_id('Active');
//$getCollectionAmtStr = "SELECT collection FROM patient_charge_list WHERE patient_id='$pat_id' and collection = 'true'";
$getCollectionAmtStr = "SELECT pat_account_status FROM patient_data WHERE id='$pat_id'";
$getCollectionAmtQry = imw_query($getCollectionAmtStr);
if(imw_num_rows($getCollectionAmtQry)>0){
	$res = imw_fetch_assoc($getCollectionAmtQry);
	if($activeId!=$res['pat_account_status'] && $res['pat_account_status']>0){
		$coll_alt=1;
		$stsRs=imw_query("Select status_name FROM account_status WHERE id='".$res['pat_account_status']."'");
		$stsRes=imw_fetch_array($stsRs);
		$statusName = $stsRes['status_name'];
	}
}
// Collection Alert
		
$sel_date = (isset($_REQUEST["sel_date"]) && !empty($_REQUEST["sel_date"])) ? $_REQUEST["sel_date"] : date("Y-m-d");

//if patient id found
if(!empty($pat_id)){
	
	$patient_notes_message = "";
	$pt_spec_message = "";
	$poe_alert = "";
	if(isset($_REQUEST["showAlert"]) && $_REQUEST["showAlert"] == "true"){
		/* ************************************************************************************* */
		/* this file will return a variable $patient_notes_message that hold content for alert
		/* ************************************************************************************* */	
		$patient_notes_tab = "scheduler";
		include_once("../common/patient_note_alert.php");
		
		/* ************************************************************************************* */
		/* pt specific alerts if any
		/* ************************************************************************************* */	
		if(trim($_SESSION['alertShowForThisSession']) != "Cancel"){
			require_once($GLOBALS['fileroot']."/library/classes/CLSAlerts.php");
			$oCLSAlert = new CLSAlerts();
			$oCLSAlert->call_from_flag = "scheduler";		
			$pt_spec_message = $oCLSAlert->getPatSpecificAlert($pat_id, "Scheduler");
		}

		/* ************************************************************************************* */
		/* poe alerts if any
		/* ************************************************************************************* */	
		require_once($GLOBALS['fileroot']."/library/classes/work_view/CPT.php");
		require_once($GLOBALS['fileroot']."/library/classes/work_view/Poe.php");
		$oPoe = new Poe($pat_id);
		$poe_alert = $oPoe->showAlert("1");
	}

	//show / hide collection flag
	$arr_coll = $obj_accounting->show_collection_flag($pat_id);
	
	//getting pt dteials
	$pat_fields = "pd.patient_notes, pd.primary_care_phy_name, pd.primary_care_phy_id, pd.chk_notes_scheduler, pd.id, pd.patientStatus, pd.otherPatientStatus, pd.dod_patient, pd.title, pd.fname, pd.mname, pd.lname, pd.suffix, pd.EMR, pd.sex, DOB, ss, providerID, phone_home, phone_biz, phone_cell, primary_care_id, primary_care, street, street2, city, state, postal_code, pd.zip_ext, email, erx_entry, erx_patient_id, default_facility, p_imagename, pd.preferr_contact, photo_ref, pd.nick_name, pd.phonetic_name, pd.language, pd.lang_code ";
	$arr_pat = $obj_scheduler->get_patient_details($pat_id, $pat_fields);
	if(is_ref_phy_deleted($arr_pat['primary_care_id'])){
		$ref_phy_class = "del_val";
	}
	if(is_ref_phy_deleted($arr_pat['primary_care_phy_id'])){
		$pri_care_class = "del_val";
	}
	//updating recent searched pt list
	$obj_scheduler->update_recent_pt_list($_SESSION["authId"], $pat_id, $arr_pat["patientStatus"]);

	//gettig patient dues
	list($pt_due, $ins_due, $outstanding_bal, $credit_amt, $total_amt) = $obj_accounting->get_pt_acc_dues($pat_id);

	//getting appt details
	$dos_for_enc = date("Y-m-d");
	
	$sch_fields = "sa.id, sa.sa_doctor_id, sa.case_type_id, sa.sa_facility_id, sa.procedureid, sa.sec_procedureid, sa.tertiary_procedureid, sa.sa_patient_app_status_id, sa.RoutineExam, sa.procedure_site, sa.procedure_sec_site, sa.procedure_ter_site, sa.sa_comments, sa.sa_app_start_date, sa.arrival_time, sa.pick_up_time, sa.sa_app_starttime, sa.sa_app_endtime, sa.appt_sync_updox, sp.acronym, u.fname, u.lname, f.city, f.name,sa.facility_type_provider, sa.sa_app_duration,sa.sa_ref_management, sa.ref_phy_id";
	
	if(constant("ENABLE_REAL_ELIGILIBILITY") == "YES"){
		$sch_fields .= ", sa.rte_id ";
	}

	if(isset($_REQUEST["sch_id"]) && !empty($_REQUEST["sch_id"])){
		$arr_appt = $obj_scheduler->get_appointment_details($sch_fields, $pat_id, $_REQUEST["sch_id"]);		
		$dos_for_enc = $arr_appt["sa_app_start_date"];
		
		//****LOGGING PT.MONITOR**
		patient_monitor_daily('PATIENT_LOAD',$pat_id,$_REQUEST["sch_id"]);
		
	}else{
		$where = " and sa_app_start_date = '".$sel_date."' ";
		$arr_appt = $obj_scheduler->get_appointment_details($sch_fields, $pat_id, 0, $where);
		$_REQUEST["sch_id"] = $arr_appt["id"];
	}
	//pre($arr_appt);
	if(count($arr_appt)>0 && ($arr_appt["sec_procedureid"]!="" || $arr_appt["tertiary_procedureid"]!=""))
	{
		$proc_req_qry = 'SELECT id,proc,acronym FROM slot_procedures WHERE id IN('.$arr_appt["sec_procedureid"].','.$arr_appt["tertiary_procedureid"].')';
		$proc_obj = imw_query($proc_req_qry);
		$slot_proc_sec_ter_arr = array();
		$slot_proc_sec_ter_arr_new = array();
		while($proc = imw_fetch_assoc($proc_obj))
		{
			$slot_proc_sec_ter_arr[$proc['id']]['acronym'] = $proc['proc'];
			$slot_proc_sec_ter_arr_new[$proc['id']]['acronym'] = $proc['acronym'];
		}
	}
	
	//show ref phy op mapping for select primary procedure
	$refPhyOpCmnt='';
	if($arr_appt["procedureid"] && $arr_pat['primary_care_id'])
	{
		$procedure_id=$arr_appt['procedureid'];
		$q="select procedureId from slot_procedures where doctor_id = $arr_appt[sa_doctor_id] AND proc != '' and active_status!='del' and id=$arr_appt[procedureid]";
		$qProc=imw_query($q);
		if(imw_num_rows($qProc)>0)
		{
			$dProc=imw_fetch_object($qProc);
			$procedure_id=$dProc->procedureId;
		}
		//echo "select linked_op from slot_procedures_linked_op where ref_id=$arr_pat[primary_care_id] and proc_id=$procedure_id and linked_op<>''";
		$q=imw_query("select linked_op from slot_procedures_linked_op where ref_id=$arr_pat[primary_care_id] and proc_id=$procedure_id and linked_op<>''");
		if(imw_num_rows($q)>0){
			$d=imw_fetch_object($q);
			$refPhyOpCmnt=" (PO Evaluation Mapping: ".$d->linked_op.')';
		}
	}

	$dos_disp_for_enc = core_date_format($dos_for_enc, "m-d-Y");

	//getting latest encounter id
	$enco_id = "";
	$dos=(isset($dos))?$dos:$dos_for_enc;
	$arr_enc = $obj_accounting->get_latest_enc_id($pat_id, $dos);
	$enco_id = $arr_enc["encounterId"];

	//getting default ins case for the patient
	$qry = "select ins_caseid, ins_case_type from insurance_case where patient_id = '".$pat_id."' and case_status = 'Open' order by ins_case_type LIMIT 0,1";
	$res = imw_query($qry);

	if(imw_num_rows($res) > 0){
		$arr = imw_fetch_assoc($res);
		$pt_ins_case_id = $arr["ins_caseid"];
	}


	//check for future appt - front desk message
	if($arr_appt === false){
		$where = " and sa_app_start_date > '".$sel_date."' ";
		$arr_future_appt = $obj_scheduler->get_appointment_details($sch_fields, $pat_id, 0, $where);

	}else{
		$arr_future_appt = $arr_appt;
		
		list($year_appoint, $month_appoint, $day_appoint) = explode('-', $arr_appt["sa_app_start_date"]);
		if($year_appoint != ""){
			$create_date_appoint = $month_appoint."-".$day_appoint."-".$year_appoint;  //GETTING APPOINTMENT DATE OF PATIENT
		}
		
		list($start_hours, $start_minutes, $start_sec) = explode(':', $arr_appt["sa_app_starttime"]);
		$app_time_start = @mktime($start_hours, $start_minutes, $start_sec, $month_appoint,$day_appoint,$year_appoint);
		$app_start_time = @date("g:iA",$app_time_start);
		
		list($end_hours, $end_minutes, $end_sec) = explode(':', $arr_appt["sa_app_endtime"]);
		$app_time_end = @mktime($end_hours, $end_minutes, $end_sec, $month_appoint,$day_appoint,$year_appoint);
		$app_end_time = @date("g:iA",$app_time_end);	
		
		$app_time = $app_start_time."-".$app_end_time;  //GETTING APPOINTTMENT TIME OF PATIENT
		
		$physician_lname = $arr_appt["lname"];
		$physician_lname_intial = $physician_lname[0];
		$physician_name = $arr_appt["fname"]." ".$physician_lname_intial.".";  //GETTING PHYSICIAN NAME FOR APPOINT OF PATIENT
	}

	//proc message
	$str_proc_alert = $obj_scheduler->default_proc_to_doctor_proc($arr_future_appt["procedureid"], $arr_future_appt["sa_doctor_id"]);
	$arr_proc_alert = explode("~", $str_proc_alert);
	$bl_proc_alert = (isset($arr_proc_alert[2]) && !empty($arr_proc_alert[2])) ? true : false;

	//getting todo appt for this pt if any
	$arr_todo = $obj_scheduler->get_todo_appointments("sa.id", $pat_id);
	$bl_todo = true;
	if($arr_todo == false){
		$bl_todo = false;
	}
	//contact lens
	$arr_received_supply = $obj_contactlens->get_order_received($pat_id);
	
	$arr_ordered_supply = array(false, "");
	$arr_trial_cost = array("", "");
	if($arr_appt !== false){
		$arr_ordered_supply = $obj_contactlens->get_DOS_due_balance($arr_appt["sa_app_start_date"], $pat_id);
		$arr_trial_cost = $obj_contactlens->get_trial_cost($arr_appt["sa_app_start_date"], $pat_id);
	}
	$last_final_rx_id = "";
	$arr_last_final_rx = $obj_contactlens->get_last_final_rx($pat_id,$sel_date);
	if($arr_last_final_rx !== false){
		if(array_key_exists('clws_id',$arr_last_final_rx[0]))
		$last_final_rx_id = $arr_last_final_rx[0]["clws_id"];
		if(array_key_exists('final_status',$arr_last_final_rx))
		$final_cl_rx_status = $arr_last_final_rx["final_status"];
	}
	$vis_mr_none_given = "";
	$arr_last_glasses_rx = $obj_contactlens->get_last_glasses_rx($pat_id,$sel_date);
	if($arr_last_glasses_rx !== false){
		if($arr_last_glasses_rx["finalize"] == 0){
			$_SESSION["form_id"] = $arr_last_glasses_rx["form_id"];
		}
		if($arr_last_glasses_rx["finalize"] == 1){
			$_SESSION["finalize_id"] = $arr_last_glasses_rx["form_id"];
		}
		$vis_statusElements=trim($arr_last_glasses_rx['vis_statusElements']);
		if($vis_statusElements){
			$arr_vis_statusElements=explode(",",$vis_statusElements);
		}
		
		if((in_array("elem_mrNoneGiven1=1",$arr_vis_statusElements)) ||  (in_array("elem_mrNoneGiven2=1",$arr_vis_statusElements)) || (in_array("elem_mrNoneGiven3=1",$arr_vis_statusElements))){
			$vis_mr_none_given =$arr_last_glasses_rx["vis_mr_none_given"];
	}
	
	}
$dyn_id = "fd_pt_controls";
$dyn_disble_future_appt = false;
if($arr_appt["sa_app_start_date"] > date("Y-m-d")){
	$dyn_disble_future_appt = true;
}

//verification required data
if(isset($_REQUEST["sch_id"]) && !empty($_REQUEST["sch_id"])){
    $verification_data=$obj_scheduler->get_verification_data($_REQUEST["sch_id"]);
    $arr_appt["sa_verification_req"]=( isset($verification_data['v_required']) )? $verification_data['v_required']:0;
}


$intSelectedInsCase = 0;
include("fd_controls.php");
$sessionHeightInFD="";
//$sessionHeightInFD= $GLOBALS['gl_browser_name']=='ipad' ? $_SESSION["wn_height"] - 370 : $_SESSION['wn_height']-577;
$sessionHeightInFD= $_SESSION['wn_height']-599;
?><div class="clearfix"></div><div class="scptinfo"><div class="scroll-content mCustomScrollbar tablcont" id="main_content_area" style="height:<?php echo $sessionHeightInFD."px";?>; overflow:scroll;overflow-x:hidden;"><div class="patientdetail"><div class="row"><div class="col-sm-5 ptinfoara"><div class="ptinpd" id="display_area"><div class="row"><div class="col-sm-7"><ul><?php
$display_name = trim($arr_pat["title"]." ".$arr_pat["fname"]." ".(substr($arr_pat["mname"], 0 ,1))." ".$arr_pat['lname']." ".$arr_pat["suffix"]);?><li onDblClick="top.change_main_Selection(window.top.document.getElementById('Work_View'));" title="<?php echo $display_name;?>"><?php
$display_name = $obj_scheduler->reduce_display_string($display_name, 27, 25);								
echo $display_name;?></li><li class="link_cursor" <?php echo'onClick="showEditAddress(\'open\');"';?>><?php
$full_show_addr = core_address_format($arr_pat['street'], $arr_pat['street2'],'','','');

$full_show_addr = strip_tags($full_show_addr);
$show_addr = $obj_scheduler->reduce_display_string($full_show_addr,25);
echo ((trim($show_addr) != "") ? "<span title=\"".$full_show_addr."\">".$show_addr."</span>" : "N/A");
?></li><li class="link_cursor" <?php echo'onClick="showEditAddress(\'open\');"';?>><?php 
$full_show_addr = core_address_format('', '', $arr_pat['city'], $arr_pat['state'], $arr_pat['postal_code']);
if(trim($arr_pat['zip_ext'])!=''){$full_show_addr .= '-'.$arr_pat['zip_ext'];}
$full_show_addr = strip_tags($full_show_addr);
$show_addr = $obj_scheduler->reduce_display_string($full_show_addr,25);
echo ((trim($show_addr) != "") ? "<span title=\"".$full_show_addr."\">".$show_addr."</span>" : "N/A");
?></li><?php
$preferr_contact = $arr_pat["preferr_contact"];
$preferr_contact_style = "color:#0000ff;font-weight:bold;"
?><li class="link_cursor" <?php echo'onClick="showEditAddress(\'open\');"';?>><?php
if($preferr_contact == 2 && ($arr_pat['phone_cell'] != "" && $arr_pat['phone_cell'] != "000-000-0000"))
{echo '<span style="color:#0000ff;font-weight:bold;"><strong>C:</strong> '.core_phone_format($arr_pat['phone_cell']).'</span>';}
else
{$pc_biz = core_phone_format($arr_pat['phone_biz']);
if($preferr_contact == 1)
{
$pc_biz = '<span style="color:#0000ff;font-weight:bold;">'.$pc_biz.'</span>';	
}
echo ((trim($arr_pat['phone_biz']) != "" && $arr_pat['phone_biz'] != "000-000-0000") ? '<strong>W:</strong> '.$pc_biz : ((trim($arr_pat['phone_cell']) != "" && $arr_pat['phone_cell'] != "000-000-0000") ? '<strong>C:</strong> '.core_phone_format($arr_pat['phone_cell']) : "N/A"));	
}					
?></li><li class="link_cursor" style=" <?php if($preferr_contact == 0){ echo $preferr_contact_style; } ?>" <?php echo'onClick="showEditAddress(\'open\');"';?>><?php
echo"<strong>H: </strong>";
echo ((trim($arr_pat['phone_home']) != "" && $arr_pat['phone_home'] != "000-000-0000") ? core_phone_format($arr_pat['phone_home']) : "N/A");
?></li><li class="link_cursor" <?php echo'onClick="showEditAddress(\'open\');"';?>>
<?php echo '<strong>SS:</strong> '.((trim($arr_pat['ss']) != "" && trim($arr_pat['ss']) != "000-00-0000") ? $arr_pat['ss'] : "N/A"); ?></li>
<li class="link_cursor" <?php echo'onClick="showEditAddress(\'open\');"';?>><?php
$full_show_email = strip_tags($arr_pat['email']);
$show_email= $obj_scheduler->reduce_display_string($full_show_email, 28, 26);
echo ((trim($arr_pat['email']) != "") ? "<span title=\"".$full_show_email."\">".$show_email."</span>" : "N/A");
?></li></ul></div><div class="col-sm-5"><div class="ptimage" style="min-height:80px;"><span class="pt_alert_container portal" onClick="top.send_secure_msg();">P</span><span class="pt_alert_container" onClick="top.popup_win('<?php echo $GLOBALS['webroot'] ?>/interface/patient_info/demographics/patient_alert.php');">0</span><span id="patient_photo_container"></span><div class="ptidno">
<span style="cursor:hand;" onDblClick="javascript:top.core_redirect_to('Patient_Info', '<?php echo $GLOBALS["web_root"]; ?>/interface/patient_info/insurance/index.php');"><?php echo $arr_pat["id"];?></span><?php if($arr_pat['EMR']==1) echo '<b>e</b>';?></div></div>
<div class="clearfix"></div><div class="ptotinfo"><?php
$pt_dob = core_date_format($arr_pat['DOB']);
$pt_dob = get_date_format($pt_dob,'mm/dd/yyyy');
echo (($arr_pat['DOB'] != "" && $arr_pat['DOB'] != "0000-00-00") ? $pt_dob."  (".$obj_scheduler->get_age($arr_pat['DOB']).")" : "DOB: N/A");
?> </div><div class="ptotinfo"><?php echo ((trim($arr_pat['sex']) != "") ? $arr_pat['sex'] : "Sex: N/A"); ?>&nbsp; &nbsp;<a href="javascript:void(0);" <?php if(!isset($_REQUEST['server_id'])){ echo'onClick="showEditAddress(\'open\');"'; }?> class="status_icon"><?php echo $arr_pat["patientStatus"];?></a></div><div class="clearfix"></div><div class="ptotinfo" <?php echo'onClick="showEditAddress(\'open\');"';?>><div class="checkbox"><input id="photo_ref_view" name="photo_ref_view" type="checkbox"  onclick="return false;" <?php echo ($arr_pat["photo_ref"])?'checked':'';?>><label for="photo_ref_view">Photo Refused</label></div></div></div></div></div>
<?php 
$sql_proc_type = 'SELECT proc_type FROM slot_procedures WHERE id='.$arr_appt["procedureid"]; 
$resp_proc_type = imw_query($sql_proc_type); 
$resp_proc_type = imw_fetch_assoc($resp_proc_type);
if($resp_proc_type['proc_type'] === 'Telemedicine'):?>
   <div class="link_cursor">
	  <ul>
		 <li><strong>Appointment Synced With Telemedicine</strong>: <?php echo ($arr_appt['appt_sync_updox'] && trim($arr_appt['appt_sync_updox'])=='1') ? 'Yes' : 'No';?></li>
	  </ul>
   </div>
<?php 
endif;
if(is_updox('telemedicine') && $arr_appt !== false): if($resp_proc_type['proc_type'] === 'Telemedicine'): ?><div class="ssoAction"><button class="btn btn-success" name="add_appt" id="add_appt" onclick="javascript:launch_telemedicine();">Launch Telemedicine</button></div><?php endif; endif; ?><!--modal wrapper class is being used to control modal design--><div class="common_modal_wrapper"><!-- Modal --><div id="editable_area" class="modal fade" role="dialog"><div class="modal-dialog sm"><!-- Modal content--><div class="modal-content"><div class="modal-header bg-primary"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Update Patient Detail</h4></div><div class="modal-body"><div class="pd10"><div class="row pt10"><div class="col-sm-4"><div class="form-group"><B><?php echo $display_name;?></B> - <B><?php echo $arr_pat["id"];?></B></div></div><div class="col-sm-4">
<div class="form-group"><select name="elem_patientStatus" id="elem_patientStatus" class="form-control minimal" onChange="showPateintOtherTxtBox(this.id,'otherPatientStatus','Transferred-Moved-Other','<?php echo $arr_pat["otherPatientStatus"]; ?>');" data-prev-val="<?php echo $arr_pat["patientStatus"];?>"><?php 
$arr_pt_status = core_pt_status_list();
echo core_make_select_options($arr_pt_status, "pt_status_name", "pt_status_name", $arr_pat["patientStatus"]);
?></select></div></div><div class="col-sm-4"><div class="form-group"><?php 
if($arr_pat["patientStatus"]=="Transferred" || $arr_pat["patientStatus"]=="Moved" || $arr_pat["patientStatus"]=="Other"){ 
$display= "inline-block";
}else{
$display= "none";
}
$dod_display= "none";
if($arr_pat["patientStatus"]=="Deceased"){
$dod_display= "inline-block";
}
?><div id="dod_patient_td" style="display:<?php print $dod_display; ?>;"><input class="date-pick form-control" name="dod_patient" id="dod_patient" title="MM-DD-YYYY" value="<?php if($arr_pat[" dod_patient "] != '0000-00-00'){ echo get_date_format($arr_pat["dod_patient "]); } ?>" placeholder="MM-DD-YYYY"/>
</div>
<div id="tdOtherPatientStatus" style="display:<?php echo $display; ?>;"><input name="otherPatientStatus" id="otherPatientStatus" style="display:<?php echo $display; ?>;" class="form-control" value="<? echo $arr_pat[" otherPatientStatus "] ?>" placeholder="Other Status"/>
</div>
</div>
</div>
</div>
<div class="row pt10">
	<div class="col-sm-6">
		<div class="form-group"><input type="text" name="frontAddressStreet" id="frontAddressStreet" value="<?php echo ((trim($arr_pat['street']) != " ") ? trim($arr_pat['street']) : "Street 1 ");?>" class="form-control" onclick="javascript:if(this.value == 'Street 1'){ this.value = '';  }" onblur="javascript:if(this.value == ''){ this.value = 'Street 1';  }"/>
			<input type="hidden" name="hidd_prev_frontAddressStreet" id="hidd_prev_frontAddressStreet" value="<?php echo trim($arr_pat['street']);?>">
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-group"><input type="text" name="frontAddressStreet2" id="frontAddressStreet2" value="<?php echo ((trim($arr_pat['street2']) != " ") ? trim($arr_pat['street2']) : "Street 2 ");?>" class="form-control" onclick="javascript:if(this.value == 'Street 2'){ this.value = '';  }" onblur="javascript:if(this.value == ''){ this.value = 'Street 2'; }"/>
			<input type="hidden" name="hidd_prev_frontAddressStreet2" id="hidd_prev_frontAddressStreet2" value="<?php echo trim($arr_pat['street2']);?>">
		</div>
	</div>
</div>
<div class="row pt10">
	<div class="col-lg-4 col-md-4 col-sm-8"><input type="text" name="frontAddressZip" id="frontAddressZip" value="<?php echo ((trim($arr_pat['postal_code']) != " ") ? trim($arr_pat['postal_code']) : "Zip Code ");?>" class="form-control" onclick="javascript:if(this.value == 'Zip Code'){ this.value = ''; }" onblur="javascript:if(this.value == ''){ this.value = 'Zip Code';  }" maxlength="<?php echo inter_zip_length();?>"/>
			<input type="hidden" name="hidd_prev_frontAddressZip" id="hidd_prev_frontAddressZip" value="<?php echo trim($arr_pat['postal_code']);?>">
	</div>
	<div class="col-lg-2 col-md-2 col-sm-4">
		<?php if(inter_zip_ext()){?><input type="text" name="frontAddressZip_ext" id="frontAddressZip_ext" value="<?php echo ((trim($arr_pat['zip_ext']) != " ") ? trim($arr_pat['zip_ext']) : "Ext ");?>" class="form-control" onclick="javascript:if(this.value == 'Ext'){ this.value = '';}" onblur="javascript:if(this.value == ''){ this.value = 'Ext';  }" maxlength="4"/>
			<input type="hidden" name="hidd_prev_frontAddressZip_ext" id="hidd_prev_frontAddressZip_ext" value="<?php echo trim($arr_pat['zip_ext']);?>">
		<?php };?>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-8"><input type="text" name="frontAddressCity" id="frontAddressCity" value="<?php echo ((trim($arr_pat['city']) != " ") ? trim($arr_pat['city']) : "City ");?>" class="form-control" onclick="javascript:if(this.value == 'City'){ this.value = ''; }" onblur="javascript:if(this.value == ''){ this.value = 'City'; }"/>
			<input type="hidden" name="hidd_prev_frontAddressCity" id="hidd_prev_frontAddressCity" value="<?php echo trim($arr_pat['city']);?>">
	</div>
	<div class="col-lg-2 col-md-2 col-sm-4"><input type="text" name="frontAddressState" id="frontAddressState" value="<?php echo ((trim($arr_pat['state']) != " ") ? trim($arr_pat['state']) : "State ");?>" class="form-control" onclick="javascript:if(this.value == 'State'){ this.value = '';}" onblur="javascript:if(this.value == ''){ this.value = 'State';  }"/>
			<input type="hidden" name="hidd_prev_frontAddressState" id="hidd_prev_frontAddressState" value="<?php echo trim($arr_pat['state']);?>">
	</div>
</div>
<?php
$load_h_ph = ( ( $arr_pat[ 'phone_home' ] != "" && $arr_pat[ 'phone_home' ] != "000-000-0000" ) ? core_phone_format( $arr_pat[ 'phone_home' ] ) : "Home Phone" );
$load_h_ph_cl = ( ( $arr_pat[ 'phone_home' ] != "" && $arr_pat[ 'phone_home' ] != "000-000-0000" ) ? "sc_blank" : "sc_input_label" );
?>
<div class="row pt10">
	<div class="col-lg-4 col-md-4 col-sm-12">
		<div class="form-group"><input type="text" name="phone_home" id="phone_home" value="<?php echo $load_h_ph;?>" onclick="javascript:if(this.value == 'Home Phone'){ this.value = ''; }" onblur="javascript:if(this.value == ''){ this.value = 'Home Phone';  }; " onchange="set_phone_format(this,'<?php echo inter_phone_format();?>')" class="form-control"/>
			<input type="hidden" name="hidd_prev_phone_home" id="hidd_prev_phone_home" value="<?php echo $load_h_ph;?>">
		</div>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-12">
		<?php
		$load_w_ph = ( ( $arr_pat[ 'phone_biz' ] != "" && $arr_pat[ 'phone_biz' ] != "000-000-0000" ) ? core_phone_format( $arr_pat[ 'phone_biz' ] ) : "Work Phone" );
		?>
		<input type="text" name="phone_biz" id="phone_biz" value="<?php echo $load_w_ph;?>" onclick="javascript:if(this.value == 'Work Phone'){ this.value = '';  }" onblur="javascript:if(this.value == ''){ this.value = 'Work Phone';  }" onchange="set_phone_format(this,'<?php echo inter_phone_format();?>')" class="form-control"/>
			<input type="hidden" name="hidd_prev_phone_biz" id="hidd_prev_phone_biz" value="<?php echo $load_w_ph;?>">
	</div>
	<div class="col-lg-4 col-md-4 col-sm-12">
		<?php
		$load_c_ph = ( ( $arr_pat[ 'phone_cell' ] != "" && $arr_pat[ 'phone_cell' ] != "000-000-0000" ) ? core_phone_format( $arr_pat[ 'phone_cell' ] ) : "Cell Phone" );
		?><input type="text" name="phone_cell" id="phone_cell" value="<?php echo $load_c_ph;?>" onclick="javascript:if(this.value == 'Cell Phone'){ this.value = '';  }" onblur="javascript:if(this.value == ''){ this.value = 'Cell Phone';  }" onchange="set_phone_format(this,'<?php echo inter_phone_format();?>')" class="form-control"/>
			<input type="hidden" name="hidd_prev_phone_cell" id="hidd_prev_phone_cell" value="<?php echo $load_c_ph;?>">
	</div>
</div>
<div class="row pt10">
	<div class="col-lg-8 col-md-8 col-sm-8">
		<div class="form-group"><input type="text" name="email" id="email" value="<?php echo $arr_pat['email'];?>" class="form-control" onclick="javascript:if(this.value == 'Email'){ this.value = '';  }" onblur="javascript:if(this.value == ''){ this.value = 'Email';  }"/>
			<input type="hidden" name="hidd_prev_email" id="hidd_prev_email" value="<?php echo $arr_pat['email'];?>">
		</div>
	</div>
	<div class="col-lg-4">
		<div class="checkbox"><input id="photo_ref" name="photo_ref" type="checkbox" value="1" <?php echo ($arr_pat[ "photo_ref"])? 'checked': '';?>><label for="photo_ref">Photo Refused</label>
		</div></div></div></div></div><div class="modal-footer"><button type="button" class="btn btn-success" onclick="javascript:before_save_changes('<?php echo $_SESSION["patient"];?>', '<?php echo $_REQUEST["sch_id"];?>','<?php echo $arr_appt['facility_type_provider']; ?>');">Save</button><button type="button" class="btn btn-danger" data-dismiss="modal">Close</button></div></div></div></div></div><!--modal wrapper class end here --></div>
<div class="col-sm-7"><div class="ptoptfrm"><div class="row ptoption" id="appt_data"><div class="row"><?php
if($arr_appt !== false){
$pri_eye_site=$arr_appt['procedure_site'];
}else{
$pri_eye_site=$_REQUEST["force_pri_site"];
}
?><div class="col-sm-4 sitepost"><div class="form-group"><label for="">Primary</label><figure><div class="dropdown"><input type="hidden" name="pri_eye_site" id="pri_eye_site" value="<?php echo $pri_eye_site;?>"><input type="hidden" name="procedure_site" id="procedure_site"><?php  
$options=$obj_scheduler->eye_site();
foreach($options as $k=>$v){
$MenuOptions[] = array($v,$arrEmpty,$k);
}
echo $obj_scheduler->get_simple_menu_sch($MenuOptions,"pri_eye_site_menu","pri_eye_site",$options[$pri_eye_site]);?></div></figure>
<?php	
$selected_proc = "";
if($arr_appt !== false){
$selected_proc = $arr_appt["procedureid"];
}else{
$selected_proc = $_REQUEST["force_proc"];
}
$default_procedure_id = $obj_scheduler->doctor_proc_to_default_proc($selected_proc);
list($list_of_procs, $arr_proc_names,$user_proc) = $obj_scheduler->load_procedures($default_procedure_id);
$select_disable=$style_backgroup="";
if($user_proc=='1'){
$select_disable='disabled="disabled"';	
$style_backgroup="background:#CCC;";
}
?><select name="sel_proc_id" id="sel_proc_id" <?php echo $select_disable; ?> onChange="show_test(this.value,'<?php echo $_REQUEST["sch_id"];?>');chk_referral('proc_sel');chk_verif_sheet('proc_sel');" class="form-control minimal" onmouseover="javascript:show_proc_fullname(this.value);"><option value="">-Reason-</option><?php echo $list_of_procs;?>
</select><?php 
if(is_array($arr_proc_names) && count($arr_proc_names) > 0){
foreach($arr_proc_names as $lopid => $lopnm){
?><div id="proc<?php echo $lopid; ?>" style="display:none;"><?php echo $lopnm;?></div><?php
}}
?></div></div><?php
if($arr_appt !== false){
$sec_eye_site=$arr_appt['procedure_sec_site'];
}else{
$sec_eye_site=$_REQUEST["force_sec_site"];
}
?><div class="col-sm-4 sitepost"><div class="form-group"><label for="">Secondary</label><figure><div class="dropdown"><input type="hidden" name="sec_eye_site" id="sec_eye_site" value="<?php echo $sec_eye_site;?>"><?php  
echo $obj_scheduler->get_simple_menu_sch($MenuOptions,"sec_eye_site_menu","sec_eye_site", $options[$sec_eye_site]);
?></div></figure><?php
$selected_proc = "";
if($arr_appt !== false){
$selected_proc = $arr_appt["sec_procedureid"];
}else{
$selected_proc = $_REQUEST["force_proc2"];
}
$default_procedure_id = $obj_scheduler->doctor_proc_to_default_proc($selected_proc);
list($list_of_procs, $arr_proc_names,$user_proc_sec) = $obj_scheduler->load_procedures($default_procedure_id);
$select_disable=$style_backgroup="";
if($user_proc_sec=='1'){
$select_disable='disabled="disabled"';	
$style_backgroup="background:#CCC;";
}							
?><select name="sec_sel_proc_id" id="sec_sel_proc_id" <?php echo $select_disable; ?> onChange="show_test(this.value,'<?php echo $_REQUEST["sch_id"];?>');" class="form-control minimal" onmouseover="javascript:show_proc_fullname(this.value);" title=""><option value="">-Reason-</option><?php echo $list_of_procs;?>
</select></div></div><?php
if($arr_appt !== false){
$ter_eye_site=$arr_appt['procedure_ter_site'];
}else{
$ter_eye_site=$_REQUEST["force_ter_site"];
}
?><div class="col-sm-4 sitepost"><div class="form-group"><label for="">Tertiary</label><figure><div class="dropdown"><input type="hidden" name="ter_eye_site" id="ter_eye_site" value="<?php echo $ter_eye_site;?>"><?php  
echo $obj_scheduler->get_simple_menu_sch($MenuOptions,"ter_eye_site_menu","ter_eye_site", $options[$ter_eye_site],'','dropdown-menu-right');
?></div></figure><?php	
$selected_proc = "";
if($arr_appt !== false){
$selected_proc = $arr_appt["tertiary_procedureid"];
}else{
$selected_proc = $_REQUEST["force_proc3"];
}
$default_procedure_id = $obj_scheduler->doctor_proc_to_default_proc($selected_proc);
list($list_of_procs, $arr_proc_names,$user_proc_tri) = $obj_scheduler->load_procedures($default_procedure_id);
$select_disable=$style_backgroup="";
if($user_proc_tri=='1'){
$select_disable='disabled="disabled"';	
$style_backgroup="background:#CCC;";
}	
?><select name="ter_sel_proc_id" id="ter_sel_proc_id"  <?php echo $select_disable; ?> onChange="show_test(this.value,'<?php echo $_REQUEST["sch_id"];?>');" class="form-control minimal" onmouseover="javascript:show_proc_fullname(this.value);" title=""><option value="">-Reason-</option><?php echo $list_of_procs;?>
</select></div></div></div><div class="row"><div class="col-sm-3"><div class="form-group"><label for="">Surgeon</label><?php
$fac_options_val=$display_fac_drop="none";
$facility_type_provider=$arr_appt['facility_type_provider'];

$qry_select="SELECT id,concat(lname,', ',fname) as username FROM users where user_type='1' order by lname, fname";
$res_select=imw_query($qry_select)or die(imw_error());
while($row_select=imw_fetch_assoc($res_select)){ 
$id=$row_select['id'];
$name=$row_select['username'];
$selected_fpc="";
if($facility_type_provider==$id){
$selected_fpc=" SELECTED ";
}
$fac_options_val.="<option ".$selected_fpc." value='".$id."'>".$name."</option>";	
}
?>
<select id="facility_type_provider" name="facility_type_provider" class="form-control minimal"><option value=""></option><?php echo $fac_options_val; ?></select></div></div><div class="col-sm-3"><div class="form-group"><label for="">Exp. Arrival Time</label><input type='text' id="arrival_time" name="arrival_time" value="<?php echo $arr_appt['arrival_time']; ?>" class="form-control"></div></div><?php
$copay_pol_qry = "SELECT vip_copay_not_collect, vip_ref_not_collect, vip_bill_not_pat, no_balance_bill,fd_pc,accept_assignment FROM copay_policies";
$copay_pol_obj = imw_query($copay_pol_qry);
$copay_pol_result = imw_fetch_assoc($copay_pol_obj);

if(DEFAULT_PRODUCT != "imwemr"){?><div class="col-sm-6"><!--contact lens data here--><div id="cl_data" class="contact_lens_data"><div class="row"><div class="col-sm-8"><?php
if(trim($arr_trial_cost[0]) == "Evaluation"){
$arr_trial_cost[0] = "Eval";
}
if($arr_trial_cost[0]!=''){
$arr_trial_cost[0]=str_replace(",", ", ", $arr_trial_cost[0]);
if(strlen($arr_trial_cost[0])>30){ $arr_trial_cost[0]=substr($arr_trial_cost[0], 0, 30).'..'; }
}
$arr_trial_cost[1] = (substr($arr_trial_cost[1],0,1) == '$') ? $arr_trial_cost[1] : "$".$arr_trial_cost[1];
echo ((trim($arr_trial_cost[0]) != "") ? "<B>CL(".$arr_trial_cost[0]."):</B>".$arr_trial_cost[1] : "<B>CL(Ord.):</B>".show_currency()."0.00");
?></div><div class="col-sm-4"><?php 
echo ((!empty($last_final_rx_id)) && ($final_cl_rx_status == 1) ? "<span style=\"color:#9900CC;cursor:hand;cursor:pointer;\" onClick=\"javascript:printContactRx('1','".$last_final_rx_id."')\"><B>CL Rx</B></span>" : "No CL");
?></div></div><div class="row"><div class="col-sm-8"><?php  
echo (($arr_ordered_supply[0] == true || trim($arr_ordered_supply[0])=='1') ? "<B>CL(Sup.):</B>".show_currency().$arr_ordered_supply[1] : "<B>CL(Sup.):</B> ".show_currency()."0.00");
?></div><div class="col-sm-4"><?php
echo (($vis_mr_none_given != "") ? "<span style=\"color:#9900CC;cursor:hand;cursor:pointer;\" onClick=\"javascript:printMr('".$vis_mr_none_given."');\"><B>GL Rx</B></span>" : "No GL");
$result_pc = $obj_contactlens->pc_data_existence($sel_date,$pat_id);
if($copay_pol_result['fd_pc']=='1'){
if($result_pc > 0 && $result_pc != ""){						
?><span style="color:#9900CC;cursor:hand;cursor:pointer;" onClick="print_vision_pc_1(<?php echo $result_pc; ?>);" ><B> PC</B></span><?php }else{ echo ' PC'; }} ?></div></div></div></div><?php }else{?><div class="col-sm-3" id="arrival_pickup_data"><div class="form-group"><label for="">Pick Up Time</label><input id='pick_up_time' type='text' name="pick_up_time" value="<?php echo $arr_appt['pick_up_time']; ?>" class="form-control"  maxlength="10"></div></div><?php }
?></div><div class="row"><div class="col-sm-6"><div class="checkbox"><input id="sa_ref_management" name="sa_ref_management" type="checkbox" <?php echo ($arr_appt['sa_ref_management']?'checked':'');?> /><label for="sa_ref_management" style="padding-top:3px;">Referral Required</label></div></div>
    <div class="col-sm-6"><div class="checkbox"><input id="sa_verification_req" name="sa_verification_req" type="checkbox" <?php echo ($arr_appt['sa_verification_req']?'checked':'');?> /><label for="sa_verification_req" style="padding-top:3px;">Auth/Verify Required</label></div></div></div>
<?php
    $interpretter_check_qry = "SELECT interpreter_type,interpretter FROM patient_data WHERE id='".$_SESSION['patient']."'";
    $interpretter_check_obj = imw_query($interpretter_check_qry);
    $interpretter_check_result = imw_fetch_assoc($interpretter_check_obj);
    if($interpretter_check_result['interpreter_type'] !="" && $interpretter_check_result['interpretter'] !="")
    {
?>
 		<div class="row"><strong>Patient is coming with Interpreter - <?php echo $interpretter_check_result['interpretter'] ?>, <?php echo $interpretter_check_result['interpreter_type'] ?></strong></div>
<?php
	}else if($interpretter_check_result['interpreter_type'] !="" && $interpretter_check_result['interpretter'] =="") 
	{   
?>
		<div class="row"><strong>Patient is coming with Interpreter - <?php echo $interpretter_check_result['interpreter_type'] ?></strong></div>
<?php
	}else if($interpretter_check_result['interpreter_type'] =="" && $interpretter_check_result['interpretter'] !="")
	{ 
?>
		<div class="row"><strong>Patient is coming with Interpreter - <?php echo $interpretter_check_result['interpretter'] ?></strong></div>
<?php
	}
?>
<?php
$superbill_vip=0;
$qry_superbill_vip="SELECT sb.vipSuperBill as super_bill_vip,cmt.date_of_service as dos from superbill as sb INNER JOIN chart_master_table as cmt on(cmt.id=sb.formId AND cmt.patient_id=sb.patientId) where sb.vipSuperBill=1 and cmt.date_of_service='".trim($_REQUEST['sel_date'])."' and cmt.patient_id='".($_SESSION['patient'])."' limit 0,1";
$res_superbill_vip=imw_query($qry_superbill_vip);
if(imw_num_rows($res_superbill_vip)>0){
$row_superbill_vip=imw_fetch_assoc($res_superbill_vip);
if($row_superbill_vip['super_bill_vip']==1){
$superbill_vip=1;	
}
}
$vip_check_qry = "SELECT vip FROM patient_data WHERE id='".$_SESSION['patient']."'";
$vip_check_obj = imw_query($vip_check_qry);
$vip_check_result = imw_fetch_assoc($vip_check_obj);
if($vip_check_result['vip']==1 || $superbill_vip==1)
{
?>
<!--<div  class="col-sm-6" style="cursor:pointer" onClick="$('#divInfo').toggle('medium');"><b>Patient is VIP:</b>-->
<?php 
$vip_copay_status_arr = array();
if($copay_pol_result['vip_copay_not_collect'] == 1)
{
$vip_copay_status_arr[]	= 'No Copay';
}
if($copay_pol_result['vip_ref_not_collect'] == 1)
{
$vip_copay_status_arr[]	= 'No Refraction';
}
if($copay_pol_result['vip_bill_not_pat'] == 1)
{
$vip_copay_status_arr[]	= 'No Bill';
}
if($copay_pol_result['no_balance_bill'] == 1)
{
$vip_copay_status_arr[]	= 'No Balance';
}																		

$vip_status_str = implode(', ',$vip_copay_status_arr);
$vip_screen_disp = $vip_status_str;
/*if(strlen($vip_screen_disp)>10){
$vip_screen_disp=substr($vip_screen_disp, 0, 7)."...";
}
echo $vip_screen_disp;*/
?><!--</div>--><div class="row"><div class="col-sm-12" id="divInfo" style="color:#F00"><b>Patient is VIP:</b><?php echo $vip_status_str;?></div></div><?php }?><div class="pcp_group"><div class="row "><div class="col-sm-4"><div class="form-group"><label for="front_primary_care_name" title="<?php echo($arr_pat['primary_care']);?>">Ref. Physician</label><div class="input-group"><?php $priCareName = $arr_pat['primary_care'];
$priCareName = $OBJCommonFunction->get_ref_phy_name($arr_pat['primary_care_id']);
?><input type="hidden" name="front_primary_care_id"  id="front_primary_care_id" value="<?php print $arr_pat['primary_care_id'];?>">
<input type="hidden" id="hidd_front_primary_care_name" name="hidd_front_primary_care_name" value="<?php echo($arr_pat['primary_care']);?>"><input name="front_primary_care_name" title="<?php echo($arr_pat['primary_care']).$refPhyOpCmnt;?>" type="text" class="form-control <?php echo $ref_phy_class;?>" id="front_primary_care_name" value="<?php echo($priCareName);?>" onkeyup="top.loadPhysicians(this,'front_primary_care_id')" onFocus="top.loadPhysicians(this,'front_primary_care_id')" autocomplete="off"><div class="input-group-addon link_cursor" onClick="searchPhysicianWindow();"><span class="addsearch" aria-hidden="true"></span></div></div></div></div><div class="col-sm-4"><div class="form-group"><label title="<?php echo($arr_pat['primary_care_phy_name']);?>" for="pcp_name">P.C.P</label><div class="input-group"><?php 
$pcpName = $arr_pat['primary_care_phy_name'];
$pcpName = $OBJCommonFunction->get_ref_phy_name($arr_pat['primary_care_phy_id']);
?><input type="hidden" name="pcp_id"  id="pcp_id" value="<?php print $arr_pat['primary_care_phy_id'];?>"><input type="hidden" id="hidd_pcp_name" name="hidd_pcp_name" value="<?php echo $arr_pat['primary_care_phy_name'];?>"><input name="pcp_name" title="<?php echo($pcpName);?>" type="text" class="form-control <?php echo $pri_care_class; ?>" id="pcp_name" value="<?php echo($pcpName);?>"  onkeyup="top.loadPhysicians(this,'pcp_id')" onFocus="top.loadPhysicians(this,'pcp_id')" autocomplete="off"><div class="input-group-addon link_cursor" onClick="searchPCPWindow();"><span class="addsearch" aria-hidden="true"></span></div></div></div></div><div class="col-sm-4"><div class="form-group"><label for="">Physician</label><?php			
if(!empty($arr_pat["providerID"])){																	
$prov_fields = "id, fname, lname, mname";
$arr_def_prov = $obj_scheduler->get_provider_details($arr_pat["providerID"], $prov_fields);
echo "<br/><input type='hidden' value='".$arr_pat["providerID"]."' id='sel_fd_provider' name='sel_fd_provider'>
<input type='text' name='phyNameDis' id='phyNameDis' class='form-control' value=\"".$arr_def_prov['fname']." ".$arr_def_prov['lname']."\" title=\"".$arr_def_prov['fname']." ".$arr_def_prov['lname']."\" readonly>";
}else{
$arr_prov_list = $obj_scheduler->get_provider_list();
?>
<select name='sel_fd_provider' id="sel_fd_provider" onChange="show_test(document.getElementById('sel_proc_id').value);" class="form-control minimal"><option value=''>--Provider--</option><?php
for($p = 0; $p < count($arr_prov_list); $p++){
echo "<option value=\"".$arr_prov_list[$p]["id"]."\">".core_name_format($arr_prov_list[$p]["lname"], $arr_prov_list[$p]["fname"], $arr_prov_list[$p]["mname"])."</option>";
}
?></select><?php } ?></div></div></div></div></div></div></div></div><div class="clearfix"></div><div class="aptcomt"><div class="form-group"><?php 								
if($arr_appt !== false){
$appt_action_type = "update_appt";
?><div class="apottime"><B>Appt.:</B><?php
$sec_ter_acronym = '';
if($slot_proc_sec_ter_arr[$arr_appt['sec_procedureid']]['acronym']!="")
{
if($slot_proc_sec_ter_arr_new[$arr_appt[0]['sec_procedureid']]['acronym']!="") {
$sec_ter_acronym .= ', '.$slot_proc_sec_ter_arr_new[$arr_appt[0]['sec_procedureid']]['acronym'];	
}else {
$sec_ter_acronym .= ', '.$slot_proc_sec_ter_arr[$arr_appt[0]['sec_procedureid']]['acronym'];
}
if($slot_proc_sec_ter_arr[$arr_appt['tertiary_procedureid']]['acronym']!="")
{
if($slot_proc_sec_ter_arr_new[$arr_appt[0]['tertiary_procedureid']]['acronym']!="") {
$sec_ter_acronym .= ', '.$slot_proc_sec_ter_arr_new[$arr_appt[0]['tertiary_procedureid']]['acronym'];
}else {
$sec_ter_acronym .= ', '.$slot_proc_sec_ter_arr[$arr_appt[0]['tertiary_procedureid']]['acronym'];	
}
}
}
echo $physician_name;?> &nbsp;<?php echo $arr_appt["name"]."  ".get_date_format($arr_appt["sa_app_start_date"], "m-d-Y")." ".$arr_appt['acronym'].$sec_ter_acronym;?>&nbsp;<span style="cursor:pointer;color:#FF6600;" onClick="load_calendar('<?php echo $arr_appt["sa_app_start_date"];?>', '<?php echo date("l", strtotime($arr_appt["sa_app_start_date"]));?>', '', '', '<?php echo (($arr_appt !== false) ? $arr_appt["id"] : -1);?>');"> <?php echo core_time_format($arr_appt["sa_app_starttime"]);$END_TIME_DURA=(int)$arr_appt["sa_app_duration"]/60;
$END_TIME_SLOT=240;//$time_slot;
$option_val="";
list($time_hr,$tm_min)=explode(":",$arr_appt["sa_app_starttime"]);
for($T=5;$T<=$END_TIME_SLOT;$T=$T+5){
$sele=""; if($T==$END_TIME_DURA){$sele=" SELECTED ";} 
$show_slot_time=date("h:i A", mktime($time_hr, $tm_min + $T,0));
$option_val.="<option value=".$T." ".$sele.">".$show_slot_time."</option>";
}
?></span> End Time: <select name="appt_duration" id="appt_duration" class="form-control minimal" ><?php echo $option_val; ?></select><input type="hidden" value="<?php echo $END_TIME_DURA; ?>" id="chk_prev_slot_val"></span></div><div class="clearfix"></div><textarea class="form-control" name="txt_comments" id="txt_comments" rows="2" onclick="javascript:if(this.value == 'Appointment Comment'){ this.value = ''; }" onblur="javascript:if(this.value == ''){ this.value = 'Appointment Comment'; }" <?php if($_REQUEST["sch_id"]=='') { echo "disabled=\"disabled\""; } ?> ><?php echo (trim($arr_appt["sa_comments"]) == "" || trim($arr_appt["sa_comments"]) == "undefined" ? "Appointment Comment" : $arr_appt["sa_comments"]); ?></textarea><textarea  style="display:none;" class="form-control" name="txt_commentsTemp" id="txt_commentsTemp" rows="2"><?php echo (trim($arr_appt["sa_comments"]) == "" || trim($arr_appt["sa_comments"]) == "undefined" ? "Appointment Comment" : $arr_appt["sa_comments"]); ?></textarea><?php 
}else{
$appt_action_type = "add_appt";
?><textarea name="txt_comments" id="txt_comments" rows="2" class="form-control" onclick="javascript:if(this.value == 'Appointment Comment'){ this.value = ''; }" onblur="javascript:if(this.value == ''){ this.value = 'Appointment Comment'; }" <?php echo (trim($_REQUEST["force_comment"]) == "" || trim($_REQUEST["force_comment"]) == "undefined" ? "disabled=\"disabled\"" : ""); ?>><?php echo (trim($_REQUEST["force_comment"]) == "" || trim($_REQUEST["force_comment"]) == "undefined" ? "Appointment Comment" : $_REQUEST["force_comment"]); ?></textarea><textarea style="display:none;" class="form-control" name="txt_commentsTemp" id="txt_commentsTemp" rows="2"><?php echo (trim($_REQUEST["force_comment"]) == "" || trim($_REQUEST["force_comment"]) == "undefined" ? "Appointment Comment" : $_REQUEST["force_comment"]); ?></textarea><?php 
}?></div></div><div class="clearfix"></div><?php
$pt_due_chk=str_replace(show_currency(),'',$pt_due);
$ins_due_chk=str_replace(show_currency(),'',$ins_due);
if($pt_due_chk>0){
$txt_pt_col="color:#F00;";
}
if($ins_due_chk>0){
$txt_ins_col="color:#F00;";
}
?><div class="ptamount"><ul><li><strong>CoPay :</strong>  <span id="cpays"><!--$0.00--></span></li><li><strong>Pt. Due :</strong>  <span class="div_td" style=" <?php echo $txt_pt_col; ?>"><?php print($pt_due);?></span></li><li><strong>Ins. Due :</strong>  <span class="div_td" style=" <?php echo $txt_ins_col; ?>"><?php print($ins_due);?></span></li><li><strong>Balance :</strong>  <span><?php echo $outstanding_bal; ?></span></li></ul></div><div class="clearfix"></div></div><div class="clearfix"></div><div class="patientdetail"><div class="boxheader"><div class="row"><div class="col-sm-4"><h2 onDblClick="javascript:top.change_main_Selection(top.document.getElementById('Insurance'));">Insurance Plan</h2></div><div class="col-sm-8 form-inline inspos"><button class="btn btn-success" name="btnsaveInsurance" id="btnsaveInsurance" onClick="javascript:submitFrontInsuraceForm();">Save Insurance</button><div id="RoutineExamVisionCase" style="display:none;">Routine Exam:&nbsp;
<!--<input type="checkbox" name="chkRoutineExam" id="chkRoutineExam" class="input_text_10" value="Yes" <?php if($arr_appt["RoutineExam"] == "Yes"){ echo "checked"; }?> />--><label class="control control--checkbox"><input type="checkbox" name="chkRoutineExam" id="chkRoutineExam" value="Yes" <?php if($arr_appt["RoutineExam"] == "Yes"){ echo "checked"; }?>><div class="control__indicator"></div></label></div><div id="accept_assignment_div" style="font-weight:bold; margin-right:10px; margin-left:10px; line-height:30px;"></div><select id="choose_prevcase" name="choose_prevcase" onChange="javascript:load_insurance(this.value);" class="form-control minimal"><?php
$qry_default_sel="select ic.ins_caseid, ic.ins_case_type from insurance_case as ic inner join insurance_case_types as ict on(ict.case_id=ic.ins_case_type) where ic.patient_id = '".$pat_id."' and ic.case_status = 'Open' and ict.default_selected=1";
$res_default_sel=imw_query($qry_default_sel);
if(imw_num_rows($res_default_sel)>0){
$row_default_sel=imw_fetch_assoc($res_default_sel);
$pt_ins_case_id=$row_default_sel['ins_caseid'];
}
$int_match_case_id = (empty($arr_appt["case_type_id"]) == false) ? $arr_appt["case_type_id"] : $pt_ins_case_id;
$arr_ptins_cases = $obj_accounting->get_patient_ins_cases($pat_id,"");
$sel_self_pay="";
if($arr_ptins_cases !== false){
for($ic = 0; $ic < count($arr_ptins_cases); $ic++){
$sel = "";
if($arr_ptins_cases[$ic]["ins_caseid"] == $int_match_case_id){
$intSelectedInsCase = (int)$int_match_case_id;
if($sel_self_pay==""){$sel = "selected";}
}
if($arr_ptins_cases[$ic]["case_status"] == "Close"){
$case_color = "#CC0000";
$siffix = "(Closed)";
}else{
$case_color = "";
$siffix = "";
}
echo "<option value=\"".$arr_ptins_cases[$ic]["ins_caseid"]."\" ".$sel." style=\"color:".$case_color."\">".$arr_ptins_cases[$ic]["case_name"]."-".$arr_ptins_cases[$ic]["ins_caseid"].$siffix."</option>";	

}
if(($arr_appt["case_type_id"])==0 && trim($arr_appt["case_type_id"]) !=""){$sel_self_pay=" selected ";}
echo "<option value='0' ".$sel_self_pay." >Self Pay</option>";
}else{
echo "<option value=\"0\">No Case</option>";
}
?></select><span class="inshx pointer" style="margin-top:-4px;" onClick="top.fmain.loadInsHx();">ins<br>hx</span></div></div></div><div class=" clearfix"></div><div class="table-responsive respotable" id="load_pt_insurance"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/sch-loader.gif" align="middle" /></div><div class=" clearfix"></div></div><div class="clearfix"></div><div class="patientdetail"><div class="boxheader"><h2>Appointments</h2></div><div class=" clearfix"></div><div class="table-responsive respotable" id="load_pt_appointments"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/sch-loader.gif" align="middle" /></div><div class=" clearfix"></div>
</div><div class="clearfix"></div><div class="patientdetail" id="inner_pages_header" style="display:none;"><div class="boxheader"><h2>Appointments</h2></div>
<div class=" clearfix"></div><div class="table-responsive respotable" id="load_pt_appointments"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/sch-loader.gif" align="middle" /></div><div class=" clearfix"></div></div></div></div><?php
$strElLinkInnerHTML = $vsToolTip = $strEBResponce = $imgRealTimeEli = $dbRespDDC = $dbRespCopay = $dbRespCoIns = $strRTEAmtInfo = "";
$intTotDeductibleAmt = $intTotCopayAmt = 0;
$intDDCAmt = $intCopayAmt = 0;
$strCoInsAmt = "";
$arrRespDDC = $arrRespCopay = $arrRespCoIns = array();
$strAppDate = $arr_appt["sa_app_start_date"];
if((constant("ENABLE_REAL_ELIGILIBILITY") == "YES") && (isset($_REQUEST["sch_id"]) == true) && (empty($_REQUEST["sch_id"]) == false) && ((int)$intSelectedInsCase > 0)){
	$qryPatIns = "SELECT insData.id as insId, insComp.claim_type as claimType, insComp.id as compId
				FROM insurance_data insData INNER JOIN insurance_companies insComp
				ON insComp.id = insData.provider 
				WHERE insData.ins_caseid = '".$intSelectedInsCase."' 
				AND insData.pid = '".$_SESSION['patient']."'
				AND insData.actInsComp = '1'
				AND insData.type = 'primary'
				ORDER BY insData.effective_date DESC LIMIT 1";
	$rsPatIns = imw_query($qryPatIns);
	if(imw_num_rows($rsPatIns) > 0){
		$objRowPatIns = imw_fetch_object($rsPatIns);
		//pre(var_dump($objRowPatIns),1);
		$qryGetRealTimeData = "SELECT DATE_FORMAT(rtme.responce_date_time, '%m-%d-%Y %I:%i %p') as vs270RespDate, rtme.transection_error as vsTransectionError, rtme.EB_responce as vsEBLoopResp, CONCAT(SUBSTRING(us.fname,1,1),SUBSTRING(us.lname,1,1),SUBSTRING(us.mname,1,1)) as elOpName, insComp.name as insCompName, rtme.responce_pat_policy_no as policy, rtme.eligibility_ask_from  as elAsk, rtme.xml_271_responce as respXMLPath, rtme.response_deductible, rtme.response_copay, rtme.response_co_insurance
							FROM real_time_medicare_eligibility rtme
							INNER JOIN users us ON us.id = rtme.request_operator  
							INNER JOIN insurance_data insData ON insData.id = rtme.ins_data_id  
                            INNER JOIN insurance_companies insComp ON insComp.id = insData.provider 
							WHERE rtme.id = '".$arr_appt["rte_id"]."' and rtme.del_status = '0' AND rtme.transection_error NOT LIKE '%error%' LIMIT 1 ";
		$rsGetRealTimeData = imw_query($qryGetRealTimeData);
		if($rsGetRealTimeData){
			if(imw_num_rows($rsGetRealTimeData)>0){
				$rowGetRealTimeData = imw_fetch_object($rsGetRealTimeData);		
				if(($rowGetRealTimeData->vs270RespDate != "00-00-0000") && ($rowGetRealTimeData->vs270RespDate != "")){
					$vsToolTip .= "Date: ".$rowGetRealTimeData->vs270RespDate;
				}
				else{
					$vsToolTip .= "Date: N/A";
				}
				$vsToolTip .= " \t \t";
				
				if($rowGetRealTimeData->elOpName != ""){
					$vsToolTip .= "Opr: ".$rowGetRealTimeData->elOpName;
				}
				else{
					$vsToolTip .= "Opr: N/A";
				}
				$vsToolTip .= "\n";
				
				if($rowGetRealTimeData->insCompName != ""){
					$dbInsCompName = $rowGetRealTimeData->insCompName;
				}
				else{
					$dbInsCompName = "N/A";
				}
				
				$dbInsCompName .= " \n";
				$vsToolTip .= $dbInsCompName;
				
				if($rowGetRealTimeData->policy != ""){
					$vsToolTip .= "Policy # ".$rowGetRealTimeData->policy;
				}
				else{
					$vsToolTip .= "Policy # "."N/A";
				}
				$vsToolTip .= " \n";
				
				$dbRespDDC = $rowGetRealTimeData->response_deductible;
				$arrRespDDC = explode("-", $dbRespDDC);
				$intDDCAmt = (int)$arrRespDDC[4];
				
				$dbRespCopay = $rowGetRealTimeData->response_copay;
				$arrRespCopay = explode("-", $dbRespCopay);
				$intCopayAmt = (float)$arrRespCopay[4];
				
				$dbRespCoIns = $rowGetRealTimeData->response_co_insurance;
				$arrRespCoIns = explode("-", $dbRespCoIns);
				$strCoInsAmt = $arrRespCoIns[6];
								
				if($intCopayAmt > 0){
					$vsToolTip .= "CoPay: $".$intCopayAmt;
				}
				else{
					$vsToolTip .= "CoPay: "."N/A";
				}
				$vsToolTip .= " \t";
				
				if($intDDCAmt > 0){
					$vsToolTip .= "DED: $".$intDDCAmt;
				}
				else{
					$vsToolTip .= "DED: "."N/A";
				}
				$vsToolTip .= " \t";
				if(empty($strCoInsAmt) == false){
					$vsToolTip .= "Co-Ins: ".$strCoInsAmt."%";
				}
				else{
					$vsToolTip .= "Co-Ins: "."N/A";
				}
				$vsToolTip .= " \n";
				
				if($rowGetRealTimeData->vsTransectionError != ""){
					//$vsToolTip .= "Status: Error \n";
					$vsToolTip .= $rowGetRealTimeData->vsTransectionError;
					$imgRealTimeEli = "<img id=\"imgEligibility\" src=\"".$GLOBALS['webroot']."/library/images/eligibility_red.png\" border=\"0\"/>";
				}
				elseif($rowGetRealTimeData->vsEBLoopResp != ""){	
					$strEBResponce = $obj_scheduler->objCoreLang->get_vocabulary("vision_share_271", "EB", (string)trim($rowGetRealTimeData->vsEBLoopResp));
					//$vsToolTip .= "Status: ".$strEBResponce;
					$rowGetRealTimeData->vsEBLoopResp;
					if(($rowGetRealTimeData->vsEBLoopResp == "6") || ($rowGetRealTimeData->vsEBLoopResp == "7") || ($rowGetRealTimeData->vsEBLoopResp == "8") || ($rowGetRealTimeData->vsEBLoopResp == "V")){
						$imgRealTimeEli = "<img id=\"imgEligibility\" src=\"".$GLOBALS['webroot']."/library/images/eligibility_red.png\" border=\"0\"/>";				
					}
					else{
						$imgRealTimeEli = "<img id=\"imgEligibility\" src=\"".$GLOBALS['webroot']."/library/images/eligibility_green.png\" border=\"0\"/>";	
					}
				}
			}
			else{
				$qryGetCertInfo = "SELECT ins_comp_id FROM vision_share_cert_config	WHERE ins_comp_id = '".(int)$objRowPatIns->compId."' LIMIT 1 ";
				$rsGetCertInfo = imw_query($qryGetCertInfo);
				if(imw_num_rows($rsGetCertInfo) > 0){ //For Medicare Eligibility
					$imgRealTimeEli = "<img id=\"imgEligibility\" src=\"".$GLOBALS['webroot']."/library/images/eligibility_blue.png\" border=\"0\" title=\"Realtime Medicare Eligibility Request\" />";
				}
				elseif(imw_num_rows($rsGetCertInfo) == 0){ //For Comercial Eligibility
					$imgRealTimeEli = "<img id=\"imgEligibility\" src=\"".$GLOBALS['webroot']."/library/images/eligibility_blue.png\" border=\"0\" title=\"Realtime Commercial Eligibility Request\" />";
				}
			}
		}
		if((int)$objRowPatIns->insId > 0){
			$intClientWindowH = $_SESSION['wn_height'] - 140;
			//$strRTEAmtInfo = $intDDCAmt."@@".$intCopayAmt."@@".$strCoInsAmt;
			if($intCopayAmt > 0){
				$strRTEAmtInfo .= "<span id=\"spCopay\">C.P:$".$intCopayAmt."</span>&nbsp;";
			}
			if($intDDCAmt > 0){
				$strRTEAmtInfo .= "<span id=\"spDDC\">DDC:$".$intDDCAmt."</span>&nbsp;";
			}
			if(empty($strCoInsAmt) == false){
				$strRTEAmtInfo .= "<span id=\"spCoins\">C.I:".$strCoInsAmt."%</span>";
			}
			
			$qryGetCertInfo = "SELECT ins_comp_id FROM vision_share_cert_config	WHERE ins_comp_id = '".(int)$objRowPatIns->compId."' LIMIT 1 ";
			$rsGetCertInfo = imw_query($qryGetCertInfo);
			if(imw_num_rows($rsGetCertInfo) > 0){
				$strElLinkInnerHTML = "<a id=\"anchorEligibility\" href=\"javascript:void(0);\" title=\"".$vsToolTip."\" onclick=\"getRealTimeEligibilityApp('".$objRowPatIns->insId."', '0', '".$GLOBALS['rootdir']."', '".$_REQUEST["sch_id"]."', '".$strAppDate."', '".$intClientWindowH."');\">".$imgRealTimeEli."</a>";
			}
			elseif(imw_num_rows($rsGetCertInfo) == 0){
				$strElLinkInnerHTML = "<a id=\"anchorEligibility\" href=\"javascript:void(0);\" title=\"".$vsToolTip."\" onclick=\"getRealTimeEligibilityApp('".$objRowPatIns->insId."', '1', '".$GLOBALS['rootdir']."', '".$_REQUEST["sch_id"]."', '".$strAppDate."', '".$intClientWindowH."');\">".$imgRealTimeEli."</a>";
			}
			
			if(isset($arr_appt["rte_id"]) && $arr_appt["rte_id"] != '0'){
				$strRTELink = "<a id=\"anchorEligibility\" class=\"activeTextOnly\" href=\"javascript:void(0);\" onclick=\"get271Report('".$arr_appt["rte_id"]."');\" title=\"".$vsToolTip."\">RTE/V</a>";
			}else{
				$strRTELink = "<a id=\"anchorEligibility\" class=\"textOnly\"  href=\"javascript:void(0);\">RTE/V</a>";
			}
			
			$rte_id = $arr_appt["rte_id"];
		}
	}
}
	
	echo "~~~~~~~~~~"; //10 times
	if(array_key_exists('fname',$arr_pat))
	echo htmlentities(stripslashes($arr_pat['fname'])); //pt first name						1
	echo "~~~~~~~~~~"; //10 times
	if(array_key_exists('mname',$arr_pat))
	echo htmlentities(stripslashes($arr_pat['mname'])); //pt middle name						2
	echo "~~~~~~~~~~"; //10 times
	if(array_key_exists('lname',$arr_pat))
	echo htmlentities(stripslashes($arr_pat['lname'])); //pt last name						3
	echo "~~~~~~~~~~"; //10 times
	if(array_key_exists('EMR',$arr_pat))
	echo $arr_pat['EMR']; //pt EMR status						4
	echo "~~~~~~~~~~"; //10 times
	echo (!empty($arr_appt["case_type_id"])) ? $arr_appt["case_type_id"] : $pt_ins_case_id; //appt pt ins case id		5
	echo "~~~~~~~~~~"; //10 times
	if(array_key_exists('p_imagename',$arr_pat))
	echo $arr_pat["p_imagename"]; //pt image path				6
	echo "~~~~~~~~~~"; //10 times
	echo (($arr_appt !== false) ? $arr_appt["sa_patient_app_status_id"] : -1); //status of appt loaded		7
	echo "~~~~~~~~~~"; //10 times
	if(isset($patient_notes_message)){
	//echo str_replace(array("'", "\"", "<br/><br/>", "<br/>"), array("&rsquo;", "&quot;", "\n", "\n"), $patient_notes_message); //pt demo alert			8
	echo $patient_notes_message; //pt demo alert			8
	}
	echo "~~~~~~~~~~"; //10 times
	if(isset($pt_spec_message))
	echo $pt_spec_message;//pt specific alert
	//echo ($pt_spec_message !== false) ? str_replace(array("'", "\"", "<br>"), array("&rsquo;", "&quot;", "\n"), $pt_spec_message) : "";//pt specific alert			9
	echo "~~~~~~~~~~"; //10 times
	if(isset($bl_todo))
	echo $bl_todo;		//to do warning to show or not									10
	echo "~~~~~~~~~~"; //10 times
	//echo str_replace(array("'", "\"", "<br/><br/>", "<br/>"), array("&rsquo;", "&quot;", "\n", "\n"), $poe_alert);	//poe alert				11
	if(isset($poe_alert))
	echo $poe_alert;								//11
	echo "~~~~~~~~~~"; //10 times
	if(array_key_exists(0,$arr_coll))
	echo $arr_coll[0]; //collection flag bool				12
	echo "~~~~~~~~~~"; //10 times
	if(array_key_exists(1,$arr_coll))
	echo $arr_coll[1]; //collection flag date				13
	echo "~~~~~~~~~~"; //10 times
	if(array_key_exists('id',$arr_appt))
	echo (($arr_appt !== false) ? $arr_appt["id"] : -1); //appt id		14
	echo "~~~~~~~~~~"; //10 times
	if(array_key_exists('chk_notes_scheduler',$arr_pat))
	echo $arr_pat["chk_notes_scheduler"]; //notes bool			15
	echo "~~~~~~~~~~"; //10 times
	if(array_key_exists('patient_notes',$arr_pat))
	echo $obj_scheduler->refine_string_for_js($arr_pat["patient_notes"]); //notes				16
	echo "~~~~~~~~~~"; //10 times
	if(isset($bl_proc_alert))
	echo $bl_proc_alert; //proc alert bool				17
	echo "~~~~~~~~~~"; //10 times
	if(array_key_exists(2,$arr_proc_alert))
	echo $arr_proc_alert[2]; //proc alert					18
	echo "~~~~~~~~~~"; //10 times
	if(array_key_exists('procedureid',$arr_appt))
	echo (($arr_appt !== false) ? $arr_appt["procedureid"] : ""); //status of appt loaded		19
	echo "~~~~~~~~~~"; //10 times//20
	if(array_key_exists('sa_app_start_date',$arr_appt))
	echo (($arr_appt !== false) ? $arr_appt["sa_app_start_date"] : ""); //appt date
	
	echo "~~~~~~~~~~"; //10 times//21
	if(array_key_exists('0',$arr_received_supply))
	echo $arr_received_supply[0]; //received or not
	echo "~~~~~~~~~~"; //10 times//22
	echo $arr_received_supply[1]; //order id for which supply is received
	if(array_key_exists('1',$arr_received_supply))
	echo "~~~~~~~~~~"; //10 times//23
	if(array_key_exists('0',$arr_ordered_supply))
	echo $arr_ordered_supply[0]; //supply ordered or not
	echo "~~~~~~~~~~"; //10 times/24
	if(array_key_exists('1',$arr_ordered_supply))
	echo $arr_ordered_supply[1]; //if ordered, the total due balance
	echo "~~~~~~~~~~"; //10 times//25
	if(array_key_exists('0',$arr_trial_cost))
	echo $arr_trial_cost[0]; //trial type (eval, fit, refit)
	echo "~~~~~~~~~~"; //10 times//26
	if(array_key_exists('1',$arr_trial_cost))
	echo $arr_trial_cost[1]; //trial cost
	echo "~~~~~~~~~~"; //10 times//27
	if(isset($last_final_rx_id))
	echo $last_final_rx_id; //last final rx db id
	echo "~~~~~~~~~~"; //10 times//28
	if(isset($appt_action_type))
	echo $appt_action_type; //add_appt / update_appt
	
	echo "~~~~~~~~~~"; //10 times//29
	if(array_key_exists('id',$arr_appt))
	echo (($arr_appt !== false) ? $arr_appt["id"] : -1); //appt id
	
	echo "~~~~~~~~~~"; //10 times//30
	if(isset($coll_alt))
	echo $coll_alt; //add_appt / update_appt
	echo "~~~~~~~~~~";//31
	if(isset($strElLinkInnerHTML))
	echo $strElLinkInnerHTML; //Eligibility
	echo "~~~~~~~~~~";//32
	if(isset($strRTELink))
	echo $strRTELink;

	echo "~~~~~~~~~~"; //10 times//33
	if(array_key_exists('sec_procedureid',$arr_appt))
	echo (($arr_appt !== false) ? $arr_appt["sec_procedureid"] : ""); //status of appt loaded
	echo "~~~~~~~~~~"; //10 times//34
	if(array_key_exists('tertiary_procedureid',$arr_appt))
	echo (($arr_appt !== false) ? $arr_appt["tertiary_procedureid"] : ""); //status of appt loaded		
	echo "~~~~~~~~~~";// 35
	if(isset($statusName))
	echo $statusName; //Patient account status name		

	//echo "~~~~~~~~~~";//36
	//echo $strRTEAmtInfo; //Eligibility Amount Information
	
	echo "~~~~~~~~~~";//37-1=36
	$available_alert='';
	$alert_already_shown=$_SESSION['first_avail_alert'];
	
	if(!$alert_already_shown[$_SESSION['patient']])
	{
		//show alert
		$available_alert=$obj_scheduler->is_first_available();
		if(trim($available_alert))
		{
			echo trim($available_alert);//first availble alert
			$alert_already_shown[$_SESSION['patient']]='Done';
			$_SESSION['first_avail_alert']=$alert_already_shown;
		}
	}
	
	echo "~~~~~~~~~~";//37
	$returnArray=$app_base->get_iconbar_status('update_recent_search');	
	echo $returnArray['recent_search'];
	echo "~~~~~~~~~~";//38
	echo $arr_pat["patientStatus"];
	echo "~~~~~~~~~~";//39
	echo $arr_pat["nick_name"];
	echo "~~~~~~~~~~";//40
	echo $arr_pat["phonetic_name"];
	echo "~~~~~~~~~~";//41
	echo $arr_pat["language"];
	echo "~~~~~~~~~~";//42
	echo $arr_pat["lang_code"];
	
}else{
	exit();
}
?>