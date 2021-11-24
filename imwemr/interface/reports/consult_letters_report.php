<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php 
/*
FILE : CONSULT_LETTER_REPORT.PHP
PURPOSE : CONSULT LETTERS REPORT
ACCESS TYPE : DIRECT
*/
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");

header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

?>
<?php 
$searchDateFrom = $_REQUEST['searchDate'];
$searchDateTo = $_REQUEST['toDate'];
$patients = $_REQUEST['patients'];
$sendFaxCase = $_REQUEST['sendFaxCase'];
$sendFaxNumber="";
$refPhyId="";

$qryUpdateAdd="";
if($sendFaxCase=="1"){
	$sendFaxNumber = $_REQUEST['send_fax_number'];
	$refPhyId = $_REQUEST['hiddselectReferringPhy'];
	$qryUpdateAdd=",fax_ref_phy_id='".$refPhyId."',fax_number='".$sendFaxNumber."',fax_status='1'";
}
$rqHidConsultLeterId = $_REQUEST['hidConsultLeterId'];
$data = $rqSelectedFac = $qryProAndFac = $qryProAndFacOrderBy = $sentdata = "";
$rqSelectedFac = $_REQUEST["rqSelectedFac"];
if(empty($rqSelectedFac) == false){
	$qryProAndFac = " and pd.default_facility in(".$rqSelectedFac.")";
	$qryProAndFacOrderBy .= " ORDER BY pd.default_facility";
}
$rqSelectedProvider = $_REQUEST["rqSelectedProvider"];
if(empty($rqSelectedProvider) == false){
	$qryProAndFac .= " and pd.providerID = '".$rqSelectedProvider."'";
	if(empty($qryProAndFacOrderBy) == true){
		$qryProAndFacOrderBy .= " ORDER BY pd.providerID";
	}
	else{
		$qryProAndFacOrderBy .= " , pd.providerID";
	}
}
if(empty($qryProAndFacOrderBy) == false){
	$qryProAndFacOrderBy .= " DESC";
}

