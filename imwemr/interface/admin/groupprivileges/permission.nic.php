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

 File: index.php
 Purpose: A router to route to selected document section
 Access Type: Indirect Access.
*/



//--

$oGroupPrevileges = new GroupPrevileges();
$menu_array = $oGroupPrevileges->fetchMenuArray();

$setting_str = '';
$menuParentArr = array(
    'Admin' => 'el_main_priv',
    'Billing' => 'el_main_priv',
    'Clinical' => 'el_main_priv',
    'Documents' => 'el_main_priv',
    'iASC_Link' => 'el_main_priv',
    'iMedic_Monitor' => 'el_main_priv',
    'iPortal' => 'el_main_priv',
    'Manage_Fields' => 'el_main_priv',
    'Optical_Settings' => 'el_main_priv',
    'Setting_Reports' => 'el_main_priv',
    'Setting_Scheduler' => 'el_main_priv',
    'IOLs' => 'el_main_priv',
    'iOptical' => 'el_main_priv',
    'iCnfdntlTxt' => 'el_main_priv',
    'Scheduler' => 'div_Reports_n_reports',
    'Practice_Analytics' => 'div_Reports_n_reports',
    'Financials' => 'div_Reports_n_reports',
    'Compliance' => 'div_Reports_n_reports',
    'CCD' => 'div_Reports_n_reports',
    'API' => 'div_Reports_n_reports',
    'State' => 'div_Reports_n_reports',
    'Optical' => 'div_Reports_n_reports',
    'Reminders' => 'div_Reports_n_reports',
    'ReportClinical' => 'div_Reports_n_reports',
    'Rules' => 'div_Reports_n_reports',
    'ReportiPortal' => 'div_Reports_n_reports'
);


foreach ($menu_array as $menu_head => $tab_head) {
    $setting_chunk = array_chunk($tab_head, 5, true);

    $menu_title = ucfirst($menu_head);
    $setting_str .= '<div id="admin'.$menu_head.'" class="adminPrivDiv" data-parent="'.$menuParentArr[$menu_head].'">';
    $setting_str .= '<table class="table">';
    foreach ($setting_chunk as $chunk) {
	$setting_str .= '<tr>';
	foreach ($chunk as $field_name => $label) {
	    $setting_str .= '<td ><div class="checkbox"><input class="privoptioncheck" type="checkbox" name="' . $field_name . '" id="' . $field_name . '" value="1" ' . $check[$field_name] . '><label for="' . $field_name . '">' . $label . '</label></div></td>';
	}
	$setting_str .= '</tr>';
    }
    $setting_str .= '</table></div>';
}

//display when not in users section
//if($zflg_in_users!=1){$pro_type_new = 11;}
$chk_disabled="";

