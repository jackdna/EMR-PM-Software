<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("../globalsSurgeryCenter.php");
if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
		$get_http_path = 'https';
         }
	elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
	{
		$get_http_path= 'http';
	}	

 $type= $_POST['chbx_quality'];
?>
<!DOCTYPE html>
<html>
<head>
<title>Quality Check</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php include("common/auditLinkfile.php");?>
<script type="text/javascript">
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
				alert("Date Of Service can not be a future date")
				return false;
			}
		}
		document.getElementById("date"+q).value=fillDate;
		mywindow.close();
	}
function padout(number){
return (number < 10) ? '0' + number : number;
}

function qualitypop(path){
var det='';
		var patient=document.quality_check_form.patient_name.value;
		//var user=document.quality_check_form.user_name.value;
		var lname1 =document.quality_check_form.start.value;
		var lname2 =document.quality_check_form.end.value;
		var quant=document.quality_check_form.quantity.value;
		var month=document.quality_check_form.month.value;
		var year=document.quality_check_form.year.value;
		if(document.getElementById("chbx_quality_yes").value!="" && document.getElementById("chbx_quality_yes").checked==true){
			var det=document.getElementById("chbx_quality_yes").value;
	  url='qualitypop.php?patient='+patient+'&lnamest='+lname1+'&lnamend='+lname2+'&number='+quant+'&mon='+month+'&year1='+year+'&sum='+det+'&get_http_path='+path;

		}else if(document.getElementById("chbx_quality_no").value!="" && document.getElementById("chbx_quality_no").checked==true){
			var det=document.getElementById("chbx_quality_no").value;
		url='qualitypop_detail.php?patient='+patient+'&lnamest='+lname1+'&lnamend='+lname2+'&number='+quant+'&mon='+month+'&year1='+year+'&sum='+det+'&get_http_path='+path;

		}
		//var user=document.quality_check_form.user_name.value;
		var url;
		
		//url='qualitypop.php?name='+user+'&patient='+patient+'&lnamest='+lname1+'&lnamend='+lname2+'&number='+quant+'&mon='+month+'&year1='+year+'&sum='+det;
		//url='qualitypop.php?patient='+patient+'&lnamest='+lname1+'&lnamend='+lname2+'&number='+quant+'&mon='+month+'&year1='+year+'&sum='+det;
		if(det!='' )
		{
		window.open(url,'wind','width=750,height=600,top=70,left=200, scrollbars=1,resizable=1');
		}
		else
		{
		 alert("Please Select Summary Or Detail")
		}
	}
