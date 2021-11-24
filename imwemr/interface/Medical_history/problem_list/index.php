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

include_once($GLOBALS['srcdir']."/classes/medical_hx/problem_list.class.php");
include_once($GLOBALS['srcdir']."/classes/CLSAlerts.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
set_time_limit(300);
$problem_obj = new ProblemLst($medical->current_tab);
$pid = $problem_obj->patient_id;

//Vocabulary Array
$arr_info_alert = $problem_obj->problem_vocabulary;

//--- GET LOGGED PROVIDER STATUS ---
$current_user_id = $_SESSION["authId"];
$tmp = getUserFirstName($current_user_id,2);
$operatorName = $tmp[1];


// Saving problem list data
if(isset($_REQUEST['do_action']) && $_REQUEST['do_action'] == 'save'){
	$save_status = $problem_obj->save_prob_list_rec($_REQUEST);
	if(trim($save_status) != '' && $save_status > 0){
		$buttons_to_show = xss_rem($_REQUEST["buttons_to_show"]);
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
			top.fmain.location.href = top.JS_WEB_ROOT_PATH + '/interface/Medical_history/index.php?showpage=problem_list';
			top.show_loading_image("hide");
		</script>
		<?php
	}
}

//--- GET POLICY STATUS FOR AUDIT TRAIL ---
$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];

if(isDssEnable()){
	try{
	    $problem_obj->dss_pat_problem_list();
	} catch(Exception $e) {
		echo '<script>top.fAlert("'.$e->getMessage().'");</script>';
	}
}

?>
<!DOCTYPE html>
<style type="text/css">
	#div_disable{
		position:absolute;
		width:100%;
		height:<?php echo ($_SESSION['wn_height']-265).'px'; ?>;
		text-align:center;
		z-index:1001;
		background-color:#fff;
		opacity:0.6;
		display:none;
	}
	tr.child_row{background-color: rgba(222, 239, 243, 0.33);}
	/*tr.child_row > td:first-child{background-color: #fff;}*/
	/*.tbl_even tr:nth-of-type(odd){background-color: #d4cdcd;padding: 1%;}
	.child_row:nth-of-type(odd) {
		background-color: #ccc!important;
	}*/
	
	/*.table td{padding: 4px 8px !important;}*/
	
</style>
<?php
//Show Options
$elem_selList = "Active";

//In URL
if(isset($_GET["sopt"]) && !empty($_GET["sopt"])){
	$elem_selList = $_GET["sopt"];
}
$elem_selList;

$patient_name = $problem_obj->get_patient_name($problem_obj->patient_id,1);
$current_date = date("m-d-Y");

//Get Values in Array
$arrProblemList = $problem_obj->get_prob_list_array($elem_selList);
$flagCreateRow = true;


if(count($arrProblemList) > 0){
	$sql_qry = imw_query("select id from pt_problem_list where pt_id = '$pid'");
	while($row = imw_fetch_array($sql_qry)){
		$pkIdAuditTrail .= $row['id']."-";
		if($pkIdAuditTrailID == ""){		
			$pkIdAuditTrailID = $row['id'];
		}
	}
}

$sessionHeightInMH3= $GLOBALS['gl_browser_name']=='ipad' ? $_SESSION["wn_height"] - 125 : $_SESSION['wn_height']-330;
?>
<body>
<div id="div_disable" style="display:none;"></div>
<!-- Modal Box -->
<div class="commom_wrapper">
	<div id="div_umls" class="modal fade in" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Problem Results</h4>	
				</div>
				<div class="modal-body">
						
				</div>	
				<div id="module_buttons" class="modal-footer ad_modal_footer">
					<input type="button" name="close" value="Close" id="close_btn" class="btn btn-danger" data-dismiss="modal">
				</div>	
			</div>
		</div>
	</div>
</div>	

