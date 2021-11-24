<?php

class PrognessNote{
	public $pid, $fid;
	public function __construct($pid,$fid=""){
		$this->pid = $pid;
		$this->fid = $fid;
		$this->tbl = "chart_progress_notes";
	}
	
	function getLastRecord(){
		$elem_patientId = $this->pid;
		//get previous with gray
		$sql = " SELECT c2.notes, c2.usr_id, form_id FROM chart_master_table c1 
				INNER JOIN chart_progress_notes c2 ON c1.id = c2.form_id
				WHERE c1.patient_id='".$elem_patientId."'
				ORDER BY date_of_service DESC, c1.id DESC
				LIMIT 0, 1
				";
		return sqlQuery($sql);
	}
	
	function get_all_cn_progress_notes($fid="", $fid_c=""){
		$pId = $this->pid;
		$str="";
		$sql = " SELECT DATE_FORMAT(c1.date_of_service, '".get_sql_date_format()."') AS date_of_service, 
				c1.id FROM chart_master_table c1
				INNER JOIN chart_progress_notes c2 ON c1.id = c2.form_id
				WHERE c1.patient_id='".$pId."'
				ORDER BY date_of_service DESC, c1.id DESC ";
		$rez=sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){		
			if(!empty($row["date_of_service"]) && !empty($row["id"])){			
				if(!empty($fid_c) && $fid_c==$row["id"]){ continue; }
				$sel = (!empty($fid) && $fid==$row["id"]) ? " selected " : "";
				$str .="<option value=\"".$row["id"]."\" ".$sel." >".$row["date_of_service"]."</option>";
			}
		}
		