//
if($zflg_in_users==1){
?>

<div id="new_priv_div" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary" id="priv_div_handler">
				<button type="button" class="close" data-dismiss="modal" id="close_priv">×</button>
				<h4 class="modal-title" id="modal_title">Privileges</h4>
			</div>

			<div class="modal-body pd5" style="overflow:hidden; overflow-y:auto;">
<?php
}else if($zflg_in_users==2){
?>
<div id="myModal" class="modal" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		<div class="modal-header bg-primary">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title" id="modal_title">Group Privileges</h4>
		</div>

		<div class="modal-body">
			<!--<div class="form-group">-->
				<div class="row">
					<input type="hidden" class="form-control" name="id" id="adm_grp_prv_Id" >
					<input type="hidden" class="form-control" name="edid" id="edid" value="<?php echo $_GET["edid"];?>" >
					<label for="gr_name" class="col-sm-2">Privilege Name</label>
					<div class="col-sm-10">
					<input class="form-control" name="gr_name" id="gr_name" type="text" required>
					</div>
				</div>
				<div class="row hidden">
					<label for="prevlgs">Group Privileges</label>
					<input class="form-control" name="prevlgs" id="prevlgs" type="text">
				</div>
				<div class="row">


<?php
} //
?>

				<div id="priv_grant"></div>
				<div id="ele_priv_chk">
					<div id="el_all_priv" class="col-xs-12 recbox auto_height el_all_priv" >
						<div class="head no_brdr">
							<div class="checkbox checkbox-inline" style="text-transform:none;padding-left:25px;"><input type="checkbox" value="1" <?php echo $chk_admin_priv_all; ?> title="All Settings" name="priv_all_settings" id="priv_all_settings" class="priv_all_settings" data-changediv="main" onclick="selectDeselect_all_admin('el_all_priv',this.checked);"><label for="priv_all_settings"></label></div>
							<span id="spn_priv_all_settings"><small>All Privileges</small></span>
						</div>
					</div>
					<div id="el_main_priv" class="col-xs-12 recbox auto_height el_main_priv" >
						<div class="head">
							<div class="checkbox checkbox-inline" style="text-transform:none;padding-left:25px;"><input type="checkbox" value="1"  title="Select All"  id="el_sel_settings"  name="el_sel_settings" onclick="selectAll(this)" <?php echo $chk_sel_settings; ?> ><label for="el_sel_settings"></label></div>
							<span>Settings</span>
						</div>
						<table class="table privTable">
							<tr>
								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Admin" name="priv_admin" id="priv_admin" value="1" <?php echo $chk_admin; ?> ><label for="priv_admin" >Admin</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Admin" data-showdiv="adminAdmin" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Admin"></i></div></td>
								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Billing" name="priv_admin_billing" id="priv_admin_billing" value="1" <?php echo $chk_priv_admin_billing; ?>><label for="priv_admin_billing">Billing</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Billing" data-showdiv="adminBilling" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_billing"></i></div></td>
								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Clinical" name="priv_admin_clinical" id="priv_admin_clinical" value="1" <?php echo $chk_priv_admin_clinical; ?> ><label for="priv_admin_clinical">Clinical</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Clinical" data-showdiv="adminClinical" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_clinical"></i></div></td>
								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Document" name="priv_document" id="priv_document" value="1" <?php echo $chk_priv_document; ?>><label for="priv_document">Documents</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Documents" data-showdiv="adminDocuments" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_documents"></i></div></td>

							</tr>
							<tr>
								<td  ><div class="checkbox"><input type="checkbox" title="iASC Link" name="priv_iOLink" id="priv_iOLink" value="1" <?php echo $chk_iOLink; ?>><label for="priv_iOLink">iASC Link</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="iASC_Link" data-showdiv="adminiASC_Link" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_iASCLink"></i></div></td>
								<td  ><div class="checkbox"><input type="checkbox" title="iMedic Monitor" name="priv_iMedicMonitor" id="priv_iMedicMonitor" value="1" <?php echo $chk_iMedicMonitor; ?>><label for="priv_iMedicMonitor">iMedic Monitor</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="iMedic_Monitor" data-showdiv="adminiMedic_Monitor" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_iMedicMonitor"></i></div></td>
								<td  ><div class="checkbox"><input type="checkbox" title="iPortal" name="priv_iportal" id="priv_iportal" value="1" <?php echo $chk_priv_iportal; ?>><label for="priv_iportal">iPortal</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="iPortal" data-showdiv="adminiPortal" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_iPortal"></i></div></td>
								<td  ><div class="checkbox"><input type="checkbox" title="Manage Fields" name="priv_manage_fields" id="priv_manage_fields" value="1" <?php echo $chk_priv_manage_fields; ?>><label for="priv_manage_fields">Manage&nbsp;Fields</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Manage_Fields" data-showdiv="adminManage_Fields" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Manage_Fields"></i></div></td>

							</tr>
							<tr>
								<td  ><div class="checkbox"><input type="checkbox" title="Optical Settings" name="priv_Admin_Optical" id="priv_Admin_Optical" value="1" <?php echo $chk_priv_Admin_Optical; ?>><label for="priv_Admin_Optical">Optical Settings</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Optical_Settings" data-showdiv="adminOptical_Settings" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Optical_Settings"></i></div></td>
								<td  ><div class="checkbox"><input type="checkbox" title="Setting Reports" name="priv_Admin_Reports" id="priv_Admin_Reports" value="1" <?php echo $chk_priv_Admin_Reports; ?>><label for="priv_Admin_Reports">Reports</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Setting_Reports" data-showdiv="adminSetting_Reports" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Setting_Reports"></i></div></td>
								<td  ><div class="checkbox"><input type="checkbox" title="IOLs" name="priv_iols" id="priv_iols" value="1" <?php echo $chk_priv_iols; ?>><label for="priv_iols">IOLs</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="IOLs" data-showdiv="adminIOLs" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_IOLs"></i></div></td>
								<td  ><div class="checkbox"><input type="checkbox" title="Setting Scheduler" name="priv_admin_scheduler" id="priv_admin_scheduler" value="1" <?php echo $chk_priv_admin_scheduler; ?>><label for="priv_admin_scheduler">Scheduler</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Setting_Scheduler" data-showdiv="adminSetting_Scheduler" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Setting_Scheduler"></i></div></td>

							</tr>
							<tr>
								<td  ><div class="checkbox"><input type="checkbox" title="iOptical" name="priv_Optical" id="priv_Optical" value="1" <?php echo $chk_Optical; ?> ><label for="priv_Optical" >iOptical</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="iOptical" data-showdiv="adminiOptical" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_iOptical"></i></div></td>
								<td  ><div class="checkbox"><input type="checkbox" title="Security" name="priv_Security" id="priv_Security" value="1" <?php echo $chk_Security; ?>><label for="priv_Security">Security</label></div></td>
								<td  ><div class="checkbox"><input type="checkbox" title="API Access" name="priv_api_access" id="priv_api_access" value="1" <?php echo $chk_api_access; ?>><label for="priv_api_access">API Access</label></div></td>
                <td  ><div class="checkbox"><input type="checkbox" title="Confidential Text" name="priv_cnfdntl_txt" id="priv_cnfdntl_txt" value="1" <?php echo $chk_cnfdntl_txt; ?>><label for="priv_cnfdntl_txt">Confidential Text</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Confidential_Text" data-showdiv="adminConfidential_Text" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_iCnfdntlTxt"></i></div></td>
							</tr>
						</table>
					</div>

					<div id="el_div_clinic" class="col-xs-12 recbox auto_height" >
						<div class="head">
						<div class="checkbox checkbox-inline" style="text-transform:none;padding-left:25px;"><input type="checkbox" value="1"  title="Select All"  id="el_sel_clinical" name="el_sel_clinical"  onclick="selectAll(this)" <?php echo $chk_sel_clinical; ?> ><label for="el_sel_clinical"></label></div>
						<span>Clinical</span>
						</div>
						<table class="table">
							<tr>
								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Work View" name="priv_cl_work_view" id="priv_cl_work_view" value="1" <?php echo $chk_cl_work_view.$chk_disabled; ?> ><label for="priv_cl_work_view">Work View</label></div></td>

								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Tests" name="priv_cl_tests" id="priv_cl_tests" value="1" <?php echo $chk_cl_tests.$chk_disabled; ?> ><label for="priv_cl_tests">Tests</label></div></td>

								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Medical Hx" name="priv_cl_medical_hx" id="priv_cl_medical_hx" value="1" <?php echo $chk_cl_medical_hx.$chk_disabled; ?> ><label for="priv_cl_medical_hx">Medical Hx</label></div></td>

								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="eRx" name="erx_chk" id="erx_chk" value="1" <?php echo $chk_erx.$chk_disabled; ?> ><label for="erx_chk">eRx</label></div></td>
							</tr>
							<tr >
								<td  ><div class="checkbox"><input type="checkbox" title="Break Glass" name="priv_break_glass" id="priv_break_glass" value="1" <?php echo $chk_break_glass.$chk_disabled; ?> ><label for="priv_break_glass">Break Glass</label></div></td>
								<td  ><div class="checkbox"><input type="checkbox" title="Patient Information Summary" name="priv_pis" id="priv_pis" value="1" <?php echo $chk_priv_pis; ?>><label for="priv_pis" title="Patient Information Summary">Pt. Info. Sum.</label></div></td>

								<td  ><div class="checkbox"><input type="checkbox" title="Clinical View-Only" name="priv_vo_clinical" id="priv_vo_clinical" value="1" <?php echo $chk_vo_clinical.$chk_disabled; ?>><label for="priv_vo_clinical" class="text-primary">View-Only </label></div></td>
								<td><div class="checkbox"><input type="checkbox" name="priv_financial_hx_cpt" id="priv_financial_hx_cpt" value="1" <?php echo $chk_priv_financial_hx_cpt; ?>><label for="priv_financial_hx_cpt">Financial - Hx CPT</label></div></td>

							</tr>
							<tr>
								<td><div class="checkbox"><input type="checkbox" name="priv_purge_del_chart" id="priv_purge_del_chart" value="1" <?php echo $chk_priv_purge_del_chart; ?>><label for="priv_purge_del_chart" class="text-primary">Purge/Delete Chart </label></div></td>
								<td><div class="checkbox"><input type="checkbox" title="Record Release" name="priv_record_release" id="priv_record_release" value="1" <?php echo $chk_priv_record_release; ?> ><label for="priv_record_release">Record Release</label></div></td>
								<td><div class="checkbox"><input type="checkbox" title="Procedure Amendments" name="priv_proc_amend" id="priv_proc_amend" value="1" <?php echo $chk_priv_proc_amend; ?> ><label for="priv_proc_amend">Proc Amendments</label></div></td>
                <td><div class="checkbox"><input type="checkbox" title="Define WNL Statements" name="priv_def_wnl_stmt" id="priv_def_wnl_stmt" value="1" <?php echo $chk_priv_def_wnl_stmt; ?> ><label for="priv_def_wnl_stmt">Define WNL Statements</label></div></td>
              </tr>
              <tr>
                <td><div class="checkbox"><input type="checkbox" title="Edit Prescriptions" name="priv_edit_prescriptions" id="priv_edit_prescriptions" value="1" <?php echo $chk_priv_edit_prescriptions; ?> ><label for="priv_edit_prescriptions">Edit Prescriptions</label></div></td>
                <td  class="chart_final <?php echo (($pro_type_new == '11')?' visible ':' invisible '); ?> "><div class="checkbox"><input  type="checkbox" title="Chart finalize" name="priv_chart_finalize" id="priv_chart_finalize" value="1" <?php echo $check_priv_chart_finalize; ?>><label for="priv_chart_finalize">Chart Finalize</label></div></td>
              </tr>
						</table>
					</div>

					<div id="el_main_front_desk" class="col-xs-12 recbox auto_height" >
						<div class="head">
						<div class="checkbox checkbox-inline" style="text-transform:none;padding-left:25px;"><input type="checkbox" value="1"  title="Select All"  id="el_sel_fd" name="el_sel_fd"  onclick="selectAll(this)" <?php echo $chk_sel_fd; ?> ><label for="el_sel_fd"></label></div>
						<span>Front Desk</span>
						</div>
						<table class="table">
							<tr>
									<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Manager" name="priv_Front_Desk" id="priv_Front_Desk" <?php echo $chk_Front_Desk.$chk_disabled; ?> value="1" onclick="selectDeselect_all('el_main_front_desk',this.checked);"><label for="priv_Front_Desk">Manager</label></div></td>

									<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Scheduler/Demo" id="priv_scheduler_demo" name="priv_scheduler_demo" <?php echo $chk_priv_scheduler_demo.$chk_disabled; ?> value="1"><label for="priv_scheduler_demo">Scheduler/Demo</label></div></td>

									<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Sch. Override" name="priv_Sch_Override" id="priv_Sch_Override" value="1" <?php echo $chk_Sch_Override.$chk_disabled; ?> ><label for="priv_Sch_Override">Sch. Override</label></div></td>

									<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" name="priv_pt_Override" id="priv_pt_Override" value="1" <?php echo $chk_pt_Override.$chk_disabled; ?> ><label for="priv_pt_Override">Pt. Override</label></div></td>


							</tr>
							<tr>
									<td><div class="checkbox"><input type="checkbox" id="priv_sch_lock_block" name="priv_sch_lock_block" value="1" <?php echo $chk_sch_lock_block; ?>><label for="priv_sch_lock_block">Lock/Block Schedule</label></div></td>

                                    <td><div class="checkbox"><input type="checkbox" id="priv_sch_telemedicine" name="priv_sch_telemedicine" value="1" <?php echo $chk_sch_telemedicine; ?>><label for="priv_sch_telemedicine">Telemedicine</label></div></td>

									<td><div class="checkbox"><input type="checkbox" id="priv_vo_pt_info" name="priv_vo_pt_info" value="1" <?php echo $chk_vo_pt_info.$chk_disabled; ?>><label for="priv_vo_pt_info" class="text-primary">View-Only </label></div></td>

							</tr>
						</table>
					</div>

					<div id="div_acc_bll" class="col-xs-12 recbox auto_height" >
						<div class="head">
						<div class="checkbox checkbox-inline" style="text-transform:none;padding-left:25px;"><input type="checkbox" value="1"  title="Select All"  id="el_sel_acc" name="el_sel_acc"  onclick="selectAll(this)" <?php echo $chk_sel_acc; ?> ><label for="el_sel_acc"></label></div>
						<span>Account/Billing</span>
						</div>
						<table class="table">
							<tr>
								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Manager" name="priv_ac_bill_manager" id="priv_ac_bill_manager" value="1" <?php echo $chk_priv_ac_bill_manager.$chk_disabled; ?> onclick="selectDeselect_all('div_acc_bll',this.checked);"><label for="priv_ac_bill_manager">Manager</label></div></td>
								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Accounting" name="priv_Accounting" id="priv_Accounting" value="1" <?php echo $chk_Accounting.$chk_disabled; ?> ><label for="priv_Accounting">Accounting</label></div></td>
								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Billing" name="priv_Billing" id="priv_Billing" value="1" <?php echo $chk_Billing; ?>><label for="priv_Billing">Billing</label></div></td>

								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" name="priv_edit_financials" id="priv_edit_financials" value="1" <?php echo $chk_edit_financials.$chk_disabled; ?> ><label for="priv_edit_financials">Edit Financials</label></div></td>

							</tr>

							<tr>
									<td  ><div class="checkbox"><input type="checkbox" title="Ins. Management" name="priv_ins_management" id="priv_ins_management" value="1" <?php echo $chk_priv_ins_management; ?> ><label for="priv_ins_management">Ins. Management</label></div></td>
									<td  ><div class="checkbox"><input type="checkbox" title="Account History" name="priv_acchx" id="priv_acchx" value="1" <?php echo $chk_acchx.$chk_disabled; ?>><label for="priv_acchx">Account History</label></div></td>

									<td  ><div class="checkbox"><input type="checkbox" title="Charges" name="priv_vo_charges" id="priv_vo_charges" value="1" <?php echo $chk_vo_charges.$chk_disabled; ?> ><label for="priv_vo_charges">Charges</label></div></td>

									<td  ><div class="checkbox"><input type="checkbox" title="Payment" name="priv_vo_payment" id="priv_vo_payment" value="1" <?php echo $chk_vo_payment.$chk_disabled; ?> ><label for="priv_vo_payment">Payment</label></div></td>


							</tr>

							<tr>
									<td  ><div class="checkbox"><input type="checkbox" name="priv_bi_statements" id="priv_bi_statements" value="1" <?php echo $chk_bi_statements.$chk_disabled; ?>><label for="priv_bi_statements">Statements</label></div></td>

									<td  ><div class="checkbox"><input type="checkbox" name="priv_bi_day_chrg_rept" id="priv_bi_day_chrg_rept" value="1" <?php echo $chk_priv_bi_day_chrg_rept; ?>><label for="priv_bi_day_chrg_rept">Day Charges</label></div></td>
									<td  ><div class="checkbox"><input type="checkbox" name="priv_bi_edit_batch" id="priv_bi_edit_batch" value="1" <?php echo $chk_priv_bi_edit_batch; ?>><label for="priv_bi_edit_batch">Edit Batches</label></div></td>
                                    <td  cols="2" ></td>
									<td  class="hide" colspan="2" ><div class="checkbox"><input type="checkbox" title="Delete Payments" name="priv_del_payment" id="priv_del_payment" value="1" <?php echo $chk_priv_del_payment; ?> ><label for="priv_del_payment">Delete Payments</label></div></td>
									<td  class="hide" ><div class="checkbox"><input type="checkbox" title="Delete Charges/Enc" name="priv_del_charges_enc" id="priv_del_charges_enc" value="1" <?php echo $chk_priv_del_charges_enc; ?>><label for="priv_del_charges_enc">Delete Charges/Enc</label></div></td>
							</tr>

						</table>
					</div>

					<div id="div_Reports_n_reports" class="col-xs-12 recbox auto_height el_main_priv" >
					<div class="head">
					<div class="checkbox checkbox-inline" style="text-transform:none;padding-left:25px;"><input type="checkbox" value="1"  title="Select All"  id="el_sel_rprt" name="el_sel_rprt"  onclick="selectAll(this)" <?php echo $chk_sel_rprt; ?> ><label for="el_sel_rprt"></label></div>
					<span>Reports</span>  &nbsp;
					<div class="checkbox checkbox-inline" style="text-transform:none;padding-left:25px;"><input type="checkbox" title="Manager"  name="priv_Reports_manager" id="priv_Reports_manager" class="priv_all_settings" value="1" <?php echo $chk_priv_Reports_manager; ?> onclick="selectDeselect_all('div_Reports_n_reports',this.checked);"><label for="priv_Reports_manager"></label></div>
					<span><small>Manager</small></span>  &nbsp;
					</div>
							<table class="table privTable">
								<tr>
								    <td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Scheduler" name="priv_sc_scheduler" id="priv_sc_scheduler" value="1" <?php echo $chk_sc_scheduler; ?>><label for="priv_sc_scheduler">Scheduler</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Scheduler" data-showdiv="adminScheduler" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Scheduler"></i></div></td>
								    <td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Practice Analytics" name="priv_report_practice_analytics" id="priv_report_practice_analytics" value="1" <?php echo $chk_priv_report_practice_analytics; ?> ><label for="priv_report_practice_analytics">Practice&nbsp;Analytics</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Practice_Analytics" data-showdiv="adminPractice_Analytics" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Practice_Analytics"></i></div></td>
								    <!-- <td class="col-xs-2" ><div class="checkbox"><input type="checkbox" title="Practice Analytics" name="priv_report_practice_analytics" id="priv_report_practice_analytics" value="1" <?php echo $chk_priv_report_practice_analytics; ?> ><label for="priv_report_practice_analytics">Practice&nbsp;Analytics</label></div></td> -->
								    <td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Financials" name="priv_report_financials" id="priv_report_financials" value="1"  <?php echo $chk_priv_report_financials; ?> ><label for="priv_report_financials">Financials</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Financials" data-showdiv="adminFinancials" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Financials"></i></div></td>
								    <td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Compliance" name="priv_report_compliance" id="priv_report_compliance" value="1"  <?php echo $chk_priv_report_compliance; ?> ><label for="priv_report_compliance">Compliance</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Compliance" data-showdiv="adminCompliance" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Compliance"></i></div></td>

								</tr>
								<tr>
								    <td  ><div class="checkbox"><input type="checkbox" title="CCD" name="priv_cl_ccd" id="priv_cl_ccd" value="1" <?php echo $chk_cl_ccd; ?>><label for="priv_cl_ccd">CCD</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="CCD" data-showdiv="adminCCD" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_CCD"></i></div></td>
								    <td  ><div class="checkbox"><input type="checkbox" title="API Access" name="priv_report_api_access" id="priv_report_api_access" value="1" <?php echo $chk_priv_report_api_access; ?>><label for="priv_report_api_access">API</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="API" data-showdiv="adminAPI" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_API"></i></div></td>
								    <td  ><div class="checkbox"><input type="checkbox" title="State" name="priv_report_State" id="priv_report_State" value="1" <?php echo $chk_priv_report_State; ?>><label for="priv_report_State">State</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="State" data-showdiv="adminState" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_State"></i></div></td>
								    <td  ><div class="checkbox"><input type="checkbox" title="Optical" name="priv_report_optical" id="priv_report_optical" value="1" <?php echo $chk_priv_report_optical; ?>><label for="priv_report_optical">Optical</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Optical" data-showdiv="adminOptical" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Optical"></i></div></td>
								</tr>
								<tr>
								    <td  ><div class="checkbox"><input type="checkbox" title="House Calls" name="priv_sc_house_calls" id="priv_sc_house_calls" value="1" <?php echo $chk_sc_house_calls; ?>><label for="priv_sc_house_calls">Reminders</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Reminders" data-showdiv="adminReminders" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Reminders"></i></div></td>
								    <td  ><div class="checkbox"><input type="checkbox" title="Clinical" name="priv_cl_clinical" id="priv_cl_clinical" value="1" <?php echo $chk_cl_clinical; ?>><label for="priv_cl_clinical">Clinical</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="ReportClinical" data-showdiv="adminReportClinical" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_ReportClinical"></i></div></td>
								    <td  ><div class="checkbox"><input type="checkbox" title="Rules" name="priv_report_Rules" id="priv_report_Rules" value="1" <?php echo $chk_priv_report_Rules; ?>><label for="priv_report_Rules">Rules</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Rules" data-showdiv="adminRules" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Rules"></i></div></td>
								    <td  ><div class="checkbox"><input type="checkbox" title="iPortal" name="priv_report_iPortal" id="priv_report_iPortal" value="1" <?php echo $chk_priv_report_iPortal; ?>><label for="priv_report_iPortal">iPortal</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="ReportiPortal" data-showdiv="adminReportiPortal" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_ReportiPortal"></i></div></td>

								</tr>
							</table>
					</div>

					<div id="div_Reports_pt_portal" class="col-xs-12 recbox auto_height" >
						<div class="head">
						<div class="checkbox checkbox-inline" style="text-transform:none;padding-left:25px;"><input type="checkbox" value="1"  title="Select All"  id="el_sel_portal"  name="el_sel_portal" onclick="selectAll(this)" <?php echo $chk_sel_portal; ?> ><label for="el_sel_portal"></label></div>
						<span>Pt Portal</span>
						</div>
						<table class="table">
							<tr>
								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Front Desk" name="priv_pt_fdsk" id="priv_pt_fdsk" value="1" <?php echo $chk_priv_pt_fdsk; ?>><label for="priv_pt_fdsk">Front Desk</label></div></td>
								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Clinical" name="priv_pt_clinical" id="priv_pt_clinical" value="1" <?php echo $chk_priv_pt_clinical; ?>><label for="priv_pt_clinical">Clinical</label></div></td>
								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Message Coordinator" <?php echo $chk_pt_coordinator; ?> name="priv_pt_coordinate" id="priv_pt_coordinate" value="1"><label for="priv_pt_coordinate">Message Coordinator</label></div></td>
								<td class="col-xs-3" ></td>
							</tr>
						</table>
					</div>

					<div id="div_Reports_pt_icons" class="col-xs-12 recbox auto_height" >
						<div class="head">
						<div class="checkbox checkbox-inline" style="text-transform:none;padding-left:25px;"><input type="checkbox" value="1"  title="Select All"  id="el_sel_icon" name="el_sel_icon"  onclick="selectAll(this)" <?php echo $chk_sel_icon; ?>><label for="el_sel_icon"></label></div>
						<span>Icons</span>
						</div>
						<table class="table">
							<tr>
								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="iMedicMonitor" name="priv_pt_icon_imm" id="priv_pt_icon_imm" value="1" <?php echo $chk_priv_pt_icon_imm; ?>><label for="priv_pt_icon_imm">iMedicMonitor</label></div></td>
								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Optical" name="priv_pt_icon_optical" id="priv_pt_icon_optical" value="1" <?php echo $chk_priv_pt_icon_optical; ?>><label for="priv_pt_icon_optical">Optical</label></div></td>

								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="iASC Link" name="priv_pt_icon_iasclink" id="priv_pt_icon_iasclink" value="1" <?php echo $chk_priv_pt_icon_iasclink; ?>><label for="priv_pt_icon_iasclink">iASC Link</label></div></td>

								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Financial Dashboard" name="priv_financial_dashboard" id="priv_financial_dashboard" value="1" <?php echo $chk_priv_financial_dashboard; ?>><label for="priv_financial_dashboard">Financial Dashboard</label></div></td>
							</tr>
							<tr>
								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="Support" name="priv_pt_icon_support" id="priv_pt_icon_support" value="1" <?php echo $chk_priv_pt_icon_support; ?>><label for="priv_pt_icon_support">Support</label></div></td>
								<td class="col-xs-3" ><div class="checkbox"><input type="checkbox" title="AR Worksheet" name="priv_ar_worksheet" id="priv_ar_worksheet" value="1" <?php echo $chk_priv_ar_worksheet ?>><label for="priv_ar_worksheet">AR Worksheet</label></div></td>
                                <td ></td>
							</tr>
						</table>
					</div>

				</div>

