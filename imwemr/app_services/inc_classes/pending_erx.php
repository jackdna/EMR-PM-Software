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
include_once(dirname(__FILE__).'/emdeon.php');
class pending_erx extends emdeon{	
	public function __construct(){
		parent::__construct();
	}
	public function show_pending_erx(){
		if($this->emdeon_url != '' && $this->eRx_user_name != '' && $this->erx_password != ''){
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
						$pat_name = $pat_dob = $pat_sex = $pat_id =  "";
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
					
					if(!empty($pat_name) && trim($phyName) == trim($phy_name)){ //trim($pat_name) == trim($inbox_data_arr[8]) && trim($phyName) == trim($phy_name)
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
						return $return_arr;
						//$return_arr['pending_prescriptions'] = $pending_prescriptions;
						$data .= '
							<tr height="25" bgcolor="#FFFFFF">
								<td align="left" >
									<span class="text_10b">';
									
									if(!empty($pat_id)){
										$data .='<a href="javascript:void(0)" class="a_clr1" onClick="open_erx(\''.$pat_id.'\');">'.ucwords($pat_name).' - '.$pat_id.'</a>';
									}else{
										$data .='<a href="javascript:void(0)" class="a_clr1" '.$clsNotMatch.' onClick="alert(\'Patient ID is empty or is not matched with database records.\')">'.ucwords($pat_name).'</a>';
									}
									
									$data .='</span><br>
									<span class="text_10">'.$inbox_data_arr[7].'<br>'.$inbox_data_arr[10].'							
									</span>
								</td>
							</tr>
						';
						
					}				
				}			
			}
		}
		}
	}
	
}

?>