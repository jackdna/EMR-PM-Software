<?php 
require_once("../../config/config.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

//check for file in progress
$file='../../interface/patient_interface/uploaddir/importStatus.txt';
file_put_contents($file,'Please do not hit back button or refresh page untill sync complete message displays.');

/*Generate Frames Data Autohrization Token*/
include('../../library/data_api/framesData.php');
require_once('../../library/data_api/framesDataDetails.php');	/*FramesData Credentials*/

$obj = new framesData();
$auth_message = array();

try{
	$obj->setConfig($configs);	/*Create Authorization Tocker*/
	
	$auth_message['renew'] = $obj->renewalMessage();
	$auth_message['terms'] = (bool)$obj->termsAgreement();
	if(!$auth_message['terms']){
		$obj->clearTocken();
	}
}
catch(Exception $e){
	$auth_message['error'] = $e->getMessage();
}

/*End Frames Data Autohrization Token*/
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Optical</title>
    <link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" type="text/css" />
	<link rel="stylesheet" href="../../library/js/themes/base/jquery-ui.css?<?php echo constant("cache_version"); ?>" type="text/css" />
	<script src="../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
	<script src="../../library/js/ui/jquery.ui.core.js?<?php echo constant("cache_version"); ?>"></script>
	<script src="../../library/js/ui/jquery.ui.datepicker.js?<?php echo constant("cache_version"); ?>"></script>
<style type="text/css">
.row{
	line-height:30px;
}

/*div:nth-of-type(odd).row*/
.rowBg{
	background-color:#EFEFEF;	
}
.chkbx{
	width: 30px;
	float:left;
}
.lbl{
	float:left;	
	font-weight:bold;
}
.clearfix{
	clear:both;
	height:1px;
}
.log{
	float: right;
    margin-right: 350px;
    font-weight: bold;
    font-size: 14px;
    color: #555;
}
</style>

<script type="text/javascript">
var authMessage = <?php echo json_encode($auth_message); ?>
</script>
</head>
<body>
<div class="tab_container" style="float:left; width:100%;margin:10px 0 0 0;">
<h3 class="listheading">Frames Data Import</h3>
<?php
$sql = "SELECT `v`.`opt_type`,`v`.`entered_date`,`v`.`entered_time`, `u`.`fname`, `u`.`lname` FROM `in_options` `v` LEFT JOIN `users` `u` ON(`v`.`entered_by`=`u`.`id`) WHERE `v`.`module_id`='8'";
$rows = imw_query($sql);
$logDetails = array();
$types = array(6=>1,7=>2,8=>3,9=>4,10=>5);
if($rows && imw_num_rows($rows)>0){
	while($row = imw_fetch_assoc($rows)){
		
		$logDetails[$types[$row['opt_type']]]['time'] = $row['entered_time'];
		$logDetails[$types[$row['opt_type']]]['date'] = date("M d Y",strtotime($row['entered_date']));
		$name = $row['lname'].", ".$row['lname'];
		$name = trim(trim($name),",");
		$logDetails[$types[$row['opt_type']]]['user'] = $name;
	}
}
	$options = array(
					 1=>	"Manufacturers",
					 2=>	"Brands",
					 3=>	"Colors",
					 4=>	"Frames",
					 5=>	"Frames Image"
					 );

//check for any active import
?>
<form id="framesData" name="framesData" action="../../library/data_api/framesImport.php" method="GET" target="process_file">
<input type="hidden" name="start" value="0">
<input type="hidden" name="limit" value="50">
<?php
foreach($options as $key=>$opt):
	$rowBg = ( $key%2 === 0 ) ? 'rowBg' : '';
?>
	<div class="row <?php echo $rowBg; ?>">
    	<div class="chkbx">
        	<input type="checkbox" name="option[]" value="<?php echo $key; ?>" id="option_<?php echo $key; ?>" />
        </div>
        <div class="lbl">
        	<label for="option_<?php echo $key; ?>"><?php echo $opt; ?></label>
        </div>
        <div class="log">
<?php if(isset($logDetails[$key])): ?>
        Last Synced on: <?php echo $logDetails[$key]['date']." ".$logDetails[$key]['time']; ?>&nbsp;&nbsp;&nbsp;
        By: <?php echo $logDetails[$key]['user']; ?>
<?php endif; ?>
        </div>
        <div class="clearfix"></div>
    </div>
<?php
	if( $key==4 ):
?>
	<div class="row <?php echo $rowBg; ?>" id="updateStyles" style="padding-left:30px; font-weight:bold; display:none;">
		Update Records From <input type="text" name="startDate" id="startDate" style="width:80px;"/>
		To <input type="text" name="endDate" id="endDate" style="width:80px;"/>
	</div>
<?
	endif;
endforeach;
?>
		<input type="hidden" value="Sync Now" name="sync_now">
</form>
<iframe name="import_status_bar" id="import_status_bar" src="import_status_bar.html" width="100%" height="<?php echo $_SESSION['wn_height']-442; ?>px" frameborder="0" style="display:none"></iframe>
<iframe name="process_file" id="process_file" src="" width="100%" height="100px" frameborder="0"></iframe>
</div>

<script type="text/javascript">
function submitFrom(){
	if(validateForm()){
		document.framesData.submit();
	}
}
function validateForm(){
	var res = $("input[name='option[]']:checked").length
	if(res<=0){
		top.falert("Please select at least one record");
		return false; 
	}
	
	document.getElementById('framesData').style.display='none';
	document.getElementById('import_status_bar').style.display='block';
	return true;
}

function framesData(resp, resp1, resp2){
	if(resp && resp1){
		$.ajax({
			method: 'POST',
			url: top.WRP+'/interface/admin/ajax.php',
			data: 'action=agreeFramesData',
			complete: function(obj){
				window.location.reload();
			}
		});
	}
	else{
		top.main_iframe.$('.tabs>li:last-child').click();
	}
}

$(document).ready(function(){
	
	/*Renewal Message*/
	if( typeof(authMessage.renew) !== 'undefined' && authMessage.renew !== '' ){
		top.falert(authMessage.renew);
	}
	else if( typeof(authMessage.terms) !== 'undefined' && authMessage.terms !== true ){
		
		var html = $('<div>').css({height: '480px', width: '100%'});
		
		$('<h3>').css('margin-top', '0px').text('Frames Data Terms & Conditions').appendTo(html);
		
		$('<iframe>').css({width: '100%', height: '424px'}).attr({src: 'https://www.framesdata.com/terms/termsandconditions.pdf'}).appendTo(html);;
		
		var terms = $('<div>').css({'margin-top': '8px'});
		var label = $('<label>').text('I Agree to Frames Data Terms & Conditions.').appendTo(terms);
		$('<input>').css('vertical-align', 'middle').attr({type: 'checkbox', id: 'framesDataAgree'}).prependTo(label);
		$(html).append(terms);
		top.fconfirm(html, framesData, 'verify');
	}
	else if( typeof(authMessage.error) !== 'undefined' && authMessage.error !== true ){
		top.falert(authMessage.error);
	}
	/*End Renewal Message*/
	
	var currentDate = new Date("<?php echo date("m-d-Y"); ?>");
	$("#startDate").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "mm-dd-yy",
		maxDate: currentDate,
		/*Restrick end Date to 5 days from start date*/
		onClose: function(selectedDate) {
			maxDate = new Date(selectedDate);
			maxDate.setDate(maxDate.getDate() + 4);
			selectedDate = new Date(selectedDate);
			if(maxDate > currentDate){
				maxDate = currentDate;
			}
			
			$("#endDate").datepicker(
				"change",{
					minDate: selectedDate,
					maxDate: maxDate
				}
			);
		}
	});
	
	$("#endDate").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "mm-dd-yy",
		maxDate: currentDate,
		/*Enter Start Date 5 days before end date if Start Date is not selected*/
		onClose: function(selectedDate) {
			startDate = $("#startDate").val();
			if(startDate==""){
				startDate = new Date(selectedDate);
				startDate.setDate(startDate.getDate() - 4);
				$("#startDate").datepicker("setDate",startDate);
			}
		}
	});
	
	$("#option_4").on('change', function(){
		$("#updateStyles").slideToggle();
	});
	
	$("#option_5").on('change', function(){
		$("#imageOptions").slideToggle();
	});
	
	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Sync Now","top.main_iframe.admin_iframe.submitFrom();");
	top.btn_show("admin",mainBtnArr);
});
</script>
</body>
</html>