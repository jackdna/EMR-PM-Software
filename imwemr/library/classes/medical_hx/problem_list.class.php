<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Medical History -> Problem List Class
 Access Type: Indirect Access.
 
*/
include_once $GLOBALS['srcdir'].'/classes/CLSAlerts.php';
include_once $GLOBALS['srcdir'].'/classes/class.cls_review_med_hx.php';
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
$cls_alerts = new CLSAlerts;
$cls_review_med_hx = new CLSReviewMedHx;

class ProblemLst extends MedicalHistory
{
	//Public variables
	public $z_flg_arrDesc2 = array();
	public $arr_problem_list = array();
	
	//Private variables
	private $tbl;
	private $tbllog;
	private $prob_lst_ids = array();
	private $prob_lst_child_data = array();
	
	public function __construct($tab = 'ocular')
	{
		parent::__construct($tab);
		$this->problem_vocabulary = $this->get_vocabulary("medical_hx", "problem_list");
		$this->tbl = "pt_problem_list";
		$this->tbllog = "pt_problem_list_log";
    }
	
	//Returns typeahead arrays
	public function get_dx_ths(){
		$strDesc = $strPracCode = $strSnowmed_ct = "";
		$sql = imw_query("SELECT diagnosis_category.category, diagnosis_code_tbl.* FROM diagnosis_code_tbl ".
		   "INNER JOIN diagnosis_category ".
		   "ON diagnosis_code_tbl.diag_cat_id = diagnosis_category.diag_cat_id ".
		   "ORDER BY diagnosis_code_tbl.d_prac_code ");
		while($row = imw_fetch_array($sql)){
			if( !empty($row["diag_description"]) && !empty($row["d_prac_code"])){
				$strDesc .= "".addslashes($row["diag_description"])." - ".$row["d_prac_code"]."~~~";
				$strPracCode .= "".$row["d_prac_code"]."," ;
				$strDesc2 .= "".addslashes($row["diag_description"])."," ;
				$this->z_flg_arrDesc2[addslashes($row["diag_description"])." - ".$row["d_prac_code"]]=trim($row["snowmed_ct"]);
			}
		}
		$strDesc = $this->remLineBrk(substr($strDesc,0,-2));
		$strPracCode = $this->remLineBrk(substr($strPracCode,0,-2));
		$strDesc2 = $this->remLineBrk(substr($strDesc2,0,-2));
		return array($strDesc,$strPracCode,$strDesc2,$strSnowmed_ct,$this->z_flg_arrDesc2);
	}
	
	//Returns problem array
	public function get_prob_list_array($status="", $idquery="", $readonly="", $ordrby="ASC", $limit="", $ptid=""){
		$pid = $this->patient_id;
		if(!empty($ptid)){ $pid=$ptid; }
		if(empty($pid)){$pid=0;}
		if(!empty($status) && ($status != "All")){        // If problem status is not empty and not 'All'
			if($status == "Active" || $status == "Other"){ //ADD Other in Active					 
				$status = "AND (status != 'Inactive' AND status != 'Resolved' AND status != 'Unobserved' AND status != 'Deleted')";
			}else{   // If status is other than 'Active' or "other'
				$status = "AND status = '".$status."' ";
			}
		}else{
			$status = "";	
		}
		$problem_list_array = array();
		$sql = "SELECT *, Date_Format(onset_date,'".get_sql_date_format()."') as onset_dates,Date_Format(onset_date,'".get_sql_date_format()."') as new_date, TIME_FORMAT(OnsetTime,'%h:%i:%s %p') as new_OnsetTime 
				FROM ".$this->tbl." WHERE ";
		if($readonly == "yes"){
			$sql .= "problem_name != '' AND";
		}
		$sql .= " pt_id = '".$pid."' $idquery ".$status." ORDER BY onset_date ".$ordrby.", id ".$ordrby." ".$limit;
		$res = imw_query($sql);
		if(imw_num_rows($res) > 0){
			while($row = imw_fetch_array($res)){
				$problem_list_array[] = $row;
				$this->prob_lst_ids[] = $row['id'];
			}
		}
		
		//Generating Child row data for every prob. list for fast HTML rendering
		$prob_lst_child_ids = implode(',',$this->prob_lst_ids);
		if( !$prob_lst_child_ids ) $prob_lst_child_ids = 0;
		$selQuery = "select *,date_format( statusDateTime, '".get_sql_date_format('','y','/')."' ) as statusdate,  
				date_format( statusDateTime, '%h:%i %p' ) as statustime,`status` , `user_id`,
				date_format( onset_date, '".get_sql_date_format()."' ) as onset_date_log,date_format( OnsetTime, '%h:%i:%s %p' ) as OnsetTime_log
				from pt_problem_list_log where `problem_id` IN($prob_lst_child_ids) order by id desc";
				
		$res = imw_query($selQuery);
		while($row = imw_fetch_array($res)){
			if(!in_array($row['problem_id'],$this->prob_lst_child_data)){
				$this->prob_lst_child_data[$row['problem_id']][] = $row;
			}
		}
		
		//Returns only prob. list id array
		return $problem_list_array;
	}
	
	
	//Returns patient name
	public function get_patient_name($id,$noFac="0")
	{
		$sql = imw_query("SELECT id,fname, lname, mname,default_facility FROM patient_data WHERE id = '$id'");
		while($row = imw_fetch_array($sql)){
			$facility_id_p=$row['default_facility'];
			if($facility_id_p<>"")
			{
				$query = "select facilityPracCode from pos_facilityies_tbl where pos_facility_id='$facility_id_p'";
				$result = imw_query($query);
				$rows = imw_fetch_array($result);
				$patient_facility = "(".$rows['facilityPracCode'].")";
			}
			$sep="-";
			if( $noFac == "1" ){
				$patient_facility = "";
				$sep=" - ";
			}
			
			if($row != false){
				$ret = $row["fname"]." ".$row["mname"]." ".$row["lname"].$sep.$row["id"]." ".$row['suffix'].$patient_facility;
				return $ret;
			}
			else{
				return false;
			}
		};
	}
	
	//Returns edit name id
	public function edit_record($id){
		$idquery="and id='".trim($id)."'";
		$this->arr_problem_list = $this->get_prob_list_array("",$idquery);
		$this->arr_problem_list[0]["OnsetTime"] = substr($this->arr_problem_list[0]["OnsetTime"],0,-3);
		if($this->arr_problem_list[0]["OnsetTime"] == '00:00'){
			$this->arr_problem_list[0]["new_OnsetTime"] = '';
		}
		$tmp = getUserFirstName($this->arr_problem_list[0]["user_id"],2);
		$return = $tmp[1];
		return $return;
	}
	
