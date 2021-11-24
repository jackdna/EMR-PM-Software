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
 
 Purpose: Documents Index
 Access Type: Direct Access.
*/
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/../../library/classes/class.app_base.php');
if(!defined('DOC_POPUP')) define('DOC_POPUP',true);
$app_base			= new app_base();
$DEFAULT_DOC_TAB = 'signed_consent';
$DEFAULT_DOC_URL = $GLOBALS['webroot'].'/interface/patient_info/consent_forms/index.php?doc_name=signed_consent&doc_collapse=yes';
$tab_name = $_REQUEST['tab_name'];
if($tab_name=='DocTab') {
	$DEFAULT_DOC_URL = $GLOBALS['webroot'].'/interface/chart_notes/scan_docs/scan_docs.php?doc_name=scan_docs&doc_collapse=yes';	
}
$patientInfoArr = $app_base->show_patient_info();
$patient_name = core_get_patient_name($app_base->session['patient']);
$patientInfo = $patient_name[4]."&nbsp;&nbsp;&nbsp;&nbsp; (DOB - ".date(phpDateFormat(), strtotime($patientInfoArr['DOB'])).", Age - ".show_age($patientInfoArr['DOB']).")";
/*if( !$app_base->session['patient'] ) {
	$DEFAULT_DOC_TAB = 'multi_docs';
	//&fromDocTab=fromDocTab
	$DEFAULT_DOC_URL = $GLOBALS['webroot'].'/interface/chart_notes/multi_upload.php?doc_name=multi_upload';
}*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>imwemr</title>
    <link rel="icon" type="image/png" href="<?php echo $GLOBALS['webroot']; ?>/favicon-16x16.png" sizes="16x16">
		<link rel="icon" type="image/png" href="<?php echo $GLOBALS['webroot']; ?>/favicon-32x32.png" sizes="32x32"> 
    <!-- Bootstrap -->
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.css" rel="stylesheet">
    <!--<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-dropdownhover.min.css" rel="stylesheet">-->
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/core.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet"> 
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.css" rel="stylesheet">
    
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" rel="stylesheet">
    <?php if($app_base->default_product == "imwemr") { ?>
        <link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
    <?php } ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/html5shiv.min.js"></script>
    <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
			.ftvertxt, #tick2 { font-family:'robotobold'; font-weight:600;font-size:14px; color:#5c2a79;}	
			/* clears the 'X' from Internet Explorer */
			input[type=search]::-ms-clear,
			input[type=text]::-ms-clear {  display: none; width : 0; height: 0; }
			input[type=search]::-ms-reveal,
			input[type=text]::-ms-reveal {  display: none; width : 0; height: 0; }
            .ui-datepicker{z-index:9999!important;}
		</style>
    <script type="text/javascript">
			var logged_user_type 	= "<?php echo $app_base->logged_user_type;?>";
			var JS_WEB_ROOT_PATH 	= "<?php echo $GLOBALS['webroot']; ?>";
			var HASH_METHOD				= "<?php echo $app_base->hash_method;?>";
			var priv_vo_clinical 	= "<?php echo $app_base->session['sess_privileges']['priv_vo_clinical'];?>";
			var priv_vo_pt_info 	= "<?php echo $app_base->session['sess_privileges']['priv_vo_pt_info'];?>";
			var priv_vo_acc 			= "<?php echo $app_base->session['sess_privileges']['priv_vo_acc'];?>";
			var phone_format			= "<?php echo $GLOBALS['phone_format'];?>";
			var phone_min_length	= "<?php echo inter_phone_length(); ?>" ;
			var ssn_format				= "<?php echo inter_ssn_format(); ?>" ;
			var ssn_reg_exp_js		= "<?php echo inter_ssn_reg_exp_js(); ?>" ;
			var ssn_length				= "<?php echo inter_ssn_length(); ?>" ;
			var zip_length				=	"<?php echo inter_zip_length(); ?>" ;
			var zip_ext						=	"<?php echo inter_zip_ext(); ?>" ;
			var state_label				=	"<?php echo inter_state_label(); ?>" ;
			var state_val					=	"<?php echo inter_state_val(); ?>" ;
			var state_length			=	"<?php echo inter_state_length(); ?>";
			var int_country				=	"<?php echo inter_country(); ?>" ;
			var ser_root 					= "<?php echo $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/xml/refphy/" ?>";
			var REF_PHY_FORMAT		= "<?php echo $GLOBALS['REF_PHY_FORMAT']; ?>";
			var inter_date_format	= "<?php echo inter_date_format(); ?>";
			var claim_status_request_js='<?php echo constant('CLAIM_STATUS_REQUEST'); ?>';
			var practice_dir	 		=	"<?php echo constant('PRACTICE_PATH'); ?>";
			var clinical_prev			=	"<?php echo (core_check_privilege(array("priv_vo_clinical")) ? 'true' : 'false'); ?>";
			var providerId = "<?php echo $_SESSION['authId']; ?>";
			var doc_pop = "<?php echo constant('DOC_POPUP');?>";
			var currentTimeZone = "<?php echo date_default_timezone_get(); ?>";
			if( window.opener){
				if( window.opener.top)
					if( window.opener.top.arr_opened_popups)
						var arr_opened_popups = window.opener.top.arr_opened_popups ;			
			}
			
			/*function searchinframe(){
					top.fmain.$("#docs_search").val($("#filterField").val()).trigger("keyup");
				}*/
			
	</script>
