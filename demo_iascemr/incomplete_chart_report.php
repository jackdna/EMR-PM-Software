<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include("common/conDb.php");
?>
<!DOCTYPE html>
<html>
<head>
<title>Patient Monitor Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="js/jquery.js"></script>
<?php
$spec = "
</head>
<body onLoad=\"MM_preloadImages('images/generate_report_hover.jpg','images/reset_hover.jpg')\">
";
$date1 = $_REQUEST['date1'];
if(!$date1) {
	$date1 = date("m-d-Y");
}
$date2 = $_REQUEST['date2'];
if(!$date2) {
	$date2 = date("m-d-Y");
}

include("common/link_new_file.php");
include_once("no_record.php");
if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
	$get_http_path = 'https';
	 }
elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
{
	$get_http_path= 'http';
}									
$provArr = $jsProvArr = array();
$qry="SELECT usersId,fname,mname,lname,user_type FROM users WHERE deleteStatus!='Yes' AND user_type IN('Surgeon','Anesthesiologist','Nurse') ORDER BY lname";
$res=imw_query($qry) or die(imw_error());
while($row=imw_fetch_array($res))
{
	$provider_id=$row['usersId'];
	$provider_fname= $row['fname'];
	$provider_mname= $row['mname'];
	$provider_lname= $row['lname'];
	$user_type= $row['user_type'];
	$provider_name=stripslashes($provider_lname.",".$provider_fname);
	$provArr[]=$provider_id.'|-|'.$user_type.'|-|'.$provider_name;
}
if(count($provArr)>0) {
	$jsProvArr = json_encode($provArr);	
}
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
function reportpop(path,report_format){
		var provider1		=	$("#provider").val();
		var provider_type	=	$("#provider_type").val();
		var fromdate		=	$("#date1").val();
	    var todate			=	$("#date2").val();
	    var flPath 			=	'incomplete_chart_reportpop.php'+'?provider_type='+provider_type+'&startdate='+fromdate+'&enddate='+todate+'&provider='+provider1+'&hidd_report_format='+report_format;
		
		var wndNme 			=	'incomplete_chart_report';
		
		if(report_format=='csv') {
			//parent.top.$(".loader").fadeIn('fast').show('fast');
			document.incomplete_chart_report.action=flPath;
			document.incomplete_chart_report.submit();
		}else {
			var parWidth = parent.document.body.clientWidth;
			var parHeight = parent.document.body.clientHeight;
			window.open(flPath,wndNme,'width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');
		}
	}

function resetfields()
{
	document.incomplete_chart_report.provider_type.value = "Surgeon";
	change_prov_type("Surgeon");
	document.incomplete_chart_report.date1.value="";
	document.incomplete_chart_report.date2.value="";
	$('.selectpicker').selectpicker('refresh');
}

