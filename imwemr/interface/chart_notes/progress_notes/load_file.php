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
 *  
 * File: load_file.php
 * Purpose: This file loads data of a particular OP note in OP note section in work view title bar.
 * Access Type : Direct
 */

require_once("../../../config/globals.php");
require_once($GLOBALS['fileroot']."/library/classes/work_view/OperativeNote.php");
require_once($GLOBALS['fileroot']."/library/classes/work_view/PnTemplate.php");
require_once($GLOBALS['fileroot']."/library/classes/work_view/PnReports.php");
require_once($GLOBALS['fileroot']."/library/classes/functions.smart_tags.php");
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
$library_path = $GLOBALS['webroot'].'/library';
$patient_id = $_SESSION["patient"];
$oPnData		= new OperativeNote;
$objPnTemp 		= new PnTemplate;
$oPnRep 		= new PnReports;
$OBJsmart_tags 	= new SmartTags;

if((!isset($_GET["elem_pnRepId"]) || empty($_GET["elem_pnRepId"]) )){
	echo("no report id selected .");
	$flgStoExec = 1;	
}



if(!isset($flgStoExec) || empty($flgStoExec)){ //$flgStoExec


	$elem_edit_id = $_GET["elem_pnRepId"];
	$arrTemp = $oPnRep->getRecordInfo($elem_edit_id);
	/****Getting media if available****/
	$media_available=true;
	$media_res = imw_query("SELECT * FROM ".constant('IMEDIC_SCAN_DB').".media WHERE source='opnote' AND source_id='".$elem_edit_id."' AND deleted=0");
	if($media_res && imw_num_rows($media_res)>0){
		$media_available=true;
	}
	/**********************************/
	
	if($arrTemp != false){
	
		$tempId = $arrTemp["tempId"];
		$status = $arrTemp["status"];
		$sc_emr_template_name = trim($arrTemp["sc_emr_template_name"]);
		$sc_emr_operative_report_id = $arrTemp["sc_emr_operative_report_id"];
		$sc_emr_laser_report_id = $arrTemp["sc_emr_laser_report_id"];
		$sc_emr_injection_report_id = $arrTemp["sc_emr_injection_report_id"];
		$elem_pnData = $arrTemp["txt_data"]; //Template data
		$sc_emr_iasc_appt_id = $arrTemp["sc_emr_iasc_appt_id"];
		
		//REPLACING SMART TAG OPTONS WITH NON-ANCHOR STRING.
			$regpattern='|<a class="cls_smart_tags_link" href=(.*) id=(.*)>(.*)</a>|U'; 
			$elem_pnData = preg_replace($regpattern, "\\3", $elem_pnData);
		/* Change image path to include full path for pdf creation */
		$elem_pnData = $oPnData->modifyImgPath4pdf($elem_pnData);
		//-- --//
		
		$strpixls = $arrTemp["signature"];
		
		if(!empty($tempId)){
			$arrTemp = $objPnTemp->getRecordInfo($tempId);
			$templateName = trim($arrTemp[1]); //Template Name
			
			
		}
	}
	$dataPath = data_path();
	$srcDir = substr(data_path(1), 0, -1);

	//start code to get opnotes related to scEMR
	
	if($sc_emr_template_name || $sc_emr_operative_report_id) {
	
		if(constant("IMEDIC_SC")!='' && $sc_emr_operative_report_id) {
			$scemr_fac_id = "";
			if(trim($sc_emr_iasc_appt_id)) {
				$fac_id_qry = "SELECT sa_facility_id FROM schedule_appointments WHERE id = '".trim($sc_emr_iasc_appt_id)."' LIMIT 0,1";
				$fac_id_res = imw_query($fac_id_qry) or die(imw_error());
				if(imw_num_rows($fac_id_res)>0) {
					$fac_id_row = imw_fetch_assoc($fac_id_res);
					$scemr_fac_id = $fac_id_row["sa_facility_id"];	
				}
			}
			include(dirname(__FILE__)."/../../accounting/connect_sc.php");
			//START IOL SCAN UPLOAD IMAGE
			$ViewOpRoomRecordQry = "SELECT oprm.operatingRoomRecordsId, oprm.iol_ScanUpload, oprm.iol_ScanUpload2 
			FROM ".$dbname.".operativereport oprp 
			INNER JOIN ".$dbname.".operatingroomrecords oprm ON (oprm.confirmation_id = oprp.confirmation_id)
			WHERE  oprp.oprativeReportId = '".$sc_emr_operative_report_id."'
			";
			$ViewOpRoomRecordRes = imw_query($ViewOpRoomRecordQry) or die(imw_error()); 
			$ViewOpRoomRecordNumRow = imw_num_rows($ViewOpRoomRecordRes);
			$ViewOpRoomRecordRow = imw_fetch_array($ViewOpRoomRecordRes); 
			$operatingRoomRecordsId = $ViewOpRoomRecordRow["operatingRoomRecordsId"];
			$iol_ScanUpload = $ViewOpRoomRecordRow["iol_ScanUpload"];
			$iol_ScanUpload2 = $ViewOpRoomRecordRow["iol_ScanUpload2"];
			if($ViewOpRoomRecordNumRow>0) {
			if($iol_ScanUpload!='' || $iol_ScanUpload2!=''){
				$elem_pnData.='<table width="100%"><tr><td class="text_10"><strong>IOL Scanned Image</strong> </td></tr>';
			}
	
			if($iol_ScanUpload!=''){
				$bakImgResourceOproom = imagecreatefromstring($iol_ScanUpload);
				$fileFullPath1 = $dataPath.'oproom1_'.$_SESSION['authId'].'.jpg';
				imagejpeg($bakImgResourceOproom,$fileFullPath1);
				$newSize=' height="100"';
				$priImageSize=array();
				if(file_exists($fileFullPath1)) {
					$priImageSize = getimagesize($fileFullPath1);
					if($priImageSize[0] > 395 && $priImageSize[1] < 840){
						$newSize = $oPnData->imageResizeSc(680,400,500);						
						$priImageSize[0] = 500;
					}					
					elseif($priImageSize[1] > 840){
						$newSize = $oPnData->imageResizeSc($priImageSize[0],$priImageSize[1],600);						
						$priImageSize[1] = 600;
					}
					else{					
						$newSize = $priImageSize[3];
					}							
					if($priImageSize[1] > 800 ){					
						echo '<newpage>';												
					}
				}
				$imagePath1 = str_ireplace($dataPath,'../../data/'.constant("PRACTICE_PATH").'/', $fileFullPath1);
				$elem_pnData.='<tr><td class="text_10"><img src="'.$imagePath1.'" '.$newSize.'></td></tr>';
			}
	
			if($iol_ScanUpload2!=''){
				$bakImgResourceOproom = imagecreatefromstring($iol_ScanUpload2);
				$fileFullPath2 = $dataPath.'oproom2_'.$_SESSION['authId'].'.jpg';
				imagejpeg($bakImgResourceOproom,$fileFullPath2);
				$priImageSize=array();
				if(file_exists($fileFullPath2)) {
					$priImageSize = getimagesize($fileFullPath2);
					$newSize = 'height="100"';
				if($priImageSize[0] > 395 && $priImageSize[1] < 840){
					$newSize = $oPnData->imageResizeSc(680,400,500);						
					$priImageSize[0] = 500;
				}elseif($priImageSize[1] > 840){
					$newSize = $oPnData->imageResizeSc($priImageSize[0],$priImageSize[1],600);						
					$priImageSize[1] = 600;
				}
				else{					
					$newSize = $priImageSize[3];
				}							
				if($priImageSize[1] > 800 ){					
					echo '<newpage>';												
				}
			}
			$imagePath2 = str_ireplace($dataPath,'../../data/'.constant("PRACTICE_PATH").'/', $fileFullPath2);
			$elem_pnData.='<tr><td class="text_10"><img src="'.$imagePath2.'" '.$newSize.'></td></tr>';
		}			
		if($iol_ScanUpload!='' || $iol_ScanUpload2!=''){
			$elem_pnData.='</table>';
		}
	}
	//END IOL SCAN UPLOAD IMAGE
	imw_close($link_ocean);
	include(dirname(__FILE__)."/../../accounting/connect_imw.php");
	}
		if(!trim($sc_emr_laser_report_id) && !trim($sc_emr_injection_report_id)) {
			$elem_pnData = nl2br($elem_pnData);
			$elem_pnData = strip_tags($elem_pnData,' <p> <br> <img> <strong> </strong><table><tr><td>');
		}
		$elem_pnData = str_ireplace("</p><br />","</p>",$elem_pnData);
	
		$elem_pnData = str_ireplace("&Acirc;","",$elem_pnData);
			
	}
	
	$elem_pnData = str_ireplace("{PHYSICIAN SIGNATURE}","",$elem_pnData);
	//stop code to get opnotes related to scEMR
	//GET HTTP OR HTTPS ADDRESS
	if($protocol == ''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }
	//Write on a file for pdf
	$TData = "";
	$TData .= $elem_pnData;
	$TData = str_ireplace(""," ",$TData);
	$TData = str_ireplace("&Acirc;","",$TData);
	$TData = preg_replace('/font-family.+?;/', "", $TData);
	$TData = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/','../../data/'.PRACTICE_PATH.'/',$TData);
	$TData = str_ireplace('/'.$RootDirectoryName.'/data/'.PRACTICE_PATH.'/','../../data/'.PRACTICE_PATH.'/',$TData);
	
	//$physical_path=data_path();
	/*if($RootDirectoryName==PRACTICE_PATH){
		$TData = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/',$protocol.$myExternalIP.'/'.$RootDirectoryName.'/data/'.PRACTICE_PATH.'/',$TData);
	}else{
		$TData = str_ireplace('/'.$RootDirectoryName.'/data/'.PRACTICE_PATH.'/',$protocol.$myExternalIP.'/'.$RootDirectoryName.'/data/'.PRACTICE_PATH.'/',$TData);
		$TData = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/',$physical_path.'/',$TData);
	} */
	
	$TData = str_ireplace($GLOBALS['webroot'].'/library/images/','../../library/images/',$TData);
	$TData = str_ireplace('../../../library/images/','../../library/images/',$TData);
	//$TData = str_ireplace($GLOBALS['webroot']."/interface/common/".$htmlFolder."/","",$TData);
	//Save image of Signature
	$id = $elem_edit_id;
	$tblName = "pn_reports";
	$pixelFieldName = "signature";
	$idFieldName = "pn_rep_id";
	$imgPath = "";
	$saveImg = "3";
	//include(dirname(__FILE__)."/../../main/imgGd.php");
	//$TData .= "<img src=\"".realpath(dirname(__FILE__)."/../../common/new_html2pdf/tmp/".$gdFilename)."\" alt=\"alt image\">";
	
	$TDataNew = '<table cellpadding="0" cellspacing="0">
				<tr>
					<td style="font-size:11px;">'.$TData.'</td>
				</tr>
			 </table>	
	';
	if($handle){
		$err = fputs($handle,$TDataNew);	
		fclose($handle);
	}
	$file_path = write_html($TDataNew);
	?>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
	<?php
	if($file_path){
	?>
		<script>
			<?php 
			/*if(constant('AV_MODULE')=='YES' && isset($_GET['media_id']) && $_GET['media_id']>0){?>
				
				if( top.fmain) {
					if(top.fmain.document.getElementById('media_player_icon')){
						top.fmain.document.getElementById('media_player_icon').innerHTML = '<img src="../../common/record_av/images/icon_play_24.png" class="ml10" style="vertical-align:middle;cursor:pointer;" title="Play MultiMedia Messages"  border="0" onClick="window.top.showMultiMediaMessage(\'opnote\',\'<?php echo $_GET['media_id'];?>\');" />';
					}
				}
				
			<?php 
			}else{*/?>
				if( top.fmain) {
					if(top.fmain.document.getElementById('media_player_icon')){
						top.fmain.document.getElementById('media_player_icon').innerHTML = '';
					}
				}
			<?php
			//}?>	
			
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			html_to_pdf('<?php echo $file_path; ?>','p','',true);
		</script>
	<?php		
	}
}
?>