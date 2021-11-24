<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
*/
include_once($GLOBALS['srcdir']."/classes/SaveFile.php");
include_once $GLOBALS['srcdir'].'/classes/common_function.php';
include_once $GLOBALS['srcdir'].'/classes/cls_common_function.php';
include_once $GLOBALS['srcdir'].'/classes/work_view/ChartAP.php';
include_once $GLOBALS['srcdir'].'/classes/AES.class.php';

class CPR extends CLSCommonFunction
{
	//Public variabels
	public $patient_id = '';
	public $call_from = '';
	public $facesheetTemplateExist = '';
	public $data_file_path = '';
	
	public function __construct($pid,$call_from=''){
	    parent::__construct();
		$this->call_from = $call_from;
		$this->patient_id = $pid;
		if($call_from == 'wv'){
			$this->arrTestNms = array("VF","HRT","OCT","GDX","Pachy","IVFA","Fundus","External/Anterior","Topography","CellCount","Laboratories","VF_GL","OCT_RNFL","Template_Type","Other","IOL Master");
		}else{
			$this->arrTestNms = array("VF","HRT","OCT","GDX","Pachy","IVFA","Fundus","External/Anterior","Topography","CellCount","Laboratories","IOL Master");
		}
        $this->cust_test_names=$this->getcustom_test_names();
        if(!empty($this->cust_test_names)) {
           	$mergedArray = array_merge($this->arrTestNms,$this->cust_test_names);
            $this->arrTestNms = array_unique($mergedArray);
        }
        
		$this->data_file_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/";
		$this->arrValidCNPhy = $GLOBALS['arrValidCNPhy'];
		$this->arrValidCNTech = $GLOBALS['arrValidCNTech'];
		$this->check_facesheet_temp();
	}
	
    public function getcustom_test_names() {
        $arr_all_test = false;
		$q_where = " AND status=1 AND test_table='test_custom_patient' ";
		$q_tests = "SELECT * FROM tests_name WHERE del_status=0 ".$q_where." ORDER BY temp_name";
		$res_tests = imw_query($q_tests);
		if($res_tests && imw_num_rows($res_tests)>0){
			$arr_all_test = array();
			while($rs_test=imw_fetch_assoc($res_tests)){
				$arr_all_test[] = $rs_test['temp_name'];
			}
		}
		return $arr_all_test;
    }
    
	//Check if facesheet temp. exist or not
	public function check_facesheet_temp(){
		$ptDocTempQry = "select pt_docs_template_id from pt_docs_template 
						 where pt_docs_template_status = '0' and pt_docs_template_enable_facesheet = 'yes' ORDER BY pt_docs_template_id DESC LIMIT 0,1";
		$ptDocTempRes = imw_query($ptDocTempQry) or die(imw_error());
		if(imw_num_rows($ptDocTempRes)>0) {
			$this->facesheetTemplateExist	= "yes";
		}
	}
	
	//Returns DOS based on form id
	public function get_form_dos($return_type = 'opt'){
		$val_array = array();
		$val_strn = '';
		$sql = "SELECT c1.id as form_id, c1.date_of_service, c1.create_dt
				FROM chart_master_table c1 ".
				"WHERE c1.patient_id = '".$this->patient_id."' ".
				"ORDER BY c1.date_of_service DESC, c1.id DESC ";
		$getFORMIDQry =imw_query($sql);
		if($getFORMIDQry){
			if(imw_num_rows($getFORMIDQry)>0){
				$counterInc=1;
				while($getFormRow = imw_fetch_array($getFORMIDQry)){
					$form_idTemp = $getFormRow['form_id'];
					$dos = $getFormRow["date_of_service"];
					if(empty($dos)||($dos=="0000-00-00")){
						$dos = $getFormRow["create_dt"];
					}
					$date_of_service = date("m-d-Y", strtotime($dos));	
					$date_of_service = get_date_format(date("Y-m-d", strtotime($dos)));	
					$checked="";
					if($counterInc==1){
						$checked="selected";
					}
					$val_array[$form_idTemp] = $date_of_service;
					$val_strn .= "<option  $checked value='".$form_idTemp."'> ".$date_of_service." </option>";
					$counterInc++;
				}
			}
		}
		
		if($return_type == 'opt'){
			return $val_strn;
		}else{
			return $val_array;
		}
	}
	
	//Returns patient dos dropdown or array
	public function get_pt_dos($return_type = 'array'){
		$opt_arr = array();
		$opt_str = '<option value="all">All</option>';
		$getFORMIDQry = imw_query("SELECT id as form_id,date_of_service FROM chart_master_table WHERE patient_id = '".$this->patient_id."' order by id desc");
		if(imw_num_rows($getFORMIDQry)>0){
			while($getFormRow = imw_fetch_array($getFORMIDQry)){
				$form_idTemp = $getFormRow['form_id'];	
				$date_of_service = date("m-d-Y", strtotime($getFormRow["date_of_service"]));	
				$checked="";
				$opt_arr[trim($date_of_service)] = $date_of_service; 
				$opt_str .= "<option  value='".trim($date_of_service)."'> ".$date_of_service." </option>";
			}
		}
		if($return_type == 'array'){
			return $opt_arr;
		}else{
			return $opt_str;
		}
	}
	
	//Returns Disclosed dos data
	public function get_disclosed_dos($return_type,$cpr=""){
		$phyName_dropdown = $sel = '';
		$phyName_arr = array();
		$phyTechNurseType= @implode(",",$this->arrValidCNPhy);
		$phyTechNurseType.= @implode(",",$this->arrValidCNTech);
		$t_provider = imw_query("select id,fname,lname,mname from users where user_type in($phyTechNurseType) and delete_status = 0 order by lname, fname ");
		while($vrst_provider = imw_fetch_array($t_provider)){
			$phyName_drop="";
			$phyName_drop = core_name_format($vrst_provider['lname'], $vrst_provider['fname'], $vrst_provider['mname']);
			$phyName_arr[$vrst_provider['id']] = trim(ucwords($phyName_drop));				
		}

		if(count($phyName_arr) > 0){
			$phyName_arr = array_unique(array_filter($phyName_arr));
			foreach($phyName_arr as $id => $name)
			{ 
				if(!empty($cpr))
				{	
				 $sel = ($_SESSION['authId'] == $id)  ? 'selected' : '';
				}
				$phyName_dropdown .="<option $sel value=\"".$id."\">".$name."</option>"; 
			}
		}

		return $returnVal = ($return_type == 'array') ? $phyName_arr : $phyName_dropdown;
	}
	
     //Returns current test array or dropdowns
    public function get_custom_test_arr($pId, $test, $disMsg = "none", $return_type = 'dropdown', $col) {
        $arr_all_test = false;
		$q_where = " AND status=1 AND test_table='test_custom_patient' AND temp_name='".$test."' ";
		$q_tests = "SELECT id FROM tests_name WHERE del_status=0 ".$q_where." ORDER BY temp_name";
		$res_tests = imw_query($q_tests);
		if($res_tests && imw_num_rows($res_tests)>0){
			$rs_test=imw_fetch_assoc($res_tests);
			$rs_test_id = $rs_test['id'];
		}

        include_once $GLOBALS['srcdir'] . '/classes/class.tests.php';
        $obj_tests = new Tests();
        $test_data = $obj_tests->get_patient_saved_tests($this->patient_id);

        $val_array = array();
        $return_val = '';
        $test_row = $test_data[$rs_test_id];

        $str = "";
        if (isset($test_row['test_rs']) && empty($test_row['test_rs']) == false && !empty($test_row['test_rs'])) {
            foreach ($test_row['test_rs'] as $row) {
                $dt = $row["dt"];
                $tId = $row["tId"];
                $prfBy = $row["prfBy"];
                $phy = $row["phy"];

                $str .= "<option value='$tId'>" . $dt . "</option>";

                $val_array[$dt] = $tId;
            }
        }

        $test = $test_row['temp_name'];
        $testNameID = str_replace(array('-','/',' '), '_', $test_row['temp_name']);

        if ($return_type == 'dropdown') {
            if ($str != '') {
                $return_val = "<div class='col-sm-" . $col . "'><label>" . $test . "</label><select  name='printTestRadio" . $testNameID . "[]' id='printTestRadio" . $testNameID . "' class='selectpicker' data-width='100%' data-size='5' multiple data-title='All' >" . $str . "</select></div>";
            }
        } else {
            $return_val = $val_array;
        }

        return $return_val;
    }
    
	//Returns test data
	public function get_test_data($return_type,$col = 2){
		$counter=1;	//pre($this->cust_test_names);pre($this->arrTestNms);
		foreach($this->arrTestNms as $key => $val){
			$test_data = '';
            if(!in_array($val,$this->cust_test_names)) {
			$test_data = $this->get_test_arr($this->patient_id, $val,$disMsg,$return_type,$col);
            } else {
                $test_data = $this->get_custom_test_arr($this->patient_id, $val, $disMsg, $return_type, $col);
            }
			if(empty($test_data) === false){
				if($return_type == 'dropdown'){
					$return_val .= $test_data;
				}else{
					$return_val[$val] = $test_data;
				}
			}
		}
		return $return_val;
	}
	
	//Returns current test array or dropdowns
	public function get_test_arr($pId, $test,$disMsg="none",$return_type = 'dropdown',$col){
		$arr_test_name=array();
		$qry_tests_name="SELECT id,temp_name FROM tests_name";
		$res_tests_name=imw_query($qry_tests_name);
		while($row_tests_name=imw_fetch_assoc($res_tests_name)){
			$arr_test_name[$row_tests_name['id']]=$row_tests_name['temp_name'];
		}
			
			$val_array = array();
			$str = "";	
			$sql = "";
			switch($test){
				case "VF":
					$sql = "SELECT vf_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt,
							performedBy AS prfBy, phyName as phy,formId as form_id
							FROM vf WHERE patientId='".$pId."' AND del_status=0 
							ORDER BY examDate DESC, examTime DESC, vf_id DESC " ;		
				break;
				case "VF_GL":
					$sql = "SELECT vf_gl_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt,
							performedBy AS prfBy, phyName as phy,formId as form_id
							FROM vf_gl WHERE patientId ='".$pId."' AND del_status=0  
							ORDER BY examDate DESC, examTime DESC, vf_gl_id DESC " ;		
				break;
				case "HRT":
					$sql = "SELECT nfa_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt, 
							performBy AS prfBy, phyName as phy,form_id
							FROM nfa WHERE patient_id='".$pId."' AND del_status=0  
							ORDER BY examDate DESC, examTime DESC, nfa_id DESC " ;			
				break;

				case "OCT":
					$sql = "SELECT oct_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt, 
							performBy AS prfBy, phyName as phy,form_id
							FROM oct WHERE patient_id='".$pId."' AND del_status=0 
							ORDER BY examDate DESC, examTime DESC, oct_id DESC " ;		
				break;
				case "OCT_RNFL":
					$sql = "SELECT oct_rnfl_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt, 
							performBy AS prfBy, phyName as phy,form_id
							FROM oct_rnfl WHERE patient_id='".$pId." AND del_status=0' 
							ORDER BY examDate DESC, examTime DESC, oct_rnfl_id DESC " ;		
				break;
				case "GDX":
					$sql = "SELECT gdx_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt, 
							performBy AS prfBy, phyName as phy,form_id
							FROM test_gdx WHERE patient_id='".$pId."' AND del_status=0  
							ORDER BY examDate DESC, examTime DESC, gdx_id DESC " ;
				break;

				case "Pachy":
					$sql = "SELECT pachy_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt, 
							performedBy AS prfBy, phyName as phy,formId as form_id
							FROM pachy WHERE patientId='".$pId."' AND del_status=0  
							ORDER BY examDate DESC, examTime DESC, pachy_id DESC " ;		
				break;

				case "IVFA":
					$sql = "SELECT vf_id AS tId, DATE_FORMAT(exam_date, '".get_sql_date_format()."') AS dt,
							performed_by AS prfBy, phy as phy,form_id
							FROM ivfa WHERE patient_id='".$pId."' AND del_status=0   
							ORDER BY exam_date DESC, examTime DESC, vf_id DESC " ;		
				break;

				case "Fundus":
					$sql = "SELECT disc_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt,
							performedBy AS prfBy, phyName as phy,formId as form_id
							FROM disc WHERE patientId='".$pId."' AND del_status=0   
							ORDER BY examDate DESC, examTime DESC, disc_id DESC " ;		
				break;

				case "External/Anterior":
					$sql = "SELECT disc_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt,
							performedBy AS prfBy, phyName as phy,formId as form_id
							FROM disc_external WHERE patientId='".$pId."' AND del_status=0  
							ORDER BY examDate DESC, examTime DESC, disc_id DESC " ;		
				break;

				case "Topography":
					$sql = "SELECT topo_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt,
							performedBy AS prfBy, phyName as phy,formId as form_id
							FROM topography WHERE patientId='".$pId."' AND del_status=0  
							ORDER BY examDate DESC, examTime DESC, topo_id DESC " ;				
				break;

				case "Ophthalmoscopy":
					$sql = "SELECT ophtha_id AS tId, DATE_FORMAT(exam_date, '".get_sql_date_format()."') AS dt, 
							performedBy AS prfBy, phyName as phy,formId as form_id
							FROM ophtha WHERE patient_id='".$pId."' 
							ORDER BY exam_date DESC, examTime DESC, ophtha_id DESC " ;			
				break;

				case "Other":
					$sql = "SELECT test_other_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt, 
							performedBy AS prfBy, phyName as phy,formId as form_id, test_other AS subcat
							FROM test_other WHERE patientId='".$pId."' AND test_template_id='0' AND del_status=0 
							ORDER BY test_other ASC, examDate DESC, examTime DESC, test_other_id DESC " ;	
				break;
				
				case "Template_Type":
					$sql = "SELECT test_other_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt, 
							performedBy AS prfBy, phyName as phy,formId as form_id, test_other AS subcat,test_template_id
							FROM test_other WHERE patientId='".$pId."'  AND test_template_id!='0' AND del_status=0 
							ORDER BY test_other ASC, examDate DESC, examTime DESC, test_other_id DESC " ;	
				break;
				case "Laboratories":
					$sql = "SELECT test_labs_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt, 
							performedBy AS prfBy, phyName as phy,formId as form_id, test_labs AS subcat
							FROM test_labs WHERE patientId='".$pId."' AND del_status=0 
							ORDER BY test_labs ASC, examDate DESC, examTime DESC, test_labs_id DESC " ;	
				break;

				case "A/Scan":
					$sql = "SELECT surgical_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt, 
							performedByOD AS prfBy, signedById as phy,form_id
							FROM surgical_tbl WHERE patient_id ='".$pId."' AND del_status=0 
							ORDER BY examDate DESC, examTime DESC, surgical_id DESC " ;	
				break;
				
				case "B-Scan":
					$sql = "SELECT test_bscan_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt, 
							performedBy AS prfBy, phyName as phy,formId as form_id
							FROM test_bscan WHERE patientId='".$pId."' AND del_status=0 
							ORDER BY examDate DESC, examTime DESC, test_bscan_id DESC " ;	
				break;

				case "CellCount":
					$sql = "SELECT test_cellcnt_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt, 
							performedBy AS prfBy, phyName as phy,formId as form_id
							FROM test_cellcnt WHERE patientId='".$pId."' AND del_status=0 
							ORDER BY examDate DESC, examTime DESC, test_cellcnt_id DESC " ;	
				break;
				case "IOL Master":
					$sql = "SELECT iol_master_id AS tId, DATE_FORMAT(examDate, '".get_sql_date_format()."') AS dt, 
							performedByOD AS prfBy, signedById as phy,form_id as form_id
							FROM iol_master_tbl WHERE patient_id='".$pId."' AND del_status=0 
							ORDER BY examDate DESC, examTime DESC, iol_master_id DESC " ;	
				break;

			}
			$qry = imw_query($sql);
			if(imw_num_rows($qry) > 0){
				while($row = imw_fetch_array($qry)){
					$dt = $row["dt"];
					$tId = $row["tId"];	
					$prfBy = $row["prfBy"];
					$phy = $row["phy"];	
					
					if($test == "Other"){
						$curCat = $row["subcat"];
						if(!isset($prevCat)){
							$prevCat = "";
							$arrAllOther = array();
						}
						if(strtolower($curCat) != strtolower($prevCat)){
							//Add Cat Name if prev is not empty
							if(!empty($prevCat)){
								$arrAllOther[$prevCat] = $str;
							}
							$prevCat = $curCat;
						}
					}
					$val_array[$dt] = $tId;	
					
					
					if($test == "Template_Type"){
						$abcArr[$row["test_template_id"]][$tId]=$dt;
					}else{
						$str .= "<option value='$tId'>".$dt."</option>";
					}
				}
				
				if($test == "Template_Type"){
					$str =$valOpt="";
					$testNameID=$test;
					foreach($abcArr as $label=>$dos_val_arr){
						$valOpt.="<optgroup label='".$arr_test_name[$label]."' data-width='100%'>";
						foreach($dos_val_arr as $tId=> $dos_val){
							$valOpt.="<option value='$tId'>".$dos_val.$test."</option>";	
						}
						$valOpt.="</optgroup>";
					}
						$str = $valOpt;
				
				}else if($test != "Other"){

					$testNameID=($test=="External/Anterior")?"External_Anterior":$test;
					if($test=="VF_GL"){$test="VF-GL";}else if($test=="OCT_RNFL"){$test="OCT-RNFL";}
					//Add Test Name 	
					$str = $str;
				}else{
					$testNameID=$test;
					//Add Last record
					$arrAllOther[$prevCat] = $str;			
					
					$str = "";
					foreach($arrAllOther as $key => $val){
						$key = ucfirst(strtolower($key));
						$str .= "<table cellpadding='0' cellspacing='0' width='100%'>
									<tr>
										 <td class=\"txt_13b\" valign=\"top\">".$key."</td></tr>
									<tr>
										<td>".$val."</td>
									</tr>
								</table>";
					}
				}
				
				if($test == 'Template_Type'){
					$test = 'Other Tests';
				}
				
				if($return_type == 'dropdown'){
					$return_val = "<div class='col-sm-".$col." tests'><label>".$test."</label><select  name='printTestRadio".$testNameID."[]' id='printTestRadio".$testNameID."' class='selectpicker' data-width='100%' data-size='5' multiple data-title='Nothing selected' >".$str."</select></div>";	
				}else{
					$return_val = $val_array;
				}
			}
		return $return_val;
	}
	
	
	//Returns Typeahead aray for Reff. Phy.
	public function get_reff_phy_typeahead($return_type = 'array'){
		$stringAllPhy = "";
		$stringAllPhyId = "";
		$refPhyXMLFileExits = false;
		
		$refPhyXMLFile = $this->data_file_path."xml/Referring_Physicians.xml";
		if(file_exists($refPhyXMLFile)){
			$refPhyXMLFileExits = true;
		}
		else{
			$this->create_ref_phy_main_xml();	
			if(file_exists($refPhyXMLFile)){
				$refPhyXMLFileExits = true;	
			}	
		}
		$refPhyFaxArr= array();
		if($refPhyXMLFileExits == true){
			$values = array();
			$XML = file_get_contents($refPhyXMLFile);
			$values = $this->xml_to_array($XML);		
			$refPhyAll = array();
			$str_faxRefPhy = ''; $str_faxRefPhyId='';
			$str_arrRefIdNameFax = '';
			foreach($values as $key => $val){	
				if(($val["tag"] =="refPhyInfo") && ($val["type"]=="complete") && ($val["level"]=="2") ){		
					$refPhyFname = str_replace("'","",stripslashes($val["attributes"]["refphyFName"]));	
					$refPhyLname = str_replace("'","",stripslashes($val["attributes"]["refphyLName"]));						
					$refPhyAll_name['name'] = "".$refPhyLname.', '.$refPhyFname."";
					$refPhyAll_name['id'] = "".$val["attributes"]["refphyId"]."";
					$refPhyAll[] = $refPhyAll_name;
					$refPhyIdAll[]="".$val["attributes"]["refphyId"]."";
					$refPhyIdFax=$val["attributes"]["refphyId"];
					$refPhyFax=$val["attributes"]["refFax"];
					$refPhyFaxArr[$refPhyIdFax."@@".$refPhyFax]=$refPhyLname.', '.$refPhyFname;
					$str_faxRefPhyId = $refPhyIdFax."@@".$refPhyFax;
					$str_faxRefPhy = $refPhyLname.', '.$refPhyFname;
					$arrRefIdNameFax_val[$refPhyIdFax] = $str_faxRefPhy.'@@'.$val["attributes"]["refFax"];
				}
			}
			array_unique($refPhyAll);
			if(count($refPhyAll)>0){
				$stringAllPhy=@implode(',',$refPhyAll);
				$stringAllPhyId=@implode(',',$refPhyIdAll);
				$stringAllPhyFax=@implode(',',$arrRefIdNameFax_val);
				if($return_type == 'array'){
					$return_arr['stringAllPhy'] = $refPhyAll;
					$return_arr['stringAllPhyId'] = $refPhyIdAll;
					$return_arr['stringAllPhyFax'] = $arrRefIdNameFax_val;
				}else{
					$return_arr['stringAllPhy'] = $stringAllPhy;
					$return_arr['stringAllPhyId'] = $stringAllPhyId;
					$return_arr['stringAllPhyFax'] = $stringAllPhyFax;
				}
				return $return_arr;
			}
		}
	}
	
