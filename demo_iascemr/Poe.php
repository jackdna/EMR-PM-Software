<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: Poe.php
Purpose: This class defines POE functionality from iDoc.
Access Type : Include file
*/
?>
<?php
class Poe extends manageData{

	private $db;
	private $pId;
	private $eId;
	private $arPoeId;
	private $arPoeCpt;

	public function __construct($pid="",$eid=""){
		$this->pId = $pid;
		$this->eId = $eid;
		$this->arPoeId = array();
		$this->arPoeCpt = array();	
	}

	public function setEId($id){
		$this->eId = $id;
	}

	public function setPtId($id){
		$this->pId = $id;
	}

	public function isPoeCode($cpt){
		$ret = false;
		if(!empty($cpt)){
			$cpt = trim($cpt);
			$sql = "SELECT elem_poe FROM cpt_fee_tbl WHERE cpt_prac_code = '".$cpt."' AND delete_status = '0' ";
			$row = $this->sqlQuery($sql); //$this->db->Execute($sql) or die("Error In Poe :: isPoeCode");
			if($row != false){
				if(!empty($row["elem_poe"])){
					$ret = $row["elem_poe"];
					$this->arPoeId[] = $ret;
					$this->arPoeCpt[] = $cpt;
				}
			}
		}
		return $ret;
	}

	private function getDOS(){
		$ret = false;
		if(!empty($this->eId)){
			//Super bill
			$sql = "SELECT dateOfService FROM superbill WHERE encounterId = '".$this->eId."' and del_status='0'";
			$row = $this->sqlQuery($sql); //$this->db->Execute($sql) or die("Error In Poe :: getDOS-1");
			if($row != false){
				$ret = !empty($row["dateOfService"]) ? $row["dateOfService"] : false;
			}
			//Patient Charge List
			if($ret == false){

				$sql = "SELECT date_of_service FROM patient_charge_list WHERE del_status='0' and encounter_id = '".$this->eId."' ";
				$row = $this->sqlQuery($sql); //$this->db->Execute($sql) or die("Error In Poe :: getDOS-2");
				if($row != false){
					$ret = !empty($row["date_of_service"]) ? $row["date_of_service"] : false;
				}
			}

		}
		return $ret;
	}
	
	function get_poe_days($poe_id, $poe_cpt){
		if(!empty($poe_cpt)){
			$sql = "SELECT elem_poe FROM cpt_fee_tbl WHERE cpt_prac_code='".$poe_cpt."' AND delete_status != '1' ";
			$row = $this->sqlQuery($sql);
			$poe_id_n=0;
			
			if($row!=false){
				if(!empty($row["elem_poe"])) $poe_id_n=$row["elem_poe"];
			}
			if(!empty($poe_id_n)){ $poe_id = $poe_id_n; }
		}
		
		list($numD,$poeMsg,$strShow) = $this->getReviewPrd($poe_id);
		return array($numD,$poe_id);
	
	}

	private function getDOSSaved($flgNotCurEnc=0){
		$cpid="";
		$poe_eId = "";
		$dateOfService = "";
		$poe_id ="";
		$phrse_no_cur_enc="";
		if(!empty($flgNotCurEnc) && !empty($this->eId)){ $phrse_no_cur_enc=" AND c1.poe_eId != '".$this->eId."'  "; }
		$sql = "SELECT c1.id, c1.poe_eId, c1.poe_id, c1.poe_cpt, c2.dateOfService, c3.date_of_service ".
				//"c4.poe_other_days, c4.poe_days ".
			   "FROM chart_pt_data c1 ".
			   "LEFT JOIN superbill c2 ON c2.encounterId = c1.poe_eId ".
			   "LEFT JOIN patient_charge_list c3 ON c3.encounter_id = c1.poe_eId ".
			   //"LEFT JOIN poe_messages c4 ON c4.poe_messages_id = c1.poe_id ".
			   "WHERE c1.patient_id = '".$this->pId."' ".$phrse_no_cur_enc." AND (c3.del_status='0' OR c3.del_status IS NULL) 
			   AND (c2.dateOfService IS NOT NULL OR c3.date_of_service IS NOT NULL)
			   ";
		$row = $this->sqlQuery($sql);
		if($row != false){
			$cpid = $row["id"];
			$poe_eId = $row["poe_eId"];
			$dateOfService = $row["dateOfService"];
			if(empty($dateOfService)) $dateOfService = $row["date_of_service"];
			$poe_id=$row["poe_id"];
			$poe_cpt=$row["poe_cpt"];
			//$poe_days=!empty($row["poe_other_days"]) ? $row["poe_other_days"]: $row["poe_days"];
			list($poe_days, $poe_id)=$this->get_poe_days($poe_id, $poe_cpt);			
			
		}
		return array($cpid, $poe_eId, $dateOfService,$poe_id,$poe_days);
	}

