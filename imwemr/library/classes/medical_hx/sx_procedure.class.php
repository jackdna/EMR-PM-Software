<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Sx Procedure Class
 Access Type: Indirect Access.
 
*/

include_once 'medical_history.class.php';
class SxProcedure extends MedicalHistory
{
	public $vocabulary = false;
	public $data = false;
	public $pat_relation = false;			//arrPtRel
	public $phrases = false;
	public $obj_review = false;
	
	public function __construct($tab = 'ocular')
	{
		parent::__construct($tab);
		$this->vocabulary = $this->get_vocabulary("medical_hx", "sx_procedures");
		$this->common_phrases();
		$this->obj_review = new CLSReviewMedHx();
	}
	
	public function get_sx_default_view($defv){
		$ret="All";
		$uid = $_SESSION["authId"];
		
		//
		if(!empty($defv)){
			$_SESSION["defSxView"] = $defv;
		}
		
		//
		if(isset($_SESSION["defSxView"]) && !empty($_SESSION["defSxView"])){
			$ret = $_SESSION["defSxView"];
			
		}else{
			$sql = "SELECT proc_display FROM med_proc_dis_setting where del_by='0' AND uid IN ('0', '".$uid."') ORDER BY uid DESC";
			$row = sqlQuery($sql);
			if($row!=false){
				$ret=$row["proc_display"];
			}
		}
		
		return $ret;
	}
	
	public function load_sx_procedure(&$request)
	{
		$return = array();
		extract($request);
		
		$query = "select id,title,type,
								if((DAY(begdate)='00' OR DAY(begdate)='0') && YEAR(begdate)='0000' && (MONTH(begdate)='00' OR MONTH(begdate)='0'),'',
									if((DAY(begdate)='00' OR DAY(begdate)='0') && (MONTH(begdate)='00' OR MONTH(begdate)='0'),date_format(begdate, '%Y'),
										if(MONTH(begdate)='00' OR MONTH(begdate)='0',date_format(begdate,'%Y'),
											if(DAY(begdate)='00' or DAY(begdate)='0',date_format(begdate,'%m-%Y'),
											date_format(begdate,'".get_sql_date_format()."')
								))))as begdate1, begtime, refusal, refusal_reason, refusal_snomed,
								referredby,comments,sites,ccda_code,referredby_id,proc_type,implant_status,procedure_status,assigning_authority_UDI,procedure_type
								from lists where pid = '".$this->patient_id."' and type in (5,6,9) and allergy_status != 'Deleted' order by begdate desc,id desc";
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		
		
		$loop_count = ($cnt >= 5) ? $cnt+1 : 5;
		
		$sx_proc_data_arr = array();
		$sx_exists = commonNoMedicalHistoryAddEdit($moduleName="Surgery",$moduleValue="",$mod="get");
		
		//Default View Setting
		$el_df_vw = $this->get_sx_default_view($request["defVw"]);
		
		$finalResArr = array();
		$pkIdAuditTrailArr = array();
		while( $sxQryRes = imw_fetch_assoc($sql) ) 
		{
			//Default View
			if(!empty($el_df_vw)){
				if($el_df_vw=="All" || ($el_df_vw==$sxQryRes['procedure_type']) || 
					($el_df_vw=="Other" && $sxQryRes['procedure_type']!="Ret" && $sxQryRes['procedure_type']!="GL")){
					
				}else{
					continue;
				}
			}
			
			if($sxQryRes['implant_status'] != '' && isset($request['filter']) && $request['filter'] != '' && $request['filter'] != 'all' && $sxQryRes['implant_status'] != $request['filter']) {
				continue;
			}
			$dataArr = array();
			
			$dataArr['SX_ID'] = $sxQryRes['id'];
			if($dataArr['SX_ID'] != ''){
				$sx_exists = 'disabled';
			}
			$type = $sxQryRes['type'];
			
			$dataArr['SX_TITLE'] = $sxQryRes['title'];
			$dataArr['SX_OCCULAR'] = ($type == 6) ? 'checked' : '';
			$dataArr['SX_BEG_DATE'] = $sxQryRes['begdate1'];
			$dataArr['SX_BEG_TIME'] = $sxQryRes['begtime'];
			$dataArr['SX_REFFERED_BY'] = $sxQryRes['referredby'];
            if($type == 9) { 
                $sg_comments = (str_replace(array('/','\\'), "", $sxQryRes['comments']));
                $dataArr['SX_COMMENTS'] = $sg_comments;
            } else {
                $dataArr['SX_COMMENTS'] = $sxQryRes['comments'];
            }
			$dataArr['MED_SITE'] = $sxQryRes['sites'];
			$dataArr['ccda_code'] =$sxQryRes['ccda_code'];
			$dataArr['referredby_id'] =$sxQryRes['referredby_id'];
			$dataArr['REFUSAL'] = $sxQryRes['refusal'];
			$dataArr['REFUSAL_REASON'] = $sxQryRes['refusal_reason'];
			$dataArr['REFUSAL_SNOMED'] = $sxQryRes['refusal_snomed'];
			$dataArr['proc_type'] = $sxQryRes['proc_type'];
			$dataArr['implant_status'] = $sxQryRes['implant_status'];
			$dataArr['procedure_status'] = $sxQryRes['procedure_status'];
			$dataArr['assigning_authority_UDI'] = $sxQryRes['assigning_authority_UDI'];
			
			$sx_proc_data_arr[] = $dataArr;
			
			if($type == 6)
				$finalResArr['OCU'][] = $dataArr;
			else if($type == 5)
				$finalResArr['SYS'][] = $dataArr;
			else if($type == 9)
				$finalResArr['IMPLANT'][] = $dataArr;
			
			//--- AUDIT TRAIL VARIABLES ---
			$pkIdAuditTrailArr[] = $dataArr['SX_ID'];
			if($pkIdAuditTrailID == ""){	
				$pkIdAuditTrailID = $dataArr['SX_ID'];
			}
		}
		
		$cnt1 = (count($finalResArr['OCU'])<5)? 5 :count($finalResArr['OCU']);
		$cnt2 = (count($finalResArr['SYS'])<5)? 5 :count($finalResArr['SYS']);
		$cnt3 = (count($finalResArr['IMPLANT'])<5)? 5 :count($finalResArr['IMPLANT']);
		for($i = count($finalResArr['OCU']); $i <= $cnt1; $i++){
				$finalResArr['OCU'][] = '';
		}
		
		for($j = count($finalResArr['SYS']); $j<=$cnt2; $j++){
			$finalResArr['SYS'][] = '';
		}
		
		for($k = count($finalResArr['IMPLANT']); $k<=$cnt3; $k++){
			$finalResArr['IMPLANT'][] = '';
		}
		
		$return['sx_proc_data'] = $sx_proc_data_arr;
		$return['sx_exists'] = $sx_exists;
		$return['finalResArr'] = $finalResArr;
		$return['pkIdAuditTrailID'] = $pkIdAuditTrailID;
		$return['pkIdAuditTrailArr'] = $pkIdAuditTrailArr;
		$return['el_df_vw'] = $el_df_vw;
		
		return $return;
	}
	
