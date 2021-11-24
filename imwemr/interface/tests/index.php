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
require_once("../../library/classes/ChartTestPrev.php");

$library_path 			= $GLOBALS['webroot'].'/library';
$patient_id				= $_SESSION['patient'];
$objTests				= new Tests;
$objTests->patient_id 	= $patient_id;
$showpage				= $_GET['showpage'];

$itsIndexPage			= true;
//----GET ALL ACTIVE TESTS FROM ADMIN------
$ActiveTests			= $objTests->get_active_tests();

//----GET PATIENT'S TESTS DONE TODAY-------
$patient_tests_todays			= $objTests->get_patient_saved_tests($patient_id,$ActiveTests,true);
// foreach($patient_tests as $pt_test_rs){

//User and  User_type
$logged_user 	= $objTests->logged_user;
$userType 		= $objTests->logged_user_type;

//No Pop
$noP = 1;
if(isset($_GET["noP"]) && !empty($_GET["noP"])){
	$noP = $_GET["noP"];
}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

<title>Tests :: <?php echo $get_test_name;?></title>
<!-- Bootstrap -->
<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
<!-- Bootstrap Selctpicker CSS -->
<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
<!-- Messi Plugin for fancy alerts CSS -->
<!-- DateTime Picker CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
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
<!-- jQuery's Date Time Picker -->
<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
<!-- Bootstrap -->
<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>

<!-- Bootstrap Selectpicker -->
<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
<!-- Bootstrap typeHead -->
<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
<script src="<?php echo $library_path; ?>/js/tests.js" type="text/javascript"></script>
<script type="text/javascript">
var imgPath = "<?php echo $GLOBALS['webroot'];?>";
var arr_ActiveTests = JSON.parse('<?php echo json_encode($ActiveTests);?>');
function ShowTestPage(id,ask){
	var this_test_rs 	= arr_ActiveTests[id];
	test_url			= this_test_rs['script_file'];
	test_visible_name 	= this_test_rs['temp_name'];
	if(ask!='0'){
		top.fancyConfirm("Do you really want to create new \""+test_visible_name+"\" test, as this is already created for today", "New Test Confirmation", "top.fmain.ShowTestPage('"+id+"',false)");
	}else{
		window.location.href = '../tests/'+test_url+'?test_master_id='+id+"&noP=1&pop=0&tId=&force_new_test=1";
	}
}
</script>
</head>
<body>
<div class="newtest noshow_checkbox">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-10">
            	<div class="row" id="main_tests_list">
                	<?php foreach($ActiveTests as $test_name_id=>$test_name_rs){
					$show_name = $test_name_rs['temp_name'];
					$today_this_test = count($patient_tests_todays[$test_name_id]['test_rs']);
					?>
					<div class="col-sm-3 list_item link_cursor" id="test_div_<?php echo $test_name_id;?>" onClick="ShowTestPage('<?php echo $test_name_id;?>','<?php echo $today_this_test;?>')"><?php echo $show_name;?></div>
                    <?php
				}?>
                </div>
            </div>
            <?php require_once("test_saved_list.php");?>
        </div>
    </div>
</div>
 
<script>

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
$(document).ready(function(e) {
    init_page_display();
});
function init_page_display(){
	if(typeof(window.top.fmain)!='undefined'){var main_height = window.top.$('#fmain').height();}
	else if(typeof(window.opener.top.fmain)!='undefined'){var main_height = window.opener.top.$('#fmain').height();}
	
	var saved_test_h	= parseInt((main_height-50));
	$('#saved_tests_container').height(saved_test_h);
	//alert(main_height);
	
}
top.$('#acc_page_name').html('Test');

<?php
if($showpage!='' && intval($showpage)>0){
	//redirect to new tests.
	echo 'document.getElementById(\'test_div_'.intval($showpage).'\').click();';
	
}
?>
</script>

</body>
</html>