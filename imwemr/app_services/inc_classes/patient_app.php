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
// Accessing the Global file
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__).'/db.php');

//Global variable $gbl_imw_connect
global $gbl_imw_connect;
$sqlArr = array();
$sqlArr['host'] = (isset($gbl_imw_connect['host']) && empty($gbl_imw_connect['host']) == false) ? $gbl_imw_connect['host'] : '';
$sqlArr['port'] = (isset($gbl_imw_connect['port']) && empty($gbl_imw_connect['port']) == false) ? $gbl_imw_connect['port'] : '';
$sqlArr['login'] = (isset($gbl_imw_connect['login']) && empty($gbl_imw_connect['login']) == false) ? $gbl_imw_connect['login'] : '';
$sqlArr['pass'] = (isset($gbl_imw_connect['pass']) && empty($gbl_imw_connect['pass']) == false) ? $gbl_imw_connect['pass'] : '';
$sqlArr['dbName'] = (isset($gbl_imw_connect['idoc_db_name']) && empty($gbl_imw_connect['idoc_db_name']) == false) ? $gbl_imw_connect['idoc_db_name'] : '';

array_walk($sqlArr,"trim");
$db_obj = new app_db($sqlArr);

class patient_app extends app_db{
	var $patient;
	var $responseArray = array();
	var $pDir = '';
	var $upDir = '';
	//var $db_obj;
	
