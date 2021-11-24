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
 
/// Show Scheduler alerts///	

$ajax_fun_arg=array();
if($_SESSION['acc_commt_pat']!=$_SESSION["patient"]){
	
	if(trim($_SESSION['alertShowForThisSession']) != "Cancel"){
		$alertToDisplayAt="Accounting";
		include_once(dirname(__FILE__)."/../../library/classes/CLSAlerts.php");	
		$OBJPatSpecificAlert = new CLSAlerts();	
		echo ($OBJPatSpecificAlert->getAdminAlert($_SESSION['patient'],$alertToDisplayAt,$form_id));	
		echo ($OBJPatSpecificAlert->getPatSpecificAlert($_SESSION['patient'],$alertToDisplayAt));	
		echo ($OBJPatSpecificAlert->autoSetDivLeftMargin("140","265"));
		echo ($OBJPatSpecificAlert->autoSetDivTopMargin("250","30"));
		echo ($OBJPatSpecificAlert->writeJS());
	}

	$ajax_fun_arg[]="current_case";
	$ajax_fun_arg[]="pat_account_status";
	$ajax_fun_arg[]="acc_notes_comment";
	$ajax_fun_arg[]="poe";
	$ajax_fun_arg[]="previous_statement_butt";
	$ajax_fun_arg[]="payments_comment_butt";
	$ajax_fun_arg[]="insurance_description";
	$ajax_fun_arg[]="patient_notes";
	$ajax_fun_arg[]="ass_plan_rg";
	//$ajax_fun_arg[]="ci_pp";
	core_refresh_recent_five();
	if($_SESSION["ent_commt_pat"]!=$_SESSION["patient"] && constant("stop_notes_alert")!='1'){
		 $ajax_fun_arg[]="payment_comment";
	}
}/*else if($_REQUEST['Check_inout_chk']!=""){
	$ajax_fun_arg[]="ci_pp";
}else if($acc_enc!=""){
	$ajax_fun_arg[]="ci_pp";
}*/
$ajax_fun_arg[]="ci_pp";
if($date_of_service_ap!="" && !in_array('ass_plan_rg',$ajax_fun_arg)){
	$ajax_fun_arg[]="ass_plan_rg";
}
$ajax_fun_arg[]="pri_sec_active_ins";
$ajax_fun_arg_imp=implode(',',$ajax_fun_arg);
$_SESSION['acc_commt_pat'] = $_SESSION["patient"];
$ajax_fun_ex_arg="";
if($date_of_service_ap!=""){
	$ajax_fun_ex_arg="date_of_service=".$date_of_service_ap;
}

$no_balance_bill="";
if($noBalanceBill>0){
	$no_balance_bill="No Balance Bill";
}
$acc_pat_dob="";
if($date_of_birth){
	$acc_pat_dob = "&nbsp;DOB:<br>&nbsp;".$date_of_birth;
}
?>
<script type="text/javascript">
	$(document).ready(function(){
		top.$('#acc_page_name').html('<?php echo $title; ?>');
		top.$('#no_balance_bill').html('<?php echo $no_balance_bill; ?>');
		top.$('.acc_pat_dob').html('<?php echo $acc_pat_dob; ?>');
		
		var claim_status_request_js='<?php echo constant('CLAIM_STATUS_REQUEST'); ?>';
		if(claim_status_request_js=='YES'){
			top.$('#btn_clm_status').show();
		}
		var acc_enc ='<?php echo $acc_enc; ?>';
		var priInsId = 0; 
		if(document.getElementById('getPriInsId')){
			priInsId = document.getElementById('getPriInsId').value;
		}
		if(typeof (top.fmain) != "undefined"){
			top.fmain.get_accept_assignment(priInsId,acc_enc);
			top.fmain.ajax_fun('<?php echo $ajax_fun_arg_imp;?>','<?php echo $ajax_fun_ex_arg;?>');
		}
	});
</script>
</div>
</body>
</html>