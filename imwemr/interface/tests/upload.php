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

$browser = browser();

if($_POST){
	$comment1 = $_POST['comments'];
	if($comment1) {
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
	 die('');
}




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

$upload_scan_url_param = "imwemr=".session_id()."&formName=".$formName."&form_id=".$form_id."&testId=".$testId."&test_temp_id=".$test_temp_id.'&test_master_id='.$test_master_id;
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
<script type="text/javascript">
var imgPath 		= "<?php echo $GLOBALS['webroot'];?>";
var elem_per_vo 	= "<?php echo $elem_per_vo;?>";
var zPath			= "<?php echo $GLOBALS['rootdir'];?>";
			
var web_root = '<?php echo $GLOBALS['php_server'];?>';
var browser_name = '<?php echo $browser['name'];?>';
var url_params = '<?php echo $upload_scan_url_param;?>';
			
var upload_url = web_root + '/interface/tests/uploadImages.php?' + url_params;

function resize_window() 
{ 
	var parWidth = (screen.availWidth > 900) ? 900 : screen.availWidth ;
	var parHeight = 620;//(browser_name == 'msie') ? 640 : 670;
	window.resizeTo(parWidth,parHeight);
	var t = 10;
	var l = parseInt((screen.availWidth - window.outerWidth) / 2)
	window.moveTo(l,t);
}

function close_window(){
	window.close();
}

resize_window();
		</script>
	</head>
	<body >
        <div class="panel panel-primary">
        	<div class="panel-heading"><?php echo ucfirst($_REQUEST['scanOrUpload']);?> Documents</div>
        	<!-- Body Content -->
        	<div class="panel-body" style="height:400px !important;">
                <div class="row">
                    <div class="col-xs-1"></div>
                    <div class="col-xs-10">
                        <div class="clearfix">&nbsp;</div>
                        <div class="row">
                            <div class="col-xs-12"><?php $upload_url = 'upload_test.php'; include $GLOBALS['srcdir'].'/upload/index.php'; ?></div>   	
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-xs-1"></div>
                </div>
	     	</div>
      	<!-- Footer Content -->
      <div class="panel-footer" style="height:110px;">
      	<form name="compareFrm" method="post" onSubmit="return chkForm();">
           <input type="hidden" name="formName" value="<?php echo $formName; ?>">
           <input type="hidden" name="show" value="<?php echo $show; ?>">
           <input type="hidden" name="formId" value="<?php echo $form_id; ?>">
           <input type="hidden" name="elem_delete" value="">
           <input type="hidden" name="testId" value="<?php echo $testId;?>">
           <textarea name="comments" id="comments" style="height:50px !important; width:99%;" placeholder="comments..."><?php echo $rowQry['testing_docscan'];?></textarea>
            <div class="clearfix"></div>
            <div class="text-center">
                <button type="submit" class="btn btn-success" id="Close" onClick="submit12();">Save &amp; Close</button>
            </div>
            <span class="pull-right text-small">
             <?php if(($rowQry['crtDate'] != '00-00-0000 12:00:00') && ($rowQry['crtDate'] != '')){?>
                  Last scan done on:&nbsp;<?php echo $rowQry['crtDate'];?>&nbsp;
               <?php }?>
            </span>
		</form>
      </div>
  	</div>       
  	
</body>
</html>