<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
if($_REQUEST['contentOf'])echo'<div class="loader" style="display: block;"> <span><b class="fa fa-spinner fa-pulse"></b>&nbsp;Loading...</span> </div>';
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$content = $_REQUEST['contentOf'];
$preDefines = $_REQUEST['preDefines'];
$modelCost = $_REQUEST['modelCost'];
$supplyCost = $_REQUEST['supplyCost'];
$supplyQuickCode = $_REQUEST['supplyQuickCode'];
$supplyUsageUnit = $_REQUEST['supplyUsageUnit'];
$sbmtFrm = $_REQUEST['sbmtFrm'];
$predefineIds = $_REQUEST['predefineIds'];
$supplyCatIds = $_REQUEST['cat_id'];
$table = $_REQUEST['table'];
$idField = $_REQUEST['idField'];
$sortby = $_REQUEST['sortby'];
$proc_search_id = $_REQUEST['proc_search_id'];
$viewBy = $_REQUEST['viewBy'];

$delete		=	false;
$update	=	false;
$insert	 =	false;

// include Trait File {}
switch($content){
	case "Mac/Regional Questions":
		$traitFile = '../library/trait/mac_regional_questions.php';
	break;	
	default:
		$traitObj = false;
		$traitFile = false;
	break;	
}

if( $traitFile ) {
	include_once($traitFile);	
	/*class PreDefineAdmin {
		//use PreDefineAdmin;
	}*/
	$traitObj = new PreDefineAdmin;
}
//var_dump($traitObj);
// End including Trait

//$preDefineStatus=$_REQUEST['predefineStatus'];

//DELETE SELECTED RECORDS

$prac_staus_id = $_REQUEST['prac_staus_id'];

$deleteSelected = $_REQUEST['deleteSelected'];

if($prac_staus_id && (strtolower($content)=="practice name" || strtolower($content)=="specialty" || strtolower($content)=="procedures category" ) ){
	list($prededines_id,$status)=explode("@@",$prac_staus_id);
	$arrayDelRecord['del_status']="";
	if($status=="active"){
		$arrayDelRecord['del_status']="yes";
	}elseif($status=="inactive"){
		$arrayDelRecord['del_status']="";	
	}
	$objManageData->UpdateRecord($arrayDelRecord, $table, $idField, $prededines_id);
}

if($prac_staus_id && (strtolower($content)=="supplies used" || $content=='Supply Categories' || $content=='Intra Op Post Op Orders' || $content=="History and Physical" || $content=="Mac/Regional Questions") ){
	list($prededines_id,$status)=explode("@@",$prac_staus_id);
	$arrayDelRecord['deleted']=0;
	if($status=="active"){
		$arrayDelRecord['deleted']=1;
	}
	$objManageData->UpdateRecord($arrayDelRecord, $table, $idField, $prededines_id);
}


$prac_sta=$_REQUEST['prac_status'];
if($deleteSelected){
	$counter=0;
	$chkBox = $_REQUEST['chkBox'];
	foreach($chkBox as $prededines_id){
		unset($arrayRecord);
		if(strtolower($content)=="practice name" || $content=='Procedures' || $content=='Diagnosis ICD9' || $content=='Diagnosis ICD10' || strtolower($content)=="specialty" || $content=="Supplies Used" || $content=="Supply Categories" || strtolower($content)=="procedures category" || strtolower($content)=="procedures group" || $content=='Intra Op Post Op Orders' || $content == "History and Physical" || $content=="Mac/Regional Questions" ){
			if($content=='Diagnosis ICD10' || $content=='Supplies Used' || $content=='Intra Op Post Op Orders' || $content=="Supply Categories" || $content == "History and Physical" || $content=="Mac/Regional Questions") {
				$arrayRecord['deleted']="1";
			}else {
				$arrayRecord['del_status']="yes";
			}
			$del_rec=$objManageData->UpdateRecord($arrayRecord, $table, $idField, $prededines_id);
			if($del_rec)$counter++;
		}
		else{
			$del_rec=$objManageData->delRecord($table, $idField, $prededines_id);
				if($del_rec)$counter++;
		}
		//START CODE TO DELETE CHILD RECORD
		if($table=='manufacturer_lens_category') {
			$objManageData->delRecord('manufacturer_lens_brand', 'catId', $prededines_id);
			//if($del_rec)$counter++;		
		}
		elseif($table=='preopnursecategory') {
			$objManageData->delRecord('preopnursequestion', 'preOpNurseCatId', $prededines_id);
			//if($del_rec)$counter++;		
		}
		//END CODE TO DELETE CHILD RECORD
	}
	if($del_rec && !$c)
	{
			$delete		=	true;
		
	}
}


//DELETE SELECTED RECORDS
if($content=='Procedures'){
	$procCategoryListArr 		= $_REQUEST['procCategoryList'];
	$preDefineProcCat 			= $_REQUEST['preDefineProcCat'];	
	$preDefineDescArr 			= $_REQUEST['preDefineDesc'];
	$preDefinecodeArr 			= $_REQUEST['preDefinecode'];
	$preDefinecodeFacilityArr 	= $_REQUEST['preDefinecodeFacility'];
	//$preDefinecodeAnesthesiaArr = $_REQUEST['preDefinecodeAnesthesia'];
	$preDefinecodePracticeArr 	= $_REQUEST['preDefinecodePractice'];
	
	$preDefineAliasArr 			= $_REQUEST['preDefineAlias'];
	$procedureIdArr 			= $_REQUEST['procedureId'];
	$specialtyIdListArr 		= $_REQUEST['specialtyIdList'];
	$preDefineUnitsArr			= $_REQUEST['preDefineUnits'];
	$laborCostArr				= $_REQUEST['preDefineLaborCost'];
	$chkBoxPoeEnableArr			= $_REQUEST['chkBoxPoeEnable'];
	$preDefinePoeDaysArr		= $_REQUEST['preDefinePoeDays'];
	unset($arrayRecord);
	if(is_array($preDefineDescArr)){
		foreach($preDefineDescArr as $k => $desc){
			$desc=addslashes($desc);
			$preDefinecodeArr[$k] 					= addslashes($preDefinecodeArr[$k]);
			$preDefinecodeFacilityArr[$k] 			= addslashes($preDefinecodeFacilityArr[$k]);
			//$preDefinecodeAnesthesiaArr[$k] 		= addslashes($preDefinecodeAnesthesiaArr[$k]);
			$preDefinecodePracticeArr[$k] 			= addslashes($preDefinecodePracticeArr[$k]);
			$preDefineAliasArr[$k] 					= addslashes($preDefineAliasArr[$k]);
			if($procCategoryListArr[$k]!=''){
				$procedure_id = $procedureIdArr[$k];
				$arrayRecord['catId'] 				= $procCategoryListArr[$k];
				$arrayRecord['name'] 				= $desc;
				$arrayRecord['code'] 				= $preDefinecodeArr[$k];
				$arrayRecord['codeFacility'] 		= $preDefinecodeFacilityArr[$k];
				//$arrayRecord['codeAnesthesia'] 		= $preDefinecodeAnesthesiaArr[$k];
				$arrayRecord['codePractice'] 		= $preDefinecodePracticeArr[$k];
				$arrayRecord['procedureAlias'] 		= $preDefineAliasArr[$k];
				$arrayRecord['specialty_id'] 		= $specialtyIdListArr[$k];
				$arrayRecord['units'] 				= $preDefineUnitsArr[$k];
				$arrayRecord['labor_cost'] 			= $laborCostArr[$k];
				$arrayRecord['poe_enable'] 			= $chkBoxPoeEnableArr[$procedure_id];
				if(($procedure_id && $chkBoxPoeEnableArr[$procedure_id]!='1')) {
					$preDefinePoeDaysArr[$k] 		= "";
				}elseif(!$procedure_id && $chkBoxPoeEnableArr[0]!='1') {
					$preDefinePoeDaysArr[$k] 		= "";
				}
				$arrayRecord['poe_days'] 			= $preDefinePoeDaysArr[$k];
				
				if($procedureIdArr[$k]){
					$c=$objManageData->UpdateRecord($arrayRecord, $table, $idField, $procedure_id);
					if($c)  $update = true;
				}else{
					$arrayRecord['poe_enable'] 		= $chkBoxPoeEnableArr[0];
					$d=$objManageData->addRecords($arrayRecord, $table);
					if($d) $insert = true;
				}
			}else{
				if($preDefineProcCat){
					$arrProcCatRecord['name'] 		= $preDefineProcCat;
					$arrayRecord['catId'] 			= $objManageData->addRecords($arrProcCatRecord, 'procedurescategory');
					$arrayRecord['name'] 			= $desc;
					$arrayRecord['code'] 			= $preDefinecodeArr[$k];
					$arrayRecord['codeFacility'] 	= $preDefinecodeFacilityArr[$k];
					//$arrayRecord['codeAnesthesia'] 	= $preDefinecodeAnesthesiaArr[$k];
					$arrayRecord['codePractice'] 	= $preDefinecodePracticeArr[$k];
					$arrayRecord['procedureAlias'] 	= $preDefineAliasArr[$k];
					$arrayRecord['specialty_id'] 	= $specialtyIdListArr[$k];
					$arrayRecord['units'] 			= $preDefineUnitsArr[$k];
					$arrayRecord['labor_cost'] 		= $laborCostArr[$k];
					$arrayRecord['poe_enable'] 		= $chkBoxPoeEnableArr[0];
					$arrayRecord['poe_days'] 		= $preDefinePoeDaysArr[$k];
					$objManageData->addRecords($arrayRecord, $table);
				}
			}
		}
	}
	
}

if($content=='Diagnosis ICD9'){
	$preDefineDiagCodeArr = $_REQUEST['preDefineDiagCode'];
	$preDefineORAliasArr = $_REQUEST['preDefineORAlias'];
	$diagnosisIdArr = $_REQUEST['diagnosisId'];
	
	if(is_array($preDefineDiagCodeArr)){
		foreach($preDefineDiagCodeArr as $k => $desc){
			$desc=addslashes($desc);
			$preDefineORAliasArr[$k] = addslashes($preDefineORAliasArr[$k]);
			$predefinedDiagID = $diagnosisIdArr[$k];
			unset($arrayRecord);
			if($desc)
			{
				$arrayRecord['diag_code'] = $desc;
				$arrayRecord['diag_alias'] = $preDefineORAliasArr[$k];
				
				if($predefinedDiagID){
					$c=$objManageData->UpdateRecord($arrayRecord, $table, $idField, $predefinedDiagID);
					if($c) $update = true;
				}else{
					$d=$objManageData->addRecords($arrayRecord, $table);
					if($d) $insert = true;
				}
			}
			
		}
		
	}
}

if($content=='Diagnosis ICD10'){
	$preDefineDiagCode9Arr = $_REQUEST['preDefineDiagCode9'];
	$preDefineDiagCode10Arr = $_REQUEST['preDefineDiagCode10'];
	$preDefineDiagDescArr = $_REQUEST['preDefineDiagDesc'];
	$preDefineDiagLateralityArr = $_REQUEST['preDefineDiagLaterality'];
	$preDefineDiagStagingArr = $_REQUEST['preDefineDiagStaging'];
	$preDefineDiagSeverityArr = $_REQUEST['preDefineDiagSeverity'];
	$diagnosisIdArr = $_REQUEST['diagnosisId'];
	
	if(is_array($preDefineDiagCode10Arr)){
		foreach($preDefineDiagCode10Arr as $k => $desc){
			$preDefineDiagDescArr[$k] = addslashes($preDefineDiagDescArr[$k]);
			$predefinedDiagID = $diagnosisIdArr[$k];
			unset($arrayRecord);
			$arrayRecord['icd9'] 		= $preDefineDiagCode9Arr[$k];
			$arrayRecord['icd10'] 		= $preDefineDiagCode10Arr[$k];
			$arrayRecord['icd10_desc'] 	= $preDefineDiagDescArr[$k];
			$arrayRecord['laterality'] 	= $preDefineDiagLateralityArr[$k];
			$arrayRecord['staging'] 	= $preDefineDiagStagingArr[$k];
			$arrayRecord['severity'] 	= $preDefineDiagSeverityArr[$k];
			//trim($preDefineDiagCode9Arr[$k]) || 
			//echo $k.'--'.trim($preDefineDiagCode10Arr[$k]).'<br>'; 
			if(trim($preDefineDiagCode10Arr[$k])) {
				if($predefinedDiagID){
					$c=$objManageData->UpdateRecord($arrayRecord, $table, $idField, $predefinedDiagID);
					if($c) $update = true;
				}else{
					$d=$objManageData->addRecords($arrayRecord, $table);
					if($d)  $insert = true;
				}
			}
			
		}
		
		
	}
}

if($content=='Manufacturer Lens Brand'){
	$manufacturerLensCategoryListArr = $_REQUEST['manufacturerLensCategoryList'];
	$preDefineManufacturerLensCat = $_REQUEST['preDefineManufacturerLensCat'];	
	$preDefineDescArr = $_REQUEST['preDefineDesc'];
	//$preDefinecodeArr = $_REQUEST['preDefinecode'];
	//$preDefineAliasArr = $_REQUEST['preDefineAlias'];
	$lensBrandIdArr = $_REQUEST['lensBrandId'];
	
	if(is_array($preDefineDescArr)){
		$counter=0;
		foreach($preDefineDescArr as $k => $desc){
			$desc=addslashes($desc);
			//$preDefinecodeArr[$k] = addslashes($preDefinecodeArr[$k]);
			//$preDefineAliasArr[$k] = addslashes($preDefineAliasArr[$k]);
			if($manufacturerLensCategoryListArr[$k]!=''){
				unset($arrayRecord);
				$arrayRecord['catId'] = $manufacturerLensCategoryListArr[$k];
				$arrayRecord['name'] = $desc;
				//$arrayRecord['code'] = $preDefinecodeArr[$k];
				//$arrayRecord['procedureAlias'] = $preDefineAliasArr[$k];
				$lensBrandId = $lensBrandIdArr[$k];
				if($lensBrandIdArr[$k]){
					$c=$objManageData->UpdateRecord($arrayRecord, $table, $idField, $lensBrandId);
					if($c) $update = true;
				}else{
					$d=$objManageData->addRecords($arrayRecord, $table);
					if($d) $insert = true;
				}
			}else{
				if($preDefineManufacturerLensCat){
					$arrManufacturerLensCatRecord['name'] = $preDefineManufacturerLensCat;
					unset($arrayRecord);
					$arrayRecord['catId'] = $objManageData->addRecords($arrManufacturerLensCatRecord, 'manufacturer_lens_category');
					$arrayRecord['name'] = $desc;
					//$arrayRecord['code'] = $preDefinecodeArr[$k];
					//$arrayRecord['procedureAlias'] = $preDefineAliasArr[$k];
					$objManageData->addRecords($arrayRecord, $table);
				}
			}
		}
		
	}
}

if($content=='Zip Codes'){
	$preDefineZipCodeArr 		= $_REQUEST['preDefineZipCode'];
	$preDefineZipCityArr 		= $_REQUEST['preDefineZipCity'];
	$preDefineZipStateArr 		= $_REQUEST['preDefineZipState'];
	$preDefineZipStateAbbreArr 	= $_REQUEST['preDefineZipStateAbbre'];
	$zipIdArr 					= $_REQUEST['zipId'];
	
	if(is_array($preDefineZipCodeArr)){
		foreach($preDefineZipCodeArr as $k => $desc){
			$desc							= addslashes($desc);
			$preDefineZipCityArr[$k] 		= addslashes($preDefineZipCityArr[$k]);
			$preDefineZipStateArr[$k] 		= addslashes($preDefineZipStateArr[$k]);
			$preDefineZipStateAbbreArr[$k] 	= addslashes($preDefineZipStateAbbreArr[$k]);
			$predefinedZipID 				= $zipIdArr[$k];
			unset($arrayRecord);
			$arrayRecord['zip_code'] 		= $desc;
			$arrayRecord['city'] 			= $preDefineZipCityArr[$k];
			$arrayRecord['state'] 			= $preDefineZipStateArr[$k];
			$arrayRecord['state_abb'] 		= $preDefineZipStateAbbreArr[$k];
			
			if($desc) {
				if($predefinedZipID){
					$c=$objManageData->UpdateRecord($arrayRecord, $table, $idField, $predefinedZipID);
					if($c) $update = true;
				}else{
					$d=$objManageData->addRecords($arrayRecord, $table);
					if($d) $insert = true;
				}
			}
		}
		
		
	}
	
	
//START PAGINATION FOR ZIP CODES
//##########paging 
$pageNum 	= $_REQUEST['page'];
$name = $_REQUEST['text'] == ""?"A":$_REQUEST['text'];
$limit  	= 150;

if(empty($pageNum) == true){
	$pageNum = 1;
}

$startLimit = ($pageNum) * $limit - $limit;
$qry = imw_query("select count(*) from zip_codes where state_abb like '$name%'");		
list($count) = imw_fetch_array($qry);
$totalPage = ceil($count / $limit);
$pageLimit = 10;
$startPage = $pageNum - $pageLimit;		
if($startPage < 1){
	$startPage =  1 ;
}

$endPage = $pageLimit + $pageNum;
if($endPage > $totalPage){
	$endPage = $totalPage;
}

for($i = $startPage;$i <= $endPage; $i++){
	if($i == $pageNum){
		$pageLink .= '<a class="text_10b">['.$i.']</a>&nbsp;&nbsp;';
	}
	else{
		$pageLink .= '<a href="predefineFrmForm.php?page='.$i.'&amp;text='.$name.'&amp;contentOf='.$content.'&amp;table='.$table.'&amp;idField='.$idField.'" class="text_10b_purpule">'.$i.'</a>&nbsp;&nbsp;';
	}
}


//------ Start Next Pagination --------------

if($count > $limit + $startLimit){
	$pageNumber = $pageNum + 1;
	$startLink = '<a href="predefineFrmForm.php?page='.$pageNumber.'&amp;text='.$name.'&amp;contentOf='.$content.'&amp;table='.$table.'&amp;idField='.$idField.'" class="text_10b_purpule">Next</a>';
}
if(0 < $startLimit - $pageNum){
	$pageNumber = $pageNum - 1;
	$previousLink = '<a href="predefineFrmForm.php?page='.$pageNumber.'&amp;text='.$name.'&amp;contentOf='.$content.'&amp;table='.$table.'&amp;idField='.$idField.'" class="text_10b_purpule">Previous</a>';
}

if($startLink){
	$startLink = '<td style="width:20%; background-color:#bfd3e6;" class="text_10ab alignCenter">'.$startLink.'</td>';
}
else{
	$startLink = '<td style="width:20%; background-color:#bfd3e6;" class="text_10ab alignCenter"></td>';
}
if($previousLink){
	$previousLink = '<td style="width:20%; background-color:#bfd3e6;" class="text_10ab alignCenter">'.$previousLink.'</td>';
}
else{
	$previousLink = '<td style="width:20%; background-color:#bfd3e6;" class="text_10ab alignCenter"></td>';
}

//------ End Next Pagination --------------		
if($pageLink){
	$pageLinks = '
		'.$previousLink.'
			<td style="background-color:#bfd3e6;" class="text_10ab alignCenter">'.$pageLink.'</td>
		'.$startLink.'
	';
}	
//END PAGINATION FOR ZIP CODES	
}

