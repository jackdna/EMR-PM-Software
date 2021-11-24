<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Filename: medications.class.php
 Coded in PHP7
 Purpose: Class File Related to Medications
 Access Type: Indirect Access.
 
*/

include_once 'medical_history.class.php';
$OBJReviewMedHx = new CLSReviewMedHx;
class Medications extends MedicalHistory
{
	public $vocabulary = false;
	public $data = false;
	public $pat_relation = false;			//arrPtRel
	public $default_columns =  false;
	public $columns =  false;
	
	public function __construct($tab = 'ocular')
	{
		parent::__construct($tab);
		$this->vocabulary = $this->get_vocabulary("medical_hx", "medications");
		
		$this->default_columns = array('Begin-Date-Time'=>0, 'End-Date-Time'=>0, 'Last-Taken-Date'=>0, 'Ordered-By'=>0, 'Refusal'=>0, 'Hx'=>0);
		
		$this->columns = getColumnsList('medication');
		if( !$this->columns ) {
			foreach( $this->default_columns as $key => $cStatus) {
				if( $cStatus ) $this->columns[] = $key;
			}
		}
	}
	
	public function load_medications(&$request)
	{
		$return = array();
		
		extract($request);
		$arrRevHis = array(); $arrUserId = array(); $phyUsers = array();
		$qryUser = "SELECT id, fname, mname, lname, user_type FROM users where fname!='' and lname!='' AND delete_status = 0 ORDER BY fname,lname ";
		$resUser = imw_query($qryUser);
		if($resUser)
		{
			while($arrUser = imw_fetch_array($resUser))
			{
				$arrUserId[$arrUser["id"]]["fname"] = $arrUser["fname"];
				$arrUserId[$arrUser["id"]]["lname"] = $arrUser["lname"];
				if($arrUser["mname"] !='' )
					$phyUsers[$arrUser["id"]]['short']=strtoupper(substr($arrUser["fname"],0,1). substr($arrUser["mname"],0,1). substr($arrUser["lname"],0,1));
				else
					$phyUsers[$arrUser["id"]]['short'] = strtoupper(substr($arrUser["fname"],0,1). substr($arrUser["lname"],0,1));
					
				$medium = trim($arrUser["lname"]).', '. trim($arrUser["fname"]);
				$medium .= ($arrUser["mname"]!='')?' '.substr($arrUser["mname"],0,1):'';
				$phyUsers[$arrUser["id"]]['medium'] = $medium;
			}
		}
		
		/*
		$medicine_typeahead = $this->medicine_typeahead();
		extract($medicine_typeahead);
		$return['medicationTitleArr'] = $medicationTitleArr;
		$return['medication_ccdacode_Arr'] = $medication_ccdacode_Arr;
		$return['medication_doses_Arr'] = $medication_doses_Arr;
		$return['medication_sig_Arr'] = $medication_sig_Arr;
		$return['arrMedicines'] = $arrMedicines;
		$return['fdb_id_arr'] = $fdb_id_arr; */
		//---- End Get the Medication for type ahead ------
		
		$query_where = '';	$selSearchBy='';
		$sortBy = '';
		if(count($searchby) > 0)
		{
			$arrSearchBy = $searchby; 
			foreach($searchby as $val)
			{
				$selSearchBy.= "'".$val."',";
			}
			$selSearchBy = substr($selSearchBy, 0, strlen($selSearchBy)-1);
			$query_where = " and allergy_status IN($selSearchBy)";
			$sortBy = ' ORDER BY allergy_status';
		}
		else
		{
			$query_where = " and allergy_status IN('Active','Administered','Order')";
			$sortBy  = ' ORDER BY id';
		}

		if(count($arrSearchBy)<=0){
			$arrSearchBy['Order']='Order';
		}

		// GET COMPLIANT AND COMMENTS FROM TABLE
		$comments = "";
		$qryComp ="Select comments FROM commonNoMedicalHistory WHERE  patient_id='".$this->patient_id."' and module_name='Medication' LIMIT 1";
		$rsComp= imw_query($qryComp) or die(imw_error());
		$rowComp = imw_fetch_row($rsComp);
		$return['comments'] = $rowComp[0];
		
		$routesArr = get_array_records_query("Select route_name From route_codes Order By route_name");
		$pkIdAuditTrail = "";
		//$arrAlert=array();
		
		//GET Meds from chart notes
		if(!empty($_REQUEST["prv_frmid"])){		
			$tempI = 0;		
			$qryMed = "select lists from chart_genhealth_archive WHERE patient_id='".$this->patient_id."'  AND form_id='".$_REQUEST["prv_frmid"]."' ";
			$row = sqlQuery($qryMed);
			if($row!=false){				
				$arrLists = unserialize($row["lists"]);
				$medicalResArr = array();
				$pkIdAuditTrail = '';
				$disableNoMed = false;				
				$medQryRes = $arrLists[4];
				$len=count($medQryRes);$arrFields=array();
				$loop_count = $len >= 5 ? $len+1 : 5 ;
				for($m=0;$m<$len;$m++){
					if($medQryRes[$m]['allergy_status']!='Active' && $medQryRes[$m]['allergy_status']!='Order'){continue;}
					$rowQryMed = array();
					$rowQryMed = $medQryRes[$m];
					if(count($rowQryMed)>0){
					//--
					$dataArr = array();
					$dataArr['MED_ID'] = $rowQryMed['id'];	
					$type = $rowQryMed['type'];
					
					$dataArr['MED_TW_DDI'] = ucfirst($rowQryMed['as_ddi']);
					$dataArr['MED_TW_ID'] = ucfirst($rowQryMed['as_id']);

					$dataArr['MED_TITLE'] = ucfirst($rowQryMed['title']);
					$dataArr['MED_DEST'] = $rowQryMed['destination'];
					$dataArr['MED_SIG'] = $rowQryMed['sig'];
					$dataArr['MED_QTY'] = $rowQryMed['qty'];
					$dataArr['MED_REFILLS'] = $rowQryMed['refills'];
					//$dataArr['MED_PREFERED_BY'] = $rowQryMed['referredby'];
					if(preg_match("/([0-9]{2})-([0-9]{2})-([0-9]{4})/",$rowQryMed['begdate'])){
						$dataArr['MED_BEG_DATE'] = $rowQryMed['begdate'];
					}
					else $dataArr['MED_BEG_DATE'] = get_date_format($rowQryMed['begdate']);
					$dataArr['MED_BEG_TIME'] = $rowQryMed['begtime'];
			    $dataArr['MED_END_TIME'] = $rowQryMed['endtime'];
					$dataArr['MED_END_DATE'] = get_date_format($rowQryMed['enddate']);
					$dataArr['MED_LASTTAKEN_DATE'] = (!empty($rowQryMed['last_take_time']) && strpos($rowQryMed['last_take_time'],"0000")===false) ? format_date($rowQryMed['last_take_time'],0,1,'show') : "" ; 
					$dataArr['MED_COMMENTS'] = $rowQryMed['med_comments'];
					$dataArr['MED_COMPLIANT'] = $rowQryMed['compliant'];
					$dataArr['MED_SITE'] = $rowQryMed['sites'];
					$dataArr['ccda_code'] = $rowQryMed['ccda_code'];
					$dataArr['medInfoButtonCode'] = trim($rowQryMed['ccda_code']);
					$medInfoButtonCode = trim($rowQryMed['ccda_code']);
					$dataArr['fdb_id'] = $rowQryMed['fdb_id'];
					$dataArr['REFUSAL'] = $rowQryMed['refusal'];
					$dataArr['REFUSAL_REASON'] = $rowQryMed['refusal_reason'];
					$dataArr['REFUSAL_SNOMED'] = $rowQryMed['refusal_snomed'];
					$dataArr['service_eligibility'] = $rowQryMed['service_eligibility'];
					if(constant("UMLS_DB") && !$medInfoButtonCode)
					{
						$qryMedInfoButton = "select RXCUI,STR from ".constant("UMLS_DB").".rxnconso where STR = '".$rowQryMed['title']."' and SAB='RXNORM' ORDER BY RXCUI DESC LIMIT 0,1";
						$rsQryMedInfoButton = imw_query($qryMedInfoButton);
						if(imw_num_rows($rsQryMedInfoButton)>0) {
							while($rowQryMedInfoButton = imw_fetch_array($rsQryMedInfoButton)){	
								$dataArr['medInfoButtonCode'] = trim($rowQryMedInfoButton['RXCUI']);
							}
						}
					}
					
					$medication_status = $rowQryMed['allergy_status'];
					if(strtoupper($medication_status) == 'ACTIVE' || strtoupper($medication_status) == 'RENEW' || strtoupper($medication_status) == 'ORDER'){
						$dataArr['STYLE'] = "color:#390;";	// GREEN TEXT COLOR
						$disableNoMed =true;		
					}
					else{
						$dataArr['STYLE'] = "color:#F00;";	// RED TEXT COLOR
					}
					$curDate = date('Y-m-d',strtotime($rowQryMed['date']));
					if(strtotime($curDate) == strtotime(date('Y-m-d'))){
						$dataArr['STYLE'] = "color:#36F;";		// BLUE TEXT COLOR
					}
					
					if($rowQryMed['compliant'] == '0' || (strtoupper($medication_status) != 'ACTIVE' && strtoupper($medication_status) != 'RENEW' && strtoupper($medication_status) != 'ORDER'))
					{
						$dataArr['STYLE'] = "color:#F00;";	// RED TEXT COLOR
					}
				
					if($medication_status == 'Deleted'){
						$dataArr['DEL_STATUS'] = true;
					}
					
					//--- SET PHYSICIAN DROP DOWN------
					$phy_drop_down = "<option value=''></option>";
					foreach($phyUsers as $phyID => $phyNameArr){
							$sel = $rowQryMed['referredby'] == $phyID ? "selected='selected'" : '';
							$phyShortName = $phyNameArr['short'];
							$phyMedName =  $phyNameArr['medium'];
							$phy_drop_down .= '<option value="'.$phyID.'" '.$sel.' title="'.$phyMedName.'">'.$phyShortName.'</option>';
					}
					$dataArr['MED_PREFERED_BY'] = $phy_drop_down;
					
					// Set Route Drop Down
					$route_drop_down = "<option value=''></option>";
					foreach($routesArr as $route){
							$sel = ($rowQryMed['med_route'] == $route['route_name']) ? "selected" : '';
							$route_drop_down .= '<option value="'.$route['route_name'].'" '.$sel.'>'.ucwords(strtolower($route['route_name'])).'</option>';
					}
					$dataArr['MED_ROUTE'] = $route_drop_down;
					$dataArr['MED_ROUTE_VAL'] = $rowQryMed['med_route'];
					
					//--- SET MEDICATION STATUS ----
					$medStatus = array('Active','Stop','Renew','Discontinue','Administered','Order');
					$staus_drop_down = '';
					foreach($medStatus as $strMedVal)
					{
						$status_name = "";
						$sel = $medication_status == $strMedVal ? 'selected="selected"' : '';
						$status_name = $strMedVal;
						$staus_drop_down .= "<option value='$status_name' $sel>$status_name</option>";
					}
					if($medication_status == 'Discontinue'){
						$dataArr['DIS_STATUS'] = true;
					}
					$dataArr['STATUS'] = $staus_drop_down;
					$dataArr['SITE'] = $site_drop_down;
					
					if($type == 4)	$medicalResArr['OCU'][] = $dataArr;
					else if($type == 1)	$medicalResArr['SYS'][] = $dataArr;
					
					
					if(empty($dataArr['MED_TITLE']) == false)
					{
						$pkIdAuditTrail .= $rowQryMed["id"]."-";
						if(empty($pkIdAuditTrailID) == true){		
							$pkIdAuditTrailID = $rowQryMed["id"];
						}
					}
					$tempI++;
					
					//--
					}
				
				}
			
			}
			
		}else{		
			$qryMed = "select * from lists where pid='".$this->patient_id."' $query_where AND type IN(1,4) $sortBy";
			$rsQryMed = imw_query($qryMed);
			$loop_count = imw_num_rows($rsQryMed) >= 5 ? imw_num_rows($rsQryMed)+1 : 5 ;
		
		
			$medicalResArr = array();
			$pkIdAuditTrail = '';
			$disableNoMed = false;
			$return['no_medication'] = commonNoMedicalHistoryAddEdit($moduleName="Medication",$moduleValue="",$mod="get");
			$tempI = 0;
			while($rowQryMed = imw_fetch_array($rsQryMed))
			{	
				$dataArr = array();
				$dataArr['MED_ID'] = $rowQryMed['id'];	
				$type = $rowQryMed['type'];
				
				$dataArr['MED_TW_DDI'] = ucfirst($rowQryMed['as_ddi']);
				$dataArr['MED_TW_ID'] = ucfirst($rowQryMed['as_id']);

				$dataArr['MED_TITLE'] = ucfirst($rowQryMed['title']);
				$dataArr['MED_DEST'] = $rowQryMed['destination'];
				$dataArr['MED_SIG'] = $rowQryMed['sig'];
				$dataArr['MED_QTY'] = $rowQryMed['qty'];
				$dataArr['MED_REFILLS'] = $rowQryMed['refills'];
				//$dataArr['MED_PREFERED_BY'] = $rowQryMed['referredby'];
				if(preg_match("/([0-9]{2})-([0-9]{2})-([0-9]{4})/",$rowQryMed['begdate'])){
					$dataArr['MED_BEG_DATE'] = $rowQryMed['begdate'];
				}
				else $dataArr['MED_BEG_DATE'] = get_date_format($rowQryMed['begdate']);
				$dataArr['MED_BEG_TIME'] = $rowQryMed['begtime'];
		    $dataArr['MED_END_TIME'] = $rowQryMed['endtime'];
				$dataArr['MED_END_DATE'] = get_date_format($rowQryMed['enddate']);
				$dataArr['MED_LASTTAKEN_DATE'] = (!empty($rowQryMed['last_take_time']) && strpos($rowQryMed['last_take_time'],"0000")===false) ? format_date($rowQryMed['last_take_time'],0,1,'show') : "" ; 
				$dataArr['MED_COMMENTS'] = $rowQryMed['med_comments'];
				$dataArr['MED_COMPLIANT'] = $rowQryMed['compliant'];
				$dataArr['MED_SITE'] = $rowQryMed['sites'];
				$dataArr['ccda_code'] = $rowQryMed['ccda_code'];
				$dataArr['medInfoButtonCode'] = trim($rowQryMed['ccda_code']);
				$medInfoButtonCode = trim($rowQryMed['ccda_code']);
				$dataArr['fdb_id'] = $rowQryMed['fdb_id'];
				$dataArr['REFUSAL'] = $rowQryMed['refusal'];
				$dataArr['REFUSAL_REASON'] = $rowQryMed['refusal_reason'];
				$dataArr['REFUSAL_SNOMED'] = $rowQryMed['refusal_snomed'];
				$dataArr['service_eligibility'] = $rowQryMed['service_eligibility'];
				if(constant("UMLS_DB") && !$medInfoButtonCode)
				{
					$qryMedInfoButton = "select RXCUI,STR from ".constant("UMLS_DB").".rxnconso where STR = '".$rowQryMed['title']."' and SAB='RXNORM' ORDER BY RXCUI DESC LIMIT 0,1";
					$rsQryMedInfoButton = imw_query($qryMedInfoButton);
					if(imw_num_rows($rsQryMedInfoButton)>0) {
						while($rowQryMedInfoButton = imw_fetch_array($rsQryMedInfoButton)){	
							$dataArr['medInfoButtonCode'] = trim($rowQryMedInfoButton['RXCUI']);
						}
					}
				}
				
				$medication_status = $rowQryMed['allergy_status'];
				if(strtoupper($medication_status) == 'ACTIVE' || strtoupper($medication_status) == 'RENEW' || strtoupper($medication_status) == 'ORDER'){
					$dataArr['STYLE'] = "color:#390;";	// GREEN TEXT COLOR
					$disableNoMed =true;		
				}
				else{
					$dataArr['STYLE'] = "color:#F00;";	// RED TEXT COLOR
				}
				$curDate = date('Y-m-d',strtotime($rowQryMed['date']));
				if(strtotime($curDate) == strtotime(date('Y-m-d'))){
					$dataArr['STYLE'] = "color:#36F;";		// BLUE TEXT COLOR
				}
				
				if($rowQryMed['compliant'] == '0' || (strtoupper($medication_status) != 'ACTIVE' && strtoupper($medication_status) != 'RENEW' && strtoupper($medication_status) != 'ORDER'))
				{
					$dataArr['STYLE'] = "color:#F00;";	// RED TEXT COLOR
				}
			
				if($medication_status == 'Deleted'){
					$dataArr['DEL_STATUS'] = true;
				}
				
				//--- SET PHYSICIAN DROP DOWN------
				$phy_drop_down = "<option value=''></option>";
				foreach($phyUsers as $phyID => $phyNameArr){
						$sel = $rowQryMed['referredby'] == $phyID ? "selected='selected'" : '';
						$phyShortName = $phyNameArr['short'];
						$phyMedName =  $phyNameArr['medium'];
						$phy_drop_down .= '<option value="'.$phyID.'" '.$sel.' title="'.$phyMedName.'">'.$phyShortName.'</option>';
				}
				$dataArr['MED_PREFERED_BY'] = $phy_drop_down;
				
				// Set Route Drop Down
				$route_drop_down = "<option value=''></option>";
				foreach($routesArr as $route){
						$sel = ($rowQryMed['med_route'] == $route['route_name']) ? "selected" : '';
						$route_drop_down .= '<option value="'.$route['route_name'].'" '.$sel.'>'.ucwords(strtolower($route['route_name'])).'</option>';
				}
				$dataArr['MED_ROUTE'] = $route_drop_down;
				$dataArr['MED_ROUTE_VAL'] = $rowQryMed['med_route'];
				
				//--- SET MEDICATION STATUS ----
				$medStatus = array('Active','Stop','Renew','Discontinue','Administered','Order');
				$staus_drop_down = '';
				foreach($medStatus as $strMedVal)
				{
					$status_name = "";
					$sel = $medication_status == $strMedVal ? 'selected="selected"' : '';
					$status_name = $strMedVal;
					$staus_drop_down .= "<option value='$status_name' $sel>$status_name</option>";
				}
				if($medication_status == 'Discontinue'){
					$dataArr['DIS_STATUS'] = true;
				}
				$dataArr['STATUS'] = $staus_drop_down;
				$dataArr['SITE'] = $site_drop_down;
				
				if($type == 4)	$medicalResArr['OCU'][] = $dataArr;
				else if($type == 1)	$medicalResArr['SYS'][] = $dataArr;
				
				
				if(empty($dataArr['MED_TITLE']) == false)
				{
					$pkIdAuditTrail .= $rowQryMed["id"]."-";
					if(empty($pkIdAuditTrailID) == true){		
						$pkIdAuditTrailID = $rowQryMed["id"];
					}
				}
				$tempI++;
			}
		
		}//
		
		for($i = $tempI; $i < $loop_count; $i++)
		{
			$dataArr = array();
			$dataArr['MED_ID'] = "";
			$type = "";	
			$dataArr['MED_TITLE'] = "";
			$dataArr['MED_DEST'] = "";
			$dataArr['MED_SIG'] = "";
			$dataArr['MED_QTY'] = "";
			$dataArr['MED_REFILLS'] = "";
			//$dataArr['MED_PREFERED_BY'] = "";
			$dataArr['MED_BEG_DATE'] = "";
			$dataArr['MED_END_DATE'] = "";
			$dataArr['MED_LASTTAKEN_DATE'] = "";
			$dataArr['MED_COMMENTS'] = "";
			$dataArr['MED_COMPLIANT'] = "";
			$dataArr['MED_SITE'] = "";
			$dataArr['ccda_code'] = "";
			$dataArr['medInfoButtonCode'] = "";
			$dataArr['fdb_id'] = "";
			$dataArr['service_eligibility'] = 0;
			$medication_status = "";
			
			//--- SET PHYSICIAN DROP DOWN------
			$phy_drop_down = "<option value=''></option>";
			foreach($phyUsers as $phyID=>$phyNameArr){
				//$sel = $rowQryMed['referredby'] == $phyID ? "selected='selected'" : '';
					$phyShortName = $phyNameArr['short'];
					$phyMedName =  $phyNameArr['medium'];
					$phy_drop_down .= "<option value='$phyID'  title='$phyMedName'>$phyShortName</option>";
			}
			$dataArr['MED_PREFERED_BY'] = $phy_drop_down;
			
			// Set Route Drop Don
			$route_drop_down = "<option value=''></option>";
			foreach($routesArr as $route){
				$route_drop_down .= '<option value="'.$route['route_name'].'">'.ucwords(strtolower($route['route_name'])).'</option>';
			}
			$dataArr['MED_ROUTE'] = $route_drop_down;
				
			//--- SET MEDICATION STATUS ----
			$medStatus = array('Active','Order','Stop','Renew','Discontinue','Administered');
			$staus_drop_down = '';
			for($s=0;$s<count($medStatus);$s++){
				$sel = $medication_status == $medStatus[$s] ? 'selected="selected"' : '';
				$status_name = $medStatus[$s];
				$staus_drop_down .= "<option value='$status_name' $sel>$status_name</option>";
			}
			$dataArr['STATUS'] = $staus_drop_down;
			$dataArr['SITE'] = $site_drop_down;
			$cnt1 = (count($medicalResArr['OCU'])<5)? 5 :count($medicalResArr['OCU']);
			for($j =count($medicalResArr['OCU']);$j<=$cnt1;$j++){
				if(count($medicalResArr['OCU'])<=$i)
				$medicalResArr['OCU'][] = $dataArr;
			}
			$cnt2 = (count($medicalResArr['SYS'])<5)? 5 :count($medicalResArr['SYS']);
			for($k =count($medicalResArr['SYS']);$k<=$cnt2;$k++){
				if(count($medicalResArr['SYS'])<=$i)
				$medicalResArr['SYS'][] = $dataArr;
			}
			
		}
		
		//If edit Prev Chart Meds
		if(!empty($_REQUEST["prv_frmid"])){$medicalResArr['SYS'] = null; unset($medicalResArr['SYS']);}
		
		ksort($medicalResArr,SORT_ASC);
		
		$return['medical_data'] = $medicalResArr;
		
		$return['checkMedications'] = commonNoMedicalHistoryAddEdit($moduleName="Medication",$moduleValue="",$mod="get");
		
		//--- SET FILTER VALUES ---
		$searchbyArr = array('Select All','Active','Stop','Renew','Discontinue','Administered','Deleted','Order');
		$filter_data_arr = array(); $arrSearchSel = array();
		$searchOptions = []; $sel = '';
		
		$arrSelected = $_REQUEST['searchby'];
		if(count($selSearcBy) <= 0) {$sel='Order';}
		
		foreach($searchbyArr as $intKeySA => $strValSA){
			if($intKeySA == 0){
				$strValSA = '';
			}
			$sel='';
			if(in_array($strValSA, $arrSelected)){$arrSearchSel[$strValSA] = $strValSA;}
			$searchOptions[$strValSA]=$strValSA;
		}

		if(count($arrSearchSel) == 0)
		{
			$arrSearchSel['Active']='Active';$arrSearchSel['Administered']='Administered';$arrSearchSel['Order']='Order';
		}
		
		$return['searchOptions'] = $searchOptions;
		$return['disableNoMed'] = $disableNoMed;
		$return['arrSearchSel'] = $arrSearchSel;
		$return['search_by_val'] = $filter_data_arr;
		$return['search_by_select'] = $searchby;
		
		//--- SET MEDICATION STAUS DROP DOWN ---
		$medStatus = array('Order'=>'Order','Active'=>'Active','Stop'=>'Stop','Renew'=>'Renew','Discontinue'=>'Discontinue','Administered'=>'Administered');
		$return['medical_options'] = $medStatus;

		//----- GET PATIENT ERX STATUS -------
		$eRxStatusRes = get_erx_status($this->patient_id);
		$return['erx_patient_id'] = $eRxStatusRes[0]['erx_patient_id'];
	
		//--- ERX ALLOW FOR APPLICATION ---
		$query = "select Allow_erx_medicare from copay_policies LIMIT 1";
		$sql = imw_query($query);
		$copay_policies_res = imw_fetch_assoc($sql);
		$return['Allow_erx_medicare'] = $copay_policies_res['Allow_erx_medicare'];
		
		return $return;
	}
	
