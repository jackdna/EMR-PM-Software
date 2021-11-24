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
 
 File: index.php
 Purpose: Shows medical history
 Access Type: Indirect Access.
*/

require_once("../../config/globals.php");
require_once("../../library/patient_must_loaded.php");
require_once($GLOBALS['srcdir']."/classes/medical_hx/medical_history.class.php");
$medical = new MedicalHistory($_REQUEST['showpage']);
$library_path = $GLOBALS['webroot'].'/library';
$callFrom = ($_REQUEST['callFrom']) ? $_REQUEST['callFrom'] : '';
if($callFrom)
	$container_class = 'col-lg-12 col-md-12 col-sm-12 mdlrht sdpanel';
else
	$container_class = 'col-lg-10 col-md-9 col-sm-9 mdlrht sdpanel';
?>
<!DOCTYPE html>
<html>
	<head>
  	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Medical History:: imwemr ::</title>
    <!-- Bootstrap -->
    <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
    <!-- Bootstrap Selctpicker CSS -->
    <link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/css/medicalhx.css" rel="stylesheet" type="text/css">
    <!--<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">-->
    <!-- Messi Plugin for fancy alerts CSS -->
		<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
    <!-- DateTime Picker CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery-ui.min.1.12.1.css"/>
    <style>
	#Medical_Hx_ROS + .tooltip > .tooltip-inner {  text-align:left; }
    </style>
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]--> 
  	
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
    <script src="<?php echo $library_path; ?>/js/jquery-ui.min.1.12.1.js" type="text/javascript" ></script>
    <!-- jQuery's Date Time Picker -->
    <script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
    <!-- Bootstrap -->
    <script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
    
    <!-- Bootstrap Selectpicker -->
    <script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
    <!-- Bootstrap typeHead -->
    <script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
    <?php if($callFrom === 'WV' ){ ?>
	<script>var global_date_format = "<?php echo phpDateFormat(); ?>";</script>
    <script src="<?php echo $library_path; ?>/js/core_main.js" type="text/javascript"></script>
    <link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet">
		<script src="<?php echo $library_path; ?>/messi/messi.js"></script>

    <script>
		var JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot'];?>';
	</script>
    <?php } ?>
		<script>top.show_loading_image('show');</script>
    <script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
	<script src="<?php echo $library_path; ?>/js/medical_hx.js" type="text/javascript"></script>
  
  </head>
  
	<body>
  	<input type="hidden" name="preObjBack" id="preObjBack" value="a">
    <input type="hidden" name="patYearsOrlder" id="patYearsOrlder" value="<?php echo $medical->patient_older ?>"/>
    <div class="medicalhx">
			<div class="row mainwhtbox margin_0">
      	<div id="leftPanell" class="col-lg-2 col-md-3 col-sm-3 lftpanel <?php echo ($callFrom) ? 'hidden': '';?>" style="max-height:99%; overflow:hidden; overflow-y:scroll;">
						<?php
							include_once 'medical_summary.php';
						?>
				</div>
        <div class="rgtpanel <?php echo $container_class; ?>">
        		<?php
							$folder = $_REQUEST['showpage'];
							if($folder == '') $folder = 'ocular';
							elseif($folder == 'medication') $folder = 'medications';
							$path = $GLOBALS['fileroot']. '/interface/Medical_history/'.$folder.'/index.php';
							include_once $path;
						?>
   			</div>
    	</div>
   	</div>
    <div id="div_loading_image" class="text-center" style="z-index:9999;">
		<div class="loading_container">
			<div class="process_loader"></div>
			<div id="div_loading_text" class="text-info"></div>
		</div>
	</div>
    <script>
			var vocabulary = <?php echo json_encode($medication->vocabulary); ?>;
			var curr_tab_title = '<?php echo $medical->get_tab_title($folder); ?>';
			top.$('#acc_page_name').html(curr_tab_title);
			var saveReq = '<?php echo $_REQUEST['save_action'] ?>';
			        
	        // Enable service connected master popup for problem list and medication.
		    var isDssEnable='<?php echo isDssEnable(); ?>';
			var curTab = '<?php echo $_REQUEST["showpage"]; ?>';
		    if(isDssEnable == 1 && (curTab == 'problem_list' || curTab == 'medication')) {
		        top.$('#dssServiceConnectedButton').css('display','inline-block');
		    } else {
		    	top.$('#dssServiceConnectedButton').css('display','none');
		    }

			//Setting table height
			function set_container_height(){
				var header_position = $('#first_toolbar',top.document).position();
				var window_height = parseInt(window.innerHeight - $('footer',top.document).outerHeight());
				
				//If pagination is there on the page i.e --> .pgn_prnt class
				if($('.pgn_prnt:first').length){
					window_height = window_height - $('.pgn_prnt').outerHeight();
					$('.pgn_prnt').css('overflow-x','hidden');
				}else{
					window_height = parseInt(window_height + 45);
				}
				
				$('.sdpanel:first, .lftpanel:first').css({
					'height':window_height,
					'max-height':window_height,
					'overflow-x':'hidden',
					'overflowY':'auto'
				});
			} 
			
			$(document).ready(function(){
				$("body").css({'max-height':'100%','overflow':'hidden'});	
				set_container_height();
				$("#medical_tab_change",top.document).val('');
				$("#hid_chk_change_data_main,#hidChkConfirmSave",top.document).val('no');
				
				//If a save request is made than reload left panel for updated data
				if(saveReq && typeof(saveReq) !== 'undefined'){
					$('#leftPanell').load('medical_summary.php?ajaxReq=1&showpage="<?php echo $_REQUEST['showpage']; ?>"',function(){
						$('.selectpicker').selectpicker();
					});
				}
				top.show_loading_image('hide');
				
				<?php 
					if($_REQUEST['callFrom'] != 'WV'){ ?>
						top.fmain.show_iportal_changes_alert_data('medical',<?php echo $medical->patient_id ?>);
				<?php	}	?>
				
				function iportal_load_pghd_medhx_reqs(){
					var n="pghd_reqs";
					window.open("get_pghd_req_med_hx.php",n,'location=1,status=1,resizable=1,left=10,top=1,scrollbars=1,width=1000,height=500');
				}
				
				<?php if(isERPPortalEnabled() && (!isset($_SESSION['POSTPONEPGHD']) || (isset($_SESSION['POSTPONEPGHD']) && $_SESSION['POSTPONEPGHD'] !=$_SESSION['patient']) ) ){ ?>
					iportal_load_pghd_medhx_reqs();
				<?php } ?>
				
			});
			
			$(window).resize(function(){
				set_container_height();
			});
			
		</script>
	</body>
</html>