	//Returns disclosed data
	public function get_disclosed_data(){
		$return_arr = array();
		$selDisclosedQ = imw_query("SELECT DATE_FORMAT(dateRequested,'".get_sql_date_format('','y','/')." %h:%i %p') as dateRequestedTEMP, disclosedBy, disclosedTo,disclosed,formid, disclosedToSpecialty, disclosedReason,CONCAT(u.lname,', ',u.fname,' ',u.mname) AS discBy FROM `disclosed_details` dd JOIN users u ON (u.id=dd.disclosedBy) where patient_id='".$this->patient_id."' group by dateRequested order by dateRequested desc");
		if(imw_num_rows($selDisclosedQ) > 0){
			while($rowDisclosed=imw_fetch_array($selDisclosedQ)){
				$disc_form_ids = $rowDisclosed["formid"];
				$get_dos_query = "SELECT DATE_FORMAT(date_of_service,'".get_sql_date_format('','y','/')."') as d FROM chart_master_table WHERE id IN ($disc_form_ids) AND patient_id = '".$this->patient_id."' ORDER BY date_of_service DESC";
				$disc_dos_res = imw_query($get_dos_query);
				$str_disclosed = '';
				while($disc_dos_rs = imw_fetch_array($disc_dos_res)){
					$str_disclosed .= '<b>'.$disc_dos_rs['d'].'</b><br>'.$rowDisclosed["disclosed"].'<br>';
				}
				if(strlen($str_disclosed)>3){$str_disclosed = substr($str_disclosed,0,-4);}
				
				$return_array['Date Requested'] = $rowDisclosed["dateRequestedTEMP"];
				$return_array['Disclosed By'] = $rowDisclosed["discBy"];
				$return_array['Disclosed To'] = $rowDisclosed["disclosedTo"];
				$return_array['Disclosed'] = $str_disclosed;
				$return_array['Specialty'] = $rowDisclosed["disclosedToSpecialty"];
				$return_array['Reason'] = $rowDisclosed["disclosedReason"];
				$return_arr[] = $return_array;
			}	
		}
		return $return_arr;	
	}	
	
	
	//Creates and provide names of CCD XML files
	public function get_ccd_xml($request){
		if($request['pId']){
			$laikaXML = "";
			$XMLPartMaritalStatus = "";
			$XMLPartSupportRelationGUARD = "";
			$XMLPartPerformerPrimaryProvider = "";
			$XMLPartPerformerReferringPhysician = "";
			$XMLPartComponent = "";
			$XMLPartAllergies = "";
			$XMLPartInsurance = "";		
			$XMLPartProblemList = "";	
			$XMLPartLabs = "";	
			$rqElectronicDOSCCD = "";  
			$pid = $request['pId'];
			$uId = $_SESSION['authId'];
			$rqElectronicDOSCCD = trim($request['electronicDOSCCD']);
			$elDOSCCD = "";	
			if(empty($rqElectronicDOSCCD) == false){
				if($rqElectronicDOSCCD != "all"){			
					list($elDOSCCDMonth,$elDOSCCDDay,$elDOSCCDYear) = explode("-",$rqElectronicDOSCCD);
					$elDOSCCD = $elDOSCCDYear."-".$elDOSCCDMonth."-".$elDOSCCDDay;			
				}
			}	
			$today = date("Ymd");
			
			//START CODE TO SET LOG OF ELECTRONIC RECORDS
			if(!$form_id) { $form_id='';}
			if(!$databaseDateOfService) { $databaseDateOfService='';}
			$this->setLogOfPtPrintedRec($pid,$form_id,$_SESSION['authId'],$databaseDateOfService,'iDoc','CCDA');
			//END CODE TO SET LOG OF ELECTRONIC RECORDS
			
			#START----------------geting current user detail
			$getUserDetail = "select fname as userFName,lname as userLName,default_facility from users where id = '$uId'";
			$rsGetUserDetail = imw_query($getUserDetail);
			if(!$rsGetUserDetail){
				echo ("Error : ".imw_error());		
			}
			else{
				if(imw_num_rows($rsGetUserDetail)){
					$rowGetUserDetail = imw_fetch_assoc($rsGetUserDetail);
					$defaultFacility = 0;
					$qryGetFacDetail = "";
					$userFName = $rowGetUserDetail['userFName'];
					$userLName = $rowGetUserDetail['userLName'];
					$defaultFacility = (int)$rowGetUserDetail['default_facility'];
					if($defaultFacility > 0){
						$qryGetFacDetail = "select name,phone,street,city,state,postal_code from facility where id = '".$defaultFacility."'";
					}
					else{
						$qryGetFacDetail = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
					}
					if(empty($qryGetFacDetail) == false){
						$rsGetProFacDetail = imw_query($qryGetFacDetail);
						if($rsGetProFacDetail){
							if(imw_num_rows($rsGetProFacDetail) > 0){
								$rowGetProFacDetail = imw_fetch_array($rsGetProFacDetail);
								$facProName = $rowGetProFacDetail['name'];
								$facProPhone = $rowGetProFacDetail['phone'];
								$facProStreet = $rowGetProFacDetail['street'];
								$facProCity = $rowGetProFacDetail['city'];
								$facProState = $rowGetProFacDetail['state'];
								$facProPostalCode = $rowGetProFacDetail['postal_code'];				
							}
						}
					}
				}
			}
			//extract(imw_fetch_assoc($rsGetUserDetail));
			#END----------------geting current user detail
			
			#START----------------geting Patient detail
			$getPatientDetail = "select patient_data.*,users.fname as ptProviderFName,users.mname as ptProviderMName,users.lname as ptProviderLName,
			refferphysician.Title as ptRefferPhyTitle,refferphysician.FirstName as ptRefferPhyFName,refferphysician.MiddleName as ptRefferPhyMName,
			refferphysician.LastName as ptRefferPhyLName,refferphysician.physician_phone as ptRefferPhyPhone
			from patient_data LEFT JOIN users on users.id = patient_data.providerID
			LEFT JOIN refferphysician ON refferphysician.physician_Reffer_id = patient_data.primary_care_id 
			where patient_data.id = '$pid'";
			
			$rsGetPatientDetail = imw_query($getPatientDetail);
			if(!$rsGetPatientDetail){
				echo ("Error : ".imw_error());
			}
			$resultPatientDetail = imw_fetch_assoc($rsGetPatientDetail);
			$AESPatientID = $AESPatientDOB = $AESPatientDOBMonth = $AESPatientDOBDay = $AESPatientFName = $AESPatientLName = "";
			$arrAESPatientDOB = array();
			$AESPatientID = $pid;
			$AESPatientDOB = $resultPatientDetail['DOB'];
			/*$arrAESPatientDOB = explode("-",$AESPatientDOB);
			$AESPatientDOBMonth = $arrAESPatientDOB[1];
			$AESPatientDOBDay = $arrAESPatientDOB[2];
			*/
			$AESPatientFName = $resultPatientDetail['fname'];
			$AESPatientLName = $resultPatientDetail['lname'];
			$patientRegistrationDate = $resultPatientDetail['date'];
			if($patientRegistrationDate == "0000-00-00 00:00:00"){
				$patientRegistrationDate = "";
			}else{
				$tmp_date = $patientRegistrationDate;
				list($year, $month, $day) = split('-',$tmp_date);		
				$day1 = explode(" ",$day);												
				//$patientRegistrationDate = $month."-".$day1[0]."-".$year;			
				$patientRegistrationDate = $year.$month.$day1[0];			
			}
				
			$ptDefaultFacility = 0;
			$qryGetFacDetail = "";
			$ptDefaultFacility = (int)$resultPatientDetail['default_facility'];
			if($ptDefaultFacility > 0){
				$qryGetFacDetail = "select name,phone,street,city,state,postal_code from facility where id = '".$ptDefaultFacility."'";
			}
			else{
				$qryGetFacDetail = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
			}
			if(empty($qryGetFacDetail) == false){
				$rsGetFacDetail = imw_query($qryGetFacDetail);
				if($rsGetFacDetail){
					if(imw_num_rows($rsGetFacDetail) > 0){
						$rowGetFacDetail = imw_fetch_array($rsGetFacDetail);
						$facName = $rowGetFacDetail['name'];
						$facPhone = $rowGetFacDetail['phone'];
						$facStreet = $rowGetFacDetail['street'];
						$facCity = $rowGetFacDetail['city'];
						$facState = $rowGetFacDetail['state'];
						$facPostalCode = $rowGetFacDetail['postal_code'];				
					}
				}
			}
			//echo '<pre>';
			//print_r($resultPatientDetail);die;
			$currentDate = date("YmdHis");
			$currentDate .= "-0500";
			$patientDOB = $resultPatientDetail['DOB'];
			$patientDOB = str_replace("-","",$patientDOB);
			
			$patientGender = $resultPatientDetail['sex'];
			switch ($patientGender):
				case "Male":
					$patientGender = "M";	
					$patientGenderDisplayName = "Male";
				break;
				case "Female":
					$patientGender = "F";	
					$patientGenderDisplayName = "Female";
				break;
				default:
					$patientGender = "UN";	
					$patientGenderDisplayName = "Undifferentiated";
				break;
			endswitch;
				
			if(strpos($request['option'],'Demographics') == true || strpos($request['option'],'Bulk') == true){	
			
				$arrImedicWareMaritalStatus = array('married','single','divorced','widowed','separated','domestic partner');
				$arrImedicLiakaStatus = array(
												array("Patient_Marital_Status"=>"M","Patient_Marital_Status_Display_Name"=> "Married"),
												array("Patient_Marital_Status"=>"S","Patient_Marital_Status_Display_Name"=> "Never Married"),								  
												array("Patient_Marital_Status"=>"D","Patient_Marital_Status_Display_Name"=> "Divorced"),
												array("Patient_Marital_Status"=>"W","Patient_Marital_Status_Display_Name"=> "Widowed"),
												array("Patient_Marital_Status"=>"L","Patient_Marital_Status_Display_Name"=> "Separated"),
												array("Patient_Marital_Status"=>"T","Patient_Marital_Status_Display_Name"=> "Domestic Partner")
											  );
				//print_r($arrImedicLiakaStatus); die;
				$patientMaritalStatus = "";
				$patientMaritalStatusDisplayName = "";
				foreach($arrImedicWareMaritalStatus as $key => $value){
					if($value == $resultPatientDetail['status']){
						$patientMaritalStatus = $arrImedicLiakaStatus[$key]['Patient_Marital_Status'];				
						$patientMaritalStatusDisplayName = $arrImedicLiakaStatus[$key]['Patient_Marital_Status_Display_Name'];				
						break;
					}		
				}
				if($patientMaritalStatus &&	$patientMaritalStatusDisplayName){
					$XMLPartMaritalStatus = "<maritalStatusCode code=\"$patientMaritalStatus\" displayName=\"$patientMaritalStatusDisplayName\" codeSystemName=\"MaritalStatusCode\" codeSystem=\"2.16.840.1.113883.5.2\"/>";
				}
			}
			
			$XMLPartPatientHomePhone = "";
			$XMLPartPatientWorkPhone = "";
			$XMLPartPatientMobilePhone = "";
			$XMLPartPatientEmail = "";
			
			if($resultPatientDetail['phone_home'] && $resultPatientDetail['phone_home']!="--"){
				$phone_home=core_phone_format($resultPatientDetail['phone_home']);
				$XMLPartPatientHomePhone = "<telecom value=\"tel:+1-$phone_home\" use=\"HP\"/>";			
			}
			if($resultPatientDetail['phone_biz'] && $resultPatientDetail['phone_biz']!="--"){
				$bizPhone=core_phone_format($resultPatientDetail['phone_biz']);
				$XMLPartPatientWorkPhone = "<telecom value=\"tel:+1-$bizPhone\" use=\"WP\"/>";
			}
			if($resultPatientDetail['phone_cell'] && $resultPatientDetail['phone_cell']!="--"){
				$cellPhone=core_phone_format($resultPatientDetail['phone_cell']);
				$XMLPartPatientMobilePhone = "<telecom value=\"tel:+1-$cellPhone\" use=\"MC\"/>";			
			}
			if($resultPatientDetail['email'] && $resultPatientDetail['email']!="--"){
				if($this->checkEmail($resultPatientDetail['email'])){
					$XMLPartPatientEmail = "<telecom value=\"mailto:$resultPatientDetail[email]\"/>";			
				}
			}

			if(strpos($request['option'],'Demographics') == true || strpos($request['option'],'Bulk') == true){
				$XMLPartPerformerPrimaryProvider = "";
				$XMLPartPerformerReferringPhysician = "";
				if($resultPatientDetail['ptProviderFName'] || $resultPatientDetail['ptProviderMName'] || $resultPatientDetail['ptProviderLName']){
					$XMLPartPerformerPrimaryProvider = "<performer typeCode=\"PRF\">
												<templateId assigningAuthorityName=\"HITSP/C32\" root=\"2.16.840.1.113883.3.88.11.32.4\"/>
												<functionCode code=\"CP\" displayName=\"Consulting Provider\" codeSystemName=\"Provider Role\" codeSystem=\"2.16.840.1.113883.12.443\">
												  <originalText>No Providers</originalText>
												</functionCode>
												<time>
												  <low />
												  <high />
												</time>
												<assignedEntity>
												  <id/>	
												  <addr/>
												  <telecom/>
												  <assignedPerson>
													<name>										  
													  <given qualifier=\"CL\">$resultPatientDetail[ptProviderFName]</given>
													  <given>$resultPatientDetail[ptProviderMName]</given>
													  <family qualifier=\"BR\">$resultPatientDetail[ptProviderLName]</family>										  
													</name>
												  </assignedPerson>
												</assignedEntity>
											  </performer>";
				}
				if($resultPatientDetail['ptRefferPhyTitle'] || $resultPatientDetail['ptRefferPhyFName'] || $resultPatientDetail['ptRefferPhyMName']  || $resultPatientDetail['ptRefferPhyLName']){
				$XMLPartPerformerReferringPhysician = "<performer typeCode=\"PRF\">
											<templateId assigningAuthorityName=\"HITSP/C32\" root=\"2.16.840.1.113883.3.88.11.32.4\"/>
											<functionCode code=\"RP\" displayName=\"Referring Provider\" codeSystemName=\"Provider Role\" codeSystem=\"2.16.840.1.113883.12.443\">
											  <originalText>No Providers</originalText>
											</functionCode>
											<time>
											  <low />
											  <high />
											</time>
											<assignedEntity>
											  <id/>		
											  <addr/>
											  <telecom value=\"tel:+1-$resultPatientDetail[ptRefferPhyPhone]\" use=\"WP\"/>						  
											  <assignedPerson>
												<name>	
												  <prefix>$resultPatientDetail[ptRefferPhyTitle]</prefix> 
												  <given qualifier=\"CL\">$resultPatientDetail[ptRefferPhyFName]</given>
												  <given>$resultPatientDetail[ptRefferPhyMName]</given>
												  <family qualifier=\"BR\">$resultPatientDetail[ptRefferPhyLName]</family>										  
												</name>
											  </assignedPerson>
											</assignedEntity>
										  </performer>";
				}
				
				#END----------------geting Patient detail
				
				
				#START----------------geting Patient responsible party data as Supports in LAIKA
				$getPatientRespPartyDetail = "select * from resp_party where patient_id = $pid";
				$rsGetPatientRespPartyDetail = imw_query($getPatientRespPartyDetail);	
				if(!$rsGetPatientRespPartyDetail){
					echo ("Error : ".imw_error());		
				}				
				$resultPatientRespPartyDetail = imw_fetch_assoc($rsGetPatientRespPartyDetail);
				//echo '<pre>';
				//print_r($resultPatientRespPartyDetail);
				//die;
				$XMLPartSupportRelationGUARD = "";
				$XMLPartSupportRelationOTHER = "";
				
				if($resultPatientRespPartyDetail['relation'] == "Guardian"){
					$home_ph1=core_phone_format($resultPatientRespPartyDetail['home_ph']);
					$work_ph1=core_phone_format($resultPatientRespPartyDetail['work_ph']);
					$mobile1=core_phone_format($resultPatientRespPartyDetail['mobile']);
					$XMLPartSupportRelationGUARD = "<guardian classCode=\"GUARD\">
											<templateId root=\"2.16.840.1.113883.3.88.11.83.3\"/>								
											<addr>
												<streetAddressLine>$resultPatientRespPartyDetail[address]</streetAddressLine> 
												<streetAddressLine>$resultPatientRespPartyDetail[address2]</streetAddressLine> 
												<city>$resultPatientRespPartyDetail[city]</city> 
												<state>$resultPatientRespPartyDetail[state]</state> 
												<postalCode>$resultPatientRespPartyDetail[zip]</postalCode> 
												<country>US</country> 
											</addr>
											<telecom value=\"tel:+1-$home_ph1\" use=\"HP\" /> 
											<telecom value=\"tel:+1-$work_ph1\" use=\"WP\" /> 
											<telecom value=\"tel:+1-$mobile1\" use=\"MC\" /> 								
												<guardianPerson>
													<name>
														<prefix>$resultPatientRespPartyDetail[title]</prefix> 
														<given qualifier=\"CL\">$resultPatientRespPartyDetail[fname]</given> 
														<given>$resultPatientRespPartyDetail[mname]</given> 
														<family qualifier=\"BR\">$resultPatientRespPartyDetail[lname]</family> 
														<suffix>$resultPatientRespPartyDetail[suffix]</suffix> 
													</name>
												</guardianPerson>
										</guardian>
										";
				}
				elseif($resultPatientRespPartyDetail['relation']){
					$home_ph2=core_phone_format($resultPatientRespPartyDetail['home_ph']);
					$work_ph2=core_phone_format($resultPatientRespPartyDetail['work_ph']);
					$mobile2=core_phone_format($resultPatientRespPartyDetail['mobile']);
					$XMLPartSupportRelationOTHER = "<participant typeCode=\"IND\">
												<templateId root=\"2.16.840.1.113883.3.88.11.32.3\"/>
												<time value=\"$patientRegistrationDate\"/>
												<associatedEntity classCode=\"AGNT\">
												  <addr>
													<streetAddressLine>$resultPatientRespPartyDetail[address]</streetAddressLine> 
													<streetAddressLine>$resultPatientRespPartyDetail[address2]</streetAddressLine> 
													<city>$resultPatientRespPartyDetail[city]</city> 
													<state>$resultPatientRespPartyDetail[state]</state> 
													<postalCode>$resultPatientRespPartyDetail[zip]</postalCode> 
													<country>US</country> 
												</addr>
												<telecom value=\"tel:+1-$home_ph2\" use=\"HP\" /> 
												<telecom value=\"tel:+1-$work_ph2\" use=\"WP\" /> 
												<telecom value=\"tel:+1-$mobile2\" use=\"MC\" /> 		
												  <associatedPerson>
													<name>
														<prefix>$resultPatientRespPartyDetail[title]</prefix> 
														<given qualifier=\"CL\">$resultPatientRespPartyDetail[fname]</given> 
														<given>$resultPatientRespPartyDetail[mname]</given> 
														<family qualifier=\"BR\">$resultPatientRespPartyDetail[lname]</family> 
														<suffix>$resultPatientRespPartyDetail[suffix]</suffix> 
													</name>
												  </associatedPerson>
												</associatedEntity>
											  </participant>
										";
				}	
				#END----------------geting Patient responsible party data as Supports in LAIKA
			}
			#START----------------geting Patient Insurence data
			
			if(strpos($request['option'],'Insurance')== true || strpos($request['option'],'Bulk')== true){
				
				$XMLPartInsurance = "";
				$getPatientInsDetail = "select insurance_data.*,insurance_companies.name as insProviderName,insurance_companies.contact_address as insProviderAddress,
				insurance_companies.contact_address as insProviderAddress,insurance_companies.City as insProviderCity,insurance_companies.State as insProviderState,
				insurance_companies.Zip as insProviderZip,insurance_companies.phone as insProviderPhone
				from insurance_data
				LEFT JOIN insurance_companies on insurance_companies.id = insurance_data.provider where pid = '$pid'";
				$rsPatientInsDetail = imw_query($getPatientInsDetail);
				if(!$rsPatientInsDetail){
					echo ("Error : ".imw_error());		
				}
				else{
					if(imw_num_rows($rsPatientInsDetail)>0){
						$arrImedicWareInsRelation = array('self','Father','Mother','Son','Doughter','Spouse','Guardian','POA');
						$arrImedicLiakaInsRelation = array(
												array("Patient_Ins_Relation"=>"SELF","Patient_Ins_Relation_Display_Name"=> "self"),
												array("Patient_Ins_Relation"=>"FTH","Patient_Ins_Relation_Display_Name"=> "Father"),								  
												array("Patient_Ins_Relation"=>"MTH","Patient_Ins_Relation_Display_Name"=> "Mother"),
												array("Patient_Ins_Relation"=>"SON","Patient_Ins_Relation_Display_Name"=> "Son"),
												array("Patient_Ins_Relation"=>"DAU","Patient_Ins_Relation_Display_Name"=> "Daughter"),
												array("Patient_Ins_Relation"=>"SPS","Patient_Ins_Relation_Display_Name"=> "Spouse"),
												array("Patient_Ins_Relation"=>"GUARD","Patient_Ins_Relation_Display_Name"=> "Guardian"),
												array("Patient_Ins_Relation"=>"POWATT","Patient_Ins_Relation_Display_Name"=> "Power of attorney")
											  );
						$XMLPartInsurance = "<component>
												<section>
													<templateId assigningAuthorityName=\"CCD\" root=\"2.16.840.1.113883.10.20.1.9\" />";
						$XMLPartInsurance .= "<code code=\"48768-6\" codeSystemName=\"LOINC\" codeSystem=\"2.16.840.1.113883.6.1\" />"; 
						$XMLPartInsurance .= "<title>Insurance Providers</title>";
						$XMLPartInsurance .= "<text>
												<table border=\"1\" width=\"100%\">
												  <thead>
													<tr>
													  <th>Insurance Provider Name</th>
													  <th>Insurance Provider Type</th>
													  <th>Insurance Provider Group Number</th>
													</tr>
												  </thead>
												  <tbody>";
						$XMLPartInsuranceHTMLEND =	"</tbody>
													</table>
												  </text>";	
						$XMLPartInsuranceComponentEnd = "</section>
											</component>";						  
						$XMLPartInsuranceHTML = "";
						$XMLPartInsuranceMain = "";
						$sequenceNumber = 0;
						while($row =imw_fetch_array($rsPatientInsDetail)){
							$sequenceNumber++;
							$insDOB = str_replace("-","",$row['subscriber_DOB']);					
							$patientInsRelation = "";
							$patientInsRelationDisplayName = "";
							foreach($arrImedicWareInsRelation as $key => $value){
								if($value == $row['subscriber_relationship']){
									$patientInsRelation = $arrImedicLiakaInsRelation[$key]['Patient_Ins_Relation'];				
									$patientInsRelationDisplayName = $arrImedicLiakaInsRelation[$key]['Patient_Ins_Relation_Display_Name'];				
									break;
								}		
							}		
							
							$XMLPartInsuranceContactHomePhone = "";
							$XMLPartInsuranceContactWorkPhone = "";
							$XMLPartInsuranceContactMobilePhone = "";
							$XMLPartInsuranceProviderPhone = "";
							
							if(trim($row[subscriber_phone]) && trim($row[subscriber_phone])!="--"){
								$XMLPartInsuranceContactHomePhone = "<telecom value=\"tel:+1-".core_phone_format(trim($row[subscriber_phone]))."\" use=\"HP\"/>";			
							}
							if(trim($row[subscriber_biz_phone]) && trim($row[subscriber_biz_phone])!="--"){
								$XMLPartInsuranceContactWorkPhone = "<telecom value=\"tel:+1-".core_phone_format(trim($row[subscriber_biz_phone]))."\" use=\"WP\"/>";
							}
							if(trim($row[subscriber_mobile]) && trim($row[subscriber_mobile])!="--"){
								$XMLPartInsuranceContactMobilePhone = "<telecom value=\"tel:+1-".core_phone_format(trim($row[subscriber_mobile]))."\" use=\"MC\"/>";			
							}
							
							if(trim($row[insProviderPhone]) && trim($row[insProviderPhone])!="--"){
								$XMLPartInsuranceProviderPhone = "<telecom value=\"tel:+1-".core_phone_format(trim($row[insProviderPhone]))."\" use=\"MC\"/>";			
							}
							else{
								$XMLPartInsuranceProviderPhone = "<telecom/>";			
							}
							# this 2844AF96-37D5-42a8-9FE3-3995C110B4F8
							$XMLPartInsuranceHTML .= "<tr>
														  <td>$row[insProviderName]</td>
														  <td>$row[insProviderName]</td>
														  <td>$row[group_number]</td>
													</tr>";
							$XMLPartInsuranceMain .= "<entry>
														<act classCode=\"ACT\" moodCode=\"DEF\">
															<templateId assigningAuthorityName=\"CCD\" root=\"2.16.840.1.113883.10.20.1.20\"/>
															<id extension=\"GroupOrContract#\" root='2844AF96-37D5-42a8-9FE3-3995C110B4F8'/>
															<code code=\"48768-6\" displayName=\"Payment Sources\" codeSystemName=\"LOINC\" codeSystem=\"2.16.840.1.113883.6.1\"/>
															<statusCode code=\"completed\"/>
															<entryRelationship typeCode=\"COMP\">
																<sequenceNumber value='$sequenceNumber'/>
																<act classCode=\"ACT\" moodCode=\"EVN\">
																	<templateId root=\"2.16.840.1.113883.10.20.1.26\"/>
																	<templateId root=\"2.16.840.1.113883.3.88.11.32.5\"/>
																	<id extension=\"GroupOrContract#\" root='2844AF96-37D5-42a8-9FE3-3995C110B4F8'/>
																	<code code=\"OT\" displayName=\"Other\" codeSystemName=\"X12N-1336\" codeSystem=\"2.16.840.1.113883.6.255.1336\"/>
																	<statusCode code=\"completed\"/>
																	<performer typeCode=\"PRF\"><!-- payer -->
																		<assignedEntity classCode=\"ASSIGNED\">
																			<id root=\"2.16.840.1.113883.3.88.3.1\"/>
																			<addr>
																				<streetAddressLine>".trim($row['insProviderAddress'])."</streetAddressLine>																		
																				<city>".trim($row['insProviderCity'])."</city>
																				<state>".trim($row['insProviderState'])."</state>
																				<postalCode>".trim($row['insProviderZip'])."</postalCode>
																				<country>US</country>
																			</addr>
																			$XMLPartInsuranceProviderPhone
																			<representedOrganization classCode=\"ORG\">
																				<id root=\"2.16.840.1.113883.19.5\"/>																	
																				<name>$row[insProviderName]</name>
																				<telecom/>
																				<addr/>
																			</representedOrganization>
																		</assignedEntity>
																	</performer>
																	
																	<participant typeCode=\"COV\"><!-- member -->
																		<participantRole classCode=\"PAT\">
																			<code code=\"$patientInsRelation\" displayName=\"$patientInsRelationDisplayName\" codeSystemName=\"RoleCode\" codeSystem=\"2.16.840.1.113883.5.111\"/>
																			<playingEntity>
																				<name>												  
																				  <given qualifier=\"CL\">$row[subscriber_fname]</given>
																				  <given>$row[subscriber_mname]</given>
																				  <family qualifier=\"BR\">$row[subscriber_lname]</family>
																				</name>
																				<sdtc:birthTime value=\"$insDOB\"/>
																			</playingEntity>
																		</participantRole>
																	</participant>
																	<participant typeCode=\"HLD\"><!-- subscriber -->
																		<participantRole classCode=\"IND\">
																			<id extension=\"$row[pid]\" root=\"AssignAuthorityGUID\"/>
																			<addr>
																				<streetAddressLine>".trim($row['subscriber_fname'])."</streetAddressLine>
																				<streetAddressLine>".trim($row['subscriber_street_2'])."</streetAddressLine>
																				<city>".trim($row['subscriber_city'])."</city>
																				<state>".trim($row['subscriber_state'])."</state>
																				<postalCode>".trim($row['subscriber_postal_code'])."</postalCode>
																				<country>US</country>
																			</addr>
																			$XMLPartInsuranceContactHomePhone
																			$XMLPartInsuranceContactWorkPhone
																			$XMLPartInsuranceContactMobilePhone
																			<playingEntity>
																				<name>
																					<given qualifier=\"CL\">$row[subscriber_fname]</given>
																					<given>$row[subscriber_mname]</given>
																					<family qualifier=\"BR\">$row[subscriber_lname]</family>
																				</name>
																				<sdtc:birthTime value=\"$insDOB\"/>
																			</playingEntity>
																		</participantRole>
																	</participant>														
																</act>
															</entryRelationship>
														</act>
												  </entry>";
						}
					}
				}
				$XMLPartInsurance .= $XMLPartInsuranceHTML;
				$XMLPartInsurance .= $XMLPartInsuranceHTMLEND;
				$XMLPartInsurance .= $XMLPartInsuranceMain;
				$XMLPartInsurance .= $XMLPartInsuranceComponentEnd;
				#END----------------geting Patient Insurence data
			}
			
			#START----------------geting Patient Allergies data	
			if(strpos($request['option'],'Allergies') == true  || strpos($request['option'],'Bulk') == true){	
				$qryDOSDate = "";
				if(empty($elDOSCCD) == false){
					$qryDOSDate = " AND (DATE_FORMAT(L.date, '%Y-%m-%d') = '".$elDOSCCD."') ";
				}	
				elseif($rqElectronicDOSCCD == "all"){
					$qryDOSDate = "";
				}							
				$getPatientAllergies = "select  L.*, U.lname, U.fname FROM lists AS L LEFT JOIN users AS U on U.id = L.user
										where L.pid = '$pid' AND L.type in(3,7) and L.begdate !='' and L.allergy_status != 'Deleted' ".$qryDOSDate." 
										order by(L.id)";
				
				$rsGetPatientAllergies = imw_query($getPatientAllergies);
				if(!$rsGetPatientAllergies){
					echo ("Error : ".imw_error());		
				}
				else{
					if(imw_num_rows($rsGetPatientAllergies)>0){
						$blPatAllergiesHave = true;
										
						$dbUMLS = $this->connectToUMLS($host,$port,$login,$pass,constant('UMLS_DB'));
						if(!$dbUMLS) {
							die ('Can\'t use UMLS : ' . imw_error());
						}	 		
			
						$XMLPartAllergies = "<component>
												<section>
													<templateId assigningAuthorityName=\"CCD\" root=\"2.16.840.1.113883.10.20.1.2\"/>
													<templateId root=\"2.16.840.1.113883.3.88.11.83.102\"/>
													<templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.3.13\"/>";
						$XMLPartAllergies .= "<code code=\"48765-2\" codeSystemName=\"LOINC\" codeSystem=\"2.16.840.1.113883.6.1\" />"; 
						$XMLPartAllergies .= "<title>Allergies, Adverse Reactions, Alerts</title>";
						$XMLPartAllergies .= "<text>
												<table border=\"1\" width=\"100%\">
												  <thead>
													<tr>
													  <th>Substance</th>
													  <th>Event Type</th>										  
													</tr>
												  </thead>
												  <tbody>";
						$XMLPartAllergiesHTMLEND =	"</tbody>
													</table>
												  </text>";	
						$XMLPartAllergiesComponentEnd = "</section>
											</component>";						  
						$XMLPartAllergiesHTML = "";
						$XMLPartAllergiesMain = "";
						while($row = imw_fetch_array($rsGetPatientAllergies)){
							$allergyName = $endDate = "";
							$allergyName = $row['title'];
							$allergyName = str_replace("&","and",$allergyName);
							$snomedCtCode = "";
							$snomedCtCodeDispName = "";
							if($row['ag_occular_drug'] == "fdbATDrugName"){
								$snomedCtCode = "416098002";
								$snomedCtCodeDispName = "Drug allergy";
							}
							elseif($row['ag_occular_drug'] == "fdbATIngredient"){
								$snomedCtCode = "414285001";
								$snomedCtCodeDispName = "Food Allergy";
							}
							elseif($row['ag_occular_drug'] == "fdbATAllergenGroup"){
								$snomedCtCode = "419199007";
								$snomedCtCodeDispName = "Allergy to Substance";
							}
							
							$startDate = str_replace("-","",$row['begdate']);						
							$endDate = str_replace("-","",$row['enddate']);	
							
							$RXCUI = "";
							$STR = "";
							$STR = $allergyName;
							$allergieCode = "";
							$fromToGet = ($row['erx_allergy_id']) ? "RXCUI = '".$row['erx_allergy_id']."'" : "STR = '".$allergyName."'";
							
							//$getAllergieDeatilFromUMLS = "select RXCUI,STR from rxnconso where $fromToGet and SAB = 'SNOMEDCT'";
							$getAllergieDeatilFromUMLS = "select RXCUI,STR from rxnconso where $fromToGet and SAB = 'RXNORM'";					
							$rsGetAllergieDeatilFromUMLS = imw_query($getAllergieDeatilFromUMLS);					
							if(imw_num_rows($rsGetAllergieDeatilFromUMLS) > 0){
								//extract(imw_fetch_assoc($rsGetAllergieDeatilFromUMLS));
								$rowGetAllergieDeatilFromUMLS = imw_fetch_assoc($rsGetAllergieDeatilFromUMLS);
								$RXCUI = trim($rowGetAllergieDeatilFromUMLS['RXCUI']);						
								$allergieCode = "<code code=\"$RXCUI\" displayName=\"$STR\" codeSystemName=\"RxNorm\" codeSystem=\"2.16.840.1.113883.6.88\" >";
								//$STR = trim($rowGetAllergieDeatilFromUMLS['STR']);
							}
							elseif(imw_num_rows($rsGetAllergieDeatilFromUMLS) == 0){
								$arrAllergyName = array();
								$allergyNameTemp = "";
								$allergyNameTemp = $allergyName;
								$allergyNameTemp = str_replace("-"," ",$allergyNameTemp);
								$arrAllergyName = explode(" ",trim($allergyNameTemp));
								$qryMore = "";
								if(count($arrAllergyName) > 1){
									foreach($arrAllergyName as $val){
										$qryMore .= " `STR` LIKE '%$val%' and";
									}
								}
								$qryMore = substr(trim($qryMore), 0, -3); 
								$getAllergieDeatilFromUMLSMore = "select RXCUI,STR from rxnconso where ".$qryMore." and SAB='RXNORM' LIMIT 1";																	
								$rsAllergieDeatilFromUMLSMore = imw_query($getAllergieDeatilFromUMLSMore);	
								if(imw_num_rows($rsAllergieDeatilFromUMLSMore) > 0){							
									$rowAllergieDeatilFromUMLSMore = imw_fetch_assoc($rsAllergieDeatilFromUMLSMore);
									$RXCUI 	= $rowAllergieDeatilFromUMLSMore['RXCUI'];
									$allergieCode = "<code code=\"$RXCUI\" displayName=\"$STR\" codeSystemName=\"RxNorm\" codeSystem=\"2.16.840.1.113883.6.88\" >";
									//$STR 	= $rowMedicationDeatilFromUMLSMore['STR'];
								}
								else{
									$allergieCode = "<code nullFlavor=\"UNK\">";
								}
							}
							$timeParameter = "";
							$activeDeactiveAllergie = "";
							if($row['allergy_status'] == 'Active'){
								$activeDeactiveAllergie = "<value code=\"55561003\" displayName=\"Active\" xsi:type=\"CE\" codeSystemName=\"SNOMED CT\" codeSystem=\"2.16.840.1.113883.6.96\"/>";
								$satusCodeAllery = "active";
							}
							elseif($row['allergy_status'] == 'Suspended'){
								$activeDeactiveAllergie = "<value code=\"73425007\" displayName=\"No Longer Active\" xsi:type=\"CE\" codeSystemName=\"SNOMED CT\" codeSystem=\"2.16.840.1.113883.6.96\"/>";						  
								$satusCodeAllery = "suspended";
							}
							elseif($row['allergy_status'] == 'Aborted'){						
								$activeDeactiveAllergie = "<value code=\"73425007\" displayName=\"No Longer Active\" xsi:type=\"CE\" codeSystemName=\"SNOMED CT\" codeSystem=\"2.16.840.1.113883.6.96\"/>";						  
								$satusCodeAllery = "aborted";
								$timeParameter = "<high value=\"00000000\"/>";
							}
							
							$recComment = "";
							$recComment = $row['comments'];
								
							if($endDate == ""){
								$endDate = "00000000";
							}
							
							if(trim($RXCUI) != ""){
							
								$XMLPartAllergiesHTML .= "<tr>
															  <td>$allergyName</td>
															  <td>$snomedCtCodeDispName</td>
															  
														</tr>";
								$XMLPartAllergiesMain .= "<entry>
																<act classCode=\"ACT\" moodCode=\"EVN\">
																  <templateId root=\"2.16.840.1.113883.10.20.1.27\"/>
																  <templateId root=\"2.16.840.1.113883.3.88.11.32.6\"/>
																  <templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.5.3\"/>
																  <templateId root=\"2.16.840.1.113883.3.88.11.83.6\"/>
																  <templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.5.1\"/>
																  <id />
																  <code nullFlavor=\"NA\"/>
																  <statusCode code=\"$satusCodeAllery\" />
																  <effectiveTime>
																		<low value=\"$startDate\"/>															
																		$timeParameter
																	  </effectiveTime>
																  <entryRelationship typeCode=\"SUBJ\" inversionInd=\"false\">
																	<observation classCode=\"OBS\" moodCode=\"EVN\">
																	  <templateId root=\"2.16.840.1.113883.10.20.1.18\"/>
																	  <templateId root=\"2.16.840.1.113883.10.20.1.28\"/>
																	  <templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.5\"/>
																	  <templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.6\"/>
																	  <id />
																	  <code code=\"$snomedCtCode\" displayName=\"$snomedCtCodeDispName\" codeSystemName=\"SNOMED CT\" codeSystem=\"2.16.840.1.113883.6.96\"/>
																	  <statusCode code=\"completed\"/>
																	  <effectiveTime>
																		<low value=\"$startDate\"/>
																		<high value=\"$endDate\"/>
																	  </effectiveTime>
																	  <value xsi:type=\"CD\" nullFlavor=\"UNK\"></value>
																	  <participant typeCode=\"CSM\">
																		<participantRole classCode=\"MANU\">
																		  <playingEntity classCode=\"MMAT\">
																			$allergieCode
																			<originalText><reference value=\"$STR\"/></originalText>
																			</code>
																			<name>$STR</name>
																		  </playingEntity>
																		</participantRole>
																	  </participant>
																	  <entryRelationship typeCode=\"SUBJ\" inversionInd=\"true\">
																		<observation classCode=\"OBS\" moodCode=\"EVN\">
																			<templateId root=\"2.16.840.1.113883.10.20.1.55\"/>
																			<templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.1\"/>
																			<code code=\"SEV\" displayName=\"Severity\" codeSystem=\"2.16.840.1.113883.5.4\"
																				codeSystemName=\"ActCode\" />
																			<text><reference value=\"$recComment\"/></text>
																			<statusCode code=\"completed\"/>
																			<value xsi:type=\"CD\" code=\"24484000\" displayName=\"Severe\" codeSystem=\"2.16.840.1.113883.6.96\" codeSystemName=\"SNOMED CT\" />
																		</observation>
																	</entryRelationship>
																	  <entryRelationship typeCode=\"REFR\">
																		<observation classCode=\"OBS\" moodCode=\"EVN\">
																		  <templateId root=\"2.16.840.1.113883.10.20.1.39\"/>
																		  <code code=\"33999-4\" displayName=\"Status\" codeSystemName=\"AlertStatusCode\" codeSystem=\"2.16.840.1.113883.6.1\"/>
																		  <statusCode code=\"completed\"/>
																		  $activeDeactiveAllergie
																		</observation>
																	  </entryRelationship>
																	</observation>
																  </entryRelationship>												  
																</act>
															  </entry>";	
							}
						}
						
					}	
				}
				$XMLPartAllergies .= $XMLPartAllergiesHTML;
				$XMLPartAllergies .= $XMLPartAllergiesHTMLEND;
				$XMLPartAllergies .= $XMLPartAllergiesMain;
				$XMLPartAllergies .= $XMLPartAllergiesComponentEnd;
			}
			#END----------------geting Patient Allergies data
			
			#START----------------geting Patient Medication data	
			
			if(strpos($request['option'],'Medications')== true || strpos($request['option'],'Bulk')== true){
				
				$this->connectToImedicware($host,$port,$login,$pass,$dbase);
				$qryDOSDate = "";
				if(empty($elDOSCCD) == false){			
					$qryDOSDate = " AND (DATE_FORMAT(date, '%Y-%m-%d') = '".$elDOSCCD."') ";
				}	
				elseif($rqElectronicDOSCCD == "all"){
					$qryDOSDate = "";
				}					
				$getPatientMedication = "select * from lists where pid='$pid' and (type='1' or type='4') and begdate!='' ".$qryDOSDate." order by(id)";
				
				$rsGetPatientMedication = imw_query($getPatientMedication);
				if(!$rsGetPatientMedication){
					echo ("Error : ".imw_error());		
				}
				else{
					if(imw_num_rows($rsGetPatientMedication)>0){
						$blPatMedicationsHave = true;
										
						$dbUMLS = $this->connectToUMLS($host,$port,$login,$pass,constant('UMLS_DB'));
						if (!$dbUMLS) {
							die ('Can\'t use UMLS : ' . imw_error());
						}
			
						$XMLPartMedication = "<component>
												<section>
													<templateId assigningAuthorityName=\"CCD\" root=\"2.16.840.1.113883.10.20.1.8\"/>
													<templateId root=\"2.16.840.1.113883.3.88.11.83.112\"/>
													<templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.3.19\"/>
													";
						$XMLPartMedication .= "<code code=\"10160-0\" displayName=\"History of medication use\" codeSystemName=\"LOINC\" codeSystem=\"2.16.840.1.113883.6.1\"/>"; 
						$XMLPartMedication .= "<title>Medications</title>";
						$XMLPartMedication .= "<text>
												<table border=\"1\" width=\"100%\">
												  <thead>
													<tr>
													  <th>Product Display Name</th>	
													  <th>Sig.</th>											  
													  <th>Ordered Value</th>
													  <th>Ordered Unit</th>
													  <th>Expiration Time</th>									  
													</tr>
												  </thead>
												  <tbody>";
						$XMLPartMedicationHTMLEND =	"</tbody>
													</table>
												  </text>";	
						$XMLPartMedicationComponentEnd = "</section>
											</component>";						  
						$XMLPartMedicationHTML = "";
						$XMLPartMedicationMain = "";
						
						$counter = 1;
						while($row =imw_fetch_array($rsGetPatientMedication)){
							$ptMedEndDate = "";
							$medQty = $medDosage = $medDosageVal = $medDosageUnit = $medDosageTag = "";
							if(!empty($row['enddate']) && $row['enddate']!="0000-00-00"){
								$ptMedEndDate = date("F d, Y",strtotime($row['enddate']));
							}
							if(!empty($row['destination']) && trim($row['destination']) != ""){
								$medDosage = trim($row['destination']);
								$arrMedDosage = explode(" ",$medDosage);
								$medDosageVal = (int)$arrMedDosage[0];
								$medDosageUnit = $arrMedDosage[1];
								if($medDosageVal > 0 && $medDosageUnit != ""){
									$medDosageTag = "<doseQuantity value=\"$medDosageVal\" unit=\"$medDosageUnit\"/>";
								}
							}
							if(!empty($row['qty']) && is_numeric($row['qty']) && trim($row['qty']) != ""){
								$medQty = trim($row['qty']);
							}
							
							$medSig = "";
							$medSig = trim($row['sig']);
							 
							
							$startDate = str_replace("-","",$row['begdate']);						
							$endDate = str_replace("-","",$row['enddate']);	
							if($endDate == ""){
								$endDate = "00000000";
							}
							$RXCUI = "";
							$STR = "";
							$STR = $row['title'];
							$medicationCode = "";
							$getMedicationDeatilFromUMLS = "select RXCUI,STR from rxnconso where STR = '".$row['title']."' and SAB='RXNORM'";					
							$rsGetMedicationDeatilFromUMLS = imw_query($getMedicationDeatilFromUMLS);
							if(imw_num_rows($rsGetMedicationDeatilFromUMLS) > 0){
								//extract(imw_fetch_assoc($rsGetMedicationDeatilFromUMLS));
								$rowGetMedicationDeatilFromUMLS = imw_fetch_assoc($rsGetMedicationDeatilFromUMLS);
								$RXCUI 	= $rowGetMedicationDeatilFromUMLS['RXCUI'];						
								$medicationCode = "<code code=\"$RXCUI\" displayName=\"$STR\" codeSystemName=\"RxNorm\" codeSystem=\"2.16.840.1.113883.6.88\">";	
								//$STR 	= $rowGetMedicationDeatilFromUMLS['STR'];
							}
							elseif(imw_num_rows($rsGetMedicationDeatilFromUMLS) == 0){
								$arrMedictionName = array();
								
								$medNameTemp = "";
								$medNameTemp = trim($row['title']);
								$medNameTemp = str_replace("-"," ",$medNameTemp);						
								
								$arrMedictionName = explode(" ",$medNameTemp);
								$qryMore = "";
								if(count($arrMedictionName) > 1){
									foreach($arrMedictionName as $val){
										$qryMore .= " `STR` LIKE '%$val%' and";
									}
								}
								$qryMore = substr(trim($qryMore), 0, -3); 
								$getMedicationDeatilFromUMLSMore = "select RXCUI,STR from rxnconso where ".$qryMore." and SAB='RXNORM' LIMIT 1";																	
								$rsMedicationDeatilFromUMLSMore = imw_query($getMedicationDeatilFromUMLSMore);	
								if(imw_num_rows($rsMedicationDeatilFromUMLSMore) > 0){							
									$rowMedicationDeatilFromUMLSMore = imw_fetch_assoc($rsMedicationDeatilFromUMLSMore);
									$RXCUI 	= $rowMedicationDeatilFromUMLSMore['RXCUI'];
									$medicationCode = "<code code=\"$RXCUI\" displayName=\"$STR\" codeSystemName=\"RxNorm\" codeSystem=\"2.16.840.1.113883.6.88\">";	
								}
								else{
									$medicationCode = "<code nullFlavor=\"UNK\">";	
								}
							}
							$activeDeactiveMedication = "";
								$activeDeactiveMedication = "<value code=\"55561003\" displayName=\"Active\" xsi:type=\"CE\" codeSystemName=\"SNOMED CT\" codeSystem=\"2.16.840.1.113883.6.96\"/>";
							if(trim($RXCUI) != ""){					
								$XMLPartMedicationHTML .= "<tr>
															  <td>
																<content ID=\"medication-$counter\">$row[title]</content>
															  </td>				
															  <td>$medSig</td>								  
															  <td>$medDosageVal</td>
															  <td>$medDosageUnit</td>
															  <td>$ptMedEndDate</td>
														</tr>";
							
								$XMLPartMedicationMain .= "<entry>
																<substanceAdministration classCode=\"SBADM\" moodCode=\"EVN\">
																  <templateId assigningAuthorityName=\"CCD\" root=\"2.16.840.1.113883.10.20.1.24\"/>
																  <templateId assigningAuthorityName=\"HITSP/C32\" root=\"2.16.840.1.113883.3.88.11.32.8\"/>
																  <templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.7\"/>
																  <templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.7.1\"/>
																  <templateId root=\"2.16.840.1.113883.3.88.11.83.8\"/>
																  <id/>
																  <statusCode code=\"completed\"/>
																  <effectiveTime xsi:type='IVL_TS'>
																	<low value='$startDate'/>
																	<high value='$endDate'/>
																  </effectiveTime>
																  $medDosageTag
																  <consumable>
																	<manufacturedProduct classCode=\"MANU\">
																	  <templateId assigningAuthorityName=\"CCD\" root=\"2.16.840.1.113883.10.20.1.53\"/>
																	  <templateId root=\"2.16.840.1.113883.3.88.11.83.8.2\"/>
																	  <templateId assigningAuthorityName=\"HITSP/C32\" root=\"2.16.840.1.113883.3.88.11.32.9\"/>
																	  <templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.7.2\"/>	
																	  <manufacturedMaterial classCode=\"MMAT\" determinerCode=\"KIND\">
																		$medicationCode															  
																		  <originalText><reference value=\"$STR\"/></originalText>
																		</code>
																		<name>$STR</name>
																	  </manufacturedMaterial>
																	</manufacturedProduct>
																  </consumable>													  
																  
																</substanceAdministration>
															  </entry>";	
							}
							$counter++;							  
						}
						
					}	
				}
				$XMLPartMedication .= $XMLPartMedicationHTML;
				$XMLPartMedication .= $XMLPartMedicationHTMLEND;
				$XMLPartMedication .= $XMLPartMedicationMain;
				$XMLPartMedication .= $XMLPartMedicationComponentEnd;
			}
			#END----------------geting Patient Medication data
			
			#START----------------geting Patient Immunizations data	
			
			if(strpos($request['option'],'Immunizations')== true || strpos($request['option'],'Bulk')== true){
				$XMLPartImmunization = "";
				
				$this->connectToImedicware($host,$port,$login,$pass,$dbase);
				$qryDOSDate = "";
				if(empty($elDOSCCD) == false){			
					$qryDOSDate = " AND (DATE_FORMAT(create_date, '%Y-%m-%d') = '".$elDOSCCD."') ";
				}	
				elseif($rqElectronicDOSCCD == "all"){
					$qryDOSDate = "";
				}					
				$getPatientImmunization = "select * from immunizations  where patient_id='".$pid."' ".$qryDOSDate." order by id";		
				$rsGetPatientImmunization = imw_query($getPatientImmunization);
				if(!$rsGetPatientImmunization){
					echo ("Error : ".imw_error());		
				}
				else{
					if(imw_num_rows($rsGetPatientImmunization)>0){
			
						$XMLPartImmunization = "<component>
												<section>
													<templateId assigningAuthorityName=\"CCD\" root=\"2.16.840.1.113883.10.20.1.6\"/>
													<templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.3.23\" assigningAuthorityName=\"IHE Immunizations Section\"/>
													<templateId root=\"2.16.840.1.113883.3.88.11.83.117\" assigningAuthorityName=\"HITSP Immunizations Section\"/>
													";
						$XMLPartImmunization .= "<code code=\"11369-6\" displayName=\"History of immunizations\" codeSystemName=\"LOINC\" codeSystem=\"2.16.840.1.113883.6.1\"/>"; 
						$XMLPartImmunization .= "<title>Immunizations</title>";
						$XMLPartImmunization .= "<text>
												<table border=\"1\" width=\"100%\">
												  <thead>
													<tr>
													  <th>Vaccine</th>											  
													  <th>Administration Date</th>
													  <th>Administration By</th>
													  <th>Manufacturer</th>
													  <th>Lot#</th>
													</tr>
												  </thead>
												  <tbody>";
						$XMLPartImmunizationHTMLEND =	"</tbody>
													</table>
												  </text>";	
						$XMLPartImmunizationComponentEnd = "</section>
											</component>";						  
						$XMLPartImmunizationHTML = "";
						$XMLPartImmunizationMain = "";
						
						while($row =imw_fetch_array($rsGetPatientImmunization)){
							$XMLPartPerformerImmunization = "";
							$XMLPartNoImmunization = "";
							$ptImmAdministeredDate = "";
							$getPtImmunizationAdminBy = "select fname as ImmAdminByFName,mname as ImmAdminByMName,lname as ImmAdminByLName from users  where id ='".$row['administered_by_id']."'";												
							$rsGetPtImmunizationAdminBy = imw_query($getPtImmunizationAdminBy);					
							if(imw_num_rows($rsGetPtImmunizationAdminBy) > 0){
								$rowGetPtImmunizationAdminBy = imw_fetch_array($rsGetPtImmunizationAdminBy);	
								$ImmAdminByFName = $rowGetPtImmunizationAdminBy['ImmAdminByFName'];
								$ImmAdminByMName = $rowGetPtImmunizationAdminBy['ImmAdminByMName'];
								$ImmAdminByLName = $rowGetPtImmunizationAdminBy['ImmAdminByLName'];
							}
							if($row['administered_date']){
								$ptImmAdministeredDate = date("F d, Y",strtotime($row['administered_date']));
							}
							
							$RXCUI = "";
							$STR = "";
							$immCodeManu = "";
							$STR = $row['manufacturer'];
											
							$dbUMLS = $this->connectToUMLS($host,$port,$login,$pass,constant('UMLS_DB'));
							if (!$dbUMLS) {
								die ('Can\'t use UMLS : ' . imw_error());
							}
							$getImmunizationDeatilFromUMLS = "select RXCUI,STR from rxnconso where STR = '".$row['manufacturer']."' and SAB='RXNORM'";					
							$rsGetImmunizationDeatilFromUMLS = imw_query($getImmunizationDeatilFromUMLS);
							if(imw_num_rows($rsGetImmunizationDeatilFromUMLS) > 0){
								$rowGetImmunizationDeatilFromUMLS = imw_fetch_assoc($rsGetImmunizationDeatilFromUMLS);
								$RXCUI 	= $rowGetImmunizationDeatilFromUMLS['RXCUI'];						
								$immCodeManu = "<code code=\"$RXCUI\" displayName=\"$STR\" codeSystemName=\"RxNorm\" codeSystem=\"2.16.840.1.113883.6.59\">";
							}
							elseif(imw_num_rows($rsGetImmunizationDeatilFromUMLS) == 0){
								$arrMedictionName = array();
								$arrMedictionName = explode(" ",trim($STR));
								$qryMore = "";
								if(count($arrMedictionName) > 1){
									foreach($arrMedictionName as $val){
										$qryMore .= " `STR` LIKE '%$val%' and";
									}
								}
								$qryMore = substr(trim($qryMore), 0, -3); 
								$getImmunizationDeatilFromUMLSMore = "select RXCUI,STR from rxnconso where ".$qryMore." and SAB='RXNORM' LIMIT 1";																	
								$rsImmunizationDeatilFromUMLSMore = imw_query($getImmunizationDeatilFromUMLSMore);	
								if(imw_num_rows($rsImmunizationDeatilFromUMLSMore) > 0){							
									$rowImmunizationDeatilFromUMLSMore = imw_fetch_assoc($rsImmunizationDeatilFromUMLSMore);
									$RXCUI 	= $rowImmunizationDeatilFromUMLSMore['RXCUI'];
									$immCodeManu = "<code code=\"$RXCUI\" displayName=\"$STR\" codeSystemName=\"RxNorm\" codeSystem=\"2.16.840.1.113883.6.59\">";
								}
								else{
									$immCodeManu = "<code nullFlavor=\"UNK\">";	
								}
							}
							$XMLPartImmunizationHTML .= "<tr>
														  <td>
															$row[immunization_id]
														  </td>												  
														  <td>
															$ptImmAdministeredDate
														  </td>	
														  <td>
															$ImmAdminByFName $ImmAdminByMName $ImmAdminByLName
														  </td>	
														  <td>
															$STR
														  </td>	
														  <td>
															$row[lot_number]
														  </td>	
													</tr>";
							$ptImmAdministeredDate = "";						
							$ptImmAdministeredDate = str_replace("-","",$row['administered_date']);	
							$ptImmAdministeredTime = date("H:i",strtotime($row['administered_time']));
							$ptImmAdministeredTime = str_replace(":","",$ptImmAdministeredTime);
							
							$XMLPartPerformerImmunization = "<performer >																											
																<assignedEntity>	
																  <id />
																  <addr nullFlavor='UNK'/>
																  <telecom nullFlavor='ASKU' use='WP'/>
																  <assignedPerson>
																	<name>										  
																	  <given qualifier=\"CL\">$ImmAdminByFName</given>
																	  <given>$ImmAdminByMName</given>
																	  <family qualifier=\"BR\">$ImmAdminByLName</family>
																	</name>
																  </assignedPerson>
																</assignedEntity>
															  </performer>";
															  
							$ptImmuNegation = "";								  
							if($row['status'] == "NotGiven"){
								$ptImmuNegation = "negationInd=\"false\"";
							}
							else{
								$ptImmuNegation = "negationInd=\"true\"";
							}
							
							if($row['status'] == "NotGiven"){
								if(
									(strtoupper($row['note'])=="IMMUNE") || (strtoupper($row['note'])=="MEDPREC") || (strtoupper($row['note'])=="OSTOCK") ||
									(strtoupper($row['note'])=="PATOBJ") || (strtoupper($row['note'])=="PHILISOP") || (strtoupper($row['note'])=="RELIG") || 
									(strtoupper($row['note'])=="VACEFF") || (strtoupper($row['note'])=="VACSAF")
								   ){
									$XMLPartNoImmunization = "<entryRelationship typeCode=\"RSON\">
																		<act classCode=\"ACT\" moodCode=\"EVN\">
																		  <templateId root=\"2.16.840.1.113883.10.20.1.27\"/>
																		  <code code=\"".strtoupper($row['note'])."\" displayName=\"name\" codeSystem=\"2.16.840.1.113883.6.96\"/>
																		</act>
																	</entryRelationship>";
																	
								}
								else{
									$XMLPartNoImmunization = "<entryRelationship typeCode=\"RSON\">
																		<act classCode=\"ACT\" moodCode=\"EVN\">
																		  <templateId root=\"2.16.840.1.113883.10.20.1.27\"/>
																		  <code code=\"".strtoupper($row['note'])."\" displayName=\"name\" codeSystem=\"2.16.840.1.113883.6.96\"/>
																		</act>
																	</entryRelationship>";
																	
								}
								
							}
							
							$doseQut = "";
							$doseQutUnit = $immDosageTag = "";
							$doseQut = $row['immzn_dose'];	
							$doseQutUnit = $row['immzn_dose_unit'];					
							if($doseQut != "" && $doseQutUnit != ""){
								$immDosageTag = "<doseQuantity value=\"$doseQut\" unit=\"$doseQutUnit\"/>";
							}
							$XMLPartImmunizationMain .= "<entry>
															<substanceAdministration classCode=\"SBADM\" $ptImmuNegation moodCode=\"EVN\" >													  
															  <templateId assigningAuthorityName=\"CCD\" root=\"2.16.840.1.113883.10.20.1.24\"/>
															  <templateId assigningAuthorityName=\"IHE Immunization\" root=\"1.3.6.1.4.1.19376.1.5.3.1.4.12\"/>
															  <templateId assigningAuthorityName=\"HITSP/C32\" root=\"2.16.840.1.113883.3.88.11.32.14\"/>
															  <templateId assigningAuthorityName=\"HITSP Immunization\" root=\"2.16.840.1.113883.3.88.11.83.13\"/>
															  
															  <id/>
															  <code code=\"IMMUNIZ\" codeSystem=\"2.16.840.1.113883.5.4\" codeSystemName=\"ActCode\"/>
															  <statusCode code=\"completed\"/>
															  <effectiveTime value=\"$ptImmAdministeredDate\"/>				
															  $immDosageTag
															  <consumable>
																<manufacturedProduct classCode=\"MANU\">
																  <templateId root=\"2.16.840.1.113883.10.20.1.53\"/>
																  <templateId root=\"2.16.840.1.113883.3.88.11.83.8.2\"/>														  
																  <templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.7.2\"/>
																  
																  <manufacturedMaterial>
																	$immCodeManu															  	
																		<originalText><reference value=\"$STR\"/></originalText>
																	</code>
																	<name>\"$STR\"</name>
																	<lotNumberText>$row[lot_number]</lotNumberText>
																  </manufacturedMaterial>
																</manufacturedProduct>
															  </consumable>
															  $XMLPartPerformerImmunization													  
															</substanceAdministration>
														  </entry>";	
												  
							
						}
						
					}	
				}
				$XMLPartImmunization .= $XMLPartImmunizationHTML;
				$XMLPartImmunization .= $XMLPartImmunizationHTMLEND;
				$XMLPartImmunization .= $XMLPartImmunizationMain;
				$XMLPartImmunization .= $XMLPartImmunizationComponentEnd;
			}
			#END----------------geting Patient Immunizations data
			
			
			#START----------------geting Patient Problem data	
			if(strpos($request['option'],'Problem_List') == true  || strpos($request['option'],'Bulk') == true){	
				$XMLPartProblemList = "";
				
				$this->connectToImedicware($host,$port,$login,$pass,$dbase);	
				$qryDOSDate = "";
				if(empty($elDOSCCD) == false){			
					$qryDOSDate = " AND (DATE_FORMAT(onset_date, '%Y-%m-%d') = '".$elDOSCCD."') ";
				}	
				elseif($rqElectronicDOSCCD == "all"){
					$qryDOSDate = "";
				}					
				$getPatientProblemList = "select PPL.*, U.lname, U.fname , U.mname FROM pt_problem_list AS PPL LEFT JOIN users AS U on U.id = PPL.user_id
											where PPL.pt_id = '$pid' and PPL.prob_type != '' ".$qryDOSDate." 
											order by(PPL.id)";		
				$rsPatientProblemList = imw_query($getPatientProblemList);
				if(!$rsPatientProblemList){
					echo ("Error : ".imw_error());		
				}
				else{
					if(imw_num_rows($rsPatientProblemList)>0){				
						$blPatProblemsHave = true;
						$XMLPartProblemList = "<component>
												<section>
													<templateId assigningAuthorityName=\"CCD\" root=\"2.16.840.1.113883.10.20.1.11\"/>
													<templateId root=\"2.16.840.1.113883.3.88.11.83.103\"/>
													<templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.3.6\"/>
													";
						$XMLPartProblemList .= "<code code=\"11450-4\" displayName=\"Problems\" codeSystemName=\"LOINC\" codeSystem=\"2.16.840.1.113883.6.1\" />"; 
						$XMLPartProblemList .= "<title>Problems</title>";
						$XMLPartProblemList .= "<text>
												<table border=\"1\" width=\"100%\">
												  <thead>
													<tr>
													  <th>ICD-9</th>
													  <th>Problem Name</th>
													  <th>Problem Type</th>
													  <th>Problem Date</th>										  
													</tr>
												  </thead>
												  <tbody>";
						$XMLPartProblemListHTMLEND =	"</tbody>
													</table>
												  </text>";	
						$XMLPartProblemListComponentEnd = "</section>
											</component>";						  
						$XMLPartProblemListHTML = "";
						$XMLPartProblemListMain = "";
						$intProbListCounter = 1;
						$arrProblemStatus = array('Active','Inactive','Chronic','Intermittent','Recurrent','Rule out','Rules out','Resolved');
						while($row = imw_fetch_array($rsPatientProblemList)){
							$problemName = $problemType = $problemDate = $problemTypeCode = $probOPFName = $probOPMName = $probOPLName = $probStatus = "";
							$problemName = $row['problem_name'];
							$problemName = str_replace("&","and",$problemName);
							$problemType = $row['prob_type'];
							$problemDate = $row['onset_date'];
							$problemDate = str_replace("-","",$problemDate);					
							$probOPFName = $row['fname'];
							$probOPMName = $row['mname'];
							$probOPLName = $row['lname'];
							$probStatus = $row['status'];
							$probStatusCCD = "";
							if(in_array($probStatus,$arrProblemStatus)){
								$probStatusCCD = $probStatus;
							}
							else{
								$probStatusCCD = "Active";
							}
							$probStatusCCD = strtolower($probStatusCCD);
							if(empty($problemName) == false){						
								switch($problemType){
									case "Diagnosis":
										$problemTypeCode = "282291009";
										break;
									case "Finding":
										$problemTypeCode = "404684003";
										break;	
									case "Problem":
										$problemTypeCode = "55607006";
										break;			
									case "Condition":
										$problemTypeCode = "64572001";
										break;			
									case "Symptom":
										$problemTypeCode = "418799008";
										break;				
									case "Complaint":
										$problemTypeCode = "409586006";
										break;
									case "Functional Limitation":
										$problemTypeCode = "248536006";
										break;
									
								}
								
								
								$arrProblemName = array();
								//preg_match_all("/([0-9]+\.[0-9]+)/", $problemName, $arrProblemName);
								#########
								preg_match_all("/([0-9]+\.[0-9]+)/", $problemName, $arrProblemName1);
								$array_replace = array();
								if(count($arrProblemName1[0]) > 0){
									foreach($arrProblemName1[0] as $val){
											array_push($array_replace, $val);
									}
								}
								$arrProblemName2 = array();
								$str_replaced = str_replace($array_replace, "", $problemName);
								preg_match_all("/([0-9]+)/", $str_replaced, $arrProblemName2);
								
								$int_arr1 = count($arrProblemName1[0]);
								$int_arr2 = count($arrProblemName2[0]);
								if($int_arr1 != "" && $int_arr2 != ""){
									$arrProblemName = array_merge($arrProblemName1, $arrProblemName2);
								}else if($int_arr1 != ""){
									$arrProblemName = $arrProblemName1;
								}else if($int_arr2 != ""){
									$arrProblemName = $arrProblemName2;
								}
								unset($arrProblemName1);
								unset($arrProblemName2);
								//print "<pre>";
								//print_r($arrProblemName);

								#########
								if(count($arrProblemName) > 1){
									if(count($arrProblemName[0]) > 0){
										foreach($arrProblemName[0] as $val){
											$icdId = $probNameOri = "";
											$icdId = $val;
											if(empty($icdId) == false){
												$qryGetProblemName = "SELECT diag_description FROM diagnosis_code_tbl WHERE dx_code = '".$icdId."'";
												$rsGetProblemName = imw_query($qryGetProblemName);
												if($rsGetProblemName){
													if(imw_num_rows($rsGetProblemName) > 0){
														$rowGetProblemName = imw_fetch_array($rsGetProblemName);
														$probNameOri = $rowGetProblemName['diag_description'];
														$XMLPartProblemListHTML .= "<tr>
																						  <td>$icdId</td>
																						  <td>$probNameOri</td>
																						  <td>$problemType</td>
																						  <td>$problemDate</td>
																					</tr>";	
														
														$XMLPartProblemListMain .= "<entry>
																						<act classCode=\"ACT\" moodCode=\"EVN\">
																						  <templateId assigningAuthorityName=\"CCD\" root=\"2.16.840.1.113883.10.20.1.27\"/>
																						  <templateId assigningAuthorityName=\"HITSP/C32\" root=\"2.16.840.1.113883.3.88.11.32.7\"/>
																						  <templateId root=\"2.16.840.1.113883.3.88.11.83.7\"/>
																						  <templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.5.1\"/>
																						  <templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.5.2\"/>
																						  <templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.5.1\"/>
																						  <id />
																						  <code nullFlavor=\"NA\"/>
																						  <statusCode code=\"completed\"/>
																						  <effectiveTime>
																							<low value=\"$problemDate\"/>
																							<high value=\"00000000\"/>
																						  </effectiveTime>
																						  <entryRelationship typeCode=\"SUBJ\" inversionInd=\"false\">
																							<observation classCode=\"OBS\" moodCode=\"EVN\">
																							  <templateId assigningAuthorityName=\"CCD\" root=\"2.16.840.1.113883.10.20.1.28\"/>
																							  <templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.5\"/>
																							  <id />
																							  <code code=\"$problemTypeCode\" displayName=\"$problemType\" codeSystemName=\"SNOMED CT\" codeSystem=\"2.16.840.1.113883.6.96\"/>
																							  <text><reference value=\"#problem-$intProbListCounter\"/></text>
																							  <statusCode code=\"completed\"/>
																							  <effectiveTime>
																								<low value=\"$problemDate\"/>
																								<high value=\"00000000\"/>
																							  </effectiveTime>
																							  
																							  <value xsi:type=\"CD\" nullFlavor=\"UNK\">
																									<originalText>
																										<reference value=\"#problem-$intProbListCounter\"/>
																									</originalText>
																									<translation code=\"$icdId\" displayName=\"$probNameOri\" codeSystemName=\"ICD-9\" codeSystem=\"2.16.840.1.113883.6.103\"/>
																								</value>
																							  <performer typeCode='PRF'>																											
																								<assignedEntity >	
																									<id />
																									<addr nullFlavor='UNK'/>
																									<telecom nullFlavor='ASKU' use='WP'/>
																									<assignedPerson>
																										<name>										  
																											<given qualifier=\"CL\">$probOPFName</given>
																											<given>$probOPMName</given>
																											<family qualifier=\"BR\">$probOPLName</family>
																										</name>
																									</assignedPerson>
																								</assignedEntity>
																							  </performer>
																							</observation>
																						  </entryRelationship>												  
																						</act>
																					  </entry>";					
														
													}
													imw_free_result($rsGetProblemName);
												}
											}
											$intProbListCounter++;
										}
									}
								}						  				
							}			
						}
					}	
				}
				$XMLPartProblemList .= $XMLPartProblemListHTML;
				$XMLPartProblemList .= $XMLPartProblemListHTMLEND;
				$XMLPartProblemList .= $XMLPartProblemListMain;
				$XMLPartProblemList .= $XMLPartProblemListComponentEnd;
			}
			#END----------------geting Patient Problem data
			
			#START----------------geting Patient Labs data	
			if(strpos($request['option'],'Labs') == true  || strpos($request['option'],'Bulk') == true){		
				$blLabTestTypeNotExits = $blLabTestDatetNotExits = $blLabTestLoincNotExits = $blLabTestNameNotExits = $blLabTestResultNotExits = $blLabTestUnitNotExits = false;
				$XMLPartLabs = "";
				
				$this->connectToImedicware($host,$port,$login,$pass,$dbase);	
				$qryDOSDate = $qryRADDOSDate = "";
				if(empty($elDOSCCD) == false){			
					$qryDOSDate = " AND (DATE_FORMAT(lab_test_performed_date, '%Y-%m-%d') = '".$elDOSCCD."') ";
					$qryRADDOSDate = " AND (DATE_FORMAT(rad_performed_date, '%Y-%m-%d') = '".$elDOSCCD."') ";
				}	
				elseif($rqElectronicDOSCCD == "all"){
					$qryDOSDate = $qryRADDOSDate = "";
				}					
				$getPatientLabRecord = "select lab_test_data_id,lab_name,lab_test_type,lab_loinc,lab_test_name,lab_results,lab_units,lab_range,lab_order_date,lab_status,lab_comments,lab_address,lab_source,lab_conditions, 
											DATE_FORMAT(lab_results_date,'%M-%d-%Y') as dtFormatOrder
											FROM lab_test_data where lab_patient_id = '$pid' and lab_status != '5' 									
											".$qryDOSDate."";		
				$rsPatientLabRecord = imw_query($getPatientLabRecord);
				
				$getPatientRadRecord = "select RTD.*,DATE_FORMAT(RTD.rad_results_date,'%M-%d-%Y') as dtFormatOrder
											FROM rad_test_data RTD where rad_patient_id  = '$pid' and rad_status != '3' ".$qryRADDOSDate."";		
				$rsPatientRadRecord = imw_query($getPatientRadRecord);
				
				if((!$rsPatientLabRecord) || (!$rsPatientRadRecord)){
					echo ("Error : ".imw_error());		
				}
				else{
					if(imw_num_rows($rsPatientLabRecord)>0 || imw_num_rows($rsPatientRadRecord)>0){				
						$blPatLabResultsHave = true;
						$XMLPartLabs = "<component>
											<section>
												<templateId assigningAuthorityName=\"CCD\" root=\"2.16.840.1.113883.10.20.1.14\"/>
												<templateId root=\"2.16.840.1.113883.3.88.11.83.122\"/> 
												<templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.3.28\"/>
												";
						$XMLPartLabs .= "<code code=\"30954-2\" displayName=\"Relevant diagnostic tests and/or laboratory data\" codeSystemName=\"LOINC\" codeSystem=\"2.16.840.1.113883.6.1\" />"; 
						$XMLPartLabs .= "<title>Relevant diagnostic tests and/or laboratory data</title>";
						$XMLPartLabs .= "<text>
											<table border=\"1\" width=\"100%\">
												<thead>
													<tr>
														<th>Lab</th>												
														<th>Lab Name</th>
														<th>Lab Address</th>
														<th>Result Date</th>
														<th>Test performed</th>
														<th>Specimen source</th>												
														<th>Result/Unit</th>
														<th>Specimen source</th>																						  
													</tr>
												</thead>
											  <tbody>";
						$XMLPartLabsHTMLEND =	"</tbody>
													</table>
												  </text>";	
						$XMLPartLabsComponentEnd = "
													</section>
												  </component>
												";						  
						$XMLPartLabsHTML = "";
						$XMLPartLabsMain = "";		
						$intLabResultCounter = 1;		
						while($row = imw_fetch_array($rsPatientLabRecord)){					
							if($row['lab_test_type'] == ""){
								$blLabTestTypeNotExits = true;
							}
							if($row['lab_loinc'] == ""){
								$blLabTestLoincNotExits = true;
							}
							if($row['lab_test_name'] == ""){
								$blLabTestNameNotExits = true;
							}
							if($row['lab_results'] == ""){
								$blLabTestResultNotExits = true;
							}
							if($row['lab_units'] == ""){
								$blLabTestUnitNotExits = true;
							}
							if($row['lab_order_date'] == "0000-00-00"){
								$blLabTestDatetNotExits = true;
							}
							if($blLabTestTypeNotExits == false && $blLabTestDatetNotExits == false && $blLabTestLoincNotExits == false && $blLabTestNameNotExits == false && $blLabTestResultNotExits == false && $blLabTestUnitNotExits == false){
								$labTestDataId = $labName = $labTestType = $labLoincCode = $labTestName = $labResults = $labUnits = $labRange = $labOrderDate = $labDtFormatOrder = $labStatus = $strLabStatus = $labComments = $labAddress = $labSource  = $labConditions = $labRangeCDA = "";
								$labTestDataId = $row['lab_test_data_id'];
								$labName = $row['lab_name'];
								$labName = str_replace("&","and",$labName);
								$labTestType = $row['lab_test_type'];
								$labTestTypeCode = "";
								$labTestTypeCodeSys = "";
								$labTestTypeLower = strtolower($labTestType);
								switch ($labTestTypeLower):
									case "virology":
										$labTestTypeCode = "395124008";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "x-ray":
										$labTestTypeCode = "363680008";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "vital sign":
										$labTestTypeCode = "46680005";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "ultrasound":
										$labTestTypeCode = "16310003";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "toxicology":
										$labTestTypeCode = "69200006";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "serology":
										$labTestTypeCode = "68793005";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "procedure":
										$labTestTypeCode = "71388002";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "pathology":
										$labTestTypeCode = "108257001";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "nuclear Medicine":
										$labTestTypeCode = "371572003";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "microbiology":
										$labTestTypeCode = "19851009";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "mri":
										$labTestTypeCode = "113091000";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "imaging":
										$labTestTypeCode = "363679005";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "hematology":
										$labTestTypeCode = "252275004";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "chemistry":
										$labTestTypeCode = "275711006";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "ct":
										$labTestTypeCode = "77477000";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "angiography":
										$labTestTypeCode = "77343006";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;
									case "cbc wo differential":
										$labTestTypeCode = "43789009";
										$labTestTypeCodeSys = "<code code=\"$labTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$labTestType\"/>";
										break;														
									default:							
										$labTestTypeCodeSys = "<code nullFlavor=\"UNK\"/>";
								endswitch;
			
								$labLoincCode = $row['lab_loinc'];					
								$labTestName = $row['lab_test_name'];
								$labResults = $row['lab_results'];
								$labUnits = $row['lab_units'];
								if($labUnits == ""){
									$labUnits = "none";
								}
								$labRange = $row['lab_range'];
								if($labRange != ""){
									$labRangeCDA = "<referenceRange>
														<observationRange>
															<text>$labRange</text>
														</observationRange>
													</referenceRange>";
								}
								$labOrderDate = $row['lab_order_date'];
								$labOrderDate = str_replace("-","",$labOrderDate);
								$labDtFormatOrder = $row['dtFormatOrder'];
								$labStatus = (int)$row['lab_status'];
								$labComments = $row['lab_comments'];
								$labAddress = (trim($row['lab_address'])) ? trim($row['lab_address']) : "-";
								$labSource = $row['lab_source'];
								$labConditions = $row['lab_conditions'];
								
								if($labStatus == 3 || $labStatus == 1){
									$strLabStatus = "ordered";						
								}
								elseif($labStatus == 4){
									$strLabStatus = "completed";
								}
								$labOrderDate = str_replace("-","",$labOrderDate);					
								
								$XMLPartLabsHTML .= "<tr>
														  <td ID=\"Lab-$intLabResultCounter\">Lab Result</td>											  
														  <td>$labName</td>
														  <td>$labAddress</td>
														  <td>$labDtFormatOrder</td>
														  <td>$labTestName</td>
														  <td>$labSource</td>
														  <td>$labResults/$labUnits</td>
														  <td>$labConditions</td>
													</tr>";											
													
								$XMLPartLabsMain .= "<entry>
														<organizer classCode=\"BATTERY\" moodCode=\"EVN\">
														<templateId root=\"2.16.840.1.113883.10.20.1.32\"/>
														<id />
														$labTestTypeCodeSys
														<statusCode code=\"$strLabStatus\"/>
														<effectiveTime value=\"$labOrderDate\" /> ";
														
								$XMLPartLabsMain .= "<component>
															<procedure classCode=\"PROC\" moodCode=\"EVN\">
																	<templateId root=\"2.16.840.1.113883.3.88.11.83.17\" assigningAuthorityName=\"HITSP C83\"/>
																	<templateId root=\"2.16.840.1.113883.10.20.1.29\" assigningAuthorityName=\"CCD\"/>
																	<templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.19\" assigningAuthorityName=\"IHE PCC\"/>
																	<id/>
																	<code code=\"$labLoincCode\" codeSystem=\"2.16.840.1.113883.6.1\">
																	   <originalText>
																			$labTestType    
																			<reference value=\"#Lab-$intLabResultCounter\"/>
																		</originalText>
																	</code>
																	<text>
																		$labTestType
																		<reference value=\"#Lab-$intLabResultCounter\"/>
																	</text>
																	<statusCode code=\"completed\"/>
															</procedure>
														</component>";
								$valueTagData = $valueTagDataContent = "";						
								if(trim($labUnits) != "%"){
									$valueTagData = "<value xsi:type=\"PQ\" unit=\"$labUnits\" value=\"$labResults\"/>";
								}
								else{
									$valueTagDataContent = "unit = % value = $labResults";
									$valueTagData = "<value xsi:type=\"ST\">$valueTagDataContent</value>";
								}
								$XMLPartLabsMain .= "<component>
														<observation classCode=\"OBS\" moodCode=\"EVN\">
															<templateId assigningAuthorityName=\"CCD\" root=\"2.16.840.1.113883.10.20.1.31\"/>
															<templateId assigningAuthorityName=\"HITSP/C32\" root=\"2.16.840.1.113883.3.88.11.32.16\" /> 
															<templateId root=\"2.16.840.1.113883.3.88.11.83.15\"/>
															<templateId root=\"2.16.840.1.113883.3.88.11.83.15.1\" assigningAuthorityName=\"HITSP C83\"/>
															<templateId root=\"2.16.840.1.113883.10.20.1.31\" assigningAuthorityName=\"CCD\"/>
															<templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.13\"/>
															<id />
															<code code=\"$labLoincCode\" codeSystem=\"2.16.840.1.113883.6.1\" displayName=\"$labTestName\" codeSystemName='LOINC'/>
															<text><reference value=\"#Lab-$intLabResultCounter\"/></text>
															<statusCode code=\"completed\"/>
															<effectiveTime value=\"$labOrderDate\" /> 
															$valueTagData																									
														</observation>
													</component>";	
													
								$XMLPartLabsMain .= "</organizer>
												</entry>";	
								$intLabResultCounter++;												
							}
						}	
						
						if($blLabTestTypeNotExits == true){
							$strlabError .= "Test Type, ";
						}
						if($blLabTestLoincNotExits == true){
							$strlabError .= "Test LOINC, ";
						}
						if($blLabTestNameNotExits == true){
							$strlabError .= "Test Name, ";
						}
						if($blLabTestResultNotExits == true){
							$strlabError .= "Test Result, ";
						}
						if($blLabTestUnitNotExits == true){
							$strlabError .= "Test Unit, ";
						}
						if($blLabTestDatetNotExits == true){
							$strlabError .= "Test Date, ";
						}
						
						//RAD CCD Data
						$blRadTestTypeNotExits = $blRadTestDatetNotExits = $blRadTestLoincNotExits = $blRadTestNameNotExits = $blRadTestResultNotExits = false;
						$XMLPartRadHTML = "";
						$XMLPartRadsMain = "";		
						$intRadResultCounter = $intLabResultCounter;		
						while($row = imw_fetch_array($rsPatientRadRecord)){
							if((int)$row['rad_type'] == 0){
								$blRadTestTypeNotExits = true;
							}
							if($row['rad_loinc'] == ""){
								$blRadTestLoincNotExits = true;
							}
							if($row['rad_name'] == ""){
								$blRadTestNameNotExits = true;
							}
							if($row['rad_results'] == ""){
								$blRadTestResultNotExits = true;
							}					
							if($row['rad_order_date'] == "0000-00-00"){
								$blRadTestDatetNotExits = true;
							}
							if($blRadTestTypeNotExits == false && $blRadTestDatetNotExits == false && $blRadTestLoincNotExits == false && $blRadTestNameNotExits == false && $blRadTestResultNotExits == false && $blRadTestUnitNotExits == false){
								$radTestDataId = $radName = $radTestType = $radLoincCode = $radTestName = $radResults = $radUnits = $radRange = $radOrderDate = $radDtFormatOrder = $radStatus = $strRadStatus = $radComments = $radAddress = $radSource  = $radConditions = $radRangeCDA = "";
								$radTestDataId = $row['rad_test_data_id'];
								$radName = $row['rad_fac_name'];
								$radName = str_replace("&","and",$radName);
								$radTestType = trim($row['rad_type']);					
								$radTestTypeCode = $radTestType;
								$radTestTypeSTR = $radTestTypeSTRCodeSys = "";
								switch ((int)$radTestType):
									case 395124008:
										$radTestTypeSTR = "virology";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 363680008:
										$radTestTypeSTR = "X-ray";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 46680005:
										$radTestTypeSTR = "Vital Sign";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 16310003:
										$radTestTypeSTR = "Ultrasound";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 69200006:
										$radTestTypeSTR = "Toxicology";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 68793005:
										$radTestTypeSTR = "Serology";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 71388002:
										$radTestTypeSTR = "Procedure";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 108257001:
										$radTestTypeSTR = "Pathology";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 371572003:
										$radTestTypeSTR = "Nuclear Medicine";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 19851009:
										$radTestTypeSTR = "Microbiology";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 113091000:
										$radTestTypeSTR = "MRI";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 363679005:
										$radTestTypeSTR = "Imaging";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 252275004:
										$radTestTypeSTR = "Hematology";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 275711006:
										$radTestTypeSTR = "Chemistry";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 77477000:
										$radTestTypeSTR = "CT";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 77343006:
										$radTestTypeSTR = "Angiography";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;
									case 43789009:
										$radTestTypeSTR = "CBC WO DIFFERENTIAL";
										$radTestTypeSTRCodeSys = "<code code=\"$radTestTypeCode\" codeSystem=\"2.16.840.1.113883.6.96\" displayName=\"$radTestTypeSTR\"/>";
										break;														
									default:
										//$radTestTypeCode = "custom";
										$radTestTypeSTR = $radTestType;
										$radTestTypeSTRCodeSys = "<code nullFlavor=\"UNK\"/>";
								endswitch;
			
								
								$radLoincCode = $row['rad_loinc'];					
								$radTestName = $row['rad_name'];
								$radResults = $row['rad_results'];										
								$radOrderDate = $row['rad_order_date'];
								$radOrderDate = str_replace("-","",$radOrderDate);
								$radDtFormatOrder = $row['dtFormatOrder'];
								$radStatus = (int)$row['rad_status'];					
								$radAddress = $row['rad_address'];
								
								
								if($radStatus == 1){
									$strRadStatus = "ordered";						
								}
								elseif($radStatus == 2){
									$strRadStatus = "completed";
								}					
								
								$XMLPartLabsHTML .= "<tr>
														  <td ID=\"Lab-$intRadResultCounter\">Rad Result</td>											  
														  <td>$radName</td>
														  <td>$radAddress</td>
														  <td>$radDtFormatOrder</td>
														  <td>$radTestName</td>
														  <td></td>											  
														  <td>$radResults</td>
														  <td></td>											  
													</tr>";																					
								$XMLPartLabsMain .= "<entry>
														<organizer classCode=\"BATTERY\" moodCode=\"EVN\">
														<templateId root=\"2.16.840.1.113883.10.20.1.32\"/>
														<id />
														$radTestTypeSTRCodeSys
														<statusCode code=\"$strRadStatus\"/>
														<effectiveTime value=\"$radOrderDate\" /> ";
														
								$XMLPartLabsMain .= "<component>
															<procedure classCode=\"PROC\" moodCode=\"EVN\">
																	<templateId root=\"2.16.840.1.113883.3.88.11.83.17\" assigningAuthorityName=\"HITSP C83\"/>
																	<templateId root=\"2.16.840.1.113883.10.20.1.29\" assigningAuthorityName=\"CCD\"/>
																	<templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.19\" assigningAuthorityName=\"IHE PCC\"/>
																	<id/>
																	<code code=\"$radLoincCode\" codeSystem=\"2.16.840.1.113883.6.1\">
																	   <originalText>
																			$radTestTypeSTR    
																			<reference value=\"#Lab-$intRadResultCounter\"/>
																		</originalText>
																	</code>
																	<text>
																		$radTestTypeSTR
																		<reference value=\"#Lab-$intRadResultCounter\"/>
																	</text>
																	<statusCode code=\"completed\"/>
															</procedure>
														</component>";
														
								
								$XMLPartLabsMain .= "<component>
														<observation classCode=\"OBS\" moodCode=\"EVN\">
															<templateId assigningAuthorityName=\"CCD\" root=\"2.16.840.1.113883.10.20.1.31\"/>
															<templateId assigningAuthorityName=\"HITSP/C32\" root=\"2.16.840.1.113883.3.88.11.32.16\" /> 
															<templateId root=\"2.16.840.1.113883.3.88.11.83.15\"/>
															<templateId root=\"2.16.840.1.113883.3.88.11.83.15.1\" assigningAuthorityName=\"HITSP C83\"/>
															<templateId root=\"2.16.840.1.113883.10.20.1.31\" assigningAuthorityName=\"CCD\"/>
															<templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.4.13\"/>
															<id />
															<code code=\"$radLoincCode\" codeSystem=\"2.16.840.1.113883.6.1\" displayName=\"$radTestName\" codeSystemName='LOINC'/>
															<text><reference value=\"#Lab-$intRadResultCounter\"/></text>
															<statusCode code=\"completed\"/>
															<effectiveTime value=\"$radOrderDate\" /> 
															<value xsi:type=\"ST\">$radResults</value>
														</observation>
													</component>";	
								//<value xsi:type="ST">dsfadsf fasdf</value>					
								$XMLPartLabsMain .= "</organizer>
												</entry>";	
								$intRadResultCounter++;												
							}		
						}
						
						if($blRadTestTypeNotExits == true){
							$strRadError .= "Test Type, ";
						}
						if($blRadTestLoincNotExits == true){
							$strRadError .= "Test LOINC, ";
						}
						if($blRadTestNameNotExits == true){
							$strRadError .= "Test Name, ";
						}
						if($blRadTestResultNotExits == true){
							$strRadError .= "Test Result, ";
						}				
						if($blRadTestDatetNotExits == true){
							$strRadError .= "Test Date, ";
						}
					}	
				}
				
				$XMLPartLabs .= $XMLPartLabsHTML;
				$XMLPartLabs .= $XMLPartLabsHTMLEND;
				$XMLPartLabs .= $XMLPartLabsMain;
				$XMLPartLabs .= $XMLPartLabsComponentEnd;
			}
			#END----------------geting Patient Problem data
			
			$XMLPartComponent = "";
			if(empty($XMLPartAllergies) && empty($XMLPartInsurance) && empty($XMLPartMedication) && empty($XMLPartImmunization) && empty($XMLPartProblemList) && empty($XMLPartLabs)){
				$XMLPartComponent = "<component>
										<section>
										</section>
									</component>";
			}
			
			$laikaXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
							<ClinicalDocument xsi:schemaLocation=\"urn:hl7-org:v3 http://xreg2.nist.gov:8080/hitspValidation/schema/cdar2c32/infrastructure/cda/C32_CDA.xsd\" xmlns:sdtc=\"urn:hl7-org:sdtc\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns=\"urn:hl7-org:v3\">
							<realmCode code=\"US\"/>
							<typeId extension=\"POCD_HD000040\" root=\"2.16.840.1.113883.1.3\"/>
							<templateId assigningAuthorityName=\"CDA/R2\" root=\"2.16.840.1.113883.3.27.1776\"/>
							<templateId root=\"2.16.840.1.113883.10.20.3\" assigningAuthorityName=\"HL7/CDT Header\"/>
							<templateId assigningAuthorityName=\"CCD\" root=\"2.16.840.1.113883.10.20.1\"/>
							<templateId assigningAuthorityName=\"HITSP/C32\" root=\"2.16.840.1.113883.3.88.11.32.1\"/>
							<templateId root=\"1.3.6.1.4.1.19376.1.5.3.1.1.1\" assigningAuthorityName=\"IHE/PCC\"/>
							<id extension=\"Laika C32 Test\" assigningAuthorityName=\"Laika: An Open Source EHR Testing Framework projectlaika.org\" root=\"2.16.840.1.113883.3.72\"/>
							<code code=\"34133-9\" displayName=\"Summarization of patient data\" codeSystemName=\"LOINC\" codeSystem=\"2.16.840.1.113883.6.1\"/>
							<title/>
							<effectiveTime value=\"$currentDate\"/>
							<confidentialityCode/>
							<languageCode code=\"en-US\"/>
							<recordTarget>
							<patientRole>
							  <id extension=\"$pid\"/>
							  <addr>
								<streetAddressLine>$resultPatientDetail[street]</streetAddressLine>
								<streetAddressLine>$resultPatientDetail[street2]</streetAddressLine>
								<city>$resultPatientDetail[city]</city>
								<state>$resultPatientDetail[state]</state>
								<postalCode>$resultPatientDetail[postal_code]</postalCode>
								<country>US</country>
							  </addr>
								$XMLPartPatientHomePhone
								$XMLPartPatientWorkPhone
								$XMLPartPatientMobilePhone
								$XMLPartPatientEmail
							  <patient>
								<name>
								  <prefix>$resultPatientDetail[title]</prefix>
								  <given qualifier=\"CL\">$resultPatientDetail[fname]</given>
								  <given >$resultPatientDetail[mname]</given>
								  <family qualifier=\"BR\">$resultPatientDetail[lname]</family>
								</name>
								<administrativeGenderCode code=\"$patientGender\" displayName=\"$patientGenderDisplayName\" codeSystemName=\"HL7 AdministrativeGenderCodes\" codeSystem=\"2.16.840.1.113883.5.1\">
								  <originalText>AdministrativeGender codes are: M (Male), F (Female) or UN (Undifferentiated).</originalText>
								</administrativeGenderCode>
								<birthTime value=\"$patientDOB\"/>
								$XMLPartMaritalStatus
								<languageCommunication>
								  <templateId root=\"2.16.840.1.113883.3.88.11.32.2\"/>
								  <languageCode code=\"en-US\"/>						  
								</languageCommunication>
								$XMLPartSupportRelationGUARD						
							  </patient>
							  <providerOrganization>
								<id/>
								<name>$facName</name>
								<telecom value=\"tel:+1-$facPhone\" use=\"WP\"/>
								<addr>
									<streetAddressLine>$facStreet</streetAddressLine>
									<city>$facCity</city>
									<state>$facState</state>
									<postalCode>$facPostalCode</postalCode>
									<country>USA</country>
								</addr>
							  </providerOrganization>
							</patientRole>
						  </recordTarget>
						<author>    
							<time value=\"$today\"/>					
							<assignedAuthor>
								<id /> 												
								<addr>
									<streetAddressLine>$facProStreet</streetAddressLine>
									<city>$facProCity</city>
									<state>$facProState</state>
									<postalCode>$facProPostalCode</postalCode>
									<country>USA</country>
								</addr>     
								<telecom value=\"tel:+1-$facProPhone\" use=\"WP\"/>
								<assignedPerson>
									<name>
										<given>$userFName</given>
										<family>$userLName</family>
									</name>
								</assignedPerson>
								<representedOrganization>							
									<name>imwemr LLC</name>
									<telecom/>
									<addr/>
								</representedOrganization>
							</assignedAuthor>
						</author>				
						  <custodian>
							<assignedCustodian>
							  <representedCustodianOrganization>
								<id/>
								<name>$facName</name>
								<telecom value=\"tel:+1-$facPhone\" use=\"WP\"/>
								<addr>
									<streetAddressLine>$facStreet</streetAddressLine>
									<city>$facCity</city>
									<state>$facState</state>
									<postalCode>$facPostalCode</postalCode>
									<country>USA</country>
								</addr>
							  </representedCustodianOrganization>
							</assignedCustodian>
						  </custodian>
						  $XMLPartSupportRelationOTHER
						  <documentationOf>
							<serviceEvent classCode=\"PCPR\">
							  <effectiveTime>					  
								<low />
								<high value=\"$patientRegistrationDate\"/>
							  </effectiveTime>
							  $XMLPartPerformerPrimaryProvider
							  $XMLPartPerformerReferringPhysician
							</serviceEvent>
						  </documentationOf>
						  <component>
							<structuredBody>
							  
								$XMLPartComponent
								$XMLPartAllergies
								$XMLPartInsurance						
								$XMLPartMedication
								$XMLPartImmunization
								$XMLPartProblemList
								$XMLPartLabs
							</structuredBody>
						  </component>
						</ClinicalDocument>
						";
		}

		$ASEKEY = core_pt_secret_phrase($AESPatientID, $AESPatientLName, $AESPatientDOB);
	
		$rqCallFrom = $request['callFrom'];
		//if($rqCallFrom == "reportCCD"){		
		if($blPatAllergiesHave == true || $blPatMedicationsHave == true || $blPatProblemsHave == true || $blPatLabResultsHave == true || $strlabError == "" || $strRadError == ""){
			$file_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/xml/liaka_ccd.xml";
			file_put_contents($file_path,$laikaXML);		
			$aes = new AES($ASEKEY);	
			$encData = $aes->encrypt(file_get_contents($file_path));
			$timeVal = time();
			$encFileName = "imedic-ENC-".$timeVal;
			$fileToCreate = $fileToCreateHASH = "";
			$fileToCreate = 'imedic_files/'.$encFileName.".xml";
			$fileToCreate = write_html($encData,$fileToCreate);					
			$encDataHashVal = hash('sha256',file_get_contents($fileToCreate));
			$hashFileName = "imedic_files/imedic-HASH-".$timeVal.".txt";
			$fileToCreateHASH = write_html($encDataHashVal,$hashFileName);					
			
			$plainFileName = "imedic_files/imedic-HASH-imedic-PLAIN-".time().'.xml';
			$fileToCreatePlain = write_html(file_get_contents($file_path),$plainFileName);
			$encDataHashValPLAIN = hash('sha256',file_get_contents($fileToCreatePlain));
			$responseData = "";
			$responseData = $encDataHashVal."~~".base64_encode($fileToCreate);		
			$patFileName = $resultPatientDetail['lname']."_".$resultPatientDetail['fname'];
			$return_val = $responseData."~~".$encDataHashValPLAIN."~~".$ASEKEY."~~".base64_encode($fileToCreatePlain)."~~".$patFileName."~~".base64_encode($fileToCreateHASH)."##"."1";
			//$return_val = $responseData."~~".$encDataHashValPLAIN."~~".$ASEKEY."~~".$plainFileName."~~".$patFileName."~~".$fileToCreateHASH."##"."1";
		}
		elseif($strlabError != "" || $strRadError != ""){
			$strError = "";
			if($strlabError != ""){
				$strlabError = "Laboratory ".$strlabError;
			}
			if($strRadError != ""){
				$strRadError = "Radiology ".$strRadError;
			}
			$return_val = $strError = $strlabError."~~".$strRadError."##"."2";	
		}
		else{
			$intPatAllergiesHaveStatus = $intPatMedicationsHaveStatus = $intPatProblemsHaveStatus = $intPatLabResultsHaveStatus = "0";
			$return_val = $intPatAllergiesHaveStatus."~~".$intPatMedicationsHaveStatus."~~".$intPatProblemsHaveStatus."~~".$intPatLabResultsHaveStatus."##"."0";
		}
		
		return $return_val;
	}
	