function change_prov_type(usrTyp) {
	var provider_data = '<?php echo $_REQUEST['provider'];?>';
	var usrArr = <?php echo $jsProvArr;?>;
	$("#provider").html("");
	var cur_splt=""; 
	all_sel = "";
	if(!provider_data || provider_data == "all") {
		all_sel = "selected";	
	}
	var type_options = '<option value="all" data-attending = "0" '+all_sel+' >All  ' + usrTyp + 's</option>'
	var sel = '';
	for(var i=0;i<usrArr.length;i++){
		sel = '';
		cur_splt=usrArr[i].split("|-|");
		if(usrTyp==cur_splt[1] || usrTyp==""){
			var Id	=		cur_splt[0] ; 
			if(provider_data) {
				provider_data_split = provider_data.split(',');
				for(var j=0;j<provider_data_split.length;j++){
					if(Id == provider_data_split[j]) { sel = "selected"; }
				}
			}
			type_options += '<option data-attending = "1" value="'+ Id +'" '+sel+'>'+cur_splt[2]+'</option>';
		}
	}
	$('#provider').html(type_options);
	$('#provider').selectpicker('refresh');
}
$(document).ready(function(e) {
    var prov_type = "Surgeon";
	var reqst_prov_type = "<?php echo $_REQUEST['provider_type'];?>";
	if(reqst_prov_type) {
		prov_type = reqst_prov_type;
	}
	change_prov_type(prov_type);
});
</script>
<div class="main_wrapper">
	<form name="incomplete_chart_report" action="#" method="post" >	
    	<div class="container-fluid padding_0">
        	<div class="inner_surg_middle ">
            
            		<div style="" id="" class="all_content1_slider ">	         
                    
                          <div class="wrap_inside_admin">
                          	<div class=" subtracting-head">
                            	<div class="head_scheduler new_head_slider padding_head_adjust_admin">
                                	<span>Incomplete Chart Report </span>
                               	</div>
                          	</div>
                            
                            <Div class="wrap_inside_admin">
                            	
								<div class="col-md-2 visible-md"></div>
                                <div class="col-lg-3 visible-lg"></div>
                                
                                <div class="col-md-8 col-sm-12 col-xs-12 col-lg-6">
                                     
                                    <div class="audit_wrap">
                                    	<div class="form_outer">
                                        	<Div class="row">
                                            	<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
                                               		<div class="form_reg">
                                                    	
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<label for="provider_type" class="text-left">Provider Type </label>
                                                      	</div>
                                                        
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<Select class="selectpicker form-control" id="provider_type" name="provider_type" onChange="change_prov_type(this.value);" tabindex="1"  title="All Provider Types">
                                                            	
                                                                <option data-attending = "1" value="Surgeon" <?php if(!$_REQUEST["provider_type"] || $_REQUEST["provider_type"]=="Surgeon") { echo "selected"; }?>>Surgeon</option>
                                                                <option data-attending = "1" value="Anesthesiologist" <?php if($_REQUEST["provider_type"]=="Anesthesiologist") { echo "selected"; }?>>Anesthesiologist</option>
                                                                <option data-attending = "1" value="Nurse" <?php if($_REQUEST["provider_type"]=="Nurse") { echo "selected"; }?>>Nurse</option>
                                                           	</Select> 
                                                      	</div>
                                                		<!----------------------- Full Inout col-12    ------------------------------>
                                                        
                                                    </div>
                                               	</div>
                                                
                                                <div class="clearfix margin_adjustment_only visible-sm"></div>
                                                <div class="clearfix margin_adjustment_only visible-xs"></div>                                                    
                                                
                                                <div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
                                                	<div class="form_reg">
                                                    	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<label for="p_select" class="text-left">Provider</label>
                                                        </div>
                                                        
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	
                                                            <select class="selectpicker form-control" name="provider[]" id="provider" multiple="multiple" tabindex="2" title="No Provider Selected" ></select>
                                                       	</div> <!----------------------- Full Inout col-12    ------------------------------>
                                                  	</div>
                                                 </div>
                                                 
                                              	<div class="clearfix margin_adjustment_only"></div>
                                                
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
                                                            	<input type="text" class="form-control" tabindex="3" id="date1" name="date1" value="<?php echo $date1;?>"/>
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
                                                            	<input type="text" class="form-control" tabindex="4" id="date2" name="date2" value="<?php echo $date2;?>" />
                                                                <div class="input-group-addon datepicker">
                                                                	<a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
                                                               	</div>
                                                          	</div>
                                                      	</div>
                                                   	</div>
                                               	</div>
                                                
                                                <Div class="clearfix margin_adjustment_only visible-sm"></Div>
                                                <Div class="clearfix margin_adjustment_only visible-xs"></Div> 
                                                <div class="clearfix margin_adjustment_only"></div>
                                                
                                          	</Div>
                                       	</div>
                                 	</div>
                                    
                                    <div class="btn-footer-slider">
                                    	<a href="javascript:void(0)" class="btn btn-info"  id="generate_report" onClick="return reportpop('<?php echo $get_http_path; ?>','');" >
                                        	<b class="fa fa-edit"></b> Generate Report 
                                       	</a>
                                        <a href="javascript:void(0)" class="btn btn-info" id="generate_csv_report" onClick="return reportpop('<?php echo $get_http_path; ?>','csv');">
                                            <b class="fa fa-download"></b> Export CSV 
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
if($_REQUEST["no_record"]=="yes") { //IN CASE OF CSV REPORT
?>
	<script>
		modalAlert("No Record Found !");
	</script>
<?php
}
?>