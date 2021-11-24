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
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");
include_once(dirname(__FILE__)."/../../../config/globals.php");

$fileBasePath = data_path().'UserId_'.$_SESSION['authId'].'/tmp/';

$chkbx_id_comma = $_REQUEST['chkbx_id_comma'];
$hidd_consult_mod = $_REQUEST['hidd_consult_mod'];
if($chkbx_id_comma) {
	$qryGetConsultLetter = "select pcl.patient_consult_id, pcl.templateData as tempData,pcl.top_margin as topMargin,
							pcl.left_margin as leftMargin,CONCAT_WS(', ',usr.lname,usr.fname) as physicianFName,
							CONCAT_WS(', ',pd.lname ,pd.fname) as patientName, report_sent_date, patient_consult_id
							 from patient_consult_letter_tbl pcl
							 INNER JOIN patient_data pd ON pcl.patient_id = pd.id
							 INNER JOIN chart_master_table cmt ON (cmt.id = pcl.patient_form_id)	
							 LEFT JOIN users usr ON (usr.id = cmt.providerId)
							 where pcl.patient_consult_id IN(".$chkbx_id_comma.")
							 ORDER BY usr.lname, pcl.templateName LIKE '%Red Alert%' DESC, pcl.templateName LIKE '%Green Alert%' DESC, pcl.patient_id, pcl.patient_consult_id
							 ";
	$rsGetConsultLetter = imw_query($qryGetConsultLetter) or die(imw_error());
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
					//$qryUpdate = "update patient_consult_letter_tbl set report_sent_date = CURDATE() ".$qryUpdateAdd." where patient_consult_id = '".$row["patient_consult_id"]."' ";
					//$rsUpdate = imw_query($qryUpdate);
					$printTempData = "";
					$printTempData = str_ireplace("/$web_RootDirectoryName/interface/common/new_html2pdf/","",$row['tempData']);
					$printTempData = str_ireplace($GLOBALS['webroot']."/interface/reports/new_html2pdf/","",$printTempData);
					$printTempData = str_ireplace("../../interface/main/uploaddir/","../../main/uploaddir/",$printTempData);
					$data 		  .= "<page  backtop=".$top_margin." backleft=".$left_margin.">".$printTempData."</page>";					
					$data_fax 	   = "<page  backtop=".$top_margin." backleft=".$left_margin.">".$printTempData."</page>";
				}
				else{
					$sentPrintTempData = "";
					$sentPrintTempData = str_replace("/$web_RootDirectoryName/interface/common/new_html2pdf/","",$row['tempData']);
					$sentPrintTempData = str_ireplace($GLOBALS['webroot']."/interface/reports/new_html2pdf/","",$sentPrintTempData);
					$sentPrintTempData = str_ireplace("../../interface/main/uploaddir/","../../main/uploaddir/",$sentPrintTempData);
					$sentdata		  .= "<page backtop=".$top_margin." backleft=".$left_margin.">".$sentPrintTempData."</page>";					
					$sentdata_fax 	   = "<page backtop=".$top_margin." backleft=".$left_margin.">".$sentPrintTempData."</page>";
				}
				
				//START CODE FOR FAX
				if($hidd_consult_mod == "consult_fax") {
					$pdfData = "";
					$file_name = "";
					if(trim($data_fax) != "" || trim($sentdata_fax) != ""){
						$pdfData = $data_fax.$sentdata_fax;
						$setFaxHtmlName="faxConsultReport_".$row['patient_consult_id'];
						$pdfData = str_ireplace($GLOBALS['webroot']."/interface/reports/new_html2pdf/","",$pdfData);
						$pdfData = str_ireplace($GLOBALS['webroot']."/interface/common/new_html2pdf/","",$pdfData);
						$pdfData = str_ireplace($protocol.$Host.$GLOBALS['webroot']."/redactor/images/","../../../redactor/images",$pdfData);
                        //write_html($setFaxHtmlName.'.html');
                        
						file_put_contents($fileBasePath.$setFaxHtmlName.'.html',$pdfData);
					}			
				}
				//END CODE FOR FAX
				
			}
		}
	}		
	//die($data);		
}
$pdfData = "";
$file_name = "";
if(trim($data) != "" || trim($sentdata) != ""){
	$file_name = $_SESSION['authId'].'_'.date('Ymdhis');
	$fp = fopen($fileBasePath.$file_name.'.html','w');
	$pdfData = $data.$sentdata;
	$intBytes = fputs($fp,$pdfData);
	
	$Host = $_SERVER['HTTP_HOST'];
	if($protocol == ''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }

	//CODE for fax 
	$getPCIP=$_SESSION["authId"];			
	$arrFind=array(".","::",":");
	$arrRepl=array("_","_","_");	
	$getIP=str_ireplace($arrFind,$arrRepl,$getPCIP);
	$setFaxHtmlName="faxConsultReport_".$getIP;
	$pdfData = str_ireplace($GLOBALS['webroot']."/interface/reports/new_html2pdf/","",$pdfData);
	$pdfData = str_ireplace($GLOBALS['webroot']."/interface/common/new_html2pdf/","",$pdfData);
	$pdfData = str_ireplace($protocol.$Host.$GLOBALS['webroot']."/redactor/images/","../../../redactor/images",$pdfData);
	file_put_contents($fileBasePath.$setFaxHtmlName.'.html',$pdfData);
	//write_html($setFaxHtmlName.'.html');
	
	fclose($fp);
}
?>
<html>
<!--    <link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
    <link rel="stylesheet" href="<?php echo $css_patient;?>" type="text/css">
    <body class="body_c">
    <script type="text/javascript" src="../../../js/jquery.js"></script>
    <script type="text/javascript">
    /*
	function sndFxFun(fxNmbr,fxNmbrCc1,fxNmbrCc2,fxNmbrCc3,serverIp,hid_consult_letter_ids,refPhyId){
        $.ajax({
            type: "GET",
            url: "../../common/new_html2pdf/createPdf.php?saveOption=fax&name=faxConsultLetterReportPdf_"+serverIp+"&htmlFileName=faxConsultReport_"+serverIp,
            success: function(resp){
                $.ajax({
                    type: "GET",
                    url: "../../reports/consult_letters_report_fax.php?send_fax_number="+fxNmbr+"&txtFaxNoCc1="+fxNmbrCc1+"&txtFaxNoCc2="+fxNmbrCc2+"&txtFaxNoCc3="+fxNmbrCc3+"&txtFaxPdfName=faxConsultLetterReportPdf_"+serverIp+"&consult_letter_ids="+hid_consult_letter_ids+"&ref_phy_id="+refPhyId,
                    success: function(r){
                        //alert(r)
                    }
                });
            }
        });
    }*/
    </script>-->

<?php
if($intBytes !== false && $hidd_consult_mod == "consult_print"){
	?>
            <form name="frm_print_window" action="../../../library/html_to_pdf/createPdf.php" method="get" target="_self">
            	<input type="hidden" name="op" value="p">
                <input type="hidden" name="onePage" value="false">
                <input type="hidden" name="file_location" value="<?php echo $fileBasePath.$file_name;?>">
                <input type="hidden" name="name" value="<?php echo $file_name;?>">
            </form>
				<script type="text/javascript">								
                    document.frm_print_window.submit();
                </script>
	<?php
}elseif($hidd_consult_mod == "consult_print"){
	
}else {
?>
	<table align="center" width="100%" border="0" cellpadding="1" cellspacing="1">
		<tr class="text_9" height="20" bgcolor="#FFFFFF" valign="top">
			<td align="center" class="failureMsg">No Result.</td>
		</tr>
	</table>
<?php
}
?>
</body>
</html>