if($content=='Modifiers'){
	
	$modifierIdArr 	= $_REQUEST['modifierId'];
	$modCodeArr		= $_REQUEST['modifierCode'];	
	$practiceCodeArr= $_REQUEST['practiceCode'];
	$descriptionArr	=	$_REQUEST['description'];	
	
	if(is_array($modCodeArr) && count($modCodeArr) > 0 )
	{
		$counter=0;
		
		foreach($modCodeArr as $key => $modifierCode)
		{
			$practiceCode	=	$practiceCodeArr[$key];
			$description		=	addslashes($descriptionArr[$key]);
			$modifierId		= $modifierIdArr[$key];
			
			if($modifierCode)
			{
				unset($arrayRecord);
				$arrayRecord['modifierCode']	= $modifierCode;
				$arrayRecord['practiceCode']	= $practiceCode;
				$arrayRecord['description']		= $description;
				
				if($modifierId)
				{
					$c=$objManageData->UpdateRecord($arrayRecord, $table, $idField, $modifierId);
					if($c) $update = true;
				}
				else
				{	
					$d=$objManageData->addRecords($arrayRecord, $table);
					if($d) $insert = true;
				}
			}

		}
		
	}
}

if($content=='Pre-Op Nurse'){
	
	$preOpNurseCategoryListArr	=	$_REQUEST['preOpNurseCategoryList'];
	$nurseQuestionId 			=	$_REQUEST['nurseQuestionId'];
	$preOpNurseQuestionName		=	$_REQUEST['preOpNurseQuestionName'];
	$showTxtBoxStatusArr		=	$_REQUEST['showTxtBoxStatus'];
	if(is_array($preOpNurseQuestionName))
	{
		foreach($preOpNurseQuestionName as $key => $orderName)
		{
			if($orderName!='')
			{
				$prevOrderId = $nurseQuestionId[$key];
				unset($chkRecordArr);
				$chkRecordArr['preOpNurseQuestionName = '] =  addslashes($orderName);
				$chkRecordArr['preOpNurseCatId = '] =  $preOpNurseCategoryListArr[$key];
				if($prevOrderId)
					$chkRecordArr['preOpNurseQuestionId <> '] =  $prevOrderId;
					
				//getAllRecords($table,$fields = array(), $where = array(), $groupBy = array(), $orderBy = array(), $returnKey = '' )
				$chkRecords	=	$objManageData->getAllRecords('preopnursequestion',array('preOpNurseQuestionId'),$chkRecordArr);
				if(!$chkRecords)
				{
					if($prevOrderId)
					{
						$arrayUpdateRecord['preOpNurseQuestionName']= addslashes($orderName);
						$arrayUpdateRecord['preOpNurseCatId'] 		= $preOpNurseCategoryListArr[$key];
						$arrayUpdateRecord['showTxtBoxStatus'] 		= $showTxtBoxStatusArr[$prevOrderId];
						
						if($preOpNurseCategoryListArr[$key]) {
							$c=$objManageData->UpdateRecord($arrayUpdateRecord, 'preopnursequestion', 'preOpNurseQuestionId', $prevOrderId);
							if($c) $update = true;
						}
					}
					else
					{
						$arrayAddRecord['preOpNurseQuestionName'] 	= addslashes($orderName);
						$arrayAddRecord['preOpNurseCatId'] 			= $preOpNurseCategoryListArr[$key];
						$arrayAddRecord['showTxtBoxStatus'] 		= $showTxtBoxStatusArr[0];
						if($preOpNurseCategoryListArr[$key]) {
							$d=$objManageData->addRecords($arrayAddRecord, 'preopnursequestion');
							if($d) $insert = true;
						}
					}
				}
				else
				{
					$error	=	true;
					$message = 'Record(s) already exists';
				}
			}
		}
		
	}
	
}

if($content=='Procedures Group'){

	$proceduresGroupId 			=	$_REQUEST['proceduresGroupId'];
	
	if(strlen(trim($_REQUEST['procedureGroupName_0'][0]))>=1)
	{	
		$procedure_id_arr	=	implode(',',$_REQUEST['procedureGroupList_0']);
		$procedure_name_arr	=	$_REQUEST['procedureGroupName_0'];
		$procedure_name_arr = 	$procedure_name_arr[0];
		if($procedure_name_arr!='')
		{
			$chkRecordArr['name = '] =  addslashes($procedure_name_arr);
			//$chkRecordArr['procedures = '] =  implode(',',$procedure_id_arr);
			
			$chkRecords	=	$objManageData->getAllRecords('proceduregroup',array('proceduresGroupId'),$chkRecordArr);
			if(!$chkRecords)
			{
				$arrayAddRecord['name'] = addslashes($procedure_name_arr);
				$arrayAddRecord['procedures'] = $procedure_id_arr;
				$d=$objManageData->addRecords($arrayAddRecord, 'proceduregroup');
				if($d) $insert = true;
				
			}
			else
			{
				$error	=	true;
				$message = 'Record(s) already exists';
			}
		}
		
	}elseif(is_array($proceduresGroupId))
	{
		foreach($proceduresGroupId as $key => $group_id)
		{
			$procedure_id_arr	=	implode(',',$_REQUEST['procedureGroupList_'.$group_id]);
			$procedure_name_arr	=	implode(',',$_REQUEST['procedureGroupName_'.$group_id]);
			if($procedure_name_arr!='')
			{
				$arrayUpdateRecord['name'] = addslashes($procedure_name_arr);
				$arrayUpdateRecord['procedures'] = $procedure_id_arr;
				$c=$objManageData->UpdateRecord($arrayUpdateRecord, 'proceduregroup', 'proceduresGroupId', $group_id);
				if($c) $update = true;
			}
		}	
	}
	
}

if($sbmtFrm){
	// ADD UPDATE RECORDS
	foreach($preDefines as $key => $preDefineDesc){
		//echo $key ; 
		if($preDefineDesc!=''){
			$preDefineDesc = addslashes($preDefineDesc);
			$predefinedID = $predefineIds[$key];
			$supplyCatId = $supplyCatIds[$key];
			$costSup = $supplyCost[$key];
			$costModel = $modelCost[$key];
			$defaultChkBoxValue	= $_REQUEST['defaultChkBox'.$predefinedID] ; 
			$defaultChkBoxValue	= ($defaultChkBoxValue)	?	$defaultChkBoxValue : 0 ;
			$quickCodeSup 	= $supplyQuickCode[$key];
			$billableSup 	= $_REQUEST['supplyBillable'.$predefinedID] ; 
			$billableSup	= ($billableSup)	?	$billableSup : 0 ;
			$usageUnitSup	= $supplyUsageUnit[$key];

			
			unset($arrayRecord);
			// ADD UPDATE RECORDS			
			if($content=='Health Questionnaire'){
				$arrayRecord['question'] = $preDefineDesc;
				$arrayRecord['isDefault'] 	= $defaultChkBoxValue;
			}else if($content=='Pre-operative comments'){
				$arrayRecord['comments'] = $preDefineDesc;
				$arrayRecord['isDefault'] 	= $defaultChkBoxValue;
			}else if($content=='Diagnosis ICD9'){
				$arrayRecord['diag_code'] = $preDefineDesc;
			}else if($content=='Diagnosis ICD10'){
				$arrayRecord['diag_code'] = $preDefineDesc;
			}else if($content=='Recovery comments'){
				$arrayRecord['recoveryComments'] = $preDefineDesc;
				$arrayRecord['isDefault'] 	= $defaultChkBoxValue;
			}else if($content=='Operating room nurses notes'){
				$arrayRecord['notes'] = $preDefineDesc;
				$arrayRecord['isDefault'] 	= $defaultChkBoxValue;
			}else if($content=='Quality Measures'){
				$arrayRecord['name'] = $preDefineDesc;
				$getStatus=($predefinedID!="")?$_REQUEST['predefineStatus'.$key]:"active";
				$arrayRecord['status']=$getStatus;
				$arrayRecord['isDefault'] 	= $defaultChkBoxValue;
			}else if($content=='Specialty'){
				$arrayRecord['specialty_name'] = $preDefineDesc;
			}else if($content=='Model'){
				$arrayRecord['name'] 		= $preDefineDesc;
				$arrayRecord['isDefault'] 	= $defaultChkBoxValue;
				$arrayRecord['model_cost'] 	= $costModel;
			}else if($content=='Supplies Used'){
				
				$qtyChkBoxValue							= $_REQUEST['qtyChkBox'.$predefinedID] ; 
				$qtyChkBoxValue							= ($qtyChkBoxValue)	? $qtyChkBoxValue : 0 ; 
				$arrayRecord['name'] 				= trim($preDefineDesc);
				$arrayRecord['cat_id'] 	= trim($supplyCatId);
				$arrayRecord['qtyChkBox'] 	= $_REQUEST['qtyChkBox'.$predefinedID];
				$arrayRecord['isDefault'] 	= $defaultChkBoxValue;
				$arrayRecord['supplies_cost']= $costSup;
				$arrayRecord['supply_quick_code']= $quickCodeSup;
				$arrayRecord['supply_billable']= $billableSup;
				$arrayRecord['supply_usage_unit']= $usageUnitSup;
				
				
			}else if($content=='Supply Categories'){
				$arrayRecord['name'] 	= addslashes(trim($preDefineDesc));
				if( $predefinedID )	
					$arrayRecord['date_updated'] = date('Y-m-d H:i:s');
				else
					$arrayRecord['date_created'] = date('Y-m-d H:i:s');
				
			}
			else if($content=='Nourishment Kind'){
				$arrayRecord['name'] 					= trim($preDefineDesc);
				$arrayRecord['nourishmentKindDefault'] 	= $defaultChkBoxValue;
			}elseif($content=='Pre-Op Nurse Category'){
				$arrayRecord['categoryName'] = $preDefineDesc;
      }elseif($content=='Procedures Category'){
				$arrayRecord['name'] = $preDefineDesc;
			}elseif($content=='History and Physical'){
				$arrayRecord['name'] = addslashes($preDefineDesc);
			}else{
				$arrayRecord['name'] = $preDefineDesc;
				$arrayRecord['isDefault'] 	= $defaultChkBoxValue;
			}
			//START TO EXCLUDE isDefault column
			$isDefaultExcludeArr = array("Allergies","Health Questionnaire","Nurse Post-Op Checklist","Surgical Pack","Manufacturer Lens Brand","Quality Measures","Practice Name","Supply Categories","History and Physical");
			if(in_array($content,$isDefaultExcludeArr)){
				unset($arrayRecord['isDefault']);
			}
			//END TO EXCLUDE isDefault column
			//print_r($predefineIds);
			if($content=='Supplies Used' || $content=='Procedures Category' || $content=='History and Physical')
			{
				$whereArray		=	array();
				
				if(trim($quickCodeSup) && $content=='Supplies Used') {
					$whereArray	["(supply_quick_code = '".$quickCodeSup."' OR name = '".$preDefineDesc."') AND 1 = " ] =	'1' ;	//IF QUICK CODE EXIST THEN QUICK CODE OR NAME MUST BE UNIQUE
				}else {
					$whereArray	['name = ']	=	$preDefineDesc ;	
				}
				if( array_key_exists($key,$predefineIds))		$whereArray	[$idField.' <> ']	=	$predefinedID ;
				//print_r($whereArray); 
				$chkCount	=	$objManageData->getRowCount($table,$whereArray);
				//echo 'Check Count is :'.$chkCount ;echo '<br>';
				if( $predefinedID && $chkCount == 0 )
				{ 
					$c=$objManageData->UpdateRecord($arrayRecord, $table, $idField, $predefinedID);
					if($c) $update = true;
					
					if($content=='Supplies Used') {
						unset($arrayQcRecord);
						$arraySuppItemDetailRecord['supply_quick_code'] = $quickCodeSup;
						$objManageData->UpdateRecord($arraySuppItemDetailRecord, 'predefine_suppliesused_item_detail', 'suppliesUsedId', $predefinedID);	
					}
				}
				else if( !$predefinedID && $chkCount == 0 )
				{
					$d=$objManageData->addRecords($arrayRecord, $table);
					if($d) $insert = true;
				}
				else if( ($predefinedID && $chkCount > 0)  ||  (!$predefinedID && $chkCount > 0) )
				{
					$suppMsg = 'Record(s)';
					if(trim($quickCodeSup) && $content=='Supplies Used') {
						$suppMsg = 'Supplies or Quick Code';	
					}
					$error	=	true;	
					$message = $suppMsg.' already exists';
				}
				
			}
			else
			{
				if($predefinedID){
					$c=$objManageData->UpdateRecord($arrayRecord, $table, $idField, $predefinedID);
					if($c) $update = true;
				}else{
					$d=$objManageData->addRecords($arrayRecord, $table);
					if($d) $insert = true;
				}
			}
			
			//start code to generate XML
			$xmlFileName='';
			switch($content){
				case 'Evaluation':
					$field1 = 'name'; $field2 = 'evaluationId'; $xmlFileName = $table.'.xml';
				break;
				case 'Post-Op evaluation':
					$field1 = 'name'; $field2 = 'postOpEvaluationId'; $xmlFileName = $table.'.xml';
				break;
			}
			//end code to generate XML
			
			if($xmlFileName) {
				$objManageData->createXML($table,$field1,$field2,$xmlFileName,$table);	
			}
			// ADD UPDATE RECORDS

		}
	}
}

if( $traitObj ) {
	$r = $traitObj -> saveContent($_REQUEST);
	extract($r);
}

if($error)
	echo "<script>top.frames[0].alert_msg('error','".$message."')</script>";
elseif($insert && !$delete )				
		echo "<script>top.frames[0].alert_msg('success')</script>";
elseif($update && !$delete)
		echo "<script>top.frames[0].alert_msg('update')</script>";
elseif($delete)
		echo "<script>top.frames[0].alert_msg('del','".$counter."')</script>";
		
