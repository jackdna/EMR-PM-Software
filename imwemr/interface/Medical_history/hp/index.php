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
include_once($GLOBALS['srcdir']."/classes/medical_hx/history_physical.class.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
$historyPhysical = new HistoryPhysical($medical->current_tab);
extract($historyPhysical->data);
$pt_custom_quesArr=$historyPhysical->pt_custom_ques;
$custom_quesArr=$historyPhysical->custom_ques;
$pid=$historyPhysical->patient_id;

function print_row_hp($label,$chkBoxValue,$chkBoxName,$txtAreaValue,$txtAreaName,$xtraData = '', $revDescEnable = '')
{
	$html = '';
	$tmpArr = array('Smoke' => 'How much', 'Drink Alcohol' => 'How much', 'Have any Metal Prosthetics' => 'Notes');
	$yesFun = "disp";
	$noFun = "disp_none";
	$chkBoxClass = ($chkBoxValue=='No' || $chkBoxValue=='Yes') ? '' : 'checkbox-mandatory';
	$txtRowClass= ($chkBoxValue=='Yes') ? '' : 'hidden';
	if($revDescEnable == 'enableDescOnNo') {
		$txtRowClass= ($chkBoxValue=='No') ? '' : 'hidden';	
		$yesFun = "disp_none";
		$noFun 	= "disp";
	}
	if(!trim($txtAreaName)) {
		$yesFun = "disp_none";
		$noFun 	= "disp_none";
	}
	$placeholder = (array_key_exists($label,$tmpArr) ? $tmpArr[$label] : 'Describe');
	
	//if($chkBoxValue=='Yes') { echo 'glyphicon-menu-up';}else { echo 'glyphicon-menu-down';}
	
	$html .= '
		<div class="col-xs-9" style="margin-top:3px;">
			<label>'.$label.'</label>
		</div>
		
		<div class="col-xs-3">
			<div class="row">
				<div class="col-xs-5 text-center">
					<div class="checkbox '.$chkBoxClass.'">
						<input '.($chkBoxValue=='Yes' ? "checked" : '').' name="'.$chkBoxName.'" type="checkbox" value="Yes" id="'.$chkBoxName.'_yes" onChange="javascript:checkSingle(\''.$chkBoxName.'_yes\',\''.$chkBoxName.'\'); chk_change(\'Yes\',this,event); changeChbxColor(\''.$chkBoxName.'\'); '.$yesFun.'(this,\''.$chkBoxName.'_row\');">
						<label for="'.$chkBoxName.'_yes"></label>
					</div>
				</div>
				
				<div class="col-xs-4 text-center">
					<div class="checkbox '.$chkBoxClass.'">
						<input '.($chkBoxValue=='No' ? "checked" : '').' name="'.$chkBoxName.'" type="checkbox" value="No" id="'.$chkBoxName.'_no" onChange="javascript:checkSingle(\''.$chkBoxName.'_no\',\''.$chkBoxName.'\'); chk_change(\'No\',this,event); changeChbxColor(\''.$chkBoxName.'\'); '.$noFun.'(this,\''.$chkBoxName.'_row\'); ">
						<label for="'.$chkBoxName.'_no"></label>
					</div>
				</div>';
	
        if(trim($txtAreaName)) {
            $html .= '			
                    <div class="col-xs-3 text-center ">
                        <i class="glyphicon '.($chkBoxValue=='Yes' ? "glyphicon-menu-up" : 'glyphicon-menu-down').' pd5 pointer" id="up_dwn_'.$chkBoxName.'_row" onClick="javascript:disp_rev(this,\''.$chkBoxName.'_row\')"></i>
                    </div>';
        }
        $html .= '				
            </div>
        </div>

        <div class="clearfix"></div> '; 
        if(trim($txtAreaName!='')) {
            $html .= '  <div class="col-xs-12 pd5 '.$txtRowClass.'" id="'.$chkBoxName.'_row" style="background-color:#efefef;">
                '.$xtraData.'
                <textarea id="'.$txtAreaName.'" placeholder="'.$placeholder.'" class="form-control" name="'.$txtAreaName.'" onKeyUp="chk_change(\''.stripslashes($txtAreaValue).'\',this,event);">'.stripslashes($txtAreaValue).'</textarea>
            </div>';	
        }
	return $html;		
}


$custom_ques_html='';
if( $custom_quesArr ) {
	foreach($custom_quesArr as $custom_ques) {
		$custom_ques_name=stripslashes($custom_ques['name']);
    $custom_id=$custom_ques['id'];
		
		$pt_ques_status=$pt_custom_quesArr[$custom_id]['ques_status'];
    $pt_ques_desc=$pt_custom_quesArr[$custom_id]['ques_desc'];
		
		$custom_ques_html .= print_row_hp($custom_ques_name,$pt_ques_status,'chbx_custom_'.$custom_id,$pt_ques_desc,'custom_desc_'.$custom_id);
		
	}
}

?>
<style type="text/css">
	.input-group-addon { font-size:11px; padding:3px!important;}
	.popover { min-width:350px!important;max-width:350px!important;}
	.popover-content {min-height:400px; }
</style>
<script>
function disp_rev(_this,elem_id) {
	
	var elem_obj = $("#"+elem_id);
	var $_this = $(_this);
	if( elem_obj.hasClass('hidden') )
	{
		elem_obj.removeClass('hidden');
		$_this.addClass('glyphicon-menu-up')
				 .removeClass('glyphicon-menu-down');	
		
	}
	else
	{
		elem_obj.addClass('hidden');
		$_this.addClass('glyphicon-menu-down')
				 .removeClass('glyphicon-menu-up');
		
	}
}

function disp(_this,elem_id) {
	var $_this = $(_this);
	if( $_this.is(':checked') ) {
		$('#' + elem_id).removeClass('hidden');
		$('#' + 'up_dwn_'+elem_id).removeClass('glyphicon-menu-down');
		$('#' + 'up_dwn_'+elem_id).addClass('glyphicon-menu-up');
	}
	else {
		$('#' + elem_id).addClass('hidden');
		$('#' + 'up_dwn_'+elem_id).removeClass('glyphicon-menu-up');
		$('#' + 'up_dwn_'+elem_id).addClass('glyphicon-menu-down');
	}
}

function disp_none(_this,elem_id) {
	var $_this = $(_this);
	if( $_this.is(':checked') ) {
		$('#' + elem_id).addClass('hidden');
		$('#' + 'up_dwn_'+elem_id).removeClass('glyphicon-menu-up');
		$('#' + 'up_dwn_'+elem_id).addClass('glyphicon-menu-down');
	}
}
</script>
<body>
<div class="col-xs-12">
	
 	<form action="<?php echo $folder;?>/save.php" method="post" name="hp_form" id="hp_form">
<!--	<input type="hidden" name="ptFormId" id="ptFormId" value="<?php echo $vs_data['Pt_Form_Id']; ?>">-->
  	<input type="hidden" name="info_alert" id="info_alert" value="<?php echo ((is_array($historyPhysical->vocabulary) && count($historyPhysical->vocabulary) > 0) ? urlencode(serialize($historyPhysical->vocabulary)) : "");?>">
    <input type="hidden" name="patient_id_hp" id="patient_id_hp" value="<?php echo $historyPhysical->patient_id; ?>">
    <input type="hidden" name="preObjBack" id="preObjBack" value="">
    <input type="hidden" name="curr_tab" id="curr_tab" value="hp"><?php //echo $historyPhysical->current_tab;?>
    <input type="hidden" name="next_tab" id="next_tab" value="">
    <input type="hidden" name="next_dir" id="next_dir" value="">
    <input type="hidden" name="record_count" id="record_count" value="<?php echo $record_count; ?>">
    <input type="hidden" name="buttons_to_show" id="buttons_to_show" value="">
    
    <input type="hidden" value="<?php echo $history_physicial_id; ?>" name="history_physicial_id" id="history_physicial_id"/>
    
    <div class="col-xs-12 col-sm-6">
       		<div class="col-xs-12">
       			<div class="row">
            	<div class="col-lg-12 col-md-12 col-sm-12 advancedirect">
      					<div class="head"><span class="valign_mid">Advance Directive</span></div>
        				<div class="clearfix"></div>
								<div class="row">
									<div class="col-sm-5">
										<label for="">Advance Directive</label><br>
										<?php
											//and form_id='0'
											$query = "select * from ".constant("IMEDIC_SCAN_DB").".scans where patient_id = '".$historyPhysical->patient_id."' And image_form = 'ptInfoMedHxGeneralHealth'";
											$sql = imw_query($query);
											$row = imw_fetch_assoc($sql);
											$file_name = $row['file_path'];
											$ad_path = substr(data_path(),0,-1).$file_name;
											$ad_web_path = substr(data_path(1),0,-1).$file_name;
											$scan_id = $row["scan_id"];
										?>
										<div id="div_ado_option" <?php echo $ptAdoOption=="Other" ? 'class="hidden"' : '' ?> >
											<select class="selectpicker" data-width="100%" data-size="10" onChange="javascript:show_hide('other_ado_option','div_ado_option',this); chk_change('',this,event);" name="ado_option" id="ado_option" title="Advance Directive" >
												<option <?php echo($ptAdoOption=="NA" ? "selected" : ""); ?> value="NA">NA</option>
												<option <?php echo($ptAdoOption=="No" ? "selected" : ""); ?> value="No">No</option>
												<option <?php echo($ptAdoOption=="Living Will" ? "selected" : ""); ?> value="Living Will">Living Will</option>
												<option <?php echo($ptAdoOption=="Power of Attorney" ? "selected" : ""); ?> value="Power of Attorney">Power of Attorney</option>
												<option <?php echo($ptAdoOption=="Other" ? "selected" : ""); ?> value="Other">Other</option>
											</select>
										</div>

										<div class="<?php echo $ptAdoOption=="Other" ? '' : 'hidden' ?>" id="other_ado_option">
											<div class="input-group">
												<input type="text" class="form-control" id="ado_other_txt" name="ado_other_txt" onKeyUp="chk_change('<?php echo addslashes($ptDescAdoOtherTxt); ?>',this,event);" value="<?php echo $ptDescAdoOtherTxt; ?>" />
												<label class="input-group-addon btn btn-success back_other" data-tab-name="ado_option">
													<span class="glyphicon glyphicon-arrow-left"></span>
												</label>
											</div>
										</div>        

									</div>

									<div class="col-sm-2"><br>
										<label class="btn btn-success mt5 btn-xs" onClick="ado_scan_fun('scan', 'ptInfoMedHxGeneralHealth')" style="font-size:13px;">
											<i class="glyphicon glyphicon-print"></i>
										</label>
										<span id="scnGenHlthId">
											<?php if( $scan_id <> '' ){ ?>
											<label class="btn btn-success mt5 btn-xs" id="" data-path="<?php echo $ad_web_path;?>" onClick="showAD(this)" style="font-size:13px;">
												<i class="glyphicon glyphicon-open-file"></i>
											</label>
										<?php } ?>
										</span>
									</div>

								</div>
								<div class="clearfix"></div>
							</div>
					
						</div>
					</div>
        	
         	<div class="col-xs-12 border">
            
            <div class="row">	
              <div class="col-xs-12 head sub_head">
                <div class="col-xs-9">
                  History And Physical
                </div>
                <div class="col-xs-3">
                  <div class="row">
                    <div class="col-xs-5 text-center">Yes</div>
                    <div class="col-xs-4 text-center">No</div>
                    <div class="col-xs-3 text-center">&nbsp;</div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Heart Trouble/Heart Attack -->
            <div class="row">
              <?php 
                echo print_row_hp('CAD/MIN(W/ WO Stent OR CABG)/PVD)',$cadMI,'chbx_cad_mi',$cadMIDesc,'cadMIDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('CVA/TIA/ Epilepsy, Neurological',$cvaTIA,'chbx_cva_tia',$cvaTIADesc,'cvaTIADesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('HTN/ +/- CP/SOB on Exertion',$htnCP,'chbx_htn_cp',$htnCPDesc,'htnCPDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('Anticoagulation therapy (i.e. Blood Thinners)',$anticoagulationTherapy,'chbx_anticoagulation_therapy',$anticoagulationTherapyDesc,'anticoagulationTherapyDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('Respiratory - Asthma / COPD / Sleep Apnea',$respiratoryAsthma,'chbx_respiratory_asthma',$respiratoryAsthmaDesc,'respiratoryAsthmaDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('Arthritis',$arthritis,'chbx_arthritis',$arthritisDesc,'arthritisDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('Diabetes',$diabetes,'chbx_diabetes',$diabetesDesc,'diabetesDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('Recreational Drug Use',$recreationalDrug,'chbx_recreational_drug',$recreationalDrugDesc,'recreationalDrugDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('GI - GERD / PUD / Liver Disease / Hepatitis',$giGerd,'chbx_gi_gerd',$giGerdDesc,'giGerdDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('Ocular',$ocular,'chbx_ocular',$ocularDesc,'ocularDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('Kidney Disease, Dialysis, G-U',$kidneyDisease,'chbx_kidney_disease',$kidneyDiseaseDesc,'kidneyDiseaseDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('HIV, Autoimmune Diseases, Contagious Diseases',$hivAutoimmune,'chbx_hiv_autoimmune',$hivAutoimmuneDesc,'hivAutoimmuneDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('History of Cancer',$historyCancer,'chbx_history_cancer',$historyCancerDesc,'historyCancerDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('Organ Transplant',$organTransplant,'chbx_organ_transplant',$organTransplantDesc,'organTransplantDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('A Bad Reaction to Local or General Anesthesia',$badReaction,'chbx_bad_reaction',$badReactionDesc,'badReactionDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('High Cholesterol',$highCholesterol,'chbx_high_cholesterol',$highCholesterolDesc,'highCholesterolDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('Thyroid Problems',$thyroid,'chbx_thyroid',$thyroidDesc,'thyroidDesc');
              ?>
            </div>
            <div class="row">
              <?php 
                echo print_row_hp('Ulcers',$ulcer,'chbx_ulcer',$ulcerDesc,'ulcerDesc');
              ?>
            </div>
            
            <div class="row pd10" style="background-color:#D1E0C9;">
            	<div class="col-xs-2 col-md-1 ">
            		<label><b>Other</b></label>
            	</div>
            	<div class="col-xs-10 col-md-11">
            		<textarea id="otherHistoryPhysical" name="otherHistoryPhysical" class="form-control"><?php echo stripslashes($otherHistoryPhysical); ?></textarea>
            	</div>
            </div>
            
            <!--<div class="row">
              <?php 
                echo print_row_hp('Discussed Advanced Directives and Patient Rights and Responsibilities',$discussedAdvancedDirective,'chbx_advance_directive','','');
              ?>
            </div>-->
   		</div>
   </div>
    
    <div class="col-xs-12 col-sm-6">
        <div class="col-xs-12 border">
            <div class="row">	
              <div class="col-xs-12 head sub_head">
                <div class="col-xs-9">
                  History And Physical
                </div>
                <div class="col-xs-3">
                  <div class="row">
                    <div class="col-xs-5 text-center">Yes</div>
                    <div class="col-xs-4 text-center">No</div>
                    <div class="col-xs-3 text-center">&nbsp;</div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row">
							<?php 
								echo print_row_hp('Heart Exam done with stethoscope - Normal',$heartExam,'chbx_heart_exam',$heartExamDesc,'heartExamDesc','','enableDescOnNo');
							?>
						</div>
						<div class="row">
							<?php 
								echo print_row_hp('Lung Exam done with stethoscope - Normal',$lungExam,'chbx_lung_exam',$lungExamDesc,'lungExamDesc','','enableDescOnNo');
							?>
						</div>
           	<?php if($custom_ques_html) { ?>
							<div class="row">
								<?php echo $custom_ques_html; ?>
							</div>
            <?php } ?>
            
        </div>
        <?php
					$procArr = $historyPhysical->load_sx_procedures();
				?>
        
        <div class="col-xs-12 border mt5">
            <div class="row">	
              <div class="col-xs-12 head sub_head">
                <div class="col-xs-12">
                  Ocular Sx/Procedures
                </div>
            	</div>
            
            	<div class="col-xs-12"><div class="row">
									<table class="table table-bordered table-hover table-striped scroll release-table">
										<thead class="header">
											<tr class="grythead">
												<th class="col-xs-3">Name</th>
												<th class="col-xs-2">Site</th>
												<th class="col-xs-2">Date</th>
												<th class="col-xs-5">Comments</th>
											</tr>
										</thead>
										<tbody>
										<?php
											$html = '';
											if( is_array($procArr['ocu']) && count($procArr['ocu']) )	{

												foreach($procArr['ocu'] as $proc){
													$html .= '<tr>';
													$html .= '<td>'.$proc['name'].'</td>';	
													$html .= '<td>'.$proc['site'].'</td>';	
													$html .= '<td>'.$proc['beg_date'].'</td>';	
													$html .= '<td>'.$proc['comment'].'</td>';	
													$html .= '<tr>';
												}
											} else{
												$html = '<tr><td colspan="4" class="text-center">No Record found</td></tr>';
											}	
											echo $html;
										?>
										</tbody>
									</table>
								</div></div>
            
            </div>
        </div>
        
        <div class="col-xs-12 border mt5">
            <div class="row">	
              <div class="col-xs-12 head sub_head">
                <div class="col-xs-12">
                  Other Sx/Procedures
								</div>
							</div>	
							<div class="col-xs-12"><div class="row">
								<table class="table table-bordered table-hover table-striped scroll release-table">
									<thead class="header">
										<tr class="grythead">
											<th class="col-xs-5">Name</th>
											<th class="col-xs-2">Date</th>
											<th class="col-xs-5">Comments</th>
										</tr>
									</thead>
									<tbody>
									<?php
										$html = '';
										if( is_array($procArr['sys']) && count($procArr['sys']) )	{

											foreach($procArr['sys'] as $proc){
												$html .= '<tr>';
												$html .= '<td>'.$proc['name'].'</td>';	
												$html .= '<td>'.$proc['beg_date'].'</td>';	
												$html .= '<td>'.$proc['comment'].'</td>';	
												$html .= '<tr>';
											}
										} else{
											$html = '<tr><td colspan="3" class="text-center">No Record found</td></tr>';
										}	
										echo $html;
									?>
									</tbody>
								</table>
							</div></div>
            	
            </div>
        </div>
        
        
    </div>
    
    <div class="clearfix">&nbsp;</div> 	
    <?php /* 
     * pending review functionality
     * 
     * $serialized=urlencode(serialize($historyPhysical->data));?>
    <?php $serialized_custom=urlencode(serialize($pt_custom_quesArr));?>
    
    <div class="clearfix">&nbsp;</div> 	
    <input type="hidden" name="hidDataMedicalHistory_History_physical" value="<?php echo ($historyPhysical->policy_status == 1) ? $serialized : ''; ?>">
    <input type="hidden" name="hidDataMedicalHistory_History_physical_custom" value="<?php echo ($historyPhysical->policy_status == 1) ? $serialized_custom : ''; ?>">
    <?php
			//--- REVIEWED CODE FOR CREATE ARRAY ---
			$operatorId = $_SESSION['authId'];
			$action = ($record_count == 0) ? 'add' : 'update';
			require_once($GLOBALS['srcdir'].'/classes/class.cls_review_med_hx.php'); 
			$OBJReviewMedHx = new CLSReviewMedHx;				
			$arrReview_GH = array();
			//$arrReview_GH = $OBJReviewMedHx->getReviewArrayAD($gen_medicine,$operatorId,$action);
			
			?>			
			<input type="hidden" name="hid_arr_review_GH" value="<?php echo urlencode(serialize($arrReview_GH)); ?>"/> */?>
			<input type="hidden" name="hp_page_load_done" id="hp_page_load_done" value="no">
 	</form>
</div>
<!-- Show Advanced Directive Image -->
<div id="ad_modal" class="modal" role="dialog">
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Advanced Directive</h4>
     	</div>
      
      <div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">
      	<div class="loader-small"></div>
     	</div>
      
      <div id="module_buttons" class="modal-footer ad_modal_footer">
        <button type="button" class="btn btn-danger"  data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>
<?php


/* pending audit functionality
//--- AUDIT TRAIL FOR VIEW -----
if(isset($_SESSION['Patient_Viewed']) === true and $historyPhysical->policy_status == 1){
	$ip = getRealIpAddr();
	$URL = $_SERVER['PHP_SELF'];													 
	
	$os = getOS();
	$browserInfoArr = array();
	$browserInfoArr = _browser();
	$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
	$browserName = str_replace(";","",$browserInfo);													 
	$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$arrAuditTrailView_HP = array();
	$arrAuditTrailView_HP[0]['Pk_Id'] = $gen_medicine['history_physicial_id'];
	$arrAuditTrailView_HP[0]['Table_Name'] = 'surgerycenter_pt_history_physical';
	$arrAuditTrailView_HP[0]['Action'] = 'view';
	$arrAuditTrailView_HP[0]['Operater_Id'] = $opreaterId;
	$arrAuditTrailView_HP[0]['Operater_Type'] = getOperaterType($opreaterId);
	$arrAuditTrailView_HP[0]['IP'] = $ip;
	$arrAuditTrailView_HP[0]['MAC_Address'] = $_REQUEST['macaddrs'];
	$arrAuditTrailView_HP[0]['URL'] = $URL;
	$arrAuditTrailView_HP[0]['Browser_Type'] = $browserName;
	$arrAuditTrailView_HP[0]['OS'] = $os;
	$arrAuditTrailView_HP[0]['Machine_Name'] = $machineName;
	$arrAuditTrailView_HP[0]['Category'] = 'patient_info-medical_history';
	$arrAuditTrailView_HP[0]['Filed_Label'] = 'Patient History Physical Data';
	$arrAuditTrailView_HP[0]['Category_Desc'] = 'history_physical';
	$arrAuditTrailView_HP[0]['pid'] = $pid;
	$patientViewed = $_SESSION['Patient_Viewed'];

	if(is_array($patientViewed) && $patientViewed["Medical History"]["History_physical"] == 0){
		auditTrail($arrAuditTrailView_HP,$mergedArray);
		$patientViewed["Medical History"]["History_physical"] = 1;			
		$_SESSION['Patient_Viewed'] = $patientViewed;
	}
} */
?>  
<script>
	
	function showAD(_this) {
		var s = $(_this).data('path');
		var i = '<img src="'+s+'" title="Advanced Directive" />';
		$("#ad_modal").find('.modal-body').html(i);
		$("#ad_modal").modal('show');	
	}
	
	var vocabulary_hp = <?php echo json_encode($health->vocabulary); ?>;
  top.btn_show("HP");
	document.getElementById('hp_page_load_done').value='yes';
</script>