$qryGetConsultLetter = "select tp.patient_id as patientID,tp.templateData as tempData,tp.top_margin as topMargin,tp.left_margin as leftMargin,CONCAT_WS(', ',us.lname,us.fname) as physicianFName,CONCAT_WS(', ',pd.lname ,pd.fname) as patientName, report_sent_date, patient_consult_id
						 from patient_consult_letter_tbl tp
						 INNER JOIN patient_data pd ON tp.patient_id = pd.id
						 left JOIN users us ON us.id = tp.operator_id
						 where tp.patient_id IN(".$patients.") and tp.patient_consult_id IN(".$rqHidConsultLeterId.")	and tp.date BETWEEN '".$searchDateFrom."' AND '".$searchDateTo."' ".$qryProAndFacOrderBy."
						 ";
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
			if($row["report_sent_date"] == "0000-00-00" && $sendFaxCase!="1"){
				if($top_margin==0 || $top_margin==""){
					$top_margin='3.5mm';
				}
				$qryUpdate = "update patient_consult_letter_tbl set report_sent_date = CURDATE() ".$qryUpdateAdd." where patient_consult_id = '".$row["patient_consult_id"]."' ";
				$rsUpdate = imw_query($qryUpdate);
				/* $printTempData = $row['tempData'];
				$printTempData = str_ireplace("/$web_RootDirectoryName/interface/common/new_html2pdf/","",$row['tempData']);
				$printTempData = str_ireplace($GLOBALS['webroot']."/interface/reports/new_html2pdf/","",$printTempData);
				$printTempData = str_ireplace("../../interface/main/uploaddir/","../../main/uploaddir/",$printTempData);
				$printTempData = str_ireplace($GLOBALS['webroot']."/interface/main/uploaddir/document_logos/",$webServerRootDirectoryName.$web_RootDirectoryName."/interface/main/uploaddir/document_logos/",$printTempData);
				$printTempData = str_ireplace("../../main/uploaddir/",$webServerRootDirectoryName.$web_RootDirectoryName."/interface/main/uploaddir/",$printTempData); */
				$data .= "<page  backtop=".$top_margin." backleft=".$left_margin.">".$printTempData."</page>";
			}
			else{
				$sentPrintTempData = $row['tempData'];
				/* $sentPrintTempData = str_replace("/$web_RootDirectoryName/interface/common/new_html2pdf/","",$row['tempData']);
				$sentPrintTempData = str_ireplace($GLOBALS['webroot']."/interface/reports/new_html2pdf/","",$sentPrintTempData);
				$sentPrintTempData = str_ireplace("../../interface/main/uploaddir/","../../main/uploaddir/",$sentPrintTempData);
				$sentPrintTempData = str_ireplace($GLOBALS['webroot']."/interface/main/uploaddir/document_logos/",$webServerRootDirectoryName.$web_RootDirectoryName."/interface/main/uploaddir/document_logos/",$sentPrintTempData);
				$sentPrintTempData = str_ireplace("../../main/uploaddir/",$webServerRootDirectoryName.$web_RootDirectoryName."/interface/main/uploaddir/",$sentPrintTempData); */
				$sentdata.= "<page backtop=".$top_margin." backleft=".$left_margin.">".$sentPrintTempData."</page>";
			}
			
		}
	//$data .= "</table>";	
	}
}		
$pdfData = "";
$file_name = "";
if(trim($data) != "" || trim($sentdata) != ""){
	//$file_name = $CLSReports->get_pdf_name($_SESSION["authId"],'check');
	//echo $file_name;
	//$fp = fopen('new_html2pdf/'.$file_name.'.html','w');
	
	
	$pdfData = $data.$sentdata;
	
	
	//$intBytes = fputs($fp,$pdfData);
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
	?>
	<html>
		
		<body class="body_c">
        <script type="text/javascript">
        function sndFxFun(fxNmbr,fxNmbrCc1,fxNmbrCc2,fxNmbrCc3,serverIp,hid_consult_letter_ids,refPhyId,send_fax_subject, phy,phycc1,phycc2,phycc3){
            $.ajax({
                type: "GET",
                url: "<?php echo $GLOBALS['webroot']; ?>/library/html_to_pdf/createPdf.php?saveOption=fax&pdf_name="+serverIp.replace('.html', '')+"&file_location="+serverIp,
				success: function(resp){
					$.ajax({
						type: "GET",
						url: "consult_letters_report_fax.php?send_fax_number="+fxNmbr+"&txtFaxNoCc1="+fxNmbrCc1+"&txtFaxNoCc2="+fxNmbrCc2+"&txtFaxNoCc3="+fxNmbrCc3+"&txtFaxPdfName="+serverIp+"&consult_letter_ids="+hid_consult_letter_ids+"&ref_phy_id="+refPhyId+"&send_fax_subject="+send_fax_subject+'&phyname='+phy+'&phycc1name='+phycc1+'&phycc2name='+phycc2+'&phycc3name='+phycc3,
						success: function(resp){
							resp = $.trim(resp);
							resp = $.parseJSON(resp);
							var alertSuccess = '';
							var alertError = '';
							//=================UPDOX FAX WORKS GET DATA FROM SENDFAX.PHP FILE=================
							//PRIMARY RECIPENT DATA
							if( typeof(resp.primary.fax_id) !== 'undefined' ){ //resp.primary.
								alertSuccess += 'Primary: '+resp.primary.fax_id+'\n';
							}  
							else if( typeof(resp.primary.error) !== 'undefined' ) alertError += 'primary: '+resp.primary.error+'\n';
							//CC1 RECIPENT DATA
							if( typeof(resp.cc1.fax_id) !== 'undefined' ){
								alertSuccess += 'CC1: '+resp.cc1.fax_id+'\n';
							}
							else if( typeof(resp.cc1.error) !== 'undefined' ) alertError += 'CC1: '+resp.cc1.error+'\n';
							//CC2 RECIPENT DATA
							if( typeof(resp.cc2.fax_id) !== 'undefined' ){ 
								alertSuccess += 'CC2: '+resp.cc2.fax_id+'\n';
							}
							else if( typeof(resp.cc2.error) !== 'undefined' ) alertError += 'CC2: '+resp.cc2.error+'\n';
							//CC3 RECIPENT DATA	
							if( typeof(resp.cc3.fax_id) !== 'undefined' ){
								alertSuccess += 'CC3: '+resp.cc3.fax_id+'\n';
							}
							else if( typeof(resp.cc3.error) !== 'undefined' ) alertError += 'CC3: '+resp.cc3.error+'\n';
							
							var alertMsg = '';
							if(alertSuccess!=='')
								alertMsg += 'Fax sent successfully:  \n'+alertSuccess+'\n';  //<br />'+alertSuccess+"<br />"
							if(alertError!=='')
								alertMsg += 'Fax sending Failed: \n'+alertError+'\n';   // <br />'+alertError+"<br />"
							
							fAlert(alertMsg);
							if(parent.document.getElementById("div_load_image")) {
								parent.document.getElementById("div_load_image").style.display="none";
								parent.document.getElementById("hiddselectReferringPhy").value="";
								parent.document.getElementById("selectReferringPhy").value="";
								parent.document.getElementById("send_fax_number").value="";
								parent.document.getElementById("send_fax_div").style.display="none";
							}
							/* alert(r); */
						}
					});
                }
            });
        }
        </script>			
			<?php
            if($_REQUEST['sendFaxCase']=="1"){
				$phy = $_REQUEST['selectReferringPhy'];
				$phycc1 = $_REQUEST['selectReferringPhyCc1'];
				$phycc2 = $_REQUEST['selectReferringPhyCc2'];
				$phycc3 = $_REQUEST['selectReferringPhyCc3'];
				
				$faxnumber		= $_REQUEST['send_fax_number'];
				$faxnumberCc1   = $_REQUEST['send_fax_numberCc1'];
				$faxnumberCc2   = $_REQUEST['send_fax_numberCc2'];
				$faxnumberCc3   = $_REQUEST['send_fax_numberCc3'];
				if(trim($faxnumber)=="" && $faxnumberCc1!=''){
					$faxnumber=$faxnumberCc1;
					$faxnumberCc1="";
				}
				?>
            	<script>sndFxFun('<?php echo $faxnumber;?>','<?php echo $faxnumberCc1;?>','<?php echo $faxnumberCc2;?>','<?php echo $faxnumberCc3;?>','<?php echo $file_location;?>','<?php echo $rqHidConsultLeterId; ?>','<?php echo $refPhyId; ?>','<?php echo $_REQUEST['send_fax_subject'];?>', '<?php echo $phy;?>','<?php echo $phycc1;?>','<?php echo $phycc2;?>','<?php echo $phycc3;?>');</script>
            <?php
			}else{ ?>
				<script type="text/javascript">
					var file_name = '<?php echo $file_location; ?>';
					var parWidth = parent.document.body.clientWidth;
					var parHeight = parent.document.body.clientHeight;
					window.open('<?php echo $GLOBALS['webroot']; ?>/library/html_to_pdf/createPdf.php?op=p&onePage=false&file_location='+file_name,'pdfPrint','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'');
				</script>
			<?php } ?>	
		</body>
	</html>
	<?php
	}
}else{
?>
	<html>
	<head>	
	<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
	<link rel="stylesheet" href="<?php echo $css_patient;?>" type="text/css">
	</head>
	<body class="body_c">
	<table align="center" width="100%" border="0" cellpadding="1" cellspacing="1">
		<tr class="text_9" height="20" bgcolor="#FFFFFF" valign="top">
			<td align="center" class="failureMsg">No Result.</td>
		</tr>
	</table>
	</body>
	</html>
<?php
}
?>
<script type="text/javascript">
	if(parent.show_img) {
		parent.show_img('none');
	}
</script>