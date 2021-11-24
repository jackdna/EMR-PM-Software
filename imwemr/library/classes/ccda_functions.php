<?php   
//  FUNCTIONS
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
		
		function gender_srh($val,$map="code_to_imw"){
			$val = trim($val);
			$arrGender = array(
								array("imw"=>'male',"code"=>"M","display_name"=> "Male"),
								array("imw"=>'female',"code"=>"F","display_name"=> "Female"),
								array("imw"=>'unknown',"code"=>"UNK","display_name"=> "Unknown")
							  );
			$arr = array();
			if($val != ""){
				foreach($arrGender as $row){
					if(in_array($val, $row)){
						if($map == "code_to_imw"){
							$arr['code'] = $row['code'];
							$arr['display_name'] = $row['display_name'];
						}else{
							$arr['imw'] = $row['imw'];
						}
						break;
					}else{
						if($map == "code_to_imw"){
						$arr['code'] = "UN";
						$arr['display_name'] = "Undifferentiated";
						}else{
							$arr['imw'] = "";
						}
					}
				}
			}
			return $arr;
		}
		
		function get_race_heirarcy($race,$race_code=''){
			$RACE_DATA_AR = array();
			if(!empty($race_code)){
				$q = "SELECT race_name,cdc_code,parent_id,h_code FROM `race` WHERE is_deleted = '0' AND cdc_code LIKE '$race_code' LIMIT 1";
			}else if(!empty($race)){
				$q = "SELECT race_name,cdc_code,parent_id,h_code FROM `race` WHERE is_deleted = '0' AND race_name LIKE '$race' LIMIT 1";
			}
			if($q != ''){
				$res = imw_query($q);
				$rs = imw_fetch_assoc($res);
				$h_code		= $rs['h_code'];
				$RACE_DATA_AR[] = $rs;
				if($h_code!=''){
					$arr_h_code = explode('.',$h_code);
					if(count($arr_h_code)>1){// Its a child node, lookup for parent.
						for($i = 0; $i < count($arr_h_code); $i++){
							$removed_h_code = array_pop($arr_h_code);
							$remaining_h_code = implode('.',$arr_h_code);
							$q2 = "SELECT race_name,cdc_code,parent_id,h_code FROM race WHERE h_code LIKE '$remaining_h_code' LIMIT 1";
							$res2 = imw_query($q2);
							if($res2 && imw_num_rows($res2)>0){
								$rs2 = imw_fetch_assoc($res2);
								array_unshift($RACE_DATA_AR,$rs2);
							}
						}
					}
				}
			}
			return $RACE_DATA_AR;
		}
		
		function race_srh($val,$cdc_code=''){
			$val = trim(strtolower($val));
			$arrRace = array();
			if($cdc_code=="") $q = "SELECT race_name,cdc_code FROM `race` WHERE is_deleted = '0' AND race_name LIKE '$val' LIMIT 1";
			else $q = "SELECT race_name,cdc_code FROM `race` WHERE is_deleted = '0' AND cdc_code LIKE '$cdc_code' LIMIT 1";
			$res = imw_query($q);
			$rs = imw_fetch_assoc($res);
			$arrRace = array("imw"=>$val,"code"=>$rs['cdc_code'],"display_name"=>$rs['race_name']);
			return $arrRace;
			
			/*$arr = array();
			if($val != ""){
				foreach($arrRace as $row){
					//$arr = explode(',',$row['imw']);
					if(in_array($val, $row)){
						$arr['imw'] = $row['imw'];
						$arr['code'] = $row['code'];
						$arr['display_name'] = $row['display_name'];
						break;
					}else{
						$arr['imw'] = $val;
						$arr['code'] = "2131-1";
						$arr['display_name'] = "Other Race";
					}
				}
			}
			return $arr;*/
		}
		function ethnicity_srh($val){
			$val = str_replace(',','',$val);
			$val = trim($val);
			$arrRace = array();
			$q = "SELECT ethnicity_name,cdc_code FROM `ethnicity` WHERE ".($val ? "LOWER(ethnicity_name) = '".strtolower($val)."' And " : '')." is_deleted = '0'";

			$res = imw_query($q);
			$i = 0;
			while($rs = imw_fetch_assoc($res)){
				$arrRace[$i]['imw'] 			= strtolower($rs['ethnicity_name']);
				$arrRace[$i]['code'] 			= $rs['cdc_code'];
				$arrRace[$i]['display_name'] 	= $rs['ethnicity_name'];
				$i++;
			}
			
			$arr = array();
			if($val != ""){
				$match = false;
				foreach($arrRace as $row){
					if(in_array($val, $row)){
						$match = true;
						$arr['code'] = $row['code'];
						$arr['display_name'] = $row['display_name'];
						break;
					}else{
						$arr['code'] = "2186-5";
						$arr['display_name'] = "Not Hispanic or Latino";
					}
					if( $match ) break;
				}
			}
			return $arr;
		}
		
		function code_to_language($code){
			$code = trim($code);
			$q = "SELECT lang_name FROM `languages` WHERE is_deleted = '0' AND (iso_639_1_code = '$code' OR iso_639_2_B_code = '$code')";
			$res = imw_query($q);
			if($res && imw_num_rows($res)>0){
				$rs = imw_fetch_assoc($res);
				return $rs['lang_name'];
			}else{
				if(strtolower($code)=='en-us') return 'English';
			}
		}

		
		function language_srh($val){
			$val = trim($val);
			
			$arrLang = array();
			$q = "SELECT lang_name,iso_639_1_code,iso_639_2_B_code FROM `languages` WHERE is_deleted = '0' AND lang_name LIKE '$val' LIMIT 1";
			$res = imw_query($q);
			while($rs = imw_fetch_assoc($res)){
				$arrLang['imw'] 			= strtolower($rs['lang_name']);
				$lcode = trim($rs['iso_639_1_code']);
				if($lcode==''){
					$lcode = trim($rs['iso_639_2_B_code']);
				}
				$arrLang['code'] 			= strtolower($lcode);
				$arrLang['display_name'] 	= $rs['lang_name'];
			}
			return $arrLang;
			/*$arr = array();
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
			return $arr;*/
		}
		
		function smoking_status_srh($val){
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
								array("imw"=>'condition',"code"=>"64572001","display_name"=> "Condition"),
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
					}
				}
			}
			return $arr;
		}
		
		function problem_status_srh($val){ 		// 	 SNOMED CT
			$val = trim($val);
			$arrProbStatus = array(
								array("imw"=>'active',"code"=>"active","display_name"=> "active"),
								array("imw"=>'suspended',"code"=>"suspended","display_name"=> "suspended"),
								array("imw"=>'aborted',"code"=>"aborted","display_name"=> "aborted"),
								array("imw"=>'completed',"code"=>"completed","display_name"=> "completed")
							  );
			$arr = array();
			if($val != ""){
				foreach($arrProbStatus as $row){
					if(in_array($val, $row)){
						$arr['code'] = $row['code'];
						$arr['display_name'] = $row['display_name'];
						break;
					}
				}
			}
			return $arr;
		}
		function allergy_type_srh($val, $map="code_to_imw"){ 	// 	 SNOMED CT
			$val = trim($val);
			$arrAllerType = array(
								/*array("imw"=>'',"code"=>"420134006","display_name"=> "Propensity to adverse reactions (disorder)"),
								array("imw"=>'',"code"=>"418038007","display_name"=> "Propensity to adverse reactions to substance (disorder)"),
								array("imw"=>'',"code"=>"419511003","display_name"=> "Propensity to adverse reactions to drug (disorder)"),
								array("imw"=>'',"code"=>"418471000","display_name"=> "Propensity to adverse reactions to food (disorder)"),*/
								array("imw"=>'fdbATAllergenGroup',"code"=>"419199007","display_name"=> "Allergy to substance (disorder)"),
								array("imw"=>'fdbATDrugName',"code"=>"416098002","display_name"=> "Drug allergy (disorder)"),
								array("imw"=>'fdbATIngredient',"code"=>"414285001","display_name"=> "Food allergy (disorder)")
								/*array("imw"=>'',"code"=>"59037007","display_name"=> "Drug intolerance (disorder)"),
								array("imw"=>'',"code"=>"235719002","display_name"=> "Food intolerance (disorder)")*/
							  );
			$arr = array();
			if($val != ""){
				foreach($arrAllerType as $row){
					if(in_array($val, $row)){
						if($map == "code_to_imw"){
							$arr['code'] = $row['code'];
							$arr['display_name'] = $row['display_name'];
						}else{
							$arr['imw'] = $row['imw'];
						}
						break;
					}
				}
			}
			return $arr;
		}
		function result_status_srh($val){ 	// 	 SNOMED CT
			$val = trim($val);
			$arrResultStatus = array(
								//array("imw"=>'3',"code"=>"aborted","display_name"=> "aborted"),
								array("imw"=>'1',"code"=>"active","display_name"=> "active"),
								array("imw"=>'3',"code"=>"cancelled","display_name"=> "cancelled"),
								array("imw"=>'2',"code"=>"completed","display_name"=> "completed")
								//array("imw"=>'held',"code"=>"held","display_name"=> "held"),
								//array("imw"=>'suspended',"code"=>"suspended","display_name"=> "suspended")
								/*array("imw"=>'',"code"=>"59037007","display_name"=> "Drug intolerance (disorder)"),
								array("imw"=>'',"code"=>"235719002","display_name"=> "Food intolerance (disorder)")*/
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
								array("imw"=>'O2Sat',"code"=>"59408-5","display_name"=> "O2 % BldC Oximetry"),
								array("imw"=>'B/P - Systolic',"code"=>"8480-6","display_name"=> "BP Systolic"),
								array("imw"=>'B/P - Diastolic',"code"=>"8462-4","display_name"=> "BP Diastolic"),
								array("imw"=>'Temperature',"code"=>"8310-5","display_name"=> "Body Temperature"),
								array("imw"=>'Height',"code"=>"8302-2","display_name"=> "Height"),
								array("imw"=>'Weight',"code"=>"29463-7","display_name"=> "Weight Measured"),
								array("imw"=>'BMI',"code"=>"39156-5","display_name"=> "BMI (Body Mass Index)"),
								array("imw"=>'InhaleO2',"code"=>"3150-0","display_name"=> "Inhaled Oxygen Concentration"),
								array("imw"=>'pulse',"code"=>"8867-4","display_name"=> "Heart Rate")
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
		function getIMWSite($val){
			if(preg_match('/left/i',$val)){
				$site = "1";
			}else if(preg_match('/right/i',$val)){
				$site = "2";
			}else if(preg_match('/mouth/i',$val)){
				$site = "4";
			}else if($val !=""){
				$site = "3";
			}
			return $site;
		}
		
		function get_med_route_val_code($input,$return='code'){
			/***IF $return='code', it will return NCI code against input value;
				if $return='value', it will return route value against NCI code****/
			if($return=='code'){$q = "SELECT code as val1 FROM route_codes WHERE LOWER(route_name)='".strtolower($input)."' LIMIT 1";}
			else{$q = "SELECT route_name as val1 FROM route_codes WHERE UPPER(code)='".strtoupper($input)."' LIMIT 1";}
			$res = imw_query($q);
			if($res && imw_num_rows($res)==1){
				$rs = imw_fetch_assoc($res);
				return $rs['val1'];
			}
			return false;
		}
		
		function convertToInt($val)
		{
			return (int)trim($val);
		}
		
//START FUNCTIONS OF CCDA XML FILE

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


function test_result_status_srh($val){ 	// 	 SNOMED CT
	$val = trim($val);
	$arrResultStatus = array(
						array("imw"=>'active',"code"=>"active","display_name"=> "active"),
						array("imw"=>'cancelled',"code"=>"cancelled","display_name"=> "cancelled"),
						array("imw"=>'completed',"code"=>"completed","display_name"=> "completed"),
						array("imw"=>'aborted',"code"=>"aborted","display_name"=> "aborted"),
						array("imw"=>'held',"code"=>"held","display_name"=> "held"),
						array("imw"=>'suspended',"code"=>"suspended","display_name"=> "suspended")
						//array("imw"=>'held',"code"=>"held","display_name"=> "held"),
						//array("imw"=>'suspended',"code"=>"suspended","display_name"=> "suspended")
						/*array("imw"=>'',"code"=>"59037007","display_name"=> "Drug intolerance (disorder)"),
						array("imw"=>'',"code"=>"235719002","display_name"=> "Food intolerance (disorder)")*/
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

function get_pt_problem_list($form_id='', $pid){
	$strType = implode(',',$arrType);
	$dataFinal = array();
	if(isset($form_id) && $form_id != ''){
		$sql_arc  = "select pt_problem_list 
				from  
				chart_genhealth_archive 
				where patient_id='".$pid."' AND
				form_id = '".$form_id."'";
	}else{
		//GETTING RECORDS WHERE PROB_TYPE IS OT EMPTY. bECAUSE ASSESSMENT RECORDS HAVE THIS FEILD EMPTY
		$sql  = "SELECT * FROM pt_problem_list WHERE prob_type != '' AND pt_id = '".$pid."' AND status IN ('Active','Completed')";
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
					if($arrData['status'] == 'Active' || $arrData['status'] == 'Completed'){
						$dataFinal[] = $arrData;
					}
			}
		}
	}
	return $dataFinal;
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
function getApproachSiteCode($site){
	global $bodysite_codes,$bodysite_snomed_codes;
	$arr = array();
	if($bodysite_codes[$site] != ""){
		$arr['ccda_code'] = $bodysite_snomed_codes[$route];
		$arr['ccda_display_name'] = $bodysite_codes[$route];
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

function valuesNewRecordsAssess($patient_id,$sel=" * ",$LF="0",$flgmemo=1)
{
	$strmemo = ($flgmemo==1) ? "AND chart_master_table.memo != '1' " : "" ;
	$LF = ($LF == "1") ? "AND chart_master_table.finalize = '1' " : "";
	$qry = "SELECT ".$sel." FROM chart_master_table ".
		  "INNER JOIN chart_assessment_plans ON chart_master_table.id = chart_assessment_plans.form_id ".
		  "WHERE chart_master_table.patient_id = '$patient_id' AND chart_master_table.delete_status='0' AND chart_master_table.purge_status='0' ".
		  "AND chart_master_table.record_validity = '1' ".
		  $strmemo. //do not get memo assessments and plans: 08-04-2014
		  $LF.
		  "ORDER BY chart_master_table.date_of_service DESC, chart_master_table.create_dt DESC, chart_master_table.id DESC LIMIT 0,1 ";
	$row = sqlQuery($qry);
	return $row;
}

function getPatientLabOrdered($pid){
	$q = "SELECT ltd.lab_status,lor.*,DATE_FORMAT(start_date,'%m-%d-%Y') AS lab_test_date_html,DATE_FORMAT(start_date,'%Y%m%d') AS lab_test_date_ccd FROM lab_observation_requested lor 
			JOIN lab_test_data ltd ON (lor.lab_test_id = ltd.lab_test_data_id) 
			WHERE ltd.lab_patient_id = '".$pid."' AND ltd.lab_status IN(1,2)";
	$res = imw_query($q);
	if(imw_num_rows($res)>0){
		$lab_ordered = array();
		while($rs = imw_fetch_assoc($res)){
			$rs['lab_destination'] = getLabOrderDestination($rs['service']);
			$lab_ordered[] = $rs;
		}
		return $lab_ordered;
	}else{
		return false;
	}
}

function getLabOrderDestination($labTestTitle){
	$q = "SELECT lab_contact_name, lab_radiology_phone, lab_radiology_address, lab_radiology_city, lab_radiology_state, 
		 lab_radiology_zip FROM `lab_radiology_tbl` WHERE `lab_radiology_name` LIKE '".$labTestTitle."' AND 
		 lab_radiology_status='0' LIMIT 1";
	$res = imw_query($q);
	if($res && imw_num_rows($res)>0){
		$rs = imw_fetch_assoc($res);
		return $rs;
	}
	return false;
}

function getPatientLabResults($labId,$pid){
	$q = "SELECT lor.*,DATE_FORMAT(result_date,'%m-%d-%Y') AS lab_result_date_html,DATE_FORMAT(result_date,'%Y%m%d') AS lab_result_date_ccd FROM lab_observation_result lor 
			JOIN lab_test_data ltd ON (lor.lab_test_id = ltd.lab_test_data_id) 
			WHERE lor.lab_test_id='".$labId."' AND ltd.lab_patient_id = '".$pid."' AND ltd.lab_status IN(1,2)";
	$res = imw_query($q);
	if(imw_num_rows($res)>0){
		$lab_ordered = array();
		while($rs = imw_fetch_assoc($res)){
			$lab_ordered[] = $rs;
		}
		return $lab_ordered;
	}else{
		return false;
	}
}

function getUDIprocWise($proc_id){
	$proc_id = trim($proc_id);
	if($proc_id=='') return false;
	$q = "SELECT * FROM `lists` WHERE `parent_id` = '$proc_id'";
	$res = imw_query($q);
	if($res && imw_num_rows($res)>0){
		$implantable_array = array();
		while($rs = imw_fetch_assoc($res)){
			$comments = explode('||', $rs['comments']);
			//$parse_udi = 
			$jsonData2 				= stripslashes(html_entity_decode($comments[2]));
			$jsonData2 				= str_replace("\\", "",$jsonData2);
			$temp_device_detail 	= json_decode($jsonData2,true);
			$device_detail 			= $temp_device_detail['gmdnTerms']['gmdn'];
			$rs['device_name'] 		= $device_detail['gmdnPTName'];
			$rs['device_desc'] 		= $device_detail['gmdnPTDefinition'];
			$implantable_array[] = $rs;
		}
		return $implantable_array;
	}else return false;
}

function getEncounterDiagnosis($form_id, $pid){
	$q = "SELECT * FROM pt_problem_list WHERE pt_id = '".$pid."' AND LOWER(status) != 'deleted'";
	if($form_id!='') $q .= " AND form_id = '".$form_id."'";
	else if($form_id=='') $q .= " AND (form_id != '' AND form_id != '0')";
	$res = imw_query($q);
	if($res && imw_num_rows($res)>0){
		$encounter_diagnosis = array();
		while($rs = imw_fetch_assoc($res)){
			$temp_title = $rs['problem_name'];
			$temp_title_ar = explode(';',$temp_title);
			$rs['problem_name'] = trim($temp_title_ar[0]);
			$encounter_diagnosis['problem_list_data'] = $rs;
		}
		return $encounter_diagnosis;
	}
	return false;
}

function getEncounterFacility($form_id,$pid){
	$q = "SELECT sa.sa_facility_id  AS facility 
			FROM schedule_appointments sa 
			JOIN chart_master_table cmt ON (cmt.date_of_service = sa.sa_app_start_date) 
			WHERE sa.sa_patient_id ='".$pid."' 
				  AND cmt.id = '".$form_id."' LIMIT 1";
	$res = imw_query($q);
	$facility = false;
	if($res && imw_num_rows($res)>0){
		$rs = imw_fetch_assoc($res);
		$facility = $rs['facility'];
	}
	if($facility){
		$qry_facility = "select name,phone,street,city,state,postal_code from facility where id='".$facility."' LIMIT 1";
	}else{
		$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type='1' LIMIT 1";
	}
	$res_fac = imw_query($qry_facility);
	if($res_fac && imw_num_rows($res_fac)>0){
		$rs_fac = imw_fetch_assoc($res_fac);
		return $rs_fac;
	}
	return false;
}

/*Create GUID for the ID (string) passed*/
function createGUID($id, $salt='')
{
	if( defined('IPORTAL_SERVER') && IPORTAL_SERVER !== '' ){
		$hash = IPORTAL_SERVER;
		$hash = md5($hash.$salt);
	}
	else
		$hash = md5('iPortal'.$salt);
	
	$id = (string)$id;
	
	$hash = substr_replace($hash, $id, -strlen($id));
	
	$hash = substr_replace($hash, '-', 8, 0);
	$hash = substr_replace($hash, '-', 13, 0);
	$hash = substr_replace($hash, '-', 18, 0);
	$hash = substr_replace($hash, '-', 23, 0);
	
	
	return $hash;
}

function getBirthSexInfo($patient_id){
	$q = "select * from general_medicine where patient_id = '$patient_id' AND birth_sex!='' AND birth_sex_date != '0000-00-00' LIMIT 1";
	$res = imw_query($q);
	if($res && imw_num_rows($res)>0){
		$rs = imw_fetch_assoc($res);
		return $rs;
	}
	return false;
}
//END FUNCTIONS OF CCDA XML FILE		
		
?>