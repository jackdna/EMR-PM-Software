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
require_once("../../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/medical_hx/history_physical.class.php");

$historyPhysical = new HistoryPhysical($medical->current_tab);
$custom_quesArr=$historyPhysical->custom_ques;
$pt_custom_quesArr=$historyPhysical->pt_custom_ques;

$hpArr=array();
$history_physicial_id=$_REQUEST['history_physicial_id'];
$hpArr['patient_id']=$_REQUEST['patient_id_hp'];

$hpArr['cadMI']=$_REQUEST['chbx_cad_mi'];
$hpArr['cadMIDesc']=($_REQUEST['chbx_cad_mi']=='Yes')?$_REQUEST['cadMIDesc']:'';

$hpArr['cvaTIA']=$_REQUEST['chbx_cva_tia'];
$hpArr['cvaTIADesc']=($_REQUEST['chbx_cva_tia']=='Yes')?$_REQUEST['cvaTIADesc']:'';

$hpArr['htnCP']=$_REQUEST['chbx_htn_cp'];
$hpArr['htnCPDesc']=($_REQUEST['chbx_htn_cp']=='Yes')?$_REQUEST['htnCPDesc']:'';

$hpArr['anticoagulationTherapy']=$_REQUEST['chbx_anticoagulation_therapy'];
$hpArr['anticoagulationTherapyDesc']=($_REQUEST['chbx_anticoagulation_therapy']=='Yes')?$_REQUEST['anticoagulationTherapyDesc']:'';

$hpArr['respiratoryAsthma']=$_REQUEST['chbx_respiratory_asthma'];
$hpArr['respiratoryAsthmaDesc']=($_REQUEST['chbx_respiratory_asthma']=='Yes')?$_REQUEST['respiratoryAsthmaDesc']:'';

$hpArr['arthritis']=$_REQUEST['chbx_arthritis'];
$hpArr['arthritisDesc']=($_REQUEST['chbx_arthritis']=='Yes')?$_REQUEST['arthritisDesc']:'';

$hpArr['diabetes']=$_REQUEST['chbx_diabetes'];
$hpArr['diabetesDesc']=($_REQUEST['chbx_diabetes']=='Yes')?$_REQUEST['diabetesDesc']:'';

$hpArr['recreationalDrug']=$_REQUEST['chbx_recreational_drug'];
$hpArr['recreationalDrugDesc']=($_REQUEST['chbx_recreational_drug']=='Yes')?$_REQUEST['recreationalDrugDesc']:'';

$hpArr['giGerd']=$_REQUEST['chbx_gi_gerd'];
$hpArr['giGerdDesc']=($_REQUEST['chbx_gi_gerd']=='Yes')?$_REQUEST['giGerdDesc']:'';

$hpArr['ocular']=$_REQUEST['chbx_ocular'];
$hpArr['ocularDesc']=($_REQUEST['chbx_ocular']=='Yes')?$_REQUEST['ocularDesc']:'';

$hpArr['kidneyDisease']=$_REQUEST['chbx_kidney_disease'];
$hpArr['kidneyDiseaseDesc']=($_REQUEST['chbx_kidney_disease']=='Yes')?$_REQUEST['kidneyDiseaseDesc']:'';

$hpArr['hivAutoimmune']=$_REQUEST['chbx_hiv_autoimmune'];
$hpArr['hivAutoimmuneDesc']=($_REQUEST['chbx_hiv_autoimmune']=='Yes')?$_REQUEST['hivAutoimmuneDesc']:'';

$hpArr['historyCancer']=$_REQUEST['chbx_history_cancer'];
$hpArr['historyCancerDesc']=($_REQUEST['chbx_history_cancer']=='Yes')?$_REQUEST['historyCancerDesc']:'';

$hpArr['organTransplant']=$_REQUEST['chbx_organ_transplant'];
$hpArr['organTransplantDesc']=($_REQUEST['chbx_organ_transplant']=='Yes')?$_REQUEST['organTransplantDesc']:'';

$hpArr['badReaction']=$_REQUEST['chbx_bad_reaction'];
$hpArr['badReactionDesc']=($_REQUEST['chbx_bad_reaction']=='Yes')?$_REQUEST['badReactionDesc']:'';

$hpArr['highCholesterol']=$_REQUEST['chbx_high_cholesterol'];
$hpArr['highCholesterolDesc']=($_REQUEST['chbx_high_cholesterol']=='Yes')?$_REQUEST['highCholesterolDesc']:'';