	public function get_operator($lastExamId)
	{
		$query = "select CONCAT_WS(', ',lname,fname) as opName ,operator_id 
										from patient_last_examined ple left join users u on u.id = ple.operator_id 
										where ple.patient_last_examined_id='".$lastExamId."' limit 1";
		$sql = imw_query($query);
		$row = imw_fetch_assoc($sql);
		
		return $opName = $row['opName'];
	}
	
	//--- DELETE MEDICATION RECORDS ---
	public function del_medication($mode,$del_id,$callFrom = '', $subcallFrom = '')
	{
		if(trim($mode) == 'delete' and trim($del_id) > 0)
		{
			if(!empty($_REQUEST['prv_frmid'])){
				$qryMed = "select lists from chart_genhealth_archive WHERE patient_id='".$_SESSION['patient']."'  AND form_id='".$_REQUEST["prv_frmid"]."' ";
				$row = sqlQuery($qryMed);
				if($row!=false){
					$arrLists = unserialize($row["lists"]);
					$medQryRes = $arrLists[4];
					
					if(count($medQryRes)>0){
						foreach($medQryRes as $j => $arMQ){
							if($arMQ["id"] == $del_id){
								$medQryRes[$j]["allergy_status"] = "Deleted";
							}
						}
					}
					
					$arrLists[4] = $medQryRes;
					$lists = sqlEscStr(serialize($arrLists));
					
					//Save
					$sql="UPDATE chart_genhealth_archive SET lists='".$lists."' WHERE patient_id='".$_SESSION['patient']."' AND form_id='".$_REQUEST["prv_frmid"]."' ";
					$row=sqlQuery($sql);
					
					if($callFrom == "WV")
					{						
						echo '<script>
										//update PMH in WV ---
										var ofmain = window.opener.top.fmain;										
										if(ofmain && typeof(ofmain.getMedHx) != "undefined"){ ofmain.getMedHx();}
									</script>';
					}
				}
				
			} else if(isDssEnable() && $callFrom=='') { // Cancel Medication Order to DSS
				$dssResponse = '';
				$dssResponse = $this->dssCancelMedicationOrder($del_id);
				if($dssResponse === 'SUCCESS') {
					$this->medDeletionProcess($del_id);
				} else {
					echo '<script>top.fAlert("'.$dssResponse.'")</script>';
				}
			} else {
				$this->medDeletionProcess($del_id);
			}
		}
	}