//DISPLAY RECORDS
switch($content){
	
	case 'Intra Op Post Op Orders':
		$getDetails = $objManageData->getArrayRecords('intra_op_post_op_order','' ,'' ,'name','Asc');
		$table = 'intra_op_post_op_order';
		$idField = 'intraOpId';
	break;
	
	case 'Mac/Regional Questions':
		//$getDetails = $objManageData->getArrayRecords('predefine_mac_regional_questions','' ,'' ,'name','Asc');
		$table = 'predefine_mac_regional_questions';
		$idField = 'id';
	break;
		
	case 'Modifiers':
		$getDetails = $objManageData->getArrayRecords('modifiers','' ,'' ,'modifierCode','ASC');
		$table = 'modifiers';
		$idField = 'modifierId';
	break;
	
	case 'Pre-Op Nurse Category':
		$getDetails = $objManageData->getArrayRecords('preopnursecategory','','','categoryName','ASC');
		$table = 'preopnursecategory';
		$idField = 'categoryId';
		$title = 'Pre-Op Nurse Question Category';
	break;
	
	case 'Pre-Op Nurse':
		$getDetails = $objManageData->getArrayRecords('preopnursequestion','','','preOpNurseQuestionName','ASC');
		$table 		= 'preopnursequestion';
		$idField 	= 'preOpNurseQuestionId';
		$title 		= 'Pre-Op Nurse Question';
	break;
	
	case 'Medications':
		$getDetails = $objManageData->getArrayRecords('medications','' ,'' ,'name','ASC');
		$table = 'medications';
		$idField = 'medicationsId';
	break;
	case 'Allergies':
		$getDetails = $objManageData->getArrayRecords('allergies','' ,'' ,'name','ASC');
		$table = 'allergies';
		$idField = 'allergiesId';
	break;
	case 'Evaluation':
		$getDetails = $objManageData->getArrayRecords('evaluation','' ,'' ,'name','ASC');
		$table = 'evaluation';
		$idField = 'evaluationId';
	break;
	case 'Complications':
		$getDetails = $objManageData->getArrayRecords('complications','' ,'' ,'name','ASC');
		$table = 'complications';
		$idField = 'complicationsId';
	break;
	case 'Dentition':
		$getDetails = $objManageData->getArrayRecords('dentation','' ,'' ,'name','ASC');
		$table = 'dentation';
		$idField = 'dentationId';
	break;
	case 'Health Questionnaire':
		$getDetails = $objManageData->getArrayRecords('healthquestioner','' ,'' ,'question','ASC');
		$table = 'healthquestioner';
		$idField = 'healthQuestioner';
	break;
	case 'History and Physical':
		$getDetails = $objManageData->getArrayRecords('predefine_history_physical','' ,'' ,'name','ASC');
		$table = 'predefine_history_physical';
		$idField = 'id';
	break;	
	case 'List of food ':
		$getDetails = $objManageData->getArrayRecords('fooddrinkslist','' ,'' ,'name','ASC');
		$table = 'fooddrinkslist';
		$idField = 'foodDrinksListId';
	break;
	case 'Pre-operative comments':
		$getDetails = $objManageData->getArrayRecords('preopcomments','' ,'' ,'comments','ASC');	
		$table = 'preopcomments';
		$idField = 'preOpCommentsId';
	break;
	case 'Site':
		$getDetails = $objManageData->getArrayRecords('site','' ,'' ,'name','ASC');	
		$table = 'site';
		$idField = 'siteId';
	break;
	case 'Procedures':
		$getDetails = $objManageData->getArrayRecords('procedurescategory','' ,'' ,'name','ASC');
		$getSubProcDetails = $objManageData->getArrayRecords('procedures');	
		$table = 'procedures';
		$idField = 'procedureId';
	break;
	case 'Diagnosis ICD9':
		$getDetails = $objManageData->getArrayRecords('diagnosis_tbl','' ,'' ,'diag_Desc','ASC');	
		$table = 'diagnosis_tbl';
		$idField = 'diag_id';
	break;
	case 'Diagnosis ICD10':
		$getDetails = $objManageData->getArrayRecords('icd10_data','' ,'' ,'icd10','ASC');	
		$table = 'icd10_data';
		$idField = 'id';
	break;
	case 'Nourishment Kind':
		$getDetails = $objManageData->getArrayRecords('nourishmentkind','' ,'' ,'name','ASC');	
		$table = 'nourishmentkind';
		$idField = 'nourishmentKindId';
	break;
	case 'Nurse Post-Op Checklist':
		$getDetails = $objManageData->getArrayRecords('postop_nurse_checklist','' ,'' ,'name','ASC');	
		$table = 'postop_nurse_checklist';
		$idField = 'postopNurseChecklistId';
	break;
	case 'Recovery comments':
		$getDetails = $objManageData->getArrayRecords('recoverycomments','' ,'' ,'recoveryComments','ASC');	
		$table = 'recoverycomments';
		$idField = 'recoveryCommentsId';
	break;
	case 'Physician Orders':
		$getDetails = $objManageData->getArrayRecords('patient2takehome','' ,'' ,'name','ASC');	
		$table = 'patient2takehome';
		$idField = 'patient2TakeHomeId';
	break;
	case 'Post-Op Drops':
		$getDetails = $objManageData->getArrayRecords('postopdrops','' ,'' ,'name','ASC');	
		$table = 'postopdrops';
		$idField = 'postOpDropsId';
	break;
	case 'Operating room nurses notes':
		$getDetails = $objManageData->getArrayRecords('oproomnursenotes','' ,'' ,'notes','ASC');	
		$table = 'oproomnursenotes';
		$idField = 'opRoomNurseNotesId';
	break;
	case 'Post-Op evaluation':
		$getDetails = $objManageData->getArrayRecords('postopevaluation','' ,'' ,'name','ASC');	
		$table = 'postopevaluation';
		$idField = 'postOpEvaluationId';
	break;
	case 'Surgical Pack':
		$getDetails = $objManageData->getArrayRecords('surgicalpack','' ,'' ,'name','ASC');	
		$table = 'surgicalpack';
		$idField = 'surgicalPackId';
	break;
	case 'IOL Manufacturer':
		$getDetails = $objManageData->getArrayRecords('manufacturer_lens_category','' ,'' ,'name','ASC');	
		$table = 'manufacturer_lens_category';
		$idField = 'manufacturerLensCategoryId';
	break;
	case 'Manufacturer Lens Brand':
		$getDetails = $objManageData->getArrayRecords('manufacturer_lens_category','' ,'' ,'name','ASC');
		$getSubProcDetails = $objManageData->getArrayRecords('manufacturer_lens_brand');	
		$table = 'manufacturer_lens_brand';
		$idField = 'lensBrandId';
	break;
	case 'Model':
		$getDetails = $objManageData->getArrayRecords('model','' ,'' ,'name','ASC');	
		$table = 'model';
		$idField = 'modelId';
	break;
	case 'Supplies Used':
		$getDetails = $objManageData->getArrayRecords('predefine_suppliesused','' ,'' ,'name','ASC');	
		$table = 'predefine_suppliesused';
		$idField = 'suppliesUsedId';
	break;
	case 'Supply Categories':
		$getDetails = $objManageData->getArrayRecords('supply_categories','' ,'' ,'name','DESC');	
		$table = 'supply_categories';
		$idField = 'id';
	break;
	case 'Laser Hx. of Present Illness':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_hx_present_illness_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_hx_present_illness_tbl';
		$idField = 'Hx_Present_illnessID';
	break;
	case 'Laser Past Medical Hx':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_past_medical_hx_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_past_medical_hx_tbl';
		$idField = 'past_medical_hxID';
	break;
	case 'Laser SLE':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_sle_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_sle_tbl';
		$idField = 'sle_ID';
	break;
	case 'Laser Fundus Exam':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_fundus_exam_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_fundus_exam_tbl';
		$idField = 'fundus_exam_ID';
	break;
	case 'Laser Mental State':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_mentalstate_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_mentalstate_tbl';
		$idField = 'mentalstateID';
	break;
	case 'Laser Post Op Orders':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_postprogressnotes_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_postprogressnotes_tbl';
		$idField = 'postprogressnotesID';
	break;
	case 'Laser Progress Note':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_postoperativestatus_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_postoperativestatus_tbl';
		$idField = 'postoperativestatusID';
	break;
	case 'Laser Chief Complaint':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_chiefcomplaint_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_chiefcomplaint_tbl';
		$idField = 'chiefcomplaintID';
	break;
	case 'Laser Spot Duration':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_spot_duration_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_spot_duration_tbl';
		$idField = 'spot_durationID';
	break;

	case 'Laser Spot Size':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_spot_size_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_spot_size_tbl';
		$idField = 'spot_sizeID';
	break;
	case 'Laser Power/Wattage':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_power_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_power_tbl';
		$idField = 'powerID';
	break;
	case 'Laser Shots':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_shots_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_shots_tbl';
		$idField = 'shots_ID';
	break;
	case 'Laser Total Energy':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_total_energy_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_total_energy_tbl';
		$idField = 'total_energyID';
	break;
	case 'Laser Degree of Opening':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_degree_opening_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_degree_opening_tbl';
		$idField = 'degree_openingID';
	break;

	case 'Laser Count':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_count_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_count_tbl';
		$idField = 'countID';
	break;
	case 'Laser Exposure':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_exposure_tbl','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_exposure_tbl';
		$idField = 'exposureID';
	break;
	case 'Quality Measures':
		$getDetails = $objManageData->getArrayRecords('qualitymeasuresadmin','' ,'' ,'name','ASC');	
		$table = 'qualitymeasuresadmin';
		$idField = 'qualityMeasuresId';
	break;
	case 'Zip Codes':
		$getDetails = $objManageData->getArrayRecords('zip_codes','' ,'' ,'state_abb','ASC, zip_code ASC');	
		$table = 'zip_codes';
		$idField = 'zip_id';
	break;
	case 'Practice Name':
		unset($condArr);
		$condArr['del_status'] = '';
		$getDetails = $objManageData->getArrayRecords('practice_name','' ,'','name','ASC');	
		$table = 'practice_name';
		$idField = 'practice_id';
	break;
	case 'Chart Unlock':
		$getDetails = $objManageData->getArrayRecords('chart_pt_lock_tbl','' ,'' ,'action_date_time','DESC');	
		$table = 'chart_pt_lock_tbl';
		$idField = 'id';
	break;
	case 'Specialty':
		unset($condArr);
		$condArr['del_status'] = '';
		$getDetails = $objManageData->getArrayRecords('specialty','' ,'' ,'specialty_name','ASC');	
		$table = 'specialty';
		$idField = 'specialty_id';
	break;
	case 'Procedures Category':
		unset($condArr);
		$condArr['del_status'] = '';
		$getDetails = $objManageData->getArrayRecords('procedurescategory','' ,'' ,'name','ASC');	
		$table = 'procedurescategory';
		$idField = 'proceduresCategoryId';
	break;
	case 'Procedures Group':
		$condArr['del_status'] = '';
		$getDetails = $objManageData->getArrayRecords('proceduregroup','','','name','ASC');
		$table 		= 'proceduregroup';
		$idField 	= 'proceduresGroupId';
		$title 		= 'Procedures Group';
	break;
	

	/*
	case 'Procedure Notes':
		$getDetails = $objManageData->getArrayRecords('laserpredefine_procedure_notes','' ,'' ,'name','ASC');	
		$table = 'laserpredefine_procedure_notes';
		$idField = 'predefine_procedure_notes_id';
	break;*/
	
}
//DISPLAY RECORDS
?>
<!DOCTYPE html>
<html>
<head>
<title>Pre Defines</title>
<?php include("adminLinkfile.php");?>
<script>
function newProcCat(obj){
	if(obj.value == 'Other'){
		document.getElementById('procCatTd').innerHTML = '<input type="text" class="text_10" name="preDefineProcCat" style="width:200px;">';
	}
	var boolReadOnly = false;
	var selectedText = obj.options[obj.selectedIndex].text;
	
	if(selectedText == 'Anesthesia'){
		boolReadOnly = true;
		$("#codeFacility").removeClass('group');
	}else{
		$("#codeFacility").addClass('group');
	}
	if(document.getElementById('preDefineAlias')) {
		document.getElementById('preDefineAlias').readOnly = boolReadOnly;
	}
	if(document.getElementById('codeFacility')) {
		document.getElementById('codeFacility').readOnly = boolReadOnly;
	}	
}
function newManufacturerLensCat(obj){
	if(obj.value == 'Other'){
		document.getElementById('manufacturerLensCatTd').innerHTML = '<input type="text" class="text_10" name="preDefineManufacturerLensCat" size="30">';
	}
}
function actFun(prac_staus_id,uniqueId,status) {
	document.getElementById(prac_staus_id).value='';
	if(uniqueId) {
		document.getElementById(prac_staus_id).value=uniqueId+'@@'+status;
		document.preDefineForm.submit();	
	}
}
function clearSearch()
{
	$("#proc_search_id").val('');
	var sortby=$("#sortby").val();	
	changeProc(sortby);
}
function changeView(viewBy)
{
	var proc_search_id=$("#proc_search_id").val();	
	var sortby=$("#sortby").val();	
	location.href = "predefineFrmForm.php?contentOf=Procedures&sortby="+sortby+"&proc_search_id="+proc_search_id+"&viewBy="+viewBy;
}

function changeProc(val) {
	var proc_search_id=$("#proc_search_id").val();
	var viewBy=$("#viewBy").val();
	location.href = "predefineFrmForm.php?contentOf=Procedures&sortby="+val+"&proc_search_id="+proc_search_id+"&viewBy="+viewBy;
	/*switch(val){
		case "Procedure":
			location.href = "predefineFrmForm.php?contentOf=Procedures&sortby=Procedure&proc_search_id="+proc_search_id;
		break;
		case "Category":
			location.href = "predefineFrmForm.php?contentOf=Procedures&sortby=Category&proc_search_id="+proc_search_id;
		break;
		case "Code":
			location.href = "predefineFrmForm.php?contentOf=Procedures&sortby=Code&proc_search_id="+proc_search_id;
		break;
		case "ProcedureAlias":
			location.href = "predefineFrmForm.php?contentOf=Procedures&sortby=ProcedureAlias&proc_search_id="+proc_search_id;
		break;
		default:
			location.href = "predefineFrmForm.php?contentOf=Procedures&proc_search_id="+proc_search_id;		
	}*/
}								

$(function()
{
	$('.active-record, .inactive-record').click(function(){
			
			var $this		=	$(this);
			var UField	=	'deleted';
			var UValue	=	$this.hasClass('active-record')	?	1 	:	0;
			var Tbl			=	$this.attr('data-table-name');
			var IField		=	$this.attr('data-unique-field');
			var ID			=	$this.attr('data-record-id');
			//alert('UF : ' +  UField +'--'+ 'UV:' + UValue+'--'+ 'TN:' + Tbl+'--'+'UO:' + IField+'--'+ 'UOV:'+ ID);
			$.ajax({
				url 	:	'updateStatus.php',
				type	:	'POST',
				dataType	:	"json",
				data : { 'UF' : UField ,'UV' : UValue, 'TN' : Tbl, 'UO' : IField, 'UOV': ID },
				beforeSend: function()
				{
					top.$(".loader").fadeIn(500);
				},
				complete: function()
				{
					top.$(".loader").fadeOut(500);
				},
				success :function (data)
				{ 
					if(data.success == 1)
					{
							if($this.hasClass('active-record'))
								$this.removeClass('active-record').addClass('inactive-record');
							else
								$this.removeClass('inactive-record').addClass('active-record');
					}
					else
					{
							top.frames[0].alert_msg('error',data.error_msg)
					}
					
				}
				
			});
			
	});
	
});
			