<div class="row">
	<form action="index.php?showpage=problem_list&do_action=save" method="post" name="problem_list_form" id="problem_list_form">
			<input type="hidden" id="mode" name="mode">
			<input type="hidden" name="curr_tab" id="curr_tab" value="<?php echo $medical->current_tab;?>">
			<input type="hidden" name="info_alert" id="info_alert" value="<?php echo ((is_array($arr_info_alert) && count($arr_info_alert) > 0) ? urlencode(serialize($arr_info_alert)) : "");?>">
			<input type="hidden" name="preObjBack" id="preObjBack" value="">
			<input type="hidden" name="next_tab" id="next_tab" value="">
			<input type="hidden" name="next_dir" id="next_dir" value="">
			<input type="hidden" name="buttons_to_show" id="buttons_to_show" value="">
			<input type="hidden" name="list_patient_id" value="<?php echo $pid;?>">
			<input type="hidden" name="current_user_id[]" id="current_user_id_0" value="<?php echo($current_user_id); ?>"/>
			<input type="hidden" name="list_operator_name[]" id="operator_name_0" value="<?php echo($operatorNameEDIT=="")?$operatorName:$operatorNameEDIT; ?>"/>
			<input type="hidden" id="hiden_val_field" name="id[]" value="<?php echo($arrProblemListID[0]["id"]); ?>"/>
			<input type="hidden" id="dss_prblm_id" name="dss_prblm_id[]" value="<?php echo($arrProblemListID[0]["dss_prblm_id"]); ?>"/>
		<div id="problem_list_content" class="col-sm-12"  style="overflow-y:scroll">
			<div class="table-responsive">
				<div id="selectpicker_div" style="position:absolute"></div>
				<table class="table table-bordered table-condensed">
					<tr class="grythead">
						<td width="25" rowspan="2" align="center">
							<div class="checkbox">
								<input type="checkbox" name="select_all" id="select_all">
								<label for="select_all"></label>
							</div>
						</td>
						<td rowspan="2" align="left">Problem </td>
						<td rowspan="2" align="left">Problem Type</td>
						<td rowspan="2" align="left">SNOMED CT</td>
						<td rowspan="2" align="center">Status</td>
                        <?php if(isDssEnable()){ ?><td rowspan="2" align="center">Service Eligibility <span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="bottom" title="Is this problem is service connected eligibility?" data-container="body"></span></td><?php } ?>
						<td align="center">Onset Date</td>
						<td colspan="4" align="center">Modified History</td>
					</tr>
					<tr class="grythead">
						<td align="center">
							<select class="selectpicker" name="elem_selList" onChange="top.fmain.setProListOpts(this.value);" data-width="100%" data-container="#selectpicker_div">
								<option value="All" <?php echo ($elem_selList == "All") ? "selected" : ""; ?>>All</option>
								<option value="Active" <?php echo ($elem_selList == "Active") ? "selected" : ""; ?>>Active</option>
								<option value="Inactive" <?php echo ($elem_selList == "Inactive") ? "selected" : ""; ?>>Inactive</option>
								<option value="Resolved" <?php echo ($elem_selList == "Resolved") ? "selected" : ""; ?>>Resolved</option>
								<option value="Unobserved" <?php echo ($elem_selList == "Unobserved") ? "selected" : ""; ?>>Unobserved</option>
								<option value="External" <?php echo ($elem_selList == "External") ? "selected" : ""; ?>>External</option>
							</select>
						</td>
						<td align="center">Date</td>
						<td align="center">Time</td>
						<td align="center">Op.ID</td>
						<td align="center"></td>
					</tr>
					<!-- Html Data to show -->
					<?php
						$row_id_number = 0;
						$rows_array = array();
						$class_name = "bgColor";
						if(count($arrProblemList)<=0)
						{
							echo "<tr><td colspan='10' class='text-center'>No Record Found.</td></tr>";
						}
						while($flagCreateRow == true)
						{
							if($class_name == ""){
								$class_name = "alt3";
							}else{
								$class_name = "";
							}
							
							if(count($arrProblemList)>0){
								$arrTmp = array_pop($arrProblemList);	//retrieve from last row and delete
								$rows_array[] = array('records' => $arrTmp, 'row_id' => ++$row_id_number, 'pid' => $pid, 'class_name' => $class_name,'arr_info_alert' => $arr_info_alert);
								//echo $problem_obj->prob_data_rows($arrTmp, ++$row_id_number, $pid, $class_name,$arr_info_alert);
							}
							else{
								$flagCreateRow = false;
							}
						}
						echo $problem_obj->prob_data_rows($rows_array);
					?>	
				</table>
			</div>
		</div>
		
		<!-- Bottom Row -->
    <!-- style="background-color:#2e4b76;color:white" -->
    <div class="col-sm-12 mt10 "  id="sub_table_List" >
    	
			<div class="row" style="padding-top:5px;border-top:solid 1px #c0c0c0; box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 2px 0.5px;" >
				<div class="col-sm-4">
                    <?php
                        $clomn1="";
                        $clomn2="col-sm-6";
                        $clomn3="col-sm-6";
                        if(isDssEnable()){
                            $clomn1="col-sm-4";
                            $clomn2="col-sm-4";
                            $clomn3="col-sm-4";
                        }
                    ?>
					<div class="row">
                        <?php if(isDssEnable()){ ?>
                            <div class="<?php echo $clomn1; ?>">
                                <label>Service Eligibility</label>
								
								<span id="dssPatSCPopup" class="glyphicon glyphicon-question-sign" onclick="top.dssLoadServiceConnectedOpt('problem_list')" data-scid="" data-toggle="tooltip" data-placement="top" title="Is this problem is service connected eligibility?" data-container="body"></span>
								<input type="hidden" name="service_eligibility" id="service_eligibility" value="">

                                <!-- <div class="checkbox">
                                    <input type="checkbox" name="service_eligibility" id="service_eligibility" value="0" autocomplete="off">
                                    <label for="service_eligibility">&nbsp;</label>
                                </div> -->
                            </div>	
                        <?php } ?>
						<div class="<?php echo $clomn2; ?>">
							<div class="input-group">
								<label>Onset Date</label>	
								<div class="input-group">
									<input type="text" name="list_date[]" id="list_date_0" value="<?php echo get_date_format(date('Y-m-d')); ?>" class="form-control datepicker pl_dt vs_dt" onClick="top.fmain.getDate_and_setToField('list_date_0', 'list_date_time0')" >
									<label for="list_date_0" class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</label>	
								</div>
							</div>	
						</div>	
						<div class="<?php echo $clomn3; ?>">
							<div class="input-group">
								<label>Onset Time</label>	
								<div class="input-group">
									<input type="text" class="form-control" value="<?php echo date("h:i:s A"); ?>" name="list_date_time[]" id="list_date_time0">
									<label for="list_date_time0" class="input-group-addon">
										<span class="glyphicon glyphicon-time"></span>
									</label>
								</div>
							</div>	
						</div>		
					</div>
				</div>

				<div class="col-sm-2">
					<label>Problem</label>	
