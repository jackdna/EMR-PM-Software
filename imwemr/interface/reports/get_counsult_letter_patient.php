<?php

include_once(dirname(__FILE__)."/../../config/globals.php");
require_once($GLOBALS['fileroot'] .'/library/classes/cls_common_function.php');
$CLSCommonFunction = new CLSCommonFunction;

$searchDateFrom = $_REQUEST['fromDate'];
$searchDateTo = $_REQUEST['toDate'];
$rqIntIncSentDate = $_REQUEST['intIncSentDate'];

$qryIncludeSentdate = " and report_sent_date = '0000-00-00'";
if(isset($rqIntIncSentDate) && $rqIntIncSentDate == 1){
	$qryIncludeSentdate = "";
}
$data = $rqSelectedFac = $qryProAndFac = $qryProAndFacOrderBy = "";
if($searchDateFrom && $searchDateTo){
	//list($month, $day, $year) = explode('-',$searchDateFrom);													
	//$searchDateFrom = $year."-".$month."-".$day;								
	$searchDateFrom = getDateFormatDB($searchDateFrom);
	//list($month, $day, $year) = explode('-',$searchDateTo);													
	//$searchDateTo = $year."-".$month."-".$day;
	$searchDateTo = getDateFormatDB($searchDateTo);
	
	$qryProAndFacOrderBy="  ORDER BY tp.date, pd.lname";
	$rqSelectedFac = $_REQUEST["selectedFac"];
	if(empty($rqSelectedFac) == false){
		$qryProAndFac = " and pd.default_facility in(".$rqSelectedFac.")";
		$qryProAndFacOrderBy .= " , pd.default_facility";
	}
	$rqSelectedProvider = $_REQUEST["selectedProvider"];
	if(empty($rqSelectedProvider) == false){
		//$qryProAndFac .= " and pd.providerID in(".$rqSelectedProvider.")";
		//$qryProAndFac .= " and (tp.provider_signature_id in(".$rqSelectedProvider.") OR pd.providerID in(".$rqSelectedProvider.")) ";
		$qryProAndFac .= " and tp.provider_signature_id in(".$rqSelectedProvider.") ";
		if(empty($qryProAndFacOrderBy) == true){
			//$qryProAndFacOrderBy .= " ORDER BY tp.date, pd.lname";
		}
		else{
			$qryProAndFacOrderBy .= " , pd.providerID";
		}
	}
	if(empty($qryProAndFacOrderBy) == false){
		$qryProAndFacOrderBy .= " DESC";
	}
	$qryGetConsultLetterPatient = "select DISTINCT tp.patient_id as patientID,tp.fax_number as faxNumber ,
							 tp.fax_ref_phy_id as refPhyId,tp.cc1_ref_phy_id,tp.cc2_ref_phy_id,tp.cc3_ref_phy_id, CONCAT_WS(', ',pd.lname ,pd.fname) as patientName, DATE_FORMAT(report_sent_date, '".get_sql_date_format()."') reportSentDate, DATE_FORMAT(tp.date, '".get_sql_date_format('','Y','/')."') generateDate, patient_consult_id as consultPriKey,if(tp.preffered_reff_fax!='~||~',tp.preffered_reff_fax,'') as preffered_reff_fax 
							 from patient_consult_letter_tbl tp
							 INNER JOIN patient_data pd ON (tp.patient_id = pd.id )
							 left JOIN users us ON us.id = tp.operator_id							 
							 where tp.date BETWEEN '".$searchDateFrom."' AND '".$searchDateTo."'
							 and tp.status !='1' ".$qryProAndFac.$qryIncludeSentdate.$qryProAndFacOrderBy."
							 ";
	$rsGetConsultLetterPatient = imw_query($qryGetConsultLetterPatient);	


	
	if($rsGetConsultLetterPatient){
		if(imw_num_rows($rsGetConsultLetterPatient)>0){
			$hieght = $_SESSION['wn_height'] - 425;
			$data = '';
			$data.="<iframe style='top:50px;left:150px; position:absolute;display:none;' id='hiddFrameForSendFax' name='hiddFrameForSendFax'></iframe>";	
			$data.= "<form name=\"frmConsultLetterDiV\" method=\"post\" action=\"consult_letters_report.php\" target=\"pdfPrint\">";
			$data.= "<div style=\" height:".$hieght1."px;display:block\" class=\"modal in\" role=\"dialog\">";
			$data.= "<div class=\"modal-dialog modal-lg\">";
			$data.= "<div class=\"modal-content\">";
			$data.= "<div class=\"modal-header\">
						<button type=\"button\" class=\"close\" onclick=\"javascript: closeCousultDiv();\">&times;</button>
						<h4 class=\"modal-title\">Consult letters</h4>
					</div>";
			$data .= "
					<div class=\"modal-body\">
					<table class=\"table table-bordered\">
						<tr class=\"subsection\">
							<td style=\"width:25px;\">
								<input type=\"checkbox\" id=\"cbkSelectAll\" name=\"cbkSelectAll\" onClick=\"selDeSelAllChkBox('sel');\"/>
							</td>
							<td class=\"text12b\" style=\"width:350px\">
								Patient Name (Pt. Id)&nbsp;
							</td>
							<td class=\"text12b\">
								Generate Date &nbsp;<span class=\"pull-right\">Total Consult Letters: ".imw_num_rows($rsGetConsultLetterPatient)."</span>
							</td>					
						</tr>";			
			$counterTr = 0; 
			$counterTd = 1;
			$counter = 1;
			$sentTDData = $unSentTDData = "<table class=\"W100per valignTop alignLeft\">";			
			while($rowGetConsultLetterPatient = imw_fetch_array($rsGetConsultLetterPatient)){
				if($counterTr == 0){
					//$data .= "<tr><td style=\"height:$unSentHieght\">";
				}				
				if($counterTd<4){	
					$generateDate='';
					$strReportFaxNo = $refLastName = $refFirstName = $strReportPhyName = $reportFaxNumber = $reportSentDateDB = $strReportSentDate = $strCbkChecked = $strSpan = "";
					$strStyleCbk = "inline";
					$reportSentDateDB = $rowGetConsultLetterPatient['reportSentDate'];	
					$reportFaxNumber  = $rowGetConsultLetterPatient['faxNumber'];
					$reportRefPhyID   = $rowGetConsultLetterPatient['refPhyId'];
					$cc1_ref_phy_id   = $rowGetConsultLetterPatient['cc1_ref_phy_id'];
					$cc2_ref_phy_id   = $rowGetConsultLetterPatient['cc2_ref_phy_id'];
					$cc3_ref_phy_id   = $rowGetConsultLetterPatient['cc3_ref_phy_id'];
					$generateDate 	  =	$rowGetConsultLetterPatient["generateDate"];
					$preffered_reff_fax=$rowGetConsultLetterPatient["preffered_reff_fax"];
					$strSpan = NULL;
					$pat_id = $rowGetConsultLetterPatient['patientID'];
					$consultPriKey = $rowGetConsultLetterPatient['consultPriKey'];
					$patientName = $rowGetConsultLetterPatient['patientName'];
					if(getNumber($reportSentDateDB) != "00000000"){
						$strReportSentDate = ", Sent on: ".$reportSentDateDB;
						if($reportFaxNumber){ $strReportFaxNo = ", Fax#: ".$reportFaxNumber; }
						if($reportRefPhyID){
							$refPhyDetail = $CLSCommonFunction->getRefferPhysician($reportRefPhyID);
							//$refPhyDetail=$objClass->refferPhysician($reportRefPhyID);
							$refLastName=$refPhyDetail->LastName;
							$refFirstName=$refPhyDetail->FirstName;
							$strReportPhyName=", Ref Physician: ".$refLastName.", ".$refFirstName;
						}
						$strStyleCbk = "none";
						$strCbkChecked = "checked";
						$strSpan = "<span class=\"glyphicon glyphicon-ok text-success\" onClick=\"javascript: closeCousultDiv();\"></span>";
						$sentTDData .=<<<DATA
							<tr>
								<td style="width:25px;"><input type="checkbox" id="cbk$counter" $strCbkChecked style="display:$strStyleCbk;" name="cbk" value="$pat_id-$consultPriKey" onClick="javascript: document.getElementById('patients').value = ''; document.getElementById('cbkSelectAll').checked = false;"> $strSpan</td>
								<td class="valignTop alignLeft" style="width:350px">$patientName (Pt. Id: $pat_id $strReportSentDate $strReportFaxNo $strReportPhyName)</td>
								<td class="valignTop alignLeft">$generateDate</td>
							</tr>
DATA;
					}
					elseif(getNumber($reportSentDateDB) == "00000000"){
						$unSentTDData .=<<<DATA
							<tr>
								<td class="valignTop alignLeft" style="width:25px;"><input type="checkbox" id="cbk$counter" $strCbkChecked style="display:$strStyleCbk;" name="cbk" value="$pat_id-$consultPriKey" onClick="javascript:document.getElementById('patients').value = ''; document.getElementById('cbkSelectAll').checked = false;faxChbxClick(this,'$reportRefPhyID','$cc1_ref_phy_id','$cc2_ref_phy_id','$cc3_ref_phy_id','$preffered_reff_fax');"></td>
								<td class="valignTop alignLeft" style="width:350px">$patientName (Pt. Id: $pat_id $strReportSentDate)</td>
								<td class="valignTop alignLeft">$generateDate</td>
							</tr>
DATA;
					}
					$counterTr = 1;
				}
				$counterTd++;																						
				if($counterTd==4){
					$counterTr = 0;
					$counterTd = 1;
					//$data .= "</td></tr>";						
				}
				$counter++;
			}
			$sentTDData .= "</table>";
			$unSentTDData .= "</table>";
			$unSentHieght = $hieght - 20;		
			if(isset($rqIntIncSentDate) && $rqIntIncSentDate == 1){			
				$unSentHieght = $unSentHieght / 2;		
			}
			$data .= "<tr><td colspan=\"3\" class=\"valignTop alignLeft\">";
			
			$data .= "<div style=\"height:".$unSentHieght."px; width:100%; overflow:auto;\">";
			$data .= $unSentTDData;
			$data .= "</div>";					

			$data .= "</td></tr>";
			if(isset($rqIntIncSentDate) && $rqIntIncSentDate == 1){			
				$data .= "<tr><td colspan=\"3\" class=\"valignTop alignLeft\">";		
				$data.= "<div class=\"modal-header\"><h4 class=\"modal-title\">Sent Consult letters</h4></div>";
				$data .= "<div style=\"height:".($unSentHieght - 20)."px; width:100%; overflow:auto;\">";
				$data .= $sentTDData;
				$data .= "</div>";
				$data .= "</td></tr>";					
			}
			if(is_updox('fax') || is_interfax()) {
				$faxInputBtn = '<input type="button" value="Send Fax" name="faxDiv" class="btn btn-success" onclick="javascript:document.getElementById(\'send_fax_div\').style.display=\'block\';">';	
			}
			
		$data .= '</table>
				</div>	
				<div class="subsection bg2 border modal-footer">
					<div class="row">
						<div class="form-group">
							<input type="button" onClick="top.fmain.getConsultReport();" value="Get Report" name="getReport" class="btn btn-success">
							'.$faxInputBtn.'
							<input type="button" onClick="closeCousultDiv();" value="Close" name="closeDiv" class="btn btn-danger">
						</div>
					</div>
				</div>
				</div>
				</div>
				</div>';
	
		$data.= '<div id="send_fax_div" style="display:none;" class="modal in" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" onclick="javascript:document.getElementById(\'send_fax_div\').style.display=\'none\';">&times;</button>
							<h4 class="modal-title">Send Fax</h4>
						</div>
						<div class="modal-body">
							<table class="table">
								<tr>
									<td><b>Subject:</b></td>
									<td colspan="3"><input type="text" name="send_fax_subject" id="send_fax_subject" class="form-control" value="'.trim(constant("fax_subject")).'"/></td>
								</tr>
								<tr>    
									<td>Ref.&nbsp;Phy:</td>
									<td>
									<input type="hidden" name="hiddselectReferringPhy" id="hiddselectReferringPhy">
									<input type="text" name="selectReferringPhy"  id="selectReferringPhy" autocomplete="off" onKeyUp="top.loadPhysicians(this,\'hiddselectReferringPhy\',\''.data_path(1).'xml/refphy\',\'send_fax_number\');">
									</td>
									<td>Fax&nbsp;No.:</td>
									<td><input type="text" name="send_fax_number" id="send_fax_number" onChange="set_fax_format(this,\'\');" autocomplete="off"></td>
							   </tr>
							   <tr>    
									<td>Cc1:</td>
									<td>
									<input type="hidden" name="hiddselectReferringPhyCc1" id="hiddselectReferringPhyCc1">
									<input type="text" name="selectReferringPhyCc1"  id="selectReferringPhyCc1" autocomplete="off" onKeyUp="top.loadPhysicians(this,\'hiddselectReferringPhyCc1\',\''.data_path(1).'xml/refphy\',\'send_fax_numberCc1\');">
									</td>
								   
									<td>Fax&nbsp;No.:</td>
									<td><input type="text" name="send_fax_numberCc1" id="send_fax_numberCc1" autocomplete="off" onChange="set_fax_format(this,\'\');"></td>
							   </tr>
							   <tr>    
									<td>Cc2:</td>
									<td>
									<input type="hidden" name="hiddselectReferringPhyCc2" id="hiddselectReferringPhyCc2">
									<input type="text" name="selectReferringPhyCc2" id="selectReferringPhyCc2" onKeyUp="top.loadPhysicians(this,\'hiddselectReferringPhyCc2\',\''.data_path(1).'xml/refphy\',\'send_fax_numberCc2\');">
									</td>
								   
									<td>Fax&nbsp;No.:</td>
									<td><input type="text" name="send_fax_numberCc2" id="send_fax_numberCc2" autocomplete="off" onChange="set_fax_format(this,\'\');"></td>
							   </tr>
							   <tr>    
									<td>Cc3:</td>
									<td>
									<input type="hidden" name="hiddselectReferringPhyCc3" id="hiddselectReferringPhyCc3">
									<input type="text" name="selectReferringPhyCc3" id="selectReferringPhyCc3" autocomplete="off" onKeyUp="top.loadPhysicians(this,\'hiddselectReferringPhyCc3\',\''.data_path(1).'xml/refphy\',\'send_fax_numberCc3\');">
									</td>
								   
									<td>Fax&nbsp;No.:</td>
									<td><input type="text" name="send_fax_numberCc3" id="send_fax_numberCc3" autocomplete="off" onChange="set_fax_format(this,\'\');"></td>
							   </tr>
							</table>
						</div>
						<div class="modal-footer">
							<div class="row">
								<div class="form-group">
								<input type="button" onclick="return sendMultipleConsultLetterFax();" value="Send Fax"  class="btn btn-success">
								<input type="button" onclick="javascript:document.getElementById(\'send_fax_div\').style.display=\'none\';" value="Close" id="fax_cancel_btn" class="btn btn-danger">
								</div>
							</div>
						</div>
						<div id="div_load_image" style="left:50px;top:0px; width:200px; position:absolute; display:none; z-index:1000; ">
							<img src="../../library/images/loading_image.gif">
						</div>
						</div>
						</div>
					</div>';
			$data .= "<input type=\"hidden\" name=\"sendFaxCase\" id=\"sendFaxCase\">";
			$data .= "<input type=\"hidden\" name=\"patients\" id=\"patients\">";
			$data .= "<input type=\"hidden\" name=\"hidConsultLeterId\" id=\"hidConsultLeterId\">";
			$data .= "<input type=\"hidden\" name=\"searchDate\" id=\"searchDate\" value='".$searchDateFrom."'>";
			$data .= "<input type=\"hidden\" name=\"toDate\" id=\"toDate\" value='".$searchDateTo."'>";
			$data .= "<input type=\"hidden\" name=\"rqSelectedFac\" id=\"rqSelectedFac\" value='".$rqSelectedFac."'>";
			$data .= "<input type=\"hidden\" name=\"rqSelectedProvider\" id=\"rqSelectedProvider\" value='".$rqSelectedProvider."'>";
		$data .= "</form>";
		}
	}
}	
echo $data;	
?>