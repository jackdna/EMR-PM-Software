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
include '../../../../config/globals.php';
$browser = browser();
//Check IP
if(trim($phpServerIP) != trim($_SERVER['HTTP_HOST']))
{
	$GLOBALS['php_server'] = $phpHTTPProtocol.$_SERVER['HTTP_HOST'].$phpServerPort.$web_root;
}

$userauthorized = $_SESSION['authId'];
$pid = $_SESSION['patient'];
$pid = (int) $pid;

$upload_scan_url_param = "imwemr=".session_id()."&load_patient=".$pid."&method=".$_REQUEST['scanOrUpload']."&scan_id=".$_REQUEST['scan_id'].'&ins_data_id='.$_REQUEST['ins_data_id']."&scan_for=".$_REQUEST['scan_for'];

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
			var scan_container_height = 350;
			
			if( action == 'scan' )
			{
				var multiScan='no';
				var no_of_scans=1;
				var upload_scan_url = web_root + '/interface/patient_info/insurance/scan/upload_scan_data.php?' + url_params;
				
			}
			else if(action == 'upload')
			{
				var upload_url = web_root + '/interface/patient_info/insurance/scan/upload_scan_data.php?' + url_params;
			}
			
			function resize_window() 
			{ 
				var parWidth = (screen.availWidth > 900) ? 900 : screen.availWidth ;
				var parHeight = 670;//(browser_name == 'msie') ? 640 : 670;
				window.resizeTo(parWidth,parHeight);
				var t = 10;
				var l = parseInt((screen.availWidth - window.outerWidth) / 2)
				window.moveTo(l,t);
			}
			
			function close_window(){
				window.close();
			}
			
			function do_action(type)
			{
				if(typeof type === 'undefined') type = '';
				else type = type.trim();
				
				var scan_id = '<?php echo $scan_id; ?>';
				var scan_for = '<?php echo $scan_for; ?>';
				var ins_data_id = '<?php echo $_REQUEST['ins_data_id']; ?>';
				var params = 'scan_id='+scan_id+'&scan_for='+scan_for+"&ins_data_id="+ins_data_id;
				if(type)
				{
					var file_name = 'scan_and_upload_data.php?';
					var scan_upload = 'scanOrUpload='+type;
				}
				else
				{
					var file_name = 'scan_refferal_case.php?';
					var scan_upload = 'scanOrUpload='+type;
				}
				var url = file_name + (scan_upload ? scan_upload+'&' : '')  + params;
				window.location.href = url;
			}
			
			resize_window();
		</script>
	</head>
	<body >
  	<div class="panel panel-primary">
      <div class="panel-heading"><?php echo ucfirst($_REQUEST['scanOrUpload']);?> Documents</div>
      <!-- Body Content -->
      <div class="panel-body popup-panel-body">
      	
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