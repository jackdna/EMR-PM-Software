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
include_once("../../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/medical_hx/sx_procedure.class.php");
//include_once($GLOBALS['srcdir'] . "/classes/implantable_devices/class.implantable_device_list.php");
//$objImpDeviceList = new implantable_device_list();
$sx = new SxProcedure($medical->current_tab);

$arr_info_alert = array();
if(isset($_REQUEST["info_alert"]) && count($_REQUEST["info_alert"]) > 0){
	$arr_info_alert = unserialize(urldecode($_REQUEST["info_alert"]));
}

if($change_value != '')
{
	//--- GET POLICY STATUS FOR AUDIT TRAIL ----	
	$query = "select policy_status from audit_policies where policy_id = '5'";
	$sql = imw_query($query);
	$row = imw_fetch_assoc($sql);
	$policyStatus = $row['policy_status'];
	
	$sx_id_str = $_REQUEST["hidSXProIdVizChange"];	
	$sx_id_str = substr(trim($sx_id_str), 0, -1);  		
	$arrSXIdVizChange = array();
	$arrSXIdVizChange = explode(",", $sx_id_str);
	
	$query = "select id,title,type,begdate,comments,referredby,sites from lists where id in (".$sx_id_str.") ";
	$sql = imw_query($query);
	
	$sxExistsDataArr = array();
	while( $sxExistsQryRes = imw_fetch_assoc($sql) )
	{
		$id = $sxExistsQryRes['id'];
		if($sxExistsQryRes['begdate'] == '0000-00-00'){
			$sxExistsQryRes['begdate'] = '';
		}
		$sxExistsDataArr[$id] = $sxExistsQryRes;
	}
	//print_r($_POST); die();
	//---- SAVE SX / PROCEDURE IN DATABASE ---
	for($i=1;$i<=$last_cnt;$i++)
	{
		if((int)trim($_POST['sg_occular'.$i]) == 9){
			$udi = trim($_POST['sx_title_text'.$i]);
			//$udi = urlencode($udi);
			//$parse_udi = array();
			//$device_detail = array();
			//if($udi != '') {
			//	$parse_udi = $objImpDeviceList->getParseUdi($udi);
			//	if (!isset($parse_udi['error'])) {
			//		$device_detail = $objImpDeviceList->getDeviceByUdi($udi);
			//	}
			//}
		}
		$sg_id = (int)$_POST['sg_id'.$i];
		$dataArr['title'] = trim(imw_real_escape_string($_POST['sx_title_text'.$i]));
		$sx_type = 5;
		if((int)trim($_POST['sg_occular'.$i]) == 6){
			$sx_type = trim($_POST['sg_occular'.$i]);
		}
		if((int)trim($_POST['sg_occular'.$i]) == 9){
			$sx_type = trim($_POST['sg_occular'.$i]);
		}
		$dataArr['type'] = $sx_type;
		
		$_POST['sg_begindate'.$i] = $_POST['sg_begindate'.$i];
		$arrDate = explode("-",trim($_POST['sg_begindate'.$i]));
		$year = "0000";
		$month = "00";
		$day = "00";
		if( inter_date_format() == "mm-dd-yyyy" ){
			if(count($arrDate)==1)
				$year = $arrDate[0];
			else if(count($arrDate) == 2){
				$month = $arrDate[0];
				$year = $arrDate[1];
			}
			else if(count($arrDate) == 3){
				$month = $arrDate[0];
				$day = $arrDate[1];
				$year = $arrDate[2];
			}
		}
		else if( inter_date_format() == "dd-mm-yyyy" ){
			if(count($arrDate)==1)
				$year = $arrDate[0];
			else if(count($arrDate) == 2){
				$month = $arrDate[0];
				$year = $arrDate[1];
			}
			else if(count($arrDate) == 3){
				$month = $arrDate[1];
				$day = $arrDate[0];
				$year = $arrDate[2];
			}
		}
		$date = $year.'-'.$month.'-'.$day;
		$dataArr['begdate'] = $date;
		$dataArr['begtime'] = $_POST['sg_begtime'.$i];
	
		$dataArr['refusal'] = $_POST['refusal'.$i];
		$dataArr['refusal_reason'] = $_POST['refusal_reason'.$i];
		$dataArr['refusal_snomed'] = $_POST['refusal_snomed'.$i];
		

		
		$dataArr['referredby'] = trim(imw_real_escape_string($_POST['sg_referredby'.$i]));

        if((int)trim($_POST['sg_occular'.$i]) == 9){
            $sg_comments = (str_replace(array('/','\\'), "",$_POST['sg_comments'.$i]));
            $dataArr['comments'] = trim(imw_real_escape_string($sg_comments));
        } else {
			$dataArr['comments'] = trim(imw_real_escape_string($_POST['sg_comments'.$i]));
		}
		$dataArr['pid'] = $sx->patient_id;
		$dataArr['ccda_code'] = $_POST['ccda_code'.$i];
		if((int)trim($_POST['sg_occular'.$i]) == 9){
			$dataArr['implant_status'] = $_POST['surgery_type'.$i];
		}else {
			$dataArr['proc_type'] = $_POST['surgery_type'.$i];
		}	
		$dataArr['referredby_id'] = $_POST['referredby_id'.$i];
		$dataArr['allergy_status'] = 'Active';
		$dataArr['sites'] = ($_POST['sx_site'.$i]!='') ? $_POST['sx_site'.$i] : 0;
		$dataArr['user'] = $_SESSION['authId'];
		$dataArr['procedure_status'] = trim(imw_real_escape_string($_POST['procedure_status'.$i]));
		$dataArr['assigning_authority_UDI'] = trim(imw_real_escape_string($_POST['assign_auth'.$i]));
		//if((int)trim($_POST['sg_occular'.$i]) == 9){
		//	print_r($dataArr);
		//} continue;
        $implantForC1=false;
		if(isset($dataArr['implant_status']) && $dataArr['implant_status']=='order' || $dataArr['implant_status']=='applied' && empty($dataArr['title'])){
            $implantForC1=true;;
        }
		
		if(empty($dataArr['title']) == false || $implantForC1)
		{ 
			//--- UPDATE IF SX / PROCEDURE EXISTS ----
			if($sg_id > 0){ 
				$sx_action = 'update';
				if(in_array($sg_id, $arrSXIdVizChange) == true){
					$sg_id = UpdateRecords($sg_id,'id',$dataArr,'lists');
				}
				
			}			
			else{//--- NEW SX / PROCEDURE INSERT ----
				$sx_action = 'add';
				$dataArr['date'] = date('Y-m-d H:i:s');
				$sg_id = AddRecords($dataArr,'lists');
			}
			
			$sx_data_arr = $sxExistsDataArr[$sg_id];

			/* ERP PORTAL ADD PATIENT SURGERY */
			if(isERPPortalEnabled()) {

				
				if(!empty($dataArr['title'])){
					$surgery_sql = "SELECT id FROM lists_admin WHERE type in (5,6) and delete_status=0 and title = '".$dataArr['title']."'";
					$surgery_res=imw_query($surgery_sql);
					$res=imw_fetch_assoc($surgery_res);
					$surgeryExternalId=$res['id'];
				}
				
				//For ERP Patient Portal API
				include_once($GLOBALS['srcdir']."/erp_portal/surgeries.php");
				$obj_patients = new Surgeries();
				$date_time = $dataArr['begdate'];
				$arrppApi['PatientExternalId']=$dataArr['pid'];
				$arrppApi['SurgeryExternalId']=!empty($surgeryExternalId) ? $surgeryExternalId : '';
				$arrppApi['SurgeryLocationType'] = !empty($dataArr['sites']) ? $dataArr['sites'] : 0;
				$arrppApi['SurgeryDate'] = $date_time;	
				$arrppApi['Active']= ($dataArr['procedure_status']=='completed') ? true : false;
				$arrppApi['EyeSurgery']= ($dataArr['type']=='6') ? true : false;
				$arrppApi['NonEyeSurgeryLocationType']= 1;
				//$arrppApi['id']='';	
				// $arrppApi['doctorExternalId']=$dataArr['referredby'];				
				// $arrppApi['comments']=$dataArr['comments'];				
				// $arrppApi['procedure_status'] = $dataArr['procedure_status'];
				$arrppApi['ExternalId']=$sg_id;				
				$obj_patients->addUpdateSurgeries($dataArr['pid'], $arrppApi);
			}	
			//---------------------------
			
			$blDoReview = false;
			if($sx_action == 'update'){
				if(in_array($sg_id, $arrSXIdVizChange) == true){					
					$blDoReview = true;
				}
			}
			elseif($sx_action == 'add'){
				$blDoReview = true;
			}
			
			if($blDoReview == true){
				//--- REVIEWED CODE FOR SX PROCEDURE ---
				$sx_reviewed_arr = array();
				$OBJReviewMedHx = new CLSReviewMedHx;
				$medDataFields = make_field_type_array("lists");
				//--- GET SX OCCULAR TYPE DATA ARRAY ----
				$review_sx_arr = array();		
				$review_sx_arr['Pk_Id'] = $sg_id;
				$review_sx_arr['Table_Name'] = 'lists';
				$review_sx_arr['UI_Filed_Name'] = 'sg_occular'.$i;
				$review_sx_arr['Data_Base_Field_Name']= "type";
				$review_sx_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"type");
				$review_sx_arr['Field_Text'] = 'Sx Occular - '.trim($_POST['sx_title_text'.$i]);
				$review_sx_arr['Operater_Id'] = $_SESSION['authId'];
				$review_sx_arr['Action'] = $sx_action;
				$review_sx_arr['Old_Value'] = $sx_data_arr['type'];
				$review_sx_arr['New_Value'] = $dataArr['type'];
				$sx_reviewed_arr[] = $review_sx_arr;
				
				//--- GET SX TITLE DATA ARRAY ----
				$review_sx_arr = array();		
				$review_sx_arr['Pk_Id'] = $sg_id;
				$review_sx_arr['Table_Name'] = 'lists';
				$review_sx_arr['UI_Filed_Name'] = 'sx_title_text'.$i;
				$review_sx_arr['Data_Base_Field_Name']= "title";
				$review_sx_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"title");
				$review_sx_arr['Field_Text'] = 'Sx Title - '.trim($_POST['sx_title_text'.$i]);
				$review_sx_arr['Operater_Id'] = $_SESSION['authId'];
				$review_sx_arr['Action'] = $sx_action;
				$review_sx_arr['Old_Value'] = imw_real_escape_string($sx_data_arr['title']);
				$review_sx_arr['New_Value'] = imw_real_escape_string($dataArr['title']);
				$sx_reviewed_arr[] = $review_sx_arr;
				
				//--- GET SX BEGIN DATE DATA ARRAY ----
				$review_sx_arr = array();		
				$review_sx_arr['Pk_Id'] = $sg_id;
				$review_sx_arr['Table_Name'] = 'lists';
				$review_sx_arr['UI_Filed_Name'] = 'sg_begindate'.$i;
				$review_sx_arr['Data_Base_Field_Name']= "begdate";
				$review_sx_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"begdate");
				$review_sx_arr['Field_Text'] = 'Sx Begin Date - '.trim($_POST['sx_title_text'.$i]);
				$review_sx_arr['Operater_Id'] = $_SESSION['authId'];
				$review_sx_arr['Action'] = $sx_action;
				$review_sx_arr['Old_Value'] = $sx_data_arr['begdate'];
				$review_sx_arr['New_Value'] = $dataArr['begdate'];
				$sx_reviewed_arr[] = $review_sx_arr;
				
				//--- GET SX REFERRED BY DATA ARRAY ----
				$review_sx_arr = array();		
				$review_sx_arr['Pk_Id'] = $sg_id;
				$review_sx_arr['Table_Name'] = 'lists';
				$review_sx_arr['UI_Filed_Name'] = 'sg_referredby'.$i;
				$review_sx_arr['Data_Base_Field_Name']= "referredby";
				$review_sx_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"referredby");
				$review_sx_arr['Field_Text'] = 'Sx Referred By - '.trim($_POST['sx_title_text'.$i]);
				$review_sx_arr['Operater_Id'] = $_SESSION['authId'];
				$review_sx_arr['Action'] = $sx_action;
				$review_sx_arr['Old_Value'] = imw_real_escape_string($sx_data_arr['referredby']);
				$review_sx_arr['New_Value'] = imw_real_escape_string($dataArr['referredby']);
				$sx_reviewed_arr[] = $review_sx_arr;
				
				//--- GET SX SITE DATA ARRAY ----
				$review_sx_arr = array();		
				$review_sx_arr['Pk_Id'] = $sg_id;
				$review_sx_arr['Table_Name'] = 'lists';
				//$review_sx_arr['UI_Filed_Name'] = 'sx_site'.$i;
				$review_sx_arr['UI_Filed_Name'] = 'sx_site';
				$review_sx_arr['Data_Base_Field_Name']= "sites";
				$review_sx_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"sites");
				$review_sx_arr['Field_Text'] = 'Sx Site - '.trim($_POST['sx_title_text'.$i]);
				$review_sx_arr['Operater_Id'] = $_SESSION['authId'];
				$review_sx_arr['Action'] = $sx_action;
				$review_sx_arr['Old_Value'] = imw_real_escape_string($sx_data_arr['sites']);
				$review_sx_arr['New_Value'] = imw_real_escape_string($dataArr['sites']);
				$sx_reviewed_arr[] = $review_sx_arr;
				
				//--- GET SX COMMENTS DATA ARRAY ----
				$review_sx_arr = array();		
				$review_sx_arr['Pk_Id'] = $sg_id;
				$review_sx_arr['Table_Name'] = 'lists';
				$review_sx_arr['UI_Filed_Name'] = 'sg_comments'.$i;
				$review_sx_arr['Data_Base_Field_Name']= "comments";
				$review_sx_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"comments");
				$review_sx_arr['Field_Text'] = 'Sx Comments - '.trim($_POST['sx_title_text'.$i]);
				$review_sx_arr['Operater_Id'] = $_SESSION['authId'];
				$review_sx_arr['Action'] = $sx_action;
				$review_sx_arr['Old_Value'] = imw_real_escape_string($sx_data_arr['comments']);
				$review_sx_arr['New_Value'] = imw_real_escape_string($dataArr['comments']);
				$sx_reviewed_arr[] = $review_sx_arr;
				
				//--- GET SX Assigning Authority UDI DATA ARRAY ----
				$review_sx_arr = array();		
				$review_sx_arr['Pk_Id'] = $sg_id;
				$review_sx_arr['Table_Name'] = 'lists';
				$review_sx_arr['UI_Filed_Name'] = 'assign_auth'.$i;
				$review_sx_arr['Data_Base_Field_Name']= "assigning_authority_UDI";
				$review_sx_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"assigning_authority_UDI");
				$review_sx_arr['Field_Text'] = 'Sx Assigning Authority UDI - '.trim($_POST['sx_title_text'.$i]);
				$review_sx_arr['Operater_Id'] = $_SESSION['authId'];
				$review_sx_arr['Action'] = $sx_action;
				$review_sx_arr['Old_Value'] = imw_real_escape_string($sx_data_arr['assigning_authority_UDI']);
				$review_sx_arr['New_Value'] = imw_real_escape_string($dataArr['assigning_authority_UDI']);
				$sx_reviewed_arr[] = $review_sx_arr;
				
				$OBJReviewMedHx->reviewMedHx($sx_reviewed_arr,$_SESSION['authId'],"Sx/Procedure",$_SESSION['patient'],0,0);
			}
		}
	}
}

