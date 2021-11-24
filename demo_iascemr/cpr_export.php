<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Anesthesiologist CPR Export</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
$spec = "
</head>
<body onLoad=\"MM_preloadImages('images/generate_report_hover.jpg','images/reset_hover.jpg')\">
";
$currDt = date("m-d-Y");
include("common/link_new_file.php");
include_once("no_record.php");
if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
	$get_http_path = 'https';
	 }
elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
{
	$get_http_path= 'http';
}									


$date1 = $_REQUEST["date1"];
$date2 = $_REQUEST["date2"];
$anesthesiologist = $_REQUEST["anesthesiologist"];
if(!$date1) { $date1 = $currDt; }
if(!$date2) { $date2 = $currDt; }

?>							
<script>
var today = new Date();
var day = today.getDate();
var month = today.getMonth()
var year = y2k(today.getFullYear());
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
	mywindow.location.href = 'mycal1.php?md='+q;
	if(mywindow.opener == null)
		mywindow.opener = self;
}
function restart(q){
	fillDate = ''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year;
	if(q==8){
		if(fillDate > todaydate){
			modalAlert("Date Of Service can not be a future date")
			return false;
		}
	}
	document.getElementById("date"+q).value=fillDate;
	mywindow.close();
}
function padout(number){
return (number < 10) ? '0' + number : number;
}

function reportpop(){
	var msgHead="Please fill in the following:-";
	var msg = "";
	var flag = 0;
	//var f1 = document.frm_cpr_export.anesthesiologist.value;
	var f2 = document.frm_cpr_export.date1.value;
	var f3 = document.frm_cpr_export.date2.value;

	//if(f1==''){ msg = msg+"\t� Anesthesiologist\n"; ++flag; }
	if(f2==''){ msg = msg+"<br>&bullet;Date From\n"; ++flag; }
	if(f3==''){ msg = msg+"<br>&bullet;Date To\n"; ++flag; }
	if(flag > 0){
		msg = msgHead+"<p style='text-align:left; padding-left:60px;'>"+msg+"</p>";
		modalAlert(msg);
		return false;	
	}
	document.frm_cpr_export.cpr_save.value='yes';
	document.frm_cpr_export.submit();
	return true;	
		
}

function resetfields() {
	$('select').selectpicker('render');
	document.frm_cpr_export.date1.value="";
	document.frm_cpr_export.date2.value="";
 
}

</script>
<div class="main_wrapper">
	<form name="frm_cpr_export" action="cpr_exportpop.php" method="post" >
    	<input type="hidden" name="cpr_save" id="cpr_save" value="">
      	<div class="container-fluid padding_0">
        	<div class="inner_surg_middle ">
            
            		<div style="" id="" class="all_content1_slider ">	         
                    
                          <div class="wrap_inside_admin">
                          	<div class=" subtracting-head">
                            	<div class="head_scheduler new_head_slider padding_head_adjust_admin">
                                	<span>CPR Export </span>
                               	</div>
                          	</div>
                            
                            <Div class="wrap_inside_admin">
                            	
								<div class="col-md-2 visible-md"></div>
                                <div class="col-lg-3 visible-lg"></div>
                                
                                <div class="col-md-8 col-sm-12 col-xs-12 col-lg-6">
                                     
                                    <div class="audit_wrap">
                                    	<div class="form_outer">
                                        	<Div class="row">
                                            	
                                                <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
                                                	<div class="form_reg text-center">
                                                    	<label class="date_r">Select Date</label>
                                                  	</div>
                                               	</div>
                                                
                                                <div class="clearfix margin_adjustment_only  its_line"></div>
                                                <div class="clearfix margin_adjustment_only"></div>                                                  
                                                
                                                <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                                	<div class="form_reg">
                                                    	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<label for="from" class="">From</label>
                                                       	</div>	
                                                        
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<div id="datetimepicker1" class="input-group">
                                                            	<input type="text" class="form-control" tabindex="3" id="date1" name="date1" value="<?php echo date("m-d-Y");?>"/>
                                                                <div class="input-group-addon datepicker">
                                                                	<a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
                                                               	</div>
                                                           	</div>
                                                       	</div>
                                                   	</div>
                                              	</div>
                                                
                                                <Div class="clearfix margin_adjustment_only visible-sm"></Div>
                                                <Div class="clearfix margin_adjustment_only visible-xs"></Div>
                                                
                                                <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                                	<div class="form_reg">
                                                    	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<label for="to" class="">To</label>
                                                       	</div>
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<div id="datetimepicker2" class="input-group">
                                                            	<input type="text" class="form-control" tabindex="4" id="date2" name="date2" value="<?php echo date("m-d-Y");?>" />
                                                                <div class="input-group-addon datepicker">
                                                                	<a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
                                                               	</div>
                                                          	</div>
                                                      	</div>
                                                   	</div>
                                               	</div>
                                                
                                               	<div class="clearfix margin_adjustment_only"></div>
                                                
                                          	</Div>
                                       	</div>
                                 	</div>
                                    
                                    <div class="btn-footer-slider">
                                    	<a href="javascript:void(0)" class="btn btn-info"  id="generate_report" onClick="return reportpop();" >
                                        	<b class="fa fa-edit"></b> Generate Report 
                                       	</a>
                                        
                                        <a class="btn btn-default" href="javascript:void(0)" id="reset" onClick="return resetfields();">
                                        	<b class="fa fa-refresh"></b> Reset
                                       	</a>
                                   	</div>
                                    
                              	</div>
                            
                            </Div>
                            
                            
                    </div> 
                  </div>  
                  <!-- NEcessary PUSH     -->	 
                  <Div class="push"></Div>
                  <!-- NEcessary PUSH     -->
            </div>
        </div>
   	</form>
</div>

<?php
if($_REQUEST["record_exist"]=='no') {
?>
	<script>modalAlert('No Record Found');</script>
<?php
}
?>
</body>					   
</html>
		
