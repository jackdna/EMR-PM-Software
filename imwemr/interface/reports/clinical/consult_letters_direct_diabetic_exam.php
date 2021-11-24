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

$consultIds 		= $_REQUEST['consultIds'];
$hidd_consult_mod 	= $_REQUEST['hidd_consult_mod'];
if($consultIds)
{
	$qryGetConsultLetter = "select pcl.patient_consult_id, pcl.templateData as tempData,pcl.top_margin as topMargin,
							pcl.left_margin as leftMargin,CONCAT_WS(', ',usr.lname,usr.fname) as physicianFName,
							CONCAT_WS(', ',pd.lname ,pd.fname) as patientName, report_sent_date, patient_consult_id
							 from patient_consult_letter_tbl pcl
							 INNER JOIN patient_data pd ON pcl.patient_id = pd.id
							 INNER JOIN chart_master_table cmt ON (cmt.id = pcl.patient_form_id)	
							 LEFT JOIN users usr ON (usr.id = cmt.providerId)
							 where pcl.patient_consult_id IN(".$consultIds.")
							 ORDER BY usr.lname, pcl.templateName LIKE '%Red Alert%' DESC, pcl.templateName LIKE '%Green Alert%' DESC, pcl.patient_id, pcl.patient_consult_id
							 ";
	$rsGetConsultLetter = imw_query($qryGetConsultLetter) or die(imw_error());
	if($rsGetConsultLetter){
		if(imw_num_rows($rsGetConsultLetter)){
			//<link rel="stylesheet" href="'.$css_header.'" type="text/css">	
			//<link rel=stylesheet href="'.$css_patient.'" type="text/css">
		$data = '
				 <style>
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
					
							
				';
			$top_margin=$left_margin=0;
			while($row = imw_fetch_array($rsGetConsultLetter))
			{
				$top_margin=$row["topMargin"];
				$left_margin=$row["leftMargin"];
				
				$printTempData = "";
				$printTempData = str_replace("/$web_RootDirectoryName/interface/common/new_html2pdf/","",$row['tempData']);
				$printTempData = str_ireplace($GLOBALS['webroot']."/interface/reports/new_html2pdf/","",$printTempData);
				$printTempData = str_ireplace("../../interface/main/uploaddir/","../../main/uploaddir/",$printTempData);
				$data		   = "<page backtop=".$top_margin." backleft=".$left_margin.">".$printTempData."</page>";					
				
				//START CODE FOR DIRECT
				if($hidd_consult_mod == "consult_direct") {
					$pdfData = "";
					$file_name = "";
					if(trim($data) != "" )
					{
						$pdfData = $data;
						$setDirectHtmlName="directConsultReport_".$row['patient_consult_id'];
						$setDirectPdfName="directConsultReportPdf_".$row['patient_consult_id'];
						$pdfData = str_ireplace($GLOBALS['webroot']."/interface/reports/new_html2pdf/","",$pdfData);
						$pdfData = str_ireplace($GLOBALS['webroot']."/interface/common/new_html2pdf/","",$pdfData);
						$pdfData = str_ireplace($protocol.$Host.$GLOBALS['webroot']."/redactor/images/","../../../redactor/images",$pdfData);
                        
                        file_put_contents($fileBasePath.$setDirectHtmlName.'.html',$pdfData);
						
						$dir = explode('/',$_SERVER['HTTP_REFERER']);
						$httpPro = $dir[0];
						$httpHost = $dir[2];
						$httpfolder = $web_RootDirectoryName;
						$ip = $_SERVER['REMOTE_ADDR'];
						$myAddressNew='';
						if((stristr($myExternalIP,':') && !stristr($myExternalIP,':9443'))) {		
							$httpPro = 'http:';	
						}
						if(stristr($myExternalIP,':3601')) {
							$myAddressNew = $httpPro.'//'.$myExternalIP;
						}
						$myAddress = $httpPro.'//'.$myInternalIP.'/'.$httpfolder;
						
						$url = $myAddress."../../../library/html_to_pdf/createPdf.php?op=p&file_location=".$setDirectHtmlName.'&name='.$setDirectPdfName.'&saveOption=fax';
						$cur = curl_init();
						curl_setopt($cur,CURLOPT_URL,$url);
						curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
						curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);
						$data = curl_exec($cur);
						if(curl_error($cur))
						{
							echo 'error';//'Curl Error'.curl_error($cur);
							
						}
						else
						{
							if(file_exists($fileBasePath.$setDirectHtmlName.'.html'))
							{
								unlink($fileBasePath.$setDirectHtmlName.'.html');	
							}	
						}
						curl_close($cur);
						
					}			
				}
				//END CODE FOR DIRECT
			}
		}
	}		
	
}