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
?>
<?php
/*	
File: search_erx_allergy.php
Purpose: Search alleries in eRx
Access Type: Direct 
*/
require_once(getcwd().'/../../../config/globals.php');

//----- GET ERX STATUS FOR PATIENT -------
$userId = $_SESSION['authId'];
$pid = $_SESSION['patient'];

$qry = "select Allow_erx_medicare from copay_policies where policies_id = '1'";
$qryRes = get_array_records_query($qry);
	
$eRxStatusRes = false;
if(strtolower($qryRes[0]['Allow_erx_medicare']) == 'yes'){
	$qry = "select erx_entry, erx_patient_id from patient_data where id = '$pid'";
	$qryResInn =  get_array_records_query($qry);
	if($qryResInn[0]['erx_patient_id'] != '' && $qryResInn[0]['erx_patient_id'] != 'null'){
				$eRxStatusRes = true;
	}
}

//--- GET EMDEON URL FOR TESTING OR PRODUCTION -----
$qry = "select EmdeonUrl from copay_policies";	
$copay_policies_res =get_array_records_query($qry);
$EmdeonUrl = $copay_policies_res[0]['EmdeonUrl'];

//--- GET LOGGED PROVIDER USER NAME AND PASSWORD AND FACILITY ID ----
$userId = $_SESSION['authId'];
$qry = "select eRx_user_name, erx_password, eRx_facility_id from users where id = '$userId'";
$userRes = get_array_records_query($qry);

$eRx_user_name = $userRes[0]['eRx_user_name'];
$erx_password = $userRes[0]['erx_password'];
$eRx_facility_id = trim($_SESSION['login_facility_erx_id']);//$userRes[0]['eRx_facility_id'];
$record_not_exists = false;
$page_refresh = false;