	public function medDeletionProcess($del_id)
	{
		global $OBJReviewMedHx;
		
		$query = "update lists set allergy_status = 'Deleted' where id = '".$del_id."'";
		$sql = imw_query($query);

		//ERP API CALL
		$erp_error=array();
		if($sql && isERPPortalEnabled()) {
			try {
				include_once($GLOBALS['srcdir']."/erp_portal/patient_medications.php");
				$obj_patients = new patient_medications();
				$obj_patients->deleteMedicationRecords($del_id);
				$obj_patients->deleteMedication($del_id);
			} catch(Exception $e) {
				$erp_error[]='Unable to connect to ERP Portal';
			}
		}	

		// --- REVIEWED CODE --
		$queryAudit = "select title from lists where id = '".$del_id."'";
		$rowAudit = get_row_record_query($queryAudit);
		
		$del_med_name = $rowAudit['title'];
		$medication_review_arr = array();
		$medication_review_arr[0]["Pk_Id"] = $del_id;		
		$medication_review_arr[0]["Table_Name"] = "lists";
		$medication_review_arr[0]["Field_Text"] = "Patient Medication";
		$medication_review_arr[0]["Operater_Id"] = $_SESSION['authId'];
		$medication_review_arr[0]["Action"] = 'delete';
		$medication_review_arr[0]["Old_Value"] = $del_med_name;
	
		$OBJReviewMedHx->reviewMedHx($medication_review_arr,$_SESSION['authId'],"Medications",$_SESSION['patient'],0,0);
		
		// Remove Review Code Here
		
		if($callFrom == "WV" && $subcallFrom == "")
		{
			echo '<script>
							//update PMH in WV ---
							var ofmain = window.opener.top.fmain;
							if(ofmain && typeof(ofmain.showMedList) != "undefined"){ ofmain.showMedList("PMH",1);}
							if(ofmain && typeof(ofmain.getMedHx) != "undefined"){ ofmain.getMedHx();}
						</script>';
		}

		if($subcallFrom == "grid")
		{
			echo '<script>
							var ofmain = window.opener.top.fmain;
							if(ofmain && typeof(ofmain.getMedHx) != "undefined"){ ofmain.getMedHx();}
						</script>';
		}
	}

