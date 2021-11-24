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
$library_path = $GLOBALS['webroot'].'/library';
include_once($library_path.'/classes/common_function.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>imwemr</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery-ui.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-select.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-colorpicker.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/messi/messi.css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/common.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/admin.css" type="text/css">
    <?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
        <link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
    <?php } ?>
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
		<script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
	<![endif]-->
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery-ui.min.1.11.2.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.dragToSelect.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-formhelpers-colorpicker.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/messi/messi.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/simple_drawing.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/core_main.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/Driving_License_Scanning.js"></script>
	
	
</head>