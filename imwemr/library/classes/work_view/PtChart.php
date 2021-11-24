<?php
//PtChart
class PtChart{
	private $pid;
	public function __construct($pid){
		$this->pid=$pid; 		
	}
	
	//Get Last Finalized/Previous Form Id
	public function getLFFormId(){
		$id=0;
		$sql =	"SELECT c1.id FROM chart_master_table c1 ".
				//"LEFT JOIN chart_left_cc_history c2 ON c1.id = c2.form_id ".
				"WHERE c1.patient_id='".$this->pid."' ".
				"AND c1.finalize='1' AND c1.delete_status='0' ".
				"ORDER BY c1.date_of_service DESC, c1.id DESC ".
				//"ORDER BY IFNULL(c2.date_of_service,DATE_FORMAT(c1.create_dt,'%Y-%m-%d')) DESC, c1.id DESC ".
				"LIMIT 0,1 ";
		//$res = $this->db->Execute($sql);
		$res = sqlQuery($sql);
		if($res !== false){
			$id = $res["id"];
		}
		return $id;
	}
	
	//Get Drop down of pt charts
	public function get_dd_pt_dos(){
		$ar=array();
		$sql = "SELECT id, date_of_service, create_dt FROM chart_master_table 
				WHERE patient_id='".$this->pid."' AND delete_status='0' AND purge_status='0' AND finalize = '1'
				ORDER BY date_of_service DESC, id DESC  ";
		$rez = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($rez); $i++){
			$dos = $row["date_of_service"];
			if(strpos($dos, "0000-")!==false){
				$dos = $row["create_dt"];
			}
			
			//
			$dos = wv_formatDate($dos);
			
			if(!empty($row["id"])){
				$ar[] = array($row["id"],$dos);
			}
		}
		echo json_encode($ar);
	}
}
?>