	public function get_wv_xl_data($request){
		extract($request);
		$count = 1;
		$arrMedHxMedi = $arrMedHxAller = $arrMedHxProbList = array();
		if(isset($request['medications']) && $request['medications'] != "")
		$arrMedHxMedi = explode("~~",$request['medications']);

		if(isset($request['allergies']) && $request['allergies'] != "")
		$arrMedHxAller = explode("~~",$request['allergies']);

		if(isset($request['problem_list']) && $request['problem_list'] != "")
		$arrMedHxProbList = explode("~~",$request['problem_list']);

		if(isset($request['create_type']) && ($request['create_type'] == "attachment" || $request['create_type'] == "printing")){
		$arrData = json_decode($request['arrData'],1);
			if($request['app_services'] && isset($request['phyId']) && $request['phyId']!=""){
				$_SESSION['authId'] = $request['phyId'];
			}
		}
		
		$arrAllFiles = array(); 
		$arrOptionsExclude = explode(",",$request['ccdDocumentOptions']);
		$arrOptionsAll = array("mu_data_set","mu_data_set_medications","mu_data_set_problem_list","mu_data_set_allergies","mu_data_set_smoking","mu_data_set_ap","mu_data_set_superbill","mu_data_set_vs","mu_data_set_care_team_members","mu_data_set_lab", "provider_info", "location_info", "reason_for_visit", "diagnostic_tests_pending", "clinical_instruc", "future_appointment", "provider_referrals", "future_sch_test", "recommended_patient_decision_aids", "visit_medication_immu");

		$arrOptions = array_diff($arrOptionsAll, $arrOptionsExclude);
		
		foreach($arrData as $key=>$arr){
			$pat_id = $arr['pat_id'];
			$form_id = $arr['form_id'];
			if($form_id == "all")
			$form_id = "";
			$dos = $arr['dos'];
			$request['pid'] = $pat_id;
			$request['electronicDOSCCD'] = $form_id;

			if($request['pid'] != ""){
				$pid = $request['pid'];
				$form_id = $request['electronicDOSCCD'];
				$sql_cmt = "SELECT date_of_service FROM chart_master_table WHERE id = '".$form_id."'";
				$res_cmt = imw_query($sql_cmt);
				$row_cmt = imw_fetch_assoc($res_cmt);
				$dos = $row_cmt['date_of_service'];
			}

			$currentDate = date("YmdHis");
			$qry = "select patient_data.*,users.fname as ptProviderFName,users.mname as ptProviderMName,users.lname as ptProviderLName,users.user_npi as ptProviderNPI,
				refferphysician.Title as ptRefferPhyTitle,refferphysician.FirstName as ptRefferPhyFName,refferphysician.MiddleName as ptRefferPhyMName,
				refferphysician.LastName as ptRefferPhyLName,refferphysician.physician_phone as ptRefferPhyPhone
				from patient_data LEFT JOIN users on users.id = patient_data.providerID
				LEFT JOIN refferphysician ON refferphysician.physician_Reffer_id = patient_data.primary_care_id 
				where patient_data.id = '".$pid."'";
			$rsPatient = imw_query($qry);
			$rowPatient = imw_fetch_assoc($rsPatient);
				
			//Pt. Data
			$XMLpatient_data = '<recordTarget>';
			$XMLpatient_data .= '<patientRole>';
			if($rowPatient['ss'] != "")
			$XMLpatient_data .= '<id extension="'.$rowPatient['ss'].'" root="2.16.840.1.113883.4.1"/>';
			else
			$XMLpatient_data .= '<id root="2.16.840.1.113883.4.6"/>';
		  
			$XMLpatient_data .= '<addr use="HP">';
			if($rowPatient['street'] != "")
			$XMLpatient_data .= '<streetAddressLine>'.$rowPatient['street'].'</streetAddressLine>';
			else
			$XMLpatient_data .= '<streetAddressLine nullFlavor="NI"/>';
			if($rowPatient['street2'] != "")
			$XMLpatient_data .= '<streetAddressLine>'.$rowPatient['street2'].'</streetAddressLine>';
			else
			$XMLpatient_data .= '<streetAddressLine nullFlavor="NI"/>';
			if($rowPatient['city'] != "")
			$XMLpatient_data .= '<city>'.$rowPatient['city'].'</city>';
			else
			$XMLpatient_data .= '<city nullFlavor="NI"/>';
			if($rowPatient['state'] != "")
			$XMLpatient_data .= '<state>'.$rowPatient['state'].'</state>';
			else
			$XMLpatient_data .= '<state nullFlavor="NI"/>';
			if($rowPatient['postal_code'] != "")
			$XMLpatient_data .= '<postalCode>'.$rowPatient['postal_code'].'</postalCode>';
			else
			$XMLpatient_data .= '<postalCode nullFlavor="NI"/>';
			$XMLpatient_data .= '<country>US</country>';
			$XMLpatient_data .= '</addr>';
			 
			 if($rowPatient['phone_home'] != "")
			 $XMLpatient_data .= '<telecom value="tel:+1-'.core_phone_format($rowPatient['phone_home']).'" use="HP"/>';
			 else
			 $XMLpatient_data .= '<telecom nullFlavor="NI" use="HP"/>';
			 
			 if($rowPatient['phone_biz'] != "")
			 $XMLpatient_data .= '<telecom value="tel:+1-'.core_phone_format($rowPatient['phone_biz']).'" use="WP"/>';
			 if($rowPatient['phone_cell'] != "")
			 $XMLpatient_data .= '<telecom value="tel:+1-'.core_phone_format($rowPatient['phone_cell']).'" use="MC"/>';
			 if($rowPatient['email'] != "")
			 $XMLpatient_data .= '<telecom value="mailto:'.$rowPatient['email'].'"/>';
			 
			 $XMLpatient_data .= '<patient>';
			 $XMLpatient_data .= '<name>';
			 
			 if($rowPatient['suffix'] != ""){
			 $XMLpatient_data .= '<prefix>'.$rowPatient['title'].'</prefix>';
			 }
			 if($rowPatient['mname']!="")
			 $XMLpatient_data .= '<given>'.$rowPatient['mname'].'</given>';
			 $XMLpatient_data .= '<given qualifier="CL">'.$rowPatient['fname'].'</given>';
			 $XMLpatient_data .= '<family>'.$rowPatient['lname'].'</family>';
			  
			 if($rowPatient['suffix'] != ""){
			 $XMLpatient_data .= '<suffix>'.$rowPatient['suffix'].'</suffix>';
			 }
			
			$XMLpatient_data .= '</name>';
			
			$arrGender = array();
			$arrGender = $this->gender_srh(strtolower($rowPatient['sex']));
			if($arrGender['code']!="" && $arrGender['display_name']!=""){	
			$XMLpatient_data .= '<administrativeGenderCode code="'.$arrGender['code'].'" codeSystem="2.16.840.1.113883.5.1"
												displayName="'.$arrGender['display_name'].'" codeSystemName="AdministrativeGender"/>';
			}else{
			$XMLpatient_data .= '<administrativeGenderCode nullFlavor="NI"/>';	
			}
			$dob = str_replace("-","",$rowPatient['DOB']);
			if($dob != "00000000"){
			$XMLpatient_data .= '<birthTime value="'.$dob.'"/>';
			}else{
				$XMLpatient_data .= '<birthTime nullFlavor="NI"/>';
			}
			
			$arrMarried = array();
			$arrMarried = $this->marr_status_srh(strtolower($rowPatient['status']));
			if($arrMarried['code']!="" && $arrMarried['display_name']!=""){
			$XMLpatient_data .= '<maritalStatusCode code="'.$arrMarried['code'].'" displayName="'.$arrMarried['display_name'].'"
										codeSystem="2.16.840.1.113883.5.2"
										codeSystemName="MaritalStatus"/>';
			}
			$arrRace = array();
			$arrRace = $this->race_srh(strtolower($rowPatient['race']));
			if($arrRace['code']!="" && $arrRace['display_name']!=""){	
			$XMLpatient_data .= '<raceCode code="'.$arrRace['code'].'" displayName="'.$arrRace['display_name'].'"
							codeSystem="2.16.840.1.113883.6.238"
							codeSystemName="Race and Ethnicity - CDC"/>';
			}else{
			$XMLpatient_data .= '<raceCode nullFlavor="NI"/>';
			}
			$arrEthnicity = array();
			$arrEthnicity = $this->ethnicity_srh(strtolower($rowPatient['ethnicity']));
			if($arrEthnicity['code']!="" && $arrEthnicity['display_name']!=""){		
			$XMLpatient_data .= '<ethnicGroupCode code="'.$arrEthnicity['code'].'"
								displayName="'.$arrEthnicity['display_name'].'"
								codeSystem="2.16.840.1.113883.6.238"
								codeSystemName="Race and Ethnicity - CDC"/>';
			}else{
			$XMLpatient_data .= '<ethnicGroupCode nullFlavor="NI"/>';
			}
			$arrLanguage = array();
			$arrLanguage = $this->language_srh(strtolower($rowPatient['language']));	
			if($arrLanguage['code']!="" && $arrLanguage['display_name']!=""){					
			$XMLpatient_data .= '<languageCommunication>
								<languageCode code="'.$arrLanguage['code'].'"/>
								</languageCommunication>';
			}
			$XMLpatient_data .= '</patient>';
			$XMLpatient_data .= '</patientRole>';
			$XMLpatient_data .= '</recordTarget>';	

			// Team Members Data
			if(in_array('mu_data_set_care_team_members',$arrOptions) || in_array('provider_info',$arrOptions)){
					$sql_patient = "SELECT * FROM patient_data WHERE id = '".$pid."'";
					$result_patient = imw_query($sql_patient);
					$row_patient = imw_fetch_assoc($result_patient);
					$providerID = $row_patient['providerID'];
					
					$XML_documentationof_data = '<documentationOf>';
					$XML_documentationof_data .= '<serviceEvent classCode="PCPR">';
					$XML_documentationof_data .= '<effectiveTime>
												 <low value="'.$currentDate.'"/>
												 </effectiveTime>';
												 
					if(in_array('mu_data_set_care_team_members',$arrOptions) || in_array('provider_info',$arrOptions)){
						$qry_provider = "SELECT * FROM users WHERE id = '".$providerID."'";  // PRIMARY PHYSICIAN
						$res_provider = imw_query($qry_provider);
						if(imw_num_rows($res_provider) > 0){
							$row_provider = imw_fetch_assoc($res_provider);
							
							$XML_documentationof_data .= '<performer typeCode="PRF">';
							$XML_documentationof_data .= '<functionCode code="CP" displayName="Consulting Provider" codeSystem="2.16.840.1.113883.5.88" 	  																	
															codeSystemName="participationFunction"/>';
							$XML_documentationof_data .= '<assignedEntity>';
							$XML_documentationof_data .= '<!-- NPI 12345 -->';
							if($row_provider['user_npi'] != "")
							$XML_documentationof_data .= '<id extension="'.$row_provider['user_npi'].'" root="2.16.840.1.113883.4.6"/>';
							else
							$XML_documentationof_data .= '<id nullFlavor="NI"/>';
							
							
							if($row_provider['facility'] > 0){
								$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$row_provider['facility']."'";
							}
							else{
								$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
							}
							$res_facility = imw_query($qry_facility);
							$row_facility = imw_fetch_assoc($res_facility);
							
							$XML_documentationof_data .= '<addr use="WP">';
							if($row_facility['street'] != "")
							$XML_documentationof_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
							if($row_facility['city'] != "")
							$XML_documentationof_data .= '<city>'.$row_facility['city'].'</city>';
							if($row_facility['state'] != "")
							$XML_documentationof_data .= '<state>'.$row_facility['state'].'</state>';
							if($row_facility['postal_code'] != "")
							$XML_documentationof_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
							$XML_documentationof_data .= '<country>US</country>';
							$XML_documentationof_data .= '</addr>';
							if($row_facility['phone'] != "")
							$XML_documentationof_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
							$XML_documentationof_data .= '<assignedPerson>';
							$XML_documentationof_data .= '<name>';
							$XML_documentationof_data .= '<given>'.$row_provider['fname'].'</given>';
							$XML_documentationof_data .= '<family>'.$row_provider['lname'].'</family>';
							$XML_documentationof_data .= '</name>';
							$XML_documentationof_data .= '</assignedPerson>';
							$XML_documentationof_data .= '</assignedEntity>';
							$XML_documentationof_data .= '</performer>';
						}
						else{
							$XML_documentationof_data .= '<performer typeCode="PRF">';
							$XML_documentationof_data .= '<functionCode code="CP" displayName="Consulting Provider" codeSystem="2.16.840.1.113883.5.88" 	  																	
															codeSystemName="participationFunction"/>';
							$XML_documentationof_data .= '<assignedEntity>';
							$XML_documentationof_data .= '<!-- NPI 12345 -->';
							$XML_documentationof_data .= '<id nullFlavor="NI"/>';
							$XML_documentationof_data .= '<addr>';
							$XML_documentationof_data .= '<streetAddressLine nullFlavor="NI"/>';
							$XML_documentationof_data .= '<city nullFlavor="NI"/>';
							$XML_documentationof_data .= '<state nullFlavor="NI"/>';
							$XML_documentationof_data .= '<postalCode nullFlavor="NI"/>';
							$XML_documentationof_data .= '<country nullFlavor="NI"/>';
							$XML_documentationof_data .= '</addr>';
							$XML_documentationof_data .= '<telecom nullFlavor="NI"/>';								
							$XML_documentationof_data .= '<assignedPerson>';
							$XML_documentationof_data .= '<name>';
							$XML_documentationof_data .= '<given nullFlavor="NI"/>';
							$XML_documentationof_data .= '<family nullFlavor="NI"/>';
							$XML_documentationof_data .= '</name>';
							$XML_documentationof_data .= '</assignedPerson>';
							$XML_documentationof_data .= '</assignedEntity>';
							$XML_documentationof_data .= '</performer>';
						}
					}
					if(in_array('mu_data_set_care_team_members',$arrOptions)){
						$qry_reff = "SELECT * FROM refferphysician WHERE physician_Reffer_id = '".$row_patient['primary_care_id']."'";  // REFERRING PHYSICIAN
						$res_reff = imw_query($qry_reff);
						if(imw_num_rows($res_reff) > 0){
					$row_reff = imw_fetch_assoc($res_reff);
					
					$XML_documentationof_data .= '<performer typeCode="PRF">';
					$XML_documentationof_data .= '<functionCode code="RP" displayName="Referring Provider" codeSystem="2.16.840.1.113883.5.88" 	  																	
													codeSystemName="participationFunction"/>';
					$XML_documentationof_data .= '<assignedEntity>';
					$XML_documentationof_data .= '<!-- NPI 12345 -->';
					if($row_reff['NPI'] != "")
					$XML_documentationof_data .= '<id extension="'.$row_reff['NPI'].'" root="2.16.840.1.113883.4.6"/>';
					else
					$XML_documentationof_data .= '<id nullFlavor="NI"/>';
					
					if($row_reff['facility'] > 0){
						$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$row_reff['facility']."'";
					}
					else{
						$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
					}
					$res_facility = imw_query($qry_facility);
					$row_facility = imw_fetch_assoc($res_facility);
					
					$XML_documentationof_data .= '<addr use="WP">';
					if($row_facility['street'] != "")
					$XML_documentationof_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
					if($row_facility['city'] != "")
					$XML_documentationof_data .= '<city>'.$row_facility['city'].'</city>';
					if($row_facility['state'] != "")
					$XML_documentationof_data .= '<state>'.$row_facility['state'].'</state>';
					if($row_facility['postal_code'] != "")
					$XML_documentationof_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
					$XML_documentationof_data .= '<country>US</country>';
					$XML_documentationof_data .= '</addr>';
					if($row_facility['phone'] != "")
					$XML_documentationof_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
					$XML_documentationof_data .= '<assignedPerson>';
					$XML_documentationof_data .= '<name>';
					if($row_reff['Title']!="")
					$XML_documentationof_data .= '<prefix>'.$row_reff['Title'].'</prefix>';
					$XML_documentationof_data .= '<given>'.$row_reff['FirstName'].'</given>';
					$XML_documentationof_data .= '<family>'.$row_reff['LastName'].'</family>';
					$XML_documentationof_data .= '</name>';
					
					$XML_documentationof_data .= '</assignedPerson>';
					$XML_documentationof_data .= '</assignedEntity>';
					$XML_documentationof_data .= '</performer>';
					}
						$qry_reff = "SELECT * FROM refferphysician WHERE physician_Reffer_id = '".$row_patient['primary_care_phy_id']."'"; // PCP PHYSICIAN
						$res_reff = imw_query($qry_reff);
						if(imw_num_rows($res_reff) > 0){
					$row_reff = imw_fetch_assoc($res_reff);
					
					$XML_documentationof_data .= '<performer typeCode="PRF">';
					$XML_documentationof_data .= '<functionCode code="PCP" displayName="Primary Care Physician" codeSystem="2.16.840.1.113883.5.88" 	  																	
													codeSystemName="participationFunction"/>';
					$XML_documentationof_data .= '<assignedEntity>';
					$XML_documentationof_data .= '<!-- NPI 12345 -->';
					if($row_reff['NPI'] != "")
					$XML_documentationof_data .= '<id extension="'.$row_reff['NPI'].'" root="2.16.840.1.113883.4.6"/>';
					else
					$XML_documentationof_data .= '<id nullFlavor="NI"/>';
					
					if($row_reff['facility'] > 0){
						$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$row_reff['facility']."'";
					}
					else{
						$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
					}
					$res_facility = imw_query($qry_facility);
					$row_facility = imw_fetch_assoc($res_facility);
					
					$XML_documentationof_data .= '<addr use="WP">';
					if($row_facility['street'] != "")
					$XML_documentationof_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
					if($row_facility['city'] != "")
					$XML_documentationof_data .= '<city>'.$row_facility['city'].'</city>';
					if($row_facility['state'] != "")
					$XML_documentationof_data .= '<state>'.$row_facility['state'].'</state>';
					if($row_facility['postal_code'] != "")
					$XML_documentationof_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
					$XML_documentationof_data .= '<country>US</country>';
					$XML_documentationof_data .= '</addr>';
					if($row_facility['phone'] != "")
					$XML_documentationof_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
					$XML_documentationof_data .= '<assignedPerson>';
					$XML_documentationof_data .= '<name>';
					if($row_reff['Title']!="")
					$XML_documentationof_data .= '<prefix>'.$row_reff['Title'].'</prefix>';
					$XML_documentationof_data .= '<given>'.$row_reff['FirstName'].'</given>';
					$XML_documentationof_data .= '<family>'.$row_reff['LastName'].'</family>';
					$XML_documentationof_data .= '</name>';
					
					$XML_documentationof_data .= '</assignedPerson>';

					$XML_documentationof_data .= '</assignedEntity>';
					$XML_documentationof_data .= '</performer>';
					}
					}
					
					if(in_array('mu_data_set_care_team_members',$arrOptions)){
						$qry_provider = "SELECT * FROM users WHERE id = '".$row_patient['assigned_nurse']."'";  // PRIMARY PHYSICIAN
						$res_provider = imw_query($qry_provider);
						if(imw_num_rows($res_provider) > 0){
							$row_provider = imw_fetch_assoc($res_provider);
							
							$XML_documentationof_data .= '<performer typeCode="PRF">';
							$XML_documentationof_data .= '<functionCode code="NASST" displayName="nurse assistant" codeSystem="2.16.840.1.113883.5.88" 	  																	
															codeSystemName="participationFunction"/>';
							$XML_documentationof_data .= '<assignedEntity>';
							$XML_documentationof_data .= '<!-- NPI 12345 -->';
							if($row_provider['user_npi'] != "")
							$XML_documentationof_data .= '<id extension="'.$row_provider['user_npi'].'" root="2.16.840.1.113883.4.6"/>';
							else
							$XML_documentationof_data .= '<id nullFlavor="NI"/>';
							
							
							if($row_provider['facility'] > 0){
								$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$row_provider['facility']."'";
							}
							else{
								$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
							}
							$res_facility = imw_query($qry_facility);
							$row_facility = imw_fetch_assoc($res_facility);
							
							$XML_documentationof_data .= '<addr use="WP">';
							if($row_facility['street'] != "")
							$XML_documentationof_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
							if($row_facility['city'] != "")
							$XML_documentationof_data .= '<city>'.$row_facility['city'].'</city>';
							if($row_facility['state'] != "")
							$XML_documentationof_data .= '<state>'.$row_facility['state'].'</state>';
							if($row_facility['postal_code'] != "")
							$XML_documentationof_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
							$XML_documentationof_data .= '<country>US</country>';
							$XML_documentationof_data .= '</addr>';
							if($row_facility['phone'] != "")
							$XML_documentationof_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
							$XML_documentationof_data .= '<assignedPerson>';
							$XML_documentationof_data .= '<name>';
							$XML_documentationof_data .= '<given>'.$row_provider['fname'].'</given>';
							$XML_documentationof_data .= '<family>'.$row_provider['lname'].'</family>';
							$XML_documentationof_data .= '</name>';
							$XML_documentationof_data .= '</assignedPerson>';
							$XML_documentationof_data .= '</assignedEntity>';
							$XML_documentationof_data .= '</performer>';
						}	
					}
				$XML_documentationof_data .= '</serviceEvent>';
				$XML_documentationof_data .= '</documentationOf>';
			}

			//Refferal to other prov.
			if(in_array('provider_referrals',$arrOptions)){
				if($form_id!=""){
					$sql_cmt = "SELECT date_of_service FROM chart_master_table WHERE id = '".$form_id."'";
					$res_cmt = imw_query($sql_cmt);
					$row_cmt = imw_fetch_assoc($res_cmt);
					$dos = $row_cmt['date_of_service'];
					
					$sql = "SELECT * FROM chart_schedule_test_external WHERE patient_id = '".$pid."'";
					if($form_id != ""){
						$sql .= " AND schedule_date >= '".$dos."'";
					}else{
						$sql .= " AND schedule_date >= '".date('Y-m-d')."'";
					}
					$sql .= " AND deleted_by = '0'";
					$sql .= " AND appoint_test = 'Referral'";
					$res = imw_query($sql);
					$row = imw_fetch_assoc($res);
					
					if($row['reff_phy'] !=""){
					$arr = explode(",",$row['reff_phy']);
					$arrFirst = explode(" ",trim($arr[0]));
					$arrSecond = explode(" ",trim($arr[1]));
					if(count($arrFirst)>1){
						$title = $arrFirst[0];
						$fname = $arrFirst[1];
					}else{
						$fname = $arrFirst[0];
					}
					
					if(count($arrSecond)>1){
						$lname = $arrSecond[0];
						$mname = $arrSecond[1];
					}else{
						$lname = $arrSecond[0];
					}
					$XML_referral_to_providers = '<componentOf>
													<encompassingEncounter>
														<id extension="'.$form_id.'" root="2.16.840.1.113883.4.6"/>
														<effectiveTime value="'.str_replace("-","",$row['schedule_date']).'" />
														<encounterParticipant typeCode="ATND">
															<assignedEntity>
																<id root="2.16.840.1.113883.4.6"/>
																<assignedPerson>
																	<name>';
											if(isset($title) && $title!="")
											$XML_referral_to_providers .= '<prefix>'.$title.'</prefix>';
											if(isset($fname) && $fname!="")
											$XML_referral_to_providers .= '<given>'.$fname.'</given>';
											if(isset($lname) && $lname!="")
											$XML_referral_to_providers .= '<family>'.$lname.'</family>';
											
											$XML_referral_to_providers .='</name>
																</assignedPerson>
															</assignedEntity>
														</encounterParticipant>
													</encompassingEncounter>
												</componentOf>';
					}
				}
			}

			//Begin author data
			$qry_user = "select * from users where id = '".$_SESSION['authId']."'";
			$res_user = imw_query($qry_user);
			$row_user = imw_fetch_assoc($res_user);
			$XML_author_data = '<author>';
			$XML_author_data .= '<time value="'.$currentDate.'"/>';
			$XML_author_data .= '<assignedAuthor>';
			if($row_user['user_npi'] != "")
			$XML_author_data .= '<id extension="'.$row_user['user_npi'].'" root="2.16.840.1.113883.4.6"/>';
			else
			$XML_author_data .= '<id root="2.16.840.1.113883.4.6"/>';
			
			if($row_user['facility'] > 0){
				$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$row_user['facility']."'";
			}
			else{
				$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
			}
			$res_facility = imw_query($qry_facility);
			$row_facility = imw_fetch_assoc($res_facility);
			
			$XML_author_data .= '<addr use="WP">';
			if($row_facility['street'] != "")
			$XML_author_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
			if($row_facility['city'] != "")
			$XML_author_data .= '<city>'.$row_facility['city'].'</city>';
			if($row_facility['state'] != "")
			$XML_author_data .= '<state>'.$row_facility['state'].'</state>';
			if($row_facility['postal_code'] != "")
			$XML_author_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
			$XML_author_data .= '<country>US</country>';
			$XML_author_data .= '</addr>';
			
			if($row_facility['phone'] != "")
			$XML_author_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
			 else
			 $XML_author_data .= '<telecom nullFlavor="NI" use="WP"/>';
			$XML_author_data .= '<assignedPerson>';
			$XML_author_data .= '<name>';
			if($row_user['mname'] != "")
			$XML_author_data .= '<given>'.$row_user['mname'].'</given>';
			$XML_author_data .= '<given qualifier="CL">'.$row_user['fname'].'</given>';
			$XML_author_data .= '<family>'.$row_user['lname'].'</family>';
			$XML_author_data .= '</name>';
			
			$XML_author_data .= '</assignedPerson>';
			$XML_author_data .= '</assignedAuthor>';
			$XML_author_data .= '</author>';

			//Begin Data enterer data
			$qry_user = "select * from users where id = '".$_SESSION['authId']."'";
			$res_user = imw_query($qry_user);
			$row_user = imw_fetch_assoc($res_user);
			$XML_data_enterer_data ='<dataEnterer>';
			$XML_data_enterer_data .='<assignedEntity>';
			if($row_user['user_npi'] != "")
			$XML_data_enterer_data .='<id root="2.16.840.1.113883.19.5" extension="'.$row_user['user_npi'].'"/>';
			else
			$XML_data_enterer_data .= '<id nullFlavor="NAV"/>';
			
			if($row_user['facility'] > 0){
				$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$row_user['facility']."'";
			}
			else{
				$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
			}
			$res_facility = imw_query($qry_facility);
			$row_facility = imw_fetch_assoc($res_facility);
			
			$XML_data_enterer_data .= '<addr use="WP">';
			if($row_facility['street'] != "")
			$XML_data_enterer_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
			if($row_facility['city'] != "")
			$XML_data_enterer_data .= '<city>'.$row_facility['city'].'</city>';
			if($row_facility['state'] != "")
			$XML_data_enterer_data .= '<state>'.$row_facility['state'].'</state>';
			if($row_facility['postal_code'] != "")
			$XML_data_enterer_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
			$XML_data_enterer_data .= '<country>US</country>';
			$XML_data_enterer_data .= '</addr>';
			if($row_facility['phone'] != "")
			$XML_data_enterer_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
			else
			$XML_data_enterer_data .= '<telecom nullFlavor="NI" use="WP"/>';
			$XML_data_enterer_data .='<assignedPerson>';
			$XML_data_enterer_data .='<name>';
			$XML_data_enterer_data .='<given>'.$row_user['fname'].'</given>';
			$XML_data_enterer_data .='<family>'.$row_user['lname'].'</family>';
			$XML_data_enterer_data .='</name>';
			
			$XML_data_enterer_data .='</assignedPerson>';
			$XML_data_enterer_data .='</assignedEntity>';
			$XML_data_enterer_data .='</dataEnterer>';	

			//Begin custodian data
			$facility = "";
			if(isset($form_id) && $form_id!=""){
				$qry = "SELECT sa.sa_facility_id  as facility
						FROM schedule_appointments sa
						JOIN chart_master_table cmt ON cmt.date_of_service = sa.sa_app_start_date
						WHERE sa.sa_patient_id ='".$pid."' 
							AND cmt.id = '".$form_id."'";
				$res = imw_query($qry);						
				$row = imw_fetch_assoc($res);
				$facility = $row['facility'];
				
			}
			if($facility == "" || $facility == "0"){
				$qry = "select default_facility as pos_facility from patient_data where id = '".$pid."'";
				$res = imw_query($qry);
				$row = imw_fetch_assoc($res);
				$pos_facility = $row['pos_facility'];
				$qry = "SELECT id as facility 
						FROM facility 
						WHERE fac_prac_code = '".$pos_facility."'
						";
				$res = imw_query($qry);		
				$row = imw_fetch_assoc($res);
				$facility = $row['facility'];
			}
			if($facility > 0){
				$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$facility."'";
			}
			else{
				$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
			}
			$res_facility = imw_query($qry_facility);
			$row_facility = imw_fetch_assoc($res_facility);
			
			$XML_custodian_data = '<custodian>';
			$XML_custodian_data .= '<assignedCustodian>';
			$XML_custodian_data .= '<representedCustodianOrganization>';
			$XML_custodian_data .= '<id root="1.1.1.1.1.1.1.1.2"/>';
			if($row_facility['name'] != "")
			$XML_custodian_data .= '<name>'.htmlentities($row_facility['name']).'</name>';
			
			if($row_facility['phone'] != "")
			$XML_custodian_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
			else
			$XML_custodian_data .= '<telecom nullFlavor="NI"/>';
			$XML_custodian_data .= '<addr>';
			if($row_facility['street'] != "")
			$XML_custodian_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
			if($row_facility['city'] != "")
			$XML_custodian_data .= '<city>'.$row_facility['city'].'</city>';
			if($row_facility['state'] != "")
			$XML_custodian_data .= '<state>'.$row_facility['state'].'</state>';
			if($row_facility['postal_code'] != "")
			$XML_custodian_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
			$XML_custodian_data .= '<country>US</country>';
			$XML_custodian_data .= '</addr>';
			
			$XML_custodian_data .= '</representedCustodianOrganization>';
			$XML_custodian_data .= '</assignedCustodian>';
			$XML_custodian_data .= '</custodian>';	
			
			//Begin social history section	
			if(in_array('mu_data_set_smoking',$arrOptions)){
				$qry = "SELECT smoking_status FROM social_history WHERE patient_id = '".$pid."'";		
				$row_social = imw_fetch_assoc(imw_query($qry));					 
				$arrTmp = explode('/',$row_social['smoking_status']);
				$smoking_status = $arrTmp[1];
				$arrSmoking = array();
				$arrSmoking = $this->smoking_status_srh(strtolower($smoking_status));
				$XML_social_history_section = '<component>';
				$XML_social_history_section .= '<section>';
				$XML_social_history_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.17"/>';
				$XML_social_history_section .= '<code code="29762-2" codeSystem="2.16.840.1.113883.6.1" displayName="Social History"/>';
				$XML_social_history_section .= '<title>SOCIAL HISTORY</title>';
				$XML_social_history_section .= '<text>';
				$XML_social_history_section .= ' <table border = "1" width = "100%">
												<thead>
										<tr>
											<th>Social History Element</th>
											<th>Description</th>
										</tr></thead><tbody>';
				if($arrSmoking['code']!="" && $arrSmoking['display_name']!=""){						
				$XML_social_history_section .='
										<tr>
											<td>Smoking Status</td>
											<td>'.$arrSmoking['display_name'].' (SNOMED-CT: '.$arrSmoking['code'].')</td>
										</tr>
									';
				}
									
				$XML_social_history_section .= '</tbody></table>';
				$XML_social_history_section .= '</text>';
						/* BEGIN SMOKING STATUS ENTRY */
						$XML_smoking_status_entry =	'<entry>';
						$XML_smoking_status_entry .= '<observation classCode="OBS" moodCode="EVN">';
						$XML_smoking_status_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.78"/>';
						$XML_smoking_status_entry .= '<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4"/>';
						$XML_smoking_status_entry .= '<statusCode code="completed"/>';
						$XML_smoking_status_entry .= '<effectiveTime>';
						$XML_smoking_status_entry .= '<low nullFlavor="NI"/>';
						$XML_smoking_status_entry .= '</effectiveTime>';
						if($arrSmoking['code']!="" && $arrSmoking['display_name']!=""){	
						$XML_smoking_status_entry .= '<value xsi:type="CD" code="'.$arrSmoking['code'].'"
													  displayName="'.$arrSmoking['display_name'].'"
													  codeSystem="2.16.840.1.113883.6.96"/>';
						}
						$XML_smoking_status_entry .= '</observation>';
						$XML_smoking_status_entry .= '</entry>';
						/* END SMOKING STATUS ENTRY */
				$XML_social_history_section .= $XML_smoking_status_entry;
				$XML_social_history_section .= '</section>';
				$XML_social_history_section .= '</component>';
			}

			//Begin medications section
			if(in_array('mu_data_set_medications',$arrOptions)){
				$XML_medication_section = '<component>';
				$XML_medication_section .= '<section>';
				$XML_medication_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.1.1"/>';
				$XML_medication_section .= '<code code="10160-0" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="History of medications"/>';
				$XML_medication_section .= '<title>Medications</title>';
				$XML_medication_section .= '<text>';
				$XML_medication_section .= '<table border = "1" width = "100%">';
				$XML_medication_section .= '<thead>
										<tr>
											<th>Medication</th>
											<th>Start Date</th>
											<th>End Date</th>
											<th>Route</th>
											<th>Dose</th>
											<th>Status</th>
											<th>Fill Instructions</th>
										</tr>
									</thead>';
				$XML_medication_section .= ' <tbody>';
				$arrType = array("1","4");
				$arrMedication = $this->get_medical_data($form_id,$arrType ,$pid);
				$flag = 0;
				if(count($arrMedication)>0){
					foreach($arrMedication as $medication){	
						if($medication['ccda_code'] == ""){
							$arrCCDA = $this->getRXNormCode($medication['title']);
							$ccda_code = $arrCCDA['ccda_code'];
						}else{
							$ccda_code = $medication['ccda_code'];
						}
						if(!in_array($medication['title'],$arrMedHxMedi)){
						$flag = 1;	
						$XML_medication_section .= '<tr>
													<td>
														<content ID = "Med'.$medication['id'].'">'.htmlentities($medication['title']).'    [RxNorm: '.$ccda_code.']</content>
													</td>
													<td>';
						$XML_medication_section .= ($medication['begdate']!="" && $medication['begdate']!='0000-00-00')?date('M d,Y',strtotime($medication['begdate'])):"";
						$XML_medication_section .='</td>
														 <td>';
						$XML_medication_section .=($medication['enddate']!="" && $medication['enddate']!='0000-00-00')?date('M d,Y',strtotime($medication['enddate'])):"";
						$XML_medication_section .='</td>
														<td ID = "MEDROUTE'.$medication['id'].'">'.$this->getSite($medication['sites']).'</td>
														<td ID = "MEDFORM'.$medication['id'].'">'.$medication['destination'].'</td>
														<td>'.$medication['allergy_status'].'</td>
														<td ID = "Instruct'.$medication['id'].'">'.htmlentities($medication['med_comments']).'</td>
													</tr>
												';	
						}
					}
				}
				if($flag == 0)
				{
					$XML_medication_section .= ' <tr><td></td></tr>';
				}
				$XML_medication_section .= ' </tbody>';
				$XML_medication_section .= '</table>';
				$XML_medication_section .= '</text>';
						$arrType = array("1","4");
						$arrMedication = $this->get_medical_data($form_id,$arrType ,$pid);
						$flag = 0;
						if(count($arrMedication)>0){
						foreach($arrMedication as $medication){
							
							if(!in_array($medication['title'],$arrMedHxMedi)){
								$flag = 1;
								
							if($medication['ccda_code']!=""){
								$arrCCDA = $this->getRXNorm_by_code($medication['ccda_code']);
								$ccda_code_med = $medication['ccda_code'];
								$ccda_display_name_med = $medication['title'];	
							}else{
								$arrCCDA = $this->getRXNormCode($medication['title']);
								if(count($arrCCDA)>0){
								$ccda_code_med = $arrCCDA['ccda_code'];
								$ccda_display_name_med = $arrCCDA['ccda_display_name'];
								}
								
							}
							
						/*  BEGIN MEDICATION ENTRY  */
							$XML_medication_activity_entry = '<entry typeCode="DRIV">';
							$XML_medication_activity_entry .= '<substanceAdministration classCode="SBADM" moodCode="EVN">';
							$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.16"/>';
							$XML_medication_activity_entry .= '<id nullFlavor="NI"/>';
							$XML_medication_activity_entry .= '<text>';
							
							$XML_medication_activity_entry .= '<reference value="#Med'.$medication['id'].'"/>'.htmlentities($medication['title']).'    [RxNorm: '.$ccda_code_med.']';
							$XML_medication_activity_entry .= '</text>';
							$XML_medication_activity_entry .= '<statusCode code="completed"/>';
							$XML_medication_activity_entry .= '<effectiveTime xsi:type="IVL_TS">';
							if($medication['begdate'] !="" && $medication['begdate']!="0000-00-00")
							$XML_medication_activity_entry .= '<low value="'.str_replace("-","",$medication['begdate']).'"/>';
							else 
							$XML_medication_activity_entry .= '<low nullFlavor="NI"/>';
							if($medication['enddate'] !="" && $medication['enddate']!="0000-00-00")
							$XML_medication_activity_entry .= '<high value="'.str_replace("-","",$medication['enddate']).'"/>';
							else 
							$XML_medication_activity_entry .= '<high nullFlavor="NI"/>';
							$XML_medication_activity_entry .= '</effectiveTime>';
							
							$medDosage = trim($medication['destination']);
							$arrMedDosage = preg_split("/(?<=\d)(?=[a-zA-Z])|(?<=[a-zA-Z])(?=\d)/",preg_replace('/\s/','',$medDosage));
							$medDosageVal = $arrMedDosage[0];
							$medDosageUnit = $arrMedDosage[1];
							/* DYNAMIC ROUTE Medication Route FDA Value Set :: Code System(s): National Cancer Institute (NCI) Thesaurus*/
							if($medication['sites'] == 1 || $medication['sites'] == 2 || $medication['sites'] == 3 )
							$XML_medication_activity_entry .= '<routeCode code="C38287" codeSystem="2.16.840.1.113883.3.26.1.1"
																codeSystemName="National Cancer Institute (NCI) Thesaurus"
																displayName="OPHTHALMIC"/>';
							else if($medication['sites'] == 4)
							$XML_medication_activity_entry .= '<routeCode code="C38288" codeSystem="2.16.840.1.113883.3.26.1.1"
																codeSystemName="National Cancer Institute (NCI) Thesaurus"
																displayName="ORAL"/>';
							if($medication['sites'] == 1){
							$XML_medication_activity_entry .= '<approachSiteCode code="362503005" codeSystem="2.16.840.1.113883.6.96"
																codeSystemName="SNOMED CT"
																displayName="Entire left eye (body structure)"/>';
							}else if($medication['sites'] == 2){ // RIGHT EYE OD
							$XML_medication_activity_entry .= '<approachSiteCode code="362502000" codeSystem="2.16.840.1.113883.6.96"
																codeSystemName="SNOMED CT"
																displayName="Entire right eye (body structure)"/>';
							}
							else if($medication['sites'] == 3){ // BOTH EYES OU
							$XML_medication_activity_entry .= '<approachSiteCode code="244486005" codeSystem="2.16.840.1.113883.6.96"
																codeSystemName="SNOMED CT"
																displayName="Entire eye (body structure)"/>';
							}
							else if($medication['sites'] == 4){ // ORAL PO
							$XML_medication_activity_entry .= '<approachSiteCode code="26643006" codeSystem="2.16.840.1.113883.6.96"
																codeSystemName="SNOMED CT"
																displayName="taking by mouth"/>';
							}
							if($medDosageVal > 0 && $medDosageUnit != "")
							$XML_medication_activity_entry .= '<doseQuantity value="'.trim($medDosageVal).'" unit="'.trim($medDosageUnit).'"/>';
							else
							$XML_medication_activity_entry .= '<doseQuantity nullFlavor="NI"/>';
							$XML_medication_activity_entry .= '<consumable>';
							$XML_medication_activity_entry .= '<manufacturedProduct classCode="MANU">';
							$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.23"/>';
							$XML_medication_activity_entry .= '<manufacturedMaterial>';
															/* DYNAMIC MEDICATION CODE FROM RXNORM Medication Clinical Drug  */
								$XML_medication_activity_entry .= '<code code="'.$ccda_code_med.'"
																codeSystem="2.16.840.1.113883.6.88"
																codeSystemName="RxNorm"
																displayName="'.$ccda_display_name_med.'">';
							$XML_medication_activity_entry .= '<originalText>
																<reference value = "#Med'.$medication['id'].'"/>
																</originalText>';
							$XML_medication_activity_entry .= '</code>';
							$XML_medication_activity_entry .= '</manufacturedMaterial>';
							$XML_medication_activity_entry .= '</manufacturedProduct>';
							$XML_medication_activity_entry .= '</consumable>';
							$XML_medication_activity_entry .= '<entryRelationship typeCode = "REFR">
																<supply classCode = "SPLY" moodCode = "INT">
																	<templateId root = "2.16.840.1.113883.10.20.22.4.17"/>
																	<id nullFlavor = "NI"/>
																	<statusCode code = "completed"/>
		<effectiveTime xsi:type="IVL_TS">';
								if($medication['begdate']!="" && $medication['begdate']!='0000-00-00')
								$XML_medication_activity_entry .= '<low value="'.str_replace("-","",$medication['begdate']).'"/>';
								else
								$XML_medication_activity_entry .= '<low nullFlavor = "NI"/>';
								if($medication['enddate']!="" && $medication['enddate']!='0000-00-00')
								$XML_medication_activity_entry .= '<high value="'.str_replace("-","",$medication['enddate']).'" />';
								else
								$XML_medication_activity_entry .= '<high nullFlavor = "NI"/>';
								$XML_medication_activity_entry .= '</effectiveTime>
																		 <product>
													<manufacturedProduct classCode = "MANU">
														<templateId root = "2.16.840.1.113883.10.20.22.4.23"/>
														<id nullFlavor="NI"/>
														<manufacturedMaterial>
															<code code = "'.$ccda_code_med.'" codeSystem = "2.16.840.1.113883.6.88" displayName = "'.$ccda_display_name_med.'" codeSystemName = "RxNorm">
																<originalText>
																	<reference value = "#Med'.$medication['id'].'"/>
																</originalText>
															</code>
														</manufacturedMaterial>
													</manufacturedProduct>
												</product>
																		<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																			<act classCode = "ACT" moodCode = "INT">
																				<templateId root = "2.16.840.1.113883.10.20.22.4.20"/>
																				<code code = "409073007" codeSystem = "2.16.840.1.113883.6.96" displayName = "Instruction"/>
																				<text>
																					<reference value = "#Instruct'.$medication['id'].'"/>';
								$XML_medication_activity_entry .= htmlentities($medication['med_comments']);
								$XML_medication_activity_entry .= '</text>
																				<statusCode code = "completed"/>
																			</act>
																		</entryRelationship>
																	</supply>
																</entryRelationship>';
								$XML_medication_activity_entry .= '</substanceAdministration>';
								$XML_medication_activity_entry .= '</entry>';
								$XML_medication_section .= $XML_medication_activity_entry;
							}
						
						}
						}
							
						if($flag == 0){
						/*  BEGIN MEDICATION ENTRY  */
							$XML_medication_activity_entry = '<entry typeCode="DRIV">';
							$XML_medication_activity_entry .= '<substanceAdministration classCode="SBADM" moodCode="EVN">';
							$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.16"/>';
							$XML_medication_activity_entry .= '<id nullFlavor="NI"/>';
							$XML_medication_activity_entry .= '<statusCode code="completed"/>';
							$XML_medication_activity_entry .= '<effectiveTime xsi:type="IVL_TS">';
							$XML_medication_activity_entry .= '<low nullFlavor="NI"/>';
							$XML_medication_activity_entry .= '<high nullFlavor="NI"/>';
							$XML_medication_activity_entry .= '</effectiveTime>';
							
							$XML_medication_activity_entry .= '<doseQuantity nullFlavor="NI"/>';
							$XML_medication_activity_entry .= '<consumable>';
							$XML_medication_activity_entry .= '<manufacturedProduct classCode="MANU">';
							$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.23"/>';
							$XML_medication_activity_entry .= '<manufacturedMaterial>';
							$XML_medication_activity_entry .= '<code nullFlavor="NI">';
							$XML_medication_activity_entry .= '</code>';
							$XML_medication_activity_entry .= '</manufacturedMaterial>';
							$XML_medication_activity_entry .= '</manufacturedProduct>';
							$XML_medication_activity_entry .= '</consumable>';
							$XML_medication_activity_entry .= '</substanceAdministration>';
							$XML_medication_activity_entry .= '</entry>';
							$XML_medication_section .= $XML_medication_activity_entry;
						
						}
						/*  END MEDICATION ENTRY*/
				$XML_medication_section .= '</section>';
				$XML_medication_section .= '</component>';
			}

			//Begin medications administered section
			if(in_array('visit_medication_immu',$arrOptions)){
				$qry_list = "";
				if(isset($form_id) && $form_id != ""){
					$qry = "SELECT date_of_service from chart_master_table where id='".$form_id."'";
					$res = imw_query($qry);
					$row = imw_fetch_assoc($res);
					
					$qry_list = "SELECT * FROM lists
							WHERE pid = '".$pid."' 
								AND begdate = '".$row['date_of_service']."'
								AND type IN (1,4)
								AND allergy_status = 'Administered'
							";
				}
				$res_list = imw_query($qry_list);
				$XML_medication_admin_section = '<component>
												<section>
												<templateId root="2.16.840.1.113883.10.20.22.2.38" />
												<code code="29549-3"
												codeSystem="2.16.840.1.113883.6.1"
												codeSystemName="LOINC"
												displayName="MEDICATIONS ADMINISTERED" />
												<title>Medications Administered</title>';
				$XML_medication_admin_section .= '<text>';
				$XML_medication_admin_section .= '<table border = "1" width = "100%">';
				$XML_medication_admin_section .= '<thead>
												<tr>
												<th>Medication</th>
												<th>Start Date</th>
												<th>End Date</th>
												<th>Route</th>
												<th>Dose</th>
												<th>Status</th>
												<th>Fill Instructions</th>
												</tr>
												</thead>';	
				$XML_medication_admin_section .= ' <tbody>';
				if(imw_num_rows($res_list)>0){
					while($medication = imw_fetch_assoc($res_list)){	
						if($medication['ccda_code'] == ""){
							$arrCCDA = $this->getRXNormCode($medication['title']);
							$ccda_code = $arrCCDA['ccda_code'];
						}else{
							$ccda_code = $medication['ccda_code'];
						}
						$XML_medication_admin_section .= '<tr>
												<td>
													<content ID = "Med'.$medication['id'].'">'.htmlentities($medication['title']).'    [RxNorm: '.$ccda_code.']</content>
												</td>
												<td>';
						$XML_medication_admin_section .= ($medication['begdate']!="" && $medication['begdate']!='0000-00-00')?date("M d,Y",strtotime($medication['begdate'])):"";
						$XML_medication_admin_section .='</td>
														 <td>';
						$XML_medication_admin_section .=($medication['enddate']!="" && $medication['enddate']!='0000-00-00')?date("M d,Y",strtotime($medication['enddate'])):"";
						$XML_medication_admin_section .='</td>
														<td ID = "MEDROUTE'.$medication['id'].'">'.$this->getSite($medication['sites']).'</td>
														<td ID = "MEDFORM'.$medication['id'].'">'.$medication['destination'].'</td>
														<td>'.$medication['allergy_status'].'</td>
														<td ID = "Instruct'.$medication['id'].'">'.htmlentities($medication['med_comments']).'</td>
													</tr>
												';						
				}
			}else{
				$XML_medication_admin_section .= ' <tr><td></td></tr>';
				}
				$XML_medication_admin_section .= ' </tbody>';
				$XML_medication_admin_section .= '</table>';
				$XML_medication_admin_section .= '</text>';															

				$res = imw_query($qry_list);
				if(imw_num_rows($res)>0){
					while($medication = imw_fetch_assoc($res)){	
						if($medication['ccda_code']!=""){
							$arrCCDA = $this->getRXNorm_by_code($medication['ccda_code']);
							//if(count($arrCCDA)>0){
							$ccda_code_med = $medication['ccda_code'];
							$ccda_display_name_med = $medication['title'];
							//}					
						}else{
							$arrCCDA = $this->getRXNormCode($medication['title']);
							if(count($arrCCDA)>0){
							$ccda_code_med = $arrCCDA['ccda_code'];
							$ccda_display_name_med = $arrCCDA['ccda_display_name'];
							}
							
						}
						
					/*  BEGIN MEDICATION ENTRY  */
						$XML_medication_activity_entry = '<entry >';
						$XML_medication_activity_entry .= '<substanceAdministration classCode="SBADM" moodCode="EVN">';
						$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.16"/>';
						$XML_medication_activity_entry .= '<id nullFlavor="NI"/>';
						$XML_medication_activity_entry .= '<text>';
						
						$XML_medication_activity_entry .= '<reference value="#Med'.$medication['id'].'"/>'.$medication['title'].'    [RxNorm: '.$ccda_code_med.']';
						$XML_medication_activity_entry .= '</text>';
						$XML_medication_activity_entry .= '<statusCode code="completed"/>';
						$XML_medication_activity_entry .= '<effectiveTime xsi:type="IVL_TS">';
						if($medication['begdate'] !="" && $medication['begdate']!="0000-00-00")
						$XML_medication_activity_entry .= '<low value="'.str_replace("-","",$medication['begdate']).'"/>';
						else 
						$XML_medication_activity_entry .= '<low nullFlavor="NI"/>';
						if($medication['enddate'] !="" && $medication['enddate']!="0000-00-00")
						$XML_medication_activity_entry .= '<high value="'.str_replace("-","",$medication['enddate']).'"/>';
						else 
						$XML_medication_activity_entry .= '<high nullFlavor="NI"/>';
						$XML_medication_activity_entry .= '</effectiveTime>';
						
						$medDosage = trim($medication['destination']);
						$arrMedDosage = preg_split("/(?<=\d)(?=[a-zA-Z])|(?<=[a-zA-Z])(?=\d)/",preg_replace('/\s/','',$medDosage));
						//$arrMedDosage = explode(" ",$medDosage);
						$medDosageVal = $arrMedDosage[0];
						$medDosageUnit = $arrMedDosage[1];
						/* DYNAMIC ROUTE Medication Route FDA Value Set :: Code System(s): National Cancer Institute (NCI) Thesaurus*/
						if($medication['sites'] == 1 || $medication['sites'] == 2 || $medication['sites'] == 3 )
						$XML_medication_activity_entry .= '<routeCode code="C38287" codeSystem="2.16.840.1.113883.3.26.1.1"
															codeSystemName="National Cancer Institute (NCI) Thesaurus"
															displayName="OPHTHALMIC"/>';
						else if($medication['sites'] == 4)
						$XML_medication_activity_entry .= '<routeCode code="C38288" codeSystem="2.16.840.1.113883.3.26.1.1"
															codeSystemName="National Cancer Institute (NCI) Thesaurus"
															displayName="ORAL"/>';	
						if($medication['sites'] == 1){ // LEFT EYE OS
						$XML_medication_activity_entry .= '<approachSiteCode code="362503005" codeSystem="2.16.840.1.113883.6.96"
															codeSystemName="SNOMED CT"
															displayName="Entire left eye (body structure)"/>';
						}else if($medication['sites'] == 2){ // RIGHT EYE OD
						$XML_medication_activity_entry .= '<approachSiteCode code="362502000" codeSystem="2.16.840.1.113883.6.96"
															codeSystemName="SNOMED CT"
															displayName="Entire right eye (body structure)"/>';
						}
						else if($medication['sites'] == 3){ // BOTH EYES OU
						$XML_medication_activity_entry .= '<approachSiteCode code="244486005" codeSystem="2.16.840.1.113883.6.96"
															codeSystemName="SNOMED CT"
															displayName="Entire eye (body structure)"/>';
						}
						else if($medication['sites'] == 4){ // ORAL PO
						$XML_medication_activity_entry .= '<approachSiteCode code="26643006" codeSystem="2.16.840.1.113883.6.96"
															codeSystemName="SNOMED CT"
															displayName="taking by mouth"/>';
						}
						if($medDosageVal > 0 && $medDosageUnit != "")
						$XML_medication_activity_entry .= '<doseQuantity value="'.trim($medDosageVal).'" unit="'.trim($medDosageUnit).'"/>';
						else
						$XML_medication_activity_entry .= '<doseQuantity nullFlavor="NI"/>';
						$XML_medication_activity_entry .= '<consumable>';
						$XML_medication_activity_entry .= '<manufacturedProduct classCode="MANU">';
						$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.23"/>';
						$XML_medication_activity_entry .= '<manufacturedMaterial>';
														/* DYNAMIC MEDICATION CODE FROM RXNORM Medication Clinical Drug  */
						
						//if($ccda_code_med != "" && $ccda_display_name_med!=""){
							$XML_medication_activity_entry .= '<code code="'.$ccda_code_med.'"
															codeSystem="2.16.840.1.113883.6.88"
															codeSystemName="RxNorm"
															displayName="'.$ccda_display_name_med.'">';
						//}else{
							//$XML_medication_activity_entry .= '<code nullFlavor="NI">';
						//}
						$XML_medication_activity_entry .= '<originalText>
															<reference value = "#Med'.$medication['id'].'"/>
															</originalText>';
						$XML_medication_activity_entry .= '</code>';
						$XML_medication_activity_entry .= '</manufacturedMaterial>';
						$XML_medication_activity_entry .= '</manufacturedProduct>';
						$XML_medication_activity_entry .= '</consumable>';
						$XML_medication_activity_entry .= '<entryRelationship typeCode = "REFR">
															<supply classCode = "SPLY" moodCode = "INT">
																<templateId root = "2.16.840.1.113883.10.20.22.4.17"/>
																<id nullFlavor = "NI"/>
																<statusCode code = "completed"/>
	<effectiveTime xsi:type="IVL_TS">';
						if($medication['begdate']!="" && $medication['begdate']!='0000-00-00')
						$XML_medication_activity_entry .= '<low value="'.str_replace("-","",$medication['begdate']).'"/>';
						else
						$XML_medication_activity_entry .= '<low nullFlavor = "NI"/>';
						if($medication['enddate']!="" && $medication['enddate']!='0000-00-00')
						$XML_medication_activity_entry .= '<high value="'.str_replace("-","",$medication['enddate']).'" />';
						else
						$XML_medication_activity_entry .= '<high nullFlavor = "NI"/>';
						$XML_medication_activity_entry .= '</effectiveTime>
																 <product>
											<manufacturedProduct classCode = "MANU">
												<templateId root = "2.16.840.1.113883.10.20.22.4.23"/>
												<id nullFlavor="NI"/>
												<manufacturedMaterial>
													<code code = "'.$ccda_code_med.'" codeSystem = "2.16.840.1.113883.6.88" displayName = "'.$ccda_display_name_med.'" codeSystemName = "RxNorm">
														<originalText>
															<reference value = "#Med'.$medication['id'].'"/>
														</originalText>
													</code>
												</manufacturedMaterial>
											</manufacturedProduct>
										</product>
																<entryRelationship typeCode = "SUBJ" inversionInd = "true">
																	<act classCode = "ACT" moodCode = "INT">
																		<templateId root = "2.16.840.1.113883.10.20.22.4.20"/>
																		<code code = "409073007" codeSystem = "2.16.840.1.113883.6.96" displayName = "Instruction"/>
																		<text>
																			<reference value = "#Instruct'.$medication['id'].'"/>';
					$XML_medication_activity_entry .= htmlentities($medication['med_comments']);
					$XML_medication_activity_entry .= '</text>
																		<statusCode code = "completed"/>
																	</act>
																</entryRelationship>
															</supply>
														</entryRelationship>';
						$XML_medication_activity_entry .= '</substanceAdministration>';
						$XML_medication_activity_entry .= '</entry>';
						$XML_medication_admin_section .= $XML_medication_activity_entry;
					
					}
					}else{
					/*  BEGIN MEDICATION ENTRY  */
						$XML_medication_activity_entry = '<entry typeCode="DRIV">';
						$XML_medication_activity_entry .= '<substanceAdministration classCode="SBADM" moodCode="EVN">';
						$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.16"/>';
						$XML_medication_activity_entry .= '<id nullFlavor="NI"/>';
						$XML_medication_activity_entry .= '<statusCode code="completed"/>';
						$XML_medication_activity_entry .= '<effectiveTime xsi:type="IVL_TS">';
						$XML_medication_activity_entry .= '<low nullFlavor="NI"/>';
						$XML_medication_activity_entry .= '<high nullFlavor="NI"/>';
						$XML_medication_activity_entry .= '</effectiveTime>';
						
						$XML_medication_activity_entry .= '<doseQuantity nullFlavor="NI"/>';
						$XML_medication_activity_entry .= '<consumable>';
						$XML_medication_activity_entry .= '<manufacturedProduct classCode="MANU">';
						$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.23"/>';
						$XML_medication_activity_entry .= '<manufacturedMaterial>';
						$XML_medication_activity_entry .= '<code nullFlavor="NI">';
						$XML_medication_activity_entry .= '</code>';
						$XML_medication_activity_entry .= '</manufacturedMaterial>';
						$XML_medication_activity_entry .= '</manufacturedProduct>';
						$XML_medication_activity_entry .= '</consumable>';
						$XML_medication_activity_entry .= '</substanceAdministration>';
						$XML_medication_activity_entry .= '</entry>';
						$XML_medication_admin_section .= $XML_medication_activity_entry;
					
					}									
				$XML_medication_admin_section .= '</section></component>';
			}

			//Begin allergies section
			if(in_array('mu_data_set_allergies',$arrOptions)){
				$XML_allergies_section = '<component>';
				$XML_allergies_section .= '<section>';
				$XML_allergies_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.6.1"/>';
				$XML_allergies_section .= '<code code="48765-2" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="History of allergies"/>';
				$XML_allergies_section .= '<title>Allergies</title>';
				$XML_allergies_section .= '<text>';
				$XML_allergies_section .= '<table border = "1" width = "100%">';
				$XML_allergies_section .= '<thead>
											<tr>
												<th>Type</th>
												<th>Substance</th>
												<th>Begin Date</th>
												<th>Reactions</th>
												<th>Status</th>
											</tr>
										</thead>';
				$XML_allergies_section .= '<tbody>';
				$arrType = array("3","7");
				$arrAllergies = $this->get_medical_data($form_id, $arrType,$pid);
				$flag = 0;
				if(count($arrAllergies)>0){
				foreach($arrAllergies as $allergy){		
					if(!in_array($allergy['title'],$arrMedHxAller)){
						
					$arrAllerType = $this->allergy_type_srh($allergy['ag_occular_drug']);
					$strAllerType = '';
					if(count($arrAllerType)>0)
					$strAllerType = '  - '.$arrAllerType['display_name'];
					$flag = 1;
					$XML_allergies_section .= '<tr ID = "ALGSUMMARY_'.$allergy['id'].'">
											<td ID = "ALGTYPE_'.$allergy['id'].'">'.$arrAllerType['display_name'].'</td>
											<td ID = "ALGSUB_'.$allergy['id'].'">'.htmlentities($allergy['title']).'</td>
											<td ID = "ALGBEGIN_'.$allergy['id'].'">
											';
					$XML_allergies_section .=(preg_replace("/-/",'',$allergy['begdate'])>0)?date('M d,Y',strtotime($allergy['begdate'])):"";
					$XML_allergies_section .='</td>
											<td ID = "ALGREACT_'.$allergy['id'].'">'.htmlentities($allergy['comments']).'</td>
											<td ID = "ALGSTATUS_'.$allergy['id'].'">'.$allergy['allergy_status'].'</td>
										</tr>';
					}
				}
				}
				if($flag == 0)
				{
					$XML_allergies_section .= ' <tr><td colspan="5"></td></tr>';
				}
				$sql = "SELECT no_value FROM commonnomedicalhistory WHERE patient_id = '".$pid."' AND module_name = 'Allergy'";
				$res = imw_query($sql);
				$row = imw_fetch_assoc($res);
				$negationInd = '';
				if($row['no_value'] == "NoAllergies"){
					$XML_allergies_section .= ' <tr><td colspan="5">No Known Drug Allergy (NKDA)</td></tr>';
				}
				
				$XML_allergies_section .= '</tbody>';
				$XML_allergies_section .= '</table>';
				$XML_allergies_section .= '</text>';
						/* BEGIN ALLERGIES PROBLEM ACT */
						$arrType = array("3","7");
						$arrAllergies = $this->get_medical_data($form_id, $arrType,$pid);
						$flag = 0;
						if(count($arrAllergies)>0){
						foreach($arrAllergies as $allergy){
							
							if(!in_array($allergy['title'],$arrMedHxAller)){
							$flag = 1;	
							$XML_allergies_problem_act = '<entry typeCode="DRIV">';
							$XML_allergies_problem_act .= '<act classCode="ACT" moodCode="EVN">';
							$XML_allergies_problem_act .= '<templateId root="2.16.840.1.113883.10.20.22.4.30"/>';
							$XML_allergies_problem_act .= '<id nullFlavor="NI"/>';
							$XML_allergies_problem_act .= '<!-- Allergy Problem Act template -->';
							$XML_allergies_problem_act .= '<code code="48765-2" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Allergies, adverse reactions, alerts"/>';
							$XML_allergies_problem_act .= '<statusCode code="active"/>';
							$XML_allergies_problem_act .= '<effectiveTime>';
							if($allergy['begdate']!= ""){
							$XML_allergies_problem_act .= '<low value="'.str_replace('-','',$allergy['begdate']).'"/>';
							}else{
							$XML_allergies_problem_act .= '<low nullFlavor="NI"/>';	
							}
							$XML_allergies_problem_act .= '</effectiveTime>';
							$XML_allergies_problem_act .= '<entryRelationship typeCode="SUBJ">';
							
							$XML_allergies_problem_act .= '<observation classCode="OBS" moodCode="EVN">';
							$XML_allergies_problem_act .= '<templateId root="2.16.840.1.113883.10.20.22.4.7"/>';
							$XML_allergies_problem_act .= '<id nullFlavor="NI"/>';
							$XML_allergies_problem_act .= '<!-- Allergy - intolerance observation template -->';
							$XML_allergies_problem_act .= '<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4"/>';
							$XML_allergies_problem_act .= '<statusCode code="completed"/>';
							if($allergy['begdate']!= ""){
							$XML_allergies_problem_act .= '<effectiveTime>';
							$XML_allergies_problem_act .= '<low value="'.str_replace('-','',$allergy['begdate']).'"/>';
							$XML_allergies_problem_act .= '</effectiveTime>';
							}else{
							$XML_allergies_problem_act .= '<effectiveTime>';
							$XML_allergies_problem_act .= '<low nullFlavor="NI"/>';
							$XML_allergies_problem_act .= '</effectiveTime>';
							}
							$arrAllerType = $this->allergy_type_srh($allergy['ag_occular_drug']);
							if($arrAllerType['code'] != "" && $arrAllerType['display_name'] != ""){				
							$XML_allergies_problem_act .= '<value xsi:type="CD" code="'.$arrAllerType['code'].'"
															displayName="'.$arrAllerType['display_name'].'"
															codeSystem="2.16.840.1.113883.6.96"
															codeSystemName="SNOMED CT">';
							$XML_allergies_problem_act .= '<originalText>';
							$XML_allergies_problem_act .= '<reference value="#ALGSUB_'.$allergy['id'].'"/>';
							$XML_allergies_problem_act .= htmlentities($allergy['title']);
							$XML_allergies_problem_act .= '</originalText>';
							$XML_allergies_problem_act .= '</value>';
							}
							$XML_allergies_problem_act .= '<participant typeCode="CSM">';
							$XML_allergies_problem_act .= '<participantRole classCode="MANU">';
							$XML_allergies_problem_act .= '<playingEntity classCode="MMAT">';
							/* */
							if($allergy['ag_occular_drug'] == "fdbATIngredient" || $allergy['ag_occular_drug'] == "fdbATAllergenGroup"){ // Food Allergy
															
								if($allergy['ccda_code'] != "")	{
								$ccda_code_aller = $allergy['ccda_code'];
								$ccda_display_name_aller = $allergy['title'];
								}
								else{
								/* DYNAMIC CODE FROM Ingredient Name Value Set (Unique Ingredient Identifier (UNII) Code System)*/		
								$ccda_code_aller = $allergy['ccda_code'];
								$ccda_display_name_aller = $allergy['title'];	
								}
								$XML_allergies_problem_act .= '<code code="'.$ccda_code_aller.'" displayName="'.$ccda_display_name_aller.'"
																codeSystem="2.16.840.1.113883.4.9" codeSystemName="UNII">';
								$XML_allergies_problem_act .= '<originalText>';
								$XML_allergies_problem_act .= '<reference value="#ALGSUB_'.$allergy['id'].'"/>';
								$XML_allergies_problem_act .= '</originalText>';
								$XML_allergies_problem_act .= '</code>';
							}
							else if($allergy['ag_occular_drug'] == "fdbATDrugName"){ // Drug Allergy
								if($allergy['ccda_code'] != "")	{
									$ccda_code_aller = $allergy['ccda_code'];
									$ccda_display_name_aller = $allergy['title'];
									//}
								}
								else{
									/* DYNAMIC CODE FROM Medication Clinical Drug Value Set (RxNorm Code System)*/
									$arrCCDA = $this->getRXNormCode($allergy['title']);
									if(count($arrCCDA)>0){
									$ccda_code_aller = $arrCCDA['ccda_code'];
									$ccda_display_name_aller = $arrCCDA['ccda_display_name'];
									}
								}
								$XML_allergies_problem_act .= '<code code="'.$ccda_code_aller.'" displayName="'.$ccda_display_name_aller.'"
																codeSystem="2.16.840.1.113883.6.88" codeSystemName="RxNorm">';
								
								$XML_allergies_problem_act .= '<originalText>';
								$XML_allergies_problem_act .= '<reference value="#ALGSUB_'.$allergy['id'].'"/>';
								$XML_allergies_problem_act .= '</originalText>';
								$XML_allergies_problem_act .= '</code>';
							}
							
							$XML_allergies_problem_act .= '</playingEntity>';
							$XML_allergies_problem_act .= '</participantRole>';
							$XML_allergies_problem_act .= '</participant>';
							$XML_allergies_problem_act .= '<entryRelationship typeCode = "SUBJ" inversionInd = "true">
												<observation classCode = "OBS" moodCode = "EVN">
													<templateId root = "2.16.840.1.113883.10.20.22.4.28"/>
													<code code = "33999-4" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "LOINC" displayName = "Status"/>
													<statusCode code = "completed"/>
													<value xsi:type = "CE" code = "55561003" codeSystem = "2.16.840.1.113883.6.96" displayName = "'.$allergy['allergy_status'].'"/>
												</observation>
											</entryRelationship>';
						   $XML_allergies_problem_act .= '<entryRelationship typeCode = "MFST" inversionInd = "true">
												<observation classCode = "OBS" moodCode = "EVN">
													<templateId root = "2.16.840.1.113883.10.20.22.4.9"/>
													<id nullFlavor="NI"/>
													<code code = "ASSERTION" codeSystem = "2.16.840.1.113883.5.4"/>
													<text>
														<reference value = "#ALGREACT_'.$allergy['id'].'"/>'.
														htmlentities($allergy['comments'])
														.'
													</text>
													<statusCode code = "completed"/>';
							$arrAllReaction = $this->getProblemCode($allergy['comments']);
											// DYNAMIC REACTION CODE //
							if($arrAllReaction['ccda_code']!="" && $arrAllReaction['ccda_display_name']){					
								$XML_allergies_problem_act .= '<value xsi:type="CD"
														code="'.$arrAllReaction['ccda_code'].'"
														codeSystem="2.16.840.1.113883.6.96"
														codeSystemName="SNOMED CT"
														displayName="'.$arrAllReaction['ccda_display_name'].'"/>';
							}else{
								$XML_allergies_problem_act .= '<value xsi:type="CD" nullFlavor="NI"/>';
							}  
							$XML_allergies_problem_act .='</observation>
											</entryRelationship>';
							$XML_allergies_problem_act .= '</observation>';
							$XML_allergies_problem_act .= '</entryRelationship>';
							$XML_allergies_problem_act .= '</act>';
							$XML_allergies_problem_act .= '</entry>';
							$XML_allergies_section .= $XML_allergies_problem_act;
							}
						
						}
						}
						
						if($flag == 0){
							
							$XML_allergies_problem_act = '<entry typeCode="DRIV">';
							$XML_allergies_problem_act .= '	<act classCode="ACT" moodCode="EVN">';
							$XML_allergies_problem_act .= '	<templateId root="2.16.840.1.113883.10.20.22.4.30"/>';
							$XML_allergies_problem_act .= '	<id nullFlavor="NI"/>';
							$XML_allergies_problem_act .= '	<!-- Allergy Problem Act template -->';
							$XML_allergies_problem_act .= '	<code code="48765-2" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Allergies, adverse reactions, alerts"/>';
							$XML_allergies_problem_act .= '	<statusCode code="active"/>';
							$XML_allergies_problem_act .= '	<effectiveTime>';
							$XML_allergies_problem_act .= '		<low nullFlavor="NI"/>';
							$XML_allergies_problem_act .= '	</effectiveTime>';
							$XML_allergies_problem_act .= '	<entryRelationship typeCode="SUBJ">';
							
							$sql = "SELECT no_value FROM commonnomedicalhistory WHERE patient_id = '".$pid."' AND module_name = 'Allergy'";
							$res = imw_query($sql);
							$row = imw_fetch_assoc($res);
							$negationInd = '';
							if($row['no_value'] == "NoAllergies")
							$negationInd = "negationInd='true'";
							$XML_allergies_problem_act .= '		<observation classCode="OBS" moodCode="EVN" '.$negationInd.'>';
							$XML_allergies_problem_act .= '			<templateId root="2.16.840.1.113883.10.20.22.4.7"/>';
							$XML_allergies_problem_act .= '			<id nullFlavor="NI"/>';
							$XML_allergies_problem_act .= '			<!-- Allergy - intolerance observation template -->';
							$XML_allergies_problem_act .= '			<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4"/>';
							$XML_allergies_problem_act .= '			<statusCode code="completed"/>';
							$XML_allergies_problem_act .= '			<effectiveTime>';
							$XML_allergies_problem_act .= '				<low nullFlavor="UNK"/>';
							$XML_allergies_problem_act .= '			</effectiveTime>';
							$XML_allergies_problem_act .= '			';
							$XML_allergies_problem_act .= '<value xsi:type="CD" nullFlavor="NI"/>';
							$XML_allergies_problem_act .= '			<participant typeCode="CSM">';
							$XML_allergies_problem_act .= '				<participantRole classCode="MANU">';
							$XML_allergies_problem_act .= '					<playingEntity classCode="MMAT">';
							$XML_allergies_problem_act .= '						<code nullFlavor="NI"/>';
							$XML_allergies_problem_act .= '					</playingEntity>';
							$XML_allergies_problem_act .= '				</participantRole>';
							$XML_allergies_problem_act .= '			</participant>';
							$XML_allergies_problem_act .= '		</observation>';
							$XML_allergies_problem_act .= '</entryRelationship>';
							$XML_allergies_problem_act .= '</act>';
							$XML_allergies_problem_act .= '</entry>';
							$XML_allergies_section .= $XML_allergies_problem_act;
						
						}
						/* END ALLERGIES PROBLEM ACT */
				$XML_allergies_section .= '</section>';
				$XML_allergies_section .= '</component>';
			}

			//Begin immu. section
			$qry_immu = '';
			$qry_immu = "SELECT * FROM immunizations immu 
						WHERE patient_id = '".$pid."' 
						";
			if(!in_array('visit_medication_immu',$arrOptions)){
				if($dos != ""){
					$qry_immu .= " AND administered_date != '".$dos."'";
				}
			}
			$XML_immunization_section = '<component>';
			$XML_immunization_section .= '<section>';
			$XML_immunization_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.2.1"/>';
			$XML_immunization_section .= '<!-- ******** Immunizations section template ******** -->';
			$XML_immunization_section .= '<code code="11369-6"
										codeSystem="2.16.840.1.113883.6.1"
										codeSystemName="LOINC"
										displayName="History of immunizations"/>';
			$XML_immunization_section .= '<title>Immunizations</title>';
			$XML_immunization_section .= '<text>';
			
			$res_immu = imw_query($qry_immu);
			if(imw_num_rows($res_immu)){
			$XML_immunization_section .= ' <table border = "1" width = "100%">';
			$XML_immunization_section .= '<thead>
									<tr>
										<th>Vaccine Name</th>
										<th>Date</th>
										<th>Status</th>
										<th>Dosage</th>
										<th>Manufacturer</th>
										<th>Administered By</th>
									</tr>
								</thead>';
			$XML_immunization_section .= '<tbody>';					
			while($row_immu = imw_fetch_assoc($res_immu)){
				$admi_date = $admi_route = $dosage = $manu = $admn_by ="";
				if(str_replace('-','',$row_immu['administered_date']) != '00000000')
				$admi_date = $row_immu['administered_date'];
				if($row_immu['immzn_route_site'] != '')
				$admi_route = $row_immu['immzn_route_site'];
				if($row_immu['immzn_dose_unit'] != '' && $row_immu['immzn_dose'] != "")
				$dosage = $row_immu['immzn_dose']. " ".$row_immu['immzn_dose_unit'];
				if($row_immu['manufacturer'] != "")
				$manu = $row_immu['manufacturer'];
				if($row_immu['administered_by_id']!="" && $row_immu['administered_by_id']>0 ){
				$qry_admin_by = "SELECT * FROM users WHERE id = '".$row_immu['administered_by_id']."'";
				$res_admin_by = imw_query($qry_admin_by);
					if(imw_num_rows($res_admin_by) > 0){
					$row_admin_by = imw_fetch_assoc($res_admin_by);
					$admn_by = $row_admin_by['fname']." ".$row_admin_by['lname'];
					}
				}	
				$XML_immunization_section .= '<tr>
										<td>
											<content ID = "immun'.$row_immu['id'].'"/>
											'.htmlentities($row_immu['immunization_id']).'  [CVX: '.$row_immu['immunization_cvx_code'].']
										</td>
										<td>';
				$XML_immunization_section .=(preg_replace("/-/",'',$admi_date)>0)?date('M d,Y',strtotime($admi_date)):"";
				$XML_immunization_section .='</td>
										<td>'.$row_immu['status'].'</td>
										<td>'.$dosage.'</td>
										<td ID = "immun_manu_'.$row_immu['id'].'">'.htmlentities($manu).'</td>
										 <td>'.$admn_by.'</td>
									</tr>';
			}
			$XML_immunization_section .= '</tbody>';
			$XML_immunization_section .= '</table>';
			}
			
			$XML_immunization_section .= '</text>';
			
			$res_immu = imw_query($qry_immu);
			if(imw_num_rows($res_immu)){
			while($row_immu = imw_fetch_assoc($res_immu)){
				$ccda_code_route = $ccda_display_name_route = "";
			$XML_immunization_entry = '<entry typeCode="DRIV">';
			$XML_immunization_entry .= '<substanceAdministration classCode="SBADM" moodCode="EVN"
											negationInd="false">';
			$XML_immunization_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.52"/>';
			$XML_immunization_entry .= '<id nullFlavor="NI"/>';
			$XML_immunization_entry .= '<!-- **** Immunization activity template **** -->';
			$XML_immunization_entry .= '<text>';
			$XML_immunization_entry .= '<reference value="#immun'.$row_immu['id'].'"/>';
			$XML_immunization_entry .= '</text>';
			$XML_immunization_entry .= '<statusCode code="completed"/>';
			if($row_immu['administered_date']!="")
			$XML_immunization_entry .= '<effectiveTime xsi:type="IVL_TS" value="'.str_replace('-','',$row_immu['administered_date']).'"/>';
			else
			$XML_immunization_entry .= '<effectiveTime xsi:type="IVL_TS" nullFlavor="NI"/>';
			/* DYNAMIC VALUE FOR ROUTE (EX ORAL) Medication Route FDA Value Set Code System(s):National Cancer Institute (NCI) Thesaurus 2.16.840.1.113883.3.26.1.1 */
			if($row_immu['immzn_route_site']!=""){
				$arrCCDA = $this->getRouteCode($row_immu['immzn_route_site']);
				$ccda_code_route = $arrCCDA['ccda_code'];
				$ccda_display_name_route = $arrCCDA['ccda_display_name'];
			}
			if($ccda_code_route != "" && $ccda_display_name_route!=""){
				$XML_immunization_entry .= '<routeCode code="'.$ccda_code_route.'" codeSystem="2.16.840.1.113883.3.26.1.1"
										codeSystemName="NCI Thesaurus"
										displayName="'.$ccda_display_name_route.'"/>';
			}else{
				$XML_immunization_entry .= '<routeCode nullFlavor="NI"/>';
			}
			if($row_immu['immzn_dose'] != "" && $row_immu['immzn_dose_unit'] != "")							
			$XML_immunization_entry .= '<doseQuantity value="'.trim($row_immu['immzn_dose']).'" unit="'.trim($row_immu['immzn_dose_unit']).'"/>';
			else
			$XML_immunization_entry .= '<doseQuantity nullFlavor="NI"/>';
			$XML_immunization_entry .= '<consumable>';
			$XML_immunization_entry .= '<manufacturedProduct classCode="MANU">';
			$XML_immunization_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.54"/>';
			$XML_immunization_entry .= '<!-- **** Immunization Medication Information **** -->';
			$XML_immunization_entry .= '<manufacturedMaterial>';
			/* DYNAMIC VALUE Vaccine Administered Value Set Code System(s):Vaccines administered (CVX) 2.16.840.1.113883.12.292 */
			$XML_immunization_entry .= '<code code="'.$row_immu['immunization_cvx_code'].'"
										codeSystem="2.16.840.1.113883.12.292"
										codeSystemName="CVX"
										displayName="'.$row_immu['immunization_id'].'">';							
			$XML_immunization_entry .= '<originalText><reference value = "#immun'.$row_immu['id'].'"/>'.$row_immu['immunization_id'].'</originalText>';
			$XML_immunization_entry .= '</code>';
			$XML_immunization_entry .= '</manufacturedMaterial>';
			if($row_immu['manufacturer'] != ""){
			$XML_immunization_entry .= '<manufacturerOrganization>
										  <name>'.htmlentities($row_immu['manufacturer']).'</name>
									   </manufacturerOrganization>';
			}
			$XML_immunization_entry .= '</manufacturedProduct>';
			$XML_immunization_entry .= '</consumable>';
			
				if($row_immu['administered_by_id']!="" && $row_immu['administered_by_id']>0 ){
				$qry_admin_by = "SELECT * FROM users WHERE id = '".$row_immu['administered_by_id']."'";
				$res_admin_by = imw_query($qry_admin_by);
				if(imw_num_rows($res_admin_by) > 0){
				$row_admin_by = imw_fetch_assoc($res_admin_by);
				
				$XML_immunization_entry .= '<performer typeCode="PRF">';
				$XML_immunization_entry .= '<assignedEntity>';
				$XML_immunization_entry .= '<!-- NPI 12345 -->';
				if($row_admin_by['user_npi'] != "")
				$XML_immunization_entry .= '<id extension="'.$row_admin_by['user_npi'].'" root="2.16.840.1.113883.4.6"/>';
				else
				$XML_immunization_entry .= '<id nullFlavor="NI"/>';
				//
				if($res_admin_by['facility'] > 0){
				$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$res_admin_by['facility']."'";
				}
				else{
				$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
				}
				$res_facility = imw_query($qry_facility);
				$row_facility = imw_fetch_assoc($res_facility);
				
				$XML_immunization_entry .= '<addr use="WP">';
				if($row_facility['street'] != "")
				$XML_immunization_entry .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
				if($row_facility['city'] != "")
				$XML_immunization_entry .= '<city>'.$row_facility['city'].'</city>';
				if($row_facility['state'] != "")
				$XML_immunization_entry .= '<state>'.$row_facility['state'].'</state>';
				if($row_facility['postal_code'] != "")
				$XML_immunization_entry .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
				$XML_immunization_entry .= '<country>US</country>';
				$XML_immunization_entry .= '</addr>';
				
				if($row_facility['phone'] != "")
				$XML_immunization_entry .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';						
				$XML_immunization_entry .= '<assignedPerson>';
				$XML_immunization_entry .= '<name>';
				$XML_immunization_entry .= '<given>'.$row_admin_by['fname'].'</given>';
				$XML_immunization_entry .= '<family>'.$row_admin_by['lname'].'</family>';
				$XML_immunization_entry .= '</name>';
				$XML_immunization_entry .= '</assignedPerson>';
				$XML_immunization_entry .= '</assignedEntity>';
				$XML_immunization_entry .= '</performer>';
				}
				}
				if($row_immu['adverse_reaction']!=""){
				$XML_immunization_entry .= '<entryRelationship typeCode="CAUS">';
				$XML_immunization_entry .= '<observation classCode="OBS" moodCode="EVN">';
				$XML_immunization_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.9"/>';
				$XML_immunization_entry .= '<!-- Reaction observation template -->';
				$XML_immunization_entry .= '<id nullFlavor="NI"/>';
				$XML_immunization_entry .= '<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4"/>';
				$XML_immunization_entry .= '<statusCode code="completed"/>';
				$XML_immunization_entry .= '<effectiveTime xsi:type="IVL_TS">';
				if($row_immu['adverse_reaction_date'] != "" && $row_immu['adverse_reaction_date'] != '0000-00-00 00:00:00'){
				$date = date('Ymd',strtotime($row_immu['adverse_reaction_date']));
				$XML_immunization_entry .= '<low value="'.$date.'"/>';
				}else{
				$XML_immunization_entry .= '<low nullFlavor="NI"/>';
				}
				$XML_immunization_entry .= '</effectiveTime>';
				$arrReaction = $this->getProblemCode($row_immu['adverse_reaction']);
										// DYNAMIC REACTION CODE //
				if($arrReaction['ccda_code']!="" && $arrReaction['ccda_display_name']){					
				$XML_immunization_entry .= '<value xsi:type="CD"
											code="'.$arrReaction['ccda_code'].'"
											codeSystem="2.16.840.1.113883.6.96"
											codeSystemName="SNOMED CT"
											displayName="'.$arrReaction['ccda_display_name'].'"/>';
				}else{
					$XML_immunization_entry .= '<value xsi:type="CD" nullFlavor="NI"/>';
				}
				$XML_immunization_entry .= '</observation>';
				$XML_immunization_entry .= '</entryRelationship>';
				}
			$XML_immunization_entry .= '</substanceAdministration>';
			$XML_immunization_entry .= '</entry>';
			$XML_immunization_section .= $XML_immunization_entry;
			}
			}else{
				
			$XML_immunization_entry = '<entry typeCode="DRIV">';
			$XML_immunization_entry .= '<substanceAdministration classCode="SBADM" moodCode="EVN"
											negationInd="false">';
			$XML_immunization_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.52"/>';
			$XML_immunization_entry .= '<id nullFlavor="NI"/>';
			$XML_immunization_entry .= '<!-- **** Immunization activity template **** -->';
			$XML_immunization_entry .= '<statusCode code="completed"/>';
			$XML_immunization_entry .= '<effectiveTime nullFlavor="NI"/>';
			$XML_immunization_entry .= '<consumable>';
			$XML_immunization_entry .= '<manufacturedProduct classCode="MANU">';
			$XML_immunization_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.54"/>';
			$XML_immunization_entry .= '<!-- **** Immunization Medication Information **** -->';
			$XML_immunization_entry .= '<manufacturedMaterial>';
			$XML_immunization_entry .= '<code nullFlavor="NI"/>';							
			$XML_immunization_entry .= '</manufacturedMaterial>';
			$XML_immunization_entry .= '</manufacturedProduct>';
			$XML_immunization_entry .= '</consumable>';
			
			$XML_immunization_entry .= '</substanceAdministration>';
			$XML_immunization_entry .= '</entry>';
			$XML_immunization_section .= $XML_immunization_entry;
			}
			$XML_immunization_section .= '</section>';
			$XML_immunization_section .= '</component>';
			
			//Begin vital sign section
			if(in_array('mu_data_set_vs',$arrOptions)){
				$XML_vital_section = '<component>';
				$XML_vital_section .= '<section>';
				$XML_vital_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.4"/>';
				$XML_vital_section .= '<code code="8716-3"
										codeSystem="2.16.840.1.113883.6.1"
										codeSystemName="LOINC"
										displayName="VITAL SIGNS" />';
				$XML_vital_section .= '<title>Vital Signs</title>';
				
				if($form_id == ""){
				$sql_vital = "SELECT vsp.*,vsl.vital_sign,vsm.date_vital FROM vital_sign_master vsm 
							JOIN vital_sign_patient vsp ON vsm.id = vsp.vital_master_id 
							JOIN  vital_sign_limits vsl ON vsl.id = vsp.vital_sign_id 
							WHERE vsm.patient_id = '".$pid."' AND  vsm.status = 0 ORDER BY vsp.id ASC";
				}else{
					$sql = "SELECT date_of_service FROM chart_master_table WHERE id = '".$form_id."'";
					$res = imw_query($sql);
					$row = imw_fetch_assoc($res);
					$sql_vital = "SELECT vsp.*,vsl.vital_sign,vsm.date_vital FROM vital_sign_master vsm 
							JOIN vital_sign_patient vsp ON vsm.id = vsp.vital_master_id 
							JOIN  vital_sign_limits vsl ON vsl.id = vsp.vital_sign_id 
							WHERE vsm.patient_id = '".$pid."' AND  vsm.status = 0 
								AND vsm.date_vital = '".$row['date_of_service']."'
							ORDER BY vsp.id ASC";
				}
				$XML_vital_section .= '<text>';
				
				$result_vital = imw_query($sql_vital);
				if(imw_num_rows($result_vital)){
				$XML_vital_section .= '<table border = "1" width = "100%">';	
				$XML_vital_section .= '<thead>
										<tr>
											<th>Vital Sign</th>
											<th>Date Time</th>
											<th >Value</th>
										</tr>
									</thead>';
				$XML_vital_section .= '<tbody>';					
				while($row_vital = imw_fetch_assoc($result_vital)){	
					$arr_vs_result_type = $this->vs_result_type_srh($row_vital['vital_sign']);
					if($arr_vs_result_type['code'] != "" && $arr_vs_result_type['display_name'] != "" && $row_vital['range_vital']!=""){
					$XML_vital_section .= '
										<tr>
											<td>'.$row_vital['vital_sign'].'</td>
											<td ID = "VS_'.$row_vital['id'].'">';
					$XML_vital_section .=(preg_replace("/-/",'',$row_vital['date_vital'])>0)?date('M d,Y',strtotime($row_vital['date_vital'])):"";
					$XML_vital_section .='</td>
											<td ID = "VS_Val_'.$row_vital['id'].'">'.$row_vital['range_vital']." ".html_entity_decode($row_vital['unit']).'</td>
										</tr>
										';
					}
				}
				$XML_vital_section .= '</tbody>';
				$XML_vital_section .= '</table>';
				}
				
				$XML_vital_section .= '</text>';
				$XML_vital_entry = '';
				$result_vital = imw_query($sql_vital);
				while($row_vital = imw_fetch_assoc($result_vital)){
					$arr_vs_result_type = $this->vs_result_type_srh($row_vital['vital_sign']);
					if($arr_vs_result_type['code'] != "" && $arr_vs_result_type['display_name'] != "" && $row_vital['range_vital']!=""){		
					$XML_vital_entry = '<entry typeCode="DRIV">';
					$XML_vital_entry .= '<organizer classCode="CLUSTER" moodCode="EVN">';
					$XML_vital_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.26"/>';
					$XML_vital_entry .= '<!-- Vital Signs Organizer template -->';
					$XML_vital_entry .= '<id nullFlavor="NI"/>';
					$XML_vital_entry .= '<code code="46680005" codeSystem="2.16.840.1.113883.6.96"
											codeSystemName="SNOMED CT" displayName="Vital signs"/>';
					$XML_vital_entry .= '<statusCode code="completed"/>';
					$XML_vital_entry .= '<effectiveTime value="'.str_replace('-','',$row_vital['date_vital']).'"/>';
					$XML_vital_entry .= '<component>';
					$XML_vital_entry .= '<observation classCode="OBS" moodCode="EVN">';
					$XML_vital_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.27"/>';
					$XML_vital_entry .= '<id nullFlavor="NI"/>';
										// @code SHOULD be selected from ValueSet HITSP Vital Sign Result Type 2.16.840.1.113883.3.88.12.80.62 STATIC
								
					$XML_vital_entry .= '<code code="'.$arr_vs_result_type['code'].'"
											codeSystem="2.16.840.1.113883.6.1"
											codeSystemName="LOINC"
											displayName="'.$arr_vs_result_type['display_name'].'"/>';
					
					$XML_vital_entry .= '<text>';
					$XML_vital_entry .= '<reference value="#VS_'.$row_vital['id'].'"/>';
					$XML_vital_entry .= '</text>';
					$XML_vital_entry .= '<statusCode code="completed"/>';
					$XML_vital_entry .= '<effectiveTime value="'.str_replace('-','',$row_vital['date_vital']).'"/>';
					if($row_vital['range_vital']!="")
					$XML_vital_entry .= '<value xsi:type="PQ" value="'.trim($row_vital['range_vital']).'" unit="'.html_entity_decode(preg_replace('/\s/','',trim($row_vital['unit']))).'"/>';
					else
					$XML_vital_entry .= '<value xsi:type="PQ" nullFlavor="NI"/>';
					
					$XML_vital_entry .= '</observation>';
					$XML_vital_entry .= '</component>';
					$XML_vital_entry .= '</organizer>';
					$XML_vital_entry .= '</entry>';
					$XML_vital_section .= $XML_vital_entry;
					}
				}
				
				$XML_vital_section .= '</section>';
				$XML_vital_section .= '</component>';
			}

			//Begin prob. list
			if(in_array('mu_data_set_problem_list',$arrOptions)){
				$arrProblemList = $this->get_pt_problem_list($form_id, $pid);
				
				$XML_problem_section = '<component>';
				$XML_problem_section .= '<section>';
				$XML_problem_section .= '<!-- Problem Section with Coded Entries Required templateID -->';
				$XML_problem_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.5.1"/>';
				$XML_problem_section .= '<code code="11450-4" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"
								  displayName="PROBLEM LIST"/>';
				$XML_problem_section .= '<title>PROBLEMS</title>';
				$XML_problem_section .= '<text>';
				$XML_problem_section .= '<table border = "1" width = "100%">';
				
				$XML_problem_section .= ' <thead>
										<tr>
											<th>Problem</th>
											<th>Effective Dates</th>
											<th>Problem Type</th>
											<th>Problem Status</th>
										</tr>
									</thead>
									<tbody>
									';
				$flag = 0;					
				if(count($arrProblemList)>0){					
					foreach($arrProblemList as $problemList){
						if(!in_array($problemList['problem_name'],$arrMedHxProbList)){
							$flag = 1;
						$XML_problem_section .= '<tr ID = "PROBSUMMARY_'.$problemList['id'].'">
												<td ID = "PROBKIND_'.$problemList['id'].'">'.htmlentities($problemList['problem_name']).' [SNOMED-CT: '.$problemList['ccda_code'].']</td>
												<td>'.date('M d,Y',strtotime($problemList['onset_date'])).'</td>';
												
						$arrProbType = $this->problem_type_srh($problemList['prob_type']);
						if($arrProbType['code']!="" && $arrProbType['display_name']!=""){						
						$XML_problem_section .= '<td ID = "PROBTYPE_'.$problemList['id'].'">'.$arrProbType['display_name'].'</td>';
						}
						$XML_problem_section .= '<td ID = "PROBSTATUS_'.$problemList['id'].'">'.$problemList['status'].'</td>
											</tr>';
						}
					}
				}
				
				if($flag == 0){
					$XML_problem_section .= '<tr><td></td></tr>';
				}
				$XML_problem_section .= '</tbody>';
				$XML_problem_section .= '</table>';
				$XML_problem_section .= '</text>';
				$XML_problem_section .= '<!-- Problem Concern Act -->';
				$res_prob_list = imw_query($qry);
				$flag = 0;
				if(count($arrProblemList)>0){
					foreach($arrProblemList as $problemList){
						if(!in_array($problemList['problem_name'],$arrMedHxProbList)){
							$flag = 1;
							$XML_problem_entry = '<entry>';
							$XML_problem_entry .= '<act classCode="ACT" moodCode="EVN">';
							$XML_problem_entry .= '<!-- Problem Concern Act template -->';
							$XML_problem_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.3"/>';
							$XML_problem_entry .= '<id nullFlavor="NI"/>';
							$XML_problem_entry .= '<code code="CONC" codeSystem="2.16.840.1.113883.5.6" displayName="Concern"/>';
							$XML_problem_entry .= '<statusCode code="active"/>';
							$XML_problem_entry .= '<effectiveTime>';
							$XML_problem_entry .= ' <low value="'.str_replace('-','',$problemList['onset_date']).'"/>';
							$XML_problem_entry .= '</effectiveTime>';
							$XML_problem_entry .= '<entryRelationship typeCode="SUBJ">';
							$XML_problem_entry .= '<observation classCode="OBS" moodCode="EVN">';
							$XML_problem_entry .= '<!-- Problem Observation template -->';
							
							$XML_problem_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.4"/>';
							$XML_problem_entry .= '<id nullFlavor="NI"/>';
							$arrProbType = $this->problem_type_srh($problemList['prob_type']);
							if($arrProbType['code']!="" && $arrProbType['display_name']!=""){
							$XML_problem_entry .= '<code code="'.$arrProbType['code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT"
														  displayName="'.$arrProbType['display_name'].'"/>';
							}
							else{
								$XML_problem_entry .= '<code nullFlavor="NI"/>';
							}
							$XML_problem_entry .= '<text>';
							$XML_problem_entry .= '<reference value="#PROBSUMMARY_'.$problemList['id'].'"/>';
							$XML_problem_entry .= '</text>';
							$XML_problem_entry .= '<statusCode code="completed"/>';
							if($problemList['onset_date'] != ""){
							$XML_problem_entry .= ' <effectiveTime>';
							$XML_problem_entry .= ' <low value="'.str_replace('-','',$problemList['onset_date']).'"/>';
							$XML_problem_entry .= '</effectiveTime>';
							}else{
								$XML_problem_entry .= ' <effectiveTime nullFlavor="NI"/>';
							}
													// DYNAMIC PROBLEM VALUE //
							if($problemList['ccda_code']!=""){									
							$XML_problem_entry .= '<value xsi:type="CD" code="'.$problemList['ccda_code'].'" codeSystem="2.16.840.1.113883.6.96" 
														  codeSystemName="SNOMED CT" displayName="'.$problemList['problem_name'].'"/>';
							}else{
								$arrProblem = $this->getProblemCode($problemList['problem_name']);
											// DYNAMIC REACTION CODE //
								if($arrProblem['ccda_code']!="" && $arrProblem['ccda_display_name']){					
									$XML_problem_entry .= '<value xsi:type="CD"
															code="'.$arrProblem['ccda_code'].'"
															codeSystem="2.16.840.1.113883.6.96"
															codeSystemName="SNOMED CT"
															displayName="'.$arrProblem['ccda_display_name'].'"/>';
								}else{
									$XML_problem_entry .= '<value xsi:type="CD"
															code=""
															codeSystem="2.16.840.1.113883.6.96"
															codeSystemName="SNOMED CT"
															displayName="'.$problemList['problem_name'].'"/>';
								}  
							}
								$XML_problem_entry .= '<entryRelationship typeCode = "REFR">
															<observation classCode = "OBS" moodCode = "EVN">
																<!--Problem status observation template -->
																<templateId root = "2.16.840.1.113883.10.20.22.4.6"/>
																<code code = "33999-4" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "LOINC" displayName = "Status"/>
																<text>
																	<reference value = "#PROBSTATUS_'.$problemList['id'].'"/>
																</text>
																<statusCode code = "completed"/>
																<value xsi:type = "CD" code = "55561003" codeSystem = "2.16.840.1.113883.6.96" displayName = "'.$problemList['status'].'" codeSystemName = "SNOMED CT"/>
															</observation>
														</entryRelationship>';
								$XML_problem_entry .= '</observation>';
								$XML_problem_entry .= '</entryRelationship>';
								$XML_problem_entry .= '</act>';
								$XML_problem_entry .= ' </entry>';
								$XML_problem_section .= $XML_problem_entry;
						}
					}
				}
				if($flag == 0){
					$XML_problem_entry = '<entry>';
					$XML_problem_entry .= '<act classCode="ACT" moodCode="EVN">';
					$XML_problem_entry .= '<!-- Problem Concern Act template -->';
					$XML_problem_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.3"/>';
					$XML_problem_entry .= '<id nullFlavor="NI"/>';
					$XML_problem_entry .= '<code code="CONC" codeSystem="2.16.840.1.113883.5.6" displayName="Concern"/>';
					$XML_problem_entry .= '<statusCode code="active"/>';
					$XML_problem_entry .= '<effectiveTime>';
					$XML_problem_entry .= '<low nullFlavor="NI"/>';
					$XML_problem_entry .= '</effectiveTime>';
					$XML_problem_entry .= '<entryRelationship typeCode="SUBJ">';
					$XML_problem_entry .= '<observation classCode="OBS" moodCode="EVN">';
					
					$XML_problem_entry .= '<!-- Problem Observation template -->';
					$XML_problem_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.4"/>';
					$XML_problem_entry .= '<id nullFlavor="NI"/>';
					$XML_problem_entry .= '<code nullFlavor="NI"/>';
					$XML_problem_entry .= '<statusCode code="completed"/>';
											// DYNAMIC PROBLEM VALUE //
					$XML_problem_entry .= '<value xsi:type="CD" nullFlavor="NI"/>';
					$XML_problem_entry .= '</observation>';
					$XML_problem_entry .= '</entryRelationship>';
					$XML_problem_entry .= '</act>';
					$XML_problem_entry .= ' </entry>';
					$XML_problem_section .= $XML_problem_entry;
				
				}
				
				$XML_problem_section .= '</section>';
				$XML_problem_section .= '</component>';
			}

			//Begin lab section	
			if(in_array('mu_data_set_lab',$arrOptions)){
				$sql_lab = "SELECT ld.*,lor.*,lr.*,lr.id as result_id 
							FROM lab_test_data ld 
							LEFT JOIN lab_observation_requested lor ON lor.lab_test_id = ld.lab_test_data_id
							JOIN  lab_observation_result lr ON lr.lab_test_id = ld.lab_test_data_id
							WHERE ld.lab_patient_id = '".$pid."' AND ld.lab_status IN(1,2)
							AND lr.del_status = 0
							GROUP BY result_id
							";			
				$XML_results_section = '<component>';
				$XML_results_section .= '<section>';
				$XML_results_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.3.1"/>';
				$XML_results_section .= '<code code="30954-2"
										codeSystem="2.16.840.1.113883.6.1"
										codeSystemName="LOINC"
										displayName="RESULTS" />';
				$XML_results_section .= '<title>Results</title>';	
				$XML_results_section .= '<text>';
				
				$res_lab = imw_query($sql_lab);
				if(imw_num_rows($res_lab)){
				$XML_results_section .= '<table border = "1" width = "100%">';	
				$XML_results_section .= '<thead>
										<tr>
											<th>Observation</th>
											<th>Actual Result</th>
											<th>Date</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
									';
				$i = 1;					
				while($row_lab = imw_fetch_assoc($res_lab)){
					$XML_results_section .= '<tr>
											<td> 	
												<content ID = "lab'.$i.'">'.htmlentities($row_lab['observation']).' [LOINC: '.$row_lab['result_loinc'].']</content>
											</td>
											';
					
					$XML_results_section .= '<td>'." ( ".$row_lab['result']." ".$row_lab['uom']." )".'</td>';
					$XML_results_section .= '<td>';
					$XML_results_section .=(preg_replace("/-/",'',$row_lab['result_date'])>0)?date('M d,Y',strtotime($row_lab['result_date'])):"";
					$XML_results_section .='</td>';
					$XML_results_section .= '<td>'.$row_lab['status'].'</td>';
					$XML_results_section .= '</tr>';
					$i++;
				}
				$XML_results_section .= '</tbody>';
				$XML_results_section .= '</table>';
				}
				
				$XML_results_section .= '</text>';
				$res_lab = imw_query($sql_lab);
				$i = 1;	
				if(imw_num_rows($res_lab)){
					while($row_lab = imw_fetch_assoc($res_lab)){
					
					$XML_results_section .= '<entry typeCode="DRIV">';
					$XML_results_section .= '<organizer classCode="BATTERY" moodCode="EVN">';
					$XML_results_section .= '<templateId root="2.16.840.1.113883.10.20.22.4.1"/>';
					$XML_results_section .= '<id nullFlavor="NI"/>';
					/* BEGIN LAB RESULTS */
					$XML_results_section .= '<!-- Result organizer template  -->';
											/* DYNAMIC CODE FROM LOINC ResultTypeCode  */	
					if($row_lab['loinc']!= "" && $row_lab['service'] != ""){					
					$XML_results_section .= '<code code="'.$row_lab['loinc'].'"
											codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="'.$row_lab['service'].'"/>';
					}else{
						$XML_results_section .= '<code nullFlavor="NI"/>';
					}
					$arrResultStatus = $this->result_status_srh($row_lab['lab_status']);	
					if($arrResultStatus['code']!="" && $arrResultStatus['display_name']!="")					
					$XML_results_section .= '<statusCode code="'.$arrResultStatus['code'].'"/>';
					else
					$XML_results_section .= '<statusCode nullFlavor="NI"/>';
					
					
						
					$XML_results_section .= '<component>';
					$XML_results_section .= '<observation classCode="OBS" moodCode="EVN">';
					$XML_results_section .= '<!-- Result observation template -->';
					$XML_results_section .= '<templateId root="2.16.840.1.113883.10.20.22.4.2"/>';
					$XML_results_section .= '<id nullFlavor="NI"/>';
											/* DYNAMIC CODE FROM LOINC ResultTypeCode  */
					if($row_lab['observation'] != "")						
					$XML_results_section .= '<code code="'.$row_lab['result_loinc'].'"
											codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="'.$row_lab['observation'].'"/>';
					else
					$XML_results_section .= '<code nullFlavor="NI"/>';								
					$XML_results_section .= '<text>';
					$XML_results_section .= '<reference value="#lab'.$i.'"/>';
					$XML_results_section .= '</text>';				
					$XML_results_section .= '<statusCode code="completed"/>';
					if($row_result['result_date'] != "")
					$XML_results_section .= '<effectiveTime value="'.str_replace('-','',$row_lab['result_date']).'"/>';
					else
					$XML_results_section .= '<effectiveTime nullFlavor="NI"/>';
					$XML_results_section .= '<value xsi:type="PQ" value="'.trim($row_lab['result']).'" unit="'.trim($row_lab['uom']).'"/>';
					if($row_lab['abnormal_flag'] != "")
					$XML_results_section .= '<interpretationCode code="'.$row_lab['abnormal_flag'].'" codeSystem="2.16.840.1.113883.5.83"/>';
					else
					$XML_results_section .= '<interpretationCode nullFlavor="NI"/>';
					$XML_results_section .= '<methodCode/>';
					$XML_results_section .= '<targetSiteCode/>';
					$XML_results_section .= '<referenceRange>';
					$XML_results_section .= '<observationRange>';
					$XML_results_section .= '<text>'.htmlentities($row_lab['result_range']).'</text>';
					$XML_results_section .= '</observationRange>';
					$XML_results_section .= '</referenceRange>';
					$XML_results_section .= '</observation>';
					$XML_results_section .= '</component>';
					
					/* END LAB RESULTS */
					$XML_results_section .= '</organizer>';
					$XML_results_section .= '</entry>';	
					$i++;
					}
				}else{
					$XML_results_entry = '<entry typeCode="DRIV">';
					$XML_results_entry .= '<organizer classCode="BATTERY" moodCode="EVN">';
					$XML_results_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.1"/>';
					$XML_results_entry .= '<id nullFlavor="NI"/>';
					/* BEGIN LAB RESULTS */
					$XML_results_entry .= '<!-- Result organizer template  -->';
											/* DYNAMIC CODE FROM LOINC ResultTypeCode  */
					$XML_results_entry .= '<code nullFlavor="NI"/>';
					$XML_results_entry .= '<statusCode nullFlavor="NI"/>';
					$XML_results_entry .= '<component>';
					$XML_results_entry .= '<observation classCode="OBS" moodCode="EVN">';
					$XML_results_entry .= '<!-- Result observation template -->';
					$XML_results_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.2"/>';
					$XML_results_entry .= '<id nullFlavor="NI"/>';
					$XML_results_entry .= '<code nullFlavor="NI"/>';
					$XML_results_entry .= '<statusCode code="completed"/>';
					$XML_results_entry .= '<effectiveTime nullFlavor="NI"/>';
					$XML_results_entry .= '<value xsi:type="PQ" nullFlavor="NI"/>';
					$XML_results_entry .= '<methodCode/>';
					$XML_results_entry .= '<targetSiteCode/>';
					$XML_results_entry .= '</observation>';
					$XML_results_entry .= '</component>';
					/* END LAB RESULTS */
					$XML_results_entry .= '</organizer>';
					$XML_results_entry .= '</entry>';	
					$XML_results_section .= $XML_results_entry;
				}
				$XML_results_section .= '</section>';
				$XML_results_section .= '</component>';
			}
			
			//Begin assessment section
			$arrApVals = array();
			$row = $this->valuesNewRecordsAssess($pid);
			if($row != false){
				$strXml = stripslashes($row["assess_plan"]);
				$oChartApXml = new ChartAP($pid,$row["form_id"]);
				//$arrApVals = $oChartApXml->getVal_Str($strXml);
				$arrApVals = $oChartApXml->getVal();
				$arrApVals = $arrApVals['data']['ap'];
			}
			$XML_assessment_section  = '<component>';
			$XML_assessment_section .= '<section>';
			$XML_assessment_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.8"/>';
			$XML_assessment_section .= '<code codeSystem="2.16.840.1.113883.6.1"
								codeSystemName="LOINC" code="51848-0"
								displayName="ASSESSMENTS"/>';
			$XML_assessment_section .= '<title>ASSESSMENTS</title>';
			$XML_assessment_section .= '<text>';
			$XML_assessment_section .= '<table border = "1" width = "100%">';
			$XML_assessment_section .= '<thead>
									<tr>
										<th>Assessment and plan</th>
									</tr>
								</thead>
								 <tbody>
								';
			$flag = 0;
			foreach($arrApVals as $apVals){
				if($apVals['assessment'] != ""){
				$flag = 1;
				$XML_assessment_section .= '<tr><td>'.htmlentities($apVals['assessment']).'</td></tr>';
				}
			}
			if($flag == 0)
			$XML_assessment_section .= '<tr><td></td></tr>';
			$XML_assessment_section .= '</tbody>
										</table>';
			$XML_assessment_section .='</text>';
			$XML_assessment_section .= '</section>';
			$XML_assessment_section .= '</component>';	
			
			//Begin encounter section
			if($form_id != ""){	
				$XML_encouters_section = '<component>';
				$XML_encouters_section .= '<section>';
				$XML_encouters_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.22"/>';
				$XML_encouters_section .= '<code code="46240-8" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="History of encounters"/>';
				$XML_encouters_section .= '<title>Encounters</title>';
				$XML_encouters_section .= '<text>';
				$XML_encouters_section .= '<table border = "1" width = "100%">';
				$XML_encouters_section .= ' <thead>
										<tr>
											<th>Encounter Diagnosis</th>
											<th>Date</th>
											<th>Status</th>
										</tr>
									</thead>
									 <tbody>';
				$res_prob_list = imw_query($qry);
				$arrProblemList = $this->get_pt_problem_list($form_id, $pid);
				$flag = 0;
				foreach($arrProblemList as $problemList){	
					if(!in_array($problemList['problem_name'],$arrMedHxProbList)){	
					$flag = 1;			
					$XML_encouters_section .= '<tr><td ID="enc_problem'.$problemList['id'].'">'.htmlentities($problemList['problem_name']).'</td>
													<td ID="enc_problem_date'.$problemList['id'].'">'.date('M d,Y',strtotime($problemList['onset_date'])).'</td>
													<td ID="enc_problem_status'.$problemList['id'].'">'.$problemList['status'].'</td>
												</tr>';
					}
				}
				if(in_array('location_info',$arrOptions)){
					//---------BEGIN LOCATION ----------
					$qry = "SELECT sa.sa_facility_id  as facility
					FROM schedule_appointments sa
					JOIN chart_master_table cmt ON cmt.date_of_service = sa.sa_app_start_date
					WHERE sa.sa_patient_id ='".$pid."' 
						AND cmt.id = '".$form_id."'";
					$res = imw_query($qry);						
					$row = imw_fetch_assoc($res);
					$facility = $row['facility'];
					
					$sql_cmt = "SELECT date_of_service 
									FROM chart_master_table 
									WHERE id = '".$form_id."'";
					$res_cmt = imw_query($sql_cmt);
					$row_cmt = imw_fetch_assoc($res_cmt);
							
					if($facility > 0){$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$facility."'";}
					else{$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";}
					$res_facility = imw_query($qry_facility);
					$row_facility = imw_fetch_assoc($res_facility);
					$XML_encouters_section .= '<tr><td>Date and Location of Visit</td>
													<td>'.$row_cmt['date_of_service'].'</td>
													<td>'.htmlentities($row_facility['name']). " - ".$row_facility['street'].",".$row_facility['city']." ".$row_facility['state'].' - '.$row_facility['postal_code'].'</td>
													
												</tr>';
					//---------END LOCATION ----------
				}
				if($flag == 0)
				$XML_encouters_section .= '<tr><td></td></tr>';
				$XML_encouters_section .= '</tbody>';
				$XML_encouters_section .= '</table>';
				$XML_encouters_section .= '</text>';
							
							$XML_encouter_entry = '<entry typeCode="DRIV">';
							$XML_encouter_entry .= '<encounter classCode="ENC" moodCode="EVN">';
							/* BEGIN ENCOUNTER ACTIVITIES */
							$XML_encouter_entry .= '<!-- Encounter Activities -->';
							$XML_encouter_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.49"/>';
							$XML_encouter_entry .= '<id nullFlavor="NI"/>';
							/* DYNAMIC ENCOUNTER TYPE CODE TO GET FROM CHART ENTRY*/ 
							$sql = "SELECT ct.ccda_cpt_code 
									FROM chart_master_table cmt
									JOIN chart_template ct ON cmt.templateId = ct.id
									WHERE id='".$form_id."' AND patient_id = '".$pid."'";
							$res = imw_query($sql);
							$row = imw_fetch_assoc($res);	
							if($row['ccda_cpt_code'] != "" && $row['ccda_cpt_code']!=0){				
							$XML_encouter_entry .= '<code code="'.$row['ccda_cpt_code'].'" displayName="'.$row['temp_name'].'"    
																codeSystem="2.16.840.1.113883.6.12" codeSystemVersion="4"> </code>';
							}else if($row['ccda_cpt_code']==0){				
							$XML_encouter_entry .= '<code code="0123" displayName="comprehensive"    
																codeSystem="2.16.840.1.113883.6.12" codeSystemVersion="4"> </code>';
							}else{
							$XML_encouter_entry .= '<code nullFlavor="NI"/>';	
							}
							$sql = "SELECT cmt.date_of_service as date_of_service,ut.user_type_name as user_type
									FROM chart_master_table cmt 
									JOIN users usr ON usr.id = cmt.providerId
									JOIN user_type ut ON usr.user_type = ut.user_type_id
									WHERE cmt.id = '".$form_id."'";
							$res = imw_query($sql);
							$row = imw_fetch_assoc($res);
							
							if($row['date_of_service'] != ""){
							$XML_encouter_entry .= '<effectiveTime value="'.str_replace("-","",$row['date_of_service']).'"/>';
							}else{
								$XML_encouter_entry .= '<effectiveTime nullFlavor="NI"/>';
							}
							
							$arrProviderType = $this->get_provider_code($row['user_type']);
							
							//------BEGIN CHART PROVIDER INFO --------
							if($arrProviderType['code'] != "" && $arrProviderType['display_name'] != ""){
							$XML_encouter_entry .= '<performer>
													<assignedEntity>';
							$XML_encouter_entry .='<id nullFlavor="NI"/>';
							$XML_encouter_entry .=	'<code code="'.$arrProviderType['code'].'"
													codeSystem="2.16.840.1.113883.6.96"
													codeSystemName="SNOMED CT"
													displayName="'.$arrProviderType['display_name'].'"/>';
							$XML_encouter_entry .= '</assignedEntity>
													</performer>';
							}
							
							//------END CHART PROVIDER INFO --------						
							if(in_array('location_info',$arrOptions)){
							//---------BEGIN LOCATION ----------
							$qry = "SELECT sa.sa_facility_id  as facility
							FROM schedule_appointments sa
							JOIN chart_master_table cmt ON cmt.date_of_service = sa.sa_app_start_date
							WHERE sa.sa_patient_id ='".$pid."' 
								AND cmt.id = '".$form_id."'";
							$res = imw_query($qry);						
							$row = imw_fetch_assoc($res);
							$facility = $row['facility'];
							if($facility > 0){$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$facility."'";}
							else{$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";}
							$res_facility = imw_query($qry_facility);
							$row_facility = imw_fetch_assoc($res_facility);
							$XML_encouter_entry .= '<participant typeCode = "LOC">
												<participantRole classCode = "SDLOC">
											<templateId root = "2.16.840.1.113883.10.20.22.4.32"/>
											<!--Service Delivery Location template -->
											<code nullFlavor="NI"/>';
							$XML_encouter_entry .= '<addr>';
							if($row_facility['street'] != "")
							$XML_encouter_entry .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
							if($row_facility['city'] != "")
							$XML_encouter_entry .= '<city>'.$row_facility['city'].'</city>';
							if($row_facility['state'] != "")
							$XML_encouter_entry .= '<state>'.$row_facility['state'].'</state>';
							if($row_facility['postal_code'] != "")
							$XML_encouter_entry .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
							$XML_encouter_entry .= '<country>US</country>';
							$XML_encouter_entry .= '</addr>';	
							if($row_facility['phone'] != "")
							$XML_encouter_entry .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
							else
							$XML_encouter_entry .= '<telecom nullFlavor="NI"/>';
							$XML_encouter_entry .='
											<playingEntity classCode = "PLC">
												<name>'.htmlentities($row_facility['name']).'</name>
											</playingEntity>
										</participantRole>
									</participant>';
							}
							//---------END LOCATION ----------
							$arrProblemList = $this->get_pt_problem_list($form_id, $pid);
							if(count($arrProblemList)>0){
								foreach($arrProblemList as $problemList){
									if(!in_array($problemList['problem_name'],$arrMedHxProbList)){
									$XML_encouter_entry .= '<entryRelationship typeCode="SUBJ" >';
									/* BEGIN ENCOUNTER DIAGNOSIS ACT*/
									$XML_encouter_entry .= '<act classCode="ACT" moodCode="EVN">';
									$XML_encouter_entry .= '<!-- Encounter diagnosis act -->';
									$XML_encouter_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.80"/>';
									$XML_encouter_entry .= '<code code="29308-4" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="ENCOUNTER DIAGNOSIS"/>';
									$XML_encouter_entry .= '<statusCode code="active"/>';
									$XML_encouter_entry .= '<entryRelationship typeCode="SUBJ" inversionInd="false">';
									$XML_encouter_entry .= '<observation classCode="OBS" moodCode="EVN" negationInd="false">';
									
									$XML_encouter_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.4"/>';
									$XML_encouter_entry .= '<id nullFlavor="NI"/>';
									$arrProbListType = $this->problem_type_srh(strtolower($problemList['prob_type']));
									if($arrProbListType['code']!="" && $arrProbListType['display_name']!=""){
									$XML_encouter_entry .= '<code code="'.$arrProbListType['code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$arrProbListType['display_name'].'"/>';}
									else{
										$XML_encouter_entry .= '<code nullFlavor="NI"/>';
									}
							
									/* BEGIN ENCOUNTER ENTRY */
									$XML_encouter_entry .= '<!-- Problem Observation template -->';
									$arrProbList = array();
									
									$XML_encouter_entry .= '<statusCode code="completed"/>';
															/* DYNAMIC SNOMED CT CODE FROM PROBLEM VALUE SET */
									if($problemList['ccda_code']!=""){						
									$XML_encouter_entry .= '<value xsi:type="CD" code="'.$problemList['ccda_code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$problemList['problem_name'].'"/>';
									}else{
										$arrProblem = $this->getProblemCode($problemList['problem_name']);
												// DYNAMIC REACTION CODE //
										if($arrProblem['ccda_code']!="" && $arrProblem['ccda_display_name']){					
											$XML_encouter_entry .= '<value xsi:type="CD"
																	code="'.$arrProblem['ccda_code'].'"
																	codeSystem="2.16.840.1.113883.6.96"
																	codeSystemName="SNOMED CT"
																	displayName="'.$arrProblem['ccda_display_name'].'"/>';
										}else{
											$XML_encouter_entry .= '<value xsi:type="CD" nullFlavor="NI"/>';
										}  
									}
									/* END ENCOUNTER ENTRY */							
									$XML_encouter_entry .= '</observation>';
									$XML_encouter_entry .= '</entryRelationship>';
									$XML_encouter_entry .= '</act>';
									/* END ENCOUNTER DIAGNOSIS ACT */
									$XML_encouter_entry .= '</entryRelationship>';
								}
								}
							}
							
							/* END ENCOUNTER ACTIVITIES */
							$XML_encouter_entry .= '</encounter>';
							$XML_encouter_entry .= '</entry>';
							
						/* END PROBLEM OBSERVATION */
				$XML_encouters_section .= $XML_encouter_entry;
				$XML_encouters_section .= '</section>';
				$XML_encouters_section .= '</component>';
			}

			//Begin plan of care section
			if(in_array('mu_data_set_ap',$arrOptions) || in_array('future_appointment',$arrOptions) || in_array('clinical_instruc',$arrOptions) || in_array('future_sch_test',$arrOptions) || in_array('recommended_patient_decision_aids',$arrOptions)){
				$XML_plan_of_care_section = '<component>';
				$XML_plan_of_care_section .= '<section>';
				$XML_plan_of_care_section .= '<!-- ** Plan of Care Section Template -->';
				$XML_plan_of_care_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.10"/>';
				$XML_plan_of_care_section .= '<!-- CCDA Plan of Care Section definition requires this code -->';
				$XML_plan_of_care_section .= '<code code="18776-5" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"
												displayName="Treatment plan"/>';
				$XML_plan_of_care_section .= '<title>PLAN OF CARE</title>';
				$XML_plan_of_care_section .= ' <text>';
				$XML_plan_of_care_section .= '<table border = "1" width = "100%">
										<thead>
											<tr>
												<th>Name</th>
												<th>Type</th>
												<th>Date / Reason</th>
											</tr>
										</thead>
										<tbody>';
				$flag = 0;						
				//----BEGIN GOALS/ INSTRUCTION ENTRY-----------------
				if(in_array('mu_data_set_ap',$arrOptions) || in_array('clinical_instruc',$arrOptions)){
					$sql_goal = "SELECT osacnd.inform ,od.name, od.snowmed, osacn.form_id, osacnd.order_set_associate_details_id 
								FROM order_set_associate_chart_notes_details osacnd 
								JOIN order_set_associate_chart_notes osacn ON osacnd.order_set_associate_id = osacn.order_set_associate_id 
								JOIN order_details od ON od.id = osacnd.order_id	
								WHERE osacn.form_id = '".$form_id."' AND patient_id = '".$pat_id."'
										AND osacnd.delete_status = 0 AND osacn.delete_status = 0
								";
					$res_goal = imw_query($sql_goal);				
					
					while($row_goal = imw_fetch_assoc($res_goal)){
						$sql_cmt = "SELECT date_of_service FROM chart_master_table WHERE id = '".$row_goal['form_id']."'";
						$res_cmt = imw_query($sql_cmt);
						$row_cmt = imw_fetch_assoc($res_cmt);
						
						if($row_goal['name'] != ""){
						$flag = 1;	
						$XML_plan_of_care_section .= '<tr><td ID="goal_'.$row_goal['order_set_associate_details_id'].'">'.htmlentities($row_goal['name']).'</td>
														  <td>Goal</td>
														  <td>'.date('M d,Y',strtotime($row_cmt['date_of_service'])).'</td>
														</tr>';
						}
						
						if($row_goal['inform'] != ""){
						$flag = 1;	
						$XML_plan_of_care_section .= '<tr><td ID="instructions_'.$row_goal['order_set_associate_details_id'].'">Instruction : '.htmlentities($row_goal['inform']).'</td>
											<td>Instruction</td>
											<td>'.date('M d,Y',strtotime($row_cmt['date_of_service'])).'</td>
											</tr>';
						}
					}
				}
				//----END GOALS/ INSTRUCTION ENTRY-----------------	
				
				//------- BEGIN FUTURE APPOINTMENTS AND TESTS-------
				if(in_array('future_appointment',$arrOptions) || in_array('future_sch_test',$arrOptions) || in_array('provider_referrals',$arrOptions)){
					$current_date = date("Y-m-d");
					$current_time = date("H:i:s");
					$sql = "SELECT date_of_service FROM chart_master_table WHERE id = '".$form_id."'";
					$res = imw_query($sql);
					$row = imw_fetch_assoc($res);
					$dos = $row['date_of_service'];
					
					$sql = "SELECT * FROM chart_schedule_test_external WHERE patient_id = '".$pid."'";
					if($form_id != ""){
						$sql .= " AND schedule_date >= '".$dos."'";
					}else{
						$sql .= " AND schedule_date >= '".date('Y-m-d')."'";
					}
					$sql .= " AND deleted_by = '0'";
					$res = imw_query($sql);
					
					while($row = imw_fetch_assoc($res)){
						if($row['appoint_test'] == "Test" && in_array('future_sch_test',$arrOptions)){
							$flag = 1;
							$XML_plan_of_care_section .= '<tr><td>'.htmlentities($row['test_type'])." : ".htmlentities($row['test_name']).'</td>
																<td>Future Sch Test</td>
																<td>';
							$XML_plan_of_care_section .=(preg_replace("/-/",'',$row['schedule_date'])>0)?date('M d,Y',strtotime($row['schedule_date'])):"";									
							$XML_plan_of_care_section .=" ".htmlentities($row['variation']).'</td>
																</tr>';
						}
						if($row['appoint_test'] == "Appointment" && in_array('future_appointment',$arrOptions)){
							$flag = 1;
							$XML_plan_of_care_section .= '<tr><td>'.htmlentities($row['reff_phy']).'</td>
																<td>Future Scheduled Appointment</td>
																<td>'.htmlentities($row['phy_address'])." ON ";
							$XML_plan_of_care_section .=(preg_replace("/-/",'',$row['schedule_date'])>0)?date('M d,Y',strtotime($row['schedule_date'])):"";									
							$XML_plan_of_care_section .=" ".htmlentities($row['variation']).'</td></tr>';
						}
						if($row['appoint_test'] == "Referral" && in_array('provider_referrals',$arrOptions)){
							$flag = 1;
							$XML_plan_of_care_section .= '<tr><td>'.htmlentities($row['reff_phy']).'</td>
																<td>Referral to other providers</td>
																<td>'.htmlentities($row['phy_address'])." ON ";
							$XML_plan_of_care_section .=(preg_replace("/-/",'',$row['schedule_date'])>0)?date('M d,Y',strtotime($row['schedule_date'])):"";									
							$XML_plan_of_care_section .=" ".$row['variation']." FOR ".htmlentities($row['reason']).'</td></tr>';
						}
					}
				}	
				//------- END FUTURE APPOINTMENTS AND TESTS----------
				
				//-------BEGIN DIAGNOSTICS TESTS PENDING --------------//
				if(in_array('diagnostic_tests_pending',$arrOptions)){
					$qry = "SELECT * FROM rad_test_data WHERE rad_patient_id = '".$pid."' AND rad_status = 1";									
					$res = imw_query($qry);
					while($row = imw_fetch_assoc($res)){
						$flag = 1;
						$XML_plan_of_care_section .= '<tr><td>RAD : '.htmlentities($row['rad_name']).' [LOINC:'.$row['rad_loinc'].']</td>
															<td>Diagnostics Test Pending</td>
															<td>';
							$XML_plan_of_care_section .=(preg_replace("/-/",'',$row['rad_order_date'])>0)?date('M d,Y',strtotime($row['rad_order_date'])):"";									
							$XML_plan_of_care_section .='</td>
															</tr>';
					}
					
					$qry = "SELECT lor.*,lore.id as result_id FROM lab_test_data ltd 
							LEFT JOIN lab_observation_requested lor ON lor.lab_test_id = ltd.lab_test_data_id 
							LEFT JOIN lab_observation_result lore ON lore.lab_test_id = ltd.lab_test_data_id
							WHERE ltd.lab_patient_id = '".$pid."' AND ltd.lab_status !=3
							";									
					$res1 = imw_query($qry);
					while($row = imw_fetch_assoc($res1)){
						if($row['result_id'] == "" || $row['result_id'] == NULL){
						$flag = 1;
						$XML_plan_of_care_section .= '<tr><td>LAB: '.htmlentities($row['service']).' [LOINC:'.$row['loinc'].']</td>
														<td>Diagnostic Test pending</td>
														<td></td>
														</tr>';
						}
					}
				}
				//-------END DIAGNOSTICS TESTS PENDING --------------//
				
				//-------BEGIN RECOMMENDED PATIENT DECISION AIDS --------------//
				if(in_array('recommended_patient_decision_aids',$arrOptions)){
					$sql = "SELECT dpr.name, doc.ccda_code 
							FROM document_patient_rel dpr 
							JOIN document doc ON dpr.doc_id = doc.id
							WHERE  dpr.p_id  = '".$pid."' AND dpr.form_id = '".$form_id."'
							";
					$res = imw_query($sql);
					while($row = imw_fetch_assoc($res)){
						$flag = 1;
						$XML_plan_of_care_section .= '<tr><td>'.htmlentities($row['name']).' SNOMED CT :'.$row['ccda_code'].'</td><td>Recommended Patient Decision Aids</td><td></td></tr>';
					}
				}
				//-------END RECOMMENDED PATIENT DECISION AIDS --------------//
				
				if($flag == 0)
				$XML_plan_of_care_section .= '<tr><td colspan="3"></td></tr>';
				$XML_plan_of_care_section .= '</tbody>';							
				$XML_plan_of_care_section .= '</table>';
				$XML_plan_of_care_section .= '</text>';
				
				//----BEGIN GOALS/ INSTRUCTION ENTRY-----------------
				if(in_array('mu_data_set_ap',$arrOptions) || in_array('clinical_instruc',$arrOptions)){
					$sql = "SELECT osacnd.inform ,od.name, od.snowmed, osacn.form_id, osacnd.order_set_associate_details_id 
							FROM order_set_associate_chart_notes_details osacnd 
							JOIN order_set_associate_chart_notes osacn ON osacnd.order_set_associate_id = osacn.order_set_associate_id 
							JOIN order_details od ON od.id = osacnd.order_id	
							WHERE osacn.form_id = '".$form_id."' AND patient_id = '".$pat_id."'
							AND osacnd.delete_status = 0 AND osacn.delete_status = 0
							";
					$res = imw_query($sql);
					while($row = imw_fetch_assoc($res)){
						$sql_cmt = "SELECT date_of_service FROM chart_master_table WHERE id = '".$row['form_id']."'";
						$res_cmt = imw_query($sql_cmt);
						$row_cmt = imw_fetch_assoc($res_cmt);
						if($row['name'] != "" && $row['snowmed'] != ""){
						$XML_plan_of_care_entry ='<entry>
												<observation classCode = "OBS" moodCode = "GOL">
													<templateId root = "2.16.840.1.113883.10.20.22.4.44"/>
													<id nullFlavor="NI"/>
													<code code = "'.$row['snowmed'].'" codeSystem = "2.16.840.1.113883.6.96" displayName = "'.$row['name'].'"/>
													<statusCode code = "new"/>
													 <effectiveTime>
														<center value = "'.str_replace("-","",$row_cmt['date_of_service']).'"/>
													</effectiveTime>
												</observation>
											</entry>';
						$XML_plan_of_care_section .= $XML_plan_of_care_entry;	
						}
						if($row['inform'] != ""){
						$XML_plan_of_care_entry ='<entry>
													<act classCode = "ACT" moodCode = "INT">
														<templateId root = "2.16.840.1.113883.10.20.22.4.20"/>
														<code nullFlavor="NI"/>
														<text>
															<reference value = "#instructions_'.$row['order_set_associate_details_id'].'"/>
															'.htmlentities($row['inform']).'
														</text>
														<statusCode code = "completed"/>
													</act>
												</entry>';
						$XML_plan_of_care_section .= $XML_plan_of_care_entry;	
						}
					}
				}
				//----END GOALS/ INSTRUCTION ENTRY-----------------	
				
				//-------BEGIN FUTURE APPOINTMENT ENTRY-------------
				if(in_array('future_appointment',$arrOptions) || in_array('future_sch_test',$arrOptions)){
					$current_date = date("Y-m-d");
					$current_time = date("H:i:s");
					$sql = "SELECT * FROM chart_schedule_test_external WHERE patient_id = '".$pid."'";
					if($form_id != ""){
						$sql .= " AND schedule_date >= '".$dos."'";
					}else{
						$sql .= " AND schedule_date >= '".date('Y-m-d')."'";
					}
					$sql .= " AND deleted_by = '0'";
					$res = imw_query($sql);
					while($row = imw_fetch_assoc($res)){
						
						switch($row['test_type']){
							case "Imaging":
							$ccda_code = $row['snomed'];
							$codeSystem = "2.16.840.1.113883.6.96";
							$codeSystemName = "SNOMED -CT";
							break; 
							
							case "Lab":
							$ccda_code = $row['loinc'];
							$codeSystem = "2.16.840.1.113883.6.1";
							$codeSystemName = "LOINC";
							break;
							
							case "Procedure":
							$ccda_code = $row['cpt'];
							$codeSystem = "2.16.840.1.113883.6.12";
							$codeSystemName = "CPT";
							break;
						}
						
						if($row['appoint_test'] == "Test" && in_array('future_sch_test',$arrOptions)){
						$XML_plan_of_care_entry ='<entry typeCode="DRIV">
														<act moodCode = "RQO" classCode = "ACT">
														<templateId root = "2.16.840.1.113883.10.20.22.4.39"/>
														<id nullFlavor="NI"/>
														<code code = "'.$ccda_code.'" codeSystem = "'.$codeSystem.'" codeSystemName = "'.$codeSystemName.'" displayName = "'.$row['test_name'].'"/>
														<statusCode code = "new"/>
														<effectiveTime>
															<center value = "'.str_replace("-","",$row['schedule_date']).'"/>
														</effectiveTime>
														</act>
													</entry>';	
						}else if($row['user_type'] == "Appointment" && in_array('future_appointment',$arrOptions)){
							$XML_plan_of_care_entry ='<entry typeCode="DRIV">
														<act moodCode = "RQO" classCode = "ACT">
														<templateId root = "2.16.840.1.113883.10.20.22.4.39"/>
														<id nullFlavor="NI"/>
														<code nullFlavor="NI"/>
														<statusCode code = "new"/>
														<effectiveTime>
															<center value = "'.str_replace("-","",$row['schedule_date']).'"/>
														</effectiveTime>
														</act>
													</entry>';	
						}else if($row['user_type'] == "Referral" && in_array('provider_referrals',$arrOptions)){
							$XML_plan_of_care_entry ='<entry typeCode="DRIV">
														<act moodCode = "RQO" classCode = "ACT">
														<templateId root = "2.16.840.1.113883.10.20.22.4.39"/>
														<id nullFlavor="NI"/>
														<code nullFlavor="NI"/>
														<statusCode code = "new"/>
														<effectiveTime>
															<center value = "'.str_replace("-","",$row['schedule_date']).'"/>
														</effectiveTime>
														</act>
													</entry>';	
						}
						$XML_plan_of_care_section .= $XML_plan_of_care_entry;					
					}
				}
				//-------END FUTURE APPOINTMENT ENTRY-------------
				
				//-------BEGIN DIAGNOSTICS RAD TESTS PENDING --------------//
				if(in_array('diagnostic_tests_pending',$arrOptions)){
					$qry = "SELECT * FROM rad_test_data WHERE rad_patient_id = '".$pid."' WHERE rad_status = 1";									
					$res = imw_query($qry);
					while($row = imw_fetch_assoc($res)){
						$XML_plan_of_care_entry ='<entry typeCode="DRIV">
															<observation classCode="OBS" moodCode="RQO">
															<templateId root="2.16.840.1.113883.10.20.22.4.44"/>
															<!-- Plan of Care Activity Observation template -->
															<id nullFlavor="NI"/>
															<code code="'.$row['rad_loinc'].'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"
															displayName="'.$row['rad_name'].'"/>
															<statusCode code="new"/>
															<effectiveTime nullFlavor="NI"/>
															</observation>
														</entry>';
						$XML_plan_of_care_section .= $XML_plan_of_care_entry;									
					}
				}
				//-------END DIAGNOSTICS RAD TESTS PENDING --------------//
				
				//-------BEGIN DIAGNOSTICS LAB TESTS PENDING --------------//
				if(in_array('diagnostic_tests_pending',$arrOptions)){
					$qry = "SELECT lor.*,lore.id as result_id FROM lab_test_data ltd 
							LEFT JOIN lab_observation_requested lor ON lor.lab_test_id = ltd.lab_test_data_id 
							LEFT JOIN lab_observation_result lore ON lore.lab_test_id = ltd.lab_test_data_id
							WHERE ltd.lab_patient_id = '".$pid."' AND ltd.lab_status !=3
							";									
					$res = imw_query($qry);
					while($row = imw_fetch_assoc($res)){
						if($row['result_id'] == "" || $row['result_id'] == NULL){
						$XML_plan_of_care_entry ='<entry typeCode="DRIV">
															<observation classCode="OBS" moodCode="RQO">
															<templateId root="2.16.840.1.113883.10.20.22.4.44"/>
															<!-- Plan of Care Activity Observation template -->
															<id nullFlavor="NI"/>
															<code code="'.$row['loinc'].'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"
															displayName="'.$row['service'].'"/>
															<statusCode code="new"/>
															<effectiveTime nullFlavor="NI"/>
															</observation>
														</entry>';
						$XML_plan_of_care_section .= $XML_plan_of_care_entry;	
						}
					}
				}
				//-------END DIAGNOSTICS RAD TESTS PENDING --------------//
				
				//----BEGIN RECOMMENDED PATIENT DECISION AIDS-----------//
				if(in_array('recommended_patient_decision_aids',$arrOptions)){
					$sql = "SELECT dpr.name, doc.ccda_code 
							FROM document_patient_rel dpr 
							JOIN document doc ON dpr.doc_id = doc.id
							WHERE  dpr.p_id  = '".$pid."' AND dpr.form_id = '".$form_id."'
							";
					$res = imw_query($sql);
					while($row = imw_fetch_assoc($res)){
					$XML_plan_of_care_entry ='<entry typeCode="DRIV">
												<supply moodCode="INT" classCode="SPLY">
												<templateId root="2.16.840.1.113883.10.20.22.4.43"/>
												<!-- ** Plan of Care Activity Supply ** -->
												<id nullFlavor="NI"/>
												<code xsi:type="CE" code="'.$row['ccda_code'].'" codeSystem="2.16.840.1.113883.6.96"
													displayName="'.$row['name'].'"/>
												</supply>
											</entry>';
					}
				}
				$XML_plan_of_care_section .= $XML_plan_of_care_entry;										
				//----END RECOMMENDED PATIENT DECISION AIDS-----------//
				
				$XML_plan_of_care_section .= '</section>';
				$XML_plan_of_care_section .= '</component>';
			}

			//Begin instruction section
			if(in_array('mu_data_set_ap',$arrOptions) || in_array('clinical_instruc',$arrOptions)){
				$sql = "SELECT osacnd.inform ,od.name, od.snowmed, osacn.form_id, osacnd.order_set_associate_details_id 
							FROM order_set_associate_chart_notes_details osacnd 
							JOIN order_set_associate_chart_notes osacn ON osacnd.order_set_associate_id = osacn.order_set_associate_id 
							JOIN order_details od ON od.id = osacnd.order_id	
							WHERE osacn.form_id = '".$form_id."' AND patient_id = '".$pat_id."'
							AND osacnd.delete_status = 0 AND osacn.delete_status = 0
							";
				$res = imw_query($sql);		
				$XML_instructions_section = '<component>';
				$XML_instructions_section .= '<section>';
				$XML_instructions_section .= '<!-- Instructions template ID -->';
				$XML_instructions_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.45"/>';
				$XML_instructions_section .= '<id nullFlavor="NI"/>';
				$XML_instructions_section .= '<code code="69730-0" codeSystem="2.16.840.1.113883.6.1" codeSystemVersion="LOINC"
								  displayName="Instructions"/>';
				$XML_instructions_section .= '<title>INSTRUCTIONS</title>';
				$XML_instructions_section .= '<text>';
				while($row = imw_fetch_assoc($res)){
					if($row['inform'] != ""){
					$XML_instructions_section .= '<paragraph>'.htmlentities($row['inform']).'</paragraph>';
					}
				}
				
				$sql = "SELECT * 
						FROM document_patient_rel
						WHERE p_id = '".$pat_id."'
						";
				$res = imw_query($sql);
				while($row = imw_fetch_assoc($res)){
					if($row['name'] != ""){
					$XML_instructions_section .= '<paragraph>'.htmlentities($row['name']).'</paragraph>';
					}
				}	
				$XML_instructions_section .= '</text>';
				
				$XML_instructions_section .= '</section>';
				$XML_instructions_section .= '</component>';
			}

			//Begin functional status section
			$sql = "SELECT neuroPsych, func_status FROM chart_left_cc_history WHERE patient_id = '".$pid."' AND form_id = '".$form_id."'";
			$res = imw_query($sql);
			$XML_functional_status_section = '<component>';
			$XML_functional_status_section .= '<section>';
			$XML_functional_status_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.14"/>';
			$XML_functional_status_section .= '<!--  ******** Functional status section template   ******** -->';
			$XML_functional_status_section .= '<code code="47420-5" codeSystem="2.16.840.1.113883.6.1"/>';
			$XML_functional_status_section .= '<title>FUNCTIONAL AND CONGNITIVE STATUS</title>';
			$XML_functional_status_section .= ' <text> ';
			if(imw_num_rows($res)>0){	
				$XML_functional_status_section .= '<table border = "1" width = "100%">
								<thead>
									<tr>
										<th>Functional Status</th>
										<th>Congnitive Status</th>
									</tr>
								</thead>
								<tbody>';
				$row = imw_fetch_assoc($res);	
				$XML_functional_status_section .= '<tr>';
				//if($row['func_status'] !=""){			
				$arrFuncStatus = $this->get_functional_status($row['func_status']);
				$XML_functional_status_section .= '<td >'.$arrFuncStatus['display_name'].'</td>';
				$XML_functional_status_section .= '<td >'.htmlentities($row['neuroPsych']).'</td>';
				//}
				$XML_functional_status_section .= '</tr>';
				$XML_functional_status_section .= '</tbody> </table>';
			}						
			$XML_functional_status_section .= '</text>';
			$XML_functional_status_section .= ' <entry typeCode="DRIV">
												<templateId root="2.16.840.1.113883.10.20.22.4.74"/>
												<!-- **** Cognitive Status Result Observation template **** -->';
			$XML_functional_status_section .= '<organizer classCode="CLUSTER" moodCode="EVN">
												<templateId root="2.16.840.1.113883.10.20.22.4.66"/>
												<!-- Cognitive Status Result Organizer template -->
												<id nullFlavor="NI"/>';
			$arrCongStatus = $this->get_cognitive_status($row['neuroPsych']);
			if($arrCongStatus['code']!="" && $arrCongStatus['display_name']!=""){									
			$XML_functional_status_section .= '<code code="'.$arrCongStatus['code'].'" displayName="'.$arrCongStatus['display_name'].'"
												codeSystem="2.16.840.1.113883.6.96"
												codeSystemName="SNOMED CT"/>';
			}
			else 
			$XML_functional_status_section .= '<code nullFlavor="NI"/>';
			$XML_functional_status_section .= '<statusCode code="completed"/>
												<component>
												<observation classCode="OBS" moodCode="EVN">
												<!-- Functional Status Result observation(such as toileting) -->
												<templateId root="2.16.840.1.113883.10.20.22.4.67"/>
												<id nullFlavor="NI"/>';
			$arrFuncStatus = $this->get_functional_status($row['func_status']);
			if($arrFuncStatus['code']!="" && $arrFuncStatus['display_name']!=""){
			$XML_functional_status_section .= '<code code="'.$arrFuncStatus['code'].'"
												displayName="'.$arrFuncStatus['display_name'].'"
												codeSystem="2.16.840.1.113883.6.96"
												codeSystemName="SNOMED CT"/>';
			}
			else 
			$XML_functional_status_section .= '<code nullFlavor="NI"/>';									
			$XML_functional_status_section .= '<statusCode code="completed"/>
												<effectiveTime nullFlavor="NI"/>
												<value xsi:type = "CD" nullFlavor="NI"/>
												</observation>
												</component>
												</organizer>';									
			$XML_functional_status_section .= '</entry>';
			$XML_functional_status_section .= '</section>';
			$XML_functional_status_section .= '</component>';	
			
			//Begin Procedures
			if(in_array('mu_data_set_superbill',$arrOptions)){
				$qry = "SELECT proc.cptCode,proc.procedureName,proc.id,
								sup.dateOfService,sup.timeSuperBill,sup.physicianId 	
						FROM superbill sup
						JOIN procedureinfo proc ON sup.idSuperBill = proc.idSuperBill
						WHERE sup.patientId ='".$pid."' 
								AND sup.formId = '".$form_id."'
								";
				$res = imw_query($qry);
				
				$XML_procedures_section = '<component>';
				$XML_procedures_section .= '<section>';
				$XML_procedures_section .= '		<templateId root="2.16.840.1.113883.10.20.22.2.7.1"/>';
				$XML_procedures_section .= '		<!-- Procedures section template -->';
				$XML_procedures_section .= '		<code code="47519-4" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"
													displayName="HISTORY OF PROCEDURES"/>';
				$XML_procedures_section .= '		<title>PROCEDURES</title>';
				
				$XML_procedures_section .= '		<text>';
				$XML_procedures_section .= '		<table border = "1" width = "100%">';
				$XML_procedures_section .= '			<thead>
														<tr>
															<th>Name</th>
															
															<th>Date</th>
															<th>Provider</th>
														</tr>
													</thead>
													 <tbody>
													 ';
				$flag = 0;
				if(imw_num_rows($res)>0){
					$res = imw_query($qry);											 
					while($row = imw_fetch_assoc($res)){
						$qry_provider = "SELECT * FROM users WHERE id = '".$row['physicianId']."'";  // PRIMARY PHYSICIAN
						$res_provider = imw_query($qry_provider);
						$row_provider = imw_fetch_assoc($res_provider);
						$flag = 1;
						$XML_procedures_section .= '	<tr>
													<td ID = "procedure'.$row['id'].'">'.htmlentities($row['procedureName']).'</td>
													<td ID = "date'.$row['id'].'">';
						$XML_procedures_section .=(preg_replace("/-/",'',$row['dateOfService'])>0)?date('M d,Y',strtotime($row['dateOfService'])):"";							
						$XML_procedures_section .= '</td>
													<td >'.$row_provider['fname']." ".$row_provider['lname'].'</td>
													</tr>';
					}
				}
				
				$sql_sx = "SELECT * FROM lists WHERE type IN (5,6) AND allergy_status = 'Active' AND pid = '".$pid."'";
				$res_sx = imw_query($sql_sx);
				while($row_sx = imw_fetch_assoc($res_sx)){
					$flag = 1;
					$XML_procedures_section .= '	<tr>
												<td ID = "procedure_sx_'.$row_sx['id'].'">'.htmlentities($row_sx['title']).'</td>
												<td ID = "date_sx_'.$row_sx['id'].'">';
					$XML_procedures_section .=(preg_replace("/-/",'',$row_sx['begdate'])>0)?date('M d,Y',strtotime($row_sx['begdate'])):"";
					$XML_procedures_section .='</td>
												<td >'.$row_sx['referredby'].'</td>
												</tr>';
				}
				
				if($flag == 0){
				$XML_procedures_section .= '<tr><td></td></tr>';	
				}
				$XML_procedures_section .= '		</tbody>';
				$XML_procedures_section .= '		</table>';
				$XML_procedures_section .= '		</text>';
				
				$res = imw_query($qry);
				if(imw_num_rows($res)>0){
				while($row = imw_fetch_assoc($res)){
				$XML_procedures_entry = '	<entry>';
				$XML_procedures_entry .= '	<procedure classCode="PROC" moodCode="EVN">';
				$XML_procedures_entry .= '		<!-- Procedure  Activity Procedure Template -->';
				$XML_procedures_entry .= '		<templateId root="2.16.840.1.113883.10.20.22.4.14"/>';
				$XML_procedures_entry .= '		<id nullFlavor="NI"/>';
				$XML_procedures_entry .= '		<code xsi:type="CE" code="'.$row['cptCode'].'" codeSystem="2.16.840.1.113883.6.12"
														displayName="'.$row['procedureName'].'"
														codeSystemName="CPT-4">';
				$XML_procedures_entry .= '				<originalText>';
				$XML_procedures_entry .= '					<reference value="#procedure'.$row['id'].'"/>';
				$XML_procedures_entry .= '				</originalText>';
				$XML_procedures_entry .= '		</code>';
				$XML_procedures_entry .= '		<statusCode code="completed"/>';
				$XML_procedures_entry .= '		<effectiveTime xsi:type="IVL_TS">';
				$XML_procedures_entry .= '				<low value="'.preg_replace("/-/","",$row['dateOfService']).preg_replace("/:/","",$row['timeSuperBill']).'"/>';
				$XML_procedures_entry .= '		</effectiveTime>';
				
				$qry_provider = "SELECT * FROM users WHERE id = '".$row['physicianId']."'";  // PRIMARY PHYSICIAN
				$res_provider = imw_query($qry_provider);
				if(imw_num_rows($res_provider) > 0){
				$row_provider = imw_fetch_assoc($res_provider);
					if($row_user['facility'] > 0){
					$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$row_provider['facility']."'";
					}
					else{
						$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
					}
					$res_facility = imw_query($qry_facility);
					$row_facility = imw_fetch_assoc($res_facility);
					
				$XML_procedures_entry .= '		<performer typeCode="PRF">';
				$XML_procedures_entry .= '			<assignedEntity>';
				$XML_procedures_entry .= '				<!-- NPI 34567 -->';
				if($row_provider['user_npi']!="")
				$XML_procedures_entry .= '				<id extension="'.$row_provider['user_npi'].'" root="2.16.840.1.113883.4.6"/>';
				else
				$XML_procedures_entry .= '				<id nullFlavor="NI"/>';
				
				
				$XML_procedures_entry .= '				<addr use="WP">';
				if($row_facility['street'] != "")
				$XML_procedures_entry .= '					<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
				if($row_facility['city'] != "")
				$XML_procedures_entry .= '					<city>'.$row_facility['city'].'</city>';
				if($row_facility['state'] != "")
				$XML_procedures_entry .= '					<state>'.$row_facility['state'].'</state>';
				if($row_facility['postal_code'] != "")
				$XML_procedures_entry .= '					<postalCode>'.$row_facility['postal_code'].'</postalCode>';
				$XML_procedures_entry .= '					<country>US</country>';
				$XML_procedures_entry .= '				</addr>';
				
				if($row_facility['phone'] != "")
				$XML_procedures_entry .= '				<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
				$XML_procedures_entry .= '				<assignedPerson>';
				$XML_procedures_entry .= '					<name>';
				$XML_procedures_entry .= '						<given>'.$row_provider['fname'].'</given>';
				$XML_procedures_entry .= '						<family>'.$row_provider['lname'].'</family>';
				$XML_procedures_entry .= '					</name>';
				$XML_procedures_entry .= '				</assignedPerson>';
				$XML_procedures_entry .= '				<representedOrganization>';
				$XML_procedures_entry .= '					<id root="1.1.1.1.1.1.1.1.3"/>';
				$XML_procedures_entry .= '					<name>';
				$XML_procedures_entry .= 					htmlentities($row_facility['name']);
				$XML_procedures_entry .= '					</name>';
				
				if($row_facility['phone'] != "")
				$XML_procedures_entry .= '					<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
				
				$XML_procedures_entry .= '					<addr use="WP">';
				if($row_facility['street'] != "")
				$XML_procedures_entry .= '					<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
				if($row_facility['city'] != "")
				$XML_procedures_entry .= '					<city>'.$row_facility['city'].'</city>';
				if($row_facility['state'] != "")
				$XML_procedures_entry .= '					<state>'.$row_facility['state'].'</state>';
				if($row_facility['postal_code'] != "")
				$XML_procedures_entry .= '					<postalCode>'.$row_facility['postal_code'].'</postalCode>';
				$XML_procedures_entry .= '					<country>US</country>';
				$XML_procedures_entry .= '					</addr>';
				
				
				$XML_procedures_entry .= '				</representedOrganization>';
				$XML_procedures_entry .= '			</assignedEntity>';
				$XML_procedures_entry .= '		</performer>';
				}
				$XML_procedures_entry .= '	</procedure>';
				$XML_procedures_entry .= '	</entry>';
				$XML_procedures_section .= $XML_procedures_entry;
				}
				}
				$res_sx = imw_query($sql_sx);
				while($row_sx = imw_fetch_assoc($res_sx)){
					$flag = 1;
				$XML_procedures_entry = '	<entry>';
				$XML_procedures_entry .= '	<procedure classCode="PROC" moodCode="EVN">';
				$XML_procedures_entry .= '		<!-- Procedure  Activity Procedure Template -->';
				$XML_procedures_entry .= '		<templateId root="2.16.840.1.113883.10.20.22.4.14"/>';
				$XML_procedures_entry .= '		<id nullFlavor="NI"/>';
				$XML_procedures_entry .= '		<code xsi:type="CE" code="'.$row_sx['ccda_code'].'" codeSystem="2.16.840.1.113883.6.96"
														displayName="'.$row_sx['title'].'"
														codeSystemName="SNOMED CT">';
				$XML_procedures_entry .= '				<originalText>';
				$XML_procedures_entry .= '					<reference value="#procedure_sx_'.$row_sx['id'].'"/>';
				$XML_procedures_entry .= '				</originalText>';
				$XML_procedures_entry .= '		</code>';
				$XML_procedures_entry .= '		<statusCode code="completed"/>';
				if($row_sx['begdate'] !="" && preg_replace("/-/","",$row_sx['begdate'])>0){
				$XML_procedures_entry .= '		<effectiveTime xsi:type="IVL_TS">';
				$XML_procedures_entry .= '				<low value="'.preg_replace("/-/","",$row_sx['begdate']).'"/>';
				$XML_procedures_entry .= '		</effectiveTime>';
				}else
				$XML_procedures_entry .= '		<effectiveTime nullFlavor="NI"/>';
				
				$qry_provider = "SELECT * FROM refferphysician WHERE physician_Reffer_id = '".$row_sx['referredby_id']."'";  // PRIMARY PHYSICIAN
				$res_provider = imw_query($qry_provider);
				if(imw_num_rows($res_provider) > 0){
				$row_provider = imw_fetch_assoc($res_provider);
				$XML_procedures_entry .= '		<performer typeCode="PRF">';
				$XML_procedures_entry .= '			<assignedEntity>';
				$XML_procedures_entry .= '				<!-- NPI 34567 -->';
				if($row_provider['NPI']!="")
				$XML_procedures_entry .= '				<id extension="'.$row_provider['NPI'].'" root="2.16.840.1.113883.4.6"/>';
				else
				$XML_procedures_entry .= '				<id nullFlavor="NI"/>';
				$XML_procedures_entry .= '				<addr>';
				if($row_provider['Address1'] != "")
				$XML_procedures_entry .= '					<streetAddressLine>'.$row_provider['Address1'].'</streetAddressLine>';
				if($row_provider['City'] != "")
				$XML_procedures_entry .= '					<city>'.$row_provider['City'].'</city>';
				if($row_provider['State'] != "")
				$XML_procedures_entry .= '					<state>'.$row_provider['State'].'</state>';
				if($row_provider['postal_code'] != "")
				$XML_procedures_entry .= '					<postalCode>'.$row_provider['postal_code'].'</postalCode>';
				$XML_procedures_entry .= '					<country>US</country>';
				$XML_procedures_entry .= '				</addr>';
				
				if($row_provider['physician_phone'] != "")
				$XML_procedures_entry .= '				<telecom use="WP" value="tel:+1-'.core_phone_format($row_provider['physician_phone']).'"/>';
				$XML_procedures_entry .= '				<assignedPerson>';
				$XML_procedures_entry .= '					<name>';
				$XML_procedures_entry .= '						<given>'.$row_provider['FirstName'].'</given>';
				$XML_procedures_entry .= '						<family>'.$row_provider['LastName'].'</family>';
				$XML_procedures_entry .= '					</name>';
				$XML_procedures_entry .= '				</assignedPerson>';
				$XML_procedures_entry .= '			</assignedEntity>';
				$XML_procedures_entry .= '		</performer>';
				}
				$XML_procedures_entry .= '	</procedure>';
				$XML_procedures_entry .= '	</entry>';
				$XML_procedures_section .= $XML_procedures_entry;
				}
				
				if($flag == 0){
				$XML_procedures_entry = '	<entry>';
				$XML_procedures_entry .= '	<procedure classCode="PROC" moodCode="EVN">';
				$XML_procedures_entry .= '		<!-- Procedure  Activity Procedure Template -->';
				$XML_procedures_entry .= '		<templateId root="2.16.840.1.113883.10.20.22.4.14"/>';
				$XML_procedures_entry .= '		<id nullFlavor="NI"/>';
				$XML_procedures_entry .= '		<code nullFlavor="NI"/>';
				$XML_procedures_entry .= '		<statusCode code="completed"/>';
				$XML_procedures_entry .= '		<effectiveTime nullFlavor="NI"/>';
				$XML_procedures_entry .= '	</procedure>';
				$XML_procedures_entry .= '	</entry>';
				$XML_procedures_section .= $XML_procedures_entry;
				}
				
				$XML_procedures_section .= '</section>';
				$XML_procedures_section .= '</component>';
			}
			
			
			//Begin chief complaint section
			if(in_array('reason_for_visit',$arrOptions)){
				$sql = "SELECT * FROM chart_left_cc_history WHERE patient_id = '".$pid."' AND form_id = '".$form_id."' ";
				$row = imw_fetch_assoc(imw_query($sql));
				$XML_chief_complaint_section = '<component>
						<section>
							<templateId root = "2.16.840.1.113883.10.20.22.2.13"/>
							<code code = "46239-0" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "LOINC" displayName = "CHIEF COMPLAINT AND REASON FOR VISIT"/>
							<title>CHIEF COMPLAINT</title>
							<text>';
				if($row['ccompliant'] != ""){				
					$XML_chief_complaint_section .= '<table border = "1" width = "100%">
										<thead>
											<tr>
												<th>Reason for Visit/Chief Complaint</th>
											</tr>
										</thead>';
									
					$XML_chief_complaint_section .= '<tbody>
											<tr>
												<td>'.htmlentities($row['ccompliant']).'</td>
											</tr>
										</tbody>';
					
					$XML_chief_complaint_section .= '</table>';
				}
				$XML_chief_complaint_section .= '</text>
					</section>
				</component>';
			}

			//Begin reason for referral section
			if(in_array('provider_referrals',$arrOptions)){
				$sql = "SELECT date_of_service FROM chart_master_table WHERE id = '".$form_id."'";
				$res = imw_query($sql);
				$row = imw_fetch_assoc($res);
				$dos = $row['date_of_service'];
				
				$sql = "SELECT * FROM chart_schedule_test_external WHERE patient_id = '".$pid."' AND appoint_test = 'Referral'";
				if($form_id != ""){
					$sql .= " AND schedule_date >= '".$dos."'";
				}else{
					$sql .= " AND schedule_date >= '".date('Y-m-d')."'";
				}
				$sql .= " AND deleted_by = '0'";
				$sql .= " ORDER BY id DESC LIMIT 0,1";
				$res = imw_query($sql);
				$row = imw_fetch_assoc($res);
				$XML_reason_for_referral = '<component>
											<section>
												<templateId root="1.3.6.1.4.1.19376.1.5.3.1.3.1"/>
												<!-- ** Reason for Referral Section Template ** -->
												<code codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" code="42349-1"
													displayName="REASON FOR REFERRAL"/>
												<title>REASON FOR REFERRAL</title>
												<text>
													<paragraph>'.htmlentities($row['reason']).'</paragraph>
												</text>
											</section>
										</component>';
			}
			
			//Begin XML Body
			$XML_cda_body = '<component>';
			$XML_cda_body .= '<structuredBody>';
				
			
			if(in_array('mu_data_set_smoking',$arrOptions)){
			$XML_cda_body .= '<!-- SOCIAL HISTORY SECTION -->';
			$XML_cda_body .= $XML_social_history_section;		   // INCLUDES SMOKING STATUS
			}
			
			if(in_array('mu_data_set_medications',$arrOptions)){
			$XML_cda_body .= '<!-- MEDICATIONS SECTION -->';
			$XML_cda_body .= $XML_medication_section;
			}
			
			if(in_array('mu_data_set_allergies',$arrOptions)){
			$XML_cda_body .= '<!-- ALLERGIES SECTION -->';
			$XML_cda_body .= $XML_allergies_section;
			}
			
			$XML_cda_body .= '<!-- IMMUNIZATION SECTION -->';
			$XML_cda_body .= $XML_immunization_section;
			
			if(in_array('mu_data_set_vs',$arrOptions)){
			$XML_cda_body .= '<!-- VITAL SIGN SECTION -->';
			$XML_cda_body .= $XML_vital_section;
			}
			
			if(in_array('mu_data_set_problem_list',$arrOptions)){
			$XML_cda_body .= '<!-- PROBLEM SECTION -->';
			$XML_cda_body .= $XML_problem_section;
			}

			if(in_array('mu_data_set_lab',$arrOptions)){
			$XML_cda_body .= '<!-- LAB TESTS SECTION -->';
			$XML_cda_body .= $XML_results_section;				   // INCLUDES LAB RESULTS
			}
			
			
			$XML_cda_body .= '<!-- ASSESSMENT SECTION -->';	
			$XML_cda_body .= $XML_assessment_section;
			
			if($form_id != ""){	
			$XML_cda_body .= '<!-- ENCOUNTERS SECTION -->';
			$XML_cda_body .= $XML_encouters_section;				// INCLUDES PROBLEMS
			}
			
			if(in_array('mu_data_set_ap',$arrOptions) || in_array('future_appointment',$arrOptions) || in_array('future_sch_test',$arrOptions) || in_array('clinical_instruc',$arrOptions) || in_array('recommended_patient_decision_aids',$arrOptions)){
			$XML_cda_body .= '<!-- PLAN OF CARE SECTION -->';
			$XML_cda_body .= $XML_plan_of_care_section;				// INCLUDES CHART ASSESSMENTS
			}
			
			
			if(in_array('mu_data_set_ap',$arrOptions) || in_array('clinical_instruc',$arrOptions)){
			$XML_cda_body .= '<!-- INSTRICTIONS SECTION -->';      // INCLUDED STATIC
			$XML_cda_body .= $XML_instructions_section;
			}
			
			$XML_cda_body .= '<!-- FUNCTIONAL STATUS SECTION -->';  // INCLUDED STATIC
			$XML_cda_body .= $XML_functional_status_section;
			
			if(in_array('reason_for_visit',$arrOptions)){
			$XML_cda_body .= '<!-- CHIEF COMPLAINT AND REASON FOR VISIT SECTION -->';  // INCLUDED STATIC
			$XML_cda_body .= $XML_chief_complaint_section;
			}
			
			if(in_array('visit_medication_immu',$arrOptions)){
			$XML_cda_body .= '<!-- MEDICATIONS ADMINISTERED SECTION -->';  // INCLUDED STATIC
			$XML_cda_body .= $XML_medication_admin_section;
			}
			
			if(in_array('mu_data_set_superbill',$arrOptions)){	
			$XML_cda_body .= '<!-- PROCEDURES SECTION -->';   // INCLUDED STATIC
			$XML_cda_body .= $XML_procedures_section;
			}
			if(in_array('provider_referrals',$arrOptions)){
			$XML_cda_body .= '<!-- REASON FOR REFERRAL SECTION -->';   // INCLUDED STATIC
			$XML_cda_body .= $XML_reason_for_referral;
			}
			
			$XML_cda_body .= '</structuredBody>';
			$XML_cda_body .= '</component>';	
			
			$xml = '<?xml version="1.0" encoding="UTF-8"?>';
			$xml .= '<?xml-stylesheet type="text/xsl" href="CDA.xsl"?>';
			$xml .= '<ClinicalDocument xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
			 xsi:schemaLocation="urn:hl7-org:v3 CDA_SDTC.xsd"
			 xmlns="urn:hl7-org:v3"
			 xmlns:cda="urn:hl7-org:v3"
			 xmlns:sdtc="urn:hl7-org:sdtc">
			  <realmCode code="US"/>
			  <typeId root="2.16.840.1.113883.1.3" extension="POCD_HD000040"/>
			  <!-- indicates conformance with US Realm Clinical Document Header template -->
			  <templateId root="2.16.840.1.113883.10.20.22.1.1"/>
			  <!-- conforms to CCD requirements -->
			  <templateId root="2.16.840.1.113883.10.20.22.1.2"/>
			  <!-- conforms to a H&P Note (History and Physical) -->
			  <templateId root="2.16.840.1.113883.10.20.22.1.3"/>
			  <id extension="Test CCDA" root="1.1.1.1.1.1.1.1.1"/>
			  <code codeSystem="2.16.840.1.113883.6.1"
					codeSystemName="LOINC" code="34133-9"
					displayName="Summarization of patient data"/>
			  <title>Health History &amp; Physical</title>
			  <effectiveTime value="'.$currentDate.'"/>
			  <confidentialityCode code="N" codeSystem="2.16.840.1.113883.5.25"/>
			  <languageCode code="en-US"/>';
			$xml .= $XMLpatient_data;
			$xml .= $XML_author_data;
			$xml .= $XML_data_enterer_data;	
			
			$xml .= $XML_custodian_data;
			if(in_array('mu_data_set_care_team_members',$arrOptions) || in_array('provider_info',$arrOptions))
			$xml .= $XML_documentationof_data; // CARE TEAM MEMBERS	
		
			if(in_array('provider_referrals',$arrOptions) && $form_id != ""){
				$xml .= $XML_referral_to_providers;
			}
			
			$xml .= $XML_cda_body;
			$xml .= '</ClinicalDocument>';

			$XML_file_name = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/xml/ccda_r2_xml.xml";
			file_put_contents($XML_file_name,$xml);

			// IMPLEMENT ENCRYPTION KEY
			$AESPatientID = $AESPatientDOB = $AESPatientDOBMonth = $AESPatientDOBDay = $AESPatientFName = $AESPatientLName = "";
			$AESPatientID = $pid;
			$AESPatientDOB = $rowPatient['DOB'];
			$AESPatientFName = $rowPatient['fname'];
			$AESPatientLName = $rowPatient['lname'];

			if(isset($_REQUEST['create_type']) && $_REQUEST['create_type'] == "attachment"){
				$plainFileName = "imedic-PLAIN-".time();
				$plain_file_name = $plainFileName.".xml";
				$save_directory = $this->set_attach_dir_path();
				
				$fileToCreatePlain = $save_directory."/mails/".$plain_file_name;	
				file_put_contents($fileToCreatePlain,file_get_contents($XML_file_name));
				$fileToCreatePlain = realpath($fileToCreatePlain);
				$file_size =  filesize($fileToCreatePlain);
				$file_size =  $this->formatBytes($file_size);
				$file_mime = 'text/plain';
				
				$local_time = date('Y-m-d H:i:s');
				imw_query("INSERT INTO log_ccda_creation  SET patient_id='".$pid."', form_id = '".$form_id."',date_time = '".$local_time."',print_by = '".$_SESSION['authId']."',dos = '".$dos."',sending_application = 'iDoc',file_name= '".$plain_file_name."',file_path='".imw_real_escape_string("/UserId_".$_SESSION['authId']."/mails/".$plain_file_name)."',size='".$file_size."',mime='".$file_mime."',type = 1,session_id='".session_id()."'");
				$log_ccda_id = imw_insert_id();
				if($log_ccda_id){
					imw_query("INSERT INTO pt_printed_records  SET patient_id='".$pid."', form_id = '".$form_id."',date_time = '".$local_time."',print_by = '".$_SESSION['authId']."',dos = '".$dos."',sending_application = 'iDoc',export_type = 'CCDA' ");
				}
				if($_REQUEST['app_services'] == 1){
					$arr_ccda_log['ccda_log_id'] = $log_ccda_id;
				}
				else{
					if(isset($_REQUEST['callFrom']) && $_REQUEST['callFrom'] == "consult_letter"){
						$_REQUEST['ccda_log_id'] = $log_ccda_id;
					}else{
						echo $log_ccda_id;
						die();
					}
				}
				
			}else{
				$ASEKEY = trim($enc_key);
				$aes = new AES($ASEKEY);
				$encData = $aes->encrypt(file_get_contents($XML_file_name));
				$timeVal = time();
				$encFileName = "imedic-ENC-".$timeVal;
				$fileToCreate = $fileToCreateHASH = "";
				$fileToCreate = 'imedic_files/'.$encFileName.".xml";
				$fileToCreate = write_html($encData,$fileToCreate);				
				$encDataHashVal = hash('sha256',file_get_contents($fileToCreate));
				$responseData = "";
				$responseData = $encDataHashVal."~~".$fileToCreate;		
				$patFileName = $rowPatient['lname']."_".$rowPatient['fname'];
				$dos = "";
				if($form_id!="" && $form_id>0){
					$qry = "SELECT date_of_service FROM chart_master_table WHERE id=".$form_id;
					$res = imw_query($qry);
					$row_chart = imw_fetch_assoc($res);
					$dos = $row_chart['date_of_service'];
				}
				imw_query("INSERT INTO pt_printed_records  SET patient_id='".$pid."', form_id = '".$form_id."',date_time = '".date('Y-m-d H:i:s')."',print_by = '".$_SESSION['authId']."',dos = '".$dos."',sending_application = 'iDoc',export_type = 'CCDA' ");
					$arrAllFiles[] = $fileToCreate;
				$count++;
			}

			if(count($arrData)<=0){
				echo "<tr><td colspan='7' align='center' height='50px'>No records found</td></tr>";
			}	
		}
		
		//Creating Zip file
		if(count($arrAllFiles) > 0){
			if(count($arrAllFiles) > 1){
				$sid = session_id();
				$files = $arrAllFiles;
				$zipname = 'fileCCD-'.time().'.zip';
				$zip = new ZipArchive;
				$zip->open($zipname, ZipArchive::CREATE);
				foreach ($files as $file) {
				  $zip->addFile($file);
				}
				$zip->close();
				
				//
				$encDataHashVal = hash('sha256',file_get_contents($zipname));
				$zipname = base64_encode($zipname);
				//Show Links --
				if(isset($_REQUEST['create_type']) && $_REQUEST['create_type'] == "printing"){
					echo "<a href='javascript:void[0]' class='btn btn-primary' onclick='download_ccd_export($zipname,'zip')'>Click to download CCDA zip</a>~~$encDataHashVal";
				}else{
					echo "<tr>
					<td class='text_12' bgcolor='#FFFFFF'>&nbsp;</td>
					<td class='text_12' bgcolor='#FFFFFF'>1.</td>				
					<td class='text_12' bgcolor='#FFFFFF'><a href='javascript:void[0]' class='btn btn-primary' onclick='download_ccd_export($zipname,'zip')'>Click to download CCDA zip</a></td>
					<td class='text_12' bgcolor='#FFFFFF'>$encDataHashVal</td>
					</tr>";	
				}
			}
			elseif(count($arrAllFiles) == 1){
				if(isset($_REQUEST['create_type']) && $_REQUEST['create_type'] == "printing"){
				echo "<a href='javascript:void[0]' class='btn btn-primary' onclick='download_ccd_export(\"".base64_encode($fileToCreate)."\")'>Click to download CCDA</a>~~$encDataHashVal";
				}else{
				echo "<tr>
					<td class='text_12' bgcolor='#FFFFFF'>&nbsp;</td>
					<td class='text_12' bgcolor='#FFFFFF'>1.</td>				
					<td class='text_12' bgcolor='#FFFFFF'><a href='javascript:void[0]' class='btn btn-primary' onclick='download_ccd_export(".base64_encode($fileToCreate).")'>Click to download CCDA</a></td>
					<td class='text_12' bgcolor='#FFFFFF'>$encDataHashVal</td>
					</tr>";	
				}
			}
		}	
	}
	
	function formatBytes($bytes, $precision = 2) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
	
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
	
		$bytes /= pow(1024, $pow);
	
		return round($bytes, $precision) . ' ' . $units[$pow];
	} 
	function set_attach_dir_path(){
			$upload_dir = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/users";
			if(!is_dir($upload_dir)){
				mkdir($upload_dir,0700);
			}
			$uDir = $upload_dir."/UserId_".$_SESSION['authId'];
			if(!is_dir($uDir)){
				mkdir($uDir,0700);
			}
			$uDirMailAttach = $upload_dir."/UserId_".$_SESSION['authId']."/mails";
			if(!is_dir($uDirMailAttach)){
				mkdir($uDirMailAttach,0700);
			}
			$save_directory = $uDir."/";
			return $save_directory;
		}
	function marr_status_srh($val){
		$val = trim($val);
		$arrMartitalStatus = array(
							array("imw"=>'married',"code"=>"M","display_name"=> "Married"),
							array("imw"=>'single',"code"=>"S","display_name"=> "Never Married"),								  
							array("imw"=>'divorced',"code"=>"D","display_name"=> "Divorced"),
							array("imw"=>'widowed,widow',"code"=>"W","display_name"=> "Widowed"),
							array("imw"=>'separated',"code"=>"L","display_name"=> "Legally Separated"),
							array("imw"=>'domestic partner',"code"=>"T","display_name"=> "Domestic Partner")
						  );
		$arr = array();
		if($val != ""){
			foreach($arrMartitalStatus as $row){
				$arr = explode(',',$row['imw']);
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}
			}
		}
		return $arr;
	}

	function gender_srh($val){
		$val = trim($val);
		$arrGender = array(
							array("imw"=>'male',"code"=>"M","display_name"=> "Male"),
							array("imw"=>'female',"code"=>"F","display_name"=> "Female")							  
						  );
		$arr = array();
		if($val != ""){
			foreach($arrGender as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}else{
					$arr['code'] = "UN";
					$arr['display_name'] = "Undifferentiated";
				}
			}
		}
		return $arr;
	}

	function race_srh($val){
		$val = trim(strtolower($val));
		$arrRace = array(
							array("imw"=>'american indian or alaska native',"code"=>"1002-5","display_name"=> "American Indian or Alaska Native"),
							array("imw"=>'asian',"code"=>"2028-9","display_name"=> "Asian"),
							array("imw"=>'black or african american',"code"=>"2054-5","display_name"=> "Black or African American"),	
							array("imw"=>'native hawaiian or other pacific islander',"code"=>"2076-8","display_name"=> "Native Hawaiian or Other Pacific Islander"),
							array("imw"=>'latin american',"code"=>"2178-2","display_name"=> "Latin American"),
							array("imw"=>'white',"code"=>"2106-3","display_name"=> "White"),											  
						  );
		$arr = array();
		if($val != ""){
			foreach($arrRace as $row){
				$arr = explode(',',$row['imw']);
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}else{
					$arr['code'] = "2131-1";
					$arr['display_name'] = "Other Race";
				}
			}
		}
		return $arr;
	}
	function ethnicity_srh($val){
		$val = trim($val);
		$arrRace = array(
							array("imw"=>'hispanic or latino',"code"=>"2135-2","display_name"=> "Hispanic or Latino")
						  );
		$arr = array();
		if($val != ""){
			foreach($arrRace as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}else{
					$arr['code'] = "2186-5";
					$arr['display_name'] = "Not Hispanic or Latino";
				}
			}
		}
		return $arr;
	}
	function language_srh($val){
		$val = trim($val);
		$arrLang = array(
							array("imw"=>'english',"code"=>"eng","display_name"=> "English"),
							array("imw"=>'spanish',"code"=>"spa","display_name"=> "Spanish"),
							array("imw"=>'japanese',"code"=>"jpn","display_name"=> "Japanese"),
							array("imw"=>'french',"code"=>"fre","display_name"=> "French"),
							array("imw"=>'italian',"code"=>"ita","display_name"=> "Italian"),
							array("imw"=>'portuguese',"code"=>"por","display_name"=> "Portuguese"),
							array("imw"=>'german',"code"=>"gem","display_name"=> "Germanic languages"),
							array("imw"=>'russian',"code"=>"rus","display_name"=> "Russian")
						  );
		$arr = array();
		if($val != ""){
			foreach($arrLang as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}else{
					$arr['code'] = "";
					$arr['display_name'] = "";
				}
			}
		}
		return $arr;
	}

