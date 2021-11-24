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
File: ListChartNotes.php
Purpose: This file provides list of chart notes of a patient.
Access Type : Include file
*/
?>
<?php
//GetWVSummery
set_time_limit(900);
include_once(dirname(__FILE__).'/patient_app.php');

class ListChartNotes extends patient_app{
	
	private $patient_id;	
	
	public function __construct($patient_id){
		parent::__construct();
		$this->patient_id= $patient_id;		
	}
	
	function getTemplateName($tId){
		$ret="Comprehensive";
		if(!empty($tId)){
			$sql = "SELECT temp_name FROM chart_template WHERE id='".$tId."' " ;
			$row=sqlQuery($sql);
			if($row!=false){
				$ret=$row["temp_name"];
			}			
		}
		return $ret;		
	}
	
	/*
	so as of now we need to create:-
	Manvinder Siingh: b. Clicking on the Pt name should bring up a list of patient Dates of Service:
	i. It should have the following columns in this order
	1. Patient Name
	2. Patient ID
	3. Patient DOB
	4. Type of Chart Note (comprehensive, new patient, post op etc)
	5. Physician the patient saw that date
	6. Location patient was seen
	*/
	
	function listChartNotes(){		
		
		//require_once $GLOBALS['incdir']."/chart_notes/common/functions.php";
		//require_once $GLOBALS['incdir']."/chart_notes/common/ChartNote.php";
		
		
		
		$ret_data = array();
		$patient_id = $this->patient_id;		
		
		//pt details
		$strPtNm =$dob="";
		$sql = "SELECT title, fname, mname, lname, suffix, id, DATE_FORMAT(DOB,'%m-%d-%Y') AS dob_show  FROM patient_data 
				WHERE id = '".$patient_id."' ";
		$row = sqlQuery($sql);
		
		if($row != false){
			$strPtNm ="";
			if(!empty($row["title"])){ $strPtNm .= $row["title"]." "; }
			if(!empty($row["fname"])){ $strPtNm .= $row["fname"]." "; }
			if(!empty($row["mname"])){ $strPtNm .= $row["mname"]." "; }
			if(!empty($row["lname"])){ $strPtNm .= $row["lname"]." "; }
			if(!empty($row["suffix"])){ $strPtNm .= $row["suffix"]." "; }		
			
			$dob=$row["dob_show"];
		}
		
		
		
		//all server
		$arrRemoteServerAbbr = $this->getServerAbbr();
		
		//list charts
		$sql = "SELECT date_of_service,templateId, providerId, serverId, facilityId, id FROM chart_master_table 
				WHERE patient_id = '".$patient_id."' AND delete_status=0 
				ORDER by date_of_service DESC, id DESC ";
		$rez = sqlStatement($sql);		
		for($i=1;$row=sqlFetchArray($rez);$i++){
			
			$dbDos=$date_of_service=$row["date_of_service"];
			$templateId=$row["templateId"];
			$providerId=$row["providerId"];
			$serverId=$row["serverId"];
			$facilityId=$row["facilityId"];
			$formId=$row["id"];
			
			//dateofservice
			//$oUt = new Utility();
			//$date_of_service_show = $oUt->formatDate($date_of_service);
			$date_of_service_show = date('m-d-Y', strtotime($date_of_service));
			
			//template name
			$tmplate_name=$this->getTemplateName($templateId);
			
			//User name
			if(!empty($providerId)){
				//$oUsr = new User($providerId);
				//$usrname = $oUsr->getName();
				
				$userDetails = getUserDetails($providerId);
				$usrname = core_name_format($userDetails['lname'], $userDetails['fname'], $userDetails['mname'], $userDetails['pro_title'], $userDetails['pro_suffix']);
			}
			
			//location
			$serverAbbr ="";
			if(!empty($facilityId)){
				$serverAbbr = $this->getFacilityAbbr($facilityId);
			}else if(!empty($serverId)){
				$serverAbbr = $arrRemoteServerAbbr[$serverId];	
			}else{
				$facilityId =  $this->getChartFacilityFromSchApp($patient_id, $dbDos);
				if(!empty($facilityId)){$serverAbbr = $this->getFacilityAbbr($facilityId);}				
			}
			if($usrname == NULL){$usrname = '';}
			if($dob == NULL){$dob = '';}
			if($tmplate_name == NULL){$tmplate_name = '';}
			if($serverAbbr == NULL){$serverAbbr = '';}
			if($date_of_service_show == NULL){$date_of_service_show = '';}
			if($formId == NULL){$formId = '';}
			$ret_data[] =array(trim($strPtNm) , $patient_id, $dob, $tmplate_name, $usrname, $serverAbbr, $date_of_service_show, $formId );
			
		}
		
		return $ret_data;
	}
	
	
	function listChartNotes_app(){		
		
		//require_once $GLOBALS['incdir']."/chart_notes/common/functions.php";
		//require_once $GLOBALS['incdir']."/chart_notes/common/ChartNote.php";
		
		
		
		$ret_data = array();
		$patient_id = $this->patient_id;		
		
		//pt details
		$strPtNm =$dob="";
		$sql = "SELECT title, fname, mname, lname, suffix, id, DATE_FORMAT(DOB,'%m-%d-%Y') AS dob_show  FROM patient_data 
				WHERE id = '".$patient_id."' ";
		$row = sqlQuery($sql);
		
		if($row != false){
			$strPtNm ="";
			if(!empty($row["title"])){ $strPtNm .= $row["title"]." "; }
			if(!empty($row["fname"])){ $strPtNm .= $row["fname"]." "; }
			if(!empty($row["mname"])){ $strPtNm .= $row["mname"]." "; }
			if(!empty($row["lname"])){ $strPtNm .= $row["lname"]." "; }
			if(!empty($row["suffix"])){ $strPtNm .= $row["suffix"]." "; }		
			
			$dob=$row["dob_show"];
		}
		
		
		
		//all server
		$arrRemoteServerAbbr = $this->getServerAbbr();
		
		//list charts
		$sql = "SELECT DATE_FORMAT(date_of_service,'%m-%d-%Y') as 'date_of_service',templateId, providerId, serverId, facilityId, id FROM chart_master_table 
				WHERE patient_id = '".$patient_id."' AND delete_status=0 
				ORDER by date_of_service DESC, id DESC ";
		$rez = sqlStatement($sql);		
		for($i=1;$row=sqlFetchArray($rez);$i++){
			
			$dbDos=$date_of_service=$row["date_of_service"];
			$templateId=$row["templateId"];
			$providerId=$row["providerId"];
			$serverId=$row["serverId"];
			$facilityId=$row["facilityId"];
			$formId=$row["id"];
			
			//dateofservice
			//$oUt = new Utility();
			//$date_of_service_show = $oUt->formatDate($date_of_service);
			
			//template name
			$tmplate_name=$this->getTemplateName($templateId);
			
			//User name
			if(!empty($providerId)){
				//$oUsr = new User($providerId);
				//$usrname = $oUsr->getName();
				
				$userDetails = getUserDetails($providerId);
				$usrname = core_name_format($userDetails['lname'], $userDetails['fname'], $userDetails['mname'], $userDetails['pro_title'], $userDetails['pro_suffix']);
			}
			
			//location
			$serverAbbr ="";
			if(!empty($facilityId)){
				$serverAbbr = $this->getFacilityAbbr($facilityId);
			}else if(!empty($serverId)){
				$serverAbbr = $arrRemoteServerAbbr[$serverId];	
			}else{
				$facilityId =  $this->getChartFacilityFromSchApp($patient_id, $dbDos);
				if(!empty($facilityId)){$serverAbbr = $this->getFacilityAbbr($facilityId);}				
			}
			if($usrname == NULL){$usrname = '';}
			if($dob == NULL){$dob = '';}
			if($tmplate_name == NULL){$tmplate_name = '';}
			if($serverAbbr == NULL){$serverAbbr = '';}
			if($date_of_service_show == NULL){$date_of_service_show = '';}
			if($formId == NULL){$formId = '';}
			$ret_data[] =array("patient_name"=>trim($strPtNm) ,"patient_id" =>$patient_id, "DOB"=>$dob,"template_name"=> $tmplate_name,
													"phyname"=> $usrname, "server"=>$serverAbbr,"date"=> $date_of_service, 
													"form_id"=>$formId );
		}
		
		return $ret_data;
	}
	
