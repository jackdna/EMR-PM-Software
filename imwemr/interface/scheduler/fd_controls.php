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

	
$disble_fd_controls = " disabled=\"disabled\"";
$disble_appt_controls = " disabled=\"disabled\" ";
$disble_future_appt_controls = "";
$disable_ext_appt_controls = "";
$img_path='../../library/images/schedule_icons/';

//varible declarition to omit warning message
$_REQUEST["sch_id"]=(isset($_REQUEST["sch_id"]))?$_REQUEST["sch_id"]:0;

if($dyn_id == "fd_pt_controls"){
	$disble_fd_controls = "";
	if(isset($_REQUEST["sch_id"]) && !empty($_REQUEST["sch_id"])){
		$disble_appt_controls = "";	
		$req_sch_id = (int) $_REQUEST["sch_id"];	
		$ch_appt_status_qry = "SELECT sa_patient_app_status_id FROM schedule_appointments WHERE id = ".$req_sch_id;
		$ch_appt_status_obj = imw_query($ch_appt_status_qry);
		$ch_appt_status_data = imw_fetch_assoc($ch_appt_status_obj);			
		if($ch_appt_status_data["sa_patient_app_status_id"] == 201 || $ch_appt_status_data["sa_patient_app_status_id"] == 18 || $ch_appt_status_data["sa_patient_app_status_id"] == 203)
		{
			if($disble_appt_controls == "")
			{
				$disable_ext_appt_controls = " disabled=\"disabled\" ";					
			}
		}
	}
	if($dyn_disble_future_appt == true){
		$disble_future_appt_controls = " disabled=\"disabled\" ";
	}
}

$ptDocsTemplateId = "";
$ptDocTempQry = "select pt_docs_template_id from pt_docs_template 
			where pt_docs_template_status = '0' and pt_docs_template_enable_facesheet = 'yes' ORDER BY pt_docs_template_id DESC LIMIT 0,1";
$ptDocTempRes = imw_query($ptDocTempQry);
if(imw_num_rows($ptDocTempRes)>0) {
	$ptDocTempRow = imw_fetch_assoc($ptDocTempRes);
	$ptDocsTemplateId = 	$ptDocTempRow["pt_docs_template_id"];
}

$patientDeceased = is_deceased($pat_id);

