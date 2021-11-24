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
	Purpose: Shows scans and uploads
	Access Type: Include 
*/
include '../../../config/globals.php';
$browser = browser();
//Check IP
if(trim($phpServerIP) != trim($_SERVER['HTTP_HOST']))
{
	$GLOBALS['php_server'] = $phpHTTPProtocol.$_SERVER['HTTP_HOST'].$phpServerPort.$web_root;
}

$userauthorized = $_SESSION['authId'];
$pid = $_SESSION['patient'];
$pid = (int) $pid;

if (isset($_POST['ajax_req']) && $_POST['ajax_req'] == "ajax_scan_id")
{
	$scan_id = $_POST['scan_id'];
	$batch_img_chk=0;
	$sel_img = imw_query("select upload_lab_rad_data_id from  upload_lab_rad_data where uplaod_primary_id='".$scan_id."' and upload_status='0'");
	$batch_img_chk=imw_num_rows($sel_img);
	if( $batch_img_chk > 0){
		$batch_img_chk=1;	
	}
	echo $batch_img_chk.'|~~|'.$scan_id;
	exit;	
}

$upload_scan_url_param = "imwemr=".session_id()."&load_patient=".$pid."&method=".$_REQUEST['scanOrUpload']."&primary_id=".$_REQUEST['lab_id'].'&upload_from_module='.$_REQUEST['upload_from'];

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo 'Scan Document :: imwemr ::';?></title>
   	
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.css" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet" />
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
		<script type="text/javascript">
			window.focus();
			
			var web_root = '<?php echo $GLOBALS['php_server'];?>';
			var browser_name = '<?php echo $browser['name'];?>';
			var action = '<?php echo $_REQUEST['scanOrUpload'];?>';
			var url_params = '<?php echo $upload_scan_url_param;?>';
			
			if( action == 'scan' )
			{
				var multiScan='yes';
				var no_of_scans=100;
				var upload_scan_url = web_root + '/interface/billing/scan/upload_lab_data.php?' + url_params;
				
			}
			else if(action == 'upload')
			{
				var upload_url = web_root + '/interface/billing/scan/upload_lab_data.php?' + url_params;
			}
			
			function resize_layout() {
				var fh = $(".panel-footer").outerHeight(true);
				var hh = $(".panel-heading").outerHeight(true);
				var h = window.outerHeight - (fh+hh+75);
				$(".panel-body").css({'max-height':h+'px','height':h+'px'});
			}
			
			function resize_window() 
			{ 
				var parWidth = screen.availWidth * 0.9; //(screen.availWidth > 1200) ? 1200 : screen.availWidth ;
				var parHeight = screen.availHeight * 0.9; //(browser_name == 'msie') ? 810 : 800;
				window.resizeTo(parWidth,parHeight);
				var t = 10;
				var l = parseInt((screen.availWidth - window.outerWidth) / 2)
				window.moveTo(l,t);
				resize_layout();	
			}
			
			function close_window()
			{
				var scan_id = document.getElementById('row_id').value;
				if(scan_id!=''){
					jQuery.ajax({
						url: '<?php echo basename($_SERVER['PHP_SELF']); ?>',
						dataType: 'html',
						type: 'POST',
						data: {ajax_req:'ajax_scan_id', scan_id:scan_id}, 
						beforeSend: function() {},
						success: function(data) {
							var resp = data.split('|~~|');
							var imgCheck = resp[0];
							var row_id = resp[1];
							if(top.frames[0]) {
								if(top.frames[0].frames[0]){
									if(top.frames[0].frames[0].frames[0]){
										if(top.frames[0].frames[0].frames[0].document.getElementById("scn_id_"+row_id)) {
											if(imgCheck == 1){
												top.frames[0].frames[0].frames[0].document.getElementById("scn_id_"+row_id).innerHTML = '<img src="../chart_notes/images/scanDcs_active.png" alt="Scan" border="0">';
											} else {
												top.frames[0].frames[0].frames[0].document.getElementById("scn_id_"+row_id).innerHTML = '<img src="../chart_notes/images/scanDcs_deactive.png" alt="Scan" border="0">';
											}		
										}
									}
								}
							}
							top.removeMessi();
						}
					});
				}
			}	
			
			function do_action(type)
			{
				if(typeof type === 'undefined') type = '';
				else type = type.trim();
				
				var lab_id = '<?php echo $lab_id; ?>';
				var upload_from = '<?php echo $upload_from; ?>';
				var params = 'lab_id='+lab_id+'&upload_from='+upload_from;
				if(type)
				{
					var file_name = 'scan_and_upload_data.php?';
					var scan_upload = 'scanOrUpload='+type;
				}
				else
				{
					var file_name = 'view_batch_images.php?';
					var scan_upload = 'scanOrUpload='+type;
				}
				var url = file_name + (scan_upload ? scan_upload+'&' : '')  + params;
				window.location.href = url;
			}
			
			$(function(){ resize_window();});
			$(window).resize(function(){resize_layout();});
		</script>	
	</head>
	<body >
  	<div class="panel panel-primary">
      <div class="panel-heading"><?php echo ucfirst($_REQUEST['scanOrUpload']);?> Documents</div>
      <!-- Body Content -->
      <div class="panel-body popup-panel-body" style="height:640px;max-height:640px;">
      	
        <div class="row">
					<div class="col-xs-1"></div>
          <div class="col-xs-10">
            <div class="clearfix">&nbsp;</div>
            <div class="row">
              <div class="col-xs-12">
                <?php
              		if($_REQUEST['scanOrUpload'] == "scan")
									{
										if($browser['name'] == 'msie' || $browser['name'] != "chrome" )
										{ 
											include_once $GLOBALS['fileroot']. "/library/scan/scan_control.php";
										}
										else include_once $GLOBALS['fileroot']. "/library/scanc/scan_control.php";
									}
									else
									{
										$upload_from = 'pdfsplitter';
										include $GLOBALS['srcdir'].'/upload/index.php';
									}
								?>
              </div>   	
              
            </div>
            
            <div class="clearfix"></div>
            
                
          </div>
  				<div class="col-xs-1"></div>
  			</div>
        
     	</div>
      
      <!-- Footer Content -->
      <footer class="panel-footer">
      	<button type="button" name="back_btn" class="btn btn-warning" id="back_btn" onClick="do_action();">Back</button>
        <button type="button" class="btn btn-danger" onClick="window.close();">Close</button>
      </footer>
  	</div>  
</body>
</html>