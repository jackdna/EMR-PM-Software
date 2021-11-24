<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
   set_time_limit(0);
   session_start();
	echo '<table id="loader_tbl" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif;">
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center">Please wait while data is retrieving from the server.</td>
			</tr>
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center"><img src="images/pdf_load_img.gif"></td> 
			</tr>
		</table>';
	
if($_SESSION['iasc_facility_id'])
{
	$fac_con=" and stub_tbl.iasc_facility_id='$_SESSION[iasc_facility_id]'"; 
}
$table_print="";
include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
include("common_functions.php");
$tablename = "localanesthesiarecord";
include_once("admin/classObjectFunction.php");
//include_once("common/header_print_function.php");
include_once("common/new_header_print_function.php");
include("imageSc/imgTimeInterval.php");
include("imageSc/imgGd.php");
global $objManageData;
$objManageData = new manageData;


//START FUCTION TO GET SCAN IMAGE-SRC WITH ITS SIZE
function getScanImgFun($imgFilePth,$imgNme,$scnContent) {
	$objManageData = new manageData;	
	$file=fopen($imgFilePth,'w+');
	fputs($file,$scnContent);
	$priImageSize=array();
	$scnImgSrc='';
	if(file_exists($imgFilePth)) {
		$priImageSize = getimagesize($imgFilePth);
		$newSize = 'height="100"';
		if($priImageSize[0] > 395 && $priImageSize[1] < 840){
			$newSize = $objManageData->imageResize(680,400,500);						
			$priImageSize[0] = 500;
		}					
		elseif($priImageSize[1] > 840){
			$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],600);						
			$priImageSize[1] = 600;
		}
		else{					
			$newSize = $priImageSize[3];
		}							
		if($priImageSize[1] > 800 ){					
			echo '<newpage>';												
		}
		$scnImgSrc ='<img src="'.$imgNme.'" '.$newSize.'>';
	}
	return $scnImgSrc;
}
//END FUCTION TO GET SCAN IMAGE-SRC WITH ITS SIZE

$patientConfirmationIdSet = $_REQUEST['patientConfirmationId'];
$patientConfirmationIdSingle = explode(",", $patientConfirmationIdSet);
$y=0;
$j=0;
$tablePDF='';
$table='';
$tableCss='';
$tableCss.='
	<style type="text/css">
		table{
			font-size:14px;
		}
		.fheader{
			padding:5px 0px 5px 0px;
			font-weight:bold;
			font-size:16px;
			text-decoration:underline;
			text-align:center;
		}
		.bold{
			font-weight:bold;
		}
		.pt5{
			padding-top:5px;	
		}
		.pd{
			padding:4px;	
		}
		.pl5{
			padding-left:5px;
		}
		.bgcolor{
			background:#C0C0C0;
		}
		.cbold{
			text-align:center;
			font-weight:bold;		
		}
		.bdrbtm{
			border-bottom:1px solid #C0C0C0;
			height:20px;	
			vertical-align:top;
		}
		.bdrtop{
			border-top:1px solid #C0C0C0;
			height:20px;	
		}
	</style>';
