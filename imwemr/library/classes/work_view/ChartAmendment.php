<?php

Class ChartAmendment{
	private $pid, $fid, $uid;
	public function __construct($pid, $fid){
		$this->fid = $fid;
		$this->pid = $pid; //		
		$this->uid = $_SESSION["authId"];
	}

	function hasAmendments(){
		$sql = "SELECT count(amendment_id) as num FROM amendments WHERE patient_id='".$this->pid."' AND form_id = '".$this->fid."' AND deleted_by='0' ";
		$row = sqlQuery($sql);
		if($row != false){
			return ($row["num"] > 0) ? true : false;
		}
		return false;
	}
	
	function getPtAmendments($form_id=0){
		
		$str_amend="";
		if(!empty($form_id)){
			$str_amend = " and form_id='".$form_id."' "; 
		}
		
		$amendQry = "select * from amendments WHERE patient_id='".$this->pid."' AND deleted_by='0' ".$str_amend." order by amend_date desc";
		$fetch_amend = imw_query($amendQry);
		$total_amend = imw_num_rows($fetch_amend);
		$str="";
		while ($row = imw_fetch_array($fetch_amend)) {
			$modify="";
			if ($row['final_amend'] == 'cancel') { 
				$modify=" <span class=\"glyphicon glyphicon-pencil\" title=\"Edit\" onclick=\"op_modify('".$row['amendment_id']."')\"></span>&nbsp;&nbsp;
						<span class=\"glyphicon glyphicon-remove-sign\" title=\"Delete\" onclick=\"op_modify('".$row['amendment_id']."','1')\"></span>
						"; 
			}else{
				$modify="<label class=\"text-danger\">Finalized</label>";
			}
			$str.= "<tr><td>".$row['amend_body']."</td><td>".wv_formatDate($row['amend_date'])."</td><td>".wv_formatDate($row['dos'])."</td><td>".$modify."</td></tr>";			
		}
		
		if(!empty($str)){
			$str="<table class=\"table table-bordered table-striped\"><tr><th>Previous notes</th><th>Date</th><th>DOS</th><th></th></tr>".$str."</table>";
		}else{
			$str="<table class=\"table table-bordered table-striped\"><tr><td>No record found.</td></table>";
		}	
		return $str;
	}
	
	function getPtDosOpt(){
		$str ="";
		$opt = new Patient($this->pid);
		$rez = $opt->getPtChartDos();
		for($i=1;$row=sqlFetchArray($rez);$i++){
			$id = $row["id"];
			$dos = wv_formatDate($row["date_of_service"]);
			
			$str .= "<option value=\"".$id."\">".$dos."</option>";
			
		}
		return $str;
	}

	function loadAmendments(){
		$patient_id = $this->pid;
		$finalize_id = $this->fid;
		$allow_add_amend=1;
		$amend_id=0;
		if(isset($_GET["finalize_id"]) && !empty($_GET["finalize_id"])){ $allow_add_amend=0; $amend_id=$_GET["finalize_id"]; }
		
		$html_prev_amendments="";
		$html_prev_amendments = $this->getPtAmendments($amend_id);
		
		$htm_prev_dos="";
		$htm_prev_dos= $this->getPtDosOpt();
		
		
		
		include($GLOBALS['fileroot']."/interface/chart_notes/view/amendments.php");
	}
	
	function getAmendmentInfo(){
		$amnd_id=$_GET["editId"];
		if(!empty($amnd_id)){
			$sql = "SELECT * FROM amendments WHERE amendment_id='".$amnd_id."'";
			$row = sqlQuery($sql);
		}else{
			$row = array();
		}
		echo json_encode($row);
	}
	
	function saveAmendments(){
	
		if($_POST["op_modify"]=="Delete"){
			if (!empty($_POST['editId'])){
				$del_qry = "update amendments SET deleted_by='".$this->uid."' where amendment_id='".$_POST['editId']."'";				
				$del_amend = sqlQuery($del_qry);
			}
			
		}else{
	
			if(!empty($_POST['finalize'])){
				$finalize = 'ok';
			}else{
				$finalize = 'cancel';
			}
			
			$dos = wv_formatDate($_POST['dos'],0,0,"insert");
			
			if (empty($_POST['editId'])){
				$sql = "INSERT INTO amendments (amend_body, amend_date, patient_id, form_id,final_amend,dos) VALUES 
						('".sqlEscStr($_POST['amend_body'])."','".date("Y-m-d")."', '".$this->pid."', '".$_POST['form_id']."','".$finalize."', '".$dos."')";			
				$Results1 = sqlQuery($sql);		
			}else{
				$sql = "update amendments SET amend_body = '".sqlEscStr($_POST['amend_body'])."', final_amend = '".$finalize."',dos = '".$dos."' where amendment_id ='".$_POST['editId']."' ";
				$Results1 = sqlQuery($sql);
			}
		}
		
		echo 0;
		
	}
	
}

?>