	//Returns problem row data
	public function prob_data_rows($values_arr){
		$problem_code = $problemNameExp = '';
		foreach($values_arr as $obj){
			$arrProblemList = $obj['records'];
			$row_id_number = $obj['row_id'];
			$pid = $obj['pid'];
			$class_name = $obj['class_name'];
			$arr_info_alert = $obj['arr_info_alert'];
			$id = $arrProblemList["id"];
			$onset_date = $arrProblemList["new_date"];
			$problem_name = $arrProblemList["problem_name"];
			$status = trim($arrProblemList["status"]);
			$problem_type = $arrProblemList["prob_type"];
			$ccda_code = $arrProblemList["ccda_code"];
			$operator_id = $arrProblemList["user_id"];
			$tmp = getUserFirstName($operator_id,2);
			$operatorName = $tmp[1];
			
            $service_eligibility='No';
            if(isset($arrProblemList['service_eligibility']) && ($arrProblemList['service_eligibility']!='' && empty($arrProblemList['service_eligibility'])==false)){
                $service_eligibility='Yes';
            }
            
			$status_other = $status;
			if($status!="Active" && $status != "Inactive" && $status != "Resolved" && $status != "Unobserved" && $status != "External"){
				$combo_box_display = "none";
				$text_box_display = "block";
				$status = "Other";
			}else{
				$combo_box_display = "block";
				$text_box_display = "none";
			}
			
			if($arrProblemList["OnsetTime"]!="00:00:00"){
				$OnsetTime = $arrProblemList["new_OnsetTime"];
			}
			else{
				$OnsetTime = '&nbsp;';		
			}
			
			if(trim($class_name) == ''){
				$class_name = 'text_10';
			}
			if(ucfirst($status_other) == "Deleted"){
				$css = "border:1px solid red;color:red;text-decoration:line-through";
			}else $css = '';
			
			//if problem exists
			if(!empty($problem_name)){
				$tmp_problem_name = $problem_name;
				if(stristr($problem_name,"(")){
					$problemNameExp = explode('(',str_ireplace(')',"",$problem_name));
				}else if(stristr($problem_name,"-")){
					$problemNameExp = explode('-',$problem_name);
				}
		
				$problem_code = trim(end($problemNameExp));
				$tmpProblemDesc = "";
				if(stristr($tmp_problem_name,"(")){
					list($tmpProblemDesc) = explode('('.$problem_code.')',$tmp_problem_name);
				}else if(stristr($tmp_problem_name,"-")){
					list($tmpProblemDesc) = explode(' - '.$problem_code,$tmp_problem_name);
				}
				list($tmpProblemDesc) = explode(';',$tmpProblemDesc);
				$tmpProblemDesc = trim($tmpProblemDesc);
				
				$snoMedCode = trim($ccda_code);
				if(!trim($snoMedCode)) {
					$snoMedCode = get_snomed_code($problem_code,$tmpProblemDesc);	
				}
				$problem_code = str_ireplace("-","",$problem_code);
				$linkProblemCode = $problem_code;
				$linkCriteriaVCSNum = "103";
				if($snoMedCode) {
					$linkProblemCode = $snoMedCode;
					$linkCriteriaVCSNum = "96";
				}else if(preg_match("/[a-z]/i", $problem_code)){
					$linkProblemCode = $problem_code;
					$linkCriteriaVCSNum = "90";
				}
				
				$tmpName = getUserFirstName($arrProblemList["user_id"],2);
				$user_name_exp = explode(',',$tmpName[0]);
				$user_name = ucfirst(substr(trim($user_name_exp[1]),0,1)).ucfirst(substr(trim($user_name_exp[0]),0,1));	
				
				$problemID = $arrProblemList['id'];
				/*
				$selQuery2 = "select id from pt_problem_list_log where `problem_id` = '".$problemID."' order by id desc";
				$res2 = imw_query($selQuery2);
				$numRows2 = imw_num_rows($res2);	
				
				 $selQuery = "select *,date_format( statusDateTime, '".get_sql_date_format('','y','/')."' ) as statusdate,  
					date_format( statusDateTime, '%h:%i %p' ) as statustime,`status` , `user_id`,
					date_format( onset_date, '".get_sql_date_format()."' ) as onset_date_log,date_format( OnsetTime, '%h:%i %p' ) as OnsetTime_log
					from pt_problem_list_log where `problem_id` = '".$problemID."' order by id desc limit 1,$numRows2";
				$res = imw_query($selQuery);
				
				//Setting child row array
				$child_row_data = array();
				while($row_sql = imw_fetch_array($res)){
					$child_row_data[] = $row_sql;
				}
				$numRows = imw_num_rows($res); */	
				$numRows = count($this->prob_lst_child_data[$problemID]);
				$row .= "
				<tr class='$class_name pointer prob_list_$id' style='".$css.";'>
				<td class='text-center'><div class='checkbox'><input type='checkbox' name='chk_records[]' id='chk_records_".$id."' value='".$id."'><label for='chk_records_".$id."'></label></div></td>
					<td class='pointer' onclick='modify_ProblemData(\"$id\",\"$pid\")'><span>".ucfirst($problem_name)."</span>&nbsp;<span id='info_prob_$row_id_number' class='glyphicon glyphicon-info-sign pull-right font-18' title='Info Button' onclick='javascript: var medInfoWin = window.open(\"https://apps.nlm.nih.gov/medlineplus/services/mpconnect.cfm?mainSearchCriteria.v.c=".$linkProblemCode."&mainSearchCriteria.v.cs=2.16.840.1.113883.6.".$linkCriteriaVCSNum."&mainSearchCriteria.v.dn=".str_ireplace("&","and",ucfirst($problem_name))."&informationRecipient.languageCode.c=en\",\"ProblemListInfo\",\"height=700,width=1000,top=50,left=50,scrollbars=yes\");medInfoWin.focus();'></span></td>
					<td onclick='modify_ProblemData(\"$id\",\"$pid\")'>".ucfirst($problem_type)."</td>
					<td onclick='modify_ProblemData(\"$id\",\"$pid\")'>".ucfirst($ccda_code)."</td>
					<td class='text-center' onclick='modify_ProblemData(\"$id\",\"$pid\")'>".ucfirst($status_other)."</td>";
                    if(isDssEnable()){$row .= "<td class='text-center' onclick='modify_ProblemData(\"$id\",\"$pid\")'>".$service_eligibility."</td>";}
					$row .= "<td class='text-nowrap' onclick='modify_ProblemData(\"$id\",\"$pid\")'>$onset_date $OnsetTime</td>";
				$row .= "<td class='text-center' nowrap onclick='modify_ProblemData(\"$id\",\"$pid\")'>".get_date_format(date('m-d-Y',strtotime($arrProblemList["timestamp"])),'mm-dd-yyyy')."</td>
				<td class='text-center text-nowrap' onclick='modify_ProblemData(\"$id\",\"$pid\")'>".date('h:i A',strtotime($arrProblemList["timestamp"]))."</td>
				<td class='text-center text-nowrap'>".$user_name."</td>
				<td id=\"tdImageArrow_".$arrProblemList['id']."\">";
				if($numRows>0){
					$row .= "<span class='glyphicon glyphicon-triangle-bottom' data-call='no' onClick='show_child_rows(this,\"$problemID\")'></span>";
				}	
				$row .= "</td></tr>";
				
				if($numRows > 0){
					//$this->get_prob_list_array() generates child row data array i.e $this->prob_lst_child_data;
					$row .= $this->get_child_row($problemID,$this->prob_lst_child_data[$problemID],$problem_code,$snoMedCode,$problem_name);
				}
			}
		}
		return $row;
	}
	
	//Returns child row data
	public function get_child_row($problemID,$child_row_data,$problem_code,$snoMedCode,$problem_name){
		$child_data ='';
		foreach($child_row_data as $obj){
			$tmpName = getUserFirstName($obj["user_id"],2);
			$user_name_exp = explode(',',$tmpName[0]);
			$user_name = ucfirst(substr(trim($user_name_exp[1]),0,1)).ucfirst(substr(trim($user_name_exp[0]),0,1));
            $service_eligibility='No';
            if(isset($obj['service_eligibility']) && $obj['service_eligibility']==1){
                $service_eligibility='Yes';
            }
			$child_data .= "<tr class='trIDS".$problemID." hide child_row' data-child='yes'>
				<td width='25' class='text-center'>&nbsp;</td>
				<td><span style='vertical-align:top;'>".ucfirst($obj['problem_name'])." </span>&nbsp;<span class='glyphicon glyphicon-info-sign' id='info_prob_$row_id_number' title='Info Button' onclick='javascript: var medInfoWin = window.open(\"https://apps.nlm.nih.gov/medlineplus/services/mpconnect.cfm?mainSearchCriteria.v.c=".$linkProblemCode."&mainSearchCriteria.v.cs=2.16.840.1.113883.6.".$linkCriteriaVCSNum."&mainSearchCriteria.v.dn=".str_ireplace("&","and",ucfirst($problem_name))."&informationRecipient.languageCode.c=en\",\"ProblemListInfo\",\"height=700,width=1000,top=50,left=50,scrollbars=yes\");medInfoWin.focus();' ></span></td>
				<td>".ucfirst($obj['prob_type'])."</td>
				<td>".ucfirst($obj['ccda_code'])."</td>
				<td class='text-center'>".ucfirst($obj['status'])."</td>";
				if(isDssEnable()){$child_data .= "<td class='text-center'>".$service_eligibility."</td>";}
				$child_data .= "<td>".$obj['onset_date_log'].' '.$obj['OnsetTime_log']."</td>";
				$child_data .= "<td class='text-center' nowrap>".get_date_format(date('m-d-Y',strtotime($obj["timestamp"])),'mm-dd-yyyy')."</td>
				<td class='text-center' nowrap>".date('h:i A',strtotime($obj["timestamp"]))."</td>
				<td nowrap colspan='3'>".$user_name."</td>";
				$child_data .= "</tr>";	
		}
		return $child_data;
	}
	