</head>
<body>
<!-- Alert & Notification Div -->
<div id="div_alert_notifications"><span class="notification_span"></span></div>
<div id="divCommonAlertMsgNew" class="panel panel-success" style="display:none; position:absolute;z-Index:802; background-color:#FFFFFF;top:20%;left:40%;"></div>

<div class="purple_bar">
	<div class="row">
		<div class="col-sm-2">
			<h4 class="acc_page_name" id="acc_page_name">Documents</h4>
		</div>
        <div  class="col-sm-3 mt5 form-inline" >
            <span id="doc_search_span_id"><label class="search " id="" style="margin-right:2px;" >Search Docs.</label><input type="text" id="filterField" autocomplete="off" class="form-control" value=""  ></span>
		</div>
		<div class="col-sm-5 mt5"><?php echo $patientInfo;?></div>
		<div class="col-sm-2 mt5">
			<!-- PAtient Verbal Communication Icon -->
			<span id="patient_communication"><a href="#" onclick="top.tb_popup(this,'docs_tab');" title="Patient Communication" class="main_iconbar_icon pd0"><img src="<?php echo $GLOBALS['webroot'];?>/library/images/Patient-Communicationicon.png" alt="" title="Pt. Communication" width="34" /></a></span>	
		</div>
	</div>
</div>

<div class="clearfix"></div>

<div class="maincontent">
	<iframe src="<?php echo $DEFAULT_DOC_URL;?>" width="100%" name="fmain" id="fmain" frameborder="0" style="z-index:0; height:<?php echo $app_base->sess_height-280;?>px"></iframe>
</div>
<div class="clearfix"></div>
<footer>
    <div class="container-fluid">
        <div class="row">
        	<div class="col-sm-3">&nbsp;</div>
            <div class="col-sm-7 text-center" id="page_buttons"></div>
            <div class="col-sm-2 ftvertxt">
            	<?php echo date('m/d/y ');?><span id="tick2" class="text11"></span>
            </div>
        </div>
    </div>
</footer>
<div id="div_loading_image" class="text-center">
	<div class="loading_container">
		<div class="process_loader"></div>
    	<div id="div_loading_text" class="text-info">Please wait, while documents are getting ready for you...</div>
	</div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/mootools.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/dg-filter.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-typeahead.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-dropdownhover.min.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
<!--<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.okayNav.min.js"></script>-->
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js?<?php echo filemtime('../../library/js/common.js');?>"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>
<!--<script src="<?php echo $GLOBALS['webroot']; ?>/library/interface/views/core_base.js"></script>-->
<!--<script src="<?php echo $GLOBALS['webroot']; ?>/library/interface/views/core_title_bar.js"></script>-->
<!--<script src="<?php echo $GLOBALS['webroot']; ?>/library/interface/chart_notes/js/buttons.js" type="text/javascript"></script>-->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/md5.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/js_crypto_sha256.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/core_main.js?<?php echo filemtime('../../library/js/core_main.js');?>"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/buttons.js??<?php echo filemtime('../../library/js/buttons.js');?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_providers.js"></script>
<script type="text/javascript">

	function onloadfilter() {
		var filter = new DG.Filter({
			frameName : 'fmain',
			filterField : $('#filterField')[0],
			filterEl : $('#fmain').contents().find('#dhtmlgoodies_topNodes')[0],
			xPathFilterElements : '.tree_link',
			onMatchShowChildren : true,
			listeners : {
				beforefilter : function() {},
				afterfilter : function() {}
			 }
		});
	}
	
	function onloadfilterScanDocs() {
		var filter = new DG.Filter({
			frameName : 'fmain',
			filterField : $('#filterField')[0],
			filterEl : $('#fmain').contents().find('#treemenuScan')[0],
			xPathFilterElements : '.sub_li',
			onMatchShowChildren : true,
			listeners : {
				beforefilter : function() {},
				afterfilter : function() {}
			 }
		});
	}
	function setFocusFilter() {
		$('#filterField').focus();
		$('#filterField').blur();
	}
	$('#fmain').on('load', function(){
		if(top.fmain.document.getElementById('dhtmlgoodies_topNodes')) {
			$('#doc_search_span_id').show();
			onloadfilter();
			setFocusFilter();
		}
		if(top.fmain.document.getElementById('treemenuScan')) {
			$('#doc_search_span_id').hide();
			//onloadfilterScanDocs();
			//setFocusFilter();
		}
	})

	$(document).ready(function(e){
		$('[data-toggle="popover"]').popover();
		$('[data-toggle="tooltip"]').tooltip();
		//Taken action each time main frame URL changes
		$('#fmain').load(function(){
			//disable backspace in frames
			this.contentWindow.document.onkeydown = keyCatcher;
		});
		
		//disable backspace in parent window
		document.onkeydown = keyCatcher;
		show_loading_image('hide');
		top.show_clock();
	});
	
	var WebRoot = '<?php echo $GLOBALS['webroot']; ?>';
</script>
</body>
</html>