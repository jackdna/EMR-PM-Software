<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Patient Info/Eligibility ERx
 Access Type: Indirect Access.
 
*/
include_once (dirname(__FILE__).'/class.language.php');
include_once (dirname(__FILE__).'/cls_common_function.php');
class Eligibility extends core_lang
{
	public $eligibility_data = '';
	public $eligibility_count = 0;
	private $patient_id = '';
	
	public function __construct($patient_id)
	{
		core_lang::__construct();
		$this->patient_id = $patient_id;
	}
	
	public function load_eligibity_data()
	{
		$total_records = 0; $return = array();
		$query = "select rtme.id as RTMEId,rtme.ins_data_type, DATE_FORMAT(rtme.request_date_time, '%m-%d-%Y') as elDate, 
																			DATE_FORMAT(rtme.request_date_time, '%h:%i %p') as elTime,
																			DATE_FORMAT(rtme.imedic_DOS, '%m-%d-%Y') as elDOS, DATE_FORMAT(rtme.request_date_time, '%m-%d-%Y') as elDEC,
																			concat(rtme.responce_pat_lname,', ',rtme.responce_pat_fname) as elPatName , 
																			rtme.responce_pat_mname as elPatMname, DATE_FORMAT(rtme.responce_pat_dob , '%m-%d-%Y') as elPatDOB, 
                                      rtme.responce_pat_add1 as elPatAdd1, rtme.responce_pat_add2 as elPatAdd2, rtme.responce_pat_city as elPatCity,
																			rtme.responce_pat_state as elPatState, rtme.responce_pat_zip as elPatZip, 
																			rtme.responce_pat_policy_no as elPatPolicyNo, rtme.EB_responce as elResponce,
																			rtme.transection_error as tranError, 
																			CONCAT_WS(', ',SUBSTRING(us.lname,1,1),SUBSTRING(us.fname,1,1)) as elOpName,rtme.hipaa_5010 as elHIPAA5010,
																			insComp.name as insCompName, rtme.comment as userComment, rtme.xml_271_responce as respXMLPath,
																			rtme.eligibility_ask_from  as elAsk
																	FROM real_time_medicare_eligibility rtme
																	LEFT JOIN users us ON us.id = rtme.request_operator 
																	LEFT JOIN insurance_data insData ON insData.id = rtme.ins_data_id 
																	LEFT JOIN insurance_companies insComp ON insComp.id = insData.provider
																	where rtme.patient_id = '".$this->patient_id."' 
																	and rtme.del_status = '0' 
																	ORDER BY request_date_time DESC";
		$sql	= imw_query($query);
		$total_records = imw_num_rows($sql);
		if($total_records > 0)
		{
			$this->eligibility_count = $total_records;
			while($row = imw_fetch_assoc($sql))
			{
				$html_row = $this->html_row($row);
				array_push($return,$html_row);
			}
		}
		else
		{
				$html_row = '<tr><td class="bg-info" colspan="14">No Patient Eligibility History Exist! To check eligibility Please go to <b>Insurance</b> and click on <a class="realtime_icon"></a><br><br></td></tr>';
				array_push($return,$html_row);
		}
		
		return $return;	
	}
	