	public function __construct($patient = ''){
		global $responseArray, $db_obj, $authId, $sqlArr;
		parent::__construct($sqlArr);
		$this->patient = (isset($patient) && $patient!="")?$patient:$_REQUEST['patId'];
		$this->authId = $authId;
		$this->db_obj = $db_obj;
		$this->responseArray = $responseArray;
		$this->upDir = $GLOBALS['php_server']."/data/".constant('PRACTICE_PATH');
		$this->pDir = data_path()."PatientId_".$this->patient;
		//$this->upDir = $GLOBALS['php_server']."/interface/main/uploaddir";
		//$this->pDir = $GLOBALS['rootdir']."/main/uploaddir/PatientId_".$this->patient;
	}
	public function get_patient_data(){
		$this->db_obj->qry = "SELECT id,fname,lname FROM patient_data WHERE id = '".$this->patient."'";
		$result = $this->db_obj->get_resultset_array();
		$result[0]['name'] = $result[0]['lname'].", ".$result[0]['fname'];
		return $result[0];
	}
	public function search_patient(){
		$searchVal = trim($_REQUEST['searchVal']);
		$findBy = $_REQUEST['findBy'];
		if(($findBy != "Resp.LN") && ($findBy != "Ins.Policy") )
		{
			$elem_status=$findBy;
			$findBy=trim($this->getFindBy($searchVal));//die();
		}
		switch($findBy)
		{
		   case "Last":
			   $findBy="lname";
		   break;
		   case "LastFirstName":
			   $findBy="LastFirstName";
		   break;
		   case "street":
			   $findBy="street";
		   break;
		   case "phone":
			   $findBy="phone";
		   break;
		   case "First":
			   $findBy="fname";
		   break;
		   case "ID":
			   $findBy="id";
		   break;
		   case "DOB":
			   $findBy="DOB";
		   break;
		   case "SSN":
			   $findBy="ss";
		   break;
		   case "Resp.LN":
			   $findBy="Resp.LN";
		   break;
		   case "Ins.Policy":
			   $findBy="Ins.Policy";
		   break;   
		}
		$patientData = $this->searchPatientData($searchVal,$findBy,$elem_status);
		return $patientData;
	}
	public function searchPatientData($val,$fld,$status="Active"){
		$strFields = " CONCAT(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) AS name,
						patient_data.id,
						patient_data.sex,
						date_format(patient_data.DOB,'%m-%d-%Y') as DOB,
						DATE_FORMAT(SUBDATE(CAST(FROM_DAYS(DATEDIFF(NOW(),SUBDATE(patient_data.dob,INTERVAL 1 YEAR))) AS DATE),INTERVAL 1 YEAR),'%m') AS AGE_MONTHS,
						DATE_FORMAT(SUBDATE(CAST(FROM_DAYS(DATEDIFF(NOW(),SUBDATE(patient_data.dob,INTERVAL 1 YEAR))) AS DATE),INTERVAL 1 YEAR),'%y') AS AGE_YEAR,
						patient_data.street,
						patient_data.street2,
						patient_data.city,
						patient_data.state,
						patient_data.postal_code,
						CONCAT(patient_data.street,', ',patient_data.street2,', ',patient_data.city,', ',patient_data.state,' ',patient_data.postal_code) AS address,
						patient_data.phone_home,
						patient_data.phone_biz,
						patient_data.phone_cell,
						patient_data.email,
						patient_data.p_imagename";
		if(is_numeric($val) && $fld != "Ins.Policy"){

			$this->db_obj->qry = "SELECT $strFields FROM patient_data WHERE id = $val";

			$result = $this->db_obj->get_resultset_array();
			
			foreach($result as $key=>$arr){
				$result[$key]['address'] = ($arr['street'] != "" && $arr['street'] != NULL)?$arr['street']:"";
				$result[$key]['address'] .= ($result[$key]['address'] != "")?", ":"";
				
				$result[$key]['address'] .= ($arr['street2'] != "" && $arr['street2'] != NULL)?$arr['street2']:"";
				$result[$key]['address'] .= ($result[$key]['address'] != "")?", ":"";
				
				$result[$key]['address'] .= ($arr['city'] != "" && $arr['city'] != NULL)?$arr['city']:"";
				$result[$key]['address'] .= ($result[$key]['address'] != "")?", ":"";
				
				$result[$key]['address'] .= ($arr['state'] != "" && $arr['state'] != NULL)?$arr['state']:"";
				
				$result[$key]['address'] .= ($arr['postal_code'] != "" && $arr['postal_code'] != NULL)?$arr['postal_code']:"";
				
				$pt_appt = $this->get_pt_appt($arr['id']);
				
				if($pt_appt['appt_dt_time']!="")
				{
					$appt_data = $pt_appt['phy_init_name'].' / '.$pt_appt['appt_dt_time'].' / '.$pt_appt['facility_name'];
				}else{
					$appt_data = 'N/A';	
				}
				$result[$key]['appt_data'] = $appt_data;
				$realpath = realpath(dirname(__FILE__)."/../../interface/main/uploaddir".$arr['p_imagename']);
				if($arr['p_imagename'] != "" && $realpath !="")
				$result[$key]['p_imagename'] =  $GLOBALS['php_server']."/interface/main/uploaddir".$arr['p_imagename'];
				else
				$result[$key]['p_imagename'] = '';
			}
			return $result;
		}
	
		if($status==""){
			$status="Active";
		}
		if($fld == "Resp.LN"){
			$this->db_obj->qry = "select $strFields from patient_data left join resp_party on
					patient_data.id = resp_party.patient_id where
					resp_party.lname = '$val'";
		}
		else if($fld == "Ins.Policy"){
			/*$this->db_obj->qry = "SELECT
				insurance_data.policy_number,	
				patient_data.fname,patient_data.pid,patient_data.lname,patient_data.postal_code,
				patient_data.street,patient_data.phone_home,patient_data.ss,patient_data.DOB,patient_data.id
				FROM insurance_data 
				INNER JOIN patient_data ON insurance_data.pid = patient_data.id
				WHERE insurance_data.policy_number LIKE '$val%'
				GROUP BY patient_data.id	
				ORDER BY patient_data.fname";*/
			$this->db_obj->qry = "SELECT
				$strFields
				FROM insurance_data 
				INNER JOIN patient_data ON insurance_data.pid = patient_data.id
				WHERE insurance_data.policy_number LIKE '$val%'
				GROUP BY patient_data.id	
				ORDER BY patient_data.fname";
		}
		else{
			
			if(($fld != 'Nothing') && ($fld != 'LastFirstName') && ($fld != 'phone')){
				$val = ($fld != "id") ? $val."%" : $val;
				$this->db_obj->qry = "select $strFields from patient_data where $fld like '$val' 
						AND patientStatus='$status' order by fname";

			}else if($fld == 'LastFirstName'){
				$searchArr = preg_split("/(,|;)/",$val);
				$val1 = trim($searchArr[0]);
				$val2 = trim($searchArr[1]);
				$val3 = trim($searchArr[2]);
				if(empty($val3) == false){
					$this->db_obj->qry = " and sex like '$val3%'";
				}
				$this->db_obj->qry = "select $strFields from patient_data where lname like '$val1%' 
						AND fname  like '$val2%' AND patientStatus='$status'  $qry
						order by fname";
			}else if($fld != 'phone'){
				$this->db_obj->qry = "select $strFields from patient_data where (phone_home like '$val%' OR phone_biz like '$val%' 
					OR phone_contact like '$val%' OR phone_cell like '$val%')  AND patientStatus='$status' order by fname";            
			}
		}	
		//echo $this->db_obj->qry;die();	
		$result = $this->db_obj->get_resultset_array();
		
