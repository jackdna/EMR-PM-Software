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
include_once("../../../config/globals.php");
if($phpServerIP != $_SERVER['HTTP_HOST']){
	$phpServerIP = $_SERVER['HTTP_HOST'];
	$GLOBALS['php_server'] = $phpHTTPProtocol.$phpServerIP.$phpServerPort.$web_root;
}
$title = ($_REQUEST['scanOrUpload'] == "scan") ? 'Scan' : 'Upload';
$upload_scan_url_param = 'imwemr='.session_id().'&method='.$_REQUEST['scanOrUpload'].'&id='.$_REQUEST['id'];
?>
<html>
	<head>
		<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo $title.' Documents for Eligibility :: imwemr ::';?></title>
   	
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
			var scan_container_height = 370;
			
			
			if( action == 'scan' ) {
				// Scan Options 
				var multiScan = 'yes';
				var no_of_scans = 1;
				var upload_scan_url = web_root + '/interface/patient_info/eligibility/uploadEligibilityScan.php?' + url_params;
			}
			else {
				var upload_url = web_root + '/interface/patient_info/eligibility/uploadEligibilityScan.php?' + url_params;	
			}
			
			function resize_window() 
			{ 
				var parWidth = (screen.availWidth > 900) ? 900 : screen.availWidth ;
				var parHeight = 750;//(browser_name == 'msie') ? 720 : 670;
				window.resizeTo(parWidth,parHeight);
				var t = 10;
				var l = parseInt((screen.availWidth - window.outerWidth) / 2)
				window.moveTo(l,t);
			}
			resize_window();
			function close_window(){
				window.opener.top.fmain.location.reload(true);
				window.close();
			}
		</script>
		
</head>
<body>
	<div class="panel panel-primary">
		<div class="panel-heading"><?php echo $title;?> Documents for Eligibility</div>
		<div class="panel-body popup-panel-body" <?php if($browser['name'] == 'msie' ) echo 'style="max-height:590px; height:590px;"'; ?>>
			<div class="col-sm-12">
			<?php	
				if( $_REQUEST['scanOrUpload'] == 'scan' ) {
					if($browser['name'] == 'msie' ) 
						include_once $GLOBALS['srcdir'].'/scan/scan_control.php';
					else 
						include_once $GLOBALS['srcdir'].'/scanc/scan_control.php';
				}
				elseif( $_REQUEST['scanOrUpload'] == 'upload' )
					include_once $GLOBALS['srcdir'].'/upload/index.php';
			?>
			</div>
		</div>
		<footer class="panel-footer">
			<button type="button" class="btn btn-danger" onClick="close_window();">Close</button>
		</footer>
	</div>
</body>
</html>