foreach ($patientConfirmationIdSingle as $value){
	$y++;
	if($value){
		$pConfId = $value;
		if($pConfId){
			
			$_REQUEST['pConfId'] = $pConfId;
			
			$head_table = new_headerInfo($pConfId);
			$head_table .= $tableCss;
			
			// Start Printing Operative Report
			$ViewoperativeQry = "select *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat from `operativereport` where confirmation_id='".$pConfId."'";
			$ViewoperativeRes = imw_query($ViewoperativeQry) or die(imw_error()); 
			$ViewoperativeNumRow = imw_num_rows($ViewoperativeRes);
			$ViewoperativeRow = imw_fetch_array($ViewoperativeRes); 
			$opReportFormStatus = $ViewoperativeRow["form_status"];
			
			if( $ViewoperativeNumRow && ($opReportFormStatus == 'completed' || $opReportFormStatus == 'not completed') ) {
				$operative_surgeon_sign = $ViewoperativeRow["signature"];
				$operative_data = stripslashes($ViewoperativeRow["reportTemplate"]);
				$signSurgeon1Id = $ViewoperativeRow["signSurgeon1Id"];
				$signSurgeon1FirstName = $ViewoperativeRow["signSurgeon1FirstName"];
				$signSurgeon1MiddleName = $ViewoperativeRow["signSurgeon1MiddleName"];
				$signSurgeon1LastName = $ViewoperativeRow["signSurgeon1LastName"];
				$signSurgeon1Status = $ViewoperativeRow["signSurgeon1Status"];
			
				$operative_data = str_ireplace( '/surgerycenter/',$_SERVER['DOCUMENT_ROOT'].'/surgerycenter/', $operative_data);
			
				if($operative_data!=""){
					
					$table.='<page>'.$head_table."\n";
				}
				
			
				$table.='<table style="width:700px;" cellpadding="0" cellspacing="0">
						<tr>	
							<td style="width:700px;" class="fheader">Operative Report</td>
						</tr>';
					
					if($operative_data!=""){
						$j++;
						$table.='
						<tr>
							<td style="width:700px;" class="bgcolor bdrbtm cbold">Operative Record</td>
						</tr>
						<tr>
							<td style="width:700px;">'.strip_tags(nl2br($operative_data),' <img> <p>').'</td>
						</tr>';
					}
				$table.='
						<tr>
							<td style="width:700px;">';
							if($signSurgeon1LastName!="" || $signSurgeon1FirstName!=''){	
								$table.='
									<b>Surgeon:&nbsp;</b>'.$signSurgeon1LastName.', '.$signSurgeon1FirstName.'
									<br><b>Electronically Signed:&nbsp;</b>'.$ViewoperativeRow['signSurgeon1Status'].'
									<br><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($ViewoperativeRow['signSurgeon1DateTime']);
							}else{
								$table.='
									<b>Surgeon:&nbsp;</b>______
									<br><b>Electronically Signed:&nbsp;</b>________
									<br><b>Signature Date:&nbsp;</b>________';
							}
						$table.='
							</td>					
						</tr>';
						//START IOL SCAN UPLOAD IMAGE
						$ViewOpRoomRecordQry = "select * from `operatingroomrecords` where  confirmation_id = '".$pConfId."'";
						$ViewOpRoomRecordRes = imw_query($ViewOpRoomRecordQry) or die(imw_error()); 
						$ViewOpRoomRecordNumRow = imw_num_rows($ViewOpRoomRecordRes);
						if($ViewOpRoomRecordNumRow>0) {
							$ViewOpRoomRecordRow = imw_fetch_array($ViewOpRoomRecordRes); 
							$operatingRoomRecordsId = $ViewOpRoomRecordRow["operatingRoomRecordsId"];
							$iol_ScanUpload = $ViewOpRoomRecordRow["iol_ScanUpload"];
							$iol_ScanUpload2 = $ViewOpRoomRecordRow["iol_ScanUpload2"];
							if($iol_ScanUpload!=''){
								$bakImgResourceOproom = imagecreatefromstring($iol_ScanUpload);
								imagejpeg($bakImgResourceOproom,'html2pdfnew/oproom_'.$operatingRoomRecordsId.'.jpg');
								
								$newSize=' height="100"';
								$priImageSize=array();
								if(file_exists('html2pdfnew/oproom_'.$operatingRoomRecordsId.'.jpg')) {
									$priImageSize = getimagesize('html2pdfnew/oproom_'.$operatingRoomRecordsId.'.jpg');
									if($priImageSize[0] > 395 && $priImageSize[1] < 840){
										$newSize = $objManageData->imageResize(680,400,500);						
										$priImageSize[0] = 500;
									}					
									elseif($priImageSize[1] > 840){
										$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],600);						
										$priImageSize[1] = 600;
									}
									else{					
										$newSize = $priImageSize[3];
									}							
									if($priImageSize[1] > 800 ){					
										echo '<page></page>';												
									}
									$table.='<tr><td style="width:700px;text-align:center;" class="bdrbtm"><img src="../html2pdfnew/oproom_'.$operatingRoomRecordsId.'.jpg" '.$newSize.'></td></tr>';
								}else{
									$table.='<tr><td style="width:700px;text-align:center;" class="bdrbtm">&nbsp;</td></tr>';
								}
							}
						
							if($iol_ScanUpload2!=''){
								$bakImgResourceOproom1 = imagecreatefromstring($iol_ScanUpload2);
								imagejpeg($bakImgResourceOproom1,'html2pdfnew/oproom1_'.$operatingRoomRecordsId.'.jpg');
								
								$priImageSize=array();
								if(file_exists('html2pdfnew/oproom1_'.$operatingRoomRecordsId.'.jpg')) {
									$priImageSize = getimagesize('html2pdfnew/oproom1_'.$operatingRoomRecordsId.'.jpg');
									$newSize = 'height="100"';
									if($priImageSize[0] > 395 && $priImageSize[1] < 840){
										$newSize = $objManageData->imageResize(680,400,500);						
										$priImageSize[0] = 500;
									}					
									elseif($priImageSize[1] > 840){
										$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],600);						
										$priImageSize[1] = 600;
									}
									else{					
										$newSize = $priImageSize[3];
									}							
									if($priImageSize[1] > 800 ){					
										echo '<page></page>';												
									}
								}
								$table.='<tr><td style="width:700px;padding-top:20px;text-align:center;" class="bdrbtm"><img src="../html2pdfnew/oproom1_'.$operatingRoomRecordsId.'.jpg" '.$newSize.'></td></tr>';
							}
						}
				$table.='</table>';	
				if($operative_data!=""){
					$table.='</page>';
				}
			}
			// End Printing Operative Report
			
			
			// Start Printing Laser Procedure Chart
			if(file_exists('laser_procedure_print_data.php'))
			{
				include'laser_procedure_print_data.php';
				
				if($laserProcFormStatus == 'completed' || $laserProcFormStatus == 'not completed' ) {
					$table.='<page>'.$head_table."\n";
					$table .= $table_print;
					$table .= '</page>';
				}
			}
			
			// End Printing Laser Procedure Chart
			
			
			// start Printing Injection Procedure Chart
			if(file_exists('injection_misc_pdf_content.php'))
			{
				include'injection_misc_pdf_content.php';
				$isMisc = $detailConfirmation->prim_proc_is_misc == 'injection' ? true : false;
				if( ($injectionMiscFormStatus == 'completed' || $injectionMiscFormStatus == 'not completed' ) && $isMisc) {
					$table.='<page>';
					$table .= $table_main;
					$table .= '</page>';	
				}
				
			}
			
			// End Printing Injection Procedure Chart
			
			
		}	
	}
}
//echo $tablePDF;

$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut  = fputs($fileOpen,$table);
fclose($fileOpen);

?>
 <?php 
if(trim($table) != ""){
	?>
	
<form name="printOperative"  action="new_html2pdf/createPdf.php?op=p" method="post">
</form>		

<script language="javascript">
	function submitfn()
	{
		document.printOperative.submit();
	}
</script>

<script type="text/javascript">
	submitfn();
</script>


	<?php
}else{
?>
	<script>
		if(document.getElementById("loader_tbl")) {
			document.getElementById("loader_tbl").style.display = "none";	
		}
	</script>	
	<table align="center" width="100%" border="0" cellpadding="1" cellspacing="1">
		<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
			<td align="center">No Result.</td>
		</tr>
	</table>
<?php
}

?>