	//Check UMLS 
	public function check_umls_pl($med_name,$index){
		$medName_exp = explode('(',trim(urldecode($med_name)));
		$medName_exp = explode('-',trim($medName_exp[0]));
		$medName=trim($medName_exp[0]);
		$index_val = "";
		if(isset($index)){
			$index_val = trim(urldecode($index));
		}
		
		$sql = "SELECT * FROM ".constant('UMLS_DB').".problem_list WHERE LOWER(Preferred_Concept_Name) LIKE '%".strtolower($medName)."%'";
		$res = imw_query($sql);
		$i=0;
		while($row = imw_fetch_assoc($res)){
			$i++;
			$str_umls .='<div class="col-sm-12 pt10">
			<div class="col-sm-1 text-center">
				<div class="radio radio-inline"><input type="radio" value="'.$row["Preferred_Concept_Name"].'" name="rxnorm_id" id="rxnorm_id_'.$i.'" onChange="top.fmain.fill_pl_code(\''.addslashes($row["Preferred_Concept_Name"]).'\',\''.addslashes($row["Concept_Code"]).'\',\''.addslashes($index_val).'\')"><label for="rxnorm_id_'.$i.'"></label></div>	
			</div>
			<div class="col-sm-6">
				'.$row["Preferred_Concept_Name"].'	
			</div>
			<div class="col-sm-5">
				'.$row["Concept_Code"].'
			</div></div>';
		}
		if($str_umls != ""){
			$str = '<div class="row" style="height:300px;overflow-y:scroll;overflow-x:hidden">
				<div class="purple_bar">
					<div class="row">
						<div class="col-sm-1">&nbsp;</div>
						<div class="col-sm-6">Problem</div>
						<div class="col-sm-5">SNOMED CT</div>
					</div>
				</div>
				<div class="col-sm-12"><div class="row">';
			$str .= $str_umls;
			$str .= "</div></div></div>";
		}
		return $str;
	}
	