	public function common_phrases()
	{
		$query = "SELECT phrase FROM common_phrases where providerID IN (0,".$_SESSION["authId"].")";
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		
		if($cnt > 0 )
		{
			$this->phrases = array();
			while( $row = imw_fetch_array($sql) )
			{
				$strTemp = addslashes(trim($row['phrase']));
				$strTemp = str_replace(array("\r","\n"),array("\\r","\\n"),$strTemp);
				array_push($this->phrases,$strTemp);
			}
		}
	}
	
	public function del_sx_procedure($mode,$del_id)
	{
		$del_id = trim($del_id);
		$del_id = (int) $del_id;
		$mode = trim($mode);
		
		
		if( $mode == 'delete' && $del_id > 0 )
		{
			//--- REVIEWED CODE ----
			$query = "select title as surgeryName,pid as dbPatientId from lists where id = '".$del_id."'";
			$sql = imw_query($query);
			$sxQryRes = imw_fetch_assoc($sql);
			$surgeryName = $sxQryRes['surgeryName'];
			$dbPatientId = $sxQryRes['dbPatientId'];
			$action = "";
			$arrReview_Sx_del = array();
			$arrReview_Sx_del[0]['Pk_Id'] = $del_id;
			$arrReview_Sx_del[0]['Table_Name'] = "lists";
			$arrReview_Sx_del[0]['Field_Text'] = "Patient Sx/Procedures";
			$arrReview_Sx_del[0]['Operater_Id'] = $_SESSION['authId'];
			$arrReview_Sx_del[0]['Action'] = "delete";
			$arrReview_Sx_del[0]['Old_Value'] = $surgeryName;
			
			$this->obj_review->reviewMedHx($arrReview_Sx_del,$_SESSION['authId'],"Sx/Procedure",$dbPatientId,0,0);
	
			$query= "update lists set allergy_status = 'Deleted' where id = '".$del_id."' ";
			$sql = imw_query($query);

			//ERP API CALL
			$erp_error=array();
			if($sql && isERPPortalEnabled()) {
				try {
					include_once($GLOBALS['srcdir']."/erp_portal/surgeries.php");
					$obj_surgery = new Surgeries();
					$obj_surgery->deleteSurgery($del_id);
				} catch(Exception $e) {
					$erp_error[]='Unable to connect to ERP Portal';
				}
			}
		}
		
	}
	
	public function sx_typehead()
	{
		global $cls_common;
		
		//---- Start Get the SxProcedures for type ahead ------	
		$sxProTitleArr = array();
		
		$sxProXMLFileExits = false;
		$sxProXMLFile = data_path()."xml/SxProcedures.xml";
		
		if(file_exists($sxProXMLFile)){
			$sxProXMLFileExits = true;
		}
		else{
			$cls_common -> create_sx_procedures_xml();	
			if(file_exists($sxProXMLFile)){
				$sxProXMLFileExits = true;	
			}	
		}

		if($sxProXMLFileExits == true)
		{
			$values = array();
			$XML = file_get_contents($sxProXMLFile);
			$values = $cls_common -> xml_to_array($XML);		
			
			foreach($values as $key => $val)
			{
				if( ($val["tag"] =="sxProceduresInfo") && ($val["type"]=="complete") && ($val["level"]=="2") )
				{
					$sxProName = "";
					$sxProName = $val["attributes"]["name"];				
					$sxProTitleArr[] = addslashes(html_entity_decode($sxProName));
				}
			}
			
		}
		
		return $sxProTitleArr;
		
	}
	
}

?>