		return $str;
	}
	
	function print_cn_progress_notes($pid="",$fid="", $dt=""){
		
		if(empty($pid)){ $pid=$this->pid; }
		if(empty($fid)){ $fid=$this->fid; }
	
		
		$str_table="";
		if(!empty($pid) && !empty($fid)){
			$sql = "SELECT ".
				 " notes, usr_id, form_id  ".
				 " FROM chart_progress_notes WHERE patient_id='".$pid."' AND form_id='".$fid."' ";		
			$row=sqlQuery($sql);
		}else{
			$row=false;	
		}
		if($row!=false){
			extract($row);
			
			$notes_val=$notes;			
			$ousr = new User($usr_id);
			$user_nm = $ousr->getName(1); // getUserFirstName($usr_id,1);
			
			if(!empty($user_nm)){ $user_nm=" - ".$user_nm; }
			
			$str_dos="";
			if(!empty($dt)){
				$oCN = new ChartNote($pid,$fid);
				$ardos = $oCN->getDOS(); //  print_getChartDetails($pid,$fid);
				$str_dos = " DOS: ".$ardos; 
			}
			
				$placeholders = array('>o','<o','<1', '<2', '<3', '<4','<5', '<6', '<7', '<8','<9','<0');
				$replace_vals = array('&gt;o','&lt;o','&lt;1','&lt;2','&lt;3','&lt;4','&lt;5', '&lt;6', '&lt;7', '&lt;8','&lt;9','&lt;0');
				$notes_val=str_replace($placeholders,$replace_vals,$notes_val);
				$notes_val=nl2br(htmlentities($notes_val));
				$str_table='<table style="width:100%;" class="border" cellpadding="0" cellspacing="0">
									<tr><td class="tb_heading">Progress Note '.$user_nm.$str_dos.'</td></tr>
									<tr><td style="width:100%;padding-left:4px;" class="">'.$notes_val.'</td></tr>
									<tr><td style="height:5px"></td></tr>
								  </table>';

		}
		if(!empty($dt)){
			return $str_table;
		}else{
			echo $str_table;
		}
	}
	
	function print_cn_progress_notes_all(){
		$pid = $this->pid;
		$str="";
		$sql = "SELECT ".
			 " form_id  ".
			 " FROM chart_progress_notes WHERE patient_id='".$pid."' ".
			 " ORDER BY form_id DESC ";
		
		$rez=sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){
			$fid = $row["form_id"];
			$str.=$this->print_cn_progress_notes($pid,$fid, 1);
		}
		return $str;	
	}
	
	function print_note(){
		$content = $this->print_cn_progress_notes_all();
		$oPrinter = new Printer($this->pid, $this->fid);
		$oPrinter->print_page($content,"Progress Note","pr_note","","","");
	}
	
	function get_cn_progess_notes_handler(){
		if(isset($_POST["op"]) && $_POST["op"]=="save"){
			$elem_formId_cur=$elem_formId= $this->fid;
			$elem_patientId= $this->pid;
			$txt=trim($_POST["txt"]);
			$op=$_POST["op"];
			$sv_per=$_POST["sv_per"];
		}else{
			$elem_dos=$_GET["elem_dos"];
			$elem_formId_cur=$elem_formId= $this->fid; //$_GET["elem_formId"];
			$elem_patientId= $this->pid; //$_GET["elem_patientId"];
			$sv_per=$_GET["sv_per"];
			$load_prev_prog=$_GET["load_prev_prog"];
			if(!empty($load_prev_prog)){ $elem_formId=$load_prev_prog; $this->fid=$elem_formId; }
			$op=$_GET["op"];
		}
		
		//names
		$oPt = new Patient($elem_patientId);
		$ptnm=$oPt->getName(1); // getPatient_Name($elem_patientId,1);
		$ousr = new User();
		$unm=$ousr->getName(1); //getUserFirstName($_SESSION["authId"],1);	
		
		//
		$cls_ta="active";
		$sql = "SELECT notes, usr_id FROM chart_progress_notes WHERE patient_id='".$elem_patientId."' AND form_id='".$elem_formId."' ";
		$row=sqlQuery($sql);
		if($row==false){
			if($sv_per==1&&empty($load_prev_prog)){
				$row = $this->getLastRecord();
				$cls_ta="inact";
			}
		}
		
		if($row!=false){
			$db_txt = $row["notes"];
			$db_usr=$row["usr_id"];
			$db_form_id=$row["form_id"];
			
			$oUser = new User($db_usr);
			$unm = $oUser->getName(1);
			$oCN = new ChartNote($elem_patientId, $elem_formId);
			$elem_dos = $oCN->getDos();
		}
		
		if(!empty($load_prev_prog)){ $cls_ta="inact"; }
		
		//
		if($op=="print"){
				//include_once(dirname(__FILE__)."/../chart_prognote_print.php");
				$this->print_note();
				
		}else if($op=="save"){
			$sql =""; $sql2="";
			if($cls_ta!="active"){
				if(!empty($txt)){$sql = "INSERT INTO chart_progress_notes SET patient_id='".$elem_patientId."', form_id='".$elem_formId."', ";}
			}else{
				$sql = "UPDATE chart_progress_notes SET ";
				$sql2 = "WHERE patient_id='".$elem_patientId."' AND form_id='".$elem_formId."' ";
			}
			
			if($sql!=""){
				$sql .= " notes='".sqlEscStr($txt)."', usr_id='".$_SESSION["authId"]."' ".$sql2;
				$row=sqlQuery($sql);
			}	
		}else{
			$str_opts = $this->get_all_cn_progress_notes($load_prev_prog, $elem_formId_cur);
			$str_opts="<option value=\"\">Prev. Notes</option>".$str_opts."<option value=\"\">Current Note</option>";
			if(!empty($str_opts)){
				$str_opts="<select id=\"el_all_prg_note\" class=\"form-control minimal\" onchange=\"cn_progess_notes(3)\">".$str_opts."</select>";
				$str_opts=$str_opts."<label style=\"display:none;\" for=\"el_all_prg_note\"></label>";
			}			
		
			$d="";
			$d="<div class=\"hdr purple_bar\" ><label>Progress Notes</label><label>".$ptnm."</label><label>DOS - ".$elem_dos."</label><label>".$unm."</label>".$str_opts."</div>";	
			$d.="<textarea id=\"el_cpn_txt\" name=\"el_cpn_txt\" class=\"form-control ".$cls_ta."\" rows=\"8\">".$db_txt."</textarea>";
			$d.="<div class=\"modal-footer ad_modal_footer\" id=\"module_buttons\">";
			if($sv_per==1 && empty($load_prev_prog)){
			$d.=	"<input type=\"button\" name=\"el_cpn_save\" value=\"Save\" onclick=\"cn_progess_notes(1)\" class=\"btn btn-success\" >";
			}
			
			$d.=	"<input type=\"button\" name=\"el_cpn_print\" value=\"Print\" onclick=\"cn_progess_notes(4)\" class=\"btn btn-success\" >";
			
			
			
			/*if (constant('AV_MODULE')=='YES'){
				$d.=	"<input type=\"button\" id=\"btn_Voice2Text\" class=\"btn btn-success\" value=\"Voice2Text\" onClick=\"top.showVoice2TextTool4RichText('el_cpn_txt',this)\"  />";
			}*/
			$d.=	"<input type=\"button\" name=\"el_cpn_cancel\" value=\"Cancel\" onclick=\"cn_progess_notes(2)\" class=\"  btn btn-danger\" >";
			$d.="</div>";
			echo $d;
		}		
	}
}

?>