	//Delete problem list 
	public function delete_pro_list($request){        
        $counter = 0;
		$record_ids = $request['del_ids'];
        
        $sqlrs=imw_query('select External_MRN_5 from patient_data where id='.$this->patient_id.' ');
        
        $dss_row=imw_fetch_assoc($sqlrs);
        $dss_dfn=$dss_row['External_MRN_5'];
        
        if(empty($dss_dfn)==false && $dss_dfn!=NULL && $dss_dfn!='' && isDssEnable() && !isset($request['dssload'])) {
            $this->delete_problem_from_vista($record_ids,$dss_dfn);
        }
        
		$q = "UPDATE pt_problem_list 
				SET status = 'Deleted' 
				WHERE id IN ($record_ids) 
				AND pt_id = '".$this->patient_id."'";
		imw_query($q)or die(imw_error());
		$arr_info_alert['save'] = "Records deleted successfully";
		$res = imw_query("SELECT * from pt_problem_list where id IN ($record_ids)");
		
		while($row = imw_fetch_assoc($res)){
			$q = "INSERT INTO pt_problem_list_log 
					SET problem_id = '".$row['id']."', 
						pt_id = '".$row['pt_id']."', 
						user_id = '".$_SESSION['authId']."', 
						problem_name = '".$row['problem_name']."',
						comments = '".$row['comments']."', 
						onset_date = '".$row['onset_date']."', 
						status = 'Deleted', 
						signerId = '".$row['signerId']."',
						coSignerId = '".$row['coSignerId']."', 
						OnsetTime = '".$row['OnsetTime']."',
						statusDateTime = '".date('Y-m-d H:i:s')."', 
						prob_type = '".$row['prob_type']."',
                        service_eligibility = ".$row['service_eligibility']."
			";	
			imw_query($q);
			$counter = ($counter+imw_affected_rows());
			
			// Audit functionality

			//Start Audit
			//$patientProblemListFields = makeFieldTypeArray("select * from pt_problem_list LIMIT 0 , 1");
			$patientProblemListFields = make_field_type_array("pt_problem_list");
			if($patientProblemListFields == 1146){
				$patientProblemListError = "Error : Table 'pt_problem_list' doesn't exist";
			}
			$table = array("pt_problem_list");
			$error = array($patientProblemListError);
			$mergedArray = mergingArray($table,$error);

			$opreaterId = $_SESSION['authId'];			
			$ip = getRealIpAddr();
			$URL = $_SERVER['PHP_SELF'];													 
			$os = getOS();
			$browserInfoArr = array();
			$browserInfoArr = _browser();
			$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
			$browserName = str_replace(";","",$browserInfo);													 
			$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);	
			$arrAuditTrail= array();
			$action="delete";
			$arrAuditTrail [] = 
				array(
						"Pk_Id"=> $_REQUEST["pk_id"],
						"Table_Name"=>"pt_problem_list",
						"Data_Base_Field_Name"=> "id" ,
						"Data_Base_Field_Type"=> fun_get_field_type($patientProblemListFields,"id"),
						"Filed_Label"=> "",
						"Filed_Text"=> "Problem Name",
						"Action"=> $action,
						"Operater_Id"=> $opreaterId,
						"Operater_Type"=> getOperaterType($opreaterId) ,
						"IP"=> $ip,
						"MAC_Address"=> $_REQUEST['macaddrs'],
						"URL"=> $URL,
						"Browser_Type"=> $browserName,
						"OS"=> $os,
						"Machine_Name"=> $machineName,
						"pid"=> $_REQUEST["pid"],
						"Category"=> "patient_info-medical_history",
						"Category_Desc"=> "problems",	
						"Old_Value"=> addcslashes(addslashes($row['problem_name']),"\0..\37!@\177..\377"),
						"New_Value"=> "",

					);
			$policyStatus = 0;
			$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];
			if($policyStatus == 1){
				auditTrail($arrAuditTrail,$mergedArray,0,0,0);
			}			
			//End Audit

		}
		return $counter;
	}
	
	public function checkDate($dt){
		if($dt == "" || $dt == "0000-00-00"){
			$dt = date("Y-m-d");
		}
		return $dt;
	}
	
	//Inserts new problem list data
	public function insert_prob_list_rec($arr){
		$tempID=0;
		$arr["onset_date"] = $this->checkDate($arr["onset_date"]);
		if($arr["OnsetTime"]==""){
			$arr["OnsetTime"]=date('H:i:s');
		}
        else
        {
            $arr["OnsetTime"]=date('H:i:s', $arr["OnsetTime"]);
        }
		if(!empty($arr["problem_name"])){
			//Remove  attached  dx codes
			//$arr["problem_name"] = remSiteDxFromAssessment($arr["problem_name"]);
			$sql = "INSERT INTO ".$this->tbl." (id,pt_id, user_id,problem_name,comments,onset_date,status,
												signerId, coSignerId, OnsetTime,prob_type,form_id,ccda_code,service_eligibility) ".
					"VALUES (NULL, '".$arr["pt_id"]."', '".$arr["user_id"]."',  '".$arr["problem_name"]."','".$arr["comments"]."','".$arr["onset_date"]."','".$arr["status"]."',
					'".$arr["signerId"]."','".$arr["coSignerId"]."','".$arr["OnsetTime"]."','".$arr["prob_type"]."','".$arr["form_id"]."','".$arr["ccda_code"]."',".$arr["service_eligibility"].")";
			$res = imw_query($sql) or die("Error in query: ".$sql." ".imw_error());
			$tempID=imw_insert_id();		
			$sqlLog = "INSERT INTO ".$this->tbllog." (id,problem_id,pt_id, user_id,problem_name,comments,onset_date,status,
												signerId, coSignerId, OnsetTime,statusDateTime,prob_type,ccda_code,service_eligibility)".
					"VALUES (NULL,'".$tempID."','".$arr["pt_id"]."', '".$_SESSION["authId"]."',  '".$arr["problem_name"]."','".$arr["comments"]."','".$arr["onset_date"]."','".$arr["status"]."',
					'".$arr["signerId"]."','".$arr["coSignerId"]."','".$arr["OnsetTime"]."',now(),'".$arr["prob_type"]."','".$arr["ccda_code"]."',".$arr["service_eligibility"].")";
			$resLog = imw_query($sqlLog) or die("Error in query: ".$sqlLog." ".imw_error());
		}
		return $tempID;
	}
	
	
	//Updates problem list data
	public function update_prob_list_rec($arr){
		$counter = 0;	
		$arr["problem_name"] = trim($arr["problem_name"]);		
		$arr["onset_date"] = $this->checkDate($arr["onset_date"]);			
		if($arr["OnsetTime"]==""){
			$arr["OnsetTime"]=date('H:i:s');
		}
        else
        {
            $arr["OnsetTime"]=date('H:i:s', $arr["OnsetTime"]);
        }
		if(!empty($arr["id"]) && !empty($arr["problem_name"])){
			//--- CHECK PREVIOUS STATUS ---
			$prev_status_flag = false;
			$query = "select * from ".$this->tbl." where id = '".$arr["id"]."'";
			$statusRez = imw_query($query);
			while($statusRes = imw_fetch_array($statusRez)){
				$tableStatus = $statusRes['status'];
				$problem_name = $statusRes['problem_name'];
				$comments = $statusRes['comments'];
				$onset_date = $statusRes['onset_date'];
				$OnsetTime = $statusRes['OnsetTime'];
				$prob_type = $statusRes['prob_type'];
				$ccda_code = $statusRes['ccda_code'];
				$service_eligibility = $statusRes['service_eligibility'];
				if($service_eligibility != $arr["service_eligibility"] || strtolower($tableStatus) != strtolower($arr["status"]) || strtolower($problem_name) != strtolower($arr["problem_name"]) || strtolower($comments) != strtolower($arr["comments"]) || strtolower($onset_date) != strtolower($arr["onset_date"]) || strtolower($OnsetTime) != strtolower($arr["OnsetTime"]) || strtolower($prob_type) != strtolower($arr["prob_type"]) || strtolower($ccda_code) != strtolower($arr["ccda_code"])){
					$prev_status_flag = true;
				}
			}
			
			
			//if update from work view
			if(!empty($arr["form_id"])){	$phrs_form_id = " , form_id='".$arr["form_id"]."' ";	}else{ $phrs_form_id=""; }
			
			//Remove  attached  dx codes
			//$arr["problem_name"] = remSiteDxFromAssessment($arr["problem_name"]);
			
			//Nov 2, 2016:: Discussed with Arun, if DX Code is same, then date in problem list should not be changed. Also make an update to correct previous records
			if(isset($arr["dx"])){//will work when wv saved
				if(!empty($arr["dx"])){
					if(strpos($problem_name, $arr["dx"])!==false){
						$arr["onset_date"] = $onset_date;
						$arr["OnsetTime"] = $OnsetTime;
					}
				}else{
					if(trim($problem_name) == trim($arr["problem_name"])){
						$arr["onset_date"] = $onset_date;
						$arr["OnsetTime"] = $OnsetTime;
					}
				}
			}
			//--
			
			$sql = "UPDATE ".$this->tbl." ".
					"SET ".
					"user_id = '".$arr["user_id"]."', ".
					"problem_name = '".$arr["problem_name"]."', ".
					"comments = '".$arr["comments"]."', ".
					"onset_date = '".$arr["onset_date"]."', ".
					"status = '".$arr["status"]."', ".
					"service_eligibility = ".$arr["service_eligibility"].", ".
					"signerId = '".$arr["signerId"]."', ".
					"coSignerId = '".$arr["coSignerId"]."', ".
					"OnsetTime = '".$arr["OnsetTime"]."', ".
					"prob_type = '".$arr["prob_type"]."', ".
					"ccda_code = '".$arr["ccda_code"]."' ".$phrs_form_id.
					"WHERE id = '".$arr["id"]."' ";
			$res = imw_query($sql) or die("Error in query: ".$sql." ".imw_error());
			$counter = ($counter+imw_affected_rows());

			if($prev_status_flag == true){
				$sqllog ="insert into  ".$this->tbllog." ".
						"SET ".
						"problem_id = '".$arr["id"]."', ".
						"pt_id = '".$arr["pt_id"]."', ".
						"user_id = '".$_SESSION["authId"]."', ".
						"problem_name = '".$arr["problem_name"]."', ".
						"comments = '".$arr["comments"]."', ".
						"onset_date = '".$arr["onset_date"]."', ".
						"status = '".$arr["status"]."', ".
						"service_eligibility = ".$arr["service_eligibility"].", ".
						"signerId = '".$arr["signerId"]."', ".
						"coSignerId = '".$arr["coSignerId"]."', ".
						"OnsetTime = '".$arr["OnsetTime"]."',
						ccda_code = '".$arr["ccda_code"]."',
						prob_type='".$arr["prob_type"]."',statusDateTime=now()" ;
				$resLog = imw_query($sqllog) or die("Error in query: ".$sqllog." ".imw_error());
				$counter = ($counter+imw_affected_rows());
			}
		}
		return $counter;
	}
	
	//Saving prob list data 
	public function save_prob_list_rec($request){
		global $cls_review_med_hx;
		$counter = 0;
        
		if( is_allscripts() ) include_once( $GLOBALS['srcdir'].'/allscripts/as_patient.php' );
		
        /*
		$timeArr = preg_split('/(:)|( )/',$request['list_date_time'][0]);
		if($timeArr[0] != 12){
			if(strtolower($timeArr[2]) == 'pm'){
				$timeArr[0] = $timeArr[0] + 12;
			}
		}
		else{
			if(strtolower($timeArr[2]) == 'am'){
				$timeArr[0] = $timeArr[0] + 12;
			}
		}
        
        
		$list_date_time[0] = $timeArr[0] .':'.$timeArr[1].':00'; 
        */
        $list_date_time[0] = strtotime($request['list_date_time'][0]);
        
        
		$id = $request["id"];
		$dss_prblm_id = $request["dss_prblm_id"];
		$list_date = xss_rem($request["list_date"]);
		$list_problem = xss_rem($request["list_problem"]);
		$list_status = xss_rem($request["list_status"]);
		$list_status_other = xss_rem($request["list_status_other"]);
		$list_operator_name = xss_rem($request["current_user_id"]);
		$selected_type = "Active";
		$prob_type = xss_rem($request["prob_type"]);
		$ccda_code = xss_rem($request["ccda_code"]);
        $service_eligibility=0;
        if(isDssEnable() && isset($request["service_eligibility"]) && $request["service_eligibility"]!='') {
        	parse_str($request["service_eligibility"], $sc_option);
            $service_eligibility = '\''.serialize($sc_option['dss']).'\'';
        }
		$length = count($list_problem);

		if($length > 0)
		{
			$arrUp = array();
			$arrUp["pt_id"] = $this->patient_id;
			for($i=0;$i<$length;$i++){		
				if(trim($list_problem[$i]) != ""){
					$arrUp["id"] = $id[$i];
					$arrUp["dss_prblm_id"] = $dss_prblm_id[$i];
					$arrUp["problem_name"] 	= addslashes($list_problem[$i]);
					$arrUp["onset_date"] 	= getDateFormatDB($list_date[$i]);
					$arrUp["comments"] 		= "";
					$arrUp["status"] 		= ($list_status[$i] != "Other") ? $list_status[$i] : $list_status_other[$i];
					$arrUp["user_id"] 		= $list_operator_name[$i];
					$arrUp["OnsetTime"]		= $list_date_time[$i];
					$arrUp["prob_type"] 	= $prob_type;
					$arrUp["ccda_code"] 	= $ccda_code;
					$arrUp["service_eligibility"] 	= $service_eligibility;
					
					$qry = "select * from pt_problem_list where id =".$id[$i];
					$res = imw_query($qry);
					$row = imw_fetch_assoc($res);
					if(!empty($id[$i])){
						//Update
						$md_action = "update";		
                        //Update Record to DSS
						if(isDssEnable()) {
                            $return = $this->upload_problem_to_vista($arrUp,$md_action);
                        }
                        
						$counter += $this->update_prob_list_rec($arrUp);
                        
                        
                        
						/*Push Update to touch Works*/
						if( is_allscripts() )
						{
							try
							{
								//Get AllScripts data for the problem Id, proceed if exists
								$asId = $asData = '';
								$asSql = "SELECT `as_id`, `as_data` FROM `pt_problem_list` WHERE `id`=".$arrUp['id'];
								$asSql = imw_query($asSql);
								if( $asSql && imw_num_rows($asSql) > 0 )
								{
									$asSql = imw_fetch_assoc( $asSql );
									$asId = $asSql['as_id'];
									$asData = ($asSql['as_data']!='')?json_decode($asSql['as_data']):'';
								}
								
								if( $asId!='' && is_object($asData))
								{
									$allowedStatus = array('Active', 'Inactive', 'Resolved');
									
									if( !in_array($arrUp['status'], $allowedStatus) )
										throw new asException( 'Error', 'Touch Works does not recognize the status \''.$arrUp['status'].'\'.<br /> Please adjust this manually.');
									
									$problemData = array('as_id' => $asId, 'title' => $arrUp['problem_name'], 'icd9' => $asData->icd9 );
									$problemData['onset'] = date('d-M-Y', strtotime($arrUp['onset_date']));
									$problemData['status'] = $arrUp['status'];
									$problemData['incActive'] = ( $arrUp['status']==='Active' )?'Y':'N';
									$problemData['incPMH'] = ( $arrUp['status']!=='Active' )?'Y':'N';
									
									$patientObj = new as_patient();
									$problemResp = $patientObj->updateProblem( $problemData );
									
									if( strtolower($problemResp->Status) !== 'success' )
										throw new asException( 'Error', $allergyResp->status);
								}
							}
							catch( asException $e)
							{
							?>
								<script>
									top.fAlert("Unable to update problem in Touch Works.<br /><?php echo $e->getErrorText(); ?>");
									/* top.document.getElementById("findBy").value = "Active";
									top.document.getElementById("findByShow").value = "Active";
									top.show_loading_image('hide'); */
								</script>
							<?php
							}
							//Unset the object
							if( isset($patientObj) && is_object($patientObj) )
								unset($patientObj);
						}
						/*End Push Update to touch Works*/
					}else{
						$md_action = "add";
                        //Add Record to DSS
                        if(isDssEnable()) {
                            $return = $this->upload_problem_to_vista($arrUp,$md_action);
                        }
                        
						$counter += $this->insert_prob_list_rec($arrUp);
					}
                    

					$selected_type = $list_status[$i];
					if($selected_type=="Other")
					{
						$selected_type = "Active";
					}
					$proDataFields = make_field_type_array("pt_problem_list");
					//--- REVIEWED FOR problem_name ---
						$reviewed_data_arr = array();
						$reviewed_data_arr['Pk_Id'] = $id[$i];
						$reviewed_data_arr['Table_Name'] = 'pt_problem_list';
						$reviewed_data_arr['UI_Filed_Name'] = 'list_problem';
						$reviewed_data_arr['Data_Base_Field_Name']= "problem_name";
						$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($proDataFields,"problem_name");
						$reviewed_data_arr['Field_Text'] = 'Problem Name';
						$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
						$reviewed_data_arr['Action'] = $md_action;
						$reviewed_data_arr['Old_Value'] = $row['problem_name'];
						$reviewed_data_arr['New_Value'] = addslashes($list_problem[$i]);
						$med_reviewed_arr[] = $reviewed_data_arr;
						
						//--- REVIEWED FOR problem type ---
						$reviewed_data_arr = array();
						$reviewed_data_arr['Pk_Id'] = $id[$i];
						$reviewed_data_arr['Table_Name'] = 'pt_problem_list';
						$reviewed_data_arr['UI_Filed_Name'] = 'prob_type';
						$reviewed_data_arr['Data_Base_Field_Name']= "prob_type";
						$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($proDataFields,"prob_type");
						$reviewed_data_arr['Field_Text'] = 'Problem Type - '.addslashes($list_problem[$i]);
						$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
						$reviewed_data_arr['Action'] = $md_action;
						$reviewed_data_arr['Old_Value'] = $row['prob_type'];
						$reviewed_data_arr['New_Value'] = $prob_type;
						$med_reviewed_arr[] = $reviewed_data_arr;
						
						//--- REVIEWED FOR problem status ---
						$reviewed_data_arr = array();
						$reviewed_data_arr['Pk_Id'] = $id[$i];
						$reviewed_data_arr['Table_Name'] = 'pt_problem_list';
						$reviewed_data_arr['UI_Filed_Name'] = 'list_status';
						$reviewed_data_arr['Data_Base_Field_Name']= "status";
						$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($proDataFields,"status");
						$reviewed_data_arr['Field_Text'] = 'Problem Status - '.addslashes($list_problem[$i]);
						$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
						$reviewed_data_arr['Action'] = $md_action;
						$reviewed_data_arr['Old_Value'] = $row['status'];
						$reviewed_data_arr['New_Value'] = $arrUp["status"];
						$med_reviewed_arr[] = $reviewed_data_arr;
						
						//--- REVIEWED FOR problem SNOMED CT ---
						$reviewed_data_arr = array();
						$reviewed_data_arr['Pk_Id'] = $id[$i];
						$reviewed_data_arr['Table_Name'] = 'pt_problem_list';
						$reviewed_data_arr['UI_Filed_Name'] = 'ccda_code';
						$reviewed_data_arr['Data_Base_Field_Name']= "ccda_code";
						$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($proDataFields,"ccda_code");
						$reviewed_data_arr['Field_Text'] = 'Problem SNOMED CT - '.addslashes($list_problem[$i]);
						$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
						$reviewed_data_arr['Action'] = $md_action;
						$reviewed_data_arr['Old_Value'] = $row['ccda_code'];
						$reviewed_data_arr['New_Value'] = $arrUp["ccda_code"];
						$med_reviewed_arr[] = $reviewed_data_arr;
						
						//--- REVIEWED FOR onset date  ---
						$reviewed_data_arr = array();
						$reviewed_data_arr['Pk_Id'] = $id[$i];
						$reviewed_data_arr['Table_Name'] = 'pt_problem_list';
						$reviewed_data_arr['UI_Filed_Name'] = 'list_date';
						$reviewed_data_arr['Data_Base_Field_Name']= "onset_date";
						$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($proDataFields,"onset_date");
						$reviewed_data_arr['Field_Text'] = 'Problem Onset Date - '.addslashes($list_problem[$i]);
						$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
						$reviewed_data_arr['Action'] = $md_action;
						$reviewed_data_arr['Old_Value'] = $row['onset_date'];
						$reviewed_data_arr['New_Value'] = getDateFormatDB($list_date[$i]);
						$med_reviewed_arr[] = $reviewed_data_arr;
						
						//--- REVIEWED FOR onset time  ---
						$tmDbase="";
						$tm = $row['OnsetTime'];
						if($tm!="00:00:00" && $md_action == "update") {$tmDbase = date('h:i A',strtotime($tm));}
						$reviewed_data_arr = array();
						$reviewed_data_arr['Pk_Id'] = $id[$i];
						$reviewed_data_arr['Table_Name'] = 'pt_problem_list';
						$reviewed_data_arr['UI_Filed_Name'] = 'list_date_time';
						$reviewed_data_arr['Data_Base_Field_Name']= "OnsetTime";
						$reviewed_data_arr['Data_Base_Field_Type']= fun_get_field_type($proDataFields,"OnsetTime");
						$reviewed_data_arr['Field_Text'] = 'Problem Onset Time - '.addslashes($list_problem[$i]);
						$reviewed_data_arr['Operater_Id'] = $_SESSION['authId'];
						$reviewed_data_arr['Action'] = $md_action;
						$reviewed_data_arr['Old_Value'] = $tmDbase;
						$reviewed_data_arr['New_Value'] = $_POST['list_date_time'][$i];
						$med_reviewed_arr[] = $reviewed_data_arr;
						//CLSReviewMedHx::reviewMedHx($med_reviewed_arr,$_SESSION['authId'],"Problem List",$_SESSION['patient'],0,0);
						$cls_review_med_hx->reviewMedHx($med_reviewed_arr,$_SESSION['authId'],"Problem List",$_SESSION['patient'],0,0);
					}
			}
		}
		
		return $counter;
	}
	
	public function remLineBrk($str){
		return str_replace(array("\r","\n"),array("\\r","\\n"),$str);
	}
	
	//Setting CLS Alerts
	public function set_lab_cls_alerts(){
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
	 * Function for getting list of problems in JSON format for patient problem list popup
	 * 
	 * @param unknown $status      Status for which problems to be get
	 * @return string              JSON string containing list of problems
	 */
	public function getPatientProblemsList($status){
	    $patientProblemListArray = $this->get_prob_list_array($status);            // Invoke get_prob_list_array() method for getting problems list as array
	    $arraySize = sizeof($patientProblemListArray);             // Get size of problem list array
	    $problem_list_array = array (); // Array for holding problem list
	    for($i = 0; $i < $arraySize; $i++){            // Iterate array to get problem details
	        $problem_array = array ();
	        $problem_array ["problem_list_id"] = $patientProblemListArray[$i]['id'];           // Get problem id and put in array
	        $problem_array ["problem_name"] = $patientProblemListArray[$i]['problem_name'];    // Get problem name and put in array
	        $problem_array ["onset_date"] = $patientProblemListArray[$i]['onset_date'];        // Get onset date and put in array
	        $problem_array ["status"] = $patientProblemListArray[$i]['status'];                // Get problem status and put in array
	        $problem_list_array ["$i"] = $problem_array;           // Put problem array in another 2d array
	    }
	    $problem_list_array["size"] = $arraySize;          // Set size of problem array
	    $tmp = explode(", ", trim($_SESSION["authProviderName"]));
	    $problem_list_array["operator"] = $tmp[1][0].$tmp[0][0];     // Set operator name in problem array
	    return json_encode($problem_list_array);           // Return problem array in JSON format
	}
	
	public function save_problem_list_from_popup($request_array){
	    $problems = json_decode($request_array['problems']);
	    $existing_problems = json_decode($request_array['old_problems']);
	    $deleteArray = array();
	    
	    foreach($problems as $k2){     // Iterate form problems list
	        $form_problem_id = $k2->problem_id;        // Problem id in form
	        $problem_name = $k2->problem_name;
	        $onset_date = $k2->onset_date;
	        $status = $k2->status;
	        $comments = "";
	        $existing_pidif = FALSE;
	        foreach($existing_problems as $k1){        // Iterate existing problems list
	            $existing_problem_id = $k1->existing_problem;
	            if($form_problem_id == $existing_problem_id){       // If problem in form is existing
	                // Update existing problem
	                $existing_pidif = TRUE;
	                //echo "Update existing problem: ".$form_problem_id."\n";
	                imw_query("update ".$this->tbl." set problem_name='".$problem_name."', onset_date='".$onset_date."', status='".$status. "' where id=".$form_problem_id.")");
	                
	                // Add record in pt_problem_list_log table
	                $query = "insert into pt_problem_list_log(problem_id, pt_id, user_id, problem_name, comments, onset_date, status, signerId, coSignerId, OnsetTime, statusDateTime, ccda_code) values
                    (".$lastInsertId.", ".$this->patient_id.", ".$_SESSION['authId'].", '".$problem_name."', '".$comments."', '".$onset_date."', '".$status."', 0, 0, '".date('H:i:s')."', 'now()', '')";
	                imw_query($query);
	                break;
	            }
	        }
            if($existing_pidif === FALSE && strpos($form_problem_id, "problem_") === 0){      // If new problem added
	            // Insert new problem
                $onset_date = $this->checkDate($onset_date);
	            $insertQuery = "insert into ".$this->tbl."(pt_id, user_id, problem_name, onset_date, status, OnsetTime) values(".$this->patient_id.", ".$_SESSION['authId'].", '".$problem_name."','".$onset_date."','". $status."', '".date('H:i:s')."')";
	            imw_query($insertQuery);       // Execute insert query
	            
	            $lastInsertId = imw_insert_id();       // Get last inserted problem id
	            
	            // Add record in pt_problem_list_log table
	            $query = "insert into pt_problem_list_log(problem_id, pt_id, user_id, problem_name, comments, onset_date, status, signerId, coSignerId, OnsetTime, statusDateTime, ccda_code) values
                (".$lastInsertId.", ".$this->patient_id.", ".$_SESSION['authId'].", '".$problem_name."', '".$comments."', '".$onset_date."', '".$status."', 0, 0, '".date('H:i:s')."', 'now()', '')";
	            imw_query($query);         // Execute insert query
	        }
	    }
	    
	    foreach($existing_problems as $j1){     // Iterate form problems list
	        $existing_problem_id = $j1->existing_problem;        // Problem id in form
	        $existing_problem_is_in_form = FALSE;
	        foreach($problems as $j2){        // Iterate existing problems list
	            $form_problem_id = $j2->problem_id;
	            if($existing_problem_id == $form_problem_id){
	                $existing_problem_is_in_form = TRUE;
	                break;
	            }
	        }
	        if($existing_problem_is_in_form === FALSE){
	            $deleteArray[] = $existing_problem_id;
	        }
	    }
	    $problemsToBeDeleted = implode(',', $deleteArray);
	    $deleteArray['del_ids'] = $problemsToBeDeleted;
	    $this->delete_pro_list($deleteArray);
	}
    
    
    public function dss_pat_problem_list() {
        if($this->patient_id !== ''){
            include_once( $GLOBALS['srcdir'].'/dss_api/dss_medical_hx.php' );
            
            $dssPtId = false;
            $dssIdSql = "SELECT `External_MRN_5` FROM `patient_data` WHERE `id`=".( (int) $this->patient_id )." ";
            $dssIdSql = imw_query( $dssIdSql );
            if( $dssIdSql && imw_num_rows($dssIdSql) > 0 )
            {
                $dssPtrs = imw_fetch_assoc( $dssIdSql );
                $dssPtId = $dssPtrs['External_MRN_5'];
            }

            if ($dssPtId && $dssPtId!= NULL && $dssPtId!='') {
                //$problem_status=array('A','I');
                $objDss_mhx = new Dss_medical_hx();

                $params = array();
                $params['patient'] = $dssPtId;
                //$params['problemStatus'] = "A";     
                //"A" if the user wants to see active problems or "I" if the user want to see inactive problems
                    
                    //API call to get patient problem list from dss
                    $returnList = $objDss_mhx->GetPatientProblemList($params);

                    if ($returnList) {
                    
                        //Fetch patients All problems
                        $selectsql="SELECT id,dss_prblm_id,status,dss_last_modified_date,problem_name FROM pt_problem_list WHERE `pt_id`=".( (int) $this->patient_id )." AND `status` != 'Deleted'  ";
                        $dss_prblm_arr=array();
                        $dss_prblmArr=array();
                        $sql_rs=imw_query($selectsql);
                        if($sql_rs && imw_num_rows($sql_rs)>0) {
                            /*End Check if Problems for the patient with same dssid(ifn) already exists in imwemr*/
                            while ($row = imw_fetch_assoc($sql_rs)) {
                                //if($row['dss_prblm_id']>0) {
                                    $dss_prblm_arr[$row['dss_prblm_id']]=$row;
                                    //$dss_prblmArr[$row['dss_prblm_id']]=$row['id'];
                                //}
                            }
                        }
                        
                        foreach ($returnList as $retVal) {
                            if($retVal['problemIfn']=='')continue;
                            if($retVal['providerNarrative']=="No problems found.")continue;

                                if($retVal['status']=='A')
                                    $prblm_status='Active';
                                else if($retVal['status']=='I')
                                    $prblm_status='Inactive';
                                $onsetDate='';
                            if($retVal['dateOfOnset']!=''){
                                $filemanDate=$retVal['dateOfOnset'];
                                // $displaydate=$objDss_mhx->MISC_DSICDateConvert($filemanDate);
                                // $displaydate=date('Y-m-d',strtotime($displaydate['external']));
                                // $onsetDate=$displaydate;
                                $onsetDate = $objDss_mhx->filemanToBase($filemanDate);
                            }

                            $last_modified_date='';
                            if($retVal['dateLastModified']!=''){
                                $filemanMDate=$retVal['dateLastModified'];
                                // $displayMdate=$objDss_mhx->MISC_DSICDateConvert($filemanMDate);
                                // $displayMdate=date('Y-m-d',strtotime($displayMdate['external']));
                                // $last_modified_date=$displayMdate;
                                $last_modified_date=$objDss_mhx->filemanToBase($filemanMDate);                
                            }

                            $onsetTime='';
                            //$snomed=$retVal['icdCode'];
                            $snomed=$retVal['snomedConceptCode'];
                            $service_eligibility=0;
                            if($retVal['serviceConnected'] && ($retVal['serviceConnected']=='SC' || strtolower($retVal['serviceConnected'])=='serviceconnected'))
                                $service_eligibility=1;
                            $problem_name= str_replace("'", '', $retVal['providerNarrative']);
                            $problem_ifn=$retVal['problemIfn'];
                            
                            
                            $update=false;
                            $str1Arr=explode('(',$problem_name);
                            $str2Arr=explode('(',$dss_prblm_arr[$problem_ifn]['problem_name']);//echo '11111111111111<br>';
                            if($dss_prblm_arr[$problem_ifn]['problem_name']=='')
                                $str2Arr=explode('(',$dss_prblm_arr[0]['problem_name']);
                            $problem_name_str1=trim($str1Arr[0]);
                            $problem_name_str2=trim($str2Arr[0]);
                            
                            $updateid=$dss_prblm_arr[$problem_ifn]['id'];
                            if( (stripos($problem_name_str1,$problem_name_str2)!==false) || ($problem_ifn==$dss_prblm_arr[$problem_ifn]['dss_prblm_id']) ){
                                $update=true;
                                if($dss_prblm_arr[0]['id'] && $updateid==''){
                                    $updateid=$dss_prblm_arr[0]['id'];
                                }
                            }
                            

                                /*End Check if Problems for the patient with same dssid(ifn) already exists in imwemr*/
                            //if($problem_ifn==$dss_prblm_arr[$problem_ifn]['dss_prblm_id'] && $dss_prblm_arr[$problem_ifn]['dss_last_modified_date'] !== $last_modified_date) {
                            if($update) {
                                    /*Update if Status no same as existing*/
                                $sqPU = " UPDATE `pt_problem_list` SET `problem_name`='".$problem_name."',
                                            `dss_prblm_id`='".$problem_ifn."',
                                            `status`='".$prblm_status."', `dss_last_modified_date`='".$last_modified_date."'
                                            WHERE `id`=".$updateid." " ;
                                
                                    if( imw_query($sqPU) )
                                    {
                                    $sqlLog = "INSERT INTO `pt_problem_list_log` SET
                                                        `problem_id`='".$updateid."',
                                                        `pt_id`=".( (int)$this->patient_id ).",
                                                        `user_id`=".( (int)$_SESSION['authUserID'] ).",
                                                        `problem_name`='".$problem_name."',
                                                        `onset_date`='".$onsetDate."',
                                                        `status`='".$prblm_status."',
                                                        `OnsetTime`='".$onsetTime."',
                                                        `ccda_code`='".$snomed."',
                                                        `service_eligibility`=".$service_eligibility."
                                                        ";
                                        imw_query($sqlLog);
                                    }

                            }elseif( count($dss_prblm_arr[$problem_ifn]) === 0 ){

                                    $sqlPI = "INSERT INTO `pt_problem_list` SET
                                            `pt_id`=".( (int)$this->patient_id ).",
                                            `user_id`=".( (int)$_SESSION['authUserID'] ).",
                                            `problem_name`='".$problem_name."',
                                            `onset_date`='".$onsetDate."',
                                            `prob_type`='Problem',
                                            `status`='".$prblm_status."',
                                            `OnsetTime`='".$onsetTime."',
                                            `ccda_code`='".$snomed."',
                                            `dss_prblm_id`='".$problem_ifn."',
                                            `service_eligibility`=".$service_eligibility.",
                                            `dss_last_modified_date`='".$last_modified_date."'

                                            ";

                                    if( imw_query($sqlPI) )
                                    {
                                        $problemId = imw_insert_id();
                                        $sqlLog = "INSERT INTO `pt_problem_list_log` SET
                                                            `problem_id`='".$problemId."',
                                                            `pt_id`=".( (int)$this->patient_id ).",
                                                            `user_id`=".( (int)$_SESSION['authUserID'] ).",
                                                            `problem_name`='".$problem_name."',
                                                            `onset_date`='".$onsetDate."',
                                                            `status`='".$prblm_status."',
                                                            `OnsetTime`='".$onsetTime."',
                                                            `ccda_code`='".$snomed."',
                                                            `service_eligibility`=".$service_eligibility."
                                                            ";
                                        imw_query($sqlLog);
                                    }

                            }
                            if(isset($dss_prblm_arr[$problem_ifn])){
                                unset($dss_prblm_arr[$problem_ifn]);
                            }else{
                                if(isset($dss_prblm_arr[$problem_ifn]['id']))
                                    $dss_prblmArr[]=$dss_prblm_arr[$problem_ifn]['id'];
                            }
                            
                        }

                        if(count($dss_prblm_arr) > 0) {
                        	foreach ($dss_prblm_arr as $key => $prb) {
                        		$dss_prblmArr[]=$prb['id'];
                        	}
                        }

                        //Delete existing problem from imwemr which are not in dss response.
                        $deleteArray=array();//pre($dss_prblmArr);die;
                        if(empty($dss_prblmArr)==false) {
                            $problemsToBeDeleted = implode(',', $dss_prblmArr);
                            $deleteArray['del_ids'] = $problemsToBeDeleted; 
                            $deleteArray['dssload'] = 'dssload';
                            $this->delete_pro_list($deleteArray);
                        }

                    }
                
                }
            }
            
        }
        
        
    public function upload_problem_to_vista($arrUp,$action) {
        $arrRet=array();
        
        $sqlrs=imw_query('select External_MRN_5 from patient_data where id='.$arrUp['pt_id'].' ');
        
        $dss_row=imw_fetch_assoc($sqlrs);
        $dss_dfn=$dss_row['External_MRN_5'];
        
        if(empty($dss_dfn)==false && $dss_dfn!=NULL && $dss_dfn!='') {
            try
            {
                include_once( $GLOBALS['srcdir'].'/dss_api/dss_medical_hx.php' );
                include_once( $GLOBALS['srcdir'].'/dss_api/dss_enc_visit_notes.php' );
                $objDss_mhx = new Dss_medical_hx();
                $objDss_enc = new Dss_enc_visit_notes();

                $patient=$this->get_patient_name($arrUp['pt_id']);
                $patientArr=explode('-',$patient);
                $patientStr=$dss_dfn.'^'.$patientArr[0];

                // $onset_date=date('Ymd',strtotime($arrUp['onset_date']));
                // $onset_date_format = $objDss_mhx->MISC_DSICDateConvert($onset_date);
                // if(!isset($onset_date_format['fileman']) && $onset_date_format['fileman']=='')
                //     throw new Exception ('Error : Unable to convert onset date to fileman format.');

                // $onset_fileman = $onset_date_format['fileman'];
                // $onset_external = str_replace(',', '', $onset_date_format['external']);

                $onset_fileman = $objDss_mhx->convertToFileman($arrUp['onset_date']);
                $onset_external = date('M d Y', strtotime($arrUp['onset_date']));

                // $currentDate=date('Ymd');
                // $currentDate_format = $objDss_mhx->MISC_DSICDateConvert($currentDate);
                // if(!isset($currentDate_format['fileman']) && $currentDate_format['fileman']=='')
                //     throw new Exception ('Error : Unable to convert current date to fileman format.');

                // $current_fileman = $currentDate_format['fileman'];
                // $current_external = str_replace(',', '', $currentDate_format['external']);

                $current_fileman = $objDss_mhx->convertToFileman(date('Y-m-d'));
                $current_external = date('M d Y');

                $status="A^ACTIVE";
                if(strtolower($arrUp['status'])=='inactive') {
                    $status="I^INACTIVE";
                }

                $comment=($arrUp["comments"] && $arrUp["comments"]!='')?array($arrUp["comments"]):array("^");
                $comment_count=count($comment);

                $lexiconInfo="^".$arrUp['problem_name'];
                $dss_prblm_id="";
                if($arrUp['dss_prblm_id']!='' && $arrUp['dss_prblm_id']!=0) {
                    $dss_prblm_id=$arrUp['dss_prblm_id'];
                    $lexiconInfo=$arrUp['dss_prblm_id']."^".$arrUp['problem_name'];
                }
                $dss_loginDUZ='';
                if(isset($_SESSION['dss_loginDUZ']) && $_SESSION['dss_loginDUZ']!='')
                    $dss_loginDUZ=$_SESSION['dss_loginDUZ'];

                $dateLastModified=$current_fileman."^".$current_external;
                if($action=='update') {
                    $dateLastModified="0^";
                }

                // Service Connected Eligibility
                $str_sc = $arrUp['service_eligibility'];
            	$str_sc = ltrim($str_sc, '\'');
            	$str_sc = rtrim($str_sc, '\'');
                $service_eligibility = unserialize($str_sc);

                // pre($service_eligibility,1);

                $service_connected_opts = array(
					'sc' => "0^NO",
					// 'cv' => "0^NO",
					'ao' => "0^NO",
					'ir' => "0^NO",
					// 'swac' => "0^NO",
					// 'shd' => "0^NO",
					// 'ec' => "0^NO",
					'mst' => "0^NO",
					'hnc' => "0^NO"
				);

                if(!empty($service_eligibility) && sizeof($service_eligibility) > 0){
                	foreach ($service_eligibility as $key => $value) {
                		if(array_key_exists(strtolower($key), $service_connected_opts)) {
                			$service_connected_opts[strtolower($key)] = $value."^YES";
                		}
                	}
                }
                // pre($service_connected_opts,1);

                try{
                	$problem_name = $arrUp['problem_name'];
					$ICD10 = '';
					if (stripos($problem_name, '(ICD-10-CM') !== false) {
					    $pr = explode('(ICD-10-CM', $problem_name);
					    $ICD10 = substr(trim($pr[1]), 0, -1);
					}
	                $lexIEN = $objDss_enc->processICD10($ICD10, $current_fileman);
                } catch(Exception $e) {
            	 	echo '<script>top.fAlert("'.$e->getMessage().'");</script>';
                	exit;
                }

                $params=array();
                $params['patient']=$patientStr;
                $params['provider']=$dss_loginDUZ;
                $params['vamc']="^";
                $params['diagnosis']=$lexIEN."^".$ICD10; //"473.9"; //
                $params['dateLastModified']=$dateLastModified;
                $params['diagDesc']="^".$problem_name;
                $params['currentDate']=$current_fileman."^".$current_external;
                $params['activeStatus']=$status;
                $params['onsetDate']=$onset_fileman."^".$onset_external;
                $params['lexiconInfo']=$lexiconInfo;
                $params['condition']="P";
                $params['currentUser']=$dss_loginDUZ."^";
                $params['recorder']="^";
                $params['treater']="^";
                $params['service']="^";
                $params['dateResolved']="^";
                $params['treatmentClinic']="^";
                $params['recordedDate']=$current_fileman."^".$current_external;
                $params['pge']="0^NO";
                $params['priority']="A^ACUTE";
                $params['comments']=$comment;
                $params['numComments']="$comment_count";
                $params = array_merge($params, $service_connected_opts); // adding service connected options
                // pre($params,1);

                if($action=='add') {
                    //upload Add problem to vista
                    $arrRet = $objDss_mhx->updatePatientProblemList($params);
                    if(isset($arrRet[0]['code']) && $arrRet[0]['code']=='-1')
                        throw new Exception ('Error add : '.$arrRet[0]['desc']);
                } else if($action=='update' && $dss_prblm_id!='' && $dss_prblm_id >0) {
                    $params['internalEntryNumber']=$dss_prblm_id;
                    //upload Edit problem to vista
                    $arrRet = $objDss_mhx->editPatientProblemList($params);
                    if(isset($arrRet[0]['code']) && $arrRet[0]['code']=='-1')
                        throw new Exception ('Error update : '.$arrRet[0]['desc']);
                }
           
            } catch (Exception $e) {
                echo '<script>top.fAlert("'.$e->getMessage().'","", \'top.fmain.location.href="../Medical_history/index.php?showpage=problem_list"\' );</script>';
                die;
                //$arrRet['dss_error'][]=$e->getMessage();
            }
        }
        return $arrRet;
    }
    
    
    function delete_problem_from_vista($record_ids,$dss_dfn) {
        include_once( $GLOBALS['srcdir'].'/dss_api/dss_medical_hx.php' );
        $objDss_mhx = new Dss_medical_hx();
        
        $arrRet=array();
        $q = "select dss_prblm_id from pt_problem_list 
				WHERE id IN ($record_ids) 
				AND pt_id = '".$this->patient_id."' ";
        
		$res=imw_query($q)or die(imw_error());
        
        $dss_loginDUZ='';
        if(isset($_SESSION['dss_loginDUZ']) && $_SESSION['dss_loginDUZ']!='')
            $dss_loginDUZ=$_SESSION['dss_loginDUZ'];
        $dss_prblm_ids=array();
        while($row=imw_fetch_assoc($res)) {
            $dss_prblm_ids[]=$row['dss_prblm_id'];
        }
        
        if(empty($dss_prblm_ids)==false) {
            foreach($dss_prblm_ids as $dss_prblm_id) {
                $params=array();
                $params['internalEntryNumber']=$dss_prblm_id;
                $params['providerId']=$dss_loginDUZ;
                $params['vamc']="500";
                $params['reason']="no longer problem";

                try
                {
                    $arrRet[] = $objDss_mhx->deletePatientProblemList($params);
                } catch(Exception $e){
                    $arrRet['dss_error'][]=$e->getMessage();
                }


            }
        }

        return $arrRet;       
    }
    
    
}
?>