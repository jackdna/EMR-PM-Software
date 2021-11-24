<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: pnReports.php
Coded in PHP7
Purpose: This class provides management of Operative notes .
Access Type : Include file
*/
?>
<?php
class PnReports{
	private $tbl;
	private $db;	
	function __construct(){
		$this->db = $GLOBALS['adodb']['db'];
		$this->tbl = "pn_reports";
	}
	
	
	//Delate Records
	public function deleteRecord($id){
		if(!empty($id)){
			//$sql = "DELETE FROM ".$this->tbl." WHERE pn_rep_id='".$id."' ";
			$sql = "UPDATE ".$this->tbl." SET status='1' WHERE pn_rep_id='".$id."' ";
			$res = sqlQuery($sql); 
			return ($res !== false) ? 0 : 1;
		}
		return 0;
	}

	//Activate Records
	public function activateRecord($id){
		if(!empty($id)){
			//$sql = "DELETE FROM ".$this->tbl." WHERE pn_rep_id='".$id."' ";
			$sql = "UPDATE ".$this->tbl." SET status='0' WHERE pn_rep_id='".$id."' ";
			$res = sqlQuery($sql); 
			return ($res !== false) ? 0 : 1;
		}
		return 0;
	}
	
	//Insert Records
	public function insertRecord($tData){
		$sql = "INSERT INTO ".$this->tbl.
			 " (pn_rep_id, patient_id, form_id, txt_data, ".
				"signature, pn_rep_date, status, tempId, opid, chart_procedure_id ) ".
			 "VALUES ".
			 " ( NULL, '".$tData["ptId"]."', '".$tData["formId"]."', '".sqlEscStr($tData["txtData"])."', ".
				"'".sqlEscStr($tData["sign"])."', '".wv_formatDate($tData["date"],0,3,"insert")."', '".$tData["status"]."', ".
				"'".$tData["tempId"]."','".$tData["opid"]."', '".$tData["chart_procedure_id"]."' ) ";		
		$res = sqlInsert($sql);
		if($res !== false){
			return $res;
		}
		return 0;
	}
	
	//Update Records
	public function updateRecord($edId, $tData){
		$sql = "UPDATE ".$this->tbl." SET ".
			 "patient_id = '".$tData["ptId"]."', ".
			 "form_id = '".$tData["formId"]."', ".
			 "txt_data = '".sqlEscStr($tData["txtData"])."', ".
			 "signature = '".sqlEscStr($tData["sign"])."', ".
			 "pn_rep_date = '".wv_formatDate($tData["date"],0,3,"insert")."', ".
			 "status = '".$tData["status"]."', ".
			 "tempId = '".$tData["tempId"]."' ".
			 "WHERE pn_rep_id = '".$edId."' ";
		$res = sqlQuery($sql); 
		return ($res !== false) ? 0 : 1;
	}
	
	//Record Info
	public function getRecordInfo($id){
		$arr = array();
		$sql = "SELECT * FROM ".$this->tbl." WHERE pn_rep_id = '".$id."' ";	
		
		$res = sqlQuery($sql); 
		if($res !== false){
			//$res = $res->GetArray();
			return $res;
		}
		return false;
	}	
	
	public function getChartProcEditId($id,$tempId=0,$sel=" * "){
		$sql = "SELECT $sel  FROM ".$this->tbl." WHERE chart_procedure_id  = '".$id."' ";
		if(!empty($tempId)){ $sql.=" AND tempId='".$tempId."'  ";  }
		
		$res = sqlQuery($sql); 
		if($res != false){
			//$res = $res->GetArray();
			return $res;
		}
		return 0;
	}
	
	//Get Pt Reports
	public function getPtReports($ptId){
	
		$arrTra = array();
		$arr = array();
		$sql = "SELECT ".
			 "pn.pn_rep_date, pn.tempId, pn.pn_rep_id,pn.status,pn.opid,date_format(pn.pn_rep_date,'".get_sql_date_format('','Y','-')." %h:%i %p') as pn_tim, pn.sc_emr_template_name, pn.sc_emr_operative_report_id, pn.chart_procedure_id, ".
			 " concat(u.lname,', ',u.fname,' ',u.mname) AS operator_name ".
			 " FROM ".$this->tbl." pn ".
			 " LEFT JOIN users u ON(u.id = pn.opid)".
			 " WHERE patient_id = '".$ptId."' ".
			 //"AND status='0' ".	
			 "ORDER BY pn_rep_id DESC ";
		$res = sqlStatement($sql); 
		if($res !== false){
			while($row=sqlFetchArray($res)){
				
				//$tdate = $res->UserDate($res->fields["pn_rep_date"],"m-d-Y");
				$tdate = get_date_format(date('Y-m-d',strtotime($row["pn_rep_date"])));
				//$tTime = $res->UserTimeStamp($row["pn_rep_date"],"H:iA");
				$tTime = $row["pn_tim"];
				$tempId = $row["tempId"];
				$edId = $row["pn_rep_id"];				
				$stt = $row["status"];
				$opid = $row["opid"];
				$operator_name = stripslashes($row["operator_name"]);
				$sc_emr_template_name  = $row["sc_emr_template_name"];
				$sc_emr_operative_report_id  = $row["sc_emr_operative_report_id"];
				
				//Check procedure notes related OP Note
				//*
				$chart_procedure_id = $row['chart_procedure_id'];
				if(!empty($chart_procedure_id) && !isProcedureNoteFinalized($chart_procedure_id)){
					//$res->MoveNext();
					continue;
				}
				//*/
				//--				

				if(empty($stt)){
					$arr[$tdate][] = array($edId, $tempId, $opid, $tTime,$sc_emr_template_name,$sc_emr_operative_report_id,$operator_name);
				}else{
					$arrTra[$tdate][] = array($edId, $tempId, $opid, $tTime,$sc_emr_template_name,$sc_emr_operative_report_id,$operator_name);
				}
				//$res->MoveNext();
			}			
		}	
		
		return array($arr,$arrTra);
	}
	
	//Load Reports
	public function loadReports($id){
		
		$sql = "SELECT txt_data FROM ".$this->tbl." WHERE pn_rep_id = '".$id."' ";		
		$res = sqlQuery($sql); 
		if($res !== false){
			$elem_pnData = $res["txt_data"];
		}
		
		//Write on a file for pdf
		$TData = "";
		$TData .= $elem_pnData;
		
		/*
		//Save image of Signature
		//$id = ;
		$tblName = "pn_reports";
		$pixelFieldName = "signature";
		$idFieldName = "pn_rep_id";
		$imgPath = "";
		$saveImg = "2";
		include(dirname(__FILE__)."/../../main/imgGd.php");
		$TData .= "<img src=\"".realpath(dirname(__FILE__)."/../../common/html2pdf/tmp/".$gdFilename)."\" alt=\"alt image\">";
		*/		
		
		//$pth = dirname(__FILE__)."/../../common/html2pdf/pn_rep_pdfFile.html";
		//$handle = fopen($pth, "w");
		
		//
		
		/*
		if($handle){
			
			//$err = fputs($handle,$TData);
			//fclose($handle);
			$err = "0";
		}else{
			$err = "0";
		}
		*/
		
		$fp = "/tmp/pn_rep_pdfFile.html";
		$oSaveFile = new SaveFile($_SESSION["authId"],1);
		$resp = $oSaveFile->cr_file($fp,$TData);

		if($err){			
			header("Location: ".$GLOBALS['webroot']."/library/html_to_pdf/createPdf.php?page=5&font_size=12&file_location=".$resp."");
			exit();
		}		
	}
}

?>