// Save Update No Surgery  Option//
$getRES = commonNoMedicalHistoryAddEdit($moduleName="Surgery",$_REQUEST["commonNoSurgeries"],$mod="save");
//End Save Updte No Surgery Option//

$cls_notifications = new core_notifications;
$cls_notifications->update_sxicon_status();//updating iconbar status.

//redirecting...
$curr_tab = xss_rem($_REQUEST["curr_tab"]);
$curr_dir = "sx_procedures";
$next_tab = xss_rem($_REQUEST["next_tab"]);
$next_dir = xss_rem($_REQUEST["next_dir"]);
if($next_tab != ""){
	$curr_tab = $next_tab;
}
if($next_dir != ""){
	$curr_dir = $next_dir;
}
$buttons_to_show = xss_rem($_REQUEST["buttons_to_show"]);

if($_REQUEST["callFrom"] == "WV" ){
	
	//Close window in case of work - view
	if($_POST["btSaveSxPro"] == "Done" )
	{
		if(!empty($_REQUEST["flgSxIco"])){	
			$tmpecho = " if(ofmain && ofmain.gebi('surgeryDiv')){ elem=ofmain.gebi('surgeryDiv'); if(elem){elem.parentNode.removeChild(elem);} } if(ofmain && typeof(ofmain.showMedList) != 'undefined'){ ofmain.hideMedList('6'); }";
		}else{
			$tmpecho = " if(ofmain && typeof(ofmain.showMedList) != 'undefined'){ ofmain.showMedList('PMH',1);} ";
		}
	
		echo "<script>
		var ofmain = window.opener.top.fmain;".
		$tmpecho." ".
		"
		window.close();
		</script>";		
	}
}


//  Remove Remote Sync Functionality

?>
<?php if($_REQUEST["callFrom"] != "WV"){?>
<script type="text/javascript">
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
	top.fmain.location.href = top.JS_WEB_ROOT_PATH + '/interface/Medical_history/index.php?showpage='+curr_tab;
	top.show_loading_image("hide");
</script>
<?php }?>