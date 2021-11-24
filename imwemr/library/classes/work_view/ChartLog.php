<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: ChartLog.php
Coded in PHP7
Purpose: This Class provide many basic functions common done in a chart note. 
Access Type : Include file
*/
?>
<?php
//ChartLog
class ChartLog{
	public $fid;
	public $pid;	
	private $uid;
	public function __construct($pid=0, $fid=0){
		$this->fid = $fid;
		$this->pid = $pid; //		
		$this->uid = $_SESSION["authId"];
	}

	function save_log($finzd,$sec="workview"){
		$user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];
		
		$ses_id=session_id();
		$as_pln="";
		$oChartAP = new ChartAP($this->pid, $this->fid);
		$arap = $oChartAP->getArrAP(1);		
		$as_pln=serialize($arap);
		$finalized_now = ((isset($_POST['el_btfinalize_pressed']) && !empty($_POST['el_btfinalize_pressed'])) || $sec=="Autofinalize") ? "1" : "0";
		
		//--
		$sql = "INSERT INTO chart_save_log SET 
				patient_id='".$this->pid."',
				form_id='".$this->fid."',
				user_id='".$this->uid."',
				dttime='".wv_dt('now')."',
				section='".$sec."',
				logged_user_type='".$user_type."',
				as_pln='".sqlEscStr($as_pln)."',
				sess_id='".$ses_id."',
				finalized='".$finzd."',
				finalized_now='".$finalized_now."'
				";
		$r = sqlQuery($sql);
		//--	
	}
	
	function get_ap_tbl($ar){
		$tr="";
		if(count($ar) > 0){
			foreach($ar as $k => $arv){
				$as = nl2br($arv["assessment"]);
				$pln = nl2br($arv["plan"]);
				$ne = !empty($arv["ne"]) ? "Yes" : "";
				$resolve = !empty($arv["resolve"]) ? "Yes" : "";
				$eye = $arv["eye"];
				
				if(!empty($as) || !empty($pln) || !empty($ne) || !empty($resolve) || !empty($eye)){
				$tr .="<tr valign=\"top\">
						<td>".$as."</td>
						<td>".$pln."</td>
						<td>".$ne."</td>
						<td>".$resolve."</td>
						<td>".$eye."</td>
					</tr>";
				}	
				
			}
			$tr = "<table width=\"100%\">
				<tr>
					<th>Assess</th>
					<th>Plan</th>
					<th>NE</th>
					<th>Resolve</th>
					<th>Eye</th>
				</tr>
				".$tr."</table>";
			
		}
		return $tr;
	}
	
	function get_log(){
		$html="";
		$sql = "SELECT * FROM chart_save_log where patient_id='".$this->pid."' AND form_id='".$this->fid."' ORDER BY id DESC ";
		$rez = sqlStatement($sql);
		$tr="";
		for($i=0; $row = sqlFetchArray($rez); $i++){
			$usrnm=$dttm="";
			$uid= $row["user_id"];
			$ousr = new User($uid);
			$usrnm = $ousr->getName(3);
			$utyp = $ousr->get_user_type_nm($row["logged_user_type"]);
			$strap = $row["as_pln"];
			$ar_ap_pln = unserialize($strap);
			$as_pln  = $this->get_ap_tbl($ar_ap_pln);
			$sess_id= $row["sess_id"];
			$finalze= ($row["finalized"]) ? "Yes" : "No";
			$dttm = $row["dttime"];
			$finalze_now = !empty($row["finalized_now"]) ? "Yes" : "No";
			
			
			$tr.="<tr valign=\"top\"  ><td>".$dttm."</td>
				<td>".$usrnm."</td>				
				<td>".$utyp."</td>
				<td>".$as_pln."</td>
				<td>".$finalze."</td>
				<td>".$finalze_now."</td>
				<td>".$sess_id."</td>
				</tr>
				";
		}
		
		if(!empty($tr)){
		$tr="<table width=\"100%\"><tr>
				<th>Time</th>
				<th>User</th>				
				<th>Role</th>
				<th width=\"50%\">Assess. Plan</th>
				<th>Finalized</th>
				<th title=\"Finalized Now\">Fin. Now</th>
				<th>Session</th>
				</tr>".$tr."</table>";
		}else{$tr="No record found.";}		
		return $tr;
	}

	function main(){
		if(isset($_POST["elem_sub"])){
	
			//
			if(!empty($_POST["elem_frmid"])){
				$prs = " and c1.id='".$_POST["elem_frmid"]."' ";
			}
			
			$dos_in = wv_formatDate($_POST["elem_dos"],0,0,'insert');
			
			
			$sql = "SELECT c1.patient_id, form_id, date_of_service FROM chart_master_table c1
					LEFT JOIN chart_assessment_plans c2 ON c1.id=c2.form_id	
					where c1.patient_id='".$_POST["elem_pt"]."' and date_of_service='".$dos_in."' ".$prs;
			//echo $sql;		
			$rez=sqlStatement($sql);
			for($i=1; $row=sqlFetchArray($rez);$i++){
				extract($row);
				
				echo " <b>Patient:</b> $patient_id, <b>FormId:</b> $form_id, <b>DOS:</b> $date_of_service <br/><br/>";
				
				if(!empty($patient_id)){$this->pid = $patient_id;} 
				if(!empty($form_id)){$this->fid = $form_id;}
				
				$data = $this->get_log();
				echo $data;
				
				/*
				$objChartAP = new ChartAP($patient_id, $form_id);
				$arr = $objChartAP->getArrLog();
				
				if(count($arr)>1){
					foreach($arr as $key => $val){
						echo "<br/>chart note saved on <font color='blue'>".$val["dt_time"]."</font> by <font color='green'>".$val["usr"]." - ".$val["usrId"]."</font>";				
					}			
				}
				*/
				
				echo "<br/><br/>";
				//$oChart_notes
				
			
			}
		}	
	}
	
	function get_finalize_dates(){
		$flg_skip_first=0;
		$ret = array();
		$sql = "SELECT user_id, dttime FROM chart_save_log where patient_id='".$this->pid."' AND form_id='".$this->fid."' AND finalized_now='1' AND finalized_now='1' ORDER BY id ASC ";		
		$rez = sqlStatement($sql);
		if(imw_num_rows($rez)>1){ //if multiple finalized: only then display dates
		for($i=0; $row = sqlFetchArray($rez); $i++){
			if(!empty($flg_skip_first)){
			$user_id = $row["user_id"];
			$ret[$user_id][] =  $row["dttime"];
			}
			$flg_skip_first=1;
		}
		}
		return $ret;
	}
	
}//End Class

?>