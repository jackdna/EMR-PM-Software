<?php 
	include '../../../../config/globals.php';
	include($GLOBALS['srcdir'].'/classes/SaveFile.php');
	
	$browser = browser();
	//Check IP
	if(trim($phpServerIP) != trim($_SERVER['HTTP_HOST']))
	{
		$GLOBALS['php_server'] = $phpHTTPProtocol.$_SERVER['HTTP_HOST'].$phpServerPort.$web_root;
	}
	$userauthorized = $_SESSION['authId'];
	$pid = $_SESSION['patient'];
	$pid = (int) $pid;
	
	$active_tab = 'scan';
	$active_tab = ($_POST['frm_new_upload'] == '1' ) ? 'upload' : $active_tab;
	$active_tab = ($_POST['frm_del_prev_insurance'] == '1' ) ? 'prev' : $active_tab;
	 
	$type = ($_GET['type']) ? $_GET['type'] : $_SESSION['patient_ins_type'];
	$isRecordExists = $_GET['isRecordExists'];
	$ins_caseid = ($cur_case_id) ? $cur_case_id : $_SESSION['currentCaseid'];
	
	$upload_scan_url_param = "imwemr=".session_id()."&method=upload&type=".$type."&isRecordExists=".$isRecordExists;
	
	if( !$isRecordExists)
	{
		if(strtolower($type)=="primary"){
			$isRecordExists=$_SESSION['patient_ins_id_pri'];
			unset($_SESSION['patient_ins_id_sec']);
		}else if(strtolower($type)=="secondary"){
			$isRecordExists=$_SESSION['patient_ins_id_sec'];
			unset($_SESSION['patient_ins_id_pri']);
		}
	}
	
	if($isRecordExists)
	{
		$qry = "select scan_card,scan_card2 from insurance_data where id = '".$isRecordExists."'";	
	}
	else
	{
		$qry = "select scan_card,scan_card2 from insurance_scan_documents 
							where type = '".$type."' and ins_caseid = '".$ins_caseid."'
							and patient_id = '".$pid."' and document_status = '0'";
	}
	$qryId = imw_query($qry);
	$row = imw_fetch_assoc($qryId);
	extract($row);
	
	// Start Checking Scan Card files physical status:
	$tmpFilePath = data_path().$scan_card; 
	if( $scan_card <> '' && !file_exists($tmpFilePath) ) 
		$scan_card = '';

	$tmpFilePath2 = data_path().$scan_card2; 
	if( $scan_card2 <> '' && !file_exists($tmpFilePath2) ) 
		$scan_card2 = '';
	// End Checking Scan Card files physician status.

	// Setting Limit to upload files 
	if($scan_card == '' && $scan_card2 == "") { $uplLimit = 2;} 
	elseif($scan_card != '' && $scan_card2 != "") { $uplLimit = 0;}
	else{ $uplLimit = 1; }
	
	if($isRecordExists)
	{
		$selQry = "select DATE_FORMAT(cardscan_date,'%m-%d-%Y %h:%i:%s') AS crtDate,cardscan_comments 
								from insurance_data where pid = '".$pid."' and type = '".$type."' and ins_caseid = '".$ins_caseid."'";
	}
	else
	{
		$selQry = "select DATE_FORMAT(cardscan_date,'%m-%d-%Y %h:%i:%s') AS crtDate,cardscan_comments 
								from insurance_scan_documents where patient_id = '".$pid."' and type = '".$type."' 
								and ins_caseid = '".$ins_caseid."' ";
	}

	$resQry = imw_query($selQry);
	$rowQry = imw_fetch_array($resQry);
	
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo 'Scan License :: imwemr ::';?></title>
   	
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.css" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet" />
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
    <script type="text/javascript">
			window.focus();
			var active_tab = '<?php echo $active_tab;?>';
			var c_grid = active_tab + '_insurance';
			var c_tab = active_tab + '_tab';
			var uplLimit = parseInt('<?php echo $uplLimit;?>');
			 
			// Scan Options 
			var multiScan = 'yes';
			var duplexScan = 'yes';
			var no_of_scans = 2;
			var autoScan='no';
			var web_root = '<?php echo $GLOBALS['php_server'];?>';
			var url_params = '<?php echo $upload_scan_url_param;?>';
			var upload_scan_url = web_root + '/interface/patient_info/insurance/scan/upload_scan_insurance.php?' + url_params;
			var browser_name = '<?php echo $browser['name'];?>';
			var scan_container_height = 300;	
			function resize_window() 
			{ 
				var parWidth = (screen.availWidth > 900) ? 900 : screen.availWidth ;
				var parHeight = 750;//(browser_name == 'msie') ? 720 : 670;
				window.resizeTo(parWidth,parHeight);
				var t = 10;
				var l = parseInt((screen.availWidth - window.outerWidth) / 2)
				window.moveTo(l,t);
			}
			
			function close_window(imageName)
			{	
				$('#hidChkChangeDemoTabDb',window.opener.top).val("yes");
				if(document.getElementById("hidCallFrom").value == ""){
					window.opener.top.fmain.askSepAccount();
				}
				else if(document.getElementById("hidCallFrom").value == "scheduler"){
					if(typeof(window.opener.get_action)!="undefined") {
						window.opener.get_action('submit_form');
					}else if(typeof(window.opener.top.callChildWinCheckIn)!="undefined") {
						window.opener.top.callChildWinCheckIn();	
					}
				}else if(typeof(window.opener.get_action)!="undefined") {
					window.opener.get_action('submit_form');
				}
				window.close();
			}
			
			function upload_scan(_this)
			{
				var source = $(_this).data('source');
				var frm = '';
				if(source === 'upload') frm = document.frm_upload;
				if(source === 'scan') frm = document.frm_scn;
				
				if(frm)
				{
					if( source === 'scan' )
						upload(frm);
					else if( source === 'upload' ) {
						$("#div_loading_image").removeClass('hide');
						window.opener.top.show_loading_image("show");
						frm.submit();
					}
				}
			}
			
			function show_prev_tab()
			{
				$("#prev_tab").trigger('click');
				$("#prev_tab a").trigger('click');
			}
			
			function refresh_limits(add)
			{
				if(typeof add === 'undefined') add = 0;
				else add = parseInt(add);
				uplLimit = uplLimit + add;
				
				if(uplLimit == 0)
				{
					$("#scan_warning,#up_warning").removeClass('hidden');
					$("#scan_control,#up_control").addClass('hidden');
				}
				else
				{
					$("#scan_warning,#up_warning").addClass('hidden');
					$("#scan_control,#up_control").removeClass('hidden');
				}
					
			}
			
			function printCard(obj){
				var fileLocation = $(obj).data('location');
				if(fileLocation.trim()){
					top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
					html_to_pdf(fileLocation,'p','InsuranceCard','','false');
				}
			}
			
			$(function(){
				
				resize_window();
				
				$('body').on('click','#scan_tab',function(){
					$('#butId3').data('source','scan').fadeIn(500);
				});
				
				$('body').on('click','#upload_tab',function(){
					$('#butId3').data('source','upload').fadeIn(500);
				});
				$('body').on('click','#prev_tab',function(){
					$('#butId3').fadeOut(500);
				});
				
				$("#"+c_grid).addClass('in active');
				$("#"+c_tab).addClass('active');
				
				
			});
		</script>
		
