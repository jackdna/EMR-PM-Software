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
File: GetSuperBill.php
Purpose: This file provides Superbill data for a patient encounter.
Access Type : Include file
*/
?>
<?php
//GetWVSummery
set_time_limit(900);
include_once(dirname(__FILE__).'/patient_app.php');

class GetSuperBill extends patient_app{
	public $ar_req;
	
	private $patient_id;
	private $form_id;
	private $encounter_id;
	
	public function __construct($patient_id, $form_id){
		parent::__construct();
		$this->patient_id= $patient_id;
		$this->form_id = $form_id;
		$this->encounter_id = $this->getEncounterId();
	}
	
	function getEncounterId(){
		$ret=0;
		$sql = "SELECT encounterId as eid FROM chart_master_table WHERE patient_id = '".$this->patient_id."' AND id='".$this->form_id."' ";
		$row = sqlQuery($sql);
		if($row!=false){
			$ret=$row["eid"];
		}
		return $ret;
	}
	
	function getSuperBill(){
		
		//?reqModule=chart_notes&service=getSuperBill&phyId=209&patId=55603&form_id=40171
		
		if(empty($this->encounter_id)){
			return "";
		}
		
		$arrMainRet=array();
		
		$sql = "SELECT idSuperBill,procOrder,todaysCharges,insuranceCaseId,vipSuperBill,arr_dx_codes FROM superbill ";	
		$sql .= " WHERE encounterId='".$this->encounter_id."' AND patientId = '".$this->patient_id."' ";
		$row=sqlQuery($sql);
		if($row != false){
			$elem_idSuperBill = $row["idSuperBill"];
			$elem_todaysCharges = number_format($row["todaysCharges"],2);
			$vipSuperBill = $row["vipSuperBill"];
			$all_dx_codes_arr=unserialize("".$row["arr_dx_codes"]);
			
			$arrMainRet["Other"]=array("TotalCharges"=>"".$elem_todaysCharges,"VIP"=>"".$vipSuperBill);
			$arrMainRet["DxCodes"]=$all_dx_codes_arr;
			
			//
			$orderBy = " porder  ";
			$sql = "SELECT * FROM procedureinfo 
					WHERE idSuperBill = '".$elem_idSuperBill."' 
					AND delete_status ='0'
					ORDER BY ".$orderBy." ";
			
			$rez = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){
				$cptCode = $row["cptCode"];
				$units = $row["units"];
				
				
				$arrCurDxCodesAll=array();
				$arrMdCode=array();
				for($j=1;$j<=12;$j++){
					$tmpDx = $row["dx".$j];
					if(!empty($tmpDx)){		$arrCurDxCodesAll[] = $tmpDx; }
					
					if($j < 4){						
						$arrMdCode[] = $row["modifier".$j];
					}					
				}
				
				//
				$arrMainRet["CPTData"][]=array("CPT"=>"".$cptCode,"Units"=>"".$units, "DxCodes"=>$arrCurDxCodesAll,"Mod1"=>"".$arrMdCode[0],"Mod2"=>"".$arrMdCode[1],"Mod3"=>"".$arrMdCode[2]);
			
			}	

		}
		
		//put empty  if not exists
		if(!isset($arrMainRet["Other"])){	$arrMainRet["Other"] = array("TotalCharges"=>"","VIP"=>"");}
		if(!isset($arrMainRet["DxCodes"])){ $arrMainRet["DxCodes"]=array(); }
		if(!isset($arrMainRet["CPTData"])){ $arrMainRet["CPTData"][]=array("CPT"=>"","Units"=>"", "DxCodes"=>"","Mod1"=>"","Mod2"=>"","Mod3"=>""); }		
		
		return $arrMainRet;
	
	}
}


?>