	private function html_row($row)
	{
		$dbElDate = $dbElTime = $dbElDOS = $dbElDEC = $dbElPatName = $dbElPatMname = $dbElPatDOB = $dbElPatAdd1 = $dbElPatAdd2 = $patientName = "";
		$dbElPatCity = $dbElPatState = $dbElPatZip = $dbElPatPolicyNo = $dbInsCompName = $dbTranError = $dbUserComment = $dbInsDataTypePriSec = "";
		$dbRTMEId = $dbTotScan = 0;
		
		extract($row);
		
		if($elDOS == "00-00-0000"){ $elDOS = "N/A"; }
		$patientName = $elPatName.' '.$elPatMname;
		$patientName = trim($patientName);
		if($patientName == ",") { $patientName = ""; }
		if($elPatDOB == "00-00-0000") { $elPatDOB = "N/A"; }
		
		$patAdd = trim($elPatAdd1." ".$elPatAdd2." ".$elPatCity." ".$elPatState.", ".$elPatZip);
		if($patAdd == ",") { $patAdd = ""; }
		
		$strEBResponce = core_lang::get_vocabulary("vision_share_271", "EB", (string)$elResponce);						
		//if($elAsk == 0) { $insCompName .= " (CMS)"; }
		//elseif($elAsk == 1) { $insCompName .= " (EMDEON)"; }
		if(empty($ins_data_type)) $insCompName .= " (Primary)";
		else if(!empty($ins_data_type)) $insCompName .= " (".strtoupper($ins_data_type).")";
		
		$q	=	"select count(*) as totScan from ".constant("IMEDIC_SCAN_DB").".scans where test_id = '".$RTMEId."' and patient_id = '".$this->patient_id."' and status = 0";
		$s	=	imw_query($q);
		if($s)
		{
			$r = imw_fetch_array($s);
			$totScan = $r["totScan"];
			imw_free_result($r);
		}
		
		$tempTitle = $tempStyle = "";
		$html = '';
		$row = array();
		
		$onclick = "get271Report('".$RTMEId."');";
		$doScan = "doScan('".$RTMEId."', 'scan');";
		$doUpload = "doScan('".$RTMEId."', 'upload');";
		if($totScan > 0)	$preview = 'title="Preview Available" onClick="getPreview(\''.$RTMEId.'\');"';
		elseif($totScan == 0)	$preview = 'title="No Preview Available" ';	
			
		$error = false;
		if($tranError <> '')
		{
			$elResponce = "Error";
			$tempbg = " bg-danger";
			$error = true;
			$column_1_td	= '<td title="'.$tranError.'" class="bg '.$tempbg.' ">'.$elResponce.'</td>';
			$show_on_click = '';
		}
		else
		{
			$tmp_array = array('6','7','8','V');
			if(in_array($elResponce,$tmp_array))
			{
				$tempbg = " bg-danger";
				$elResponce = $strEBResponce;
			}
			else
			{
				$tempbg = "bg-success";
				$elResponce = (is_string($strEBResponce) ? $strEBResponce : "View Detail");
			}
			$show_on_click = 'onclick="'.$onclick.'"';
			$column_1_td = 
				'<td>
					<a href="javascript:void(0);" '.$show_on_click.' class="bg '.$tempbg.'">'.$elResponce.'</a><br>
					<a href="javascript:void(0);" onClick="setRTEAmount(\''.$RTMEId.'\')" class="purple-text">Set&nbsp;RTE&nbsp;Amt.</a>
				</td>';
					
		}
		
		$html .=
				'<tr>
					'.$column_1_td.'
					<td onclick="'.$onclick.'">'.$elDate.'</td>
					<td '.$show_on_click.'>'.$elTime.'</td>
					<td '.$show_on_click.'>'.$elDOS.'</td>
					<td '.$show_on_click.'>'.$elDEC.'</td>
					<td '.$show_on_click.'>'.$patientName.'</td>
					<td '.$show_on_click.'>'.$elPatDOB.'</td>
					<td '.$show_on_click.'>'.$patAdd.'</td>
					<td '.$show_on_click.'>'.$insCompName.'</td>
					<td '.$show_on_click.'>'.$elPatPolicyNo.'</td>
					<td >
						<ul class="list-group">
							<li class="list-group-item pointer" data-title="Active Coverage Information" data-request="aco" data-id="'.$RTMEId.'" onClick="show_details(this);">Active Coverage</li>
							<li class="list-group-item pointer" data-title="Co-insurance Information" data-request="coi" data-id="'.$RTMEId.'" onClick="show_details(this);">Co-Insurance</li>
							<li class="list-group-item pointer" data-title="Co-Payment Information" data-request="cop" data-id="'.$RTMEId.'" onClick="show_details(this);">CoPay</li>
							<li class="list-group-item pointer" data-title="Deductible Information" data-request="ded" data-id="'.$RTMEId.'" onClick="show_details(this);">Deductible</li>
							<li class="list-group-item pointer" data-title="Limitations Information" data-request="lmt" data-id="'.$RTMEId.'" onClick="show_details(this);">Limitations</li>
						</ul>
					</td>
					<td>
						<input type="hidden" name="primaryKey[]" id="primaryKey_'.$RTMEId.'" value="'.$RTMEId.'" />
						<textarea id="txtAComment_'.$RTMEId.'" name="txtAComment_'.$RTMEId.'" rows="2">'.trim($userComment).'</textarea>
					</td>
					<td>
						<span class="glyphicon scan-icon-micro pointer" title="Scan Documents" onClick="'.$doScan.'"></span>
            <span class="glyphicon glyphicon-upload pointer" title="Upload Documents" onClick="'.$doUpload.'"></span>
						'.($totScan > 0 ? '<span class="glyphicon glyphicon-search '.($totScan > 0 ? ' pointer':'').'" '.$preview.'></span>' : '').'
					</td>
					<td '.$show_on_click.'>'.$elOpName.'</td>
				</tr>';
				
				
			return $html;	
			
  }
	public function save (&$request)
	{
		extract($request);
		if($save)
		{
			if(is_array($primaryKey) == true)
			{
				foreach($primaryKey as $key => $val)
				{
					$txtAName = "";
					$txtAName = "txtAComment_".$val;			
					$query	= "update real_time_medicare_eligibility set comment = '".htmlentities(addslashes(trim($$txtAName)))."' where id = '".$val."'";
					$sql = imw_query($query);
				}
			}
		}
	}	

}
?>