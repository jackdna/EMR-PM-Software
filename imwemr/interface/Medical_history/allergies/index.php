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

include_once($GLOBALS['srcdir']."/classes/medical_hx/allergies.class.php");
include_once($GLOBALS['srcdir']."/classes/CLSAlerts.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
$allergies_obj = new Allergies($medical->current_tab);
$callFrom  = '';
if($_REQUEST['callFrom']){
	$callFrom = $_REQUEST['callFrom'];
}

$search_by = 'Active';
if(empty($_REQUEST['allergy_act_status'])){
	$_REQUEST['allergy_act_status'] = $search_by;
}

$pid = $allergies_obj->patient_id;

//--- UPDATE ALLERGIES STATUS ----
if($_REQUEST['allergy_act_status'] != '' && empty($_REQUEST['ag_id']) != true){
	//making review in database - end
	$update_status = $allergies_obj->update_allergies_status($_REQUEST);	
}


//--- Saving Allergies Data ---
if(isset($_REQUEST['save_action']) && $_REQUEST['save_action'] == 'save_allergies'){
	$allergies_obj->save_allergies_data($_REQUEST);
}

if(isDssEnable()){
    $allergies_obj->dssLoadPatientAllergies();
}

//--- GET ALL ALLERGIRES DATA ----
$allergies_data = $allergies_obj->get_allergies_data($_REQUEST,$callFrom);
extract($allergies_data);


//--- Variables to be used in JS file
$global_js_var = array();
$global_js_var['callFrom'] 		 	 = $callFrom;
$global_js_var['Allow_erx_medicare'] = $Allow_erx_medicare;
$global_js_var['eRx_patient_id'] 	 = $eRx_patient_id;
$global_js_var['last_cnt'] 		 	 	 = $last_cnt;
$global_js_var['disable'] 		  	 = $disable;
$global_js_var['arr_info_alert'] 	 = $arr_info_alert;
$global_js_var['severityArr'] 	 	 = ag_severity();
$global_js_valariable = json_encode($global_js_var);
?>
<script>
	//Contains php variable values
	var global_php_var = $.parseJSON('<?php echo $global_js_valariable; ?>');
</script>
<script src="<?php echo $library_path; ?>/js/med_allergies.js" type="text/javascript"></script>	

	<div class="">
  	
    <div <?php echo ($_REQUEST['callFrom'] == 'WV' ? 'style=" min-height:'.($_REQUEST["divH"]).'px; max-height:'.($_REQUEST["divH"]).'px;overflow:hidden; overflow-y:auto;"' : ''); ?> >
		<!-- Extra Divs -->
		<div id="divMedicineAllergie" style="position:absolute;top:200px;left:200px;visibility:hidden; z-index:1000;"></div>	
		<!-- Search Div -->
		<div id="show_search_div" style="border:1px solid #CCC;height:200px;width:200px;overflow:scroll;overflow-x:hidden;display:none;background:#FFF"></div>
			
		<div id="controlsDiv" class="row">
			<form action="index.php?showpage=allergies&save_action=save_allergies" method="post" name="allergies_form" id="allergies_form">        	
				<input type="hidden" name="info_alert" id="info_alert" value="<?php echo ((is_array($arr_info_alert) && count($arr_info_alert) > 0) ? urlencode(serialize($arr_info_alert)) : "");?>">
				<input type="hidden" name="preObjBack" id="preObjBack" value="">
				<input type="hidden" name="next_tab" id="next_tab" value="">
				<input type="hidden" name="curr_tab" id="curr_tab" value="allergies">
				<input type="hidden" name="next_dir" id="next_dir" value="">
				<input type="hidden" name="last_cnt" id="last_cnt" value="<?php echo $last_cnt; ?>">
				<input type="hidden" name="allergy_change" id="allergy_change" value="">
				<input type="hidden" name="buttons_to_show" id="buttons_to_show" value="">
				<input type="hidden" name="today_date" id="today_date" value="<?php echo get_date_format(date("Y-m-d"));?>">
				<input type="hidden" name="callFrom" id="callFrom" value="<?php echo $callFrom;?>" />
        <input type="hidden" name="btSaveAllergies" id="btSaveAllergies" value="Done">
				<input type="hidden" name="hidAllergyIdVizChange" id="hidAllergyIdVizChange" value="">
				<input type="hidden" name="hidDelID" id="hidDelID" value="">
				<input type="hidden" name="hidDelMed" id="hidDelMed" value="">
				<!-- Heading Row -->
				<div class="col-sm-12">
					<div class="oculartop">
						<div class="row">
							<div class="col-sm-3">
							  <div class="eyetst eyetstname medicat">
								<figure>
									<div class="checkbox">
										<input type="checkbox" name="commonNoAllergies" id="commonNoAllergies" value="NoAllergies"<?php echo($checkAllergy); ?> onClick="<?php if($callFrom != "WV"){?>top.fmain.chk_change('<?php if($checkAllergy == "checked"){echo 'disabled';} ?>',this,event);<?php }?> allergy_change_fun();statusOfAllInputs();">
										<label for="commonNoAllergies">NKDA</label>
									</div>
								</figure>
								<h2>&nbsp;</h2>
							  </div>
							</div>
							<div class="col-sm-5 col-sm-offset-4 col-md-3 col-md-offset-6 form-inline pt10">
								<div class="row">
									<div class="col-sm-3 col-md-4 text-right allflter">
										<label>FILTER&nbsp;:</label>
									</div>	
									<div class="col-sm-9 col-md-8">
										<select name="allergy_act_status" class="form-control minimal" data-width="100%" onChange="formSubmit();">
											<option value="all">All</option>
											<option value="Active" <?php if($_REQUEST['allergy_act_status'] == 'Active') print 'selected'; ?>>Active</option>
											<option value="Suspended" <?php if($_REQUEST['allergy_act_status'] == 'Suspended') print 'selected'; ?>>Suspended</option>
											<option value="Aborted" <?php if($_REQUEST['allergy_act_status'] == 'Aborted') print 'selected'; ?>>Aborted</option>
											<option value="Deleted" <?php if($_REQUEST['allergy_act_status'] == 'Deleted') print 'selected'; ?>>Completed</option>
										</select>
									</div>	
								</div>
							</div>
						</div>
					</div>	
				</div>	
				
				<!-- Allergies data rows -->
				<div class="col-sm-12">
					<div class="row">
						<div class="table-responsive" id="allergy_row_data">
							<table class="table table-striped table-bordered">
								<thead>
									<tr class="grythead">
										<th width="10%">Drug</th>
										<th width="17%">Name</th>
										<th width="10%">Begin Date</th>
										<th width="17%">Reactions / Comments</th>
                    <th class="col-xs-1">Severity</th>
										<th class="col-xs-1">Status</th>
										<th width="8%">Code</th>
										<th width="1%">Hx</th>
										<th width="1%">Del</th>
									</tr>	
								</thead>
								<tbody id="allergies_tb">
									<?php
										echo $allergy_page_data;
									?>
									<!-- Emplty last row -->
									<tr id="tblag_<?php print $last_cnt; ?>">
										<td>
											<select name="ag_occular_drug<?php print $last_cnt; ?>" id="ag_occular_drug<?php print $last_cnt; ?>" class="form-control minimal dropup" onChange="<?php if($callFrom != "WV"){?>top.fmain.chk_change('',this,event);<?php }?> allergy_change_fun();" data-dropup-auto="false" data-width="100%">
												<option value="fdbATDrugName">Drug</option>
												<option value="fdbATIngredient">Ingredient</option>
												<option value="fdbATAllergenGroup">Allergen</option>
											</select>
										</td>
										<td>
											<input type="text" id="textTitleA<?php print $last_cnt; ?>" tabindex="<?php print $last_cnt; ?>" onChange="<?php if($callFrom != "WV"){?>top.fmain.chk_change('',this,event);<?php }?> allergy_change_fun();" onKeyUp="search_erx_allergy(this.value, '<?php print $last_cnt; ?>');<?php if($callFrom != "WV"){?>top.fmain.chk_change('',this,event);<?php }?>" onMouseDown="addNewAllergie(event,this);" value="" class="form-control" name="ag_title<?php print $last_cnt; ?>" >
											<input type="hidden" id="hiddenTitleA<?php print $last_cnt; ?>" name="hiddenTitleA<?php print $last_cnt; ?>" value="" />
										</td>
										<td>
											<div class="input-group">
												<input type="text" id="ag_begindate<?php print $last_cnt; ?>" tabindex="<?php print $last_cnt; ?>" name="ag_begindate<?php print $last_cnt; ?>" onKeyUp="<?php if($callFrom != "WV"){?>top.fmain.chk_change('',this,event);<?php }?>" value="" class="datepicker form-control allergy_bg_date" onChange="<?php if($callFrom != "WV"){?>top.fmain.chk_change('',this,event); <?php }?>allergy_change_fun();checkdate(this);" maxlength="10" /> 
												<label for="ag_begindate<?php print $last_cnt; ?>" class="input-group-addon">
													<span class="glyphicon glyphicon-calendar"></span>
												</label>	
											</div>                      
										</td>
										<td>
											<input type="hidden" name="ag_reaction_code<?php print $last_cnt; ?>" id="ag_reaction_code<?php print $last_cnt; ?>" value="" /> 
                      <textarea class="form-control" id="ag_comments<?php print $last_cnt; ?>" tabindex="<?php print $last_cnt; ?>" rows="1" name="ag_comments<?php print $last_cnt; ?>" onKeyDown="indexEnt();" onKeyUp="<?php if($callFrom != "WV"){?>top.fmain.chk_change('',this,event);<?php }?>" onChange="allergy_change_fun();get_rx_code(this,'<?php print $last_cnt; ?>');" onFocus="get_rx_code(this,'<?php print $last_cnt; ?>');"></textarea>
										</td>
                    <td>
                    	<?php $severityArr = ag_severity(); ?>
                    	<select name="ag_severity<?php print $last_cnt; ?>" class="form-control minimal dropup" onChange="<?php if($callFrom != "WV"){?>top.fmain.chk_change('',this,event);<?php }?> allergy_change_fun();" title="Select" data-dropup-auto="false" data-width="100%">
                            <option value="">Select</option>
                      	<?php
													foreach( $severityArr as $val => $severity)
													{
														echo '<option value="'.$val.'" >'.$severity['value'].'</option>';		
													}
												?>
											</select>
										</td>
										<td>
											<select name="ag_status<?php print $last_cnt; ?>" class="form-control minimal dropup" onChange="<?php if($callFrom != "WV"){?>top.fmain.chk_change('',this,event);<?php }?> allergy_change_fun();" data-dropup-auto="false" data-width="100%">
												<option value="Active" <?php if($_REQUEST['allergy_act_status'] - 1 == 0) print 'selected'; ?>>Active</option>
												<option value="Suspended" <?php if($_REQUEST['allergy_act_status'] - 1 == 1) print 'selected'; ?>>Suspended</option>
												<option value="Aborted" <?php if($_REQUEST['allergy_act_status'] - 1 == 2) print 'selected'; ?>>Aborted</option>
											</select>
										</td>
										<td>
										 <input type="text" id="ccda_code<?php print $last_cnt; ?>" tabindex="<?php print $last_cnt; ?>" name="ccda_code<?php print $last_cnt; ?>" onKeyUp="<?php if($callFrom != "WV"){?>top.fmain.chk_change('',this,event);<?php }?>" value=""  class="form-control allergy_bg_date" onChange="<?php if($callFrom != "WV"){?>top.fmain.chk_change('',this,event); <?php }?>allergy_change_fun();" maxlength="10" />   	
										</td>
										<td style="text-align:center;">
											<a href='#' title='Changes History' data-toggle='popover' data-trigger='focus' data-content='No History' data-html='true' data-placement='left'><img src='../../library/images/search.png' width="20px" height="auto"></a>
										</td>
										<td class="text-center">
											<span id="add_row_<?php print $last_cnt; ?>" class="glyphicon glyphicon-plus pointer" alt="Add More" onClick="addNewRow('<?php print $last_cnt; ?>');" ></span>
										</td>
									</tr>	
								</tbody>	
							</table>	
						</div>
					</div>
				</div>

			</form>
		</div>
		
  	</div>
    
    <!-- Footer buttons -->
    <?php if(isset($callFrom) && $callFrom == "WV"){ ?>
    <div class="panel-footer ad_modal_footer" id="module_buttons">
    	<input type="button" id="btSaveAllergies_btn" name="btSaveAllergies_btn" class="btn btn-success" value="Done" />
      <input type="button" id="btClose" name="btClose" class="btn btn-danger" value="Cancel" onClick="window.close();" />
  	</div>
    <?php } ?>	
  </div>

	<?php
		//--- GET AUDIT STATUS FROM POLICIES -----
		$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];

		//--- AUDIT TRAIL FOR VIEW ONLY ----
		if($policyStatus == 1 and trim($pkIdAuditTrailID) != '' and isset($_SESSION['Patient_Viewed']) === true){
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
			
			$arrAuditTrailView_Allergies = array();
			$arrAuditTrailView_Allergies[0]['Pk_Id'] = $pkIdAuditTrailID;
			$arrAuditTrailView_Allergies[0]['Table_Name'] = 'lists';
			$arrAuditTrailView_Allergies[0]['Action'] = 'view';
			$arrAuditTrailView_Allergies[0]['Operater_Id'] = $opreaterId;
			$arrAuditTrailView_Allergies[0]['Operater_Type'] = getOperaterType($opreaterId);
			$arrAuditTrailView_Allergies[0]['IP'] = $ip;
			$arrAuditTrailView_Allergies[0]['MAC_Address'] = $_REQUEST['macaddrs'];
			$arrAuditTrailView_Allergies[0]['URL'] = $URL;
			$arrAuditTrailView_Allergies[0]['Browser_Type'] = $browserName;
			$arrAuditTrailView_Allergies[0]['OS'] = $os;
			$arrAuditTrailView_Allergies[0]['Machine_Name'] = $machineName;
			$arrAuditTrailView_Allergies[0]['Category'] = 'patient_info-medical_history';
			$arrAuditTrailView_Allergies[0]['Filed_Label'] = 'Patient Allergies Data';
			$arrAuditTrailView_Allergies[0]['Category_Desc'] = 'allergies';
			$arrAuditTrailView_Allergies[0]['Old_Value'] = $pkIdAuditTrail;
			$arrAuditTrailView_Allergies[0]['pid'] = $pid;
			
			$patientViewed = $_SESSION['Patient_Viewed'];
			if(is_array($patientViewed) && $patientViewed["Medical History"]["Allergies"] == 0){
				auditTrail($arrAuditTrailView_Allergies,$mergedArray,0,0,0);
				$patientViewed["Medical History"]["Allergies"] = 1;
				$_SESSION['Patient_Viewed'] = $patientViewed;
			}
		}
		
		if(trim($_SESSION['alertShowForThisSession']) != "Cancel"){
			echo $allergies_obj->set_cls_alerts();
		}
	?>
