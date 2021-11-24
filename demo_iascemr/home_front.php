<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgery Center EMR</title>
<style>
	td{
		font-family:verdana,arial;font-size:11px;
	}
</style>
<?php
$spec= "
</head>
<body>";
include_once("common/commonFunctions.php");
include("common/link_new_file.php");
if($sel_month_number == "") {
	$sel_month_number = date("m");
}
if($sel_year_number == "") {
	$sel_year_number = date("Y");
}
if($sel_day_number == "") {
	$sel_day_number = date("d");
}
$surgerycenter_address_qry = "select * from surgerycenter where surgeryCenterId=1";
$surgerycenter_address_res = imw_query($surgerycenter_address_qry) or die(imw_error());
$surgerycenter_address_row = imw_fetch_array($surgerycenter_address_res);
$surgerycenter_name = $surgerycenter_address_row["name"];
$surgerycenter_address = stripslashes($surgerycenter_address_row["address"]);
$surgerycenter_city = $surgerycenter_address_row["city"]; 
$surgerycenter_state = $surgerycenter_address_row["state"]; 
$surgerycenter_zip = $surgerycenter_address_row["zip"]; 
$surgerycenter_loginLegalNotice = nl2br($surgerycenter_address_row["loginLegalNotice"]);

?> 

<script>
var today = new Date();
var day = today.getDate();
var month = today.getMonth()
var year = y2k(today.getYear());
var mon=month+1;
if(mon<=9){
	mon='0'+mon;
}
var todaydate=mon+'-'+day+'-'+year;
function y2k(number){
	return (number < 1000)? number+1900 : number;
}
function newWindow(q){
	mywindow=open('mycal1.php?md='+q,'','width=200,height=250,top=200,left=300');
	//mywindow.location.href = 'mycal1.php?md='+q;
	if(mywindow.opener == null)
		mywindow.opener = self;
}
function restart(q){
	//fillDate = ''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year;
	fillDate = ''+ padout(month - 0 + 1) + '/'  + padout(day) + '/' +  year;
	if(q==8){
		if(fillDate > todaydate){
			alert("Date Of Service can not be a future date")
			return false;
		}
	}
	//document.getElementById(q).value=fillDate;
	document.getElementById(q).innerHTML=fillDate;
	mywindow.close();
}
function padout(number){
	return (number < 10) ? '0' + number : number;
}
function tab_change(){
	iframe_home_inner_front.document.getElementById('patient_info').style.display='none';
	iframe_home_inner_front.document.getElementById('patient_search_results_id').style.display='none';
	iframe_home_inner_front.document.getElementById('cal').style.display='block';
}
function back_pat_info(){
	iframe_home_inner_front.document.getElementById('cal').style.display='none';
	iframe_home_inner_front.document.getElementById('patient_search_results_id').style.display='none';
	iframe_home_inner_front.document.getElementById('patient_info').style.display='block';
}
function today_sch() {
	iframe_home_inner_front.location="home_inner_front.php?day=<?php echo $sel_day_number;?>&selected_month_number=<?php echo $sel_month_number;?>&year_now=<?php echo $sel_year_number;?>";
}

</script>
<div class="" >
    <div class="subtracting-head">	
        <div class="head_scheduler new_head_slider padding_head_adjust_admin margin-bottom-0">
            <span><?php echo $surgerycenter_name;?></span>
        </div>
        <div class="msg msg-clear text-center"><?php echo $surgerycenter_address;?><br><?php echo $surgerycenter_city.', '.$surgerycenter_state.' '.$surgerycenter_zip;?></div>
        <div class="head_tab_inline text-center width_hundred"><span> Hipaa Confidential Statement </span></div>
    </div>
	<div id="div_scroll_id" class=" wrap_inside_admin scrollable_yes"><?php echo stripslashes($surgerycenter_loginLegalNotice);?></div>
</div>




<script>
	var redirect_txt_patient_search_value = '<?php echo $_GET["redirect_txt_patient_search_id"];?>';
	//var redirect_txt_patient_search_value = eval(document.getElementById("txt_patient_search"))
	if(redirect_txt_patient_search_value!="") {
		//alert(iframe_home_inner_front);
		top.document.getElementById('admin_audit_report_id').style.display='block';
		
		top.iframeHome.iframe_home_inner_front.location = "home_inner_front.php?txt_patient_search_id="+redirect_txt_patient_search_value+"&display_cal=none&display_patient_sch=none&display_patient_search=display";
	}
</script>
</body>
</html>	
