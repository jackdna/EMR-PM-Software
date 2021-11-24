<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: Facility.php
Coded in PHP7
Purpose: This class provides functions for Facility management in work view.
Access Type : Include file
*/
?>
<?php
//Facility.php
class Facility{
	public $facid;
	public function __construct($id=""){
		//parent::__construct();
		
		//Set HQ as facility
		if($id=="HQ"){
			$id=$this->getHqFacility();
		}

		$this->facid = $id;
		
	}

	public function getHqFacility()
	{
		$sql = "SELECT id FROM facility WHERE facility_type = '1' LIMIT 0,1 ";
		//$res = $this->db->Execute($sql);
		$res = imw_exec($sql);
		if($res != false)
		{
			return $res["id"];
		}
		else
		{
			// Fix if No Hq. is selected
			$sql = "SELECT id FROM facility LIMIT 0,1 ";
			//$res = $this->db->Execute($sql);
			$res = imw_exec($sql);
			if($row != false)
			{
				return $res["id"];
			}
		}
		return 0;
	}

	public function getEncounterId()
	{
		$facilityId = $this->getHqFacility();
		$sql = "SELECT encounterId FROM facility WHERE id='".$facilityId."' ";
		//$res = $this->db->Execute($sql);
		$res = imw_exec($sql);
		if($res != false){
			$encounterId = $res["encounterId"];
		}
		
		//get from policies
		$sql = "select Encounter_ID from copay_policies WHERE policies_id = '1' ";
		$row = sqlQuery($sql);
		if($row != false){
			$encounterId_2 = $row["Encounter_ID"];		
		}
		//bigg
		if($encounterId<$encounterId_2){
			$encounterId = $encounterId_2;
		}
		
		//--		
		$counter=0; //check only 100 times
		do{
		
		$flgbreak=1;
		//check in superbill
		if($flgbreak==1){
			$sql = "select count(*) as num FROM superbill WHERE encounterId='".$encounterId."' ";
			$row = sqlQuery($sql);
			if($row!=false && $row["num"]>0){
				$flgbreak=0;
			}	
		}
		
		//check in chart_master_table--
		if($flgbreak==1){
			$sql = "select count(*) as num FROM chart_master_table WHERE encounterId='".$encounterId."' ";
			$row = sqlQuery($sql);
			if($row!=false && $row["num"]>0){
				$flgbreak=0;
			}
		}
		
		//check in Accounting
		if($flgbreak==1){
			$sql = "select count(*) as num FROM patient_charge_list WHERE encounter_id='".$encounterId."'";
			$row = sqlQuery($sql);
			if($row!=false && $row["num"]>0){
				$flgbreak=0;
			}	
		}
		
		if($flgbreak==0) {$encounterId=$encounterId+1;}
		$counter++;
		}while($flgbreak==0 && $counter<100);
		if($counter>=100){ exit("Error: encounter Id counter needs to reset."); }
		//--
		
		
		$sql = "UPDATE copay_policies SET Encounter_ID = '".($encounterId+1)."' WHERE policies_id='1' ";
		$row = sqlQuery($sql);		
		
		//Update
		$sql = "UPDATE facility SET encounterId = '".($encounterId+1)."' WHERE id='".$facilityId."' ";
		//$res = $this->db->Execute($sql);
		$res = sqlQuery($sql);
		
		return $encounterId;
	}
	
	public function getChartTimers(){
		$sql = "SELECT chart_timer,chart_finalize
				FROM facility WHERE id='".$this->facid."' ";
		//$res = $this->db->Execute($sql);
		$res = imw_exec($sql);

		if($res != false){
			$review = $res["chart_timer"];
			$finalize = $res["chart_finalize"];
		}
		
		return array("review"=>$review, "finalize"=>$finalize);
	}
	
	public function getFacilityInfo(){
		$sql = "SELECT *
				FROM facility WHERE id='".$this->facid."' ";
		//$res = $this->db->Execute($sql);
		$res = imw_exec($sql);
		return $res;
	}
	
	//facility name abbr --
	function getFacilityAbbr(){
		$ret="";
		$id=$this->facid;
		if(!empty($id)){
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
		}
		return trim($ret);
	}
	
	function getFacilityName(){
		$ret="";
		$id=$this->facid;
		if(!empty($id)){
			$sql = "SELECT name  FROM facility WHERE id = '".$id."' ";
			$row=sqlQuery($sql);
			if($row != false){
				$ret=$row["name"];
			}
		}
		return trim($ret);
	}
	
	static function getRefDigSetting(){
		$refdig=0;
		$sql = "SELECT refdig FROM facility WHERE facility_type = '1' ORDER BY id limit 0,1 ";
		$row = sqlQuery($sql);
		if($row != false){
			$refdig = $row["refdig"];
		}
		return $refdig;
	}
}

?>