<?php if($content!=''){?>
	$(window).load(function()
	{
		$(".loader").fadeOut(1000).hide(1000); 
		
		var LDL	=	function()
		{
			var H	=top.frames[0].frames[0].frames[0].innerHeight-top.frames[0].frames[0].frames[0].$(".scheduler_margins_head").outerHeight(true);
			$("#local_scrollable_div").css({'min-height':H +'px','height':H +'px','max-height':H +'px','overflow':'auto'});
		}
		LDL();
		$(window).resize(function(e) {
           LDL();
        });
	});
<?php }?>
<?php if($content == 'Procedures'){?>
	$(function(){
			
			jQuery.extend({
				getValues: function(url) {
						var result = null;
						$.ajax({
								url: url,
								type: 'get',
								dataType: 'json',
								async: false,
								success: function(data) {
									result = data;
								}
						});
						return result;
				}
			});
			
			var PracCodes = $.getValues("../iDocPracticeCode.php?val="+Math.random());
			
			$("body").on('click focus', '.group',function(e){
				
				var $_this	=	$(this);
				var PId		=	$_this.attr('data-proc-id');
				var Id		=	$_this.attr('id');
				var Key		=	$("#procedureCode"+PId).val();
				
				
				var Top		=	setVerticalPosition($_this,$(document),$("#preDefineGroupProcedures"));// $_this.offset().top;
				var Left	=	setHorizontalPosition($_this,$(document),$("#preDefineGroupProcedures")) ;
				
				if(Key && PracCodes[Key])
				{
					var List		=	PracCodes[Key];
					var Html	=	'';	
					$("#preDefineGroupProcedures #HtmlContainer").html('')
					var counter = 0;
					$.each(List, function(i,v){
						counter++;
						Html		=	'<li tabindex="'+counter+'" data-field-id="'+Id+'" data-put-val="'+v+'" class="hoverdiv" style="padding:5px; border-bottom:1px solid #ccc; font-weight:bold; " >'+v+'</li>';
						$("#preDefineGroupProcedures #HtmlContainer").append(Html);
						$("#preDefineGroupProcedures").css({'left' : Left + 'px', 'top' : Top + 'px'}).fadeIn(50);
					});
					
					$("#preDefineGroupProcedures li:first").addClass('scrollSelected');
					
				}
				else
				{
					$("#preDefineGroupProcedures").fadeOut(100);	
					$("#preDefineGroupProcedures #HtmlContainer").html('');	
				}
				
			});
			
			$("body").on("keydown", ".group ", function(e){ 
				
				var li 	=	$("#preDefineGroupProcedures li.scrollSelected");
				var ix	=	$("#preDefineGroupProcedures li").index(li);
				var id =	li.attr('data-field-id');
				var vl =	li.attr('data-put-val');
					
				if(e.which === 38)	
				{	
					//e.preventDefault();
					if(ix > 0 )	
					{
						li.removeClass()
						li.prev().addClass('scrollSelected');	
					}
				} 
				else if(e.which === 40)	
				{
					//e.preventDefault();	
					if(ix < ($("#preDefineGroupProcedures li").length -1))
					{
						li.removeClass();
						li.next().addClass('scrollSelected');
					}
				}
				else if(e.which === 13)	
				{
					e.preventDefault();	
					$("#preDefineGroupProcedures").hide(50);	
					$("#preDefineGroupProcedures #HtmlContainer").html('');
					$(this).val(vl);	
				}
				else if(e.which === 27)	
				{
					e.preventDefault();	
					$("#preDefineGroupProcedures").hide(50);	
					$("#preDefineGroupProcedures #HtmlContainer").html('');
					$(this).focus(true);
					
				}
				else
				{
					return true;
				}
			
			});
			
			
			$("body").on("mouseenter", "#preDefineGroupProcedures", function(){ 
				//$(".group").unbind('blur');
				$("#hiddPopUpField").val("PopUpEnable");	
			});
			
			$("body").on("mouseleave","#preDefineGroupProcedures", function(){
				//$(".group").bind('blur');
				if($("#hiddPopUpField").val() == 'PopUpEnable' )
				{
					$(this).fadeOut(100);
					$("#hiddPopUpField").val('');
				}
			});
			
			$('body').on('click focus','body, select,input[type="checkbox"],input[name="preDefinecode[]"],input[name="preDefineDesc[]"],input[name="preDefineAlias[]"]',function(){
				$("#preDefineGroupProcedures").fadeOut(100);	
				$("#preDefineGroupProcedures #HtmlContainer").html('');	
			});
			
			$("body").on("click", "#preDefineGroupProcedures li", function(){ 
				var li 	=	$("#preDefineGroupProcedures li.scrollSelected");
				var id =	$(this).attr('data-field-id');
				var vl =	$(this).attr('data-put-val');
				li.removeClass()
				$(this).addClass('scrollSelected');	
				$("#"+id+"").val(vl).focus().trigger('blur');
				
				$("#preDefineGroupProcedures").hide(50);	
				$("#preDefineGroupProcedures #HtmlContainer").html('');	
				
			});
			//-------------------- autocomplete options for idoc cpt codes  -------------------------
			var CPT = $.getValues("../iDocPracticeCode.php?ctype=cpt&val="+Math.random());
			
			$("body").on('click focus keyup', '.typeHead',function(e){
				
				if(e.type == 'click' || e.type == 'focusin' || e.type == 'focus') { return ; }
				var $_this	=	$(this);
				var RowId	=	$_this.attr('id');
				var ContObj	=	$("#preDefineCPT");
				var Typo	=	$_this.val().toLowerCase();
				Typo		=	Typo.replace(/[^0-9a-z]/gi, '')
				
				if(Typo.length == 0 ) { 
					if(ContObj.hasClass('active')) {
						ContObj.removeClass('active').hide(50);
					}
					return; 
				} 
				
				var List	=	CPT;
				var FirstLi	=	$("#preDefineCPT li:first");
				var SelLi	=	$("#preDefineCPT li.scrollSelected");
				var SelIndex=	$("#preDefineCPT li").index(SelLi);
				
				if(!ContObj.hasClass('active'))
				{
					ContObj.addClass('active')
					var Top		=	setVerticalPosition($_this,$(document),ContObj);// $_this.offset().top;
					var Left	=	setHorizontalPosition($_this,$(document),ContObj) ;
				}
				
				if(e.which === 38)	
				{	
					if(SelIndex > 0 )	
					{
						SelLi.removeClass()
						SelLi.prev().addClass('scrollSelected');	
					}
				} 
				else if(e.which === 40)	
				{
					if(SelIndex < ($("#preDefineCPT li").length -1))
					{	  
						SelLi.removeClass();
						SelLi.next().addClass('scrollSelected');
					}
				}
				else if(e.which === 13)	
				{
					$("#preDefineCPT li.scrollSelected").trigger('click');
					$("#preDefineCPT").removeClass('active').hide(50);
					$("#preDefineCPT #HtmlContainer").html('');
					$_this.focus(true);
					
				}
				else if(e.which === 27)	
				{
					ContObj.removeClass('active').hide(50);
					$("#preDefineCPT #HtmlContainer").html('');
					$(this).focus(true);
					
				}
				else
				{
					$("#preDefineCPT #HtmlContainer").html('');
					var Html = '';
					var counter = 0;
					$.each(List, function(i,v){
						counter++;
						
						var elem = v['cpt4_code']; 
						if ((elem.toLowerCase().indexOf(Typo.toLowerCase()) != -1))
						{
							Html += '<li tabindex="'+RowId+'" data-row-index="'+RowId+'" data-cpt-val="'+elem+'" class="hoverdiv" style="padding:5px; border-bottom:1px solid #ccc; font-weight:bold; " >';
							Html += '<span style="width:100%">';
							Html += '<span style="width:25%">'+elem+'</span>';
							Html += '</span>';
							Html += '</li>';
							
							
						}
						
					});
					if(!Html)
					{
						Html = 	'<li tabindex="'+RowId+'" data-row-index="'+RowId+'" data-cpt-val="" class="hoverdiv" style="padding:5px; border-bottom:1px solid #ccc; font-weight:bold; " >No Result Match</li>';	
					}
					$("#preDefineCPT #HtmlContainer").append(Html);
					ContObj.css({'left' : Left + 'px', 'top' : Top + 'px'}).addClass('active').fadeIn(50);	
					$("#preDefineCPT li:first").addClass('scrollSelected');
				}
			 });
			
			// ------------------ autocomplete options for idoc cpt code ends here ------------------
			
			$("body").on("mouseenter", "#preDefineCPT", function(){ 
				$("#hiddPopUpField").val("PopUpEnable");	
			});
			
			$("body").on("mouseleave","#preDefineCPT", function(){
				if($("#hiddPopUpField").val() == 'PopUpEnable' )
				{
					$(this).fadeOut(100);
					$("#hiddPopUpField").val('');
				}
			});
			
			$('body').on('click focus','body, select,input',function(){
				$("#preDefineCPT").removeClass('active').fadeOut(100);	
				$("#preDefineCPT #HtmlContainer").html('');	
			});
			
			$("body").on("click", "#preDefineCPT li", function(){
				var li 	=	$("#preDefineCPT li.scrollSelected");
				var RowId=	$(this).attr('data-row-index');
				var Prac =	$(this).attr('data-cpt-val');
				
				li.removeClass()
				$(this).addClass('scrollSelected');
				
				$("#"+RowId+"").val(Prac)
				
				$("#preDefineCPT").removeClass('active').hide(50);	
				$("#preDefineCPT #HtmlContainer").html('');	
				
			});
			
			
			
			//================================================================================================//
			
			
			//-------------------- autocomplete options for idoc procedures -------------------------
			var Procedures= $.getValues("../iDocPracticeCode.php?ctype=proceduresCode&val="+Math.random());
			
			$("body").on('click focus keyup', '.procTypeHead',function(e){
				
				if(e.type == 'click' || e.type == 'focusin' || e.type == 'focus') { return ; }
				var $_this	=	$(this);
				var RowId	=	$_this.attr('id');
				var ContObj	=	$("#preDefineProc");
				var Typo	=	$_this.val().toLowerCase();
				Typo		=	Typo.replace(/[^0-9a-z]/gi, '')
				
				if(Typo.length == 0 ) { 
					if(ContObj.hasClass('active')) {
						ContObj.removeClass('active').hide(50);
					}
					return; 
				} 
				
				var List	=	Procedures;
				var FirstLi	=	$("#preDefineProc li:first");
				var SelLi	=	$("#preDefineProc li.scrollSelected");
				var SelIndex=	$("#preDefineProc li").index(SelLi);
				
				if(!ContObj.hasClass('active'))
				{
					ContObj.addClass('active')
					var Top		=	setVerticalPosition($_this,$(document),ContObj);// $_this.offset().top;
					var Left	=	setHorizontalPosition($_this,$(document),ContObj) ;
				}
				
				if(e.which === 38)	
				{	
					if(SelIndex > 0 )	
					{
						SelLi.removeClass()
						SelLi.prev().addClass('scrollSelected');	
					}
				} 
				else if(e.which === 40)	
				{
					if(SelIndex < ($("#preDefineProc li").length -1))
					{	  
						SelLi.removeClass();
						SelLi.next().addClass('scrollSelected');
					}
				}
				else if(e.which === 13)	
				{
					$("#preDefineProc li.scrollSelected").trigger('click');
					$("#preDefineProc").removeClass('active').hide(50);
					$("#preDefineProc #HtmlContainer").html('');
					$_this.focus(true);
					
				}
				else if(e.which === 27)	
				{
					ContObj.removeClass('active').hide(50);
					$("#preDefineProc #HtmlContainer").html('');
					$(this).focus(true);
					
				}
				else
				{
					$("#preDefineProc #HtmlContainer").html('');
					var Html = '';
					var counter = 0;
					$.each(List, function(i,v){
						counter++;
						
						var elem = v; 
						//alert(v+'\n' + elem + '--' + Typo +'\n' +  (elem.toLowerCase().indexOf(Typo.toLowerCase())) );
						if ((elem.toLowerCase().indexOf(Typo.toLowerCase()) !== -1))
						{
							
							Html += '<li tabindex="'+RowId+'" data-row-index="'+RowId+'" data-cpt-val="'+elem+'" class="hoverdiv" style="padding:5px; border-bottom:1px solid #ccc; font-weight:bold; " >';
							Html += '<span style="width:100%">';
							Html += '<span style="width:25%">'+elem+'</span>';
							Html += '</span>';
							Html += '</li>';
							
						}
						
						
					});
					if(!Html)
					{
						Html = 	'<li tabindex="'+RowId+'" data-row-index="'+RowId+'" data-cpt-val="" class="hoverdiv" style="padding:5px; border-bottom:1px solid #ccc; font-weight:bold; " >No Result Match</li>';	
					}
					$("#preDefineProc #HtmlContainer").append(Html);
					ContObj.css({'left' : Left + 'px', 'top' : Top + 'px'}).addClass('active').fadeIn(50);	
					$("#preDefineProc li:first").addClass('scrollSelected');
				}
			 });
			
			// ------------------ autocomplete options for idoc procedures ends here ------------------
			
			$("body").on("mouseenter", "#preDefineProc", function(){ 
				$("#hiddPopUpField").val("PopUpEnable");	
			});
			
			$("body").on("mouseleave","#preDefineProc", function(){
				if($("#hiddPopUpField").val() == 'PopUpEnable' )
				{
					$(this).fadeOut(100);
					$("#hiddPopUpField").val('');
				}
			});
			
			$('body').on('click focus','body, select,input',function(){
				$("#preDefineProc").removeClass('active').fadeOut(100);	
				$("#preDefineProc #HtmlContainer").html('');	
			});
			
			$("body").on("click", "#preDefineProc li", function(){
				var li 	=	$("#preDefineProc li.scrollSelected");
				var RowId=	$(this).attr('data-row-index');
				var Prac =	$(this).attr('data-cpt-val');
				
				li.removeClass()
				$(this).addClass('scrollSelected');
				
				$("#"+RowId+"").val(Prac)
				
				$("#preDefineProc").removeClass('active').hide(50);	
				$("#preDefineProc #HtmlContainer").html('');	
				
			});
			
	});
	
		
<?php }?>

	<?php if($content == 'Modifiers'){?>
	$(function(){
			
			jQuery.extend({
				getValues: function(url) {
						var result = null;
						$.ajax({
								url: url,
								type: 'get',
								dataType: 'json',
								async: false,
								success: function(data) {
									result = data;
								}
						});
						return result;
				}
			});
			
			//-------------------- autocomplete options for modifiers -------------------------
			var Modifiers = $.getValues("../iDocModifiers.php?val="+Math.random());
			
			$("body").on('click focus keyup', '.typeHead',function(e){
				
				if(e.type == 'click' || e.type == 'focusin' || e.type == 'focus') { return ; }
				var $_this	=	$(this);
				var RowId	=	$_this.attr('data-row-index');
				var ContObj	=	$("#preDefineModifiers");
				var Typo	=	$_this.val().toLowerCase();
				Typo		=	Typo.replace(/[^0-9a-z]/gi, '')
				
				if(Typo.length == 0 ) { 
					if(ContObj.hasClass('active')) {
						ContObj.removeClass('active').hide(50);
					}
					return; 
				} 
				
				var List	=	Modifiers;
				var FirstLi	=	$("#preDefineModifiers li:first");
				var SelLi	=	$("#preDefineModifiers li.scrollSelected");
				var SelIndex=	$("#preDefineModifiers li").index(SelLi);
				
				if(!ContObj.hasClass('active'))
				{
					ContObj.addClass('active')
					var Top		=	setVerticalPosition($_this,$(document),ContObj);// $_this.offset().top;
					var Left	=	setHorizontalPosition($_this,$(document),ContObj) ;
				}
				
				if(e.which === 38)	
				{	
					if(SelIndex > 0 )	
					{
						SelLi.removeClass()
						SelLi.prev().addClass('scrollSelected');	
					}
				} 
				else if(e.which === 40)	
				{
					if(SelIndex < ($("#preDefineModifiers li").length -1))
					{	  
						SelLi.removeClass();
						SelLi.next().addClass('scrollSelected');
					}
				}
				else if(e.which === 13)	
				{
					$("#preDefineModifiers li.scrollSelected").trigger('click');
					$("#preDefineModifiers").removeClass('active').hide(50);
					$("#preDefineModifiers #HtmlContainer").html('');
					$_this.focus(true);
					
				}
				else if(e.which === 27)	
				{
					ContObj.removeClass('active').hide(50);
					$("#preDefineModifiers #HtmlContainer").html('');
					$(this).focus(true);
					
				}
				else
				{
					$("#preDefineModifiers #HtmlContainer").html('');
					var Html = '';
					var counter = 0;
					$.each(List, function(i,v){
						counter++;
						
						var elemM = v['modifier_code']; 
						var elemP = v['mod_prac_code'];
						var elemD = v['mod_description'];
						if (	(elemM.toLowerCase().indexOf(Typo.toLowerCase()) != -1)
							||	(elemP.toLowerCase().indexOf(Typo.toLowerCase()) != -1)
							||	(elemD.toLowerCase().indexOf(Typo.toLowerCase()) != -1)
						   ) 
						
						{
							Html += '<li tabindex="'+RowId+'" data-row-index="'+RowId+'" data-mod-val="'+elemM+'" data-prac-val="'+elemP+'" data-description="'+elemD+'" class="hoverdiv" style="padding:5px; border-bottom:1px solid #ccc; font-weight:bold; " >';
							Html += '<span style="width:100%">';
							Html += '<span style="width:25%">'+elemM+'</span>';
							Html += '<span style="width:25%; margin-left:2%">'+elemP+'</span>';
							Html += '<span style="width:45%; margin-left:2%">'+elemD+'</span>';
							Html += '</span>';
							Html += '</li>';
							
							
						}
						
					});
					if(!Html)
					{
						Html = 	'<li tabindex="'+RowId+'" data-row-index="'+RowId+'" data-mod-val="" data-prac-val="" data-description="" class="hoverdiv" style="padding:5px; border-bottom:1px solid #ccc; font-weight:bold; " >No Result Match</li>';	
					}
					$("#preDefineModifiers #HtmlContainer").append(Html);
					ContObj.css({'left' : Left + 'px', 'top' : Top + 'px'}).addClass('active').fadeIn(50);	
					$("#preDefineModifiers li:first").addClass('scrollSelected');
				}
			 });
			
			// ------------- autocomplete options for modifiers code ends here -----------------
			
			
			
			$("body").on("mouseenter", "#preDefineModifiers", function(){ 
				$("#hiddPopUpField").val("PopUpEnable");	
			});
			
			$("body").on("mouseleave","#preDefineModifiers", function(){
				if($("#hiddPopUpField").val() == 'PopUpEnable' )
				{
					$(this).fadeOut(100);
					$("#hiddPopUpField").val('');
				}
			});
			
			$('body').on('click focus','body, select,input',function(){
				$("#preDefineModifiers").removeClass('active').fadeOut(100);	
				$("#preDefineModifiers #HtmlContainer").html('');	
			});
			
			$("body").on("click", "#preDefineModifiers li", function(){ 
				var li 	=	$("#preDefineModifiers li.scrollSelected");
				var RowId=	$(this).attr('data-row-index');
				var Mod =	$(this).attr('data-mod-val');
				var Prac=	$(this).attr('data-prac-val');
				var Desc=	$(this).attr('data-description');
				
				li.removeClass()
				$(this).addClass('scrollSelected');
				
				$("#practice"+RowId+"").val(Prac)
				$("#description"+RowId+"").val(Desc)
				$("#modifier"+RowId+"").val(Mod).focus().trigger('blur');
				
				$("#preDefineModifiers").removeClass('active').hide(50);	
				$("#preDefineModifiers #HtmlContainer").html('');	
				
			});
		});
<?php }?>

//FUNCTIONS END

	function KeyCheck(evt) {
		evt = (evt) ? evt : ((event) ? event : null);
		var evver = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null );
		var keynumber = evt.keyCode;
		if(keynumber==13){
			proc_search_link_id.click();  //IF PRESS ENTER THEN SEARCH LINK AUTOMATICALLY BE CLICKED
		}
	}
	
	function searchProc()
	{
		
		var sortby=$("#sortby").val();
		var viewBy=$("#viewBy").val();
		var proc_search_id=$("#proc_search_id").val();
		if(!proc_search_id){alert('Please enter somthing to search');return false;}
		location.href = "predefineFrmForm.php?contentOf=Procedures&sortby="+sortby+"&proc_search_id="+proc_search_id+"&viewBy="+viewBy;
		return false;
	}
	
	
	function LTrim( value ){
		var re = /\s*((\S+\s*)*)/;
		return value.replace(re, "$1");
	}
	// Removes ending whitespaces
	function RTrim(value){
		var re = /((\s*\S+)*)\s*/;
		return value.replace(re, "$1");
	}
	// Removes leading and ending whitespaces
	function trim(value){
		return LTrim(RTrim(value));
	}	
	//FUNCTIONS END 
	
	//function to remove special character from cpt code input box
	$(function(){
		$("body").on('keyup', "input[name='preDefinecode[]']", function() {
			var input = $(this);
			text = input.val().replace(/[^a-zA-Z0-9-\s]/g, "");
			if(/_|\s/.test(text)) {
				text = text.replace(/_|\s/g, "");
				// logic to notify user of replacement
			}
			input.val(text);
		});
	});	
	
	function checkAnes(obj,id)
	{
		var selectedText = obj.options[obj.selectedIndex].text;
		if(selectedText == 'Anesthesia')
		{
			$('#codeFacility'+id).attr('readonly',true);
			$('#preDefineAlias'+id).attr('readonly',true);
			$('#codeFacility'+id).removeClass('group');	
		}else
		{
			$('#codeFacility'+id).removeAttr('readonly');
			$('#preDefineAlias'+id).removeAttr('readonly');	
			$('#codeFacility'+id).addClass('group');
		}
	}
	function checkPoeFun(obj,id)
	{
		id = id || '';
		if(obj.checked == false)
		{
			$('#preDefinePoeDays'+id).attr('readonly',true);
		}else
		{
			$('#preDefinePoeDays'+id).removeAttr('readonly');
		}
	}
	function checkAllFn($_this){
		var obj = document.getElementsByName("chkBox[]");
		var len = obj.length;
		for(i=0; i<len; i++){
			if($_this.checked==true){
				obj[i].checked = true;
			}else{
				obj[i].checked = false;
			}
		}
	}
	function checkAllTxtBox($_this){
		var objNrsQuestIdArr 	= document.getElementsByName("nurseQuestionId[]");
		var len 				= objNrsQuestIdArr.length;
		var nurseQuestId		= "";
		var obj 				= "";
		for(i=0; i<len; i++){
			nurseQuestId 		= objNrsQuestIdArr[i].value;
			obj    		 		= document.getElementById("showTxtBoxStatus"+nurseQuestId);
			if($_this.checked==true){
				obj.checked 	= true;
			}else{
				obj.checked 	= false;
			}
		}
	}

</script>

