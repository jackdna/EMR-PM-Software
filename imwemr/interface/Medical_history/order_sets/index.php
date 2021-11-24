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

include_once($GLOBALS['srcdir']."/classes/medical_hx/order_set.class.php");
include_once($GLOBALS['srcdir']."/classes/CLSAlerts.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");

$order_set_obj = new Order_set($medical->current_tab);
$patient_id = $order_set_obj->patient_id;

//Vocabulary Array
$arr_info_alert = $order_set_obj->order_set_vocabulary;
$logged_provider_id = $_SESSION['authId'];

//Saving order set details
if(isset($_REQUEST['do_action']) && $_REQUEST['do_action'] == 'saveForm' && trim($_REQUEST['save_data']) != '' && count($_REQUEST['order_set_status']) > 0){
	$update_status = $order_set_obj->save_order_set($_REQUEST);
	if(trim($update_status) != '' && $update_status > 0){
		?>
		<script>
			top.show_loading_image("show", 100);
			if(top.document.getElementById('medical_tab_change')) {
				if(top.document.getElementById('medical_tab_change').value!='yes') {
					top.alert_notification_show('<?php echo $arr_info_alert["save"];?>');
				}
				if(top.document.getElementById('medical_tab_change').value=='yes') {
					top.chkConfirmSave('yes','set');		
				}
				top.document.getElementById('medical_tab_change').value='';
			}
			top.fmain.location.href = top.JS_WEB_ROOT_PATH + '/interface/Medical_history/index.php?showpage=order_sets';
			top.show_loading_image("hide");
		</script>
		<?php 
	}
}

//Getting data to display
$data = $order_set_obj->get_all_order_set_details($_REQUEST);
if($data['counter'] == 0){
	$page_data = '<tr><td colspan="8" class="bgcolor failureMsg text-center" >No Record Found.</td></tr>';
}else{
	$page_data = $data['file_content'].$data['order_file_content'];
}

$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];
?>
<div class="row">
	<div style="col-sm-12">
		<input type="hidden" name="preObjBack" id="preObjBack" value="<?php print $PHP_SELF; ?>">	
		<form name="order_set_frm" id="order_set_frm" action="index.php?showpage=order_sets&do_action=saveForm" method="post">
			<input type="hidden" name="save_data" id="save_data" value="">
			<input type="hidden" name="next_tab" id="next_tab" value="">
			<input type="hidden" name="next_dir" id="next_dir" value="">
			<input type="hidden" name="change_order_set_val" id="change_order_set_val" value="">
			<input type="hidden" name="buttons_to_show" id="buttons_to_show" value="">
			<table class="table table-bordered table-striped table-condensed">
				<tr class="grythead">
					<th>Date &amp; Time</th>
					<th>Order set name</th>
					<th>Opr.</th>
					<th>Site</th>
					<th>Schedule</th>
					<th>Priority</th>
					<th>Options</th>
					<th style="width:10%;">
						<select class="selectpicker" name="change_order_set_status" onChange="javascript:top.fmain.document.order_set_frm.submit();" data-width="100%">
							<?php echo $order_set_obj->get_status_bar_opt($_REQUEST['change_order_set_status']); ?>
						</select>
					</th>
				</tr>
				<?php echo $page_data; ?>
			</table>
		</form>	
	</div>
</div>

<?php
$pkIdAuditTrail = $data['pkIdAuditTrail'];
$pkIdAuditTrailID = $data['pkIdAuditTrailID'];
//--- AUDIT TRAIL CODE
if($policyStatus == 1 and $pkIdAuditTrailID != '' and isset($_SESSION['Patient_Viewed']) === true){
	$opreaterId = $_SESSION["authId"];												 
	$ip = getRealIpAddr();
	$URL = $_SERVER['PHP_SELF'];													 
	//$os = get_os_($_SERVER['HTTP_USER_AGENT']);
	$os = getOS();
	$browserInfoArr = array();
	$browserInfoArr = _browser();
	$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
	$browserName = str_replace(";","",$browserInfo);													 
	$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$arrAuditTrailView_OS = array();
	$arrAuditTrailView_OS[0]['Pk_Id'] = $pkIdAuditTrailID;
	$arrAuditTrailView_OS[0]['Table_Name'] = 'order_set_associate_chart_notes';
	$arrAuditTrailView_OS[0]['Action'] = 'view';
	$arrAuditTrailView_OS[0]['Operater_Id'] = $opreaterId;
	$arrAuditTrailView_OS[0]['Operater_Type'] = getOperaterType($opreaterId);
	$arrAuditTrailView_OS[0]['IP'] = $ip;
	$arrAuditTrailView_OS[0]['MAC_Address'] = $_REQUEST['macaddrs'];
	$arrAuditTrailView_OS[0]['URL'] = $URL;
	$arrAuditTrailView_OS[0]['Browser_Type'] = $browserName;
	$arrAuditTrailView_OS[0]['OS'] = $os;
	$arrAuditTrailView_OS[0]['Machine_Name'] = $machineName;
	$arrAuditTrailView_OS[0]['Category'] = 'patient_info-medical_history';
	$arrAuditTrailView_OS[0]['Filed_Label'] = 'Patient Order Sets Data';
	$arrAuditTrailView_OS[0]['Category_Desc'] = 'order_set';
	$arrAuditTrailView_OS[0]['Old_Value'] = $pkIdAuditTrail;
	$arrAuditTrailView_OS[0]['pid'] = $patient_id;

	$patientViewed = $_SESSION['Patient_Viewed'];
	if(is_array($patientViewed) && $patientViewed["Medical History"]["order_set"] == 0){
		auditTrail($arrAuditTrailView_OS,$mergedArray,0,0,0);
		$patientViewed["Medical History"]["order_set"] = 1;
		$_SESSION['Patient_Viewed'] = $patientViewed;
	}
}


if(trim($_SESSION['alertShowForThisSession']) != "Cancel"){
	echo $order_set_obj->set_cls_alerts();
}
?>
<script type="text/javascript">	
	//Js functions in main med js file
	self.focus();
	top.btn_show("ORDRST");
	top.show_loading_image("hide");
</script>
</body>
</html>