	private function getReviewPrd($poeId){

		$poe_days=$poe_pat_message=$strOpen="";
		$sql = "SELECT
				poe_days,poe_other_days,poe_pat_message,
				poe_scheduler,poe_medical,poe_billing
				FROM poe_messages
				WHERE poe_messages_id='".$poeId."'
				LIMIT 0,1 ";
		$row = $this->sqlQuery($sql);
		if($row != false){

			if(!empty($row["poe_other_days"])){
				$poe_days=$row["poe_other_days"];
			}else{
				$poe_days=$row["poe_days"];
			}

			$poe_pat_message=$row["poe_pat_message"];
			$strOpen .= ($row["poe_scheduler"]==2) ? "1," : "";
			$strOpen .= ($row["poe_medical"]==2) ? "2," : "";
			$strOpen .= ($row["poe_billing"]==2) ? "3," : "";
		}
		return array($poe_days,$poe_pat_message,$strOpen);
	}

	private function getDays2Go($dt,$numd){

		$dateSv = new DateTime($dt);
		$dateSv->modify("+".$numd." day");
		$dateSv2 = $dateSv->format('Y-m-d');
		$dateCur2 = date("Y-m-d");
		$intD = round((strtotime($dateSv2) - strtotime($dateCur2)) / (60 * 60 * 24));
		//echo "<br/>".$dateCur2." - ".$dt." - ".$numd." - ".$dateSv2." :: ".
		$numDRem = ($intD > 0) ? $intD : 0;
		return $numDRem;
	}

	private function getPoeId2Attach(){
		$tmp = count($this->arPoeId);

		if($tmp>=1){
			//GET POE With Max days
			$mxId = "";
			$str = implode(", ",$this->arPoeId);
			$sql = "SELECT poe_messages_id,poe_days,poe_other_days FROM poe_messages WHERE poe_messages_id IN (".$str.") ";
			$res = imw_query($sql);
			if($res!=false){
				$mxDys = 0;
				while($row=imw_fetch_array($res)){
					$chk = !empty($row["poe_other_days"]) ? $row["poe_other_days"] : $row["poe_days"];
					if(!empty($chk) && ($chk > $mxDys)){
						$mxDys = $chk;
						$mxId = $row["poe_messages_id"];
					}
					//$res->MoveNext();
				}
			}

			return array($mxId,$mxDys);
		}else{
			return "";
		}
	}

	//check if POE is added previously, then remove it
	function rem_poe(){
		$sql = "UPDATE chart_pt_data SET poe_eId = '0', poe_id=0, poe_cpt='' WHERE patient_id = '".$this->pId."' AND poe_eId = '".$this->eId."' ";
		$row = $this->sqlQuery($sql);
	}

	public function setPoeEnId(){

		//Get Poe Id
		list($poeId,$poedays)  = $this->getPoeId2Attach();
		if(empty($poeId)){
			$this->rem_poe();
			return;
		}

		//get cpt code from poeid
		$indx = array_search($poeId, $this->arPoeId);
		if($indx!==false){
			$cpt = $this->arPoeCpt[$indx];
			if(!empty($cpt)){ $poeId=""; }
		}
		
		//GET Cur encounter Id Dos
		$dtDos_curr = $this->getDOS();

		//Get Already entered Poe Encounter id DOS, if any
		$arrPoeSaved = $this->getDOSSaved();
		$cpid = $arrPoeSaved[0];
		$eid_Saved = $arrPoeSaved[1];
		$dtDos_Saved = $arrPoeSaved[2];
		$poeId_Saved = $arrPoeSaved[3];
		$poeDays_Saved = $arrPoeSaved[4];

		// Bigger Dates will be Saved
		//if( ($eid_Saved != $this->eId  && $dtDos_curr >= $dtDos_Saved) || ($eid_Saved == $this->eId && $poeId_Saved!=$poeId)){
		//if($dtDos_curr >= $dtDos_Saved && $poeId_Saved!=$poeId){
		$curenddate = $this->getDays2Go($dtDos_curr,$poedays);
		$savedenddate = $this->getDays2Go($dtDos_Saved,$poeDays_Saved);

		if($curenddate >= $savedenddate){
			//Set Cur Encounter Id
			if(!empty($cpid)){
				$sql = "UPDATE chart_pt_data SET poe_eId = '".$this->eId."', poe_id='".$poeId."', poe_cpt='".$cpt."' WHERE patient_id = '".$this->pId."' ";
			}else{
				$sql = "INSERT INTO chart_pt_data SET poe_eId = '".$this->eId."', poe_id='".$poeId."', patient_id = '".$this->pId."', poe_cpt='".$cpt."' ";
			}
			$row = $this->sqlQuery($sql);
		}
	}
	
}

?>