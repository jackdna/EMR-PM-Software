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
require_once("../../../library/classes/common_function.php");

$iolinkUrl = $_REQUEST['iolinkUrl'];
$iolinkUrlUsername = $_REQUEST['iolinkUrlUsername'];
$iolinkUrlPassword = $_REQUEST['iolinkUrlPassword'];
$wishToDownLoad = $_REQUEST['wishToDownLoad'];
$newOrAll = $_REQUEST['newOrAll'];

$url = $iolinkUrl."?userName=$iolinkUrlUsername&password=$iolinkUrlPassword&downloadForm=yes&wishToDownLoad=$wishToDownLoad";
$cur = curl_init($url);
curl_setopt($cur,CURLOPT_URL,$url);
curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);  
$data = curl_exec($cur);
curl_close($cur);
if($data){	
	if($wishToDownLoad == 1){
		$xmlData = explode('~~~~~~~',$data);
		$xml = $xmlData[0];    
		$tag = 'consentform';
		$consentCategory = array();
		$consentCategory = XMLtoArray($xml,$tag);	
		
		$a = 0;
		while ($a < count($consentCategory)) {		
		
			$category_id = 			$consentCategory[$a]['categoryid'];
			$category_name =	 	$consentCategory[$a]['categoryname'];
			$category_status = 		$consentCategory[$a]['categorystatus'];
				
			$insertQuery = "insert into surgery_center_consent_category set 
							category_id = $category_id,
							category_name = '$category_name',
							category_status = '$category_status',
							category_created_from = 'iolink'
							";
			//exit;
			$insertQueryRsId = imw_query($insertQuery);
			if(!$insertQueryRsId && $newOrAll=='all'){
				$updateQuery = "update surgery_center_consent_category set 
						category_id = $category_id,
						category_name = '$category_name',
						category_status = '$category_status',
						category_created_from = 'iolink'
						where category_id = $category_id and category_created_from != 'user'";
				$updateQueryRsId = imw_query($updateQuery);
			}
			$a++;
		}
	
		$xmlNew = $xmlData[1];    
		$tag = 'consentformstemplatechild';
		$consentTemplate = array();
		$consentTemplate = XMLtoArray($xmlNew,$tag);	
		$a = 0;
		while ($a < count($consentTemplate)) {		
			$consent_id = 				$consentTemplate[$a]['consent_id'];
			$consent_name = 			$consentTemplate[$a]['consent_name'];
			$consent_alias = 			$consentTemplate[$a]['consent_alias'];
			$consent_category_id = 		$consentTemplate[$a]['consent_category_id'];
			$consent_data = 			$consentTemplate[$a]['consent_data'];
			$consent_delete_status = 	$consentTemplate[$a]['consent_delete_status'];
			
			$insertQuery = "insert into surgery_center_consent_forms_template set
							consent_id = '".addslashes($consent_id)."',
							consent_name = '".addslashes($consent_name)."',
							consent_alias = '".addslashes($consent_alias)."',
							consent_category_id = '".addslashes($consent_category_id)."',
							consent_data = '".addslashes($consent_data)."',
							consent_delete_status = '".addslashes($consent_delete_status)."',
							form_created_from = 'iolink'
							";
			$insertQueryRsId = imw_query($insertQuery);
			if(!$insertQueryRsId && $newOrAll=='all'){
				$updateQuery = "update surgery_center_consent_forms_template set
							consent_id = '".addslashes($consent_id)."',
							consent_name = '".addslashes($consent_name)."',
							consent_alias = '".addslashes($consent_alias)."',
							consent_category_id = '".addslashes($consent_category_id)."',
							consent_data = '".addslashes($consent_data)."',
							consent_delete_status = '".addslashes($consent_delete_status)."',
							form_created_from = 'iolink'
							where consent_id = '".addslashes($consent_id)."' and form_created_from != 'user'";
				$updateQueryRsId = imw_query($updateQuery);
			}
			$a++;
			$b++;
		}
		if(count($consentTemplate)==$b) {
			$consntMsg = 'Surgery Center New Consent Forms Download Complete';
			if($newOrAll=='all') {$consntMsg = 'Surgery Center All Consent Forms Download Complete'; }
			echo $consntMsg;
			/*
			echo "<script>
					document.getElementById('dataSavedId').innerHTML = '".$consntMsg."';
					document.getElementById('dataSavedId').style.display='block';
					</script>";*/
		}
	}
	elseif($wishToDownLoad == 2){
		if( $data == 'No Record found') 
			echo ucwords(strtolower($data));
		else {
			$xmlNew = $data;    
			$tag = 'healthquestionerchild';
			$healthQuestionerArr = array();
			$healthQuestionerArr = XMLtoArray($xmlNew,$tag);	
			$a = 0;
			while ($a < count($healthQuestionerArr)) {					
				$healthQuestioner = $healthQuestionerArr[$a]['healthQuestioner'];
				$adminQuestion	  = $healthQuestionerArr[$a]['adminquestion'];
				
				$insertQuery = "insert into surgery_center_health_questioner set
								healthQuestioner = '".addslashes($healthQuestioner)."',
								question = '".addslashes($adminQuestion)."'
								";
				$insertQueryRsId = imw_query($insertQuery);
				if(!$insertQueryRsId){				
					$updateQuery = "update surgery_center_health_questioner set
								healthQuestioner = '".addslashes($healthQuestioner)."',
								question = '".addslashes($adminQuestion)."'
								where healthQuestioner = '".addslashes($healthQuestioner)."'";
					$updateQueryRsId = imw_query($updateQuery);
				}
				$a++;
				$b++;
				
			}
			if(count($healthQuestionerArr)==$b) {		
				echo 'Health Questionnaire Download Complete';
			}
		}
	}
	elseif($wishToDownLoad == 3){
		if( $data == 'No Record found') 
			echo ucwords(strtolower($data));
		else {
			$xmlNew = $data;    
			$tag = 'historyphysicalchild';
			$historyphysicalArr = array();
			$historyphysicalArr = XMLtoArray($xmlNew,$tag);
			$a = 0;
			while ($a < count($historyphysicalArr)) {					
				$id = $historyphysicalArr[$a]['historyPhysicalId'];
				$ques = $historyphysicalArr[$a]['historyPhysicalQues'];
				$status = $historyphysicalArr[$a]['historyPhysicalStatus'] ? $historyphysicalArr[$a]['historyPhysicalStatus'] : 0;
				
				$insertQuery = "insert into surgerycenter_history_physical_ques set surgerycenter_id = '".$id."', name = '".addslashes($ques)."', deleted = '".$status."' ";
				$insertQueryRsId = imw_query($insertQuery);
				
				if(!$insertQueryRsId){				
					$updateQuery = "update surgerycenter_history_physical_ques set name = '".addslashes($ques)."', deleted = '".$status."' Where surgerycenter_id = '".$id."' ";
					$updateQueryRsId = imw_query($updateQuery);
				}
				$a++;
				$b++;
				
			}
			if(count($historyphysicalArr)==$b) {		
				echo 'H & P Download Complete';
			}
		}
	}
}
else{
	echo 'Connection could not be established';
}

function XMLtoArray($xml,$tag){
	$parser = xml_parser_create('ISO-8859-1'); // For Latin-1 charset
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); // Dont mess with my cAsE sEtTings
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); // Dont bother with empty info
	xml_parse_into_struct($parser, $xml, $values);
	xml_parser_free($parser);
  
	$return = array(); // The returned array
	$stack = array(); // tmp array used for stacking
	$arr = array();
	$arrMain = array();
	$c=0;
	$flag = false;

	foreach($values as $key => $val){	
		if(($val["tag"] ==$tag)){
			if($val["type"] == "open"){ 
				$flag = true;
				$arr = array();
			}
			if($val["type"] == "close"){ 
				$flag = false;
				$arrMain[$c++] = $arr;
				unset($arr); 
			}
		}
	
		if(($flag == true)&&($val["type"]=="complete")){
			if(isset($val["value"]) && !empty($val["value"])){
				$arr[$val["tag"]] = $val["value"];
			}
		}	
	}
	return $arrMain;
}
exit();
?>	