<!--					<textarea name="list_problem[]" id="list_problem_0" rows="1" class="form-control" onChange="check4DxDesc_new(this);" onKeyPress="check4DxDesc_new(this);" onFocus="check_umls_pl(this,'0');"><?php echo($arrProblemListID[0]["problem_name"]);?></textarea>-->
					<textarea name="list_problem[]" id="list_problem_0" rows="1" class="form-control" onFocus="check_umls_pl(this,'0');"><?php echo($arrProblemListID[0]["problem_name"]);?></textarea>
				</div>

				<div class="col-sm-2">
					<label>Problem Type</label>	
					<select name="prob_type" id="prob_type" class="selectpicker" data-width="100%" data-size="5" data-title="Select">                                    	
						<?php 
							$arrProbType = array("","Disorder","Diagnosis","Finding","Problem","Condition","Symptom","Complaint","Functional Limitation");
							foreach ($arrProbType as $val) {
								echo "<option value='$val'>$val</option>";																			
							}					
						?>                                        
					</select>
				</div>

				<div class="col-sm-2">
					<div class="row">
						<div class="col-sm-12" id="list_status_0">
							<label>Status</label>
							<select name="list_status[]"  class="selectpicker" onChange="top.fmain.toggle_other_dropdown('list_status_other_0', 'list_status_0',this.value)" data-width="100%">
								<option value="Active">Active</option>
								<option value="Inactive">Inactive</option>
								<option value="Resolved">Resolved</option>
								<option value="Unobserved">Unobserved</option>
								<option value="External">External</option>	
								<option value="Other">Other</option>
							</select>	
						</div>	
						<div class="col-sm-12">
							<div class="col-sm-12 hide" id="list_status_other_0">
								<label>Other</label>
								<div class="input-group">
									<input type="text" name="list_status_other[]" class="form-control"  value="" />
									<label for="" class="input-group-addon" onClick="top.fmain.toggle_other_dropdown('list_status_0', 'list_status_other_0','Other');">
										<span class="glyphicon glyphicon glyphicon-arrow-left"></span>
									</label>	
								</div>	
							</div>	
						</div>
					</div>
				</div>

				<div class="col-sm-2">
					<label>SNOMED CT</label>	
					<input type="text" id="ccda_code" name="ccda_code" class="form-control" value="">
				</div>	
			</div>	
			<br />
		</div>	
	</form>	