	/**
	 * Cancel Medication Order to DSS
	 */
	public function dssCancelMedicationOrder($del_id)
	{
		$return = '';
		// get medication order ien
		$sqlm = imw_query("SELECT dss_allergy_id FROM lists WHERE id = '".$del_id."'");
		if($sqlm && imw_num_rows($sqlm) > 0){
			$row = imw_fetch_assoc($sqlm);
		}
		if(!empty($row['dss_allergy_id']) && $row['dss_allergy_id'] != "")
		{
			$eSign = isset($_REQUEST['vcode']) ? base64_decode($_REQUEST['vcode']) : '';
			$duz = isset($_SESSION['dss_loginDUZ']) ? $_SESSION['dss_loginDUZ'] : '';
			$loc = isset($_SESSION['dss_location']) ? $_SESSION['dss_location'] : '';

			if(!empty($eSign) && $eSign != "" && !empty($loc) && $loc != "")
			{
				if(!empty($duz) && $duz != "") {
					
					$params = array(
                        'orderIEN' => $row['dss_allergy_id'],
                        'providerIEN' => $duz,
                        'locationIEN' => $loc,
                        'electronicSignature' => $eSign,
                        'reasonForCanceling' => '14'
                    );
                    
                    include_once( $GLOBALS['srcdir'].'/dss_api/dss_medical_hx.php' );
                    $objDss = new Dss_medical_hx();
                    $data = $objDss->cancelMedicationOrder($params);
                    if($data[0]['success'] == '-1') {
                        $return = 'Error: '.$data[0]['message'];
                    } else {
                        $return = 'SUCCESS';
                    }
					
				} else {
					$return = 'Error: Provider is not found';
				}
			} else {
				$return = 'Error: Electronic signature and facility is required';
			}
		} else {
			$return = 'Error: medication order ien not found';
		}
		return $return;
	}