	function smoking_status_srh($val){ // 	 SNOMED CT
		$val = trim($val);
		$arrSmoking = array(
							array("imw"=>'current every day smoker',"code"=>"449868002","display_name"=> "Current every day smoker"),
							array("imw"=>'current some day smoker',"code"=>"428041000124106","display_name"=> "Current some day smoker"),
							array("imw"=>'former smoker',"code"=>"8517006","display_name"=> "Former smoker"),
							array("imw"=>'never smoked',"code"=>"266919005","display_name"=> "Never smoker"),
							array("imw"=>'smoker, current status unknown',"code"=>"77176002","display_name"=> "Smoker, current status unknown"),
							array("imw"=>'unknown if ever smoked',"code"=>"266927001","display_name"=> "Unknown if ever smoked"),
							array("imw"=>'heavy tobacco smoke',"code"=>"428071000124103","display_name"=> "Heavy tobacco smoker"),
							array("imw"=>'light tobacco smoker',"code"=>"428061000124105","display_name"=> "Light tobacco smoker")
						  );
		$arr = array();
			foreach($arrSmoking as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}else{
					$arr['code'] = "266927001";
					$arr['display_name'] = "Unknown if ever smoked";
				}
			}
		return $arr;
	}
	function problem_type_srh($val){ 		// 	 SNOMED CT
		$val = trim(strtolower($val));
		$arrProbType = array(
							array("imw"=>'finding',"code"=>"404684003","display_name"=> "Finding"),
							array("imw"=>'complaint',"code"=>"409586006","display_name"=> "Complaint"),
							array("imw"=>'diagnosis',"code"=>"282291009","display_name"=> "Diagnosis"),
							array("imw"=>'condition',"code"=>"64572001","display_name"=> "Condition"),
							array("imw"=>'smoker, current status unknown',"code"=>"248536006","display_name"=> "Finding of functional performance and activity"),
							array("imw"=>'symptom',"code"=>"418799008","display_name"=> "Symptom"),
							array("imw"=>'problem',"code"=>"55607006","display_name"=> "Problem"),
							array("imw"=>'cognitive function finding',"code"=>"373930000","display_name"=> "Cognitive function finding")
						  );
		$arr = array();
		if($val != ""){
			foreach($arrProbType as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}else{
					$arr['code'] = '';
					$arr['display_name'] = '';
				}
			}
		}
		return $arr;
	}

	function allergy_type_srh($val){ 	// 	 SNOMED CT
		$val = trim($val);
		$arrAllerType = array(
							array("imw"=>'fdbATAllergenGroup',"code"=>"419199007","display_name"=> "Allergy to substance (disorder)"),
							array("imw"=>'fdbATDrugName',"code"=>"416098002","display_name"=> "Drug allergy (disorder)"),
							array("imw"=>'fdbATIngredient',"code"=>"414285001","display_name"=> "Food allergy (disorder)")
						  );
		$arr = array();
		if($val != ""){
			foreach($arrAllerType as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}
			}
		}
		return $arr;
	}
	function result_status_srh($val){ 	// 	 SNOMED CT
		$val = trim($val);
		$arrResultStatus = array(
							array("imw"=>'1',"code"=>"active","display_name"=> "active"),
							array("imw"=>'3',"code"=>"cancelled","display_name"=> "cancelled"),
							array("imw"=>'2',"code"=>"completed","display_name"=> "completed"),
						  );
		$arr = array();
		if($val != ""){
			foreach($arrResultStatus as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}
			}
		}
		return $arr;
	}
	
	function vs_result_type_srh($val){
		$val = trim($val);
		$arrVSType = array(
							array("imw"=>'Respiration',"code"=>"9279-1","display_name"=> "Respiratory Rate"),
							array("imw"=>'O2Sat',"code"=>"2710-2","display_name"=> "O2 % BldC Oximetry"),
							array("imw"=>'B/P - Systolic',"code"=>"8480-6","display_name"=> "BP Systolic"),
							array("imw"=>'B/P - Diastolic',"code"=>"8462-4","display_name"=> "BP Diastolic"),
							array("imw"=>'Temperature',"code"=>"8310-5","display_name"=> "Body Temperature"),
							array("imw"=>'Height',"code"=>"8302-2","display_name"=> "Height"),
							array("imw"=>'Weight',"code"=>"3141-9","display_name"=> "Weight Measured"),
							array("imw"=>'BMI',"code"=>"39156-5","display_name"=> "BMI (Body Mass Index)")
						  );
		$arr = array();
		if($val != ""){
			foreach($arrVSType as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}
			}
		}
		return $arr;
	}
	function get_medical_data($form_id='', $arrType, $pid){
		$strType = implode(',',$arrType);
		$dataFinal = array();
		if(isset($form_id) && $form_id != ''){
			$sql_arc  = "select lists 
					from  
					chart_genhealth_archive 
					where patient_id='".$pid."' and
					form_id = '".$form_id."'";
		}else{
			$sql_list  = "select * ,
							date_format(begdate,'%m/%d/%y') as DateStart from lists where pid='".$pid."' and
							allergy_status = 'Active' and type in($strType) order by id";
		}
		if($sql_list != ""){
			$res_list = imw_query($sql_list);	
			while($row_list = imw_fetch_assoc($res_list))	{
				$dataFinal[] = $row_list;
			}
		}
		if($sql_arc != ""){
			$res_arc = imw_query($sql_arc);

			$dataFinal = array();
			while($row_arc = imw_fetch_assoc($res_arc)){
				$arrList = unserialize($row_arc['lists']);
				foreach($arrList as $arrData){
					foreach($arrData as $data){
						if(in_array($data['type'],$arrType)){
							if($data['allergy_status'] == 'Active'){
								$dataFinal[] = $data;
							}
						}
					}
				}
			}
		}
		return $dataFinal;
	}

	function get_pt_problem_list($form_id='', $pid){
		$strType = implode(',',$arrType);
		$dataFinal = array();
		if(isset($form_id) && $form_id != ''){
			$sql_arc  = "select pt_problem_list 
					from  
					chart_genhealth_archive 
					where patient_id='".$pid."' and
					form_id = '".$form_id."'";
		}else{
			$sql  = "SELECT * FROM pt_problem_list WHERE pt_id = '".$pid."' AND status = 'Active'";
		}
		if($sql != ""){
			$res = imw_query($sql);	
			while($row = imw_fetch_assoc($res))	{
				$dataFinal[] = $row;
			}
		}
		if($sql_arc != ""){
			$res_arc = imw_query($sql_arc);

			$dataFinal = array();
			while($row_arc = imw_fetch_assoc($res_arc)){
				$arrList = unserialize($row_arc['pt_problem_list']);
				foreach($arrList as $arrData){
						if($arrData['status'] == 'Active'){
							$dataFinal[] = $arrData;
						}
				}
			}
		}
		return $dataFinal;
	}

	function getSite($val){
		switch($val){
			case "1":
				$site = "OS";
			break;
			case "2":
				$site = "OD";
			break;
			case "3":
				$site = "OU";
			break;
			case "4":
				$site = "PO";
			break;
		}
		return $site;
	}
	function getRXNormCode($str){
		$arr = array();
		$sql = "select RXCUI,STR from ".constant("UMLS_DB").".rxnconso where STR = '".$str."' and SAB='RXNORM'";
		$res = imw_query($sql);
		if(imw_num_rows($res)>0){
			$row = imw_fetch_assoc($res);
			$arr['ccda_code'] = $row['RXCUI'];	
			$arr['ccda_display_name'] = $str;		
		}else{
			$medNameTemp = "";
			$medNameTemp = trim($str);
			$medNameTemp = str_replace("-"," ",$medNameTemp);						
			
			$arrMedictionName = explode(" ",$medNameTemp);
			$qryMore = "";
			if(count($arrMedictionName) > 1){
				foreach($arrMedictionName as $val){
					$qryMore .= " `STR` LIKE '%$val%' and";
				}
			}
			$qryMore = substr(trim($qryMore), 0, -3); 
			$sql = "select RXCUI,STR from ".constant("UMLS_DB").".rxnconso where '".$qryMore."' and SAB='RXNORM'  LIMIT 1";
			$res = imw_query($sql);
			$row = imw_fetch_assoc($res);
			$arr['ccda_code'] = $row['RXCUI'];	
			$arr['ccda_display_name'] = $str;	
		}
		return $arr;
	}
	function getRXNorm_by_code($ccda_code){
		$arr = array();
		$sql = "select RXCUI,STR from  ".constant("UMLS_DB").".rxnconso where RXCUI = '".$ccda_code."' and SAB='RXNORM'";
		$res = imw_query($sql);
		$row = imw_fetch_assoc($res);
		$arr['ccda_code'] = $ccda_code;	
		$arr['ccda_display_name'] = $row['STR'];
		return $arr;
	}
	function getRouteCode($route){
		global $routeset_codes,$routeset_nci_codes;
		$arr = array();
		if($routeset_codes[$route] != ""){
			$arr['ccda_code'] = $routeset_nci_codes[$route];
			$arr['ccda_display_name'] = $routeset_codes[$route];
		}else{
		$sql = "select code,term from  ".constant("UMLS_DB").".route_nci_thesaurus where LOWER(term) = '".strtolower($route)."' OR LOWER(code) = '".strtolower($route)."'";
		$res = imw_query($sql);
		$row = imw_fetch_assoc($res);
		$arr['ccda_code'] = $row['code'];
		$arr['ccda_display_name'] = $row['term'];
		}
		return $arr;
	}
	
	function getProblemCode($str){
		$arr = array();
		$sql = "select Concept_Code,Preferred_Concept_Name from ".constant("UMLS_DB").".problem_list where (Concept_Name = '".$str."' or Preferred_Concept_Name ='".$str."') and Code_System_OID = '2.16.840.1.113883.6.96'";
		$res = imw_query($sql);
		if(imw_num_rows($res)>0){
			$row = imw_fetch_assoc($res);
			$arr['ccda_code'] = $row['Concept_Code'];	
			$arr['ccda_display_name'] = $row['Preferred_Concept_Name'];	
		}else{
			$tmp = trim($str);
			$tmp = str_replace("-"," ",$tmp);						
			
			$arrTmp = explode(" ",$tmp);
			$qryMore = "";
			if(count($arrTmp) > 1){
				foreach($arrTmp as $val){
					$qryMore .= "(`Concept_Name` LIKE '%$val%' or Preferred_Concept_Name LIKE '%$val%')";
				}
			}
			$qryMore = substr(trim($qryMore), 0, -3); 
			$sql = "select Concept_Code,Preferred_Concept_Name from ".constant("UMLS_DB").".problem_list where '".$qryMore."' and and Code_System_OID = '2.16.840.1.113883.6.96' LIMIT 1";
			$res = imw_query($sql);
			$row = imw_fetch_assoc($res);
			$arr['ccda_code'] = $row['Concept_Code'];	
			$arr['ccda_display_name'] = $row['Preferred_Concept_Name'];
		}
		return $arr;
	}
	function get_functional_status($val){
		$arr = array();
		$val = trim($val);
		if($val == "NE"){
			$arr['code'] = "";
			$arr['display_name'] = "Not Evaluated";
		}else if($val == 0){
			$arr['code'] = "66557003";
			$arr['display_name'] = "No Disability";
		}else if($val >= 10 && $val<=30){
			$arr['code'] = "161043008";
			$arr['display_name'] = "Mild Disability";
		}else if($val >= 40 && $val<=70){
			$arr['code'] = "161044002";
			$arr['display_name'] = "Moderate Disability";
		}else if($val >= 80 && $val<=100){
			$arr['code'] = "161045001";
			$arr['display_name'] = "Severe Disability";
		}
		return $arr;
	}
	function get_cognitive_status($val){
		$val = trim($val);
		$arrResultStatus = array(
							array("imw"=>'Alert',"code"=>"248233002","display_name"=> "Alert"),
							array("imw"=>'Oriented X3',"code"=>"426224004","display_name"=> "No Disability"),
							array("imw"=>'Confused',"code"=>"162702000","display_name"=> "Slight Disability"),
							array("imw"=>'Agitated',"code"=>"162721008","display_name"=> "Moderate Disability"),
							array("imw"=>'Flat Affect',"code"=>"932006","display_name"=> "Severe Disability"),
							array("imw"=>'Uncooperative',"code"=>"248042003","display_name"=> "Severe Disability"),
							array("imw"=>'Mentally Retarded',"code"=>"419723007","display_name"=> "Severe Disability")
						  );
		$arr = array();
		if($val != ""){
			foreach($arrResultStatus as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}
			}
		}
		return $arr;
	}

	function get_provider_code($val){
		$val = trim($val);
		$arrResultStatus = array(
							array("imw"=>'Attending Physician',"code"=>"405279007","display_name"=> "Attending physician"),
							array("imw"=>'Physician',"code"=>"309343006","display_name"=> "Physician"),
							array("imw"=>'Resident',"code"=>"405277009","display_name"=> "Resident physician"),
							array("imw"=>'Consultant',"code"=>"158967008","display_name"=> "Consultant physician")
						  );
		$arr = array();
		if($val != ""){
			foreach($arrResultStatus as $row){
				if(in_array($val, $row)){
					$arr['code'] = $row['code'];
					$arr['display_name'] = $row['display_name'];
					break;
				}
			}
		}
		return $arr;
	}
	
	function valuesNewRecordsAssess($patient_id,$sel=" * ",$LF="0",$flgmemo=1){
		$return_arr = array();
		$strmemo = ($flgmemo==1) ? "AND chart_master_table.memo != '1' " : "" ;
		$LF = ($LF == "1") ? "AND chart_master_table.finalize = '1' " : "";
		$qry = imw_query("SELECT ".$sel." FROM chart_master_table ".
			  "INNER JOIN chart_assessment_plans ON chart_master_table.id = chart_assessment_plans.form_id ".
			  "WHERE chart_master_table.patient_id = '$patient_id' AND chart_master_table.delete_status='0' AND chart_master_table.purge_status='0' ".
			  "AND chart_master_table.record_validity = '1' ".
			  $strmemo. //do not get memo assessments and plans: 08-04-2014
			  $LF.
			  "ORDER BY chart_master_table.date_of_service DESC, chart_master_table.create_dt DESC, chart_master_table.id DESC LIMIT 0,1 ");
		while($row = imw_fetch_array($qry)){
			$return_arr[] = $row;
		}
		return $return_arr;
	}
	
	
	public function connectToImedicware($host,$port,$login,$pass,$dbase){	
		$imedicLink = mysqli_connect($host, $login, $pass);	
		$db_selected = mysqli_select_db($dbase, $imedicLink);
		if ($db_selected === false) {
			return false;	
		}
		else{
			return true;	
		}	
	}

	public function connectToUMLS($host,$port,$login,$pass,$dbase){	
		$UMLSLink = mysqli_connect($host, $login, $pass);	
		$db_selected = mysqli_select_db($dbase, $UMLSLink);
		if ($db_selected === false) {
			return false;	
		}
		else{
			return true;	
		}	
	}

	public function setLogOfPtPrintedRec($pid,$form_id,$loginUserName,$dos,$sendingApplication='',$export_type = ''){
		//$loginUserName = $_SESSION['authProviderName'];	
		$printDtTime = date('Y-m-d H:i:s');
		$ptPrintedRecordsQry = "INSERT INTO pt_printed_records SET 
								patient_id='".$pid."',
								form_id='".$form_id."',
								date_time='".$printDtTime."',
								print_by='".$loginUserName."',
								dos='".$dos."',
								sending_application='".$sendingApplication."',
								export_type='".$export_type."'
								";
		$ptPrintedRecordsRes = imw_query($ptPrintedRecordsQry) or die(imw_error());						
	}

	public function checkEmail($email) {
		if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
			return true;
		} else {
			return false;
		}
	}
	
	//Provides download file details
	public function download_ccd($request){
		$rqfileName = "";
		$rqfileName = base64_decode($request['filename']);
		$download_name = "";
		if(empty($rqfileName) === false && isset($request['filename']) && trim($rqfileName) != "liaka_ccd.xml"){
			$filename = $rqfileName;
			if(isset($request['ccdPatName']) && $request['ccdPatName'] != "" && $request['fileType'] == "xml"){		
				$download_name = $request['ccdPatName']."_CCD.xml";
			}
			elseif(isset($request['ccdPatName']) && $request['ccdPatName'] != "" && $request['fileType'] == "txt"){		
				$download_name = $request['ccdPatName']."_SHA2.txt";
			}
			else{
				$download_name = explode('/',$rqfileName);
				$download_name = array_pop($download_name);
			}
		}
		else{
			$filename = $this->data_file_path."xml/liaka_ccd.xml";
			$download_name = "liaka_ccd.xml";
		}
		
		$return_arr['filename'] = $filename;
		$return_arr['download_name'] = $download_name;
		return $return_arr;
	}
}
?>