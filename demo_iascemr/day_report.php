<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

//Report Settings	
$type = (isset($_REQUEST['dType']) && $_REQUEST['dType']) ? $_REQUEST['dType'] : "details";
?>
<!DOCTYPE html>
<html>
<head>
<title>Day Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php 
	
	$spec = '
	</head>
	<body id="main" class="laser-continue">
	';
	
	include("common/link_new_file.php");
	include_once("no_record.php");
    
	$date	=	isset($_REQUEST['date1'])		?	$_REQUEST['date1']	:	date('m-d-Y');
	
	if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
		$get_http_path = 'https';
    }
	elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
	{
		$get_http_path= 'http';
	}	
	
 ?>
<script >
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
	var date=document.day_report.date1.value;
	
	var S	=	document.getElementById('summary');
	var D	=	document.getElementById('details');
	var dType	=	((S.checked) ? S.value : ((D.checked) ? D.value : 'details' )) ;

	if( !date ) {
		alert('Please select date');
		return false;
	}
	var win_name	=	'Day Report ' + date ;
	var flPath = 'day_reportpop.php?date12='+ date+'&get_http_path='+path+'&dType='+dType+'&hidd_report_format='+report_format;
	if(report_format=='csv') {
		//parent.top.$(".loader").fadeIn('fast').show('fast');
		document.day_report.action=flPath;
		document.day_report.submit();
	}else {
		var parWidth = parent.document.body.clientWidth;
		var parHeight = parent.document.body.clientHeight;
		window.open(flPath,win_name,'width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');
	}
	
}

function date12()
{ 
	 var s_date=document.day_report.date1.value;
		   if(s_date=="")
   {
	  modalAlert("Plz select the date"); 
   }
}
function resetdate()
{
	document.day_report.date1.value="";
}

</script>

<div class="main_wrapper" >	
		<form name="day_report" action="day_reportpop.php" method="post" >	
            <div class="container-fluid padding_0">
            	
                <div class="inner_surg_middle ">
					
                    <div style="" id="" class="all_content1_slider ">
                    	
                        <div class="wrap_inside_admin">
                        	
                            <div class=" subtracting-head">
                            	
                                <div class="head_scheduler new_head_slider padding_head_adjust_admin">
                                	<span>Day Report</span>
                              	</div>
                                
                          	</div>
                            
                            
                            <Div class="wrap_inside_admin  scrollable_yes">
                            	
                                <div class="col-md-2 visible-md"></div>
                                <div class="col-lg-3 visible-lg"></div>
                                
                                <div class="col-md-8 col-sm-12 col-xs-12 col-lg-6">
                                	
                                    <div class="audit_wrap">
                                    	
                                        
                                        
                                        <div class="form_outer">
                                        	<!----------------------- Full Inout col-12    ------------------------------>
                                            <div class="clearfix margin_adjustment_only"></div>
                                            
                                            <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
                                            	<div class="form_reg">
                                                	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                    	<div class="form_reg text-center">
                                                        	<label class="" for="date1"> Input Date</label>
                                                       	</div>
                                                  	</div>
                                                    
                                                    
                                                    <div class="clearfix margin_adjustment_only its_line"></div>
                                               		<div class="clearfix margin_adjustment_only"></div>
                                               		<div class="col-md-3 col-lg-3 col-xs-12 clearfix hidden-sm"></div>
                                                    
                                                    <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                                    	<div class="input-group" id="datetimepicker1">
                                                        	<input type="text" class="form-control" id="date1" name="date1" value="<?php echo $date;?>" tabindex="1">
                                                      		<div class="input-group-addon datepicker">
                                                            	<a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
                                                          	</div>
                                                      	</div>
                                                	</div> 
                                                    
		
               										<div class="clearfix margin_adjustment_only"></div>
                                                    	<div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
                                                        	<div class="form_reg text-center">
                                                                <label class="date_r">
                                                                   Report Type
                                                                </label>
                                                          	</div>
														</div>		
														<div class="clearfix margin_adjustment_only its_line"></div>
															<div class="clearfix margin_adjustment_only"></div>
															<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
																<div class="width_adj_sum">
																	<div class="wrapped_inner_ans_pro">
																		<label>
																			<input type="checkbox" onClick="javascript:checkSingle('summary','dType');" name="dType" id="summary" <?php if($type=="summary") echo "checked";?>value="summary" tabindex="7"  /> Summary </label>
																	</div>
																	<div class="wrapped_inner_ans_pro">
																		<label>
																			<input type="checkbox" onClick="javascript:checkSingle('details','dType');" name="dType" id="details" <?php if($type=="details" || !$type ) echo "checked";?> value="details" tabindex="7"  /> Details   </label>
																	</div>
																</div>
															</div>
                                                
	
														</div>
	
                                          	</div>
                                            <!----------------------- Full Inout col-12    ------------------------------>
                                            
                                        </div> <!-- Form Outer -->
                                   	
                                    </div> <!-- Audit Wrap -->
                                    
                                    
                                    <div class="btn-footer-slider">
                                    
                                    	<a href="javascript:void(0)" class="btn btn-info" id="generate_report" onClick="return reportpop('<?php echo $get_http_path; ?>','');" >
                                        	<b class="fa fa-edit"></b> Generate Report 
                                      	</a>
                                        <a href="javascript:void(0)" class="btn btn-info" id="generate_csv_report" onClick="return reportpop('<?php echo $get_http_path; ?>','csv');">
                                            <b class="fa fa-download"></b> Export CSV 
                                        </a>
                                        <a class="btn btn-default" href="javascript:void(0)" id="reset" onClick="return resetdate();" >
                                        	<b class="fa fa-refresh"></b>	Reset
                                       	</a>
                                        
                                  	</div>
                                                                        
                               	</div>
                                
                       		</Div>
                            
                        </div> 
                        
                 	</div>
                    
                    <!-- Necessary Push     -->	 
                    <Div class="push"></Div>
                    <!-- Necessary Push     -->
                    
           		</div>
        </div>
	</form>	
</div>

<?php
if($_REQUEST["no_record"]=="yes") { //IN CASE OF CSV REPORT
?>
	<script>
		modalAlert("No Record Found !");
		var date12 = '<?php echo $_REQUEST["date12"];?>';
		var dType = '<?php echo $_REQUEST["dType"];?>';
		document.getElementById('date1').value=date12;
		if(dType=='summary') {
			document.getElementById(dType).click();
		}
	</script>
<?php
}
?>
</body>
</html>