<script>
$(document).ready(function(){
	//$('.selectpicker').selectpicker('refresh');
    $('[data-toggle="popover"]').popover();   
    statusOfAllInputs();
});
</script>	

<!-- Add/Modify Allergies Modal -->
<div id="myModal" class="modal fade" role="dialog">
<div class="modal-dialog">
	<!-- Modal content-->
	<div class="modal-content">
		<!-- Header -->
		<div class="modal-header bg-primary">
			<div class="row">
				<form id="allergy_details_form" onsubmit="return false;">
					<div class="col-sm-4">
						<h4 class="modal-title">Allergies Type Ahead</h4>
					</div>
					<div class="col-sm-7 text-cenrter">
						<div class="row">
							<div class="col-sm-9">
								<input type="text" id="allergy_name" name="allergy_name" class="form-control" placeholder="Add allergy" onKeyUp="top.fmain.filter_table_allergy(this.value,'allergies_name_tbl');">
								<input type="hidden" id="allergy_id" name="allergy_id">	
							</div>	
							<div class="col-sm-3">
								<input type="button" class="btn btn-success" name="action" value="Save" id="save_btn" onClick="save_allergy_data(this,'allergy_details_form');">
							</div>	
						</div>
					</div>	
					<div class="col-sm-1">
						<button type="button" class="close" data-dismiss="modal" onclick="close_allergy()">&times;</button>
						<span id="input_id_val" class="hide"></span>		
					</div>	
				</form>	
			</div>
		</div>
		<!-- Content -->
		<div class="modal-body" style="max-height:300px;overflow-y:scroll">
			<div class="row">
			Loading! Please wait. ...
			</div>
		</div>
		<div id="module_buttons" class="modal-footer ad_modal_footer">
			<div class="row">
				<div class="text-center">
					<button type="button" class="btn btn-primary hide" id="add_btn" onClick="top.fmain.modify_allergy_name('add');">Add Record</button>
					<button type="button" class="btn btn-success" id="show_all_btn" onClick="top.fmain.load_allergy_ta('1');">Show All</button>
					<button data-dismiss="modal" class="btn btn-danger" onclick="close_allergy();">Close</button>
				</div>	
			</div>
		</div>
	</div>	
</div>
</div>