	public function getServerAbbr(){
		$arr=array();
		$sql = "SELECT * FROM servers ";
		$rez = sqlStatement($sql);
		for($i=0; $row=sqlFetchArray($rez); $i++){
			if(!empty($row["id"])){
				$arr[$row["id"]] = $row["abbre"];
			}		
		}
		return $arr;
	}
	
	public function getFacilityAbbr($id){
	
		if(constant("SHOW_SERVER_LOCATION") == 1){
			$sql = "SELECT name, c2.abbre  FROM facility c1 
					LEFT JOIN server_location c2 ON c1.server_location = c2.id
					WHERE c1.id = '".$id."' ";
		}else{
			$sql = "SELECT name  FROM facility WHERE id = '".$id."' ";
		}	
		
		$row=sqlQuery($sql);
		if($row != false){
			
			if(constant("SHOW_SERVER_LOCATION") == 1){
				if(!empty($row["abbre"])){
					$ret=$row["abbre"];
				}
				
			}
			
			if(empty($ret)){
				$ret=$row["name"];
			}
		}
		return trim($ret);
	}
	
	public function getChartFacilityFromSchApp($patient_id, $dos){
		$ret=0;
		$sql="select sa_facility_id from schedule_appointments where sa_app_start_date='$dos' and sa_patient_id='$patient_id' and sa_patient_app_status_id NOT IN (203,201,18,19,20) order by sa_app_start_date,sa_app_starttime asc";
		$row=sqlQuery($sql);
		if($row!=false){
			if(!empty($row["sa_facility_id"])){
				$ret=$row["sa_facility_id"];	
			}
		}
		return $ret;
	}
}
?>