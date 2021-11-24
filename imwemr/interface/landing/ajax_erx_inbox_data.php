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
require_once(dirname(__FILE__).'/../../config/globals.php');;
require_once(dirname(__FILE__).'/../../library/classes/class.erx_functions.php');


$objERX 				= new ERXClass();
$user_id 				= $_SESSION['authId'];
	
//--- GET ERX STATUS AND EMDEON ACCESS URL -------
$copay_policies_res		= $objERX->get_copay_policies('*');
$Allow_erx_medicare 	= $copay_policies_res['Allow_erx_medicare'];
$EmdeonUrl 				= $copay_policies_res['EmdeonUrl'];

//--- GET ERX USERNAME AND PASSWORD --------
$phyQryRes 				= $objERX->getProviderDetails($user_id);
$eRx_user_name 			= $phyQryRes['eRx_user_name'];
$erx_password 			= $phyQryRes['erx_password'];
$eRx_facility_id 		= trim($_SESSION['login_facility_erx_id']);//$phyQryRes[0]['eRx_facility_id'];
$dd_provider_npi = trim($phyQryRes['user_npi']);

$data = '';

if(strtolower($Allow_erx_medicare)=='no'){ $data = '<div class="alert alert-danger">eRx not allowed.</div>';}
else if($EmdeonUrl == '' && $eRx_user_name == '' && $erx_password == ''){
	$data = '<div class="alert alert-danger">User\'s eRx credentials/Clinician URL missing.</div>';
}else if($EmdeonUrl != '' && $eRx_user_name != '' && $erx_password != ''){
	$url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&hdnBusiness=$eRx_facility_id&target=servlet/servlets.apiRxServlet&actionCommand=rxinboxext&apiLogin=true&textError=true";
	$url = preg_replace('/ /','%20',$url);
	$cur = curl_init();
	curl_setopt($cur,CURLOPT_URL,$url);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
	$erx_data = curl_exec($cur);
	curl_close($cur);
	//--- Log out from emdeon erx --------
	$cur = curl_init();
	$url = "$EmdeonUrl/servlet/lab.security.DxLogout?userid=$eRx_user_name&BaseUrl=$EmdeonUrl&LogoutPath=/html/AutoPrintFinished.html";
	curl_setopt($cur,CURLOPT_URL,$url);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
	curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
	$log_data = curl_exec($cur);
	curl_close($cur);
	preg_match("/<-- ERROR :/",$erx_data,$errorArr);
	imw_query("update users set lasteRxInboxReadTime = '".date('Y-m-d H:i:s')."' where id = '$user_id'");
	
	//die($erx_data);
	if(count($errorArr) == 0){
		$erx_data = preg_replace('/<--BEGIN RX>/','',$erx_data);
		$erx_data = trim(preg_replace('/<--END RX>/','',$erx_data));
		$erx_data_arr  = explode("^",$erx_data);
		
		/*******GETTING ARRAY OF USER WITH NPI*******/
		$res_users = imw_query("SELECT id,CONCAT(lname,', ',fname,' ',mname) AS phy_name,user_npi FROM users WHERE eRx_user_name!='' AND erx_password!='' AND user_npi!='' AND delete_status='0'");
		$array_users = array();
		$current_user_npi = '';
		if($res_users && imw_num_rows($res_users)>0){
			while($rs_users = imw_fetch_assoc($res_users)){
				$array_users[$rs_users['user_npi']] = $rs_users;
				if($_SESSION['authId']==$rs_users['id']) $current_user_npi = $rs_users['user_npi'];
			}
		}
		/********************************************/
		$records = 0;
		$data = '';
		for($i=0;$i<count($erx_data_arr);$i++){
			$inbox_data = $erx_data_arr[$i];
			$inbox_data_arr = explode('|',$inbox_data);
			//pre($inbox_data_arr,1);
			
			$issue_method 	= trim($inbox_data_arr[0]); //ELECTRONIC/PRINT ETC.
			$issue_type		= trim($inbox_data_arr[2]); //NEW/RENEWAL ETC
			$erx_error		= strtolower(trim($inbox_data_arr[4])) == 'error' ? true : false;
			$erx_error_msg	= trim($inbox_data_arr[3]); //if error then text, else empty.
			$erx_status		= trim($inbox_data_arr[5]); //PENDING/AUTHORIZED ETC.
				$issued_date_arr = explode(' ',$inbox_data_arr[6]);
			$issued_date = date('m-d-Y',strtotime($issued_date_arr[0]));
			$prescription	= trim($inbox_data_arr[7]);
			$patient_name	= trim($inbox_data_arr[8]);
			$prescriber_name= trim($inbox_data_arr[9]);
			$pres_sig		= trim($inbox_data_arr[10]);
			$patient_id		= trim($inbox_data_arr[17]);
			$prescriber_npi	= trim($inbox_data_arr[19]);
			$entered_by		= trim($inbox_data_arr[22]); //erx username.
			$patient_city	= trim($inbox_data_arr[25]);
			$patient_state	= trim($inbox_data_arr[26]);
			$patient_zip	= trim($inbox_data_arr[27]);

			//Enable the line below to see only PENDING prescription records.
			if(strtolower($erx_status)!='pending') 	continue;
			if($prescriber_npi=='') 				continue; //if no prescriber npi received, skip this record.
			if($current_user_npi != $prescriber_npi)continue; //showing records related to logged in user only.

			$phy_name_db = 	$array_users[$prescriber_npi]['phy_name'];
				
			$qry_patient = "SELECT concat(lname,', ',fname,' - ',id) as patient_name, id FROM patient_data WHERE ";
			if(!empty($patient_id)){	
				$qry_patient .= "id = '$patient_id' LIMIT 0,1";	
				$res_patient = imw_query($qry_patient);
			}else{
				if(!empty($patient_name)){					
					$arrPtnm = explode(",",$patient_name);					
					$qry_patient .= "UPPER(fname) = '".strtoupper(trim($arrPtnm[1]))."' AND UPPER(lname)= '".strtoupper(trim($arrPtnm[0]))."' ";
					$res_patient = imw_query($qry_patient);
					if($res_patient && imw_num_rows($res_patient)!=1){
						if(!empty($patient_city)){					
							$qry_patient .= "AND UPPER(city) = '".strtoupper($patient_city)."' ";
						}
						if(!empty($patient_state)){
							$qry_patient .= "AND UPPER(state) = '".strtoupper($patient_state)."' ";
						}
						if(!empty($patient_zip)){
							$qry_patient .= "AND postal_code = '".$patient_zip."'";
						}
						$qry_patient .= " LIMIT 0,5";
						imw_free_result($res_patient);
						$res_patient = imw_query($qry_patient);
					}
				}
			}
			$PtMatch= false;
			if($res_patient && imw_num_rows($res_patient)==1){
				$rs_patient = imw_fetch_assoc($res_patient);
				$patient_id = $rs_patient['id'];
				$patient_name = $rs_patient['patient_name'];
				$PtMatch= true;
			}
			
			$data .= '
				<div class="phyrow">
					<strong>';
					if($PtMatch){
						$data .='<a href="javascript:void(0)" class="a_clr1" onClick="open_erx(\''.$patient_id.'\');">'.ucwords($patient_name).'</a>';
					}else{
						$data .='<a href="javascript:void(0)" class="a_clr1" style="color:#f00;" onClick="alert(\'Patient ID is empty or is not matched with database records.\')">'.ucwords($patient_name).'</a>';
					}
			$data .= '
					</strong><br>
					'.$prescription.'<br>'.$pres_sig.'
					<div class="mesgopt hide"><button class="referalbut" type="submit">Referrals</button></div>
				</div>
			';
			$records++;
			if($records>=6) break;
		}
	}else{
		$data = '<div class="alert alert-danger">'.$erx_data.'</div>';
	}
}

if( trim($data) == ''){
	$data='<div class="alert alert-info">You don\'t have any eRx Message.</div>';
}

echo $data;