</head>
<body>
<?php 
if($content){

	if($content!='Procedures' && $content!='Diagnosis ICD9' && $content!='Diagnosis ICD10' && $content!='Manufacturer Lens Brand' && $content!='Zip Codes' && $content!='Chart Unlock' && $content!='Modifiers' && $content!='Pre-Op Nurse' && $content!='Procedures Group' && $content <> 'Mac/Regional Questions' ){
		
			$hasDefault	=	1;
			if(		$content == 'Pre-Op Nurse Category' || $content == 'Specialty' || $content == 'Procedures Category' || $content == 'Procedures Group' 
				||	$content == 'Allergies' || $content == 'Health Questionnaire' || $content == 'History and Physical' || $content == 'IOL Manufacturer'
				||	$content == 'Nurse Post-Op Checklist' || $content == 'Surgical Pack' || $content == 'Practice Name' 
				||	$content == 'Quality Measures' ||	$content == 'Supply Categories'
			  )
			{
				$hasDefault = 0;	
			}
		?>
		<form name="preDefineForm" action="predefineFrmForm.php" method="post" autocomplete="off">
			<input type="hidden" name="sbmtFrm" value="true">
			<input type="hidden" name="contentOf" value="<?php echo $content; ?>">
			<input type="hidden" name="deleteSelected" value="">
			<input type="hidden" name="table" value="<?php echo $table; ?>">
			<input type="hidden" name="idField" value="<?php echo $idField; ?>">
			<input type="hidden" name="prac_staus_id" id="prac_staus_id" value="">
   <div class="scheduler_table_Complete">
    <div class="my_table_Checkall adj_tp_table" style="padding:0px; margin:0px">
       <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
		   <?php if($content=='Quality Measures'&& count($getDetails)>0){ ?>
           <thead>
            <tr>
                <th style="text-align:left;">Active</th>
                <th style="text-align:left;">Del</th>
                <?php if($hasDefault): ?>
                <th style="text-align:left;">Default</th>
                <?php endif; ?>
                <th style="text-align:left;">Question</th>
                <th style="text-align:left;">Active</th>
                <th style="text-align:left;">Del</th>
                <?php if($hasDefault): ?>
                <th style="text-align:left;">Default</th>
                <?php endif; ?>
                <th style="text-align:left;">Question</th>
		  </tr>
           </thead>	
            <?php 
			}else if($content=='Supplies Used'&& count($getDetails)>0) {
			?>
           <thead>
            <tr>
                <th style="text-align:left;">Active</th>
                <th style="text-align:left;">Del</th>
                <?php if($hasDefault): ?>
                <th style="text-align:left;">Default</th>
                <?php endif; ?>
                <th style="text-align:left;">Qty Box</th>
                <th style="text-align:left;">Category</th>
                <th style="text-align:left;">Supplies</th>
                <th style="text-align:left;">Supply Cost</th>
                <th style="text-align:left;">Quick Code</th>
                <th style="text-align:left;">Billable</th>
                <th style="text-align:left;">Usage Unit</th>
		  </tr>
           </thead>	
            <?php 
			}else if($content=='Model') {
			?>
            <thead>
            	<th style="text-align:left;"></th>
                <th style="text-align:left;">Del</th>
                <?php if($hasDefault): ?>
                <th style="text-align:left;">Default</th>
                <?php endif; ?>
                <th style="text-align:left;"><?php echo $content;?></th>
                <th style="text-align:left;">Model Cost</th>
                <th style="text-align:left;"></th>
                <th style="text-align:left;">Del</th>
                <?php if($hasDefault): ?>
                <th style="text-align:left;">Default</th>
                <?php endif; ?>
                <th style="text-align:left;"><?php echo $content;?></th>
                <th style="text-align:left;">Model Cost</th>
            </thead>
			<?php	
			}
			else {
			?>
            <thead>
            	<th style="text-align:left;"></th>
                <th style="text-align:left;">Del</th>
                <?php if($hasDefault): ?>
                <th style="text-align:left;">Default</th>
                <?php endif; ?>
                <th style="text-align:left;"><?php echo $content;?></th>
                <th style="text-align:left;"></th>
                <th style="text-align:left;">Del</th>
                <?php if($hasDefault): ?>
                <th style="text-align:left;">Default</th>
                <?php endif; ?>
                <th style="text-align:left;"><?php echo $content;?></th>
            </thead>
			<?php	
			}
			?>
            <tbody>
            <tr>
            <?php
				$supplyCatArr = $objManageData->getArrayRecords('supply_categories','','',"name='Other' ASC, name",'ASC');
                if(count($getDetails)>0){
                    foreach($getDetails as $key => $detailsPreDefine){
                        if($content=='Medications'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->medicationsId;
						}
                        if($content=='Allergies'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->allergiesId;
                        }
                        if($content=='Evaluation'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->evaluationId;
                        }
						if($content=='Complications'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->complicationsId;
                        }
						if($content=='Dentition'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->dentationId;
                        }
                        if($content=='Health Questionnaire'){
                            $preDefineDesc = $detailsPreDefine->question;
                            $preDefineID = $detailsPreDefine->healthQuestioner;
                        }
						if($content=='History and Physical'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->id;
							$preDefineStatus = $detailsPreDefine->deleted;
                        }
                        if($content=='List of food '){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->foodDrinksListId;
                        }
                        if($content=='Pre-operative comments'){
                            $preDefineDesc = $detailsPreDefine->comments;
                            $preDefineID = $detailsPreDefine->preOpCommentsId;
                        }
                        if($content=='Site'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->siteId;
                        }
                        /*
                        if($content=='Diagnosis ICD9'){
                            //$preDefineDesc = $detailsPreDefine->diag_Desc;
                            $preDefineDesc = $detailsPreDefine->diag_code;
                            $preDefineID = $detailsPreDefine->diag_id;
                        }*/
                        if($content=='Nourishment Kind'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->nourishmentKindId;
						}
						if($content=='Nurse Post-Op Checklist'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->postopNurseChecklistId;
                        }
                        if($content=='Recovery comments'){
                            $preDefineDesc = $detailsPreDefine->recoveryComments;
                            $preDefineID = $detailsPreDefine->recoveryCommentsId;
                        }
                        if($content=='Physician Orders'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->patient2TakeHomeId;
                        }
                        if($content=='Post-Op Drops'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->postOpDropsId;
                        }
                        if($content=='Operating room nurses notes'){
                            $preDefineDesc = $detailsPreDefine->notes;
                            $preDefineID = $detailsPreDefine->opRoomNurseNotesId;
                        }
                        if($content=='Post-Op evaluation'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->postOpEvaluationId;
                        }
                        if($content=='Surgical Pack'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->surgicalPackId;
                        }
                        if($content=='IOL Manufacturer'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->manufacturerLensCategoryId;
                        }
                        if($content=='Model'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->modelId;
							$model_cost = $detailsPreDefine->model_cost;
                        }
                        if($content=='Supplies Used'){
                            $preDefineDesc = $detailsPreDefine->name;
														$supplyCatID = $detailsPreDefine->cat_id;
														$qtyChkBox		=	$detailsPreDefine->qtyChkBox;
                            $preDefineID = $detailsPreDefine->suppliesUsedId;
														$preDefineStatus = $detailsPreDefine->deleted;
														$costSup = ($detailsPreDefine->supplies_cost>0)?$detailsPreDefine->supplies_cost:'';
														$quickCodeSup = $detailsPreDefine->supply_quick_code;
														$billableSup = $detailsPreDefine->supply_billable;
														$usageUnitSup = $detailsPreDefine->supply_usage_unit;
														
                        }
                        if($content=='Supply Categories'){
                            $preDefineDesc = stripslashes($detailsPreDefine->name);
														$preDefineStatus = $detailsPreDefine->deleted;
                            $preDefineID = $detailsPreDefine->id;
                        }
                        if($content=='Laser Hx. of Present Illness'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->Hx_Present_illnessID;
                        }
                        if($content=='Laser Past Medical Hx'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->past_medical_hxID;
                        }
                        if($content=='Laser SLE'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->sle_ID;
                        }
                        if($content=='Laser Fundus Exam'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->fundus_exam_ID;
                        }
                        if($content=='Laser Mental State'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->mentalstateID;
                        }
                        if($content=='Laser Post Op Orders'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->postprogressnotesID;
                        } 
                        if($content=='Laser Progress Note'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->postoperativestatusID;
                        }
                        if($content=='Laser Chief Complaint'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->chiefcomplaintID;
                        }
                        if($content=='Laser Spot Duration'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->spot_durationID;
                        }
                        if($content=='Laser Spot Size'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->spot_sizeID;
                        }
                        if($content=='Laser Power/Wattage'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->powerID;
                        }
                        if($content=='Laser Shots'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->shots_ID;
                        }
                        if($content=='Laser Total Energy'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->total_energyID;
                        }
                        if($content=='Laser Degree of Opening'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->degree_openingID;
                        }
                        if($content=='Laser Exposure'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->exposureID;
                        }
                        if($content=='Laser Count'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->countID;
                        }
                        if($content=='Quality Measures'){
                            $preDefineID = $detailsPreDefine->qualityMeasuresId;
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineStatus = $detailsPreDefine->status;
                        }
                        if($content=='Practice Name'){
                            $preDefineID = $detailsPreDefine->practice_id;
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineStatus = $detailsPreDefine->del_status;
                        }
                        if($content=='Specialty'){
                            $preDefineID = $detailsPreDefine->specialty_id;
                            $preDefineDesc = $detailsPreDefine->specialty_name;
                            $preDefineStatus = $detailsPreDefine->del_status;
                        }
												if($content=='Procedures Category'){
                            $preDefineID = $detailsPreDefine->proceduresCategoryId;
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineStatus = $detailsPreDefine->del_status;
														$preDefineIsMisc	=	$detailsPreDefine->isMisc;
														$preDefineIsInj	=	$detailsPreDefine->isInj;
												}
												if($content=='History and Physical'){
                            $preDefineID = $detailsPreDefine->id;
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineStatus = $detailsPreDefine->deleted;
												}
												if($content=='Pre-Op Nurse Category'){
                            $preDefineID = $detailsPreDefine->categoryId;
                            $preDefineDesc = $detailsPreDefine->categoryName;
                        }
                        if($content=='Intra Op Post Op Orders'){
                            $preDefineID = $detailsPreDefine->intraOpId;
                            $preDefineDesc = $detailsPreDefine->name;
														$preDefineStatus = $detailsPreDefine->deleted;
                        }
						/*
                        if($content=='Procedure Notes'){
                            $preDefineDesc = $detailsPreDefine->name;
                            $preDefineID = $detailsPreDefine->predefine_procedure_notes_id;
                        }*/
                        $preDefineDefault = $detailsPreDefine->isDefault;
												if($content=='Nourishment Kind'){
													$preDefineDefault = $detailsPreDefine->nourishmentKindDefault;
												}
						
						
                        ++$tr;
                        ?>
                        <td style="width:1%; text-align:center">
                        	<input type="hidden" value="<?php echo $preDefineID; ?>" name="predefineIds[]">
                          <?php if($content=='Quality Measures'){ ?>
                          <input type="checkbox" name="predefineStatus<?php echo($key);?>" <?php if($preDefineStatus=="active"){  ?> checked="checked" <?php } ?> value="active">
													<?php } ?>
                          <?php if($content=='Practice Name' || $content=='Specialty' ||  $content=='Supplies Used' || $content=='Procedures Category' || $content=='Intra Op Post Op Orders' || $content=='Supply Categories' || $content=='History and Physical'){
														if($content=='Supplies Used' || $content=='Intra Op Post Op Orders' || $content=='Supply Categories' || $content=='History and Physical' ) {
															$preDefineStatus	=	($preDefineStatus == 1) ? 'yes' : '' ;
														}
														if($content=='Procedures Category') {
															$doNotEdit = ($preDefineDesc == 'Procedure' || $preDefineDesc == 'Laser Procedure' || $preDefineDesc == 'G-Codes' || $preDefineDesc == 'Anesthesia' || $preDefineIsMisc == '1' || $preDefineIsInj == '1') ? 'yes' : '' ;
														}
														
														if($preDefineStatus!="yes"  ){ ?>
                                  	<img src="../images/active.jpg" style="cursor:pointer;" title="Active" alt="Active" <?php if($doNotEdit!='yes') {?>onClick="actFun('prac_staus_id','<?php echo $preDefineID; ?>','active');" <?php }?> > 
                            <?php }else{ ?>
                                <img style="cursor:pointer;" title="In-Active"  src="../images/inactive.jpg" value="<?php echo $preDefineID; ?>" <?php if($doNotEdit!='yes') {?> onClick="actFun('prac_staus_id','<?php echo $preDefineID; ?>','inactive');" <?php }?>>
                                <?php } ?>
                            <?php } ?>
                        </td>
                       			
                        <td style="width:1%; text-align:center"><input type="checkbox" name="chkBox[]" <?php if($doNotEdit=='yes') {?> disabled <?php }?> value="<?php echo $preDefineID; ?>"></td>
                       
                        <?php if($hasDefault): ?>
                        <td style="width:3%; text-align:center">
                            	<input type="checkbox" name="defaultChkBox<?php echo($preDefineID);?>" <?php if($preDefineDefault==1){  ?> checked="checked" <?php } ?> value="1" />
                      	</td>          
						<?php endif; ?>
                       		 
                        <?php if($content=='Supplies Used'): ?>
                        <td style="width:3%; text-align:center">
                            	<input type="checkbox" name="qtyChkBox<?php echo($preDefineID);?>" <?php if($qtyChkBox==1){  ?> checked="checked" <?php } ?> value="1" />
                      	</td>
                        <td style="width:9%; text-align:center">
                        	<select name="cat_id[]" class="selectpicker form-control" data-width="100%">
                          	<option value="" selected>Select</option>
                            <?php
															foreach( $supplyCatArr  as $s)
															{
																if( $s->deleted && $s->id <> $supplyCatID )
																	continue;
																	
																$tmp_selected = ($s->id == $supplyCatID) ? 'selected' : '';	  
																echo '<option value="'.$s->id.'" '.$tmp_selected.'>'.stripslashes($s->name).'</option>';
															}
														?>
                          </select>
                            	
                      	</td>          
						<?php endif; ?>
            
                        <td style="width:12%; text-align:left"><input type="text" class="form-control" name="preDefines[]" <?php if($doNotEdit=='yes') {?> readonly <?php }?> value="<?php echo stripslashes($preDefineDesc); ?>"></td>
                              
                         <?php if($content=='Model'): ?>     
                         <td style="width:4%; text-align:left"><input type="text" class="form-control" name="modelCost[]" value="<?php echo stripslashes($model_cost); ?>"></td>
                         <?php endif; ?>
                         
						 <?php if($content=='Supplies Used'): ?> 
                         <td style="width:3%; text-align:left"><input type="text" class="form-control" name="supplyCost[]" <?php if($doNotEdit=='yes') {?> readonly <?php }?> value="<?php echo stripslashes($costSup); ?>"></td>
                         <td style="width:6%; text-align:left"><input type="text" class="form-control" name="supplyQuickCode[]"   value="<?php echo stripslashes($quickCodeSup); ?>"></td>
                         <td style="width:2%; text-align:center"><input type="checkbox" name="supplyBillable<?php echo($preDefineID);?>" <?php if($billableSup=='1'){  echo 'checked'; } ?> value="1" /></td>
                         <td style="width:3%; text-align:left"><input type="text" class="form-control" name="supplyUsageUnit[]" value="<?php echo stripslashes($usageUnitSup); ?>"></td>
						 <?php endif; ?>  
                    <?php
                        if($tr>1 || ($tr>0 && $content=='Supplies Used')){
                            $tr = 0;
                            echo '</tr><tr>';
                        }
                    }
                }
                ?>
                </tr>
                </tbody>
        </table>
					
         </div>
   </div>
	  <div class="modal fade" id="predefimeTr">
     <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title rob">Add <?php echo $content;?> </h4>  
            </div>
            <div class="modal-body">
                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12  table-condensed cf  table-striped">
                <tbody>
                <tr><?php
								for($i=0;$i<1;$i++){
									++$new;
									?>
	          		<?php 
									if($content == 'Supplies Used'){ 
								?>		      
                		<td style="width:25%">
                   		<select name="cat_id[]" class="selectpicker" data-width="100%">
                          <option value="" selected>Select</option>
                          <?php
                            foreach( $supplyCatArr  as $s)
                            {
                              if( $s->deleted )
                                continue;
                             	echo '<option value="'.$s->id.'">'.stripslashes($s->name).'</option>';
                            }
                          ?>
                    	</select>
                 		</td>
                <?php
									}
								?>	
            		 		
                    <td align="center">
                    
                    	<input type="text"name="preDefines[]" value="" placeholder="<?php echo $content;?>" class="form-control">
                     </td>
                     <?php 
					 if($content == 'Model'){
					 ?>
                     <td style="width:25%; background-color:white;">
                     	<input type="text"name="modelCost[]" value="" placeholder="Model Cost" class="form-control">
                     </td>	
                     <?php		 
					 }
					 if($content == 'Supplies Used'){ ?>
                     <td style="width:25%; background-color:white;">
                     	<input type="text"name="supplyCost[]" value="" placeholder="Supply Cost" class="form-control">
                     </td>
                     <td style="width:25%; background-color:white; ">
                     	<label><b>Qty Box</b></label>
                        <input type="checkbox" name="qtyChkBox" checked="checked" value="1" />
                     </td>	
                     
										 <?php 	}elseif($hasDefault) {
					?>
                     <td style="width:50%; background-color:white; padding-left:10px; ">
                     		<label><b>Default</b></label>
                        <input type="checkbox" name="defaultChkBox" value="1" />
                     </td>	
                    
                    <?php }
					
						//if($new==2)
						//{
						//	$new = 0;
						//	echo '</tr><tr>';
						//}
					}
					?>  
                    </tr>
                <tr>
                     <?php		 
					 if($content == 'Supplies Used'){ ?>
                         <td style="width:25%; background-color:white;">
                            <input type="text"name="supplyQuickCode[]" value="" placeholder="Quick Code" class="form-control">
                         </td>
                         <td style="width:25%; background-color:white;">
                            <input type="text"name="supplyUsageUnit[]" value="" placeholder="Usage Unit" class="form-control">
                         </td>
                         <td style="width:25%; background-color:white; ">
                            <label><b>Billable</b></label>
                            <input type="checkbox" name="supplyBillable" checked="checked" value="1" />
                         </td>	
					 <?php 	
					 }
					?>  
                    </tr>
                    
                    </tbody>
                </table>
            </div>
			<div class="modal-footer">
            <div id="div_innr_btn">
                
                <a href="javascript:void(0)" class="btn btn-info " id="saveButtonSub" style="display: inline-block;" onclick="return top.frames[0].getPageSrc('Save');"><b class="fa fa-save"></b> Save</a>
                <a href="javascript:void(0)" class="btn btn-danger " id="cancelButton" style="display: inline-block;" data-dismiss="modal"><b class="fa fa-times"></b> Close</a>
                 
            </div>
            </div>
         
        </div>
     </div>
    </div>
          <script>
		  function closeModal()
		  {
          top.frames[0].frames[0].frames[0].$('#predefimeTr').modal({
					show: false,
					backdrop: true,
					keyboard: true
					});
		  }
          </script> 
          			
	</form>
	<?php
	}
	else if($content=='Procedures'){
		$getCategoryDetails = $objManageData->getArrayRecords('procedurescategory','' ,'' ,'name','ASC');
?>
       
		<form name="perDefineProceduresFrm" action="predefineFrmForm.php" method="post" autocomplete="off">
		<input type="hidden" name="contentOf" value="<?php echo $content; ?>">
		<input type="hidden" name="table" value="<?php echo $table; ?>">
		<input type="hidden" name="idField" value="<?php echo $idField; ?>">
		<input type="hidden" name="deleteSelected" value="">
        <input type="hidden" id="hiddPopUpField" value="">
         <div class="scheduler_table_Complete">
    <div class="my_table_Checkall adj_tp_table" style="padding:0px; margin:0px">
    <div class="col-xs-12 col-md-12 col-lg-12 col-sm-12">
	<div class="col-xs-2 col-md-1 col-lg-1 col-sm-2 text-right padding-top-adjustment">View By :</div>
    <div class="col-xs-4 col-md-2 col-lg-2 col-sm-4 text-left">
        <select name="viewBy" id="viewBy" class="form-control selectpicker" onChange="javascript:changeView(this.value);">
                                <option></option>
                                <?php
                                foreach($getCategoryDetails as $cat_desc){
                                    $category_del = $cat_desc->del_status;
                                    if($category_del!="yes" ){
                                    
									?>
                                    <option value="<?php echo $cat_desc->proceduresCategoryId; ?>" <?php if($_REQUEST['viewBy']==$cat_desc->proceduresCategoryId) echo "SELECTED"; ?>><?php echo $cat_desc->name; ?></option>
                                    <?php
									}
                                }
                                ?>
                            </select>
    </div>
    
    <div class="col-xs-2 col-md-1 col-lg-1 col-sm-2 text-right padding-top-adjustment">Sort By :</div>
    <div class="col-xs-4 col-md-2 col-lg-2 col-sm-4 text-left">
        <select name="sortby" id="sortby" class="form-control selectpicker" onChange="javascript:changeProc(this.value);">
            <option value="Category"  		<?php if($sortby=='Category') 		{ echo 'selected'; }?> >Category</option><!--
            <option value="Speciality" 	  	<?php if($sortby=='Speciality')		{ echo 'selected'; }?> >Speciality</option>-->
            <option value="Procedure" 		<?php if($sortby=='Procedure')		{ echo 'selected'; }?> >Procedure</option>
            <option value="Code" 	  		<?php if($sortby=='Code') 			{ echo 'selected'; }?> >Code</option>
            <option value="ProcedureAlias" 	<?php if($sortby=='ProcedureAlias') { echo 'selected'; }?> >Procedure Alias</option>
        </select>
    </div>
    
    <div class="col-xs-2 col-md-1 col-lg-1 col-sm-2 text-right padding-top-adjustment">Filter :</div>
    <div class="col-xs-4 col-md-2 col-lg-2 col-sm-4 text-left">
        <div class="input-group">
              <input name="proc_search_id" id="proc_search_id" onkeypress="KeyCheck(event)" type="text" class="form-control" value="<?php echo $proc_search_id;?>">
              <span class="input-group-addon" id="proc_search_link_id" onclick="return searchProc();"><b class="fa fa-search"></b></span>
          </div>
    </div>
    <?php if($proc_search_id){?>
    <div class="col-xs-4 col-md-2 col-lg-2 col-sm-4 text-left" style="padding-top:3px">
    <a href="javascript:void(0)" class="btn btn-info" id="clearSearch" style="display: inline-block;" onclick="return clearSearch();"><b class="fa fa-close"></b> Clear Search</a>
    </div>
    <?php }?>
    
   </div>
       <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
       <thead>
        <tr>
            <th style="width:2%"></th>
            <th style="text-align:left;">Category</th>
            <th style="text-align:left;">Speciality</th>
            <th style="text-align:left;">Procedure</th>
            <th style="text-align:left;">Code</th>
            <th style="text-align:left;">Surgeon</th>
            <th style="text-align:left;">Facility</th><!--
            <th style="text-align:left;">Anesthesia</th>-->
            <th style="text-align:left;">Units</th>
            <th style="text-align:left;">Labor Cost</th>
            <th style="text-align:left;">Procedure Alias</th>
            <th style="text-align:left;">POE</th>
            <th style="text-align:left;">POE Days</th>
        </tr>
       </thead>
       <tbody>
            <?php
            
            unset($condArr);
            $condArr['1'] = '1';
            $sortProcQry = 'procCat.name, proc.name';
            if($sortby=='Category') 		{$sortProcQry = 'procCat.name, proc.name'; 			}
            elseif($sortby=='Speciality') 		{$sortProcQry = 'proc.name, proc.code'; 			}
            elseif($sortby=='Procedure') 		{$sortProcQry = 'proc.name, proc.code'; 			}
            elseif($sortby=='Code') 			{$sortProcQry = 'proc.code, proc.name'; 			}
            elseif($sortby=='ProcedureAlias') 	{$sortProcQry = 'proc.procedureAlias, proc.name'; 	}
			
			$where.="";
			if($viewBy)
			{
				$where.=" AND proc.catId ='$viewBy'";	
			}
			if($proc_search_id)
			{
				$where.=" AND (proc.name LIKE '%$proc_search_id%'
								OR proc.code LIKE '%$proc_search_id%'
								OR proc.codePractice LIKE '%$proc_search_id%')";	
			}
            $getDetails = array();
            $procQry = "SELECT proc.* FROM procedures proc, procedurescategory procCat  
                        WHERE proc.catId = procCat.proceduresCategoryId 
                        AND proc.del_status !='yes' $where
                        ORDER BY ".$sortProcQry;
            $procRes = imw_query($procQry);
            if($procRes){
                while($procRow = imw_fetch_object($procRes)){
                    $getDetails[] = $procRow;
                }
                
            }
            if(count($getDetails)>0){
				
                 
				 
                foreach($getDetails as $key => $detailsPreDefine){
                    $preDefineDesc 				= $detailsPreDefine->name;
                    $preDefineCatId 			= $detailsPreDefine->catId;
                    $preDefineSpecialtyId 		= $detailsPreDefine->specialty_id;
                    
                    //CATEGORY ID DESC
                    $getCategoryIDDetails 	= $objManageData->getRowRecord('procedurescategory', 'proceduresCategoryId', $preDefineCatId,'name','ASC');
                     $catDesc 				= $getCategoryIDDetails->name;
                    //CATEGORY ID DESC
					$preDefinecode 				= $detailsPreDefine->code;
					$preDefinecodeFacility 		= $detailsPreDefine->codeFacility;
					//$preDefinecodeAnesthesia 	= $detailsPreDefine->codeAnesthesia;
					$preDefinecodePractice 		= $detailsPreDefine->codePractice;
                    $preDefineAlias 			= $detailsPreDefine->procedureAlias;						
                    $preDefineID 				= $detailsPreDefine->proceduresCategoryId;	
					$preDefineUnits				= ($detailsPreDefine->units != '0') ? $detailsPreDefine->units : '';
					$preDefineLaborCost			= $detailsPreDefine->labor_cost;
					$preDefinePoeDays			= ($detailsPreDefine->poe_days != '0') ? $detailsPreDefine->poe_days : '';	
                    ?>
                    <input type="hidden" name="procedureId[]" value="<?php echo $detailsPreDefine->procedureId; ?>">
                    <tr>
                        <td style="text-align:center;width:2%"><input type="checkbox" name="chkBox[]" value="<?php echo $detailsPreDefine->procedureId; ?>"></td>
                        <td style="text-align:left;width:10%">
                            <select name="procCategoryList[]" class="form-control selectpicker" onChange="checkAnes(this,'<?=$detailsPreDefine->procedureId?>')">
                                <option></option>
                                <?php
                                foreach($getCategoryDetails as $cat_desc){
                                    $category_del = $cat_desc->del_status;
                                    if($category_del!="yes" || $preDefineCatId==$cat_desc->proceduresCategoryId){
                                    
									?>
                                    <option value="<?php echo $cat_desc->proceduresCategoryId; ?>" <?php if($preDefineCatId==$cat_desc->proceduresCategoryId) echo "SELECTED"; ?>><?php echo $cat_desc->name; ?></option>
                                    <?php
									}
                                }
                                ?>
                            </select>
                        </td>
                        <td style="text-align:left;width:10%">
                            <select name="specialtyIdList[]" class="form-control selectpicker">
                                <option></option>
                                <?php
                                $getSpecialtyDetails = $objManageData->getArrayRecords('specialty','' ,'' ,'specialty_name','ASC');
                                foreach($getSpecialtyDetails as $specialty_desc){
                                    $specialty_del = $specialty_desc->del_status;
                                    if($specialty_del!="yes" || $preDefineSpecialtyId==$specialty_desc->specialty_id){
                                    ?>
                                        <option value="<?php echo $specialty_desc->specialty_id; ?>" <?php if($preDefineSpecialtyId==$specialty_desc->specialty_id) echo "SELECTED"; ?>><?php echo $specialty_desc->specialty_name; ?></option>
                                    <?php
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td style="text-align:left;width:18%"><input type="text" class="form-control procTypeHead" name="preDefineDesc[]" value="<?php echo stripslashes($preDefineDesc); ?>" id="preDefineDesc<?=$detailsPreDefine->procedureId?>" /></td>
                        <td style="text-align:left;width:8%"><input  type="text" class="form-control typeHead" name="preDefinecode[]" id="procedureCode<?=$detailsPreDefine->procedureId?>" value="<?php echo stripslashes($preDefinecode); ?>"></td>
                        <td style="text-align:left;width:8%" ><input  type="text" class="form-control group" name="preDefinecodePractice[]" id="codePractice<?=$detailsPreDefine->procedureId?>" data-proc-id="<?=$detailsPreDefine->procedureId?>" value="<?php echo stripslashes($preDefinecodePractice); ?>"></td>
                        <td style="text-align:left;width:8%" ><input  type="text" class="form-control group" <?php if($preDefineCatId=='21') {?> readonly   <?php } ?> name="preDefinecodeFacility[]" id="codeFacility<?=$detailsPreDefine->procedureId?>" data-proc-id="<?=$detailsPreDefine->procedureId?>"		value="<?php echo stripslashes($preDefinecodeFacility); ?>"></td>
                        <!--<td style="text-align:left;width:9%" ><input  type="text" class="form-control group" name="preDefinecodeAnesthesia[]" id="codeAnesthesia<?=$detailsPreDefine->procedureId?>" data-proc-id="<?=$detailsPreDefine->procedureId?>"  value="<?php echo stripslashes($preDefinecodeAnesthesia); ?>"></td>-->
                        <td style="text-align:left;width:6%"><input type="text" class="form-control" name="preDefineUnits[]" id="preDefineUnits<?=$detailsPreDefine->procedureId?>" value="<?php echo stripslashes($preDefineUnits); ?>"></td>
                        <td style="text-align:left;width:8%"><input type="text" class="form-control" name="preDefineLaborCost[]" id="preDefineLaborCost<?=$detailsPreDefine->procedureId?>" value="<?php echo stripslashes($preDefineLaborCost); ?>"></td>
                        <td style="text-align:left;width:12%"><input type="text" class="form-control" <?php if($preDefineCatId=='21') {?> readonly  <?php } ?> name="preDefineAlias[]" id="preDefineAlias<?=$detailsPreDefine->procedureId?>" value="<?php echo stripslashes($preDefineAlias); ?>"></td>
                    	<td style="text-align:center;width:2%"><input type="checkbox" name="chkBoxPoeEnable[<?=$detailsPreDefine->procedureId?>]" id="chkBoxPoeEnable<?=$detailsPreDefine->procedureId?>" value="1" <?php if($detailsPreDefine->poe_enable == "1") { echo "CHECKED"; }?> onClick="checkPoeFun(this,'<?=$detailsPreDefine->procedureId?>');"></td>
                        <td style="text-align:left;width:6%"><input type="number" class="form-control" name="preDefinePoeDays[]" id="preDefinePoeDays<?=$detailsPreDefine->procedureId?>" value="<?php echo stripslashes($preDefinePoeDays); ?>" <?php if($detailsPreDefine->poe_enable != "1") { ?> readonly <?php }?> ></td>
                    </tr>
                    <?php
                    }
                }
                ?>
          </tbody>
        </table>
    </div>
    </div>
						<?php
						for($j=0;$j<1;$j++){
							?>
                            
                             <div class="modal fade" id="predefimeTr">
                             <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header text-center">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title rob">Add <?php echo $content;?> </h4>  
                                    </div>
                                    <div class="modal-body">
                                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-condensed cf  table-striped">
                                       <tr>
								
											<td id="procCatTd">
												<select name="procCategoryList[]" class="form-control selectpicker" onChange="return newProcCat(this);">
													<option></option>
													<?php
													$getCategoryDetails = $objManageData->getArrayRecords('procedurescategory','' ,'' ,'name','ASC');
													foreach($getCategoryDetails as $cat_desc){
														?>
														<option value="<?php echo $cat_desc->proceduresCategoryId; ?>"><?php echo $cat_desc->name; ?></option>
														<?php
													}
													?>
													<option value="Other">Other</option>
												</select>
											</td>
                                            <td>
                                                <select name="specialtyIdList[]" class="form-control selectpicker">
                                                    <option></option>
                                                    <?php
                                                    $getSpecialtyDetails = $objManageData->getArrayRecords('specialty','' ,'' ,'specialty_name','ASC');
                                                    foreach($getSpecialtyDetails as $specialty_desc){
                                                        $specialty_del = $specialty_desc->del_status;
                                                        if($specialty_del!="yes"){
                                                        ?>
                                                            <option value="<?php echo $specialty_desc->specialty_id; ?>"><?php echo $specialty_desc->specialty_name; ?></option>
                                                        <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </td>
											<td align="center"><input type="text" name="preDefineDesc[]" id="preDefineDesc" class="form-control procTypeHead" /></td>
											<td align="center"><input type="text" name="preDefineAlias[]" id="preDefineAlias" class="form-control"></td>
										</tr>
										<tr>
											<td style="width:25%">Category</td>
                                            <td style="width:25%">Specialty</td>
											<td style="width:25%">Procedure</td>
											<td style="width:25%">Procedure Alias</td>
										</tr>
                                        <tr>
											<td align="center"><input type="text" name="preDefinecode[]" id="procedureCode" class="form-control typeHead"></td>
                                            <td align="center"><input type="text" name="preDefinecodePractice[]" id="codePractice" 	class="form-control group" data-proc-id=""></td>
                                            <td align="center"><input type="text" name="preDefinecodeFacility[]" id="codeFacility"	class="form-control group" data-proc-id=""></td>
                                            <td align="center">
                                            <input type="text" class="form-control" name="preDefineUnits[]" id="preDefineUnits" value="">
                                            <!--<input type="text" name="preDefinecodeAnesthesia[]" 	class="form-control">--></td>
                                            
                                        </tr>
                                        <tr>
											<td style="width:25%">Code</td>
                                            <td style="width:25%">Practice</td>
                                            <td style="width:25%">Facility</td>
                                            <td style="width:25%">Units</td>
                                            
										</tr>
                                        <tr>
											<td align="center" style=" width:25%; background-color:#FFFFFF;"><input type="text" class="form-control" name="preDefineLaborCost[]" id="preDefineLaborCost" value=""></td>
                                            <td align="left" style=" width:25%;background-color:#FFFFFF;">
                                            	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12   table-striped">
                                                	<tr>
                                                    	<td align="left" style="width:30%;"><input type="checkbox" name="chkBoxPoeEnable[0]" id="chkBoxPoeEnable" value="1" onClick="checkPoeFun(this,'');"></td>
                                                        <td align="left" style="width:70%;"><input type="number" name="preDefinePoeDays[]" id="preDefinePoeDays" 	class="form-control group" readonly ></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            
                                            <td align="center" style=" width:25%; background-color:#FFFFFF;"></td>
                                            <td align="center" style=" width:2%; background-color:#FFFFFF;"></td>
                                        </tr>
                                        <tr>
											<td style="width:25%">Labor Cost</td>
                                            <td align="left" style=" width:25%;background-color:#FFFFFF;">
                                            	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 " style="background-color:#FFFFFF;">
                                                	<tr>
                                                    	<td align="left" style="width:30%;background-color:#FFFFFF;">POE</td>
                                                        <td align="left" style="width:70%;background-color:#FFFFFF;">POE Days</td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td align="center" style=" width:25%; background-color:#FFFFFF;"></td>
                                            <td align="center" style=" width:25%; background-color:#FFFFFF;"></td>
                                            <td align="center" style=" width:2%; background-color:#FFFFFF;"></td>
                                        </tr>
									</table>
                                    </div>
                                    <div class="modal-footer">
                                    <div id="div_innr_btn">
                                        
                                        <a href="javascript:void(0)" class="btn btn-info " id="saveButtonSub" style="display: inline-block;" onclick="return top.frames[0].getPageSrc('Save');"><b class="fa fa-save"></b> Save</a>
                                        <a href="javascript:void(0)" class="btn btn-danger " id="cancelButton" style="display: inline-block;" data-dismiss="modal"><b class="fa fa-times"></b> Close</a>
                                         
                                    </div>
                                    </div>
                                 
                                </div>
                             </div>
                            </div>
                         <script>
                          function closeModal()
                          {
                          top.frames[0].frames[0].frames[0].$('#predefimeTr').modal({
                                    show: false,
                                    backdrop: true,
                                    keyboard: true
                                    });
                          }
                          </script> 
								
										
								
							<?php
						}
						?>
						
                        <div id="preDefineGroupProcedures"  style="z-index:9999" class="col-md-4 col-lg-3 col-xs-4 col-sm-4 preDefinePopUp padding_0">
                        	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Codes (Practice &amp; Facility)<span onClick="document.getElementById('preDefineGroupProcedures').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
                           <ul class="col-md-12 col-lg-12 col-xs-12 col-sm-12" id="HtmlContainer" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF; list-style:none;"></ul>
                           
                      	</div>
                        
                        
		</form>
        <div id="preDefineCPT" style="z-index:9999" class="col-md-4 col-lg-3 col-xs-4 col-sm-4 preDefinePopUp padding_0 active">
        	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Procedure Code<span onClick="document.getElementById('preDefineCPT').style.display='none';" style="float:right; color:#FFF; cursor:pointer; list-style:none; ">X</span></div>
            <ul class="col-md-12 col-lg-12 col-xs-12 col-sm-12" id="HtmlContainer" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"></ul>
      	</div>
        
        <div id="preDefineProc" style="z-index:9999" class="col-md-4 col-lg-3 col-xs-4 col-sm-4 preDefinePopUp padding_0 active">
        	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Procedures<span onClick="document.getElementById('preDefineProc').style.display='none';" style="float:right; color:#FFF; cursor:pointer; list-style:none; ">X</span></div>
            <ul class="col-md-12 col-lg-12 col-xs-12 col-sm-12" id="HtmlContainer" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"></ul>
      	</div>
        
		<?php
	
	}
	else if($content=='Diagnosis ICD9'){
		?>
		<form name="perDefineDiagnosisFrm" action="predefineFrmForm.php" method="post" autocomplete="off">
		<input type="hidden" name="contentOf" value="<?php echo $content; ?>">
		<input type="hidden" name="table" value="<?php echo $table; ?>">
		<input type="hidden" name="idField" value="<?php echo $idField; ?>">
		<input type="hidden" name="deleteSelected" value="">
	   <div class="scheduler_table_Complete">
    <div class="my_table_Checkall adj_tp_table" style="padding:0px; margin:0px">
       <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
       <thead>
         <tr>
                <th style="width:2%">&nbsp;</th>
                <th style="width:49%" align="left" class="">Diagnosis ICD9</th>
                <th style="width:49%" align="left" class="">OR Alias</th>
                
            </tr>
       </thead>
       <tbody>
            <?php
            //$getDetails = $objManageData->getArrayRecords('diagnosis_tbl','' ,'' ,'diag_code','ASC');
            unset($condArr);
            $condArr['1'] = '1';
            $getDetails = $objManageData->getMultiChkArrayRecords("diagnosis_tbl", $condArr,"diag_code","ASC"," AND del_status !='yes' ");
            
            
            if(count($getDetails)>0){
                foreach($getDetails as $key => $detailsPreDefine){
                    $preDefineDiagCode = $detailsPreDefine->diag_code;
                    $preDefineORAlias = $detailsPreDefine->diag_alias;	
                    $preDefineDiagId = $detailsPreDefine->diag_id;					
                    ?>
                    <input type="hidden" name="diagnosisId[]" value="<?php echo $detailsPreDefine->diag_id; ?>">
                    <tr>
                        <td align="center"><input type="checkbox" name="chkBox[]" value="<?php echo $detailsPreDefine->diag_id; ?>"></td>							
                        <td align="center"><input type="text" class="form-control" name="preDefineDiagCode[]" value="<?php echo stripslashes($preDefineDiagCode); ?>"></td>
                        <td align="center"><input type="text" class="form-control" name="preDefineORAlias[]" value="<?php echo stripslashes($preDefineORAlias); ?>"></td>
                    </tr>
                    <?php
                    }
                }
                ?>
         </tbody>
        </table>
			
        </div>
        </div>
    <?php
    for($j=0;$j<1;$j++){
        ?>
                <div class="modal fade" id="diagnosisTr">
     <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title rob">Diagnosis ICD9 </h4>  
            </div>
            <div class="modal-body">
                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12  table-condensed cf  table-striped">
	                <tr>
            		 <td align="center"><input type="text"  name="preDefineDiagCode[]" <?php if($content=='Diagnosis ICD9'){ ?> onBlur=" if(this.value==''){  this.value = 'Dx.code, Description'; this.style.color = '#CCCCCC'; }" onFocus="if(this.value=='Dx.code, Description'){ this.value = ''; this.style.color = '#000000'; }" <?php } ?> value="<?php if($content=='Diagnosis ICD9') echo ''; ?>" class="form-control" placeholder="Dx.code, Description"></td>
                        <td align="center"><input type="text" class="form-control" name="preDefineORAlias[]" value=""></td>
                        
                    </tr>
                    <tr>
                        <td >Diagnosis ICD9</td>
                        <td >OR Alias</td>
                    </tr>
                </table>
            </div>
			<div class="modal-footer">
            <div id="div_innr_btn">
                
                <a href="javascript:void(0)" class="btn btn-info " id="saveButtonSub" style="display: inline-block;" onclick="return top.frames[0].getPageSrc('Save');"><b class="fa fa-save"></b> Save</a>
                <a href="javascript:void(0)" class="btn btn-danger " id="cancelButton" style="display: inline-block;" data-dismiss="modal"><b class="fa fa-times"></b> Close</a>
                 
            </div>
            </div>
         
        </div>
     </div>
    </div>
          <script>
		  function closeModal()
		  {
          top.frames[0].frames[0].frames[0].$('#diagnosisTr').modal({
					show: false,
					backdrop: true,
					keyboard: true
					});
		  }
          </script>      
            
        <?php
    }
    ?>
								
		</form>
		<?php
	}
	else if($content=='Diagnosis ICD10'){
		$query=imw_query("select id,title from icd10_laterality where id IN(1,2) Order By title Asc 	");
		while($data=imw_fetch_object($query))
		{
			$laterArr[]=$data;	
		}
		//$query.close;
		
		$query=imw_query("select id,title from icd10_laterality where id IN(3) Order By title Asc");
		while($data=imw_fetch_object($query))
		{
			$severityArr[]=$data;	
		}
		//$query.close;
		
		$query=imw_query("select id,title from icd10_laterality where id IN(4,5) Order By title Asc");
		while($data=imw_fetch_object($query))
		{
			$stagingArr[]=$data;	
		}
		//$query.close;
		
		function get_list($extra,$sel,$laterArr)
		{
			$str='<select '.$extra.'><option value="">-- Select --</option>';
			foreach($laterArr as $data)
			{
				if($abbr=getNodes($data->id))$abbr="($abbr)";
				$str.='<option value="'.$data->id.'"';
				$str.=($data->id==$sel)?' selected':' ';
				$str.='>'.$data->title.$abbr.'</option>';
			}
			$str.='</select>';
			echo $str;
		}
		
		
		function getNodes($id)
		{
			$query=imw_query("select abbr from icd10_laterality where under ='$id' and deleted=0");
			while($data=imw_fetch_object($query))
			{
				$abbr[]=$data->abbr;
			}
			//$query.close;
			return implode(',',$abbr);
		}
		?>
		<form name="perDefineDiagnosisFrm" action="predefineFrmForm.php" method="post" autocomplete="off">
        
		<input type="hidden" name="contentOf" value="<?php echo $content; ?>">
		<input type="hidden" name="table" value="<?php echo $table; ?>">
		<input type="hidden" name="idField" value="<?php echo $idField; ?>">
		<input type="hidden" name="deleteSelected" value="">
	   <div class="scheduler_table_Complete">
    <div class="my_table_Checkall adj_tp_table" style="padding:0px; margin:0px">
       <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
       <thead>
         <tr>
                <th style="width:2%">&nbsp;</th>
                <th style="width:10%" align="left" class="">ICD9</th>
                <th style="width:10%" align="left" class="">ICD10</th>
                <th style="width:39%" align="left" class="">Description</th>
                <th style="width:13%" align="left" class="">Laterality</th>
                <th style="width:13%" align="left" class="">Staging</th>
                <th style="width:13%" align="left" class="">Severity</th>
                
            </tr>
       </thead>
       <tbody>
            <?php
            //$getDetails = $objManageData->getArrayRecords('diagnosis_tbl','' ,'' ,'diag_code','ASC');
            unset($condArr);
            $condArr['1'] = '1';
            $getDetails = $objManageData->getMultiChkArrayRecords("icd10_data", $condArr,"icd10","ASC"," AND deleted =0 AND icd10!='' "); //AND icd9!='' Group BY icd9
            
            
            if(count($getDetails)>0){
                foreach($getDetails as $key => $detailsPreDefine){
					?>
                    <input type="hidden" name="diagnosisId[]" value="<?php echo $detailsPreDefine->id; ?>">
                    <tr>
                        <td align="center"><input type="checkbox" name="chkBox[]" value="<?php echo $detailsPreDefine->id; ?>"></td>							
                        <td align="center"><input type="text" class="form-control" name="preDefineDiagCode9[]" value="<?php echo stripslashes($detailsPreDefine->icd9); ?>"></td>
                        <td align="center"><input type="text" class="form-control" name="preDefineDiagCode10[]" value="<?php echo stripslashes($detailsPreDefine->icd10); ?>"></td>
                        <td align="center"><input type="text" class="form-control" name="preDefineDiagDesc[]" value="<?php echo stripslashes($detailsPreDefine->icd10_desc); ?>"></td>
                        <td align="center"><?php get_list('class="form-control minimal" name="preDefineDiagLaterality[]"',$detailsPreDefine->laterality, $laterArr);?></td>
                        <td align="center"><?php get_list('class="form-control minimal" name="preDefineDiagStaging[]"',$detailsPreDefine->staging, $stagingArr);?></td>
                        <td align="center"><?php get_list('class="form-control minimal" name="preDefineDiagSeverity[]"',$detailsPreDefine->severity, $severityArr);?></td>
                    </tr>
                    <?php
                    }
                }
                ?>
         </tbody>
        </table>
			
        </div>
        </div>
    <?php
    for($j=0;$j<1;$j++){
        ?>
                <div class="modal fade" id="diagnosisTr">
     <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title rob">Diagnosis ICD10 </h4>  
            </div>
            <div class="modal-body">
                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12  table-condensed cf  table-striped">
	                <tr>
                         <td width="20%"><input type="text" name="preDefineDiagCode9[]" value="" class="form-control"></td>
                         <td width="20%"><input type="text" name="preDefineDiagCode10[]" value="" class="form-control"></td>
                         <td width="20%"><?php get_list('class="form-control selectpicker" name="preDefineDiagLaterality[]"','', $laterArr);?></td>
                         <td width="20%"><?php get_list('class="form-control selectpicker" name="preDefineDiagStaging[]"','', $stagingArr);?></td>
                         <td width="20%"><?php get_list('class="form-control selectpicker" name="preDefineDiagSeverity[]"','', $severityArr);?></td>
                    </tr>
                    <tr>
                        <td>ICD9</td>
                        <td>ICD10</td>
                        <td>Laterality</td>
                        <td>Staging</td>
                        <td>Severity</td>
                    </tr>
                    <tr>
                    	 <td colspan="5"><input type="text" name="preDefineDiagDesc[]" value="" class="form-control"></td>
                    </tr>
                    <tr>
                    	<td colspan="5">ICD10 Description</td>
                    </tr>
                </table>
            </div>
			<div class="modal-footer">
            <div id="div_innr_btn">
                
                <a href="javascript:void(0)" class="btn btn-info " id="saveButtonSub" style="display: inline-block;" onclick="return top.frames[0].getPageSrc('Save');"><b class="fa fa-save"></b> Save</a>
                <a href="javascript:void(0)" class="btn btn-danger " id="cancelButton" style="display: inline-block;" data-dismiss="modal"><b class="fa fa-times"></b> Close</a>
                 
            </div>
            </div>
         
        </div>
     </div>
    </div>
          <script>
		  function closeModal()
		  {
          top.frames[0].frames[0].frames[0].$('#diagnosisTr').modal({
					show: false,
					backdrop: true,
					keyboard: true
					});
		  }
          </script>      
            
        <?php
    }
    ?>
								
		</form>
		<?php
	}
	else if($content=='Manufacturer Lens Brand'){
		?>
		<form name="perDefineManufacturerLensBrandFrm" action="predefineFrmForm.php" method="post" autocomplete="off">
		<input type="hidden" name="contentOf" value="<?php echo $content; ?>">
		<input type="hidden" name="table" value="<?php echo $table; ?>">
		<input type="hidden" name="idField" value="<?php echo $idField; ?>">
		<input type="hidden" name="deleteSelected" value="">
		
         <div class="scheduler_table_Complete">
    <div class="my_table_Checkall adj_tp_table" style="padding:0px; margin:0px">
       <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
       <thead>
         <tr>
                <th style="width:2%">&nbsp;</th>
                <th style="width:49%" align="left" class="">Manufacturer</th>
                <th style="width:49%" align="left" class="">Lens Brand</th>
                
            </tr>
       </thead>
       <tbody>
       
     <?php
	$getDetails = $objManageData->getArrayRecords('manufacturer_lens_brand','' ,'' ,'name','ASC');
	if(count($getDetails)>0){
		foreach($getDetails as $key => $detailsPreDefine){
			$preDefineDesc = $detailsPreDefine->name;
			$preDefineCatId = $detailsPreDefine->catId;
			//CATEGORY ID DESC
				$getCategoryIDDetails = $objManageData->getRowRecord('manufacturer_lens_category', 'manufacturerLensCategoryId', $preDefineCatId,'name','ASC');
				$catDesc = $getCategoryIDDetails->name;
			//CATEGORY ID DESC
			$preDefineID = $detailsPreDefine->manufacturerLensCategoryId;						
			?>
			<input type="hidden" name="lensBrandId[]" value="<?php echo $detailsPreDefine->lensBrandId; ?>">
			<tr>
				<td align="left"><input type="checkbox" name="chkBox[]" value="<?php echo $detailsPreDefine->lensBrandId; ?>"></td>							
				<td align="left">
					<select name="manufacturerLensCategoryList[]" class="form-control selectpicker">
						<option></option>
						<?php
						$getCategoryDetails = $objManageData->getArrayRecords('manufacturer_lens_category','' ,'' ,'name','ASC');
						foreach($getCategoryDetails as $cat_desc){
							?>
							<option value="<?php echo $cat_desc->manufacturerLensCategoryId; ?>" <?php if($preDefineCatId==$cat_desc->manufacturerLensCategoryId) echo "SELECTED"; ?>><?php echo $cat_desc->name; ?></option>
							<?php
						}
						?>
					</select>
				</td>
				<td align="left"><input type="text" class="form-control" name="preDefineDesc[]" value="<?php echo stripslashes($preDefineDesc); ?>"></td>
			</tr>
			<?php
			}
		}
		?>
		</tbody>
        </table>
     </div>
     </div>           
            
        <?php
        for($j=0;$j<1;$j++){
            ?>
              <div class="modal fade" id="manufacturerLensBrandTr">
             <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title rob">Manufacturer Lens Brand</h4>  
                    </div>
                    <div class="modal-body">
                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-condensed cf  table-striped">
                            <tr>
                             <td align="center" id="manufacturerLensCatTd">
                             <select name="manufacturerLensCategoryList[]" onChange="return newManufacturerLensCat(this);" class="form-control selectpicker">
                                <option></option>
                                <?php
                                $getCategoryDetails = $objManageData->getArrayRecords('manufacturer_lens_category','' ,'' ,'name','ASC');
                                foreach($getCategoryDetails as $cat_desc){
                                    ?>
                                    <option value="<?php echo $cat_desc->manufacturerLensCategoryId; ?>"><?php echo $cat_desc->name; ?></option>
                                    <?php
                                }
                                ?>
                                </select></td>
                                <td align="center"><input type="text" class="form-control" value="" name="preDefineDesc[]"></td>
                                
                            </tr>
                            <tr>
                                <td >Manufacturer</td>
                                <td >Lens Brand</td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                    <div id="div_innr_btn">
                        
                        <a href="javascript:void(0)" class="btn btn-info " id="saveButtonSub" style="display: inline-block;" onclick="return top.frames[0].getPageSrc('Save');"><b class="fa fa-save"></b> Save</a>
                        <a href="javascript:void(0)" class="btn btn-danger " id="cancelButton" style="display: inline-block;" data-dismiss="modal"><b class="fa fa-times"></b> Close</a>
                         
                    </div>
                    </div>
                 
                </div>
             </div>
            </div>
                  <script>
                  function closeModal()
                  {
                  top.frames[0].frames[0].frames[0].$('#manufacturerLensBrandTr').modal({
                            show: false,
                            backdrop: true,
                            keyboard: true
                            });
                  }	</script>	
            <?php
        }
        ?>
    
<!-- Manufacturer Lens Brand -->
						
		</form>
		<?php
	}
	else if($content=='Zip Codes'){
		$arrPage = range('A','Z');	
		$imgsrc = array_search($text,$arrPage);
		if($imgsrc){
			$src = $imgsrc + 1;
		}
		else{
			$src = 1;
		}			
		for($i=1;$i<=count($arrPage);$i++){
			$key = $i;
			$val = $arrPage[$i-1];
			$pageClass="";
			if($val==$name) {
				$pageClass = "activealpha";	
			}
			if($key == $src){
				$alphaPaging .= '<a href="predefineFrmForm.php?text='.$val.'&contentOf='.$content.'&table='.$table.'&idField='.$idField.'" class="'.$pageClass.'" >'.$val.'</a>';
			}
			else{
				$alphaPaging .= '<a href="predefineFrmForm.php?text='.$val.'&contentOf='.$content.'&table='.$table.'&idField='.$idField.'" class="'.$pageClass.'" >'.$val.'</a>';
			}
		}
		?>
		<form name="perDefineZipCodeFrm" action="predefineFrmForm.php" method="post" autocomplete="off">
		<input type="hidden" name="contentOf" value="<?php echo $content; ?>">
		<input type="hidden" name="table" value="<?php echo $table; ?>">
		<input type="hidden" name="idField" value="<?php echo $idField; ?>">
		<input type="hidden" name="deleteSelected" value="">
        <input type="hidden" name="text" value="<?php echo $name; ?>">
        <input type="hidden" name="page" value="<?php echo $pageNumber+1; ?>">

        <div class="my_table_Checkall adj_tp_table" style="padding:0px; margin:0px">
          <div class="scheduler_margins_head" style="float:left; display:inline-block; width:100%">
            <div class="valignTop alignCenter pagenation_alpha">
                <?php print $alphaPaging;?>
            </div>
			<div class="full_width">
                <div class="clearfix"></div>           
                 <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 text-center">
                    <tr>
                        <?=$pageLinks?>
                    </tr>
                 </table>
                <div class="clearfix"></div>
                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-condensed cf  table-striped">
                    <thead>
                    <tr>
                        <th style="text-align:left; width:3%"></th>
                        <th style="text-align:left; width:24%">Zip Code</th>
                        <th style="text-align:left; width:24%">City</th>
                        <th style="text-align:left; width:24%">State</th>
                        <th style="text-align:left; width:25%">State Abbreviation</th>
                    </tr>
                    </thead>
                </table>
            </div>    
                
            </div>
             <div class="scheduler_table_Complete" id="local_scrollable_div">
                <table class="table table-bordered  table-condensed cf  table-striped">
                <tbody>
                <?php
                //$getDetails = $objManageData->getArrayRecords('zip_codes','' ,'' ,'zip_id','ASC');
                $getDetails=array();
                $qryStr = "SELECT * FROM zip_codes WHERE state_abb LIKE '$name%' ORDER BY state_abb ASC, zip_code ASC LIMIT $startLimit,$limit";
                $qryQry = imw_query($qryStr);
                if($qryQry){
                    while($qryRow = imw_fetch_object($qryQry)){
                        $getDetails[] = $qryRow;
                    }	
                }
                
                if(count($getDetails)>0){
                	foreach($getDetails as $key => $detailsPreDefine){
                        $preDefineZipCode = $detailsPreDefine->zip_code;
                        $preDefineZipCity = $detailsPreDefine->city;	
                        $preDefineZipState = $detailsPreDefine->state;
                        $preDefineZipStateAbbre = $detailsPreDefine->state_abb;	
               					?>
                        <input type="hidden" name="zipId[]" value="<?php echo $detailsPreDefine->zip_id; ?>">
                        <tr>
                            <td align="center"><input type="checkbox" name="chkBox[]" value="<?php echo $detailsPreDefine->zip_id; ?>"></td>							
                            <td align="center"><input type="text" class="form-control" name="preDefineZipCode[]" value="<?php echo stripslashes($preDefineZipCode); ?>"></td>
                            <td align="center"><input type="text" class="form-control" name="preDefineZipCity[]" value="<?php echo stripslashes($preDefineZipCity); ?>"></td>
                            <td align="center"><input type="text" class="form-control" name="preDefineZipState[]" value="<?php echo stripslashes($preDefineZipState); ?>"></td>
                            <td align="center"><input type="text" class="form-control" name="preDefineZipStateAbbre[]" value="<?php echo stripslashes($preDefineZipStateAbbre); ?>"></td>
                        </tr>
                        <?php
                    }
                }else{
                ?>
                  <tr>
                  	<td colspan="5" class="text-center" >No Record Found</td>
                	</tr>
                <?php		
                }
               	?>
              </tbody>
            	</table>
            </div>
                    
            
            </div>
						
						
						<?php for($j=0;$j<1;$j++){ ?>
						<div class="modal fade" id="zipCodeTr">
            	<div class="modal-dialog modal-lg">
              	<div class="modal-content">
                	<div class="modal-header text-center">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title rob">Add Zip Code</h4>  
                 	</div>
                  <div class="modal-body">
                  	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-condensed cf  table-striped">
							
										<tr>
											<td ><input type="text" class="form-control" name="preDefineZipCode[]" id="preDefineZipCodeId" size="30"></td>
											<td ><input type="text" class="form-control" name="preDefineZipCity[]" size="30"></td>
											<td ><input type="text" class="form-control" name="preDefineZipState[]" size="30"></td>
											<td ><input type="text" class="form-control" name="preDefineZipStateAbbre[]" size="30"></td>
											
										</tr>
										<tr>
											<td >Zip Code</td>
											<td >City</td>
											<td >State</td>
											<td >State Abbreviation</td>
										</tr>
										
								
							
					</table>
									</div>
									
									<div class="modal-footer">
                  	<div id="div_innr_btn">
                    	<a href="javascript:void(0)" class="btn btn-info " id="saveButtonSub" style="display: inline-block;" onclick="return top.frames[0].getPageSrc('Save');"><b class="fa fa-save"></b> Save</a>
                      <a href="javascript:void(0)" class="btn btn-danger " id="cancelButton" style="display: inline-block;" data-dismiss="modal"><b class="fa fa-times"></b> Close</a>
                  	</div>
                	</div>
              	      
							 	</div>
							</div>			 
						</div>
						<?php
						}
						?>
							 															 														
				</td>
				<!-- Zip Codes -->
			</tr>
		</table>			
		</form>
		<?php
	}
	else if($content=='Chart Unlock'){
		?>
		<form name="perDefineChartUnlockFrm" action="predefineFrmForm.php" method="post" autocomplete="off">
		<input type="hidden" name="contentOf" value="<?php echo $content; ?>">
		<input type="hidden" name="table" value="<?php echo $table; ?>">
		<input type="hidden" name="idField" value="<?php echo $idField; ?>">
		<input type="hidden" name="deleteSelected" value="">
		<table border="0" cellpadding="0" cellspacing="0" width="99%">
			<tr>
				<!-- Zip Codes -->
				<td>
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						
						<tr>
							<td colspan="4" align="left">
								<div style="position:relative;height:380px;border:2px;overflow:auto;">
								<table border="0" cellpadding="0" cellspacing="0" style="width:95%;">
									
									<?php
									$getDetails=array();
									$qryStr = "SELECT cp.id, cp.form_name as chartFormName, DATE_FORMAT(cp.action_date_time,'%m-%d-%Y (%h:%i %p)') as chartLockedDate,
												TRIM(CONCAT(pdt.patient_lname,', ',pdt.patient_fname,' ',pdt.patient_mname)) as chartPatientName, 
												pc.dos as chartDOS,pc.ascId as chartASCID,
												TRIM(CONCAT(usr.lname,', ',usr.fname,' ',usr.mname)) as chartLockedBy,
												usr.user_type AS chartOperatorType
												FROM chart_pt_lock_tbl cp
												LEFT JOIN users usr ON (usr.usersId=cp.user_id)
												LEFT JOIN patientconfirmation pc ON (pc.patientConfirmationId=cp.confirmation_id)
												LEFT JOIN patient_data_tbl pdt ON (pdt.patient_id=pc.patientId)
												ORDER BY cp.action_date_time DESC";
									$qryQry = imw_query($qryStr);
									if($qryQry){
										while($qryRow = imw_fetch_object($qryQry)){
											$getDetails[] = $qryRow;
										}	
									}
									
									if(count($getDetails)>0){
									?>
                                    <tr>
										<td class="text_10b nowrap valignTop" style="width:80px;"></td>
                                        <td class="text_10b nowrap valignTop" style="width:300px;">Patient Name</td>
                                        <td class="text_10b nowrap valignTop" style="width:400px;">Chart Name</td>
                                        <td class="text_10b nowrap valignTop" style="width:200px;">Chart DOS</td>
										<td class="text_10b nowrap valignTop" style="width:200px;">ASCID</td>
                                        <td class="text_10b nowrap valignTop" style="width:150px;">Locked On</td>
                                        <td class="text_10b nowrap valignTop" style="width:200px;">Locked By</td>
                                        
										
									</tr>
                                    <?php	
										$r=0;
										foreach($getDetails as $key => $detailsPreDefine){
											$r++;
											if(($r%2)==0) {
												$unlockBgColor = '#FFFFFF;';	
											}
											$preDefinePatientName 	= $detailsPreDefine->chartPatientName;
											$preDefineFormName 		= $detailsPreDefine->chartFormName;	
											$preDefineDOS 			= $detailsPreDefine->chartDOS;
											$preDefineASCID 		= $detailsPreDefine->chartASCID;
											$preDefineLockedDate 	= $detailsPreDefine->chartLockedDate;
											$preDefineLockedBy 		= $detailsPreDefine->chartLockedBy;
											$preDefineOperatorType 	= $detailsPreDefine->chartOperatorType;
											$preDefineFormNameShow 	= $preDefineFormName;
											if($preDefineFormName=='mac_regional_anesthesia_form') {
												$preDefineFormNameShow = 'MAC/Local/Regional Anesthesia Record';
											}
											
											?>
											<tr style=" height:30px; <?php if(($r%2)!=0) echo "background-color:#FFFFFF;";?>">
												<td class="text_10 valignTop alignLeft" style="padding-left:10px;"><input type="checkbox" name="chkBox[]" value="<?php echo $detailsPreDefine->id; ?>"></td>							
												<td class="text_10 valignTop alignLeft"><?php echo $preDefinePatientName; ?></td>
                                                <td class="text_10 valignTop alignLeft"><?php echo $preDefineFormNameShow; ?></td>
                                                <td class="text_10 valignTop alignLeft"><?php echo $preDefineDOS; ?></td>
                                                <td class="text_10 valignTop alignLeft"><?php echo $preDefineASCID; ?></td>
                                                <td class="text_10 valignTop alignLeft"><?php echo $preDefineLockedDate; ?></td>
                                                <td class="text_10 valignTop alignLeft"><?php echo $preDefineLockedBy.'('.$preDefineOperatorType.')'; ?></td>
											</tr>
											<?php
										}
									}else{
									?>
                                    		<tr>
                                                <td></td>
                                                <td colspan="4" class="text_10b alignCenter" style="padding-top:10px; padding-left:60px;">No Record Found</td>
                                            </tr>
                                    <?php		
									}
										?>
								</table>
								</div>
                                <div class="alignCenter">
                                    <table class="table_collapse">
                                        <tr>
                                            <?=$pageLinks?>
                                        </tr>
                                    </table>
                                    <div class="valignTop alignCenter pagenation_alpha">
                                        <?php print $alphaPaging;?>
                                    </div>
                                </div>
							</td>
						</tr>
						<tr height="10">
							<td colspan="4"></td>
						</tr>
						<?php
						for($j=0;$j<1;$j++){
							?>
							<tr >
								<td id="zipTr" style="display:none;">
									<table border="0" cellpadding="0" cellspacing="0">
										<tr>
								
											<td></td>
											
											<td align="center"><input type="text" class="text_10" name="preDefineZipCode[]" id="preDefineZipCodeId" size="30"></td>
											<td align="center"><input type="text" class="text_10" name="preDefineZipCity[]" size="30"></td>
                                            <td align="center"><input type="text" class="text_10" name="preDefineZipState[]" size="30"></td>
                                            <td align="center"><input type="text" class="text_10" name="preDefineZipStateAbbre[]" size="30"></td>
											
										</tr>
										<tr align="left" style="font-size:9px; font-style:italic;vertical-align:top; padding-left:8px;" class="text_10">
											<td>&nbsp;&nbsp;&nbsp;</td>
											<td>Zip Code</td>
											<td>City</td>
                                            <td>State</td>
                                            <td>State Abbreviation</td>
										</tr>
									</table>
								</td>
							</tr>			
								
							<?php
						}
						?>
					</table>
				</td>
				<!-- Zip Codes -->
			</tr>
		</table>			
		</form>
		<?php
	
		
	}
	
	else if($content=='Modifiers'){
		?>
	<form name="perDefineModifierFrm" action="predefineFrmForm.php" method="post" autocomplete="off">
		<input type="hidden" name="contentOf" value="<?php echo $content; ?>">
		<input type="hidden" name="table" value="<?php echo $table; ?>">
		<input type="hidden" name="idField" value="<?php echo $idField; ?>">
		<input type="hidden" name="deleteSelected" value="">
		
         <div class="scheduler_table_Complete">
         	<div class="my_table_Checkall adj_tp_table" style="padding:0px; margin:0px">
            	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                	<thead>
                    	<tr>
                    		<th style="width:2%">&nbsp;</th>
                            <th style="width:2%">&nbsp;</th>
                            <th style="width:32%" align="left" class="">Modifier Code</th>
                            <th style="width:32%" align="left" class="">Practice Code</th>
                            <th style="width:32%" align="left" class="">Description</th>
                    	</tr>
       </thead>
       <tbody>
       <?php
	   	$getDetails = $objManageData->getArrayRecords('modifiers','' ,'' ,'modifierCode','ASC');
		$counter = 0;
		if(count($getDetails)>0)
		{
			foreach($getDetails as $key => $detailsPreDefine)
			{ $counter++;

				$modifierId		=	$detailsPreDefine->modifierId;
				$modifierCode	=	$detailsPreDefine->modifierCode;
				$practiceCode	=	$detailsPreDefine->practiceCode;
				$description	=	$detailsPreDefine->description;
				$deleted		=	$detailsPreDefine->deleted;
				
		?>
        		<input type="hidden" name="modifierId[]" value="<?php echo $modifierId; ?>" />
                <tr>
                	<td class="<?=($deleted == 1 ? 'inactive-record' : 'active-record')?>" data-record-id = "<?=$modifierId?>" data-table-name="<?=$table?>" data-unique-field="modifierId" >&nbsp;</td>
                    <td align="left">
                    	<input type="checkbox" name="chkBox[]" value="<?php echo $modifierId; ?>">
                   	</td>
                    <td align="left">
                    	<input type="text" class="form-control typeHead" name="modifierCode[]" id="modifier<?=$counter?>" data-row-index="<?=$counter?>" value="<?php echo stripslashes($modifierCode); ?>" />
                    </td>
                    <td align="left">
                    	<input type="text" class="form-control" name="practiceCode[]" id="practice<?=$counter?>" data-row-index="<?=$counter?>" value="<?php echo stripslashes($practiceCode); ?>" />
                    </td>
                    <td align="left">
                    	<input type="text" class="form-control" name="description[]" id="description<?=$counter?>" data-row-index="<?=$counter?>" value="<?php echo stripslashes($description); ?>" />
                  	</td>
			</tr>
			<?php
			}
		}
		?>
		</tbody>
        </table>
     </div>
     </div>           
            
        <?php
        for($j=0;$j<1;$j++){
			$counter++;
            ?>
              <div class="modal fade" id="modifierTr">
             <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title rob">Add Modifiers</h4>  
                    </div>
                    <div class="modal-body">
                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-condensed cf  table-striped">
                            <tr>
                             	<td align="left">
                                	<input type="text" class="form-control typeHead" name="modifierCode[]" id="modifier<?=$counter?>" data-row-index="<?=$counter?>"  />
                              	</td>
                                <td align="left">
                                	<input type="text" class="form-control" name="practiceCode[]" id="practice<?=$counter?>" data-row-index="<?=$counter?>" />
                               	</td>
                                <td align="left">
                                	<input type="text" class="form-control" name="description[]" id="description<?=$counter?>" data-row-index="<?=$counter?>"  />
                               	</td>
                         	</tr>
                            <tr>
                                <td >Modifier Code</td>
                                <td >Practice Code</td>
                                <td >Description</td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                    <div id="div_innr_btn">
                        
                        <a href="javascript:void(0)" class="btn btn-info " id="saveButtonSub" style="display: inline-block;" onclick="return top.frames[0].getPageSrc('Save');"><b class="fa fa-save"></b> Save</a>
                        <a href="javascript:void(0)" class="btn btn-danger " id="cancelButton" style="display: inline-block;" data-dismiss="modal"><b class="fa fa-times"></b> Close</a>
                         
                    </div>
                    </div>
                 
                </div>
             </div>
            </div>
           		<script>
                  function closeModal()
                  {
                  top.frames[0].frames[0].frames[0].$('#modifierTr').modal({
                            show: false,
                            backdrop: true,
                            keyboard: true
                            });
                  }	</script>	
            <?php
        }
        ?>
    
		<div id="preDefineModifiers" style="z-index:9999" class="col-md-4 col-lg-3 col-xs-4 col-sm-4 preDefinePopUp padding_0 active">
        	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Modifiers<span onClick="document.getElementById('preDefineModifiers').style.display='none';" style="float:right; color:#FFF; cursor:pointer; list-style:none; ">X</span></div>
            <ul class="col-md-12 col-lg-12 col-xs-12 col-sm-12" id="HtmlContainer" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"></ul>
      	</div>
						
		</form>
<?php
	}
	else if($content=='Pre-Op Nurse'){
		?>
		<form name="perDefinePreOpNurseFrm" action="predefineFrmForm.php" method="post" autocomplete="off">
		<input type="hidden" name="contentOf" value="<?php echo $content; ?>">
		<input type="hidden" name="table" value="<?php echo $table; ?>">
		<input type="hidden" name="idField" value="<?php echo $idField; ?>">
		<input type="hidden" name="deleteSelected" value="">
		
         <div class="scheduler_table_Complete">
         	<div class="my_table_Checkall adj_tp_table" style="padding:0px; margin:0px">
            	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                	<thead>
                    	<tr>
                        	<tr>
                            	<th class="text-left" style="width:7%"><input type="checkbox"  id="checkall" onClick="return checkAllFn(this);">Delete </th>
                                <th class="text-left" style="width:10%; white-space:nowrap;"><input type="checkbox"  id="checkall_txtbox" onClick="return checkAllTxtBox(this);">Show Input Box </th>
                                <th class="text-left" style="width:34%">Question Category</th>
                                <th class="text-left">Question</th>
                      		</tr>
                     	</tr>
                 	</thead>
           			<tbody>
                    <?php
					$getDetails = $objManageData->getArrayRecords('preopnursequestion','','','preOpNurseQuestionName','ASC');
					if(count($getDetails)>0)
					{
						foreach($getDetails as $preNurseQuestion)
						{
							$preOpNurseQuestionId = $preNurseQuestion->preOpNurseQuestionId;
							$preOpNurseQuestionName = stripslashes($preNurseQuestion->preOpNurseQuestionName);
							$preOpNurseCatId = $preNurseQuestion->preOpNurseCatId;
							$showTxtBoxStatus = $preNurseQuestion->showTxtBoxStatus;
					?>
                    		<tr>
                            	<td class="text-left" style="width:7%">
                                	<input type="checkbox" name="chkBox[]" value="<?php echo $preOpNurseQuestionId; ?>">
                                	<input type="hidden" name="nurseQuestionId[]" value="<?php echo $preOpNurseQuestionId; ?>">
                               	</td>
                                <td class="text-left" style="width:10%">
                                	<input type="checkbox" name="showTxtBoxStatus[<?php echo $preOpNurseQuestionId; ?>]" id="showTxtBoxStatus<?php echo $preOpNurseQuestionId; ?>" value="1" <?php if($showTxtBoxStatus=='1') { echo "checked"; } ?>>
                               	</td>
                                <td class="text-left low_width_t" style="width:34%"> 
                                	<select class="selectpicker" name="preOpNurseCategoryList[]">
                                    	<?php
											$getCategoryDetails = $objManageData->getArrayRecords('preopnursecategory','' ,'' ,'categoryName','ASC');
											foreach($getCategoryDetails as $cat_desc)
											{
										?>
                                        		<option value="<?php echo $cat_desc->categoryId; ?>" <?php if($preOpNurseCatId==$cat_desc->categoryId) echo "SELECTED"; ?>><?php echo $cat_desc->categoryName; ?></option>
                                       	<?php
											}
										?>
                                  	</select> 
                             	</td>
                                <td class="text-left medium_width_t">
                                	<input type="text" class="form-control" name="preOpNurseQuestionName[]" value="<?php echo $preOpNurseQuestionName; ?>">
                              	</td>
                         	</tr>
                	<?php
						}
					}
					
					?>
					</tbody>
        		</table>
     		</div>
     	</div>           
            
        <?php
        for($j=0;$j<1;$j++){
            ?>
              <div class="modal fade " id="preOpNurseTr">
     <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title rob">ADD NEW  </h4>  
            </div>
            <div class="modal-body">
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                    <div class="form_inner_m">
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label for="ps" class="text-left"> 
                                     Question Category
                                </label>
                            </div>
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                 <select name="preOpNurseCategoryList[]" class="selectpicker" >
										<?php
										$getCategoryDetails = $objManageData->getArrayRecords('preopnursecategory','' ,'' ,'categoryName','ASC');
										foreach($getCategoryDetails as $cat_desc){
											?>
											<option value="<?php echo $cat_desc->categoryId; ?>" <?php if($preOpNurseCatId==$cat_desc->categoryId) echo "SELECTED"; ?>><?php echo $cat_desc->categoryName; ?></option>
											<?php
										}
										?>
									</select>
                            </div>
                        </div>
                    </div>
                    </div>
               		 <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form_inner_m">
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label for="ps" class="text-left"> 
                                     Question 
                                </label>
                            </div>
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                 <input name="preOpNurseQuestionName[]" type="text" class="form-control" >
                            </div>
                        </div>
                    </div>
                    </div>
               		 <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <div class="form_inner_m">
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label for="ps" class="text-left"> 
                                     Show Input Box  
                                </label>
                            </div>
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                 <input type="checkbox" name="showTxtBoxStatus[0]" value="1" >
                            </div>
                        </div>
                    </div>
                    </div>
                    
                    <div class="clearfix"></Div>
                                     
                    
                    
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" href="javascript:void(0);" onClick="top.frames[0].document.getElementById('saveButton').click();">  <b class="fa fa-save"></b>  Save </a>
                <a class="btn btn-danger" href="javascript:void(0)" onClick="top.frames[0].document.getElementById('cancelButton').click();" data-dismiss="modal"><b class="fa fa-times"></b>	Cancel  </a>
            </div>
         
        </div>
     </div>
    </div>
                  <script>
                  function closeModal()
                  {
                  top.frames[0].frames[0].frames[0].$('#preOpNurseTr').modal({
                            show: false,
                            backdrop: true,
                            keyboard: true
                            });
                  }	</script>	
            <?php
        }
        ?>
    
<!-- Pre-Op Nurse -->
						
		</form>
		<?php
	}
	else if($content=='Procedures Group'){
		?>
		<form name="perDefineProcedureGroup" action="predefineFrmForm.php" method="post" autocomplete="off">
		<input type="hidden" name="contentOf" value="<?php echo $content; ?>">
		<input type="hidden" name="table" value="<?php echo $table; ?>">
		<input type="hidden" name="idField" value="<?php echo $idField; ?>">
		<input type="hidden" name="deleteSelected" value="">
		
         <div class="scheduler_table_Complete">
         	<div class="my_table_Checkall adj_tp_table" style="padding:0px; margin:0px">
            	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                	<thead>
                    	<tr>
                        	<tr>
                            	<th class="text-center" style="width:2%"><input type="checkbox"  id="checkall" onClick="return checkAllFn(this);"> </th>
                                <th class="text-left" style="width:49%">Procedures</th>
                                <th class="text-left">Group Name</th>
                      		</tr>
                     	</tr>
                 	</thead>
           			<tbody>
                    <?php
					unset($condArr);
					$condArr['del_status'] = '';
		$table = 'proceduregroup';
		$idField = 'proceduresGroupId';
		$getProcedureDetails = $objManageData->getMultiChkArrayRecords('procedures',$condArr ,'name','ASC'," AND del_status !='yes' ");
		$getDetails = $objManageData->getMultiChkArrayRecords("proceduregroup", $condArr,"name","ASC"," AND del_status !='yes' ");
					if(count($getDetails)>0)
					{
						foreach($getDetails as $key=>$procedureGroup)
						{
							$proceduresGroupId = $procedureGroup->proceduresGroupId;
							$proceduresGroupName = stripslashes($procedureGroup->preOpNurseQuestionName);
							$proceduresId = explode(',',$procedureGroup->procedures);
					?>
                    		<tr>
                            	<td class="text-center" style="width:2%">
                                	<input type="checkbox" name="chkBox[]" value="<?php echo $proceduresGroupId; ?>">
                                	<input type="hidden" name="proceduresGroupId[]" value="<?php echo $proceduresGroupId; ?>">
                               	</td>
                                <td class="text-left low_width_t" style="width:49%"> 
                                	<select class="selectpicker" name="procedureGroupList_<?php echo $proceduresGroupId; ?>[]" multiple>
                                    	<option value="">Please Select</option>
                                    	<?php
											
											foreach($getProcedureDetails as $pro_desc)
											{
										?>
                                        		<option value="<?php echo $pro_desc->procedureId; ?>" <?php if(in_array($pro_desc->procedureId,$proceduresId)) echo "SELECTED"; ?>><?php echo (strlen($pro_desc->name)>110)?substr($pro_desc->name,0,110).'..':$pro_desc->name; ?></option>
                                       	<?php
											}
										?>
                                  	</select> 
                             	</td>
                                <td class="text-left medium_width_t">
                                	<input type="text" class="form-control" name="procedureGroupName_<?php echo $proceduresGroupId; ?>[]" value="<?php echo stripcslashes($procedureGroup->name); ?>">
                              	</td>
                         	</tr>
                	<?php
						}
					}
					
					?>
					</tbody>
        		</table>
     		</div>
     	</div>           
            
        <?php
        for($j=0;$j<1;$j++){
            ?>
              <div class="modal fade" id="proGroupTr">
     <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title rob">ADD NEW  </h4>  
            </div>
            <div class="modal-body">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="form_inner_m">
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label for="ps" class="text-left"> 
                                     Procedures
                                </label>
                            </div>
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                 <select class="selectpicker" name="procedureGroupList_0[]" multiple>
                                    	<option value="">Please Select</option>
                                    	<?php
											
											foreach($getProcedureDetails as $pro_desc)
											{
										?>
                                        		<option value="<?php echo $pro_desc->procedureId; ?>" ><?php echo (strlen($pro_desc->name)>110)?substr($pro_desc->name,0,110).'..':$pro_desc->name; ?></option>
                                       	<?php
											}
										?>
                                  	</select>
                            </div>
                        </div>
                    </div>
                    </div>
               		 <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="form_inner_m">
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label for="ps" class="text-left"> 
                                     Group Name 
                                </label>
                            </div>
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                 <input name="procedureGroupName_0[]" type="text" class="form-control" >
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="clearfix"></Div>
                                     
                    
                    
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" href="javascript:void(0);" onClick="top.frames[0].document.getElementById('saveButton').click();">  <b class="fa fa-save"></b>  Save </a>
                <a class="btn btn-danger" href="javascript:void(0)" onClick="top.frames[0].document.getElementById('cancelButton').click();" data-dismiss="modal"><b class="fa fa-times"></b>	Cancel  </a>
            </div>
         
        </div>
     </div>
    </div>
                  <script>
                  function closeModal()
                  {
                  top.frames[0].frames[0].frames[0].$('#proGroupTr').modal({
                            show: false,
                            backdrop: true,
                            keyboard: true
                            });
                  }	</script>	
            <?php
        }
        ?>
    
<!-- procedure group -->
						
		</form>
		<?php
	}
	else if( $traitObj ) {
		$data['content'] = $content;
		$data['table'] = $table;
		$data['idField'] = $idField;
		
		echo $traitObj->showAdminHtml($data);
	}

}

?>
</body>
</html>
