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

?><?php
/*
File: get_reports_emdeon.php
Purpose: Download billing reports from clearing house.
Access Type: Include file 
*/
set_time_limit(0);
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/../../library/classes/class.electronic_billing.php');
require_once(dirname(__FILE__).'/../../library/classes/billing_functions.php');
require_once(dirname(__FILE__).'/../../library/classes/SaveFile.php');
$objEBilling = new ElectronicBilling();
$oSaveFile = new SaveFile();

$ClearingHouse	= $objEBilling->ClearingHouse();
$CL_mode 		= $ClearingHouse[0]['connect_mode'];
$CL		 		= $ClearingHouse[0]['abbr'];
$CL_url			= ($CL_mode=='T') ? $ClearingHouse[0]['test_url'] : $ClearingHouse[0]['prod_url'];
$CL_url			.= 'ITS/post.aspx';
function getThisUnzipped($path){
	$zip_content_arr = array();
	$zip = @zip_open($path);
	while($zip_entry = @zip_read($zip)){
		$entryId = @zip_entry_open($zip,$zip_entry,'r');
		if($entryId){
			$data = @zip_entry_read($zip_entry,@zip_entry_filesize($zip_entry));
			$data = str_replace('<','*',$data);
			$data = str_replace('>','*',$data);
			$dataArr = array();
			$dataArr['ws_file_name'] = @zip_entry_name($zip_entry);
			
			$dataArr['report_data'] = addslashes($data);
			$dataArr['operator_id'] = $_SESSION['authId'];
			$dataArr['report_status'] = 0;
			$dataArr['Online_Url'] = addslashes($CL_url);
			$dataArr['wsUserID'] = $emdeonUserId;
			$dataArr['wsPassword'] = $EmdeonPassword;
			$dataArr['wsMessageType'] = $MessageType;
			$dataArr['wsGetFile'] = addslashes($path);
			$dataArr['report_recieve_date'] = date('Y-m-d H:i:s');
			if(trim($data) != ""){
				$zip_content_arr[] = $dataArr;
			}
		}
	}
	return $zip_content_arr;
	@zip_close($zip);	
}

if($CL_mode=='P'){
	$MessageType = 'MCD';
}else{
	$MessageType = 'MCT';
}

$basePath 	= $oSaveFile->upDir.'/BatchFiles';	/*Path to save report file downloaded*/
$sharePath 	= $basePath.'/others/get_report';	/*Report copy path for shared era*/
if(!is_dir($basePath) || !file_exists($basePath)){
	mkdir($basePath,0777,true);
}

