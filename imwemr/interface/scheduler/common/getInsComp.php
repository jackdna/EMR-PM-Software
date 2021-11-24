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

include_once('../../../config/globals.php');

include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php'); 

$objManageData = new CLSCommonFunction;


function getReffPhysicianDetails($refPhysicianId='', $read_from_database=0){
	$refPhysicianIdArr = array();
	$refPhyDataArr = array();
	if(trim($refPhysicianId) != ''){
		$refPhysicianIdArr = preg_split('/,/',$refPhysicianId);
		$refPhysicianIdArr=array_combine($refPhysicianIdArr,$refPhysicianIdArr);
	}

	$xml_file_name = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/xml/Referring_Physicians.xml";
	if(empty($xml_file_name) === true || !file_exists($xml_file_name) || $read_from_database==1){
		$qry = "select physician_Reffer_id as refphyId, Title as refPhyTitle,
					FirstName as refphyFName,LastName as refphyLName, MiddleName as refPhyMname,physician_fax,physician_fax as refFax,Address1 as refPhyAdd1,Address2 as refPhyAdd2,City as refPhyCity,State as refPhyState,ZipCode as refPhyZip from refferphysician";
		if($refPhysicianId != ''){
			$qry .= " where physician_Reffer_id = '$refPhysicianId'";
		}
		$sql_qry = imw_query($qry);
		$refPhyDataArr = imw_fetch_array($sql_qry);
	}
	else{
		$fileContent = file_get_contents($xml_file_name);
		//file_put_contents('a.html',$fileContent,FILE_APPEND);
		$insFileData = new SimpleXMLElement($fileContent);
		//print'<pre>';print_r($insFileData);
		foreach($insFileData->refPhyInfo as $refPhysicianDataObj){
			$refPhysicianArr = (array)$refPhysicianDataObj;
			$refPhysicianDataArr = $refPhysicianArr['@attributes'];
			if(count($refPhysicianIdArr) > 0){					
				if(in_array($refPhysicianDataArr['refphyId'],$refPhysicianIdArr) === true){
					$refPhyDataArr[] = $refPhysicianDataArr; 
				}
			}else{
				$refPhyDataArr[] = $refPhysicianDataArr; 
			}
		}
	}
	return $refPhyDataArr;
}


$patient_id = $_REQUEST['pid'];
$ins_case_type_arr = preg_split('/-/',$_REQUEST['ins']);
$normal_case = $ins_case_type_arr[1];
$vision_case = $ins_case_type_arr[2];
$ins_case_id = $ins_case_type_arr[3];