</div>
<?php
if($policyStatus == 1 && $pkIdAuditTrailID != '' && isset($_SESSION['Patient_Viewed']) === true){
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
	$arrAuditTrailView_ProbList = array();
	$arrAuditTrailView_ProbList[0]['Pk_Id'] = $pkIdAuditTrailID;
	$arrAuditTrailView_ProbList[0]['Table_Name'] = 'pt_problem_list';
	$arrAuditTrailView_ProbList[0]['Action'] = 'view';
	$arrAuditTrailView_ProbList[0]['Operater_Id'] = $opreaterId;
	$arrAuditTrailView_ProbList[0]['Operater_Type'] = getOperaterType($opreaterId);
	$arrAuditTrailView_ProbList[0]['IP'] = $ip;
	$arrAuditTrailView_ProbList[0]['MAC_Address'] = $_REQUEST['macaddrs'];
	$arrAuditTrailView_ProbList[0]['URL'] = $URL;
	$arrAuditTrailView_ProbList[0]['Browser_Type'] = $browserName;
	$arrAuditTrailView_ProbList[0]['OS'] = $os;
	$arrAuditTrailView_ProbList[0]['Machine_Name'] = $machineName;
	$arrAuditTrailView_ProbList[0]['Category'] = 'patient_info-medical_history';
	$arrAuditTrailView_ProbList[0]['Filed_Label'] = 'Patient Problem List Data';
	$arrAuditTrailView_ProbList[0]['Category_Desc'] = 'prob_list';
	$arrAuditTrailView_ProbList[0]['Old_Value'] = $pkIdAuditTrail;
	$arrAuditTrailView_ProbList[0]['pid'] = $pid;
	
	$patientViewed = $_SESSION['Patient_Viewed'];
	if(is_array($patientViewed) && $patientViewed["Medical History"]["prob_list"] == 0){
		auditTrail($arrAuditTrailView_ProbList,$mergedArray,0,0,0);
		$patientViewed["Medical History"]["prob_list"] = 1;
		$_SESSION['Patient_Viewed'] = $patientViewed;
	}
}

if(trim($_SESSION['alertShowForThisSession']) != "Cancel"){
	echo $problem_obj->set_lab_cls_alerts();
}

?>
	<script>
		var vocabulary_arr = '<?php echo json_encode($arr_info_alert); ?>';
        var isDssEnable='<?php echo isDssEnable(); ?>';
        var zPath = "<?php echo $GLOBALS['rootdir'];?>";
	</script>
  <script src="<?php echo $library_path; ?>/js/icd10_autocomplete.js" type="text/javascript"></script>
  <script src="<?php echo $library_path; ?>/js/work_view/work_view.js" type="text/javascript"></script>
	<script src="<?php echo $library_path; ?>/js/med_problem_list.js" type="text/javascript"></script>
</body>
</html>