		foreach($result as $key=>$arr){
			$result[$key]['address'] = ($arr['street'] != "" && $arr['street'] != NULL)?$arr['street']:"";
			$result[$key]['address'] .= ($result[$key]['address'] != "")?", ":"";
			
			$result[$key]['address'] .= ($arr['street2'] != "" && $arr['street2'] != NULL)?$arr['street2']:"";
			$result[$key]['address'] .= ($result[$key]['address'] != "")?", ":"";
			
			$result[$key]['address'] .= ($arr['city'] != "" && $arr['city'] != NULL)?$arr['city']:"";
			$result[$key]['address'] .= ($result[$key]['address'] != "")?", ":"";
			
			$result[$key]['address'] .= ($arr['state'] != "" && $arr['state'] != NULL)?$arr['state']:"";
			
			$pt_appt = $this->get_pt_appt($arr['id']);
			if($pt_appt['appt_dt_time']!="")
			{
				$appt_data = $pt_appt['phy_init_name'].' / '.$pt_appt['appt_dt_time'].' / '.$pt_appt['facility_name'];
			}else{
				$appt_data = 'N/A';	
			}
			$result[$key]['appt_data'] = $appt_data;
			//.$arr['p_imagename']
			$realpath = realpath(dirname(__FILE__)."/../../interface/main/uploaddir".$arr['p_imagename']);
			if($arr['p_imagename'] != "" && $realpath!="")
			$result[$key]['p_imagename'] = $GLOBALS['php_server']."/interface/main/uploaddir".$arr['p_imagename'];
			else
			$result[$key]['p_imagename'] = '';
		}
		return $result;		
	}
	function getPatAgeYear($patId){
		$sql = "SELECT DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(dob, '00-%m-%d')) AS ageYear FROM `patient_data` WHERE id = '".$patId."'";
		$result = imw_query($sql);
		$row = imw_fetch_row($result);
		$age = "";
		$ageYear = $row[0];
		if($ageYear >= 1)
		{
			$age = $ageYear;
		}
	
		return $age;
	}
	
	public function getFindBy($search)
	{
	   $genderSearch = "";
	   $arrSearch = explode(";",$search);
	   $search = trim($arrSearch[0]);
	   $genderSearch = trim($arrSearch[1]);
	   if(strtoupper($genderSearch) == "M"){
			$genderSearch = "Male";
	   }
	   elseif(strtoupper($genderSearch) == "MALE"){
			$genderSearch = "Male";
	   }
	   elseif(strtoupper($genderSearch) == "F"){
			$genderSearch = "Female";
	   }
	   elseif(strtoupper($genderSearch) == "FEMALE"){
			$genderSearch = "Female";
	   }
	   
	   $search = trim($search);    
	   $retVal = "Last";
	   $ptrnSSN = '/^[0-9]{3}-[0-9]{2}-[0-9]{4}$/'; 
	   $ptrnPhone = '/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/'; 
	   $ptrnDate = '/^((0[1-9])|(1[012]))[-\/](0[1-9]|[12][0-9]|3[01])[-\/]((18|19|20|21)?[0-9]{2})$/'; 
	   if(is_numeric($search))
	   {
		 $retVal = "ID";
	   }
	   elseif(preg_match($ptrnSSN,$search))
	   {
		 $retVal = "SSN";   
	   }
	   elseif(preg_match($ptrnPhone,$search))
	   {
		 $retVal = "phone";  
	   }
	   elseif(preg_match($ptrnDate,$search))
	   {
		 $retVal = "DOB";  
	   }   
	   elseif(preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is',$search))
	   {
		 $retVal = "email";    
	   }
	   elseif(preg_match('/\w+/',$search) && (preg_match('/\d+/',$search)) && (preg_match('/\s*/',$search)))
	   {
		 $retVal = "street";    
	   }
	   
	   elseif(strpos($search,",") !== false)
	   {
		 $retVal = "LastFirstName";
	   }
	   elseif(is_string($search))
	   {
		 $retVal = "Last";  
	   }
	   
	   return $retVal;
	} 
	public function get_pt_appt($pt_id){
		$this->db_obj->qry = "SELECT sa.sa_patient_id, facility.name as facility_name, sp.acronym as acronym, concat(SUBSTRING(users.fname,1,1),SUBSTRING(users.lname,1,1)) as phy_init_name, concat(users.lname,', ',users.fname,' ',users.mname) as phy_name, CONCAT(date_format(sa.sa_app_start_date,'%m-%d-%Y'),' ',date_format(sa.sa_app_starttime,'%h:%i %p')) as appt_dt_time
				FROM schedule_appointments sa JOIN slot_procedures sp ON (sp.id=sa.procedureid) INNER JOIN users ON sa.sa_doctor_id = users.id INNER JOIN facility ON facility.id = sa.sa_facility_id 
				WHERE sa.sa_patient_id = '".$pt_id."' AND sa.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) 
				AND CONCAT(sa.sa_app_start_date, ' ', sa.sa_app_starttime) > NOW() LIMIT 0,1";
		$result = $this->db_obj->get_resultset_array();
		return $result[0];		
	}
	public function getAge($dob=""){
		$yr=0;
		if(!empty($dob)&&($dob!="0000-00-00")){
			$dob_time = strtotime($dob);
			$cur_time = strtotime(date("Y-m-d H:i:s"));
			$yr = floor(($cur_time-$dob_time)/(60*60*24*365));
		}
		return $yr;		
	}
	public function getPtForms(){		 
		
		
		
		include_once($GLOBALS['incdir']."/chart_notes/common/functions.php");
		include_once($GLOBALS['incdir']."/main/main_functions.php");		
		
		//Get Remote server Abbre --
		if(constant("REMOTE_SYNC") == 1){
			$arrRemoteServerAbbr = getServerAbbr();
		}
		//Get Remote server Abbre --		
		
		$arrAllCharts=array();
		$i=1;
		$sql = "SELECT ".		 
						 "DATE_FORMAT(chart_master_table.date_of_service,'%m-%d-%y') AS date_of_service2, ".
						 "chart_master_table.* ".
						 "FROM chart_master_table  ".		 
						 "WHERE chart_master_table.patient_id = '".$this->patient."' ".
						 "AND chart_master_table.delete_status = '0' ".		 
						 "ORDER BY chart_master_table.date_of_service ASC, chart_master_table.id ASC ".		 
						 "";	
		$mysqli = $this->db_obj->db_obj;
		if ($result = $mysqli->query($sql)) {
			
			while ($row = $result->fetch_array()) {
				$id = $row["id"];				
				
				$dbDos=$row["date_of_service"];
				$memoId = $row["memo_id"];
				$providerId = $row["providerId"];
				$arrProviderIntials = getUserFirstName($providerId,2);
				$serverId = $row["serverId"]; //serverId
				
				//chart status
				$chartStatus = ($row["finalize"] == "1") ? "Final" : "Active"; //"Finalized"
				$chartStatus2 = (($row["finalize"] == "1") || (trim($chartStatus) == "Final")) ? "" : $chartStatus ;
				$chartStatus2 = (trim($chartStatus2) == "Final") ? "" : $chartStatus2;
				
				//Chart Notes type
				$typeChart = (!empty($memoId)) ? "Memo Chart Note" : "Chart Note";
				$ptVisitTestTmp = $ptVisitTestTmp2 = "";
				if($row["finalize"] == "1"){
					if(!empty($row["ptVisit"])){
						$ptVisitTestTmp = addslashes($row["ptVisit"]);
					}else if(!empty($row["testing"])){
						$ptVisitTestTmp = addslashes($row["testing"]);
					}else{
						$ptVisitTestTmp = $typeChart;
					}

				}else{
					$ptVisitTestTmp = ($row["ptVisit"] == "CEE") ? "CEE" : "".$ptVisitTestTmp;
				}
				$ptVisitTest = (strlen($ptVisitTestTmp) > 10) ? substr($ptVisitTestTmp,0,10).".." : $ptVisitTestTmp;
				//echo $ptVisitTestTmp." : ".$ptVisitTest;
				
				
				//Chart Note Server Abbr.---			
				$serverAbbr=$initProvId="";
				if(constant("REMOTE_SYNC") == 1){
					if(!empty($arrProviderIntials[1])){ $initProvId = "-".$arrProviderIntials[1]; }
					if(!empty($arrRemoteServerAbbr[$serverId])){	$serverAbbr = "-".$arrRemoteServerAbbr[$serverId]."";	}
				}
				//Chart Note Server Abbr.---
				
				//Purge ---
				if($purge_status==1){
					$purge_statusStrike= "purged";
					$purge_user = "<span class=\"purge_user\">".$arrProviderIntials[1]."</span>";
				}else{
					$purge_statusStrike=$purge_user ="";
				}
				//Purge ---
				
				$arrAllCharts[$dbDos.$i."0"] = array("id"=>$id,
											"DOS"=>$dbDos,
											"name"=> $chartStatus2." ".$ptVisitTest."".$initProvId."".$serverAbbr,
											"purgedby"=>$purge_user	);				
				$i++;							
			}			
			
			/* free result set */
			$result->close();			
		}
		
		
		
		// get chart images
		$sql = "SELECT ".
		   "DATE_FORMAT(chart_note_date, '%m-%d-%y')  AS prev_finalized_date, ".
		   "DATE_FORMAT(chart_note_date, '%m-%d-%Y')  AS prev_finalized_date_FullYear, ".
		   "chart_note_date, doc_title, pdf_url, ".
		   "scan_doc_id ".
		   "FROM ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl ".
		   "WHERE patient_id ='".$this->patient."' AND chart_note = 'yes' ".
		   "ORDER BY chart_note_date ASC, scan_doc_id ASC ";
		$mysqli = $this->db_obj->db_obj;
		if ($result = $mysqli->query($sql)) {
			
			while ($row = $result->fetch_array()) {   
				$id = $row["scan_doc_id"];
				$dbDos = $row["chart_note_date"];
				$docTitle = !empty($row["doc_title"]) ? $row["doc_title"] : $row["pdf_url"] ;
				
				$chartStatus = "Final";//"Finalized";
				$chartStatus2 = "".$docTitle;
				$ptVisitTest="";
				$initProvId=$serverAbbr="";
				
				$chartStatus2 = preg_replace('/[^A-Za-z0-9\-\_]/', '', $chartStatus2);
				$docTitle2 = preg_replace('/[^A-Za-z0-9\-\_]/', '', $docTitle2);

				if(empty($dos)){
					$dos = date("m-d-y");
					$dos2 = date("m-d-Y");
					$dbDos = date("Y-m-d");
				}
			
				$arrAllCharts[$dbDos.$i."1"] = array("id"=>$id,
											"DOS"=>$dbDos,
											"name"=> $chartStatus2." ".$ptVisitTest."".$initProvId."".$serverAbbr,
											"purgedby"=>$purge_user	);
				$i++;							
			
			}			
			
			/* free result set */
			$result->close();			
		}
		
		//Sort Chart
		krsort($arrAllCharts);		
		
		return $arrAllCharts;		
		
	}
	
}
?>