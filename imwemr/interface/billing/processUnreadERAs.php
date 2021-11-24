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
require_once(dirname(__FILE__)."/../../library/classes/billing_functions.php");
set_time_limit(0);

//----SQL DATE FORMAT---
$getSqlDateFormat= get_sql_date_format();

$unreadERAids = xss_rem($_GET['unreadERAids']);	/* Reject parameter with unwanted values - Security Values */

$unreadERAidsArr = explode(',',$unreadERAids);
$start = isset($_GET['st']) ? intval($_GET['st']) : 0;


$electronicFilesTblId = $unreadERAidsArr[$start];
$write_off_code=$_REQUEST['write_off_code'];
require("createERA835DB.php");

//sleep(2);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<title><?php echo $title; ?></title>
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet">
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
	var zPath = "<?php echo $GLOBALS['rootdir'];?>";
	var JS_WEB_ROOT_PATH 	= "<?php echo $GLOBALS['webroot']; ?>";
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
<script type="text/javascript">
<?php if($start < count($unreadERAidsArr)){?>
$(document).ready(function(e) {
    window.location.href = 'processUnreadERAs.php?unreadERAids=<?php echo $unreadERAids;?>&st=<?php echo $start+1;?>';
});
<?php }else{?>
	l = window.top.fmain.location.href;
	window.top.fmain.location.href = l;
<?php }?>
</script>
</head>
<body class="whitebox">
<?php if($start < count($unreadERAidsArr)){?>
	<h3>Processing <?php echo ($start+1);?> of <?php echo count($unreadERAidsArr);?>.</h3>
    <p>Please do not close this dialog box until process complete.</p>
    
<?php
}else{?>
	<h3>Process Complete</h3>
    <p>You may close this dialog box now.</p>
	<div class="row">
    	<div class="col-sm-12 text-right"><input type="button" class="btn btn-danger" value="Close" onClick="top.removeMessi();"></div>
    </div>
<?php
}
?>
</body>

</html>
