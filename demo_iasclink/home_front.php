<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
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
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" />
<style>
	td{
		font-family:verdana,arial;font-size:11px;
	}
</style>
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
	mywindow=open('mycal1.php?md='+q+'&rf=yes','','width=200,height=250,top=200,left=300');
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
<?php
$spec= "
</head>
<body>";
include("common/link_new_file.php");
include_once("common/commonFunctions.php");
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
<table class="table_collapse alignCenter" style="overflow:auto; border:none; width:98%; background-color:#FFFFFF;">
	<tr>
		<td colspan="3" class="alignCenter" style="height:35px; vertical-align:baseline;">
			<div class="text_homeb" style="padding-bottom:2px; ">&nbsp;
				<?php echo $surgerycenter_name;?>
			</div>
			&nbsp;&nbsp;<?php echo $surgerycenter_address;?>
			<br>&nbsp;&nbsp;<?php echo $surgerycenter_city.', '.$surgerycenter_state.' '.$surgerycenter_zip;?>
			
		</td>
	</tr>
	<tr>
		<td colspan="3" class="text_homeb alignCenter valignMiddle" style="padding-left:6px;height:20px; background-color:#ECF1EA;">
			HIPAA CONFIDENTIALITY STATEMENT
		</td>
	</tr>
	<tr>
		<td style="width:10px;"></td>
		<td class="valignTop" >
			<div style="height:510px; overflow:auto;">
				<!-- <p style=" padding-left:10px;width:975px;text-align:justify;line-height:15px;"><?php //echo $surgerycenter_loginLegalNotice;?></p> -->
				<p style=" padding-left:10px;width:965px;text-align:justify;line-height:15px;"><?php echo stripslashes($surgerycenter_loginLegalNotice);?></p>
			</div>
		</td>
		<td style="width:5px;"></td>
	</tr>
</table>
<script>
	var redirect_txt_patient_search_value = '<?php echo $_GET["redirect_txt_patient_search_id"];?>';
	//var redirect_txt_patient_search_value = eval(document.getElementById("txt_patient_search"))
	if(redirect_txt_patient_search_value!="") {
		//alert(iframe_home_inner_front);
		top.iframeHome.iframe_home_inner_front.location = "home_inner_front.php?txt_patient_search_id="+redirect_txt_patient_search_value+"&display_cal=none&display_patient_sch=none&display_patient_search=display";
	}
</script>	
</body>
</html>