#######	CHECK SHALL WE SHOW ICONS OR BUTTONS FOR FD CONTROL ACTION
if(constant('SCHEDULER_FD_ICON') && constant('SCHEDULER_FD_ICON')==1)//	SHOW ICONS FOR FD CONTROL ACTION
{
	?>
	<div id="<?php echo $dyn_id;?>" class="patientprob">
	<div class="scheduloption">
	<ul>
	<?php
		//<!-- A & P -->
		$AandP="<li>";
		$enable_img=$img_path.'active/A&p.png';
		$disable_img=$img_path.'inactive/A&p.png';
		if($disble_fd_controls)
		{
			//show disabled link
			$AandP.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="A&P"/>';	
		}else{
			//get date to show a&p
			$query="SELECT cmt.date_of_service as show_dos FROM `chart_assessment_plans` as cap 
			INNER JOIN chart_master_table cmt ON cmt.id = cap.form_id 			
			WHERE cap.patient_id = '$_SESSION[patient]' ORDER BY cmt.date_of_service DESC,cmt.id DESC LIMIT 0,1 ";
			/*$query="SELECT exam_date FROM `chart_assessment_plans` 
			WHERE patient_id = '$_SESSION[patient]' ORDER BY exam_date DESC,id DESC LIMIT 0,1 ";*/
			
			$sqlAssessQry =imw_query($query) or die(imw_error()) ;
			if(imw_num_rows($sqlAssessQry)>0){
				$ap_date=imw_fetch_assoc($sqlAssessQry);
			
			$AandP.="<a href=\"javascript:void(0)\" id=\"AandP\" onClick=\"showAssesmentList('1','$ap_date[show_dos]');\"> 
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"A&P\"/>
			</a>";
			}
			else
			{
				$AandP.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="A&P"/>';	
			}
		 }
		$AandP.=" </li>";
		$icon_tray[1]=$AandP;

		 //<!-- appt hx -->
		$pat_app="<li>";
		$enable_img=$img_path.'active/appointment_history.png';
		$disable_img=$img_path.'inactive/appointment_history.png';
		if(isset($_REQUEST['server_id']) || $disble_fd_controls)
		{
			//show disabled link
			$pat_app.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="Appt. HX"/>';	
		}else{
			$pat_app.="<a href=\"javascript:void(0)\" id=\"pat_app\" onclick=\"javascript:load_appt_hx();\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Appt. HX\"/>
			 </a>";
		 }
		$pat_app.="</li>";
		$icon_tray[2]=$pat_app;

		//<!-- CANCEL -->
		$cancel="<li>";
		$enable_img=$img_path.'active/canecl.png';
		$disable_img=$img_path.'inactive/canecl.png';
		if($disble_fd_controls || $disble_appt_controls || $disable_ext_appt_controls)
		{
			//show disabled link
			$cancel.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="Cancel"/>';	
		}else{
			$cancel.="<a href=\"javascript:void(0)\" onClick=\"javascript:change_status('18', '$_SESSION[patient]', '$_REQUEST[sch_id]');\" id=\"cancel\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Cancel\"/>
			 </a>";
		}
		$cancel.="</li>";
		$icon_tray[3]=$cancel;
	
		//<!-- check in -->
		$check_in="<li>";
		$enable_img=$img_path.'active/checkout.png';
		$disable_img=$img_path.'inactive/checkout.png';
		if($disble_fd_controls || $disble_appt_controls || $disable_ext_appt_controls)
		{
			//show disabled link
			$check_in.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="Check In"/>';	
		}else{
			 $check_in.="<a href=\"javascript:void(0)\" onClick=\"javascript:change_status('13', '$_SESSION[patient]', '$_REQUEST[sch_id]');\" id=\"check_in\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Check In\"/>
			 </a>";
		}
		$check_in.="</li>";
		$icon_tray[4]=$check_in;

		//<!-- check out -->
		$check_out="<li>";
		$enable_img=$img_path.'active/checkin1.png';
		$disable_img=$img_path.'inactive/checkin1.png';
		if($disble_fd_controls || $disble_appt_controls || $disable_ext_appt_controls)
		{
			//show disabled link
			$check_out.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="Check Out"/>';	
		}else{
			$check_out.="<a href=\"javascript:void(0)\" onClick=\"javascript:change_status('11', '$_SESSION[patient]', '$_REQUEST[sch_id]');\" id=\"check_out\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Check Out\"/>
			 </a>";
		}
		$check_out.="</li>";
		$icon_tray[5]=$check_out;

		//<!-- cl supply -->
		$btnClSupplyOrder="<li>";
			$enable_img=$img_path.'active/cl-sply.png';
			$disable_img=$img_path.'inactive/cl-sply.png';
			$sql='';
			/*if(isset($_REQUEST["sch_id"]) && $_REQUEST["sch_id"]>0){
				$sql = "SELECT cl_order FROM schedule_appointments sc_app 
						JOIN chart_master_table cmt ON cmt.patient_id = sc_app.sa_patient_id
						WHERE sc_app.sa_patient_id = '".$_SESSION['patient']."'
							AND sc_app.id = '".$_REQUEST["sch_id"]."'
							AND cmt.date_of_service = sc_app.sa_app_start_date";
			}elseif(isset($sel_date)){*/

				$sql = "SELECT cl_order FROM chart_master_table 
						WHERE date_of_service = '".$sel_date."'
							AND patient_id = '".$_SESSION['patient']."' LIMIT 1";	  
			//}
			$res = imw_query($sql);
			$row = imw_fetch_assoc($res);
			if($row['cl_order']){
				$style = 'font-weight:bold';

				if($last_final_rx_id != "")
				{
					$sql_cl_order = 'SELECT print_order_id FROM clprintorder_master WHERE clws_id = '.$last_final_rx_id.' and dos = "'.$sel_date.'"';
					$sql_cl_order_obj = imw_query($sql_cl_order);	
					if(imw_num_rows($sql_cl_order_obj) > 0)
					{
						$style = '';		
					}
				}			
			}else{
				$style = '';
			}
			//CHECK IF ANY WORKSHEET EXISTS
			$clRs=imw_query("Select clws_id FROM contactlensmaster WHERE patient_id='".$_SESSION['patient']."'");
			$clRes=imw_fetch_assoc($clRs);
			$clws_id = $clRes['clws_id'];
			if($clws_id>0){
				$clSupplyFunction="javascript:showClSupplyOrderFromFrontDesk();";
			}else{
				$clSupplyFunction="javascript:top.fAlert('No&nbsp;contact&nbsp;lens&nbsp;worksheet&nbsp;exists!');";
			}
		if(isset($_REQUEST['server_id']) || $disble_fd_controls)
		{
			//show disabled link
			$btnClSupplyOrder.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="CL-Sply"/>';		
		}else
		{
		
			$btnClSupplyOrder.="<a href=\"javascript:void(0)\" id=\"btnClSupplyOrder\" onClick=\"$clSupplyFunction\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"CL-Sply\"/>
			</a>";
		}
		$btnClSupplyOrder.="</li>";
		$icon_tray[6]=$btnClSupplyOrder;

		//<!-- cl Disp -->
		$btnClSupplyDisp="<li>";
		$enable_img=$img_path.'active/cl-disp.png';
		$disable_img=$img_path.'inactive/cl-disp.png';
		if(isset($_REQUEST['server_id']) || $disble_fd_controls)
		{
			//show disabled link
			$btnClSupplyDisp.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="CL-Disp"/>';		
		}else{
			$btnClSupplyDisp.="<a href=\"javascript:void(0)\" id=\"btnClSupplyDisp\" onClick=\"javascript:contactLensDispense();\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"CL-Disp\"/>
			</a>";
		}
		$btnClSupplyDisp.="</li>";
		$icon_tray[7]=$btnClSupplyDisp;

		//<!-- confirm -->
		$confirm="<li>";
		$enable_img=$img_path.'active/confirm.png';
		$disable_img=$img_path.'inactive/confirm.png';
		if($disble_fd_controls || $disble_appt_controls || $disable_ext_appt_controls)
		{
			//show disabled link
			$confirm.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="Confirm"/>';
		}else{
			$confirm.="<a href=\"javascript:void(0)\" id=\"confirm\" onClick=\"javascript:change_status('17', '$_SESSION[patient]', '$_REQUEST[sch_id]');\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Confirm\"/>
			</a>";
		}
		$confirm.="</li>";
		$icon_tray[8]=$confirm;

		//<!-- erx -->
		$erxButton="<li>";
		$enable_img=$img_path.'active/erx1.png';
		$disable_img=$img_path.'inactive/erx1.png';
		if($disble_fd_controls)
		{
			//show disabled link
			$erxButton.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="eRx"/>';
		}else{
			$erxButton.="<a href=\"javascript:void(0)\" id=\"erxButton\" onClick=\"javascript:open_erx('$_SESSION[patient]');\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"eRx\"/>
			</a>";
		}
		$erxButton.="</li>";
		$icon_tray[9]=$erxButton;

		//<!-- face sheet -->
		$btnFacesheet="<li>";
		$enable_img=$img_path.'active/facesheet.png';
		$disable_img=$img_path.'inactive/facesheet.png';
		if($disble_fd_controls){
			//show disabled link
			$btnFacesheet.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="Facesheet"/>';
		 }else{
			$btnFacesheet.="<a href=\"javascript:void(0)\" id=\"btnFacesheet\" onClick=\"javascript:printFaceSheetFromFrontDesk('$ptDocsTemplateId','$_REQUEST[sch_id]');\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Facesheet\"/>
			</a>";
		 }
		 $btnFacesheet.="</li>";
		$icon_tray[10]=$btnFacesheet;

		//<!-- First Available  -->
		$first_avail="<li>";
		$enable_img=$img_path.'active/first_available1.png';
		$disable_img=$img_path.'inactive/first_available1.png';
		if(isset($_REQUEST['server_id']) || $disble_fd_controls || $disble_appt_controls || $disable_ext_appt_controls)
		{
			//show disabled link
			$first_avail.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="First Available"/>';	
		}else{
			$first_avail.="<a href=\"javascript:void(0)\" id=\"first_avail\" onClick=\"javascript:change_status('271', '$_SESSION[patient]', '$_REQUEST[sch_id]');\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"First Available\"/>
			</a>";
		}
		$first_avail.="</li>";
		$icon_tray[11]=$first_avail;

		//<!-- MAKE APPT -->
		$makeAppt="<li>";
		$enable_img=$img_path.'active/make_appointment.png';
		$disable_img=$img_path.'inactive/make_appointment.png';
		if(isset($_REQUEST['server_id']) || $disble_fd_controls || $disble_appt_controls || $disable_ext_appt_controls)
		{
			//show disabled link
			$makeAppt.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="Make Appointment"/>';
		}else{
			$makeAppt.="<a href=\"javascript:void(0)\" id=\"makeAppt\" onClick=\"javascript:mk_appt('$_SESSION[patient]')\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Make Appointment\"/>
			</a>";
		}
		$makeAppt.="</li>";
		$icon_tray[12]=$makeAppt;

		//<!-- new pt -->
		$new_pt="<li>
		   <a href=\"javascript:void(0)\" id=\"new_pt\" onClick=\"";
			$new_pt.=(core_check_privilege(array("priv_vo_pt_info")) == true)?"view_only_pt_call(1); return false; ":"javascript:window.top.clean_patient_session(); newPatient_info('');";
			$new_pt.="\">
				<img src=\"".$img_path."active/new_patient.png\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"New Patient\"/>
			</a>";
		$new_pt.="</li>";
		$icon_tray[13]=$new_pt;

		//<!-- Patient Pre Payments -->
		$pt_deposits="<li>";
		$enable_img=$img_path.'active/patient_pre_payment.png';
		$disable_img=$img_path.'inactive/patient_pre_payment.png';
		if(isset($_REQUEST['server_id']) || $disble_fd_controls)
		{
			//show disabled link
			$pt_deposits.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="Patient Pre Payment (PMT)"/>';
		}else{
			$pt_deposits.="<a href=\"javascript:void(0)\" id=\"pt_deposits\" onClick=\"javascript:pt_deposits_fun();\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Patient Pre Payment (PMT)\"/>
			</a>";
		}
		$pt_deposits.="</li>";
		$icon_tray[14]=$pt_deposits;

		//<!-- recalls -->
		$recall="<li>";
		$enable_img=$img_path.'active/recall.png';
		$disable_img=$img_path.'inactive/recall.png';
		$disabled='';
		if($disble_fd_controls){
			//show disabled link
			$recall.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="Recall"/>';
		}
		else{
			$recall.="<a href=\"javascript:void(0)\" onClick=\"javascript:descrip('$_SESSION[patient]', '$_REQUEST[sch_id]')\" id=\"recall\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Recall\"/>
			</a>";
		}
		$recall.="</li>";
		$icon_tray[15]=$recall;

		//<!-- super bill -->
		$super_bill="<li>";
		$enable_img=$img_path.'active/superbill.png';
		$disable_img=$img_path.'inactive/superbill.png';
		if($disble_fd_controls || $disble_appt_controls){
			//show disabled link
			$super_bill.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="Super Bill"/>';
		}
		else{
			$super_bill.="<a href=\"javascript:void(0)\" onClick=\"javascript:superbill_info('$_SESSION[patient]')\" id=\"super_bill\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Super Bill\"/>	
			</a>";
		}
		$super_bill.="</li>";
		$icon_tray[16]=$super_bill;

		//<!-- save -->
		$save="<li>";
		$enable_img=$img_path.'active/save.png';
		$disable_img=$img_path.'inactive/save.png';
		if($disble_fd_controls || $disble_appt_controls){
			//show disabled link
			$save.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="Save"/>';
		}
		else{
			$save.="<a href=\"javascript:void(0)\" onclick=\"javascript:save_changes('$_SESSION[patient]', '$_REQUEST[sch_id]','";
			$save.=(isset($arr_appt['facility_type_provider']))?$arr_appt['facility_type_provider']:0;
			$save.="');\" id=\"save\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Save\"/>	
			</a>";
		}
		$save.="</li>";
		$icon_tray[17]=$save;

		//<!-- Reschedule -->
		$resch_appt="<li>";
		$enable_img=$img_path.'active/reshedule.png';
		$disable_img=$img_path.'inactive/reshedule.png';
		if($disble_fd_controls || $disble_appt_controls){
			//show disabled link
			$resch_appt.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="Reschedule"/>';
		}
		else{
			$resch_appt.="<a href=\"javascript:void(0)\" onclick=\"javascript:drag_name('$_REQUEST[sch_id]', '$_SESSION[patient]', 'reschedule');\" id=\"resch_appt\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Reschedule\"/>	
			</a>";
		}
		$resch_appt.="</li>";
		$icon_tray[18]=$resch_appt;

		//<!-- add appt -->
		$add_appt="<li>";
		$enable_img=$img_path.'active/add-appointment.png';
		$disable_img=$img_path.'inactive/add-appointment.png';
		if($disble_fd_controls){
			//show disabled link
			$add_appt.='<img src="'.$disable_img.'" data-toggle="tooltip" data-placement="bottom" title="Add Appt"/>';
		}
		else{
			$add_appt.="<a href=\"javascript:void(0)\" onclick=\"javascript:drag_name('', '$_SESSION[patient]', 'addnew');\" id=\"add_appt\">
				<img src=\"$enable_img\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Add Appt\"/>	
			</a>";
		}
		$add_appt.="</li>";
		$icon_tray[19]=$add_appt;
	
	//get icon ordering from 
	$q_icon=imw_query("select id from schedule_icon_list_order order by icon_order asc");
	while($d_icon=imw_fetch_object($q_icon))
	{
		echo $icon_tray[$d_icon->id]."\n";
	}
	?>

	</ul>

	</div>
	</div>
	<?php
}
else //	SHOW BUTTONS FOR FD CONTROL ACTION
{
?>

	<div id="<?php echo $dyn_id;?>" class="patientprob">
		<?php
 		/*<!-- A & P -->*/
 		$disabled=0;
 		$icon_tray[1]['class']='';
		$icon_tray[1]['id']='AandP';
		$icon_tray[1]['text']='A&P';
		$icon_tray[1]['onclick']="";
		$icon_tray[1]['disable']=$disabled;
 
		if($disble_fd_controls){
			$disabled=1;
			$icon_tray[1]['disable']=$disabled;
		 }else{
			//get date to show a&p
			$query="SELECT cmt.date_of_service as show_dos FROM `chart_assessment_plans` as cap 
			INNER JOIN chart_master_table cmt ON cmt.id = cap.form_id 			
			WHERE cap.patient_id = '$_SESSION[patient]' ORDER BY cmt.date_of_service DESC,cmt.id DESC LIMIT 0,1 ";

			$sqlAssessQry =imw_query($query) or die(imw_error()) ;
			if(imw_num_rows($sqlAssessQry)>0){
				$ap_date=imw_fetch_assoc($sqlAssessQry);
				$icon_tray[1]['onclick']="showAssesmentList('1','$ap_date[show_dos]');";
			}
			else
			{
				$disabled=1;
				$icon_tray[1]['disable']=$disabled;
			}
		}
 		
 		/*<!-- appt hx-->*/
		$disabled=0;
		if(isset($_REQUEST['server_id']) || $disble_fd_controls){$disabled=1;}
		$icon_tray[2]['class']='';
 		$icon_tray[2]['id']='pat_app';
 		$icon_tray[2]['text']='Appt Hx.';
 		$icon_tray[2]['onclick']='javascript:load_appt_hx();';
 		$icon_tray[2]['disable']=$disabled;
		
 		/*<!-- CANCEL -->*/
		$disabled=0;
		if($disble_fd_controls || $disble_appt_controls || $disable_ext_appt_controls){$disabled=1;}
		$icon_tray[3]['class']='btn btn-danger';
 		$icon_tray[3]['id']='cancel';
 		$icon_tray[3]['text']='Cancel';
 		$icon_tray[3]['onclick']="javascript:change_status('18', '$_SESSION[patient]', '$_REQUEST[sch_id]');";
 		$icon_tray[3]['disable']=$disabled;
 
 
		/*<!-- check in -->*/
		$disabled=0;
		if($disble_fd_controls || $disble_appt_controls || $disable_ext_appt_controls){$disabled=1;}
 		$icon_tray[4]['class']='';
 		$icon_tray[4]['id']='check_in';
 		$icon_tray[4]['text']='Check In';
 		$icon_tray[4]['onclick']="javascript:change_status('13', '$_SESSION[patient]', '$_REQUEST[sch_id]');";
 		$icon_tray[4]['disable']=$disabled;

		/*<!-- check out -->*/
		$disabled=0;
		if(isset($_REQUEST['server_id']) || $disble_fd_controls || $disble_appt_controls || $disable_ext_appt_controls || $disble_future_appt_controls){$disabled=1;}
 		$icon_tray[5]['class']='';
 		$icon_tray[5]['id']='check_out';
 		$icon_tray[5]['text']='Check Out';
 		$icon_tray[5]['onclick']="javascript:change_status('11', '$_SESSION[patient]', '$_REQUEST[sch_id]');";
 		$icon_tray[5]['disable']=$disabled;
 
 		/*<!-- CL-Sply -->*/
 		$disabled=0;
 		$icon_tray[6]['style']='';
 		$icon_tray[6]['class']='';
		$icon_tray[6]['id']='btnClSupplyOrder';
		$icon_tray[6]['text']='CL-Sply';
		$icon_tray[6]['onclick']="";
		$icon_tray[6]['disable']=$disabled;
 
		$sql='';
		/*if(isset($_REQUEST["sch_id"]) && $_REQUEST["sch_id"]>0){
			$sql = "SELECT cl_order FROM schedule_appointments sc_app 
					JOIN chart_master_table cmt ON cmt.patient_id = sc_app.sa_patient_id
					WHERE sc_app.sa_patient_id = '".$_SESSION['patient']."'
					AND sc_app.id = '".$_REQUEST["sch_id"]."'
					AND cmt.date_of_service = sc_app.sa_app_start_date";
		}elseif(isset($sel_date)){*/
			$sql = "SELECT cl_order FROM chart_master_table 
					WHERE date_of_service = '".$sel_date."'
					AND patient_id = '".$_SESSION['patient']."' LIMIT 1";	  
		//}
		$res = imw_query($sql);
		$row = imw_fetch_assoc($res);
		if($row['cl_order']){
			$style = 'font-weight:bold';
			if($last_final_rx_id != "")
			{
				$sql_cl_order = 'SELECT print_order_id FROM clprintorder_master WHERE clws_id = '.$last_final_rx_id.' and dos = "'.$sel_date.'"';
				$sql_cl_order_obj = imw_query($sql_cl_order);	
				if(imw_num_rows($sql_cl_order_obj) > 0)
				{
					$style = '';		
				}
			}			
		}else{
			$style = '';
		}
 		
		//CHECK IF ANY WORKSHEET EXISTS
		$clRs=imw_query("Select clws_id FROM contactlensmaster WHERE patient_id='".$_SESSION['patient']."'");
		$clRes=imw_fetch_assoc($clRs);
		$clws_id = $clRes['clws_id'];
		if($clws_id>0){
			$clSupplyFunction="javascript:showClSupplyOrderFromFrontDesk();";
		}else{
			$clSupplyFunction="javascript:top.fAlert('No&nbsp;contact&nbsp;lens&nbsp;worksheet&nbsp;exists!');";
		}
 
		if(isset($_REQUEST['server_id']) || $disble_fd_controls)
		{$icon_tray[6]['disable']=1;}
 		else{
			$icon_tray[6]['style']=$style;
			$icon_tray[6]['onclick']=$clSupplyFunction;
		}
 
 		/*<!-- CL-Disp -->*/
 		$disabled=0;
 		$icon_tray[7]['style']='';
 		$icon_tray[7]['class']='';
		$icon_tray[7]['id']='btnClSupplyDisp';
		$icon_tray[7]['text']='CL-Disp';
		$icon_tray[7]['onclick']="";
		$icon_tray[7]['disable']=$disabled;
 
		if(isset($_REQUEST['server_id']) || $disble_fd_controls)
		{$icon_tray[7]['disable']=1;}
		else {$icon_tray[7]['onclick']="javascript:contactLensDispense();";}
 
 		/*<!-- Confirm -->*/
 		$disabled=0;
 		$icon_tray[8]['style']='';
 		$icon_tray[8]['class']='';
		$icon_tray[8]['id']='confirm';
		$icon_tray[8]['text']='Confirm';
		$icon_tray[8]['onclick']="";
		$icon_tray[8]['disable']=$disabled;
 
		if($disble_fd_controls || $disble_appt_controls || $disable_ext_appt_controls){$icon_tray[8]['disable']=1;}
		else {$icon_tray[8]['onclick']="javascript:change_status('17', '$_SESSION[patient]', '$_REQUEST[sch_id]');";}
		
 		/*<!-- erx -->*/
 		$disabled=0;
 		$icon_tray[9]['style']='';
 		$icon_tray[9]['class']='';
		$icon_tray[9]['id']='erxButton';
		$icon_tray[9]['text']='eRx';
		$icon_tray[9]['onclick']="";
		$icon_tray[9]['disable']=$disabled;
 
		if($disble_fd_controls){$icon_tray[9]['disable']=1;}
		else {$icon_tray[9]['onclick']="javascript:open_erx('$_SESSION[patient]');";}
 
 		/*<!-- face sheet -->*/
 		$disabled=0;
 		$icon_tray[10]['style']='';
 		$icon_tray[10]['class']='';
		$icon_tray[10]['id']='facesheet';
		$icon_tray[10]['text']='Face Sheet';
		$icon_tray[10]['onclick']="";
		$icon_tray[10]['disable']=$disabled;
 
		if($disble_fd_controls){$icon_tray[10]['disable']=1;}
		else {$icon_tray[10]['onclick']="javascript:printFaceSheetFromFrontDesk('$ptDocsTemplateId','$_REQUEST[sch_id]');";}
 
 		/*<!-- First Available -->*/
 		$disabled=0;
 		$icon_tray[11]['style']='';
 		$icon_tray[11]['class']='';
		$icon_tray[11]['id']='to_do';
		$icon_tray[11]['text']='First Available';
		$icon_tray[11]['onclick']="";
		$icon_tray[11]['disable']=$disabled;
 
		if(isset($_REQUEST['server_id']) || $disble_fd_controls || $disble_appt_controls || $disable_ext_appt_controls){$icon_tray[11]['disable']=1;}
		else {$icon_tray[11]['onclick']="javascript:change_status('271', '$_SESSION[patient]', '$_REQUEST[sch_id]');";}
 
 		/*<!-- MAKE APPT -->*/
 		$disabled=0;
 		$icon_tray[12]['style']='';
 		$icon_tray[12]['class']='';
		$icon_tray[12]['id']='makeAppt';
		$icon_tray[12]['text']='Make Appointment';
		$icon_tray[12]['onclick']="";
		$icon_tray[12]['disable']=$disabled;
 
		if(isset($_REQUEST['server_id']) || $disble_fd_controls || $disble_appt_controls || $disable_ext_appt_controls){$icon_tray[12]['disable']=1;}
		else {$icon_tray[12]['onclick']="javascript:mk_appt('$_SESSION[patient]');";}
 
 		/*<!-- new pt -->*/
 		$disabled=0;
 		$icon_tray[13]['style']='';
 		$icon_tray[13]['class']='';
		$icon_tray[13]['id']='new_pt';
		$icon_tray[13]['text']='New Patient';
		$icon_tray[13]['onclick']="";
		$icon_tray[13]['disable']=$disabled;
 		
 		if(core_check_privilege(array("priv_vo_pt_info")) == true){
			$icon_tray[13]['onclick']="view_only_pt_call(1); return false;";
		}else{ 
			$icon_tray[13]['onclick']="javascript:window.top.clean_patient_session(); newPatient_info('');";
		}
 
 		/*<!-- Patient Pre Payments -->*/
 		$disabled=0;
 		$icon_tray[14]['style']='';
 		$icon_tray[14]['class']='';
		$icon_tray[14]['id']='pt_deposits';
		$icon_tray[14]['text']='PMT';//Patient Pre Payments 
		$icon_tray[14]['onclick']="";
		$icon_tray[14]['disable']=$disabled;
 		
 		if($disble_fd_controls){
			$icon_tray[14]['disable']=1;
		}else{ 
			$icon_tray[14]['onclick']="javascript:pt_deposits_fun();";
		}
 
 		/*<!-- recalls -->*/
 		$disabled=0;
 		$icon_tray[15]['style']='';
 		$icon_tray[15]['class']='';
		$icon_tray[15]['id']='pt_recalls';
		$icon_tray[15]['text']='Recalls';
		$icon_tray[15]['onclick']="";
		$icon_tray[15]['disable']=$disabled;
 		
 		if($disble_fd_controls){
			$icon_tray[15]['disable']=1;
		}else{ 
			$icon_tray[15]['onclick']="javascript:descrip('$_SESSION[patient]', '$_REQUEST[sch_id]')";
		}
 
 		/*<!-- super bil -->*/
 		$disabled=0;
 		$icon_tray[16]['style']='';
 		$icon_tray[16]['class']='';
		$icon_tray[16]['id']='super_bill';
		$icon_tray[16]['text']='Super Bill';
		$icon_tray[16]['onclick']="";
		$icon_tray[16]['disable']=$disabled;
 		
 		if($disble_fd_controls || $disble_appt_controls){
			$icon_tray[16]['disable']=1;
		}else{ 
			$icon_tray[16]['onclick']="javascript:superbill_info('$_SESSION[patient]')";
		}

		/*<!-- save -->*/   
		$disabled=0;
		if($disble_fd_controls){$disabled=1;}
 		//this is a temporary check to fix issue occured on 13 apr 2018, it might be removed in future
 		if(isset($_REQUEST["sch_id"]) && $_REQUEST["sch_id"]>0){
			$sqluTyp = imw_query("SELECT u.user_type FROM schedule_appointments sa 
			LEFT JOIN users u ON sa.sa_doctor_id = u.id
			WHERE sa.sa_patient_id = '".$_SESSION['patient']."'
			AND sa.id = '".$_REQUEST["sch_id"]."'
			limit 1");
			$datauTyp=imw_fetch_assoc($sqluTyp);
			$appointed_phy_typ=$datauTyp['user_type'];
		}
 		$temp_fac_fix=0;
 		if(!$arr_appt['facility_type_provider'] && $appointed_phy_typ==22)
		{$temp_fac_fix=1;}
		$icon_tray[17]['class']='';
 		$icon_tray[17]['id']='save_chan';
 		$icon_tray[17]['text']='Save';
 		$icon_tray[17]['onclick']="javascript:save_changes('$_SESSION[patient]', '$_REQUEST[sch_id]','";
 		$icon_tray[17]['onclick'].=($arr_appt['facility_type_provider'])?$arr_appt['facility_type_provider']:$temp_fac_fix;
 		$icon_tray[17]['onclick'].="');";
 		$icon_tray[17]['disable']=$disabled;

		/*<!-- REschedule -->*/
		$disabled=0;
		if($disble_fd_controls || $disble_appt_controls || $patientDeceased){$disabled=1;}
 		$icon_tray[18]['class']='';
 		$icon_tray[18]['id']='resch_appt';
 		$icon_tray[18]['text']='Re Schedule';
 		$icon_tray[18]['onclick']="javascript:drag_name('$_REQUEST[sch_id]', '$_SESSION[patient]', 'reschedule');";
 		$icon_tray[18]['disable']=$disabled;

		/*<!-- Add Aptp-->*/
		$disabled=0;
		if($disble_fd_controls || $patientDeceased){$disabled=1;}
 		$icon_tray[19]['class']='';
 		$icon_tray[19]['id']='add_appt';
 		$icon_tray[19]['text']='Add Appt';
 		$icon_tray[19]['onclick']="javascript:drag_name('', '$_SESSION[patient]', 'addnew');";
 		$icon_tray[19]['disable']=$disabled;
 
 		//get icon ordering from 
		$q_icon=imw_query("select id from schedule_icon_list_order order by icon_order asc");
		while($d_icon=imw_fetch_object($q_icon))
		{
			$style=		$icon_tray[$d_icon->id]['style'];
			$class=		($icon_tray[$d_icon->id]['class'])?$icon_tray[$d_icon->id]['class']:'btn btn-success';
			$id=		$icon_tray[$d_icon->id]['id'];
			$text=		$icon_tray[$d_icon->id]['text'];
			$onclick=	$icon_tray[$d_icon->id]['onclick'];
			$disable=	$icon_tray[$d_icon->id]['disable'];
			
			if($btn_cntr>6)//show li
			{
				if($disable==1){
					$li.="<li><a href=\"javascript:void(0)\" class=\"disabled_link\">$text</a></li>";	
				}
				else
				{
					$li.="<li class=\"\"><a href=\"javascript:void(0)\" id=\"$id\" onclick=\"$onclick\" style=\"$style\">$text</a></li>";
				}
			}else//show buttons
			{
				$disable=($disable==1)?'disabled="disable"':'';
				$buttons.=" <button class=\"$class\" name=\"$id\" id=\"$id\" onclick=\"$onclick\" $disable>$text</button>";
			}
			
			$btn_cntr++;
		}
 		echo $buttons;
 		$scan_docs_class="";
 		if($disabled==1){
			$scan_docs_class=" class=\"disabled_link\"";	
		}
		$li.="<li class=\"\"><a href=\"javascript:void(0)\" id=\"scan_docs\" onclick=\"top.popup_win('../common/documents.php?tab_name=DocTab');\" style=\"\" $scan_docs_class>Scan Docs</a></li>";
		$popFeatures = "location=0,menubar=0,resizable=1,status=0,titlebar=0,toolbar=0,width=1000,height=900,left=100";
		$li.="<li class=\"\"><a href=\"javascript:void(0)\" id=\"quick_scan\" title=\"Quick Scan\" onclick=\"top.tb_popup(this);\" $scan_docs_class>Quick Scan</a></li>";
		?>
		 <div class="btn-group">
		 <button type="button" class="dotmenu dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<img src="<?php echo $GLOBALS['webroot'];?>/library/images/dotmenu.png" alt=""/></button>
			<ul class="dropdown-menu"><?php echo $li;?></ul>
		</div>
	</div>
<?php
}//end of checking is we showing fd control buttons or icons
?>