$hpArr['thyroid']=$_REQUEST['chbx_thyroid'];
$hpArr['thyroidDesc']=($_REQUEST['chbx_thyroid']=='Yes')?$_REQUEST['thyroidDesc']:'';

$hpArr['ulcer']=$_REQUEST['chbx_ulcer'];
$hpArr['ulcerDesc']=($_REQUEST['chbx_ulcer']=='Yes')?$_REQUEST['ulcerDesc']:'';

$hpArr['otherHistoryPhysical']=$_REQUEST['otherHistoryPhysical'];

$hpArr['heartExam']=$_REQUEST['chbx_heart_exam'];
$hpArr['heartExamDesc']=($_REQUEST['chbx_heart_exam']=='No')?$_REQUEST['heartExamDesc']:'';

$hpArr['lungExam']=$_REQUEST['chbx_lung_exam'];
$hpArr['lungExamDesc']=($_REQUEST['chbx_lung_exam']=='No')?$_REQUEST['lungExamDesc']:'';

$hpArr['discussedAdvancedDirective']=$_REQUEST['chbx_advance_directive'];

//$hpArr['hidDataMedicalHistory_History_physical']=$_REQUEST['hidDataMedicalHistory_History_physical'];

//Saving for static questions history physical
if(empty($history_physicial_id) === true) {
 		$hp_action = 'add';
  	$hpArr['create_date_time']=date('Y-m-d H:i:s');
		$hpArr['create_operator_id']=$_SESSION['authId'];
    AddRecords($hpArr,'surgerycenter_pt_history_physical');

} else {
    $hp_action = 'update';
		$hpArr['save_date_time']=date('Y-m-d H:i:s');
		$hpArr['save_operator_id']=$_SESSION['authId'];
    UpdateRecords($history_physicial_id,'history_physicial_id',$hpArr,'surgerycenter_pt_history_physical');	   
}


// Save values into patient medical condition field in General Health Page
$historyPhysical->save_med_cond();

//Save Advance Directive
$ado_option_text_value = $_REQUEST['ado_other_txt'];
$ado_option_value = $_REQUEST['ado_option'];
$historyPhysical->save_advance_directive($ado_option_value,$ado_option_text_value);

//Saving for custom questions history physical
if( $custom_quesArr ) {
	foreach($custom_quesArr as $custom_ques) {
		$custom_id=$custom_ques['id'];
		
		$custom_hpArr=array();
    $custom_hpArr['patient_id']=$_REQUEST['patient_id_hp'];
    $custom_hpArr['ques_id']=$custom_id;
    $custom_hpArr['ques_status']=$_REQUEST['chbx_custom_'.$custom_id];
    $custom_hpArr['ques_desc']=($_REQUEST['chbx_custom_'.$custom_id]=='Yes')?addslashes($_REQUEST['custom_desc_'.$custom_id]):'';
		
		if( $pt_custom_quesArr[$custom_id])
			UpdateRecords($pt_custom_quesArr[$custom_id]['id'],'id',$custom_hpArr,'surgerycenter_pt_history_physical_ques');
		else 
			AddRecords($custom_hpArr,'surgerycenter_pt_history_physical_ques');
		
	}
}

$hp_make_pdf = "yes";
include_once("hp_print.php");

//redirecting...
$curr_tab = $_REQUEST["curr_tab"];
$curr_dir = "hp";
$next_tab = $_REQUEST["next_tab"];
$next_dir = $_REQUEST["next_dir"];
$curr_tab = ($next_tab != "") ? $next_tab : $curr_tab; 
$buttons_to_show = $_REQUEST["buttons_to_show"];

$hp_page_load_done = $_REQUEST["hp_page_load_done"];

$arr_info_alert = array();
if(isset($_REQUEST["info_alert"]) && count($_REQUEST["info_alert"]) > 0){
	$arr_info_alert = unserialize(urldecode($_REQUEST["info_alert"]));
}
?>
<script type="text/javascript">
	//if(typeof(window.parent.setChkChangeDefault)!='undefined'){window.parent.setChkChangeDefault();}
	var curr_tab = '<?php echo $curr_tab; ?>';
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
	top.fmain.location.href = top.JS_WEB_ROOT_PATH+'/interface/Medical_history/index.php?showpage='+curr_tab;
	top.show_loading_image("hide");	
</script>