//--- GET ALL ALLERGY TYPE ----
function get_erx_allergies($EmdeonUrl, $eRx_user_name, $erx_password, $eRx_facility_id, $allergyName, $allergy_type){
	//--- ERX LOGIN ----------
	$cookie_file = data_path().'UserId_'.$userId.'/'.'cookie_'.$userId.'.txt';
	$cur = curl_init();
	$url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&hdnBusiness=$eRx_facility_id&target=html/LoginSuccess.html&testLogin=true";
	curl_setopt($cur,CURLOPT_URL,$url);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
	curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
	$eRxLogin = curl_exec($cur);
	preg_match('/Login Error/',$eRxLogin,$login_error);
	
	if(count($login_error) == 0 and $eRxLogin != ''){
		if($eRx_facility_id){
			$url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&hdnBusiness=$eRx_facility_id&target=servlet/servlets.apiRxServlet&actionCommand=searchfdballergy&apiLogin=true&textError=true&allergyname=$allergyName&allergytype=$allergy_type";
			$cur = curl_init();
			curl_setopt($cur,CURLOPT_URL,$url);
			curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
			curl_setopt($cur,CURLOPT_COOKIEFILE,$cookie_file);
			curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
			print_r(curl_error($cur));
			$erx_data = curl_exec($cur);
			$erx_data = substr($erx_data,3,-1);
			
			//--- IF RECORD NOT FOUND FROM ERX -------
			if(trim($erx_data) == 'NO RECORDS FOUND'){
				//--- LOG OUT FROM EMDEON --------
				$cur = curl_init();
				$url = "$EmdeonUrl/servlet/lab.security.DxLogout?userid=$eRx_user_name&BaseUrl=$EmdeonUrl&LogoutPath=/html/AutoPrintFinished.html";
				curl_setopt($cur,CURLOPT_URL,$url);
				curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
				curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
				curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
				$data = curl_exec($cur);
				$record_not_exists = true;
			}
			else{
				$allergyData_arr = explode('BEGIN FDB ALLERGY LIST>',$erx_data);
				$allergyData_arr = explode('<--END FDB ALLERGY LIST>',$allergyData_arr[1]);
				$allergyData = trim($allergyData_arr[0]);
				$allergyData_arr = explode('|',$allergyData);
				
				//--- DISPLAY ALL ALLERGIES -------
				$display_data_arr[0]['alergyId'] = $allergyData_arr[0];
				for($i=1;$i<count($allergyData_arr);$i++){
					$dis_arr = explode(' ',$allergyData_arr[$i]);
					$fac_name = '';
					$fac_name_arr = array();
					for($j=0;$j<count($dis_arr)-1;$j++){
						$fac_name_arr[] = $dis_arr[$j];
					}
					
					//--- FACILITY NAME ------
					$fac_name = implode(' ',$fac_name_arr);
					$dis_str = preg_replace('/\s+/','_iMedic_',end($dis_arr));
					$name_arr = explode('_iMedic_',$dis_str);
					$fac_name .= ' '.$name_arr[0];
					$facId = end($name_arr);
					if($i < count($allergyData_arr) - 1){
						$display_data_arr[$i]['alergyId'] = $facId;
					}
					$display_data_arr[$i-1]['alergy_name'] = $fac_name;
				}				
			}
		}
	}
	
	//--- LOG OUT FOR EMDEON --------
	if($loginSucess == true){		
		$cur = curl_init();
		$url = "$EmdeonUrl/servlet/lab.security.DxLogout?userid=$eRx_user_name&BaseUrl=$EmdeonUrl&LogoutPath=/html/AutoPrintFinished.html";
		curl_setopt($cur,CURLOPT_URL,$url);
		curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
		$data = curl_exec($cur);
		unlink($cookie_file);
	}

	//---- GET HTML DISPLAY DATA ----
	$displayDataArr = array();
	$str_cache = "";
	for($i=0;$i<count($display_data_arr);$i++){
		if( trim($display_data_arr[$i]['alergy_name'])) {
			$dataArr = array();
			$dataArr['id'] = $display_data_arr[$i]['alergyId'];
			$dataArr['name'] = trim($display_data_arr[$i]['alergy_name']);
			$displayDataArr[] = $dataArr;
			$str_cache .= $display_data_arr[$i]['alergyId']."__".trim($display_data_arr[$i]['alergy_name'])."::";
		}
	}
	if(trim($str_cache) != ""){
		$str_cache = substr($str_cache, 0, -2);
	}
	
	//--- DELETE ERX LOGIN COOKIE FILE ----
	@unlink($cookie_file);
	
	return array($displayDataArr, $str_cache);
}
$displayDataArr = array();
if($eRxStatusRes and $allergyName != '' and $allergy_type != '' and $erx_password != '' and $eRx_user_name != '' and trim($EmdeonUrl) != ''){
	$qry = "SELECT id, suggestions, created_on FROM erx_allergies_cache WHERE keyword = '".$allergyName."' LIMIT 1";
	$res = get_array_records_query($qry);
	if(count($res) > 0){
		$arr = $res;
		$curr_ts = time();
		$save_ts = strtotime($arr[0]["created_on"]);
		$diff_ft = $curr_ts - $save_ts;
		if($diff_ft > (ERX_ALLERGY_CACHE_LIMIT * 24 * 60 * 60)){
			list($displayDataArr, $str_cache) = get_erx_allergies($EmdeonUrl, $eRx_user_name, $erx_password, $eRx_facility_id, $allergyName, $allergy_type);
			if(trim($str_cache) == ""){
				$str_cache = "0__No Match Found.";
			}
			$qry = "UPDATE erx_allergies_cache SET keyword = '".$allergyName."', suggestions = '".$str_cache."', created_on = '".date("Y-m-d H:i:s")."', created_by = '".$_SESSION["authId"]."' WHERE id = '".$arr[0]["id"]."'";
			imw_query($qry);
		}else{
			$str_cache = $arr[0]["suggestions"];
			$arr_tmp = explode("::", $str_cache);
			$displayDataArr = array();
			for($i=0;$i<count($arr_tmp);$i++){
				$display_data_arr = explode("__", $arr_tmp[$i]);
				if( trim($display_data_arr[1]) ) {
					$dataArr = array();
					$dataArr['id'] = $display_data_arr[0];
					$dataArr['name'] = trim($display_data_arr[1]);
					$displayDataArr[] = $dataArr;
				}
			}
		}
	}else{
		list($displayDataArr, $str_cache) = get_erx_allergies($EmdeonUrl, $eRx_user_name, $erx_password, $eRx_facility_id, $allergyName, $allergy_type);
		if(trim($str_cache) == ""){
			$str_cache = "0__No Match Found.";
		}
		$qry = "INSERT INTO erx_allergies_cache SET keyword = '".$allergyName."', suggestions = '".$str_cache."', created_on = '".date("Y-m-d H:i:s")."', created_by = '".$_SESSION["authId"]."'";
		imw_query($qry);
	}
}

echo json_encode($displayDataArr);
?>