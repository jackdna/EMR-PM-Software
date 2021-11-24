<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Medical History -> Order set class
 Access Type: Indirect Access.
 
*/
include_once $GLOBALS['srcdir'].'/classes/CLSAlerts.php';
include_once $GLOBALS['srcdir'].'/classes/class.cls_review_med_hx.php';
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");

$cls_alerts = new CLSAlerts;
$cls_review_med_hx = new CLSReviewMedHx;

class Allergies extends MedicalHistory
{
	//Public variables
	public $all_vocab 		   = '';
	public $Allow_erx_medicare = '';
	public $eRx_patient_id 	   = '';
	public $allergiesTitleArr  = array();
	public $strAllergyTitle    = array();
	public $arrUserId   	   = array();
	public $checkAllergy   	   = array();
	
	public function __construct($tab = 'ocular')
	{
		parent::__construct($tab);
		$this->all_vocab = $this->get_vocabulary("medical_hx", "allergies");
		
		//Returns status of allergies
		$this->checkAllergy = commonNoMedicalHistoryAddEdit($moduleName="Allergy",$moduleValue="",$mod="get");
		
		//Checking ERX is allowed or not
		$erx_sql = imw_query("select Allow_erx_medicare from copay_policies where policies_id = '1'");
		$erx_res = imw_fetch_array($erx_sql);
		$this->Allow_erx_medicare = $erx_res['Allow_erx_medicare'];
		
		//ERX Patient id
		$erx_pat_sql = imw_query("select erx_patient_id from patient_data where id='$this->patient_id'");
		$eRx_patient_data = imw_fetch_array($erx_pat_sql);
		$this->eRx_patient_id = $eRx_patient_data["erx_patient_id"];	
	}
	
	//Get allergies name for allergies modal box
	public function get_allergies_names($from=0, $limit=0, $srch=""){
		
		$limit_phrase = "";
		if(!empty($limit)){
			$limit_phrase = " LIMIT $from, $limit ";
		}
		
		$srch = trim($srch);
		$srch_phrase = "";
		if(!empty($srch)){
			$limit_phrase = "";
			$srch_phrase = " WHERE allergie_name LIKE '".$srch."%' ";
		
			$qry = imw_query("Select DISTINCT allergie_name,allergies_id from allergies_data ".$srch_phrase." order by allergies_id ".$limit_phrase."");
			while($row = imw_fetch_array($qry)){
				$return_arr[] = $row;
			}
			
			$srch_phrase = " WHERE allergie_name LIKE '%".$srch."%' AND allergie_name NOT LIKE '".$srch."%' ";
			$qry = imw_query("Select DISTINCT allergie_name,allergies_id from allergies_data ".$srch_phrase." order by allergies_id ".$limit_phrase."");
			while($row = imw_fetch_array($qry)){
				$return_arr[] = $row;
			}
		
		}else{
		
		$qry = imw_query("Select DISTINCT allergie_name,allergies_id from allergies_data ".$srch_phrase." order by allergies_id ".$limit_phrase."");
		while($row = imw_fetch_array($qry)){
			$return_arr[] = $row;
		}
		
		}
		return $return_arr;	
	}
	
	//Sets allergies xml typeahead array
	public function get_xml_typeahead_arr($action){
		global $cls_common;
		$allergiesXMLFileExits = false;
		$allergiesXMLFile =  $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/xml/Allergies.xml";
		if(file_exists($allergiesXMLFile)){
			$allergiesXMLFileExits = true;
		}
		else{
			$cls_common->create_allergies_xml();	
			if(file_exists($allergiesXMLFile)){
				$allergiesXMLFileExits = true;	
			}	
		}

		if($allergiesXMLFileExits == true){
			$values = array();
			$XML = file_get_contents($allergiesXMLFile);
			$values = $cls_common->xml_to_array($XML);		
			foreach($values as $key => $val){	
				$allergieName = "";
				if( ($val["tag"] =="allergiesInfo") && ($val["type"]=="complete") && ($val["level"]=="2") ){		
					$allergieName = str_replace("'","",$val["attributes"]["name"]);							
					$this->allergiesTitleArr[]="'".$allergieName."'";
				}
			}
			
			if(count($this->allergiesTitleArr)>0){
				$this->strAllergyTitle = join(',',$this->allergiesTitleArr);
			}
		}
		
		if(isset($action)){
			return $this->allergiesTitleArr;
		}
	}
	