//-- GET EMDEON USER NAME / PASSWORD ----
$q = "select user_id as EmdeonUserId ,user_pwd as EmdeonPassword, group_institution from groups_new where user_id != '' AND user_pwd != ''";
$groupQryRs = imw_query($q);
$MessageType1 = $MessageType;
while($groupQryRes = imw_fetch_assoc($groupQryRs)){	
	$emdeonUserId = trim($groupQryRes['EmdeonUserId']);
	$EmdeonPassword = trim($groupQryRes['EmdeonPassword']);
	$is_institutional = $groupQryRes['group_institution'];
	if($is_institutional=='1' && $MessageType=='MCD'){$MessageType='HCD';}else{$MessageType = $MessageType1;}
	if($is_institutional=='1' && $MessageType=='MCT'){$MessageType='HCT';}else if($MessageType!='HCD'){$MessageType = $MessageType1;}
	if($MessageType=='HCD'){
		$arr_MessageType = array($MessageType,'MCD');	
	}else{
		$arr_MessageType = array($MessageType);
	}
	foreach($arr_MessageType as $MessageType){
		
		$eraFileName = date('m_d_y_h_i_s').'_reports.zip';
		$path = $basePath.'/'.$eraFileName;
		
		/*Download List of era files*/
		$era_files_list = array();
		if(dl_era() && !in_array($emdeonUserId,array("MWI_RK8496C3","MWI_HP8C6583"))){
			$era_files_data = file_get_contents(ERA_URL.'/interface/billing/share_era_files.php?file_type=report&userId='.$emdeonUserId.'&query=list');
			$era_files_data = json_decode($era_files_data);
			if(isset($era_files_data->files) && isset($era_files_data->practice_path)){
				$era_files_list = $era_files_data->files;
				$dl_era_practice_path = $era_files_data->practice_path;
			}
		}
		else{
			$cur = curl_init();
			$loginfields = array();
			$loginfields['wsUserID'] 		= $emdeonUserId;
			$loginfields['wsPassword'] 		= $EmdeonPassword;
			$loginfields['wsMessageType'] 	= $MessageType;
			$loginfields['wsGetFile'] = $path;
			curl_setopt($cur,CURLOPT_URL,$CL_url);
			curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($cur, CURLOPT_POSTFIELDS, $loginfields);
			$output2 = curl_exec($cur);
			print curl_error($cur);
		}
		
		do{
			
			if(dl_era() && !in_array($emdeonUserId,array("MWI_RK8496C3","MWI_HP8C6583"))){
				$http_code = 0;
				$era_file_name = array_pop($era_files_list);
				if($era_file_name !== NULL && !file_exists($basePath.'/'.$era_file_name) && $dl_era_practice_path!=''){
					$path = $basePath.'/'.$era_file_name;
					$era_file_name = rawurlencode($era_file_name);
					$era_url = ERA_URL.'/data/'.$dl_era_practice_path.'/BatchFiles/others/get_report/'.$emdeonUserId.'/'.$era_file_name;
					$cur = curl_init();
					curl_setopt($cur,CURLOPT_URL, $era_url);
					curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
					$output2 = curl_exec($cur);
					$http_code = curl_getinfo($cur, CURLINFO_HTTP_CODE);
					print curl_error($cur);
					curl_close($cur);
					
					if($http_code!==200)
						$output2 = '[FILENOTFOUND]';
				}
				else
					$output2 = '[FILENOTFOUND]';
			}
			
			//--NOT LOGGING IF "FILENOTFOUND".
			if($output2 == '[FILENOTFOUND]') continue;
			
			//--- SAVE CURL OUTPUT --------
			$dataArr['report_data'] = base64_encode($output2);
			$dataArr['operator_id'] = $_SESSION['authId'];
			$dataArr['report_status'] = 2;
			$dataArr['Online_Url'] = addslashes($CL_url);
			$dataArr['wsUserID'] = $emdeonUserId;
			$dataArr['wsPassword'] = $EmdeonPassword;
			$dataArr['wsMessageType'] = $MessageType;
			$dataArr['wsGetFile'] = addslashes($path);
			$dataArr['report_recieve_date'] = date('Y-m-d H:i:s');
			$insertId = AddRecords($dataArr,'emdeon_reports');
			//--- ERROR CHECKS -------
			$fileError = $fileError2 = false; 
			$error = explode('[BatchGetFileNoFile]',$output2);
			if(count($error)== 2){
				$fileError = true;
			}
			if($fileError == false){
				$error = explode('[HttpsLoginFailure]',$output2);
				if(count($error)== 2) $fileError = true;
			}
			if($fileError == false){
				$error = explode('[209]',$output2);
				if(count($error)== 2) $fileError2 = true;
			}
			if($fileError == false){
				$error = explode('[4363]',$output2);
				if(count($error)== 2) $fileError2 = true;
			}
			if($fileError == false){
				$error = explode('[FILENOTFOUND]',$output2);
				if(count($error)== 2) $fileError2 = true;
			}
		
			//------------ OPEN ZIP FILES -----------
			if($fileError == false && $fileError2 == false)
			{
				$fileId = fopen($path,'w');
				$data3 = fputs($fileId,$output2);
				fclose($fileId);
				
				/*Save file to be used by external practice - era shared*/
				if(is_era_shared())
					copy_file($basePath, $sharePath.'/'.$emdeonUserId, $eraFileName);
				
				$ARR_Files_Downloaded = getThisUnzipped($path);
				foreach($ARR_Files_Downloaded as $dataArr){
					$dataArr['Online_Url'] = addslashes($CL_url);
					$dataArr['wsUserID'] = $emdeonUserId;
					$dataArr['wsPassword'] = $EmdeonPassword;
					$dataArr['wsMessageType'] = $MessageType;
					$dataArr['report_recieve_date'] = date('Y-m-d H:i:s');
					$insertId = AddRecords($dataArr,'emdeon_reports');
				}
			}
			else if($fileError2 == false){
				$dataArr['report_data'] = addslashes($output2);
				$dataArr['operator_id'] = $_SESSION['authId'];
				$dataArr['report_status'] = 1;
				$dataArr['Online_Url'] = addslashes($CL_url);
				$dataArr['wsUserID'] = $emdeonUserId;
				$dataArr['wsPassword'] = $EmdeonPassword;
				$dataArr['wsMessageType'] = $MessageType;
				$dataArr['wsGetFile'] = addslashes($path);
				$dataArr['HttpsLoginFailure'] = $error[1];
				$dataArr['report_recieve_date'] = date('Y-m-d H:i:s');
				$insertId = AddRecords($dataArr,'emdeon_reports');
			}
			
			/*Unlink File on remote server* /
			if( dl_era() && $era_file_name !== NULL && $http_code===200){
				file_get_contents(ERA_URL.'/interface/Billing/share_era_files.php?file_type=report&query=del&userId='.$emdeonUserId.'&file_name='.$era_file_name);
			}*/
		}while(count($era_files_list)>0);
	}
}
?>