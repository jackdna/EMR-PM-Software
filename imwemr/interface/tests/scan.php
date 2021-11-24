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
File: scanImages.php
Purpose: This file provides Scan, Upload and Preview section in tests.
Access Type : Direct
*/
?>
<?php
set_time_limit(90);
require_once(dirname(__FILE__)."/../../config/globals.php");
require_once("../../library/patient_must_loaded.php");
require_once("../../library/classes/class.tests.php");
require_once("../../library/classes/SaveFile.php");
$objTests				= new Tests;
$patient_id 	= $_SESSION['patient'];
//MAKING OBJECT TO SAVE IMAGE FILES
$oSaveFile = new SaveFile($patient_id);

$form_id 			= $_REQUEST["formId"];
$scan_id 			= $_SESSION['document_scan_id'];
$userauthorized 	= $_SESSION['authId'];
$testId 			= $_REQUEST["testId"];
$test_temp_id 		= $_REQUEST["test_temp_id"];
$test_master_id 	= $_REQUEST["test_master_id"];
$formName 			= $_REQUEST['formName'];
$show 				= $_REQUEST['show'];
$library_path 		= $GLOBALS['webroot'].'/library';

if(!isset($_REQUEST["formId"])){
	if(empty($form_id)){
	$form_id = (isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"])) ? $_SESSION["finalize_id"] : $_SESSION['form_id'];
	}
}

//port
if($_SERVER["SERVER_PORT"] == 80){
	$phpHTTPProtocol="http://";
}
if($phpServerIP != $_SERVER['HTTP_HOST']){
	$phpServerIP = $_SERVER['HTTP_HOST'];
	$GLOBALS['php_server'] = $phpHTTPProtocol.$phpServerIP.$phpServerPort.$web_root;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>TESTS: Scan</title>
<link href="<?php echo $library_path; ?>/css/tests.css?<?php echo filemtime('../../library/css/tests.css');?>" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/common.css?<?php echo filemtime('../../library/css/common.css');?>" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/remove_checkbox.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/lightbox/lightbox.css" rel="stylesheet">
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]--> 
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
<script src="<?php echo $library_path; ?>/js/jquery.mCustomScrollbar.concat.min.js"></script> 
<script src="<?php echo $library_path; ?>/js/common.js?<?php echo filemtime('../../library/js/common.js');?>" type="text/javascript"></script>
<script src="<?php echo $library_path; ?>/js/tests.js?<?php echo filemtime('../../library/js/tests.js');?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/lightbox/lightbox.js"></script>
<script type="text/javascript">
var imgPath 		= "<?php echo $GLOBALS['webroot'];?>";
var elem_per_vo 	= "<?php echo $elem_per_vo;?>";
var zPath			= "<?php echo $GLOBALS['rootdir'];?>";
var web_root = "<?php echo $GLOBALS['php_server'];?>";
var scan_container_height = 350;
	
function isIE () {
	var myNav = navigator.userAgent.toLowerCase();
	return (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
}
//if (isIE() <= 9) {top.window.resizeTo(680, 710);}
//else{top.window.resizeTo(800, 710);}

var oPO = ((typeof(window.opener)!="undefined") && (typeof(window.opener.oPO)!="undefined"))? window.opener.oPO : null;

function submit12(){
	//browser = get_browser();
	//if(browser == "ie")
	upload(document.compareFrm)
	//else if(browser == "chrome")
	//document.compareFrm.submit();
}
function frm_submit(){
	document.uploadFrm.submit();
}

window.onbeforeunload = function(){ 	
	//if any code to run before closing of popup.
		
}
</script>
</head>
<body onkeydown=onKeyDown("submit12()")>
<form name="backFrm" method="post">
    <input type="hidden" name="formName" value="<?php echo $formName; ?>">
    <input type="hidden" name="show" value="<?php echo $show; ?>">
    <input type="hidden" name="sId" value="">		
    <input type="hidden" name="formId" value="<?php echo $form_id; ?>">	
    <input type="hidden" name="testId" value="<?php echo $testId;?>">
</form>
<div class="testtbar"><b><?php echo ucwords($show);?> Documents > <?php if($formName=='Topogrphy'){echo "Topography"; }else { echo $formName; }?></b></div>
<div class="clearfix"></div>
<!--<div class="tstopt">-->
<?php if( $show == "upload" ){
	if($_POST){
		$comment1 = $_POST['comment'];
		$site = $_POST['site'];
		if($comment1 || $site) {
			$chkCmntQry = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans 
							WHERE patient_id='$patient_id' 
								  AND image_form='$formName' 
								  AND file_type != '' 
								  AND scan_or_upload='upload' 
								  AND scan_id='$scan_id'";
			$chkCmntRes =imw_query($chkCmntQry);
			if(imw_num_rows($chkCmntRes)>0){
				$chkCmntRow = imw_fetch_array($chkCmntRes);
				$chkDocUploadDate = $chkCmntRow['doc_upload_date'];
				//$scanOrUploadDate = '$chkDocUploadDate';
				$explDtTm = explode(' ',$chkDocUploadDate);
				list($yr, $mnth, $dy) = explode('-',$explDtTm[0]);
				list($hr, $min, $scnd) = explode(':',$explDtTm[1]);
				$chkNewDt = date('y-m-d H:i:s', mktime($hr,$min,$scnd-120,$mnth,$dy,$yr));
			}
			
			$tmp = "";
			if(!empty($form_id)){
				 $tmp = "form_id ='".$form_id."' && ";	
			}else{
				 $tmp = "test_id = '".$testId."' && ";
			} 		
			$qry = "UPDATE ".constant("IMEDIC_SCAN_DB").".scans 
					SET multi_doc_upload_comment = '$comment1' 
					WHERE patient_id='$patient_id' 
						  AND scan_id ='$scan_id'";
			$res = imw_query($qry);
			 echo "<script>window.close();</script>";
		 }else{
			echo "<script>window.close();</script>";
		 }
	}
	$tmp = "";
	
	if(!empty($form_id)){$tmp = "form_id ='".$form_id."' && ";}
	else{$tmp = "test_id = '".$testId."' && ";}
	
	$selQry = "SELECT DATE_FORMAT(doc_upload_date,'".getDateFormatDB()." %h:%i:%s') AS crtDate1,multi_doc_upload_comment 
			   FROM ".constant("IMEDIC_SCAN_DB").".scans 
			   WHERE patient_id = '$patient_id' 
			   		AND image_form='".$formName."' 
					AND scan_or_upload='upload' 
			   ORDER BY `doc_upload_date` DESC LIMIT 0,1";
	$resQry = imw_query($selQry);
	$rowQry = imw_fetch_array($resQry);
	?>	
    <!-- Upload -->	
    
    <div class="text-center"> 
    <?php  $arrBrow = browser(); // --defined in sql.php file------
    if($arrBrow['name'] == 'msie' && $arrBrow['version'] < 10){?>
    <img src="../../images/active.gif" width="16" height="16">
    <iframe src="../common/csxthumbupload.php?formName=<?php echo $formName;?>&form_id=<?php echo $form_id;?>&testId=<?php echo $testId;?>&upload_url=<?php echo urlencode($GLOBALS['php_server']."/interface/chart_notes/upload_test.php?imwemr=".session_id()."&formName=".$formName."&form_id=".$form_id."&testId=".$testId."&activex=1&test_temp_id=".$test_temp_id."");?>" width="550" height="520" scrolling="yes"> </iframe> 
        <?php }else{?>
    <iframe src="../../library/upload/index.php?formName=<?php echo $formName;?>&form_id=<?php echo $form_id;?>&testId=<?php echo $testId;?>&upload_url=<?php echo urlencode("chart_notes/upload_test.php?imwemr=".session_id()."&formName=".$formName."&form_id=".$form_id."&testId=".$testId."&test_temp_id=".$test_temp_id);?>" width="650" height="500" scrolling="yes" border="0"> </iframe>
    
    <?php }?>
    </div>
                                    
    <div class="clearfix"></div>					
                                    
    <form name="uploadFrm" method="post" >
    <input type="hidden" name="formName" value="<?php echo $formName; ?>">
    <input type="hidden" name="show" value="<?php echo $show; ?>">
    <input type="hidden" name="formId" value="<?php echo $form_id; ?>">
    <input type="hidden" name="elem_delete" value="">
    <input type="hidden" name="testId" value="<?php echo $testId;?>">
    
    <label><b>Comment:&nbsp;</b></label>
    <textarea name="comment" id="comment" rows="3" cols="40"><?php echo $rowQry['multi_doc_upload_comment'];?></textarea>
    </form>
    
    
    <div class="clearfix"></div>
    <input type="button"  class="dff_button" id="Close" value="Save & Close"  onMouseOver="button_over('Close')" onMouseOut="button_over('Close','')" onClick="frm_submit();">
    
    <?php if(($rowQry['crtDate1'] != '00-00-0000 12:00:00') && ($rowQry['crtDate1'] != '')){?>
        <Center>Last Upload Date Time-:&nbsp;<?php echo $rowQry['crtDate1'];?></Center>
    <?php }?>
    <?php
} 
if($show=='scan'){
	if($_POST){
		$comment = $_POST['comments'];
		$site = $_POST['site'];
		if($comment || $site){
			 $chkCmntQry = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans 
							WHERE patient_id='$patient_id' 
							AND image_form='$formName' 
							AND scan_or_upload='scan' 
							AND scan_id ='$scan_id'";
			$chkCmntRes =imw_query($chkCmntQry);
			if(imw_num_rows($chkCmntRes)>0){
				$chkCmntRow = imw_fetch_array($chkCmntRes);
				$chkDocUploadDate = $chkCmntRow['created_date'];
				//$scanOrUploadDate = '$chkDocUploadDate';
				$explDtTm = explode(' ',$chkDocUploadDate);
				list($yr, $mnth, $dy) = explode('-',$explDtTm[0]);
			    list($hr, $min, $scnd) = explode(':',$explDtTm[1]);
			    $chkNewDt = date('y-m-d H:i:s', mktime($hr,$min,$scnd-180,$mnth,$dy,$yr));
			}
			$tmp = "";
			
			if(!empty($form_id)){$tmp = "form_id ='".$form_id."' && ";}
			else{$tmp = "test_id = '".$testId."' && ";}
			
			$qry = "UPDATE ".constant("IMEDIC_SCAN_DB").".scans 
					SET testing_docscan = '$comment',
					site = '".$site."' 
					WHERE patient_id='$patient_id' 
						 AND scan_id ='$scan_id'";
			$res = imw_query($qry);
			echo "<script>window.close();</script>";
		}else{
			echo "<script>window.close();</script>";
		}
		die('');
	}
    $tmp = "";
	
	if(!empty($form_id)){$tmp = "form_id ='".$form_id."' && ";}
	else{$tmp = "test_id = '".$testId."' && ";}
	
	$selQry = "SELECT DATE_FORMAT(created_date,'%m-%d-%Y %h:%i:%s') AS crtDate, testing_docscan from ".constant("IMEDIC_SCAN_DB").".scans 
				WHERE patient_id = '$patient_id' AND image_form='".$formName."' AND scan_or_upload='scan' 
				ORDER BY `created_date` DESC LIMIT 0,1";
    $resQry = imw_query($selQry);
    $rowQry = imw_fetch_array($resQry);
	?>
	<!-- SCAN -->
	<div id="mlr10">
	<script type="text/javascript">
			multiScan='yes';
			no_of_scans=100;
			upload_scan_url  = '<?php echo $GLOBALS['php_server'];?>/interface/tests/uploadScans.php';
			upload_scan_url += '?method=upload&formName=<?php echo $formName?>&form_id=<?php echo $form_id;?>&testId=<?php echo $testId;?>&test_master_id=<?php echo $test_master_id;?>';
		</script>
		
	<?php 
		$browser = browser();
		if(!$_POST){ 
			if($browser['name'] == "msie"){
				include_once("../../library/scan/scan_control.php");
			}else{
				include_once("../../library/scanc/scan_control.php");
			}
		}
	?>                
	</div>
    <form name="compareFrm" method="post" onSubmit="return chkForm();">
	   <input type="hidden" name="formName" value="<?php echo $formName; ?>">
	   <input type="hidden" name="show" value="<?php echo $show; ?>">
	   <input type="hidden" name="formId" value="<?php echo $form_id; ?>">
	   <input type="hidden" name="elem_delete" value="">
	   <input type="hidden" name="testId" value="<?php echo $testId;?>">
       <table class="table table-bordered"><tr>
			<td class="tstrstopt" style="width:25%">
                <b>Select Site:</b><br>
                <label><input type="radio" name="site" value="0"><span class="label_txt">None</span></label>
                <label><input type="radio" name="site" value="3"><span class="label_txt">OU</span></label>
                <label><input type="radio" name="site" value="2"><span class="label_txt">OD</span></label>
                <label><input type="radio" name="site" value="1"><span class="label_txt">OS</span></label>
			</td>
            <td style="width:auto;"><textarea name="comments" id="comments" style="height:50px !important; width:99%;" placeholder="comments..."><?php echo $rowQry['testing_docscan'];?></textarea></td>
        </tr></table>
        <div class="clearfix"></div>
        <div class="col-sm-12 text-center">
	        <button type="button" class="btn btn-success" id="Close" onClick="submit12();">Save &amp; Close</button>
        </div>
        <span class="pull-right text-small">
		 <?php if(($rowQry['crtDate'] != '00-00-0000 12:00:00') && ($rowQry['crtDate'] != '')){?>
              Last scan done on:&nbsp;<?php echo $rowQry['crtDate'];?>&nbsp;
           <?php }?>
        </span>
	</form>	
<?php
	  }
?>
</body>
</html>