</head>
	<body>
		<div id="div_loading_image" class="text-center hide">
			<div class="loading_container">
				<div class="loader"></div>
					<div id="div_loading_text" class="text-info"></div>
			</div>
		</div>
   
    <form name="frmtype" method="post" action="">
			<input type="hidden" value="<?php echo $_GET['type'];?>" name="hide_type" id="hide_type">
			<input type="hidden" value="<?php echo $_GET['isRecordExists'];?>" name="isRecordExists" id="isRecordExists">
   		<input type="hidden" id="hidCallFrom" name="hidCallFrom" value="<?php echo $_REQUEST['call_from'];?>">
		</form>


  	<div class="panel panel-primary">
      <div class="panel-heading">Patient Scan License</div>
      <div class="panel-body popup-panel-body" <?php if($browser['name'] == 'msie' ) echo 'style="max-height:590px; height:590px;"'; ?>>
      	<input type="hidden" name="curr_tab" value="new_scan">
       	
        <!-- Tabs -->
        <ul class="nav nav-tabs">
          <li id="scan_tab" ><a data-toggle="tab" href="#scan_insurance">Scan Insurance</a></li>
          <li id="upload_tab" ><a data-toggle="tab" href="#upload_insurance">Upload Insurance</a></li>
          <li id="prev_tab" ><a data-toggle="tab" href="#prev_insurance">Previous Scans</a></li>
       	</ul>
        
        <!-- Contents -->
        <div class="tab-content">
        	
          <div id="scan_insurance" class="tab-pane fade">
          		<?php include_once 'new_scan_insurance.php'; ?>
          </div>
          
          <div id="upload_insurance" class="tab-pane fade">
          		<?php include_once 'new_upload_insurance.php'; ?>
          </div>
          
          <div id="prev_insurance" class="tab-pane fade ">
          		<?php include_once 'prev_scan_insurance.php'; ?>
          </div>
      	
        </div>
        
         	
     	</div>
      
  		<footer class="panel-footer">
      	<input class="btn btn-success" id="butId3" type="button" name="close" value="Save & Close"  onClick="upload_scan(this)" data-source="scan" />
      	<button type="button" class="btn btn-danger" onClick="window.close();">Close</button>
      </footer>
      
    </div>
        <!--<script>
            //window.addEventListener('load', window.opener.top.fmain.askSepAccount);
        </script>-->
</body>
</html>