//--- REFERRING PHYSICIAN DETAILS ----
$refPhysicianDetails = getReffPhysicianDetails();
$refPhyDataArr = array();
for($i=0;$i<count($refPhysicianDetails);$i++){
	$refphyId = $refPhysicianDetails[$i]['refphyId'];
	$refPhyNameArr = array();
	$refPhyNameArr['FIRST_NAME'] = addslashes($refPhysicianDetails[$i]['refphyFName']);
	$refPhyNameArr['MIDDLE_NAME'] = addslashes($refPhysicianDetails[$i]['refPhyMname']);
	$refPhyNameArr['LAST_NAME'] = addslashes($refPhysicianDetails[$i]['refphyLName']);
	
	$refPhyDataArr[$refphyId] = changeNameFormat($refPhyNameArr);
}
//--- GET ALL INSURANCE DATA DETAILS ---
$ins_com_query = imw_query("select insurance_data.id as insurance_dataId, insurance_data.group_number,insurance_data.copay,insurance_data.co_ins,
			date_format(insurance_data.effective_date, '%m-%d-%Y') as effective_date,
			date_format(insurance_data.expiration_date, '%m-%d-%Y') as expiration_date,insurance_data.policy_number, 
			insurance_data.provider,insurance_companies.in_house_code, insurance_companies.name, 
			insurance_data.actInsComp, insurance_data.type, insurance_data.referal_required,
			insurance_data.self_pay_provider,insurance_data.auth_required			
			from insurance_data left join insurance_companies
			on insurance_companies.id = insurance_data.provider 
			where insurance_data.ins_caseid = '$ins_case_id' and insurance_data.pid = '$patient_id'
			and insurance_data.actInsComp = '1' and insurance_data.type != 'tertiary'
			order by insurance_data.type");
while($insQryResArr = imw_fetch_array($ins_com_query)){
	$insQryRes[] = $insQryResArr;
};

$insCompArrData = array();$arrInsSwapData = array();
for($i=0;$i<count($insQryRes);$i++){
	$type = $insQryRes[$i]['type'];
	$in_house_code = $insQryRes[$i]['in_house_code'];
	if(trim($in_house_code) == ''){
		$in_house_code = substr($insQryRes[$i]['name'],0,20);
	}
	$insurance_dataId = $insQryRes[$i]['insurance_dataId'];
	$insDataArr = array();
	
	if(strtolower($type) == 'primary'){
		$arrInsSwapData[] = $ins_case_id.'||Primary||'.$insurance_dataId.'||'.$insQryRes[$i]['provider'].'||'.$insQryRes[$i]['name'];
	}
	elseif(strtolower($type) == 'secondary'){
		$arrInsSwapData[] = $ins_case_id.'||Secondary||'.$insurance_dataId.'||'.$insQryRes[$i]['provider'].'||'.$insQryRes[$i]['name'];
	}
	
	$insDataArr[] = $in_house_code;
	$insDataArr[] = $insQryRes[$i]['insurance_dataId'];
	$insDataArr[] = $insQryRes[$i]['group_number'];
	$insDataArr[] = $insQryRes[$i]['copay'];
	if($insQryRes[$i]['effective_date'] == '00-00-0000'){
		$insQryRes[$i]['effective_date'] = '';
	}
	if($insQryRes[$i]['expiration_date'] == '00-00-0000'){
		$insQryRes[$i]['expiration_date'] = '';
	}
	$insDataArr[] = $insQryRes[$i]['effective_date'];
	$insDataArr[] = $insQryRes[$i]['expiration_date'];
	$insDataArr[] = $insQryRes[$i]['policy_number'];
	$insDataArr[] = $insQryRes[$i]['provider'];
	$insDataArr[] = $insQryRes[$i]['referal_required'];
	$insDataArr[] = $insQryRes[$i]['self_pay_provider'];
	$insDataArr[] = $insQryRes[$i]['co_ins'];
	$insDataArr[] = $insQryRes[$i]['auth_required'];
	
	$insType = '1';
	if(strtolower($type) == 'secondary'){
		$insType = '2';
	}
	
	//--- VISION CASE AUTH DATA ----
	//if($insQryRes[$i]['auth_required'] == 'Yes' and $vision_case == 1){
		$authQryRes = array();
		$auth_query = imw_query("select auth_name,a_id,AuthAmount,ins_type, date_format(auth_date,'%m-%d-%Y') as auth_date,
				date_format(end_date,'%m-%d-%Y') as auth_end_date, no_of_reffs, reff_used
				from patient_auth where ins_type = '$insType' and patient_id = '$patient_id' 
				and auth_status = '0' and ins_case_id = '$ins_case_id' 
				order by a_id desc");
		while($authQryResArr = imw_fetch_array($auth_query)){
			$authQryRes[] = $authQryResArr;
		};
		
		$insDataArr[] = $authQryRes[0]['a_id'];
		$insDataArr[] = $authQryRes[0]['auth_name'];
		$insDataArr[] = $authQryRes[0]['AuthAmount'];
		if($authQryRes[0]['auth_date'] == '00-00-0000'){
			$authQryRes[0]['auth_date'] = '';
		}
		$insDataArr[] = $authQryRes[0]['auth_date'];
		
		if($authQryRes[0]['auth_end_date'] == '00-00-0000'){
			$authQryRes[0]['auth_end_date'] = '';
		}
		$insDataArr[] = $authQryRes[0]['auth_end_date'];
		
		// visits
		if($authQryRes[0]['no_of_reffs'] + $authQryRes[0]['reff_used'] == '0'){
			$auth_visit_value="";
		}
		else{
			if($authQryRes[0]['reff_used'] >0)
			$auth_visit_value	=	$authQryRes[0]['no_of_reffs'] .'/'.$authQryRes[0]['reff_used'];
			else
			$auth_visit_value	=	$authQryRes[0]['no_of_reffs'];
		}	
		$insDataArr[] = $auth_visit_value;	
	
	
		$authMenuArr = array();
		for($a=0;$a<count($authQryRes);$a++){
			$authMenuArr[] = array($authQryRes[$a]['auth_name'],"", $authQryRes[$a]['auth_name']);
		}
		
		$authDivName = 'pri_simple_menu';
		$authFldName = 'AuthPriNumber';
		if($insType == 2){
			$authDivName = 'sec_simple_menu';
			$authFldName = 'AuthSecNumber';	
		}
		$insDataArr[] = get_simple_menu($authMenuArr,$authDivName,$authFldName);
	//}
	
	//--- NORMAL CASE REFERRAL DATA ---
	if($insQryRes[$i]['referal_required'] == 'Yes' and $normal_case == 1){		
		$ref_query = imw_query("select reff_id ,reff_phy_id,reffral_no,reff_used,no_of_reffs,
					 date_format(patient_reff.effective_date, '%m-%d-%Y') as effective_date,
		 			 date_format(patient_reff.end_date, '%m-%d-%Y') as end_date 
							from 
					patient_reff
					where patient_id = '$patient_id' and ins_data_id = '$insurance_dataId'
					and reff_type = '$insType' order by reff_id desc limit 0,1");
		while($refQryResArr = imw_fetch_array($ref_query)){
			$refQryRes[] = $refQryResArr;
		};
		$reff_phy_id = $refQryRes[0]["reff_phy_id"];
		$totalVisists = $refQryRes[0]["no_of_reffs"] + $refQryRes[0]["reff_used"];
		$totalVisists = $refQryRes[0]["reff_used"]."/".$totalVisists;
		
		if($refQryRes[0]["reff_id"]){
			$insDataArr[] = $refQryRes[0]["reff_id"];
			$insDataArr[] = $reff_phy_id;
			$insDataArr[] = $refPhyDataArr[$reff_phy_id];
			$insDataArr[] = $totalVisists;
			$insDataArr[] = $refQryRes[0]["reffral_no"];
			$insDataArr[] = ($refQryRes[0]["effective_date"] <> '00-00-0000') ? $refQryRes[0]["effective_date"] : '';
			$insDataArr[] = ($refQryRes[0]["end_date"] <> '00-00-0000') ? $refQryRes[0]["end_date"] : '';
		}
	}
	
	$insCompArrData[$type] = join("||",$insDataArr);	
}

/*$insCompDataStr = $normal_case."||".$vision_case."~";
$insCompDataStr .= join("~",$insCompArrData);
$insCompDataStr .= "~".join("~",$arrInsSwapData);// Data for swap Modal*/
$insCompDataStr = $normal_case."||".$vision_case."~~";
$insCompDataStr .= join("|~|",$insCompArrData);
$insCompDataStr .= "~~".join("|~|",$arrInsSwapData);// Data for swap Modal
print $insCompDataStr;
?>