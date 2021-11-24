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
require_once("../../library/classes/class.tests.php");
require_once("../../library/classes/SaveFile.php");

$library_path 			= $GLOBALS['webroot'].'/library';
$patient_id				= $_SESSION['patient'];
$objTests				= new Tests;
$oSaveFile 				= new SaveFile($patient_id);
$objTests->patient_id 	= $patient_id;
$scan_id				= xss_rem($GLOBALS['scan_id']);
$test_table				= xss_rem($GLOBALS['test_table']);

//----THIS IMAGE DETAILS-------
$scan_rs				= $objTests->get_test_images_by_id($scan_id);
$CUR_img_details		= $scan_rs['scan_uploads'];
$CUR_img_type			= strtolower($CUR_img_details['extension']);
$CUR_img_URL			= $CUR_img_details['original'];
$CUR_test_id			= $scan_rs['test_id'];
//----THIS TEST ALL IMAGES-----
$this_test_images = $objTests->get_test_images($patient_id,$test_table,$CUR_test_id,$test_type,true);

//----GET ALL ACTIVE TESTS FROM ADMIN------
$ActiveTests			= $objTests->get_active_tests();

//----GET PATIENT'S TESTS DONE-------
$patient_tests			= $objTests->get_patient_saved_tests($patient_id,$ActiveTests,false,$test_table);

//User and  User_type
$logged_user 	= $objTests->logged_user;
$userType 		= $objTests->logged_user_type;

//-----MARK THIS IMAGE/FILE  AS VIEWED BY THIS PROVIDER----
$objTests->providerViewLogFun($scan_id,$logged_user,$patient_id,'tests');
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>Tests Image Viewer</title>
<!-- Bootstrap -->
<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
<!-- Bootstrap Selctpicker CSS -->
<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
<!-- Messi Plugin for fancy alerts CSS -->
<!-- DateTime Picker CSS -->
<link href="<?php echo $library_path; ?>/css/tests.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/remove_checkbox.css" rel="stylesheet" type="text/css">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]--> 

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
<!-- Bootstrap -->
<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
<script src="<?php echo $library_path; ?>/js/tests.js" type="text/javascript"></script>
<script type="text/javascript">
	var imgPath = "<?php echo $GLOBALS['webroot'];?>";
	var arr_ActiveTests = JSON.parse('<?php echo json_encode($ActiveTests);?>');
	function makeItScrolling(m){
		if(m){
			$('img#main_view_obj').hide();
			$('iframe#main_view_obj, .resetspan').show();
		}else{
			$('iframe#main_view_obj, .resetspan').hide();
			$('img#main_view_obj').show();
		}
		init_page_display_img();
	}
</script>
<style type="text/css">
.tstlst_tab{
	overflow:auto !important;	
}
#rightpan1{background-color: #673782;}
#scan_big_img<?php echo $scan_id;?>{border:solid 4px #aaa; border-left-width:5px; border-right-width:5px;}
.resetspan{display:inline-block; border:1px solid #ccc; background-color:#000; margin:10px; position:absolute; top:15px; color:#fff; padding:2px 8px; border-radius:8px; font-weight:bold; opacity:0.3; cursor:pointer;}
</style>
</head>
<body>
<div class="newtest noshow_checkbox">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-10" id="leftpan1">
            	<?php if($CUR_img_type=='pdf'){?>
                	<iframe id="main_view_obj" tagType="iframe" src="<?php echo $CUR_img_URL;?>" width="100%" height="500" frameborder="0"></iframe>
                <?php }else{?>
                	<img id="main_view_obj" tagType="img" onClick="makeItScrolling(1)" src="<?php echo $CUR_img_URL;?>" class="img img-responsive link_cursor">
            		<span class="resetspan" onClick="makeItScrolling(0);" style="display:none;">RESET</span>
                    <iframe id="main_view_obj" tagType="iframe" src="<?php echo $CUR_img_URL;?>" width="100%" height="500" frameborder="0" style="display:none"></iframe>
                <?php }?>
            </div>
            <div class="col-sm-2" id="rightpan1">
                <div class="clearfix"></div>
                <div>
                    <?php //----GET PATIENT SAVED TESTS FOR ACTIVE TESTS-----
                    foreach($patient_tests as $pt_test_rs){?>
                    <div class="clearfix"></div>
                    <div class="tstlst_tab">
                        <ul>
                            <?php $ajax_milliseconds = 1000;
							foreach($pt_test_rs['test_rs'] as $test_rs){
							//pre($test_rs);continue;
                            $flag_color = $objTests->get_test_flag_status($test_rs,$pt_test_rs['test_name']);
                            $flag_title = $objTests->get_test_flag_title($flag_color);
                            $list_test_name = $test_rs['dt'];
							
							/*****SKIP SHOWING PURGED TEST******/
                            if(!empty($test_rs['purged'])){continue;}

                            /****CHECKING IF PICTURE ICON NEED TO SHOW***/
                            $current_list_test_images = $objTests->get_test_images($patient_id,$pt_test_rs['test_table'],$test_rs['tId'],$pt_test_rs['test_type']);

							if($current_list_test_images){?>
	                            <li><a class="link_cursor" data-parent="#saved_tests_container" data-toggle="collapse" href="#saved_tests_images<?php echo $test_rs['tId'];?>"><?php echo $list_test_name;?></a></li>
                            	<script type="text/javascript">
									setTimeout(function(){load_this_test_images('<?php echo $patient_id;?>','<?php echo $pt_test_rs['test_table'];?>','<?php echo $test_rs['tId'];?>','<?php echo $pt_test_rs['test_type'];?>',true,'saved_tests_images<?php echo $test_rs['tId'];?>');},'<?php echo $ajax_milliseconds;?>');
								</script>
                            	<div class="collapse in" id="saved_tests_images<?php echo $test_rs['tId'];?>"></div>
                            <?php 
								$ajax_milliseconds += 1000;
							}?>
                            
                           <?php }?>
                        </ul>
						<div class="clearfix"></div>
                    </div>
                    <?php
                    }							
                    ?>
					<div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
    	<div class="col-sm-12 text-center">
            <button class="btn btn-warning" onClick="javascript:window.close();">Close Window</button>
         </div>
	</div>
    <div class="clearfix"></div>
    
</div>
 
<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
$(document).ready(function(e) {
    init_page_display_img();
});
function init_page_display_img(){
	var main_height = window.innerHeight;
	var saved_test_h	= parseInt((main_height-50));
	$('#leftpan1, #rightpan1').height(saved_test_h+'px');
	$('#main_view_obj').css('max-height',saved_test_h);
	$('img#main_view_obj').css({'max-width':($('#leftpan1').width()-10),'min-width':'auto'});
	$('iframe#main_view_obj').css('min-height',saved_test_h);
	$('.tstlst_tab').height(saved_test_h-10);
	//alert(saved_test_h);
}
</script>

</body>
</html>