	/**
	 * Get Patient Medication from DSS
	 */
	public function getPatientMedList(){
		$patient_id = $_SESSION['patient'];
		$sqlDFN = "SELECT External_MRN_5 FROM `patient_data` WHERE `id` = ".$this->patient_id;
		$resultDFN = imw_query($sqlDFN);
		if( imw_num_rows($resultDFN) > 0 ) {
			$data = imw_fetch_assoc($resultDFN);
			$patientDFN = $data['External_MRN_5'];
		}

		try
		{
			if( !empty($patientDFN) && $patientDFN != '' ):
				include_once( $GLOBALS['srcdir'].'/dss_api/dss_medical_hx.php' );
				$objDss = new Dss_medical_hx();
				
				// $dt = $objDss->MISC_DSICDateConvert(date('Y-m-d'));
				// $medData = $objDss->getPatientMedList($patientDFN, "2700101", $dt['fileman']);

				$dt = $objDss->convertToFileman(date('Y-m-d'));
				$medData = $objDss->getPatientMedList($patientDFN, "2700101", $dt);

                if($medData) {
                    // Check patient medications.
					$sql_pt_allergy = "SELECT id,title,dss_allergy_id FROM lists 
						WHERE pid = ".$this->patient_id." 
							AND type = 1 OR type = 4 
						";
					$sql_pt_med_result = imw_query($sql_pt_allergy);
                    $dssPatientMed=array();
                    $dssMedArr=array();
                    $dssPatientMedArr=array();
					if($sql_pt_med_result && imw_num_rows($sql_pt_med_result) > 0){
                        while($row = imw_fetch_assoc($sql_pt_med_result)) {
                            $dssPatientMed[$row['dss_allergy_id']]=$row;
                            $dssPatientMedArr[$row['dss_allergy_id']]=$row;
                        }
					}

                    foreach ($medData as $key => $arr) {
                        $orderIfn=$arr['orderIfn'];
                        // Check existing medicine_data
                        $sqlMed = 'SELECT id, medicine_name, ocular FROM medicine_data WHERE medicine_name = "'.trim($arr['drugNameDose']).'" ';
                        $resultMed = imw_query($sqlMed);
                        $isOcular = 0;
                        if( imw_num_rows($resultMed) > 0 ) {
                            $dataArr = imw_fetch_assoc($resultMed);
                            if($dataArr['ocular'] == 0) $isOcular = 0;
                            if($dataArr['ocular'] == 1) $isOcular = 1;
                        }

                        // Insert patient medicin data
                        $tmpArr = array();
                        $tmpArr['date'] = date('Y-m-d H:i:s');
                        $tmpArr['type'] = ($isOcular == 0) ? 1 : 4;
                        $tmpArr['title'] = imw_real_escape_string(trim($arr['drugNameDose']));

                        // Check if comment contains (<,>).
                        $comment = trim($arr['field_8']);
                        $posS = stripos($comment, '<');
                        $posL = strripos($comment, '>');
                        if ($posS !== false) $comment = ltrim($comment, '<');
                        if ($posL !== false) $comment = rtrim($comment, '>');

                        $tmpArr['med_comments'] = $comment;
                        $tmpArr['pid'] = $this->patient_id;
                        $tmpArr['user'] = $_SESSION['authId'];

                        $tmpArr['begdate'] = $this->formatDate($arr['startDate'], 'date');
                        $tmpArr['begtime'] = $this->formatDate($arr['startDate'], 'time');
                        $tmpArr['enddate'] = $this->formatDate($arr['stopDate'], 'date');
                        $tmpArr['endtime'] = $this->formatDate($arr['stopDate'], 'time');

                        $tmpArr['timestamp'] = date('Y-m-d H:i:s');
                        $tmpArr['dss_allergy_id'] = $arr['orderIfn'];

                        $status = ''; 
                        if(strpos( $arr['status'], ';' ) !== false) {
                            $status = explode(';',$arr['status']);
                            $status = trim($status[1]);
                        }

                        if(  !empty($status) && $status != "") {
                            if($status == 'ACTIVE' || $status == 'PARTIAL RESULTS' || $status == 'NO STATUS') {
                                $tmpArr['allergy_status'] = 'Active';
                            }
                            if($status == 'DISCONTINUED' || $status == 'COMPLETE' || $status == 'UNRELEASED' || $status == 'DISCONTINUED/EDIT') {
                                $tmpArr['allergy_status'] = 'Discontinue';
                            }
                            if($status == 'HOLD' || $status == 'FLAGGED' || $status == 'DELAYED') {
                                $tmpArr['allergy_status'] = 'Stop';
                            }
                            if($status == 'PENDING' || $status == 'SCHEDULED') {
                                $tmpArr['allergy_status'] = 'Order';
                            }
                            if($status == 'EXPIRED' || $status == 'CANCELLED') {
                                $tmpArr['allergy_status'] = 'Deleted';
                            }
                            if($status == 'LAPSED' || $status == 'RENEWED') {
                                $tmpArr['allergy_status'] = 'Renew';
                            }
                        } else {
                            $tmpArr['allergy_status'] = 'Discontinue';
                        }

                        $update=false;
                        $updateid=$dssPatientMed[$orderIfn]['id'];
                        if( (strtolower($tmpArr['title'])==strtolower($dssPatientMed[0]['title'])) || (strtolower($tmpArr['title'])==strtolower(imw_real_escape_string(trim($dssPatientMed[$orderIfn]['title']))) ) || ($orderIfn==$dssPatientMed[$orderIfn]['dss_allergy_id']) ){
                            $update=true;
                            if($dssPatientMed[0]['id'] && $updateid==''){
                                $updateid=$dssPatientMed[0]['id'];
                            }
                        }

                        if($update) {
                            $arr = array(
                                'title' => imw_real_escape_string($tmpArr['title']),
                                'dss_allergy_id' => $orderIfn,
                                'allergy_status' => imw_real_escape_string($tmpArr['allergy_status'])
                            );
                            UpdateRecords($updateid, 'id', $arr, 'lists');
                        }elseif( count($dssPatientMed[$orderIfn]) === 0 ){
                            $id = AddRecords($tmpArr,'lists');
                        }

                        if(isset($dssPatientMedArr[$orderIfn])){
                            unset($dssPatientMedArr[$orderIfn]);
                        }else{
                            if(isset($dssPatientMedArr[$orderIfn]['id']))
                                $dssMedArr[]=$dssPatientMedArr[$orderIfn]['id'];
                        }
                    }
                    
                    //Delete existing Medications from imwemr which are not in dss response.
                    if(empty($dssMedArr)==false) {
                        foreach($dssMedArr as $del_id) {
                            $this->del_medication('delete',$del_id,'dssload');
                        }
                    }
                    
                }
			endif;
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function formatDate($dateString, $type = 'date') {
		if(!empty($dateString)) {
			if( strpos( $dateString, '@' ) !== false ) {
				$date = date('Y-m-d',strtotime(str_replace('@',' ',$dateString)));
				$time = date('H:i:s',strtotime(str_replace('@',' ',$dateString)));
			} else {
				$date = date('Y-m-d',strtotime($dateString));
				$time = '';
			}
		}
		if($type == 'date') return $date;
		if($type == 'time') return $time;
	}
	
}

?>