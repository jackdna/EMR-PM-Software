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
?>
<?php
/*
  FILE : scheduler_new_report.php
  PURPOSE : Search criteria for scheduler report
  ACCESS TYPE : Direct
 */
include_once(dirname(__FILE__)."/../../../config/globals.php");
include_once(dirname(__FILE__)."/../../../library/classes/class.mur_reports.php");
$library_path 	= $GLOBALS['webroot'].'/library';
$objMUR			= new MUR_Reports;

if(stristr($dbtemplate_name,'2016')) 		$mur_version = '2016';
else if(strtolower($dbtemplate_name)=='transition aci 2018' || stristr($dbtemplate_name,'2017') || strtolower($dbtemplate_name)=='mips/macra') 	$mur_version = '2017';
else if(stristr($dbtemplate_name,'2018')) 	$mur_version = '2018';
else if(stristr($dbtemplate_name,'2019')) 	$mur_version = '2019';
else if(stristr($dbtemplate_name,'2020')) 	$mur_version = '2020';

//From main file, where this current file is included.
$dbtemplate_name_show = ($mur_version=='2017') ? 'Transitional ACI (Promoting Interoperability) 2018' : (($mur_version=='2018') ? 'ACI (Promoting Interoperability) 2018' : (($mur_version=='2019') ? '2019 MIPS PI' : $dbtemplate_name));

$objMUR->mur_version = $mur_version;
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>:: imwemr ::</title>
<!-- Bootstrap -->
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
<script>var result_gen = false;</script>
<script type="text/javascript" src="<?php echo $library_path;?>/js/reports_mur.js?<?php echo filemtime('../../library/js/reports_mur.js');?>"></script>
<style>
.pd5.report-content {
	background-color: #EAEFF5;
}	
#content1{
	background-color:#EAEFF5;
}
#mur_version,.date-pick{width:100px;}
.visibility_hidden{visibility:hidden;}
</style>
</head>
<body>
<div class="container-fluid">
    <div class="whitebox" id="report_form">
        <form name="form_mur" id="form_mur" method="post" action="" autocomplete="off">
        <input type="hidden" name="mur_version" id="mur_version" value="<?php echo $mur_version;?>">
        <input type="hidden" name="provider" id="provider" value="">
        <input type="hidden" name="facility_id" id="facility_id" value="">
        <?php if($mur_version=='2019' || $mur_version=='2020'){include("mur_report_form_2019.php");}else{include("mur_report_form.php");}?>
        </form>
    </div>
    <div class="clearfix"></div>
    <div class="whitebox">
        <div id="report_result_area">
    	    &nbsp;
        </div>
    </div>
</div>
<script type="text/javascript">
function set_container_height(){
	h = parseInt(window.innerHeight)-parseInt($('#report_form').height());
	h = h-60; //margins
	$('#report_result_area').css({
		'height':(h),
		'max-height':(h),
		'overflow-x':'hidden',
		'overflowY':'auto'
	});
} 

$(window).load(function(){
	set_container_height();
});

$(window).resize(function(){
	set_container_height();
});

//Btn --
if(result_gen) {
var ar = [["qrda_cat1","QRDA Cat I","window.frames['fmain'].searchResult(7);"],
			  ["qrda_cat3","QRDA Cat III","window.frames['fmain'].searchResult(8);"]];
	top.btn_show("mur_stage_qrda",ar);
} else {
    top.btn_show();
}
//Btn --

</script> 
</body>
</html>