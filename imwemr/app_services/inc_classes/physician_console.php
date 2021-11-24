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
set_time_limit(900);
include_once(dirname(__FILE__).'/patient_erx_data.php');
class physician_console extends patient_erx_data{	
	var $reqModule;
	var $arrProvider = array();
	var $production = 0;
	public function __construct(){
		parent::__construct();
	}
	public function eRx_inbox(){
		$arrReturn = array();
		if(($this->emdeon_url != '' && $this->eRx_user_name != '' && $this->erx_password != '') || !$this->production){
			if($this->production){
				$url = $this->emdeon_url."/servlet/DxLogin?userid=".$this->eRx_user_name."&PW=".$this->erx_password."&hdnBusiness=".$this->eRx_facility_id."&target=servlet/servlets.apiRxServlet&actionCommand=rxinboxext&apiLogin=true&textError=true";
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
				$url = $this->emdeon_url."/servlet/lab.security.DxLogout?userid=".$this->eRx_user_name."&BaseUrl=".$this->emdeon_url."&LogoutPath=/html/AutoPrintFinished.html";
				curl_setopt($cur,CURLOPT_URL,$url);
				curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
				curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
				curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
				$log_data = curl_exec($cur);
				curl_close($cur);
				preg_match("/<-- ERROR :/",$erx_data_arr[0],$errorArr);
			}
			if(!$this->production){
			$errorArr = array();
			$erx_data = file_get_contents("erx_demo.txt");
			}
			
			if(count($errorArr) == 0){
			$erx_data = preg_replace('/<--BEGIN RX>/','',$erx_data);
			$erx_data = trim(preg_replace('/<--END RX>/','',$erx_data));
			$erx_data_arr  = explode("^",$erx_data);
			
			
			$return_arr = array();
			for($i=0;$i<count($erx_data_arr);$i++){
				$inbox_data = $erx_data_arr[$i];
				$inbox_data_arr = explode('|',$inbox_data);
				
				if(strtoupper($inbox_data_arr[5]) == 'PENDING'){
					
					$clsNotMatch="";	
					$pat_id = $inbox_data_arr[17];
					
					$user_npi = $inbox_data_arr[19];
					
					if(!empty($user_npi)){
					$this->db_obj->qry  = "select concat(lname,', ',fname) as name from users where user_npi = '$user_npi'";				
					$result_arr = $this->db_obj->get_resultset_array();
					$phy_name = trim($result_arr[0]['name']);
					}				
					
					$this->db_obj->qry  = "SELECT concat(lname,', ',fname) AS name, 
											mname, sex, id,
											date_format(DOB,'%m-%d-%Y') as pat_dob
											FROM patient_data ";
					if(!empty($pat_id)){	
						$this->db_obj->qry  .= "WHERE id = '$pat_id'";	
					}else if(!empty($inbox_data_arr[8])){					
						$arrPtnm = explode(",",$inbox_data_arr[8]);					
						$this->db_obj->qry  .= "WHERE UCASE(fname) = '".strtoupper(trim($arrPtnm[1]))."' 
												AND UCASE(lname)= '".strtoupper(trim($arrPtnm[0]))."'  ";
					}
					$result_arr = $this->db_obj->get_resultset_array();
					if(count($result_arr)==1){ //Check exactly for 1 patient so that do not show if anything is not sure
						$pat_name = trim($result_arr[0]['name'].' '.$result_arr[0]['mname']);
						$pat_dob = $result_arr[0]['pat_dob'];
						$pat_sex = $result_arr[0]['sex'];
						$pat_id = 	$result_arr[0]['id'];
						
					}else{
						if($this->production){
						$pat_name = $pat_dob = $pat_sex = $pat_id =  "";
						}
					}
					
					//Check for No Match ---					
					if(empty($pat_name) && !empty($inbox_data_arr[8])){
						$pat_name = $inbox_data_arr[8];	
						$clsNotMatch=" style=\"color:#FF0000;\"  ";					
					}
					
					if(empty($phy_name)&&!empty($inbox_data_arr[9])){
						$phy_name = $inbox_data_arr[9];
					}				
					//Check for No Match ---	
					
					if((!empty($pat_name) && trim($phyName) == trim($phy_name)) || !$this->production){ //trim($pat_name) == trim($inbox_data_arr[8]) && trim($phyName) == trim($phy_name)
						$issued_date_arr = explode(' ',$inbox_data_arr[6]);
						$issued_date = date('m-d-y',strtotime($issued_date_arr[0]));
						$return_arr['issued_date'] = $issued_date;
						if(!empty($pat_id)){
							$pending_prescriptions = ucwords($pat_name).' - '.$pat_id;
						}else{
							$pending_prescriptions = ucwords($pat_name);
						}
						$return_arr['pending_prescriptions'] = $pending_prescriptions;
						$return_arr['issue_as'] = ucfirst($inbox_data_arr[0]);
						$return_arr['detail1'] = $inbox_data_arr[7];
						$return_arr['detail2'] = $inbox_data_arr[10];
						$return_arr['patient_id'] = $pat_id;
						$arrReturn[] = $return_arr;
					}				
				}			
			}
		}
		}
		return $arrReturn;
	}
	public function eRx_selection(){
			//require_once(dirname(__FILE__).'/../../interface/main/Functions.php');
			//$objManageData = new ManageData;
			$patientId = $this->patient;
			$userId = $this->authId;
			
			//$objManageData->QUERY_STRING = "select eRx_user_name, erx_password from users where id = '$userId'";
			$sqlQry = imw_query("select eRx_user_name, erx_password from users where id = '$userId'");
			//$userRes = $objManageData->mysqlifetchdata();
			
			$userRes = '';
			if($sqlQry && imw_num_rows($sqlQry) > 0){
				$userRes = imw_fetch_assoc($sqlQry);
			}
			
			$eRx_user_name = $userRes['eRx_user_name'];
			$erx_password = $userRes['erx_password'];
			
			//$objManageData->QUERY_STRING = "select EmdeonUrl,emdeon_test_pro from copay_policies";
			//$copay_policies_res = $objManageData->mysqlifetchdata();
			
			$sqlQry2 = imw_query("select EmdeonUrl,emdeon_test_pro from copay_policies");
			$copay_policies_res = '';
			if($sqlQry2 && imw_num_rows($sqlQry2) > 0){
				$copay_policies_res = imw_fetch_assoc($sqlQry2);
			}
			
			$EmdeonUrl 			= $copay_policies_res['EmdeonUrl'];
			$emdeon_test_pro 	= $copay_policies_res['emdeon_test_pro'];
			if($EmdeonUrl==''){
				if($emdeon_test_pro==1){
					$EmdeonUrl = 'https://clinician.emdeon.com';
				}else if($emdeon_test_pro==0){
					$EmdeonUrl = 'https://cli-cert.emdeon.com';	
				}
			}
			if($eRx_user_name != '' && $erx_password != '' && trim($EmdeonUrl) != ''){
			//$objManageData->QUERY_STRING = "select * from patient_data where id = '$patientId'";
			//$qryRes = $objManageData->mysqlifetchdata();
			echo "select * from patient_data where id = '$patientId'";
			$sqlQry3 = imw_query("select * from patient_data where id = '$patientId'");
			$qryRes = '';
			if($sqlQry3 && imw_num_rows($sqlQry3) > 0){
				$qryRes = imw_fetch_assoc($sqlQry3);
			}
			$id = $qryRes[0]['id'];
			$fname = $qryRes[0]['fname'];
			$lname = $qryRes[0]['lname'];
			list($year,$mon,$day) = preg_split('/-/',$qryRes[0]['DOB']);
			$patient_dob = $mon.'/'.$day.'/'.$year;
			$subModuleURL = (isset($_GET['loadmodule']) && trim($_GET['loadmodule'])=='ptdemo') ? 'lab/person/PersonDemographics.jsp' : 'lab/person/PersonRxHistory.jsp';
			echo $erx_url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&apiLogin=true&target=jsp/$subModuleURL&actionCommand=apiRxHistory&P_ACT=$id&P_LNM=$lname&P_FNM=$fname&P_DOB=$patient_dob";die;
			header("location: $erx_url");
			}
			die();
	}
	public function getUsersList(){
		
		 $arrReturn = array();
		 $this->db_obj->qry = "select name AS user_type_name,name AS user_type_id from user_groups where status = '1' order by display_order";
         $result1 = $this->db_obj->get_resultset_array();   
		 
		 
		 $this->db_obj->qry = "select id AS user_type_id, CONCAT(lname,', ',fname,' ',mname) AS user_type_name from users where id > 0 and delete_status = '0'
								order by lname,fname";
		 $result2 = $this->db_obj->get_resultset_array();
		$arrReturn = array_merge($result1,$result2);
		 return $arrReturn;
		                        
	}
	public function getUsersList_app(){
		
		 $arrReturn = array();
		 $result3 = array();
		 $this->db_obj->qry = "select name AS user_type_name,name AS user_type_id from user_groups where status = '1' order by display_order";
         $result1 = $this->db_obj->get_resultset_array();   
		 
		 
		 $this->db_obj->qry = "select  CONCAT(lname,', ',fname,' ',mname) AS user_type_name,id AS user_type_id from users where id > 0 and delete_status = '0'
								order by lname,fname";
		 $result2 = $this->db_obj->get_resultset_array();
		 
		 for($i=0;$i<count($result2);$i++){
		 	$result2[$i]["user_type_id"] = "".$result2[$i]["user_type_id"];
		 }
		 
 		$arrReturn = array_merge($result1,$result2);
		 return $arrReturn;
		                        
	}
	
}

?>