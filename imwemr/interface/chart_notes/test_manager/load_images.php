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

require_once("../../../config/globals.php");

//To check pt logged in or not
require_once("../../../library/patient_must_loaded.php");

$library_path = $GLOBALS['webroot'].'/library';

include_once($GLOBALS['srcdir'].'/classes/common_function.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/wv_functions.php');
include_once($GLOBALS['srcdir'].'/classes/SaveFile.php');

$oSaveFile = new SaveFile($_SESSION["patient"]);
$testDname = $_REQUEST['testDname'];
$testDname1 = $testDname;
$examDate = $_REQUEST['examDate'];
$testType = $_REQUEST['testType'];
if($testDname=='B-Scan')		{$testDname1='BScan';}
elseif($testDname=='Cell Count')		{$testDname1='CellCount';}
else if($testDname=='External')		{$testDname1='discExternal';}
else if($testDname=='Fundus')		{$testDname1='Disc';}
else if($testDname=='HRT')			{$testDname1='NFA';}
else if($testDname=='IOL Master')	{$testDname1='IOL_Master';}
else if($testDname=='Laboratories')	{$testDname1='TestLabs';}
else if($testDname=='Topography')	{$testDname1='Topogrphy';}
else if($testDname=='Other')		{$testDname1='TestOther';}
else if($testType == 1) {
	if(stripos($testDname1,'customtests')===0){
		$testDname1 = 'CustomTests';
	}else{
		$testDname1 = 'TemplateTests';
	}

}


$test_id = $_REQUEST['test_id'];
$session_patient = $_SESSION['patient'];
$str = "";
$q2 = "SELECT *, DATE_FORMAT(modi_date,'".get_sql_date_format('','y')."') AS modi_date1,DATE_FORMAT(created_date,'".get_sql_date_format('','y')."') AS created_date1  
		FROM ".constant('IMEDIC_SCAN_DB').".scans s 
		WHERE s.image_form='".$testDname1."' 
			  AND  s.test_id='".$test_id."' 
			  AND s.status='0' 
			  AND s.patient_id='".$session_patient."' 
		ORDER BY site DESC";
