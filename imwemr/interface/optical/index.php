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

require_once("../../config/globals.php");
require_once("../../library/patient_must_loaded.php");
require_once('../../library/classes/common_function.php');

require_once($GLOBALS['srcdir'].'/classes/cls_common_function.php');
$cls_object = New CLSCommonFunction;

require_once('../../library/classes/optical_class.php');

$pid = $_SESSION['patient'];
$auth_id = $_SESSION['authId'];
$optical_obj = New Optical($pid,$auth_id);

//Returns tab title
$tab_title_arr = $optical_obj->get_tab_title($_REQUEST['showpage']);
$header_title = $tab_title_arr['header_title'];
$header_tab_title = $tab_title_arr['tab_title'];

$library_path = $GLOBALS['webroot'].'/library';
$global_currency = html_entity_decode(show_currency());

//This is used to hide elements in the included files 
// e.g --> if(isset($contact_lens_view)){'hide element'}
$contact_lens_view = 'yes';

$js_php_var_arr = array();
$js_php_var_arr['currency'] = html_entity_decode($global_currency);
$js_php_var_arr['typeahead'] = $optical_obj->vendor_typeahead_arr;
$js_php_var_arr['p_id'] = base64_encode($optical_obj->patient_id);

$js_glob_arr = json_encode($js_php_var_arr);


/* if(isset($_REQUEST['form_save'])){
	if($_POST['txt_save_type'] == 'save'){
		extract($_POST);
		$_POST['operator_id'] = $optical_obj->auth_id;
		$_POST['frame_cost'] = str_replace($global_currency,'',$frame_cost);
		$_POST['lenese_cost'] = str_replace($global_currency,'',$lenese_cost);
		$_POST['adminPatientLenseCost'] = str_replace($global_currency,'',$adminPatientLenseCost);
		$_POST['tint_cost'] = str_replace($global_currency,'',$tint_cost);
		$_POST['scr_cost'] = str_replace($global_currency,'',$scr_cost);
		$_POST['ar_cost'] = str_replace($global_currency,'',$ar_cost);
		$_POST['other_cost'] = str_replace($global_currency,'',$other_cost);
		$_POST['total'] = str_replace($global_currency,'',$total);
		$_POST['deposit'] = str_replace($global_currency,'',$deposit);
		$_POST['balance'] = str_replace($global_currency,'',$balance);	
		$_POST['frame_scr_price'] = str_replace($global_currency,'',$frame_scr_price);
		$_POST['tini_solid_price'] = str_replace($global_currency,'',$tini_solid_price);
		$_POST['tini_gradient_price'] = str_replace($global_currency,'',$tini_gradient_price);
		$_POST['transition_price'] = str_replace($global_currency,'',$transition_price);
		$_POST['frame_ar_price'] = str_replace($global_currency,'',$frame_ar_price);
		$_POST['polar_cost'] = str_replace($global_currency,'',$polar_cost);
		$_POST['trans_cost'] = str_replace($global_currency,'',$trans_cost);
		$_POST['Slad_Off_cost'] = str_replace($global_currency,'',$Slad_Off_cost);
		$_POST['hi_cost_price'] = str_replace($global_currency,'',$hi_cost_price);
		$_POST['tint_cost_price'] = str_replace($global_currency,'',$tint_cost_price);
		$_POST['Photochromatic_cost'] = str_replace($global_currency,'',$Photochromatic_cost);
		$_POST['framePrice'] = str_replace($global_currency,'',$txtframePrice);
		$_POST['prism_cost'] = str_replace($global_currency,'',$prism_cost);
		$_POST['uv_cost'] = str_replace($global_currency,'',$uv_cost);
		
		$_POST['order_place_date'] = date('Y-m-d');
		if(!$txt_order_save_id)
			$order_id = $optical_obj->AddRecords1($_POST,'optical_order_form');
		else
			$order_id = $optical_obj->UpdateRecords1($txt_order_save_id,'Optical_Order_Form_id',$_POST,'optical_order_form');
		if($order_id){	
			$msg = 'Order successfully saved';
		}
		if($txtpostCharges == 'Save & Post'){
			require_once('optical_enter_charges.php');
		}
	}
} */
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
    <link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
    <!-- Messi Plugin for fancy alerts CSS -->
	<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
    <!-- DateTime Picker CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]--> 
  	
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
    <!-- jQuery's Date Time Picker -->
    <script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
    <!-- Bootstrap -->
    <script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
    
    <!-- Bootstrap Selectpicker -->
    <script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
    <!-- Bootstrap typeHead -->
    <script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
    <script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
	<script>
		var js_php_arr = JSON.parse('<?php echo $js_glob_arr; ?>');
		<?php if(empty($msg) === false){ ?>
			top.fAlert('<?php echo $msg; ?>');
		<?php } ?>
	</script>
  </head>
  <style>
	.optical_content_div .mainwhtbox{box-shadow:none!important;padding:0px;margin:0px}
	.od{color:blue;}
	.os{color:green;}
	.ou{color:#9900cc;}
  </style>
	<body>
    <div class="container-fluid">
		<div class="mainwhtbox">
			<div class="row">
				<div class="col-sm-12">
					<?php
						include_once 'optical_header.php';
					?>
				</div>
				<div class="col-sm-12 optical_content_div">
					<?php
						$file_path = $_REQUEST['showpage'];
						if($file_path == ''){
							$path = $GLOBALS['fileroot']. '/interface/chart_notes/contact_lens_order_history.php';
						}else{
							$path = $GLOBALS['fileroot']. '/interface/optical/'.$file_path.'.php';
						}
						include_once $path;
					?>
				</div>
			</div>
		</div>
	</div>
    
    <script>
			var curr_tab_title = '<?php echo $header_title;  ?>';
			top.$('#acc_page_name').html(curr_tab_title);
			$('.datepicker').datetimepicker({timepicker:false,format:top.jquery_date_format,autoclose: true,scrollInput:false});
			$('.selectpicker').selectpicker();
		</script>
	</body>
</html>