<?php
if($zflg_in_users==1){
?>
			</div>
			<div id="pri_btn_con" class="modal-footer"></div>
		</div>
	</div>
</div>

<?php }else if($zflg_in_users==2){ ?>
			</div>
				<!--</div>-->
			</div>
			<div id="module_buttons" class="ad_modal_footer modal-footer">
				<button type="submit" class="btn btn-success">Save</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>

			</div>
		</div>
	</div>
<?php } ?>

<div id="priv_div_modal" class="modal" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary">
			    <button type="button" class="close"  id="close_priv_modal" onclick="resotreOldpriv();">x</button>
			    <h4 class="modal-title" id="modal_title"> Privileges</h4>
			</div>
			 <div class="modal-body pd5" style="overflow:hidden; overflow-y:auto;">
			    <div class="pd10">
				<input type="hidden" name="puchkbx" id="puchkbx" value="" />
				<input type="hidden" name="popupsection" id="popupsection" value="" />
				<input type="hidden" name="popupoldvals" id="popupoldvals" value="" />
				<div class="checkbox checkbox-inline"><input type="checkbox" title="Select All" name="priv_select_all" id="priv_select_all" ><label for="priv_select_all">Select All</label></div>
			    </div>
			    <div id="allprivdivs"><?php echo $setting_str; ?></div>
			</div>
			<div id="module_buttons" class="modal-footer ad_modal_footer">
			    <div class="row mdl_btns_dp">
				<div class="col-sm-12 text-center">
				    <button type="button" class="btn btn-success"  onclick="privcheckboxcolor();">Done</button>
				    <button type="button" class="btn btn-danger"  onclick="resotreOldpriv();">Close</button>
				</div>
			    </div>
			</div>
		</div>
	</div>
</div>
