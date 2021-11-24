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
include_once($GLOBALS['srcdir']."/classes/class.erx_functions.php");

$erxFuncObj = New ERXClass();

class patient_erx_data extends emdeon{	
	var $person = '';
	var $personhsi = '';
	var $objDataManage;
	var $objMedHx;
	public function __construct(){
		parent::__construct();
		//require_once(dirname(__FILE__).'/../../interface/common/functions.inc.php');
		$this->get_patient_erxid_hsi();
		require_once(dirname(__FILE__).'/medical_hx.php');
		$this->objMedHx = new medical_hx;
	}
	public function get_patient_erxid_hsi(){
		$this->db_obj->qry = "SELECT patient_eRx_person_hsi,patient_eRx_person
							  FROM patient_erx_prescription 
							  WHERE patient_eRx_Patient_id = '".$this->patient."'";
		$result_phy_arr = $this->db_obj->get_resultset_array();
		$this->person = $result_phy_arr[0]['patient_eRx_person'];
		$this->personhsi = $result_phy_arr[0]['patient_eRx_person_hsi'];
		if($this->person == "" && $this->personhsi == ""){
			$eRx_pres_res = $this->getPatientPrescription();
			$this->person = $eRx_pres_res[0]['patient_eRx_person'];
			$this->personhsi = $eRx_pres_res[0]['patient_eRx_person_hsi'];
		}
	}
	public function getPatientPrescription(){
		global $erxFuncObj;
		$eRx_pres_res = $erxFuncObj->put_patient_erx_details_to_db($this->emdeon_url,$this->eRx_user_name,$this->erx_password,$this->eRx_facility_id,$this->patient,$this->person);
		return $eRx_pres_res;
	}
	public function get_erx_medication(){
		global $erxFuncObj;
		$arrReturn = array();
		$arrMedications = $erxFuncObj->get_patient_erx_prescription($this->emdeon_url,$this->eRx_user_name,$this->erx_password,$this->eRx_facility_id,$this->patient,$this->person,$this->personhsi,'01/01/2009');
		foreach($arrMedications as $medication){
			$noRecord = false;
			$title = (is_object($medication->title))?$medication->title : $medication['title'];
			$erx_id = (is_object($medication->erx_id))?$medication->erx_id : $medication['erx_id'];
			$begdate = (is_object($medication->begdate))?$medication->begdate : $medication['begdate'];
			$begdate=($begdate>1)?$begdate:'';
			$erx_modified_date = (is_object($medication->erx_modified_date))?$medication->erx_modified_date : $medication['erx_modified_date'];
			$sig = (is_object($medication->sig))?$medication->sig : $medication['sig'];
			$comments = (is_object($medication->comments))?$medication->comments : $medication['comments'];
			$destination = (is_object($medication->destination))?$medication->destination : $medication['destination'];
			$user = (is_object($medication->user))?$medication->user : $medication['user'];
			$allergy_status = (is_object($medication->allergy_status))?$medication->allergy_status : $medication['allergy_status'];
			$allergy_status = ($allergy_status == "DISCONTINUED")?"Discontinue":$allergy_status;
			
			$insertArr = array();
			$insertArr['type'] = '4';
			$insertArr['compliant'] = '1';
			$insertArr['pid'] = $this->patient;
			$insertArr['title'] = $title;
			if($begdate != "")$insertArr['begdate'] = $begdate;
			
			
			if($erx_modified_date != "")$insertArr['erx_modified_date'] = $erx_modified_date;
			if($sig != '')$insertArr['sig'] = $sig;
			
			$insertArr['allergy_status'] = $allergy_status;
			$insertArr['eRx_drug_status'] = 1;
			$insertArr['eRx_by'] = $_SESSION['authId'];
			$insertArr['erx_id'] = $erx_id;
			$insertArr['user'] = $user;
			if($comments != "")$insertArr['comments'] = $comments;
			if($destination != "")$insertArr['destination'] = $destination;
			
			$this->db_obj->qry = "select id,erx_modified_date from lists where type = '4' and pid = '".$this->patient."' and erx_id = '$erx_id'";
			$qryRes = $this->db_obj->get_resultset_array();
			//$qryRes =$this->objDataManage->mysqlifetchdata();
			$id = $qryRes[0]['id'];
			if(empty($id) == true){
				$insertArr['date'] = date("Y-m-d H:i:s");
				//commented on request of Taran sir to stop duplication of med data in table/ issue raised by tuft
				//$insertId = $this->objDataManage->AddRecords($insertArr,'lists');
			}
			else{
				if(strtotime($qryRes[0]['erx_modified_date']) <= strtotime($insertArr['erx_modified_date'])){
				//commented on request of Taran sir to stop duplication of med data in table/ issue raised by tuft					
				//$insertId = $this->objDataManage->UpdateRecords($id,'id',$insertArr,'lists');
				}
			}
			$arrReturn[] = $insertArr;
		}
		$arr=$this->objMedHx->get_all_medications();
		foreach($arr as $subArr)
		{
			$subArr['begdate']=($subArr['begdate']!='00-00-0000')?$subArr['begdate']:'';
			$subArr['enddate']=($subArr['enddate']!='00-00-0000')?$subArr['enddate']:'';
			$tempArr[]=$subArr;
		}
		return $tempArr;
	}
	
}

?>