	//Gets last updation date of medication
	public function get_changed_history($currentUser, $current_id){
		$qryUser = "SELECT id, fname, mname, lname, user_type FROM users where fname!='' and lname!='' AND delete_status = 0 ORDER BY fname,lname ";
		$resUser = imw_query($qryUser);
		if($resUser){
			while($arrUser = imw_fetch_array($resUser)){
				$this->arrUserId[$arrUser["id"]]["fname"] = $arrUser["fname"];
				$this->arrUserId[$arrUser["id"]]["lname"] = $arrUser["lname"];
			}
		}

		if($current_id !='' && $current_id >0){
			$query = imw_query("SELECT plec.master_pat_last_exam_id, DATE_FORMAT(plec.date_time,'".get_sql_date_format()." %h:%i %p') as dateTimeFormat,plec.action,
						plec.operator_id as operator_id_plec,ple.operator_id 
						FROM patient_last_examined ple 
						INNER JOIN patient_last_examined_child plec on ple.patient_last_examined_id = plec.master_pat_last_exam_id
						WHERE 
					plec.section_table_primary_key='$current_id' AND plec.section_table_name = 'lists' 
					GROUP BY plec.date_time ORDER BY plec.date_time DESC");
			$arr1 = array();
			$display ='';
			while($rs = imw_fetch_array($query)){
				$dt = $rs['dateTimeFormat'];
				$dtRevAct = '<b>Action: '.ucfirst($rs["action"]).'</b>';
				if($rs['operator_id_plec']>0){
					$operator_id_plec=$rs['operator_id_plec'];
				}else{
					$operator_id_plec=$rs['operator_id'];
				}
				if($this->arrUserId[$operator_id_plec]['lname']!=""){
					$opName = $this->arrUserId[$operator_id_plec]['lname'].', '.$this->arrUserId[$operator_id_plec]['fname'];
				}else{
					$opName = $this->arrUserId[$operator_id_plec]['fname'];
				}
				$arr1[] = $opName.'<br/>'.$dt.'<br/>'.$dtRevAct;
			}
			
			if(count($arr1)==0){
				return 'No Change History';
			}
			else if(count($arr1)==1){
				$display = '<div class=\'white botborder padd5\' style=\'width:200px;\'>'.$arr1[0].'</div>';			
			}
			else if(count($arr1)>1){
				$display = "";	
				for($i=0; $i<count($arr1); $i++){
					$display .= '<div class=\'white botborder padd5\' style=\'width:200px;\'>'.$arr1[$i].'</div>';
				}
			}
			return $display;
		}else return 'No Change History';
	}
	
	//Get all alergies data
	public function get_allergies_data($request,$callFrom){
		if(empty($request["type"]) == false){
			$type = $request["type"];
		}
		else{
			$type = '3,7';
		}	
		
		$allergy_act_status = $request['allergy_act_status'];
		$sql_qury = "select lists.*,date_format(lists.begdate,'".get_sql_date_format()."') as begdate,
		date_format(lists.date,'%m-%d-%y') as date,time_format(lists.date,'%l:%i %p') as time,
		users.lname,users.fname,users.mname from lists left join users on users.id = lists.user
		where pid = '$this->patient_id' and type in ($type)";
		
		if($request['allergy_act_status'] == ""){
			$request['allergy_act_status'] = 'Active';
		}
		if($allergy_act_status != '' and $allergy_act_status != 'all'){
			$sql_qury .= " and allergy_status = '$allergy_act_status'";	
		}
		$sql_qury .= " order by id";
		$res = imw_query($sql_qury);
		while($row = imw_fetch_array($res)){
			$allergyQryRes[] = $row;
		}
		
		$allergy_page_data = '';
		$pkIdAuditTrail = "";
		$loop_cnt = count($allergyQryRes) > 5 ? count($allergyQryRes) : 5;
		$disable = false;
		for($i=0,$j=1,$k=1;$i<$loop_cnt;$i++,$j++){
			$class = fmod($i,2) == 0 ? 'bgcolor' : '';
			$ag_id = $allergyQryRes[$i]['id'];
			$pkIdAuditTrail .= $allergyQryRes[$i]['id']."-";	
			if($pkIdAuditTrailID == ""){	
				$pkIdAuditTrailID = $allergyQryRes[$i]['id'];
			}
			$ag_title = htmlspecialchars($allergyQryRes[$i]['title']);	
			$ag_fdb_id = $allergyQryRes[$i]['fdb_id'];
			$ag_title = addslashes(trim($ag_title));
			$txt_ag_title = stripslashes($allergyQryRes[$i]['title']);
			$txt_ag_title = trim($txt_ag_title);
			$ag_occular_drug = trim($allergyQryRes[$i]['ag_occular_drug']);	
			if($allergyQryRes[$i]['begdate']!="00-00-0000")
			$bg_date = $allergyQryRes[$i]['begdate'];
			else
			$bg_date = "";
			$ag_reactions = trim($allergyQryRes[$i]['reactions']);
			$ag_severity = strtolower(trim($allergyQryRes[$i]['severity']));
			$ag_comments = ' '.htmlspecialchars($allergyQryRes[$i]['comments']);
			$ag_comments = trim(preg_replace( "/\r|\n/", " ", $ag_comments));
			$ag_reaction_code = $allergyQryRes[$i]['reaction_code'];
			$phyName = $allergyQryRes[$i]['fname'][0];
			$phyName .= $allergyQryRes[$i]['lname'][0];
			$phyName = strtoupper($phyName);
			$allergy_status = $allergyQryRes[$i]['allergy_status'];
			$ccda_code = $allergyQryRes[$i]['ccda_code'];
			$mod_data = $this->get_changed_history($allergyQryRes[$i]['user'],$allergyQryRes[$i]['id']);
			
			$severity_data = '';
			$tempObj = "'ag_id".$k."'";
			$severity_data = ' <select name="ag_severity'.$k.'" class="form-control minimal" title="Select" data-width="100%" onChange="';
			if($callFrom != "WV"){
				$severity_data .= 'top.fmain.chk_change(\'\',this,event);';
			}
			$severity_data .= 'allergy_change_fun(); insertAllergIdVizChange(\'\',this,event, document.getElementById('.''.$tempObj.''.'));" >';
            $severity_data .= '<option value="">Select</option>';
			$severityArr = ag_severity();
			foreach($severityArr as $val =>  $severity)
			{
				$sel = ($ag_severity == $val) ? 'selected' : '';
				$severity_data .= '<option value="'.$val.'" '.$sel.'>'.$severity['value'].'</option>';
			}
			$severity_data .= '</select>';
			
			
			//--- CHECK DELETE STATUS ----
			$status_data = '';
			if($allergy_status != 'Deleted'){
				$status_chk1 = $allergy_status == 'Active' ? "selected" : '';
				$status_chk2 = $allergy_status == 'Suspended' ? "selected" : '';
				$status_chk3 = $allergy_status == 'Aborted' ? "selected" : '';
				$tempObj = "'ag_id".$k."'";
				$status_data = ' <select name="ag_status'.$k.'" class="form-control minimal" data-width="100%" onChange="';
				if($callFrom != "WV"){
				$status_data .= 'top.fmain.chk_change(\'\',this,event);';
				}
				$status_data .= 'allergy_change_fun(); insertAllergIdVizChange(\'\',this,event, document.getElementById('.''.$tempObj.''.'));" >
					<option value="Active"  '.$status_chk1.'>Active</option>
					<option value="Suspended"  '.$status_chk2.'>Suspended</option>
					<option value="Aborted"  '.$status_chk3.'>Aborted</option>
				</select>';
			}
			else{
				$status_data = 'Completed';
			}
			
			$drug_sel = '';
			if($ag_occular_drug == 'fdbATDrugName'){
				$drug_sel = 'selected="selected"';
				$drug_txt = 'Drug';
			}
			$ingredient_sel = '';
			if($ag_occular_drug == 'fdbATIngredient'){
				$ingredient_sel = 'selected="selected"';
				$drug_txt = 'Ingredient';
			}
			$allergen_drug_sel = '';
			if($ag_occular_drug == 'fdbATAllergenGroup'){
				$allergen_drug_sel = 'selected="selected"';
				$drug_txt = 'Allergen';
			}
			//--- ALL DATA VARIABLE --------
			if($allergy_status != 'Deleted'){
				if($ag_id > 0){
					$disable = true;
				}
				$onClick = '';
				$top = 'top.fmain.';
				if( $callFrom == 'WV') $top = 'top.';
				if($ag_id){
					$onClick= "onClick=\"top.fancyConfirm('".$this->all_vocab['delete']."','','".$top."deleteAllergy_remove_tr(\'".$ag_id."\',\'".$ag_title."\', this,\'".$GLOBALS['webroot']."\', \'".$this->all_vocab['delete']."\',\'".$k."\')');\"";
				}else{
					$onClick = "onClick=\"".$top."deleteAllergy_remove_tr('','', this,'', '',".$k.")\"";
				}
				
					$allergy_page_data .= "
					<tr id="."tblag_".$k." class=".$class.">
						<td><input type=\"hidden\" name="."ag_id".$k." id="."ag_id".$k." value=".$ag_id.">	
							<select name="."ag_occular_drug".$k." id="."ag_occular_drug".$k." class=\"form-control minimal\" data-width=\"100%\" onChange=\"";
							if($callFrom != "WV")
							$allergy_page_data .= "top.fmain.chk_change('',this,event);";
							$allergy_page_data .= "allergy_change_fun(); insertAllergIdVizChange('',this,event, document.getElementById('ag_id$k'));\">
								<option value=\"fdbATDrugName\" ".$drug_sel.">Drug</option>
								<option value=\"fdbATIngredient\" ".$ingredient_sel.">Ingredient</option>
								<option value=\"fdbATAllergenGroup\" ".$allergen_drug_sel.">Allergen</option>
							</select>
						</td>
						<td>
							<input type=\"text\" id="."textTitleA".$k." tabindex=".$k." onChange=\"";
							if($callFrom != "WV")
								$allergy_page_data .= "top.fmain.chk_change('".addslashes($txt_ag_title)."',this,event);";
							$allergy_page_data .= "allergy_change_fun();\" onKeyUp=\"search_erx_allergy(this.value, '".$k."');";
							if($callFrom != "WV")
								$allergy_page_data .= "top.fmain.chk_change('".addslashes($txt_ag_title)."',this,event);";
							$allergy_page_data .= "insertAllergIdVizChange('".addslashes($txt_ag_title)."',this,event, document.getElementById('ag_id".$k."'));\" value=\"".$txt_ag_title."\" class=\"form-control\" name="."ag_title".$k." onMouseDown=\"addNewAllergie(event,this);\" >
							<input type=\"hidden\" id=\"hiddenTitleA".$k."\" name=\"hiddenTitleA".$k."\" value=\"".$ag_fdb_id."\" />
						</td>
						<td>
							<div class=\"input-group\">
								<input type=\"text\" id="."ag_begindate".$k." tabindex=".$k." name="."ag_begindate".$k." onKeyUp=\"";
								if($callFrom != "WV")
								$allergy_page_data .= "top.fmain.chk_change('".addslashes($bg_date)."',this,event);";
								$allergy_page_data .= "insertAllergIdVizChange('".addslashes($bg_date)."',this,event, document.getElementById('ag_id".$k."'));\" onChange=\"";
								if($callFrom != "WV")
								$allergy_page_data .= "top.fmain.chk_change('".addslashes($bg_date)."',this,event);";
								$allergy_page_data .= "allergy_change_fun(); insertAllergIdVizChange('".addslashes($bg_date)."',this,event, document.getElementById('ag_id".$k."'));checkdate(this);\" value='".$bg_date."' class=\"datepicker form-control allergy_bg_date\" maxlength=\"10\" onBlur=\"";
								if($callFrom != 'WV')
									$allergy_page_data .= "top.fmain.chk_change('".addslashes($bg_date)."',this,event);";
								$allergy_page_data .= "insertAllergIdVizChange('".addslashes($bg_date)."',this,event, document.getElementById('ag_id".$k."'));\">
								<label for=\"ag_begindate".$k."\" class=\"input-group-addon\">
									<span class=\"glyphicon glyphicon-calendar\"></span>	
								</label>
							</div>
						</td>
						<td>
							<input type=\"hidden\" name=\"ag_reaction_code".$k."\" id=\"ag_reaction_code".$k."\" value=\"".$ag_reaction_code."\" onChange=\"insertAllergIdVizChange('".addslashes($ag_reaction_code)."',this,event, document.getElementById('ag_id".$k."'));\" /> 
							<textarea class=\"form-control\" onChange=\"allergy_change_fun();get_rx_code(this,'".$k."');\" ".show_tooltip($ag_reaction_code)." onFocus=\"get_rx_code(this,'".$k."');\" id="."ag_comments".$k." tabindex=".$k." rows=\"1\" name="."ag_comments".$k." onKeyDown=\"indexEnt();\" onKeyUp=\"";
							if($callFrom != "WV")
							$allergy_page_data .= "top.fmain.chk_change('".addslashes($ag_comments)."',this,event);";
							$allergy_page_data .= "insertAllergIdVizChange('".addslashes($ag_comments)."',this,event, document.getElementById('ag_id".$k."'));\">".html_entity_decode($ag_comments)."</textarea>
						</td>
						<td class=\"text-center\">
							".$severity_data."
						</td>
						<td class=\"text-center\">
							".$status_data."
						</td>
						<td>
							<input type=\"text\" id="."ccda_code".$k." name="."ccda_code".$k." tabindex=".$k." ";
							if($callFrom != "WV")
								$allergy_page_data .= "onChange=\"top.fmain.chk_change('".addslashes($ccda_code)."',this,event);\"";
							$allergy_page_data .= " onKeyPress=\"entsub(this.value,'".$k."')\"";
							$allergy_page_data .= " onKeyUp=\"";
							if( $callFrom <> 'WV')
								$allergy_page_data .= "top.fmain.chk_change('".addslashes($ccda_code)."',this,event);";
							$allergy_page_data .= "insertAllergIdVizChange('".addslashes($ccda_code)."',this,event, document.getElementById('ag_id".$k."'));\" value='".$ccda_code."' class=\"form-control\" ";
							$allergy_page_data .= " value=\"".$ccda_code."\" class=\"form-control\" name="."ccda_code".$k." >
						</td>
						<td>
							 <a href=\"#\" title=\"Changes History\" data-toggle=\"popover\" data-trigger=\"focus\" data-content=\"".$mod_data."\" data-html=\"true\" data-placement=\"left\" data-container=\"#med_popover_cont".$j."\"><img src=\"../../library/images/search.png\" width=\"20px\" height=\"auto\"></a>
							 <span id=\"med_popover_cont".$j."\" class=\"med_popover\"></span>
						</td>
						<td class='text-center'><span class='glyphicon glyphicon-remove pointer' title='Delete Row' ".$onClick."></span></td>
					</tr>";
		$k++;
			}
			else{
				$allergy_page_data .= '<tr id="tblag_'.$j.'" align="top">
						<td style="text-decoration:line-through;color:#FF0000;">			
							'.$drug_txt.'
						</td>
						<td style="text-decoration:line-through;color:#FF0000;">
							'.$ag_title.'
						</td>
						<td  style="text-decoration:line-through;color:#FF0000;">
							'.$bg_date.'&nbsp;
						</td>
						<td style="text-decoration:line-through;color:#FF0000;">
							'.$ag_comments.'&nbsp;
						</td> 
						<td style="text-decoration:line-through;color:#FF0000;text-align:left;">
							'.$severity_data.'
						</td> 
						<td style="text-decoration:line-through;color:#FF0000; text-align:left;">
							'.$status_data.'
						</td>
						<td></td>
						<td class="text-center">
							<a href="#" title="Changes History" data-toggle="popover" data-trigger="focus" data-content="'.$mod_data.'" data-html="true" data-placement="left" data-container="#med_popover_cont'.$j.'"><img src="../../library/images/search.png" width="20px" height="auto"></a>
							<span id="med_popover_cont'.$j.'" class="med_popover"></span>
						</td>				
						<td class="text-center" style="text-decoration:line-through;color:#FF0000;">&nbsp;</td>
					</tr>';
			}	
		}
		$this->checkAllergy = commonNoMedicalHistoryAddEdit($moduleName="Allergy",$moduleValue="",$mod="get");
		$return_arr['allergy_page_data'] 	= $allergy_page_data;
		$return_arr['last_cnt'] 			= $j;
		$return_arr['disable'] 				= $disable;
		$return_arr['pkIdAuditTrailID'] 	= $pkIdAuditTrailID;
		$return_arr['pkIdAuditTrail'] 		= $pkIdAuditTrail;
		$return_arr['policyStatus'] 		= $this->policy_status;
		$return_arr['Allow_erx_medicare'] 	= $this->Allow_erx_medicare;
		$return_arr['eRx_patient_id'] 		= $this->eRx_patient_id;
		$return_arr['checkAllergy'] 		= $this->checkAllergy;
		$return_arr['strAllergyTitle'] 	 	= $this->strAllergyTitle;
		$return_arr['allergiesTitleArr'] 	= $this->allergiesTitleArr;
		$return_arr['arr_info_alert'] 		= $this->all_vocab;
		
		return $return_arr;
	}

	//Get values for allergies name modification
	public function modify_allergies($request){
		$return_val = '';
		global $cls_common;
		//Default Values	
		$request_allergy_name = trim($request['allergy_name']);
		$allergieName = ucfirst(trim($request['allergy_name']));
		$counter = 0;
		$allergy_id = $request['allergy_id'];
		if($request['del_id']<>""){
			imw_query("Delete from allergies_data where allergies_id='".$request['del_id']."'");
			$counter = ($counter+imw_affected_rows());
			$cls_common->create_allergies_xml();	
			return $counter;
		}
		
		if($request['save_data'] != ""){
			if(!empty($allergieName)){
				$qry = "select * from allergies_data where UCASE(allergie_name) = UCASE('".$request_allergy_name."')";
				$res = imw_query($qry);
				$numRows = imw_num_rows($res);
			}		
			if($numRows==0){
				if($request['allergy_id']==""){
					$qry2 = "insert into allergies_data  set
							allergie_name='$request_allergy_name'";
					imw_query($qry2);
					$cls_common->create_allergies_xml();	
					$counter = ($counter+imw_affected_rows());
					if($counter > 0){
						$return_val = 'Record Added';
					}	
				}
				else if($request['allergy_id'] != ""){
					$qry2="update allergies_data set
							allergie_name='$request_allergy_name'
							where allergies_id = $allergy_id";
					imw_query($qry2);
					$cls_common->create_allergies_xml();	
					$counter = ($counter+imw_affected_rows());
					if($counter > 0){
						$return_val = 'Record Updated';
					}		
				}					
			}else{		
				$return_val = 'Record already exists';
				$counter = 0;
			}
		}
		
		$return_arr['allergy_name'] = $allergieName;
		$return_arr['return_val'] 	= $return_val;
		$return_arr['counter'] 	= $counter;
		$return_arr['qry'] 	= $qry2;
		return $return_arr;
	}	
	
	//Updating allergies status
	public function update_allergies_status($request){
		global $cls_review_med_hx;
		global $cls_common;	
		$data_arr['allergy_status'] = $allergy_act_status;
		$data_arr["user"] = $_SESSION["authId"];		
		$cls_common->UpdateRecords($ag_id,'id',$data_arr,'lists');
		//making review in database - start
		$opreaterId = $_SESSION['authId'];
		$action = $allergy_act_status;		
		$arrReview_Allergies_Active_Inactive = array();
		//$arrReview_Allergies_Active_Inactive = CLSReviewMedHx::getReviewArrayAllergiesActiveInactive($ag_id,$request['agName'],$opreaterId,$action);
		$arrReview_Allergies_Active_Inactive = $cls_review_med_hx->getReviewArrayAllergiesActiveInactive($ag_id,$request['agName'],$opreaterId,$action);
		//CLSReviewMedHx::reviewMedHx($arrReview_Allergies_Active_Inactive,$_SESSION['authId'],"Allergies",$pid,0,0);
		$cls_review_med_hx->reviewMedHx($arrReview_Allergies_Active_Inactive,$_SESSION['authId'],"Allergies",$pid,0,0);
	}
	
	//Saving and updating filled allergies data
	public function save_allergies_data($request){
		//Allscripts main function file
		if( is_allscripts() )
		{
			include_once( $GLOBALS['srcdir'].'/allscripts/as_patient.php' );
			include_once( $GLOBALS['srcdir'].'/allscripts/as_dataValues.php' );

			$asDataValues = new as_dataValues();

			$twDataError = array();
			$twSavingError = array();
			$twUpdError = array();
		}
		
		global $cls_review_med_hx;
		
		$counter = 0;
		$cur_date = date('Y-m-d H:i:s');
		//eRx skipped 
		
		$getRES = commonNoMedicalHistoryAddEdit($moduleName="Allergy",$request["commonNoAllergies"],$mod="save");
		
		if($request['ag_title1'] != ''){
			$exists_alergy_id_str = $request["hidAllergyIdVizChange"];	
			$exists_alergy_id_str = substr(trim($exists_alergy_id_str), 0, -1);  		
			$arrAllergyIdVizChange = array();
			$arrAllergyIdVizChange = explode(",", $exists_alergy_id_str);
			$allergyDataArr = array();
			$sql = imw_query("select *, date_format(begdate,'%Y-%m-%d') as beg_date
				from lists where id in ('$exists_alergy_id_str')");
			while($row = imw_fetch_array($sql)){
				$id = $row['id'];
				if($row['beg_date'] == '0000-00-00'){
					$row['begdate'] = '';
				}
				$allergyDataArr[$id] = $row;
			}
				
			for($i=1;$i<=$request['last_cnt'];$i++){

				if( is_allscripts() )
					$asAddNewAllergy = false;

				$ag_id = (int)trim($request['ag_id'.$i]);
				$dataArr = array();
				$dataArr['ag_occular_drug'] = trim($request['ag_occular_drug'.$i]);
				$dataArr['title'] = trim($request['ag_title'.$i]);
				$dataArr['fdb_id'] = trim($request['hiddenTitleA'.$i]);
				$dataArr['title'] = imw_real_escape_string($dataArr['title']);
				if(trim($request['ag_begindate'.$i]) != ''){
					$dataArr['begdate'] = getDateFormatDB($request['ag_begindate'.$i]);
				}
				$dataArr['comments'] = trim($request['ag_comments'.$i]);
				$dataArr['reaction_code'] = trim($request['ag_reaction_code'.$i]);
				$dataArr['type'] = '7';
				
				$dataArr['pid'] = $this->patient_id;	
				$dataArr['user'] = $_SESSION['authId'];
				$dataArr['del_allergy_status'] = 0;
				$dataArr['severity'] = $request['ag_severity'.$i];
				$dataArr['allergy_status'] = $request['ag_status'.$i];
				$dataArr['ccda_code'] = $request['ccda_code'.$i];
				
				$listQryRes = $allergyDataArr[$ag_id];
				
				//--- CHECK IF ALLERGY VALUE CHANGED ---
				$allergychanged = false;
				if($dataArr['ag_occular_drug'] != $listQryRes['ag_occular_drug']){
					$allergychanged = true;
				}
				if($dataArr['begdate'] != $listQryRes['begdate']){
					$allergychanged = true;
				}
				if($dataArr['title'] != $listQryRes['title']){
					$allergychanged = true;
				}
				if($dataArr['comments'] != $listQryRes['comments']){
					$allergychanged = true;
				}
				if($dataArr['severity'] != $listQryRes['severity']){
					$allergychanged = true;
				}
				if($dataArr['reaction_code'] != $listQryRes['reaction_code']){
					$allergychanged = true;
				}
				if($dataArr['allergy_status'] != $listQryRes['allergy_status']){
					$allergychanged = true;
				}
				if($dataArr['ccda_code'] != $listQryRes['ccda_code']){
					$allergychanged = true;
				}
				$arrReview_Allergies = array();
				
			//=======================Add Allergy in Master Table if not Exist================//
				if(trim($dataArr['title'])){
					$qryAddAllergies = "SELECT allergies_id FROM allergies_data WHERE allergie_name='".$dataArr['title']."'";
					$resAddAllergies = imw_query($qryAddAllergies);
					$allergyData = imw_fetch_assoc($resAddAllergies);
					$allergyID = $allergyData['allergies_id'];
					if(imw_num_rows($resAddAllergies)==0){
						$qryInsertAllergies = "INSERT INTO allergies_data set allergie_name='".$dataArr['title']."'";
						$resInsertAllergies = imw_query($qryInsertAllergies);
					}
				}
				
			//===============================================================================//
				if($dataArr['title'] != '' && $allergychanged == true){

					if( is_allscripts() && $_SESSION['as_mrn']!=='' && $_SESSION['as_id']!=='' )
					{
						$asTransId = '';
						try
						{
							/*
							 * Check if allergy exists in TW and then attempt to save the same in TW if it exists in TW dictionary.
							 * Check if we aready queried for the same allergy from TW.
							 * If not then query for the same and save the resutls in iDoc for future use.
							*/
							$sqlCheck = "SELECT `allergies_id`, `as_id`, `as_type` 
										FROM `allergies_data`
										WHERE LOWER(`allergie_name`)='".imw_real_escape_string(strtolower($dataArr['title']))."'";
							$respCheck = imw_query($sqlCheck);

							$allergyID = false;
							$asAllergyId = '';
							$asAllergyType = '';

							if( $respCheck && imw_num_rows($respCheck) > 1 )
							{
								$respAS = imw_fetch_assoc($respCheck);
								
								$allergyID = trim($respAS['allergies_id']);
								$asAllergyId = trim($respAS['as_id']);
								$asAllergyType = trim($respAS['as_type']);
							}

							/*Get TW Trans Id for the Allergy Id*/
							if( $ag_id )
							{
								$sqlAS = "SELECT `as_id` FROM `lists` WHERE `id`=".$ag_id;
								$sqlAS = imw_query($sqlAS);
								if($sqlAS)
								{
									$sqlAS = imw_fetch_assoc($sqlAS);
									$asTransId = trim($sqlAS['as_id']);
								}
							}
							/*End Get TW Trans Id for the Allergy Id*/

							/*Query data from TW only if the entry does not have TW transaction ID*/
							if( $asTransId === '' && ( $asAllergyId === '' || $asAllergyType === '' ) )
							{
								
								$asAllergyDataValues = $asDataValues->query( $dataArr['title'], 'allergies', $_SESSION['as_id'] );
								$asAllergyInsert = false;
								foreach($asAllergyDataValues as $asKey=>$asVal)
								{
									if( strtolower($asVal['name']) === strtolower($dataArr['title']) )
									{
										$asAllergyId = $asKey;
										$asAllergyType = $asVal['type'];

										$asAllergyInsert = true;
										break;
									}
								}

								if( $asAllergyInsert )
								{
									$sqlAllergy = '';
									$sqlAllergyWhere = '';

									if( $allergyID === false )
										$sqlAllergy = "INSERT INTO `allergies_data` SET `allergie_name`='".$dataArr['title']."', ";
									else
									{
										$sqlAllergy = "UPDATE `allergies_data` SET ";
										$sqlAllergyWhere = 'WHERE `allergies_id`='.$allergyID;
									}
									
									$sqlAllergy .= "`as_id`='".$asAllergyId ."', `as_type`='".$asAllergyType."' ";
									$sqlAllergy .= $sqlAllergyWhere;
									
									$sqlAllergy = trim($sqlAllergy);
									$sqlAllergy = rtrim($sqlAllergy, ',');
									imw_query($sqlAllergy);

									$allergyID = imw_insert_id();
								}
							}

							if( $asTransId === '' && (trim($asAllergyId) === '' || trim($asAllergyType) === '') )
								throw new asException( 'allergyAlert', 'no match' );
						}
						catch( asException $e)
						{
							if( $e->getErrorType() === 'allergyAlert' && $e->getErrorText() === 'no match')
							{
								array_push($twDataError, "<li>".$dataArr['title']." - no match found in TW.</li>");
							}
							else
							{
								array_push($twDataError, "<li>".$dataArr['title']." - ".$e->getErrorText()."</li>");
							}
						}
						/*End Allergy checking in TW*/
					}

					//--- UPDATE ALLERGY ----
					if($ag_id > 0){		
						$status = 'update';
						if(in_array($ag_id, $arrAllergyIdVizChange) == true){

							$ag_id = UpdateRecords($ag_id,'id',$dataArr,'lists');
							
							/*Update Allergy Stauts in Touch Works*/
							if( is_allscripts() && $ag_id && $_SESSION['as_mrn']!=='' && $_SESSION['as_id']!=='' )
							{
								try
								{
									$asPatientObj = new as_patient();

									if( $asTransId!='' )
									{
										$asAllergyData = array( 'status'=>(($dataArr['allergy_status']=='Active')?'Activate':'Inactivate'), 'transId'=>$asTransId );

										$allergyResp = $asPatientObj->changeAllergyStatus( $asAllergyData );
										
										if( strtolower($allergyResp->status) !== 'success' )
											throw new asException( 'Error', $allergyResp->status);
									}
									else
									{
										$asAddNewAllergy = true;
									}
								}
								catch( asException $e)
								{
									array_push($twUpdError, "<li>".$dataArr['title'].' - '.$e->getErrorText()."</li>");
								}
							}

							if( isset($asPatientObj) && is_object($asPatientObj) )
								unset($asPatientObj);
							//Skipped Touch works inplementation for updating allergies
						}
					}
					//--- NEW ALLERGY -----
					else{
						$status = 'add';
						$dataArr['date'] = date('Y-m-d H:i:s');
						$sql = "SELECT * FROM lists 
								WHERE pid = ".$this->patient_id." 
									AND title = '".imw_real_escape_string($dataArr['title'])."'
									AND allergy_status = 'Active'
									AND ag_occular_drug = '".$dataArr['ag_occular_drug']."'
								";
						$res = imw_query($sql);
						if(imw_num_rows($res) == 0){
                            // Send new patient allergy to DSS
                            if( isDssEnable() )
                            {
                                $newAllr=$this->dssAddPatientAllergies($dataArr);
                            }
                    
							$ag_id = AddRecords($dataArr,'lists');

							$asAddNewAllergy = true;
						}
					}

					/*Push New Allergy to Touch Works*/
					if( is_allscripts() && $ag_id && $_SESSION['as_mrn']!=='' && $_SESSION['as_id']!=='' && 
						($asAllergyId !== '' || $asAllergyType !== '')  && $asAddNewAllergy === true
					)
					{
						try
						{
							$asPatientObj = new as_patient();

							/*Trigger Save Call only if the allergy for the patient does exits in TW*/
							$asAllergyData = array('name'=>$dataArr['title'], 'isMed'=>( ($dataArr['ag_occular_drug']=='fdbATDrugName')?'Y':'N' ), 'comments'=> $dataArr['comments']);
							$allergyResp = $asPatientObj->addAllergy( $asAllergyData );

							if( !isset($allergyResp->status) || strtolower($allergyResp->status) !== 'success' )
								throw new asException( 'Error', $allergyResp->status );
							elseif( !isset($allergyResp->transid) || $allergyResp->transid === '' )
								throw new asException( 'Error', 'No transaction id returned fro Unity API.' );
							else
							{
								/*Save All Scripts Trans. Id*/
								$sqlAS = "UPDATE `lists` SET `as_id`='".$allergyResp->transid."' WHERE `id`=".$ag_id;
								imw_query($sqlAS);
							}
						}
						catch( asException $e)
						{
							array_push($twSavingError, "<li>".$dataArr['title'].' - '.$e->getErrorText()."</li>");
						}
					}
					if( isset($asPatientObj) && is_object($asPatientObj) )
						unset($asPatientObj);
					/*End Push new allergy to TW*/

					/* ERP PORTAL CREATE NEW PATIENT ALLERGY */
					if(isERPPortalEnabled()) {
						try {
							$arrppApi = [];
							//For ERP Patient Allergy Portal API
							include_once($GLOBALS['srcdir']."/erp_portal/patient_allergies.php");
							$obj_allergy = new patient_allergies();
							
							if($dataArr['allergy_status']=='Suspended' || $dataArr['allergy_status']=='Aborted'){
								$res = $obj_allergy->deleteAllergy($ag_id);

							}else{
								$arrppApi['patientExternalId']=$dataArr['pid'];
								$arrppApi['allergyExternalId']=$allergyID;
								$arrppApi['allergyName']= $dataArr['title'];
								$arrppApi['allergySeverityName'] = $dataArr['severity'];
								$arrppApi['startDate'] = $dataArr['begdate'];
								$arrppApi['notes'] = $dataArr['comments'];
								$arrppApi['active']= ($dataArr['allergy_status']=='Suspended' || $dataArr['allergy_status']=='Aborted')? false : true;
								$arrppApi['externalId'] = $ag_id;
								
								$obj_allergy->addUpdateAllergy($dataArr['pid'], $arrppApi);
								
							}
						} catch(Exception $e) {
							$erp_error[]='Unable to connect to ERP Portal';
						}
					}
					
					$blDoRewiev = false;
					if($status == 'update'){
						if(in_array($ag_id, $arrAllergyIdVizChange) == true){				
							$blDoRewiev = true;
						}
					}
					elseif($status == 'add'){
						$blDoRewiev = true;
					}
					if($blDoRewiev == true){
						$medDataFields = make_field_type_array("lists");
						//--- ALLERGY TYPE FOR VIEWED ----
						$Review_Allergies_arr = array();		
						$Review_Allergies_arr['Pk_Id'] = $ag_id;
						$Review_Allergies_arr['Table_Name'] = 'lists';
						$Review_Allergies_arr['UI_Filed_Name'] = 'ag_occular_drug'.$i;
						$Review_Allergies_arr['Data_Base_Field_Name']= "ag_occular_drug";
						$Review_Allergies_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"ag_occular_drug");
						$Review_Allergies_arr['Field_Text'] = 'Patient Allergy Drug - '.trim($request['ag_title'.$i]);
						$Review_Allergies_arr['Operater_Id'] = $_SESSION['authId'];
						$Review_Allergies_arr['Action'] = $status;
						$Review_Allergies_arr['Old_Value'] = $listQryRes['ag_occular_drug'];
						$Review_Allergies_arr['New_Value'] = $dataArr['ag_occular_drug'];
						$arrReview_Allergies[] = $Review_Allergies_arr;
						
						//--- ALLERGY NAME FOR VIEWED ----
						$Review_Allergies_arr = array();		
						$Review_Allergies_arr['Pk_Id'] = $ag_id;
						$Review_Allergies_arr['Table_Name'] = 'lists';
						$Review_Allergies_arr['UI_Filed_Name'] = 'ag_title'.$i;
						$Review_Allergies_arr['Data_Base_Field_Name']= "title";
						$Review_Allergies_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"title");
						$Review_Allergies_arr['Field_Text'] = 'Patient Allergy Name - '.trim($request['ag_title'.$i]);
						$Review_Allergies_arr['Operater_Id'] = $_SESSION['authId'];
						$Review_Allergies_arr['Action'] = $status;
						$Review_Allergies_arr['Old_Value'] = $listQryRes['title'];
						$Review_Allergies_arr['New_Value'] = $dataArr['title'];
						$arrReview_Allergies[] = $Review_Allergies_arr;
						
						//--- ALLERGY BEGIN DATE FOR VIEWED ----
						$Review_Allergies_arr = array();		
						$Review_Allergies_arr['Pk_Id'] = $ag_id;
						$Review_Allergies_arr['Table_Name'] = 'lists';
						$Review_Allergies_arr['UI_Filed_Name'] = 'ag_begindate'.$i;
						$Review_Allergies_arr['Data_Base_Field_Name']= "begdate";
						$Review_Allergies_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"begdate");
						$Review_Allergies_arr['Field_Text'] = 'Patient Allergy Begin Date - '.trim($request['ag_title'.$i]);
						$Review_Allergies_arr['Operater_Id'] = $_SESSION['authId'];
						$Review_Allergies_arr['Action'] = $status;
						$Review_Allergies_arr['Old_Value'] = $listQryRes['begdate'];
						$Review_Allergies_arr['New_Value'] = $dataArr['begdate'];
						$arrReview_Allergies[] = $Review_Allergies_arr;
						
						//--- ALLERGY CEMMENTS FOR VIEWED ----
						$Review_Allergies_arr = array();		
						$Review_Allergies_arr['Pk_Id'] = $ag_id;
						$Review_Allergies_arr['Table_Name'] = 'lists';
						$Review_Allergies_arr['UI_Filed_Name'] = 'ag_comments'.$i;
						$Review_Allergies_arr['Data_Base_Field_Name']= "comments";
						$Review_Allergies_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"comments");
						$Review_Allergies_arr['Field_Text'] = 'Patient Allergy Reaction/Comments - '.trim($request['ag_title'.$i]);
						$Review_Allergies_arr['Operater_Id'] = $_SESSION['authId'];
						$Review_Allergies_arr['Action'] = $status;
						$Review_Allergies_arr['Old_Value'] = $listQryRes['comments'];
						$Review_Allergies_arr['New_Value'] = $dataArr['comments'];
						$arrReview_Allergies[] = $Review_Allergies_arr;
						
						//--- ALLERGY STATUS FOR VIEWED ----
						$Review_Allergies_arr = array();		
						$Review_Allergies_arr['Pk_Id'] = $ag_id;
						$Review_Allergies_arr['Table_Name'] = 'lists';
						$Review_Allergies_arr['UI_Filed_Name'] = 'ag_status'.$i;
						$Review_Allergies_arr['Data_Base_Field_Name']= "allergy_status";
						$Review_Allergies_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"allergy_status");
						$Review_Allergies_arr['Field_Text'] = 'Patient Allergy Status - '.trim($request['ag_title'.$i]);
						$Review_Allergies_arr['Operater_Id'] = $_SESSION['authId'];
						$Review_Allergies_arr['Action'] = $status;
						$Review_Allergies_arr['Old_Value'] = $listQryRes['allergy_status'];
						$Review_Allergies_arr['New_Value'] = $dataArr['allergy_status'];
						$arrReview_Allergies[] = $Review_Allergies_arr;

						//--- ALLERGY SNOMED CODE FOR VIEWED ----
						$Review_Allergies_arr = array();		
						$Review_Allergies_arr['Pk_Id'] = $ag_id;
						$Review_Allergies_arr['Table_Name'] = 'lists';
						$Review_Allergies_arr['UI_Filed_Name'] = 'ccda_code'.$i;
						$Review_Allergies_arr['Data_Base_Field_Name']= "ccda_code";
						$Review_Allergies_arr['Data_Base_Field_Type']= fun_get_field_type($medDataFields,"ccda_code");
						$Review_Allergies_arr['Field_Text'] = 'Patient Allergy CCDA Code - '.trim($request['ag_title'.$i]);
						$Review_Allergies_arr['Operater_Id'] = $_SESSION['authId'];
						$Review_Allergies_arr['Action'] = $status;
						$Review_Allergies_arr['Old_Value'] = $listQryRes['ccda_code'];
						$Review_Allergies_arr['New_Value'] = $dataArr['ccda_code'];
						$arrReview_Allergies[] = $Review_Allergies_arr;

						//CLSReviewMedHx::reviewMedHx($arrReview_Allergies,$_SESSION['authId'],"Allergies",$_SESSION['patient'],0,0);
						$cls_review_med_hx->reviewMedHx($arrReview_Allergies,$_SESSION['authId'],"Allergies",$_SESSION['patient'],0,0);
					}
					
					//--- UPLOAD ALLERGY TO EMDEON ----
					if($eRxLoginStatus == true and $ag_id > 0){
						//Skipped eRx allergy upload 
					}
				}
			}

			/*Unset the object*/
			if( is_allscripts() && ( count($twDataError) > 0 || count($twSavingError) > 0 || count($twUpdError) > 0 ) )
			{
				$asErrorMessage = '';
				if( isset($twDataError) && count($twDataError) > 0 )
				{
					$asErrorMessage = "<br /><br />Allergy does not exist in TW:<br />";
					$asErrorMessage .= "<ol>".implode('', $twDataError)."</ol>";
				}

				if( isset($twSavingError) && count($twSavingError) > 0 )
				{
					$asErrorMessage = "<br /><br />Error in saving data to TW:<br />";
					$asErrorMessage .= "<ol>".implode('', $twSavingError)."</ol>";
				}

				if( isset($twUpdError) && count($twUpdError) > 0 )
				{
					$asErrorMessage = "<br /><br />Error is updating allergy satus to TW:<br />";
					$asErrorMessage .= "<ol>".implode('', $twUpdError)."</ol>";
				}

				if( $asErrorMessage !== '')
				{
				?>
					<script>
						top.fAlert("<strong>Unable to save data in Touch Works.</strong><?php echo $asErrorMessage; ?>Please adjust these manually.");
						top.document.getElementById("findBy").value = "Active";
						top.document.getElementById("findByShow").value = "Active";
						top.show_loading_image('hide');
					</script>
				<?php
				}

				unset($twDataError, $twSavingError, $asErrorMessage);
			}

			$curr_tab = xss_rem($request["curr_tab"]);
			$next_tab = xss_rem($request["next_tab"]);
			$next_dir = xss_rem($request["next_dir"]);
			if($next_tab != ""){
				$curr_tab = $next_tab;
			}
			
			if($_REQUEST["callFrom"] == "WV")
			{
				if($_POST["btSaveAllergies"] == "Done")
				{
					echo '
						<script>
							//update PMH in WV ---
							var ofmain = window.opener.top.fmain;
							if(ofmain && typeof(ofmain.showMedList) != "undefined"){ ofmain.showMedList("PMH",1);}
							//--
							document.write("<div class=\"mt20 text-center\" style=\"height:630px;\"><div class=\"loader\"></div><div style=\"font-family:verdana;font-size:12px;\">Please wait..... Work view is getting refreshed.</div></div>");
							top.window.close();
						</script>';
				}
			}
			
			?>
			<script type="text/javascript">
				var curr_tab = '<?php echo xss_rem($curr_tab); ?>';	
				top.show_loading_image("show", 100);
				if(top.document.getElementById('medical_tab_change')) {
					if(top.document.getElementById('medical_tab_change').value!='yes') {
						top.alert_notification_show('<?php echo $this->all_vocab["save"];?>');
					}
					if(top.document.getElementById('medical_tab_change').value=='yes') {
						top.chkConfirmSave('yes','set');		
					}
					top.document.getElementById('medical_tab_change').value='';
				}
				top.fmain.location.href = top.JS_WEB_ROOT_PATH+'/interface/Medical_history/index.php?showpage='+curr_tab;	
				top.show_loading_image("hide");
			</script>
			<?php
		}	
	}
	
	
	//Delete allergies
	public function delete_allergies($request){
		//print_r($request); die();
		global $cls_review_med_hx;
		//--- GET AUDIT STATUS FROM POLICIES -----
		$policyStatus = $this->policy_status;
		$medicationAllergiesDataFields = array(); 
		$medicationAllergiesDataFields = make_field_type_array("lists");
		if($medicationAllergiesDataFields == 1146){
			$medicationAllergiesError = "Error : Table 'lists' doesn't exist";
		}
		$pid = $this->patient_id;
		$med_id = $request['med_id'];

		//Emdeon Integration skipped 
		
		
		#GET DELETE INFORMATION FOR AUDIT ---
		$sql_qry = imw_query("select title as allergyName,pid as dbPatientId from lists where id='$med_id'");
		$infoQryRes = imw_fetch_array($sql_qry);
		$allergyName = $infoQryRes['allergyName'];
		$dbPatientId = $infoQryRes['dbPatientId'];	
		
		$form_tit1 = $request['med_name'];

		//print_r($dbPatientId);
        
        //DSS Cancel Patient Allergy (Mark allergies in error)
        if( isDssEnable() && !isset($request['dssload'])) {
            $return=$this->dssCancelPatientAllergies($med_id);
        }
		
		//--- CHANGE STATUS AS DELETED ALLERGY IN LIST TABLE ----
		$data_arr = array();
		$data_arr['allergy_status'] = 'Deleted';
		UpdateRecords($med_id,'id',$data_arr,'lists');
		$counter = ($counter+imw_affected_rows());
		

		//making review in database - start
		$opreaterId = $_SESSION['authId'];
		$action = "delete";
						
		$arrReview_Allergies_Delete = array();
		//$arrReview_Allergies_Delete = CLSReviewMedHx::getReviewArrayAllergiesDelete($med_id,$allergyName,$opreaterId,$action);
		$arrReview_Allergies_Delete = $cls_review_med_hx->getReviewArrayAllergiesDelete($med_id,$allergyName,$opreaterId,$action);
		$arrReview_Allergies_Delete[0]['Data_Base_Field_Name'] = 'id';
		$arrReview_Allergies_Delete[0]['Data_Base_Field_Type'] = fun_get_field_type($medicationAllergiesDataFields,"id");
		$arrReview_Allergies_Delete[0]['Filed_Label'] = 'id';

		//Audit
		//CLSReviewMedHx::reviewMedHx($arrReview_Allergies_Delete,$_SESSION['authId'],"Allergies",$dbPatientId,0,0);
		$cls_review_med_hx->reviewMedHx($arrReview_Allergies_Delete,$_SESSION['authId'],"Allergies",$dbPatientId,0,0);
		//making review in database - end	
		return $counter;
	}
	
	//Set CLS Alerts
	public function set_cls_alerts(){
		global $cls_alerts;
		$return_str= '';
		$alertToDisplayAt = "admin_specific_chart_note_med_hx";
		$return_str .= $cls_alerts->getAdminAlert($_SESSION['patient'],$alertToDisplayAt,$form_id,"350px");
		$alertToDisplayAt = "patient_specific_chart_note_med_hx";
		$return_str .= $cls_alerts->getPatSpecificAlert($_SESSION['patient'],$alertToDisplayAt,"350px");
		$return_str .= $cls_alerts->autoSetDivLeftMargin("140","265");
		$return_str .= $cls_alerts->autoSetDivTopMargin("250","30");
		$return_str .= $cls_alerts->writeJS();
		return $return_str;	
	}
	
	/**
	 * Get patient allergies from DSS. If allergy_data is available then update the dss id in db.
	 */
	public function dssLoadPatientAllergies() {
		$patient_id = $_SESSION['patient'];
		$sqlDFN = "SELECT External_MRN_5 FROM `patient_data` WHERE `id` = ".$this->patient_id;
		$resultDFN = imw_query($sqlDFN);
		if( imw_num_rows($resultDFN) > 0 ) {
			$data = imw_fetch_assoc($resultDFN);
			$patientDFN = $data['External_MRN_5'];
		}
        
        if( empty($patientDFN)==false && $patientDFN != '' ) {
            include_once( $GLOBALS['srcdir'].'/dss_api/dss_medical_hx.php' );
            $objDss = new Dss_medical_hx();
            $allergyData = $objDss->getPatientAllergies($patientDFN);

            if($allergyData) {
                $patientAllery=array();
                $dssAlleryArr=array();
                $sql_pt_allergy = "SELECT id,title,dss_allergy_id FROM lists 
                                    WHERE pid = ".$this->patient_id." 
                                        AND allergy_status != 'Deleted'
                                        AND type = '7'
                                    ";
                $result_pt_allergy = imw_query($sql_pt_allergy);
                if( $result_pt_allergy && imw_num_rows($result_pt_allergy) > 0 ) {
                    while($data = imw_fetch_assoc($result_pt_allergy)) {
                        $patientAllery[$data['dss_allergy_id']]=$data;
                    }
                }

                foreach($allergyData as $key => $allergy) {
                    if($allergyData[0]['allergyId']=='')continue;
                    if($allergyData[0]['allergent']=='No Allergy Assessment')continue;

                    // Check if allergy name contains ([,]) sqaure brackets.
                    $allerName = trim($allergy['allergent']);
                    $posS = stripos($allerName, '[');
                    $posL = strripos($allerName, ']');
                    if ($posS !== false) $allerName = ltrim($allerName, '[');
                    if ($posL !== false) $allerName = rtrim($allerName, ']');

                    // Allergy Data
                    $dataArr = array();
                    $dataArr['date'] = date('Y-m-d H:i:s');
                    $dataArr['type'] = '7';
                    $dataArr['title'] = imw_real_escape_string(trim($allerName));
                    $dataArr['comments'] = imw_real_escape_string(trim($allergy['symptoms']));
                    $dataArr['pid'] = $this->patient_id;
                    $dataArr['user'] = $_SESSION['authId'];
                    $dataArr['allergy_status'] = 'Active';
                    $dataArr['severity'] = imw_real_escape_string(trim($allergy['severity']));
                    $dataArr['timestamp'] = date('Y-m-d H:i:s');
                    $dataArr['dss_allergy_id'] = imw_real_escape_string(trim($allergy['allergyId']));
                    $dataArr['ag_occular_drug'] = 'fdbATAllergenGroup';

                    $update=false;
                    $updateid=$patientAllery[$allergy['allergyId']]['id'];
                    if( (strtolower($allerName)==strtolower($patientAllery[0]['title'])) || (strtolower($allerName)==strtolower($patientAllery[$allergy['allergyId']]['title'])) || ($allergy['allergyId']==$patientAllery[$allergy['allergyId']]['dss_allergy_id']) ){
                        $update=true;
                        if($patientAllery[0]['id'] && $updateid==''){
                            $updateid=$patientAllery[0]['id'];
                        }
                    }

                    if($update) {
                        $arr = array(
                            'title' => imw_real_escape_string($allerName),
                            'dss_allergy_id' => $allergy['allergyId'],
                        );
                        UpdateRecords($updateid, 'id', $arr, 'lists');

                    }elseif( count($patientAllery[$allergy['allergyId']]) === 0 ){
                        $id = AddRecords($dataArr,'lists');
                    }

                    if(isset($patientAllery[$allergy['allergyId']])){
                        unset($patientAllery[$allergy['allergyId']]);
                    }else{
                        if(isset($patientAllery[0]['id']) && $patientAllery[0]['id']==$updateid)
                            unset($patientAllery[0]);
                        else
                            $patientAllery=$patientAllery;
                    }
                }
                
                if(empty($patientAllery)==false) {
                    foreach($patientAllery as $key=>$val) {
                        $dssAlleryArr[]=$val['id'];
                    }
                }

                //Delete existing allergy from imwemr which are not in dss response.
                if(empty($dssAlleryArr)==false) {
                    foreach($dssAlleryArr as $val) {
                        $deleteArray=array();
                        $deleteArray['med_id'] = $val;
                        $deleteArray['dssload'] = 'dssload';
                        $this->delete_allergies($deleteArray);
                    }
                }

            }

        }

	}
    
    /**
	 * Upload to DSS ADD Patient Allergy
	 */
    function dssAddPatientAllergies($dataArr) {
        $sqlDFN = "SELECT External_MRN_5 FROM `patient_data` WHERE `id` = ".$this->patient_id;
        $resultDFN = imw_query($sqlDFN);
        $data = imw_fetch_assoc($resultDFN);
        $patientDFN = $data['External_MRN_5'];
        
        $alrSql="Select allergies_id,dss_id,dss_type,globalNode,dss_order from allergies_data where allergie_name='".$dataArr['title']."' ";
        $alrRes=imw_query($alrSql);
        $alrRow=imw_fetch_assoc($alrRes);

        $newAllr=array();
        try
        {
            if( !empty($patientDFN) && $patientDFN != '' && $alrRow['dss_id']!='' ){

                require_once(dirname(__FILE__) . "/../../../library/dss_api/dss_medical_hx.php");
                $objDss = new Dss_medical_hx();
                
                $dssAlgr=array();
                if(empty($alrRow)==false && $alrRow['globalNode']!='' && $alrRow['dss_type']!='' && $alrRow['dss_order']!='' && $alrRow['dss_id']!='' && $alrRow['dss_id']!='0') {
                    $alrRow['type']=$alrRow['dss_type'];
                    $alrRow['order']=$alrRow['dss_order'];
                    $alrRow['ien']=$alrRow['dss_id'];
                    $alrRow['name']=trim($dataArr['title']);
                    unset($alrRow['dss_type']);unset($alrRow['dss_order']);unset($alrRow['dss_id']);
                    $dssAlgr=$alrRow;                    
                } else {
                    $vistaallergy=array();
                    $vistaallergy['input']=trim($dataArr['title']);
                    $vistaallergylist = $objDss->searchForVistAAllergies($vistaallergy);
                    
                    foreach($vistaallergylist as $vallerg) {
                        if($vallerg['ien']==$alrRow['dss_id'] && strtolower($vallerg['name'])==strtolower($dataArr['title']) ) {
                            $dssAlgr=$vallerg;
                        }
                    }
                    
                    if( empty($dssAlgr) == false ) {
                        $Usql='Update allergies_data set dss_type="'.$dssAlgr['type'].'", globalNode=\''.$dssAlgr['globalNode'].'\', dss_order="'.$dssAlgr['order'].'" where allergies_id='.$alrRow['allergies_id'].' ';
                        imw_query($Usql);
                    }
                }
                if( empty($dssAlgr) || ($dssAlgr['globalNode']=='' || $dssAlgr['type']=='' || $dssAlgr['order']=='' || $dssAlgr['ien']=='' || $dssAlgr['ien']=='0') ) {
                    throw new Exception('Error: Allergy '.$dataArr['title'].' not found in DSS.');
                }
                
                $globalNode=($dssAlgr['globalNode'])?'^'.$dssAlgr['globalNode']:'';
                $type=($dssAlgr['type'])?'^'.$dssAlgr['type']:'';
                $sendtype=($dssAlgr['type'])?$dssAlgr['type'].'^':'';
                $order=($dssAlgr['order'])?'^'.$dssAlgr['order']:'';
                $allergyIdName=$dssAlgr['ien'].'^'.$dssAlgr['name'].$globalNode.$type.$order;
                
                // convert date into a fileman format
                $alBegDate = (empty($dataArr['begdate'])) ? date('Y-m-d') : $dataArr['begdate'];
                // $dateTime = $objDss->MISC_DSICDateConvert($alBegDate);
                $dateTime = $objDss->convertToFileman($alBegDate);

                $comments=$dataArr['comments']?$dataArr['comments']:"^";

                $newAllergy = array();
                $newAllergy["patientDFN"] = $patientDFN;
                $newAllergy["agent"] = $allergyIdName; //"131^PEANUTS^GMRD(120.82,\"B\")^F^1"
                $newAllergy["type"] = $sendtype; //"F^Food"
                $newAllergy["natureOfReaction"] = "A^Allergy";
                $newAllergy["originator"] = $_SESSION['dss_loginDUZ']; //"10000000032"
                $newAllergy["symptomList"] = array("^"); //["67^DRY MOUTH^3090313.1621^Mar 13,2009@16:21^"]
                $newAllergy["chartList"] = array($dateTime.'.000000'); //["3181218.132205"]
                $newAllergy["observedHistorical"] = "h^HISTORICAL";
                $newAllergy["reactionDate"] = $dateTime.'.000000'; //"3181218.132205"
                $newAllergy["severity"] = "^".$dataArr['severity']; //"1^Mild"
                $newAllergy["comments"] = array($comments); //["Line 1","Line 2","Line 3"

                $newAllr = $objDss->savePatientAllergies($newAllergy);

                if($newAllr[0]['code'] == -1) {
                    throw new Exception('Error: '.$newAllr[0]['desc']);
                }
            } else {
            	throw new Exception('Error: Dss master allergy data not exist.');
            }
        } 
        catch(Exception $e) {
            echo '<script>top.fAlert("'.$e->getMessage().'","", \'top.fmain.location.href="../Medical_history/index.php?showpage=allergies"\' );</script>';
            die;
        }
        return $newAllr;
    }

	/**
	 * Cancel Patient Allergy (Mark allergies in error)
	 */
	public function dssCancelPatientAllergies($del_id) {
		try 
		{
            $patient_id = $_SESSION['patient'];
            $sqlDFN = "SELECT External_MRN_5 FROM `patient_data` WHERE `id` = ".$patient_id;
            $resultDFN = imw_query($sqlDFN);
            $data = imw_fetch_assoc($resultDFN);
            $patientDFN = $data['External_MRN_5'];
                
			// get allergy ien
			$sqlm = imw_query("SELECT dss_allergy_id FROM lists WHERE id = '".$del_id."'");
			if($sqlm && imw_num_rows($sqlm) > 0){
				$row = imw_fetch_assoc($sqlm);
            }
			if(!empty($row['dss_allergy_id']) && $row['dss_allergy_id'] != "" && !empty($patientDFN) && $patientDFN != '')
			{
                $parms=array();
                $parms['patient']=$patientDFN;
                $parms['allergy']=$row['dss_allergy_id'];
                
                include_once( $GLOBALS['srcdir'].'/dss_api/dss_medical_hx.php' );
                $objDss = new Dss_medical_hx();
                $return = $objDss->cancelPatientAllergy($parms);
			}
             else {
				throw new Exception('Error: allergy ien not found');
			}

			return $return;
		} catch(Exception $e) {
			//echo $e->getMessage();
            echo '<script>top.fAlert("'.$e->getMessage().'","", \'top.fmain.location.href="../Medical_history/index.php?showpage=allergies"\' );</script>';
            die;
		}
	}
	
	function get_allergies_modal(){
		
		$from = !empty($_REQUEST["from"]) ? $_REQUEST["from"] : 0 ;
		$limit = 100;
		$i=$from;
		if($i>0){ $i+=1; }	
		
		if($from === "All"){ $limit = 0;  $from = 0; 	}
		
		$srch = "";
		if(!empty($_REQUEST["srch"])){ $srch = $_REQUEST["srch"]; }
		
		$modal_data = ''; $modal_data_tr = '';		
		$allergies_name_arr = $this->get_allergies_names($from, $limit, $srch);
		if(count($allergies_name_arr) > 0){
			
			//<!-- Allergies names row -->';
			//$i=0;
			foreach($allergies_name_arr as $obj){
				$tmp = (!empty($obj['allergie_name'])) ? $obj["allergie_name"] : "&nbsp;" ;
				$modal_data_tr .='<tr id="allergy_row_'.$i.'">
					<td>
						<span class="pointer" id="allergy_name_'.$i.'" onClick="setAllergyName(\''.addslashes(htmlentities($obj["allergie_name"])).'\')">'.$tmp.'</span>
					</td>
					<td>
						<span class="glyphicon glyphicon-pencil" alt="Edit" onClick="modify_allergy_name(\'edit\',\''.$obj['allergies_id'].'\',\'allergy_name_'.$i.'\');"></span>
					</td>
					<td>
						<span class="glyphicon glyphicon-remove pointer" border="0" alt="Delete" onClick="javascript:top.fmain.fancyConfirm(\'Do you want to delete this record?\',\'Delete Record\',\'top.fmain.modify_allergy_name(\\\'del\\\',\\\''.$obj['allergies_id'].'\\\',\\\'allergy_name_'.$i.'\\\')\');"></span>
					</td>
				</tr>';	
			 
				$i++;
			} 

			if(empty($from)){
				$modal_data .= '<div id="view_record" class="col-sm-12">
					<div class="row">
						<div class="col-sm-12">
							<div class="row">
								<table id="allergies_name_tbl" class="table table-bordered table-striped table-condensed">
									<tr class="grythead">
										<th>Allergies</th>
										<th colspan="2" class="text-center">Actions</th>
									</tr>';
								$modal_data .= $modal_data_tr;	
								$modal_data .='</table>	
							</div>	
						</div>
					</div>	
				</div>';
			}else{ $modal_data .= $modal_data_tr; }
			
		}else{
			if(empty($from)){
			$modal_data .= '<div id="view_record" class="col-sm-12">
				<label>No Record</label>
			</div>';
			}
		}
		$modal_data .=	'';
		echo $modal_data;
		exit();
	}

}
?>