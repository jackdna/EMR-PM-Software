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
$browser = browser();
//Check IP
if(trim($phpServerIP) != trim($_SERVER['HTTP_HOST']))
{
	$GLOBALS['php_server'] = $phpHTTPProtocol.$_SERVER['HTTP_HOST'].$phpServerPort.$web_root;
}
	
$userauthorized = $_SESSION['authId'];
$pid = (int) $_SESSION['patient'];
$scanDocId = $_SESSION['scanDocId'];
$mthd = ($_REQUEST['mthd']) ? $_REQUEST['mthd'] : 'scan';
$scanTypeFolder = ($_REQUEST['scanTypeFolder']) ? $_REQUEST['scanTypeFolder'] : 0;

$upload_url_params = "imwemr=".session_id()."&method=".$mthd."&scanTypeFolder=".$scanTypeFolder;

if($_REQUEST["img_fin_sig"]<>""){			
	$imgpathr_f=$_REQUEST["img_fin_sig"];
	if(trim($imgpathr_f)!=""){
		if(file_exists($imgpathr_f)){
			@unlink($imgpathr_f);
			$msg="Photo Deleted Sucessfully!";
		}
	}
}
			
if( isset($_REQUEST['submit_req']) && $_REQUEST['submit_req'] == '1' ){
	$comment12 = $_REQUEST['comments'];
  if($comment12)
  {
		$chkCmntQry = "select * from surgery_center_patient_scan_docs where patient_id='$pid' && scan_type_folder='$scanTypeFolder' && id = '$scanDocId'";
		$chkCmntRes =imw_query($chkCmntQry);
		if(imw_num_rows($chkCmntRes)>0){
			$chkCmntRow = imw_fetch_array($chkCmntRes);
			$chkDocUploadDate = $chkCmntRow['surgery_patient_scan_date'];

           $explDtTm = explode(' ',$chkDocUploadDate);
           list($yr, $mnth, $dy) = explode('-',$explDtTm[0]);
           list($hr, $min, $scnd) = explode(':',$explDtTm[1]);
           $chkNewDt = date('y-m-d H:i:s', mktime($hr,$min,$scnd-30,$mnth,$dy,$yr));
   	}
		$qry = "update surgery_center_patient_scan_docs set surgery_patient_scan = '$comment12' where patient_id='$pid' && scan_type_folder='$scanTypeFolder' && surgery_patient_scan_date >= '$chkNewDt'";
		$res = imw_query($qry);
	}
}

$scanFolderTypeArr = array('Patient Info','Clinical','Health Questionnaire','H&P','EKG','Ocular Hx');

$selQry = "select DATE_FORMAT(surgery_patient_scan_date,'".get_sql_date_format()." %h:%i:%s') AS crtDate,surgery_patient_scan from surgery_center_patient_scan_docs  where patient_id = '$pid' and scan_type_folder='".$scanTypeFolder."' order by `surgery_patient_scan_date` desc limit 0,1";
$resQry = imw_query($selQry);
$rowQry = imw_fetch_array($resQry);
?>
<html>
<head>
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.css" type="text/css" rel="stylesheet" />
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet" />
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script type="text/javascript">
	// Scan/upload Options 
	var method = '<?php echo $mthd;?>';
	var web_root = '<?php echo $GLOBALS['php_server'];?>';
	var browser_name = '<?php echo $browser['name'];?>';
	var url_params = '<?php echo $upload_url_params;?>';
	
	if( method == 'upload')
	{
		var upload_url = web_root + "/interface/patient_info/surgery_consent_forms/upload_surgery_consent.php?" + url_params;
	}
	else
	{
		var multiScan = 'yes';
		var no_of_scans = 100;
		var upload_scan_url = web_root + "/interface/patient_info/surgery_consent_forms/upload_surgery_consent.php?" + url_params;
	}
	
	function close_window()
	{
		<?php if($mthd != "upload"){?>
			//if(browser_name == "msie")
				upload();
		<?php }?>
		setTimeout(close_window_part,1000);
	}
	function close_window_part(){
		document.frm1.submit_req.value=1;
		document.frm1.submit();
		top.fmain.left_panel();
		//var f = top.fmain.document.getElementById('consent_data_id_surgery');
		//f.contentWindow.location.reload(false);
	}
	function gotoUploadPgFun(vl) {
		if(vl=='yes') {
			document.frm1.mthd.value='upload';
		}else if(vl='no') {
			document.frm1.mthd.value='';
		}
		document.frm1.submit();
	}
</script>
</head>
<body class="bg-white">

	<div class="container-fluid ">
  	<div class="row">
  		<div class="col-xs-12 head sub_head">
      		Scan/Upload Document For <?php echo $scanFolderTypeArr[$scanTypeFolder]; ?>
      </div>
   	</div>
    
    <div class="row">
    	<div class="col-xs-12">
      	<div class="row">
        	<div class="col-md-2 visible-md visible-lg">&nbsp;</div>
          <div class="col-xs-12 col-md-8 " style="min-height:350px;">
          <?php 
            if($mthd!='upload')
            {
              if($browser['name'] == "msie" )
              {
                include_once $GLOBALS['srcdir']."/scan/scan_control.php";
              }
							else include_once $GLOBALS['srcdir']."/scanc/scan_control.php";
			  //else {} //COMPATIBILITY
            }
			else
			{
				 include '../../../library/upload/index.php';	
			}
          ?>
          </div>
        	<div class="col-md-2 visible-md visible-lg">&nbsp;</div>
       	</div>   
    	</div>
   	</div>
    
    <form name="frm1" action="<?php echo basename($_SERVER['PHP_SELF']);?>" method="get">
    	<input type="hidden" name="mthd" value="<?php echo xss_rem($mthd);?>">
      <input type="hidden" name="scanTypeFolder" value="<?php echo xss_rem($scanTypeFolder);?>">
      <input type="hidden" name="submit_req" id="submit_req" value="0" /> 
      <div class="row">
    		<div class="col-xs-12">
        	<div class="row">
          	<div class="col-md-2 visible-md visible-lg">&nbsp;</div>
            <div class="col-xs-12 col-md-8">
            	<div class="col-xs-12">
              <label><b>Comment:&nbsp;</b></label>
              <textarea name="comments" rows="2" cols="50" class="form-control"><?php echo $rowQry['surgery_patient_scan'];?></textarea>
              </div>
            </div>
            <div class="col-md-2 visible-md visible-lg">&nbsp;</div>
            
        	</div>
      	</div>
    	</div>
      
      <div class="row">
    		<div class="col-xs-12 text-center mt5">
        	<input class="btn btn-success" id="butId3" type="button" name="save" value="Save" onClick="close_window();">
       		<?php	if($mthd!='upload') { ?>
          <input class="btn btn-success" id="butId4" type="button" name="uploadImage" value="Upload Image" onClick="gotoUploadPgFun('yes');">
         	<?php	}else { ?>
          <input class="btn btn-success" id="butId5" type="button" name="back" value="Back" onClick="gotoUploadPgFun('no');">
          <?php	} ?>
          
      	</div>
     	</div>
      	
      <?php if(($rowQry['crtDate'] != '00-00-0000 12:00:00')&& ($rowQry['crtDate'] != '')){?>
      <div class="row">
    		<div class="col-xs-12 text-center">
        	Last Scan Date Time-:&nbsp;<?php echo $rowQry['crtDate']; ?>
       	</div>
     	</div>   
			<?php }?>
      
 		</form>	
        
 	</div>
	<script>
		top.btn_show();
 	</script>
</body>
</html>