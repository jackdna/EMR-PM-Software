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
include_once(dirname(__FILE__)."/../../config/globals.php");
if(isERPPortalEnabled()) {
	include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");
	$OBJRabbitmqExchange = new Rabbitmq_exchange();
}
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/billing_functions.php");
include_once(dirname(__FILE__)."/accounting_session.php");
if($without_pat!=""){
}else{
	if(!(isset($_SESSION["patient"]) && !empty($_SESSION["patient"]))){
		include_once(dirname(__FILE__)."/../../library/patient_must_loaded.php");
	}
}

if($_SESSION["ent_commt_pat"]!=$_SESSION["patient"]){
	$_SESSION["ent_commt_pat"]="";
}
$patient_id = $_SESSION['patient'];
$operator_id = $_SESSION['authId'];
$curr_time = date('H:i:s');
$curr_date = date('Y-m-d');
$curr_date_time = date('Y-m-d H:i:s');

$encounter_id = $_REQUEST['encounter_id'];
if(empty($encounter_id) == true){
	$encounter_id = $_SESSION['encounterIdFromCL'];
	$_SESSION['encounterIdFromCL'] = NULL;
}
$_SESSION["accounting_tab"] = "";
if($title!=""){
	$_SESSION["accounting_tab"]=strtolower(str_replace(' ','_',$title));
}else{
	$title="Accounting";
}

//Setting > Billing > Phrases Typeahead
$phraseArr = array();
$sel_rec_comm=imw_query("select * from int_ext_comment where status='0' order by comment");
while($sel_comm=imw_fetch_assoc($sel_rec_comm)){
	$coment = addslashes($sel_comm['comment']);
 	array_push($phraseArr, $coment);
}		
if(count($phraseArr) > 0) $phraseArr = json_encode($phraseArr);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<title><?php echo $title; ?></title>
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/accounting.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.datetimepicker.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/core.css" type="text/css" rel="stylesheet">
<?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
<?php } ?>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
  <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
	var pat_js='<?php echo $_SESSION["patient"]; ?>';
	var claim_status_request_js='<?php echo constant('CLAIM_STATUS_REQUEST'); ?>';
	var server_name='<?php echo strtolower($billing_global_server_name); ?>';
	var default_user_selected='<?php echo isDefaultUserSelected(); ?>';
	var medicare_imp='<?php echo implode(',',$arr_Medicare_payers); ?>';
	var zPath = "<?php echo $GLOBALS['rootdir'];?>";
	var JS_WEB_ROOT_PATH 	= "<?php echo $GLOBALS['webroot']; ?>";
    var phraseArr = <?php echo $phraseArr; ?>;
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-typeahead.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/icd10_autocomplete.js"></script> 
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/superbill.js"></script> 
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/acc_common.js"></script> 
</head>
<script>
	acc_js_date_format = '<?php echo phpDateFormat(); ?>';
	$(function(){
		$('[data-toggle="tooltip"]').tooltip();
		$('.selectpicker').selectpicker();
		$('.date-pick').datetimepicker({
			timepicker:false,
			format:acc_js_date_format, //'m-d-Y',
			formatDate:'Y-m-d',
			scrollInput:false
		});
	});	
</script>
<body>
<?php if($hide_acc_div==""){?>
<div class="mainwhtbox">
	<input type="hidden" name="acc_view_only" id="acc_view_only" value="<?php echo $acc_view_only; ?>">
	<input type="hidden" name="acc_view_pay_only" id="acc_view_pay_only" value="<?php echo $acc_view_pay_only; ?>">
	<input type="hidden" name="acc_view_chr_only" id="acc_view_chr_only" value="<?php echo $acc_view_chr_only; ?>">
	<input type="hidden" name="acc_edit_financials" id="acc_edit_financials" value="<?php echo $acc_edit_financials; ?>">
    <input type="hidden" name="bi_edit_batch" id="bi_edit_batch" value="<?php echo $bi_edit_batch; ?>">
	
	<div id="poe_div"></div>
	<div id="adm_div"></div>
	<div id="pat_spec_div"></div>
	<div id="multi_cpt_shw_id" style="display:none;position:absolute;padding-right:4px;" class="text_b_w"></div>
	<div id="assesMentTemp"></div>
	<div id="ci_pp"></div>
	<div class="wthbox1">
	<div id="auth_div" class="div_popup"></div>
	<div class="div_popup hide border bg2" id="ins_show_div"></div>
	<div style="position:absolute;top:130;left:170;visibility:hidden;" id="msgDiv"></div>
	<div style="position:absolute;display:none; top:140px; left:250px;" id="divChoosePqriCodes"></div>
<?php } ?>		
