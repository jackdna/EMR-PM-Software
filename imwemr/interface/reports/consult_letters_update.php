<?php
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
$str_ids=$_POST['str_ids'];
$sendFaxCase=$_POST['sendFaxCase'];
$record_updated=0;

if($sendFaxCase=="1"){
	$sendFaxNumber = $_POST['send_fax_number'];
	$refPhyId = $_POST['hiddselectReferringPhy'];
	$qryUpdateAdd=",fax_ref_phy_id='".$refPhyId."',fax_number='".$sendFaxNumber."',fax_status='1'";
}
	
$qryGetConsultLetter = "select tp.patient_id as patientID,tp.templateData as tempData,tp.top_margin as topMargin,tp.left_margin as leftMargin,
CONCAT_WS(', ',us.lname,us.fname) as physicianFName,CONCAT_WS(', ',pd.lname ,pd.fname) as patientName, report_sent_date, patient_consult_id
FROM patient_consult_letter_tbl tp
INNER JOIN patient_data pd ON tp.patient_id = pd.id
left JOIN users us ON us.id = tp.operator_id
WHERE tp.patient_consult_id IN(".$str_ids.")";

$rsGetConsultLetter = imw_query($qryGetConsultLetter);
if($rsGetConsultLetter){
	if(imw_num_rows($rsGetConsultLetter)){
	$data = "<style>
				.tb_heading{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#FE8944;
				}
				.text_b{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#FFFFFF;
					background-color:#FE8944;
				}				
			</style>
				
						
			";
		$top_margin=$left_margin=0;
		while($row = imw_fetch_array($rsGetConsultLetter)){
			$top_margin=$row["topMargin"];
			$left_margin=$row["leftMargin"];
			$printTempData=$row["tempData"];
			if($row["report_sent_date"] == "0000-00-00" && $sendFaxCase!="1"){
				if($top_margin==0 || $top_margin==""){
					$top_margin='3.5mm';
				}
				$qryUpdate = "update patient_consult_letter_tbl set report_sent_date = CURDATE() ".$qryUpdateAdd." where patient_consult_id = '".$row["patient_consult_id"]."' ";
				$rsUpdate = imw_query($qryUpdate);
				$data .= "<page  backtop=".$top_margin." backleft=".$left_margin.">".$printTempData."</page>";
				$record_updated=1;
			}
			else{
				$sentPrintTempData = $row['tempData'];
				$sentdata.= "<page backtop=".$top_margin." backleft=".$left_margin.">".$sentPrintTempData."</page>";
			}
			
		}
	}
}

if(trim($data) != "" || trim($sentdata) != ""){
	$pdfData = $data.$sentdata;
	
	$file_location = write_html($pdfData);
	$Host = $_SERVER['HTTP_HOST'];
	if($protocol == ''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }
	
	//CODE for fax 
	$getPCIP=$_SESSION["authId"];			
	$arrFind=array(".","::",":");
	$arrRepl=array("_","_","_");	
	$getIP=str_ireplace($arrFind,$arrRepl,$getPCIP);
	$setFaxHtmlName="faxConsultReport_".$getIP;
	$pdfData = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH,'../../data/'.PRACTICE_PATH,$pdfData);
	$pdfData = str_ireplace('/'.$RootDirectoryName.'/data/'.PRACTICE_PATH,'../../data/'.PRACTICE_PATH,$pdfData);
	
	file_put_contents($file_location,$pdfData);
	fclose($fp);
	if($file_location !== false){
		
		if($_POST['sendFaxCase']=="1"){
			$phy = $_POST['selectReferringPhy'];
			$phycc1 = $_POST['selectReferringPhyCc1'];
			$phycc2 = $_POST['selectReferringPhyCc2'];
			$phycc3 = $_POST['selectReferringPhyCc3'];
			
			$faxnumber		= $_POST['send_fax_number'];
			$faxnumberCc1   = $_POST['send_fax_numberCc1'];
			$faxnumberCc2   = $_POST['send_fax_numberCc2'];
			$faxnumberCc3   = $_POST['send_fax_numberCc3'];
			if(trim($faxnumber)=="" && $faxnumberCc1!=''){
				$faxnumber=$faxnumberCc1;
				$faxnumberCc1="";
			}
            $refPhyId = $_POST['hiddselectReferringPhy'];
			
			$arrReturnFaxInfo['faxnumber']=$faxnumber;
			$arrReturnFaxInfo['faxnumberCc1']=$faxnumberCc1;
			$arrReturnFaxInfo['faxnumberCc2']=$faxnumberCc2;
			$arrReturnFaxInfo['faxnumberCc3']=$faxnumberCc3;
			$arrReturnFaxInfo['getIP']=$file_location;
			//$arrReturnFaxInfo['rqHidConsultLeterId']=$rqHidConsultLeterId;
			$arrReturnFaxInfo['rqHidConsultLeterId']=$str_ids;
			$arrReturnFaxInfo['send_fax_subject']=$_POST['send_fax_subject'];
			$arrReturnFaxInfo['phy']=$phy;
			$arrReturnFaxInfo['phycc1']=$phycc1;
			$arrReturnFaxInfo['phycc2']=$phycc2;
			$arrReturnFaxInfo['phycc3']=$phycc3;
			$arrReturnFaxInfo['phycc3']=$phycc3;
			$arrReturnFaxInfo['refPhyId']=$refPhyId;
            
			echo json_encode($arrReturnFaxInfo);			
			
		}else{
			//FOR PDF
			echo $file_location.'~~'.$record_updated;
		}
	}	
}

?>