$res2 = imw_query($q2);
if($res2 && imw_num_rows($res2)>0){
   $display = "block";//($str == "")?"block":"none";	
   $testDnameSpan = str_replace(" ","_",$testDname);
   $str .= '<div id="tab_'.$testDnameSpan.'_'.$test_id.'" class="table_collapse_autoW filmstrip" style="display:'.$display.';">';
   while($rs2 = imw_fetch_assoc($res2)){
		$scan_id		= $rs2["scan_id"];
		$scanType 		= $rs2["file_type"];
		$scanDt_cr 		= $rs2["created_date"];
		$scanDt_up 		= $rs2["modi_date1"]!='00-00-00' ? $rs2["modi_date1"] : $rs2["created_date1"];
		$scanPth 		= str_replace(".PDF",".pdf",$rs2['file_path']);
		$image_form 	= $rs2["image_form"];
		$scan_site		= intval($rs2['site']);
		$fileInfoArr 	= pathinfo($scanPth);
		$link_type_class= "jpg_colorbox";
		$link_file_path = $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH').$scanPth;
		$disc_full_path	= $oSaveFile->getFilePath($scanPth,'i');
		
		if(substr($link_file_path, -3)=='pdf'){$link_type_class="pdf_colorbox";}
		if(strtolower($scanType) == "application/pdf" || strtolower($fileInfoArr['extension']) == 'pdf' || strtoupper($fileInfoArr['extension']) == 'PDF'){
			$imgSrc_file = $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH').$scanPth;
			if(file_exists($disc_full_path)){
				$pdf_info = pathinfo($disc_full_path);
				$pdf_basename 	= $pdf_info['basename'];
				$pdf_dir	  	= $pdf_info['dirname'];
				$pdf_name	  	= $pdf_info['filename'];
				$pdf_thumbnail_dest	= $pdf_dir."/thumbnail";
				$pdf_thumb_dest	= $pdf_dir."/thumb";
				$pdf_jpg_dest	= $pdf_dir."/".$pdf_name.".jpg";
				
				$pdf_jthumb_dest= $pdf_thumb_dest."/".$pdf_name.".jpg";
				$pdf_jthumbnail_dest= $pdf_thumbnail_dest."/".$pdf_name.".jpg";
				
				if(is_dir($pdf_thumb_dest) == false){
					mkdir($pdf_thumb_dest, 0777, true);
				}
				$source = realpath($pdf_dir."/".$pdf_basename).'[0]';
				$exe_path = $GLOBALS['IMAGE_MAGIC_PATH'];
				if(!empty($exe_path)){$exe_path .= "/";}else{$exe_path='';}
				if (!file_exists($pdf_jpg_dest)){
					if(constant("STOP_CONVERT_COMMAND")=="YES") {
						//STOP CONVERT COMMAND
					}else {
						exec($exe_path.'convert -flatten "'.$source.'" -quality 95 -thumbnail 1500 -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$pdf_jpg_dest.'"');
					}
				}
				if (!file_exists($pdf_jthumb_dest)){
					$http_pdf_path  = substr($link_file_path,0,strlen($link_file_path)-strlen($pdf_basename));
					$imgSrc 		= $http_pdf_path."/thumbnail/".$pdf_name.".jpg";
					if (!file_exists($pdf_jthumbnail_dest)){
						if(constant("STOP_CONVERT_COMMAND")=="YES") {
							$http_pdf_path  = substr($link_file_path,0,strlen($link_file_path)-strlen($pdf_basename));
							$imgSrc 		= $GLOBALS["webroot"].'/library/images/pdfimg.png';
						}else {
							exec($exe_path.'convert -flatten "'.$source.'" -quality 95 -thumbnail 500 -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$pdf_jthumbnail_dest.'"');
						}
					}
				}else{
					$http_pdf_path  = substr($link_file_path,0,strlen($link_file_path)-strlen($pdf_basename));
					$imgSrc 		= $http_pdf_path."/thumb/".$pdf_name.".jpg";
				}
				//$http_pdf_path  = substr($link_file_path,0,strlen($link_file_path)-strlen($pdf_basename));
				//$imgSrc 		= $http_pdf_path."/thumbnail/".$pdf_name.".jpg";
				$link_file_path	= $http_pdf_path.$pdf_basename;
				$thumb_img = '<a class="'.$link_type_class.' cboxElement" href="'.$link_file_path.'" title="'.$testDname.' - '.$examDate.'">
							<img id="imgId'.$scanId.'" src="'.$imgSrc.'" style="cursor:hand;border:1px solid #CCC;"  alt="Pdf File" class="border thumb_img"></a>
							';
			}else {
				continue;
			}
		}else{
			$tempDir = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/PatientId_".$_SESSION["patient"]."/tmp";
								if(is_dir($tempDir) == false){
									mkdir($tempDir, 0777, true);
								}
								
								$pathThumb = $imgSrc = "";
								$imgPath = $oSaveFile->getFilePath($scanPth,"i");
								$imgPathInfo = pathinfo($imgPath);
								$imgDir 	= $oSaveFile->getFileDir($imgPath);
								$imgName 	= $imgPathInfo['basename'];
								$imgDirNameARR	= explode($GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/PatientId_".$_SESSION["patient"]."/",$imgPathInfo['dirname']);
								$imgDirName		= $imgDirNameARR[1];
								unset($imgDirNameARR);
								if(!is_dir($imgDir."/thumbnail")){
									mkdir($imgDir."/thumbnail",0700);
								}
								$thumbPath = realpath($imgDir."/thumbnail")."/".$imgName;//die();
								if(!file_exists($thumbPath)){
									$oSaveFile->createThumbs($imgPath,$thumbPath);
								}
								if(!is_dir($imgDir."/thumb")){
									mkdir($imgDir."/thumb",0700);
								}
								$mthumbPath = realpath($imgDir."/thumb")."/".$imgName;//die();
								if(!file_exists($mthumbPath)){
									$oSaveFile->createThumbs($imgPath,$mthumbPath,500,500);
								}
								$thumb_img = '<a class="iviewer '.$link_type_class.' cboxElement" href="'.$link_file_path.'" title="'.$testDname.' - '.$examDate.'" target="_blank"><img src="'.data_path(1).$oSaveFile->pDir."/".$imgDirName."/thumbnail/".$imgName.'" class="border thumb_img"></a>';
		}
		
		//arrays to use in JS
		$link_file_path = str_replace('\\', '/', $link_file_path);
		$scanImgArr['a'.$scan_id] 	= $link_file_path;
		$TestWiseImgs[$testDname.'@'.$examDate][]	= $link_file_path;
		$pic_site = $arr_scan_site[$scan_site];
		$site_hide='';
		if($scan_site==0){$site_hide=' hide';}
   
   $str .= '<div style="display:inline-block">
   			<span class="badge badge-primary site'.$site_hide.'">'.$pic_site.'</span>'.$thumb_img.'
		<div class="scanDate" onClick="SelectMe(this,\''.$testDname.'\',\''.$test_id.'\',\''.$scan_id.'\')"><span class="check">&#10003; </span>'.$scanDt_up.'</div></div>';
   }
   $str .= '</div>';
}
echo json_encode(array("html"=>$str,"scanImgArr"=>$scanImgArr,"TestWiseImgs"=>$TestWiseImgs));//
die();
?>