</script>
</head>
<body>
<form name="quality_check_form" method="post">
	<div class="container-fluid padding_0">
		<div class="inner_surg_middle ">
			<div style="" id="" class="all_content1_slider ">	         
					<div class="wrap_inside_admin">
				<div class=" subtracting-head">
					<div class="head_scheduler new_head_slider padding_head_adjust_admin">
						<span>
						   Quality Check
						</span>
					</div>
				</div>   
					<Div class="wrap_inside_admin ">
						<div class="col-md-2 visible-md"></div>
						<div class="col-lg-3 visible-lg"></div>
						<div class="col-md-8 col-sm-12 col-xs-12 col-lg-6">
							   <div class="audit_wrap">
									 <div class="form_outer">
										  <Div class="row">
											  <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
												  <div class="form_reg">
														<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
															  <label for="patient_name" class="text-left"> 
																	Audit by Patient Name	
															  </label>
														  </div>
														  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
															   <input class="form-control" type="text" id="patient_name" name="patient_name" />
														   </div> <!----------------------- Full Inout col-12    ------------------------------>
												  </div>
											  </div>
											  <div class="clearfix margin_adjustment_only"></div>
											  <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
												  <div class="form_reg text-center">
														  <label class="date_r">
															  Audit By Last Name Range			
														  </label>
												  </div>
											 </div>     
											 <div class="clearfix margin_adjustment_only  its_line"></div>
											 <div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
												  <div class="form_reg">
														<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
															<label class="" for="audit_start">
																From			
															</label>
														</div>
														<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
															<input autocomplete="off" class="form-control" type="text" name="start" id="audit_start" tabindex="1" />
														</div> <!----------------------- Full Inout col-12    ------------------------------>
												  </div>	
											 </div>
											 <div class="clearfix margin_adjustment_only visible-sm"></div>
											 <div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
												  <div class="form_reg">
														<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
															<label class="" for="audit_end">
																To
															</label>
														</div>
														<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
															<input autocomplete="off" class="form-control" type="text" name="end" id="audit_end" tabindex="1" />
														</div> <!----------------------- Full Inout col-12    ------------------------------>
												  </div>	
											 </div>
											  <div class="clearfix margin_adjustment_only"></div>
											  <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
												  <div class="form_reg text-center">
														<label class="date_r">
															 Audit by Random Number	
														</label>
												  </div>
											 </div>     
											  <div class="clearfix margin_adjustment_only  its_line"></div>
											 <div class="col-md-4 col-sm-12 col-lg-4 col-xs-12">
												  <div class="form_reg">
														<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
															<label class="" for="audit_quantity">
																Quantity
															</label>
														</div>
														<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														   <input class="form-control" type="text" name="quantity" id="audit_quantity"/>
														</div> <!----------------------- Full Inout col-12    ------------------------------>
												  </div>	
											 </div>
											 <div class="clearfix margin_adjustment_only visible-sm"></div>
												<div class="col-md-4 col-sm-12 col-lg-4 col-xs-12">
												  <div class="form_reg">
														<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														  <label class="" for="audit_month">
															Month
														  </label>
														</div>
														<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														   <select class="selectpicker" name="month" id="audit_month" data-size="10">
																<option value="">Select</option>
																<option value="1">January</option>
																<option value="2">February</option>
																<option value="3">March</option>
																<option value="4">April</option>
																<option value="5">May</option>
																<option value="6">June</option>
																<option value="7">July</option>
																<option value="8">August</option>
																<option value="9">September</option>
																<option value="10">October</option>
																<option value="11">November</option>
																<option value="12">December</option>
														   </select>
														</div> <!----------------------- Full Inout col-12    ------------------------------>
													  </div>	
												 </div>
											   <div class="clearfix margin_adjustment_only visible-sm"></div>     
												 <div class="col-md-4 col-sm-12 col-lg-4 col-xs-12">
												  <div class="form_reg">
														<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														  <label class="" for="audit_year">
															Year
														  </label>
														</div>
														<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														   <select class="selectpicker" name="year" id="audit_year" data-size="10">
																<option value="">Select</option>
																<?php 
																$date=date("Y");
																for($i=$date;$i>=$date-50;$i--)
																{
																?>
																<option value="<?php echo $i; ?>"><?php echo $i;?></option>
																<?php
																}
																?>
														   </select>
														</div> <!----------------------- Full Inout col-12    ------------------------------>
													  </div>	
												</div>
												<div class="clearfix margin_adjustment_only"></div>
												 <div class="clearfix margin_adjustment_only"></div>
													  <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
														  <div class="form_reg text-center">
																  <label class="date_r">
																	 Select Report Type
																  </label>
														  </div>
													 </div>
													 <div class="clearfix margin_adjustment_only its_line"></div>
													 <div class="clearfix margin_adjustment_only"></div>
													 <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
														  <div class="width_adj_sum">
															  <div class="wrapped_inner_ans_pro">
																  <label for="chbx_quality_yes">
																	<input type="checkbox" value="summary" name="chbx_quality" id="chbx_quality_yes" tabindex="7" onClick="javascript:checkSingle('chbx_quality_yes','chbx_quality')" <?php if ($type=='summary')echo 'checked'; ?> /> Summary
																  </label>
															  </div>
															  <div class="wrapped_inner_ans_pro">
																  <label for="chbx_quality_no">
																	<input type="checkbox" value="detail" name="chbx_quality" id="chbx_quality_no" tabindex="7" onClick="javascript:checkSingle('chbx_quality_no','chbx_quality')" <?php if ($type=='detail')echo 'checked'; ?> /> Details
																  </label>
															  </div>
														  </div>
													  </div>	                                                      
										  </Div>	
									 </div>
								</div>
								<div class="btn-footer-slider">
									  <a href="javascript:void(0)" onClick="return qualitypop('<?php echo $get_http_path;?>');" class="btn btn-info" id="auditBtn">
										  <b class="fa fa-edit"></b> Generate Report 
									  </a>
									  <a id="resetBtn" onClick="return submitfn();" class="btn btn-default" href="javascript:void(0)">
										 <b class="fa fa-refresh"></b>	Reset
									  </a>
								</div>
						   </div>
					</div>		
			   </Div>
			  </div> 
			</div>  
			<!-- NEcessary PUSH     -->	 
			<Div class="push"></Div>
			<!-- NEcessary PUSH     -->
	</div>

</form>		
<script type="text/javascript">
	function submitfn()
	{
		//document.quality_check_form.user_name.value='';
		document.quality_check_form.patient_name.value='';
		document.quality_check_form.start.value='';
		document.quality_check_form.end.value='';
		document.quality_check_form.month.value='';
		document.quality_check_form.year.value='';
		document.quality_check_form.quantity.value='';
		//document.quality_check_form.chbx_quality_yes.Checked=false;
		document.getElementById("chbx_quality_no").checked=false;
		document.getElementById("chbx_quality_yes").checked=false;
		$(".selectpicker").selectpicker('refresh');
	}
</script>	  
</body>
</html>