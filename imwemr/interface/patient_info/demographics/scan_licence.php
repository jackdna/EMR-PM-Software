<?php 
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

	$type = $_REQUEST['type'] ?? '';
	$tblName = $type == 'rp' ? 'resp_party' : 'patient_data';
	$licImgFld = $type == 'rp' ? 'licence_image' : 'licence_photo';
	$idFld = $type == 'rp' ? 'patient_id' : 'id';
	//$prev_tab_active = ($_POST["frm_del_prev_license"] == 1)  ? 'true' : 'false'; 
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo 'Scan License :: imwemr ::';?></title>
   	
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.css" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet" />
    <?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
        <link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
    <?php } ?>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
    <script type="text/javascript">
			window.focus();
			
			// Scan Options 
			var multiScan = 'no';
			var no_of_scans = 1;
			var web_root = '<?php echo $GLOBALS['php_server'];?>';
			var type = '<?php echo $type; ?>';
			var upload_scan_url = web_root + '/interface/patient_info/demographics/upload_scan_licence.php?method=upload' + (type?'&type='+type:'');
			var browser_name = '<?php echo $browser['name'];?>';	
			var scan_container_height = 300;
			
			$(document).ready(function(){
				var parWidth = (screen.availWidth > 900) ? 900 : screen.availWidth ;
				window.resizeTo(parWidth,745);
				var t = 10;
				var l = parseInt((screen.availWidth - window.outerWidth) / 2)
				window.moveTo(l,t);
			});
				
			function close_window(imageName,type)
			{	
				$('#hidChkChangeDemoTabDb',window.opener.top).val("yes");
				if( typeof type === 'undefined' ) type = '';
				if(imageName){
					var div_id =  type  ? 'respLic' : 'ptLic';
					if(window.opener.top.fmain && typeof(window.opener.top.fmain.image_DIV) != 'undefined'){
						window.opener.top.fmain.image_DIV(imageName,div_id);	
					}else if(typeof(window.opener.top.image_DIV) != 'undefined'){
						window.opener.image_DIV(imageName,div_id);	
					}
				}
				window.close();
			}
			
			function upload_licence(frm)
			{
				upload(frm);
				//if(browser_name == "msie")	 upload(frm);
				//else if(browser_name == "chrome") frm.submit();
			}
	
			$(function(){
				var prev_tab_active = '<?php echo $prev_tab_active;?>';
				var c_grid = (prev_tab_active == 'true') ? 'prev_license' : 'scan_license';
				var c_tab = (prev_tab_active == 'true') ? 'prev_tab' : 'scan_tab';
				
				$('body').on('click','#scan_tab',function(){
					$('#butId3').fadeIn(500);
				});
				$('body').on('click','#prev_tab',function(){
					$('#butId3').fadeOut(500);
				});
				
				$("#"+c_grid).addClass('in active');
				$("#"+c_tab).addClass('active').trigger('click');
				
				
			});
		
		</script>
	</head>
  <body>
  	<div class="panel panel-primary">
      <div class="panel-heading">Patient Scan License <?php echo $type ? '(Resp. Party)' : '';?></div>
      <div class="panel-body popup-panel-body" style="max-height:580px; height:580px;">
      	<input type="hidden" name="curr_tab" value="new_scan">
       	
        <!-- Tabs -->
        <ul class="nav nav-tabs">
          <li id="scan_tab" onClick=""><a data-toggle="tab" href="#scan_license">Scan New License</a></li>
          <li id="prev_tab" onClick="$('#butId3').fadeOut(500);"><a data-toggle="tab" href="#prev_license">Previous License</a></li>
       	</ul>
        
        <!-- Contents -->
        <div class="tab-content">
        	
          <div id="scan_license" class="tab-pane fade">
          		<?php include_once 'new_scan_licence.php'; ?>
          </div>
          
          <div id="prev_license" class="tab-pane fade ">
          		<?php include_once 'prev_scan_licence.php'; ?>
          </div>
      	
        </div>
        
         	
     	</div>
  		<footer class="panel-footer">
      	<input class="btn btn-success" id="butId3" type="button" name="close" value="Save & Close"  onClick="upload_licence(document.frm_new_scan_licence)" />
      	<button type="button" class="btn btn-danger" onClick="window.close();">Close</button>
      </footer>
    </div>
    
	</body>
</html>