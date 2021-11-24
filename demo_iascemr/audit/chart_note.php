<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("../globalsSurgeryCenter.php");
$userid=$_REQUEST['patient_name'];
$start_datetxt=$_REQUEST['date1'];
$end_datetxt=$_REQUEST['date2'];
$flag=false;
$disp=none;
if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
		$get_http_path = 'https';
         }
	elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
	{
		$get_http_path= 'http';
	}	
?>
<!DOCTYPE html>
<html>
<head>
	<title>Patient Chart Notes</title>
	<meta name="viewport" content="width=device-width, maximum-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<?php include("common/auditLinkfile.php"); ?>
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


function chartpop(path){
		
		var flag=0;
		msg = "Please fill following\n";
		var u=document.chart_form.patient_name.value;
		var logindate = document.chart_form.date1.value;
		var logoutdate = document.chart_form.date2.value;
		if(!logindate) { msg = msg+"\u2003\u2022 From Date\n"; ++flag; }
		if(!logoutdate) { msg = msg+"\u2003\u2022 To Date\n"; ++flag; }
		var audit=document.chart_form.type.value;
		//alert(audit);
		if(flag > 0){
			alert(msg);
			return false;	
		}else {
			window.open('chart_pop.php?user='+u+'&login='+logindate+'&logout='+logoutdate+'&atype='+audit+'&get_http_path='+path,'','width=650,height=600,top=70,left=200,resizable=yes,scrollbars=1');
		}
		
		
	}
//button swaping done by mamta
function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

//button swaping done by mamta
</script>
</head>
<body>
<form name="chart_form" method="post">
	<div class="container-fluid padding_0">
		<div class="inner_surg_middle ">
				<div style="" id="" class="all_content1_slider ">	         
					<div class="wrap_inside_admin">
					<div class=" subtracting-head">
						<div class="head_scheduler new_head_slider padding_head_adjust_admin">
							<span>
								Patient Chart Notes  
							</span>
						</div>
					</div>   
					<Div class="wrap_inside_admin ">
						<div class="col-md-2 visible-md"></div>
						<div class="col-lg-4 visible-lg"></div>
						<div class="col-md-8 col-sm-12 col-xs-12 col-lg-4">
							<div class="audit_wrap">
								<h5 class="ans_pro_h">
									<span>Chart Note Audit Report</span>
								</h5>
								<div class="form_outer">
									<Div class="row">
										<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
											<div class="form_reg">
												<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													<label for="audit_patient_name" class="text-left"> 
														Select User	
													</label>
												</div>
												  
												<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													<Select class="form-control selectpicker" id="audit_patient_name" name="patient_name" data-size="10">
														<option value="">Select User</option>
														<?php 
															$qry=imw_query("select * from users where deleteStatus!='Yes' order by lname");
															while($uname=imw_fetch_array($qry))
															{
																$user= stripslashes($uname[4].", ".$uname[2]);
																$uid=$uname[0];
														?>
														<option value="<?php echo $uname[0]; ?>"   <?php //if($_REQUEST['patient_name']==$uid){ echo 'selected="selected"';}  ?>  ><?php echo $user;  ?></option>
														<?php } ?>
													</Select>	
												 </div> <!----------------------- Full Inout col-12    ------------------------------>
											</div>
										</div>
										<div class="clearfix margin_adjustment_only visible-sm"></div> 
										<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
											<div class="form_reg">
												<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													<label for="audit_type" class="text-left"> 
														Audit Type	
													</label>
												</div>
													
												<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													<Select class="form-control selectpicker" id="audit_type" name="type" data-size="10">
														<option value="all">All</option>
														<option value="created">Created</option>
														<option value="modified">Modified</option>
														<option value="viewed">Viewed</option>
														<option value="printed">Printed</option>
														<option value="scanned">Scanned</option>
													</Select>	
												</div> <!----------------------- Full Inout col-12    ------------------------------>
											</div>	
										</div>
										<div class="clearfix margin_adjustment_only"></div>
										<div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
											<div class="form_reg text-center">
												<label class="date_r">
													Date Range				
												</label>
											</div>
										</div>     
										<div class="clearfix margin_adjustment_only  its_line"></div>
										<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
											<div class="form_reg">
												<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													<label class="" for="date1">
														From			
													</label>
												</div>
												<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													<Div class="input-group"  id='datetimepicker1'>
														<input class="form-control" type="text" name="date1" id="date1" tabindex="1" />
														<div class="input-group-addon datepicker">
															<a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
														</div>
													</Div>
												</div> <!----------------------- Full Inout col-12    ------------------------------>
											</div>	
										</div>
										<div class="clearfix margin_adjustment_only visible-sm"></div>
										<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
											<div class="form_reg">
												<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													<label class="" for="date2">
														To
													</label>
												</div>
												<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													<Div class="input-group" id="datetimepicker2">
														<input class="form-control" type="text" id="date2" name="date2" tabindex="1" />
														<div class="input-group-addon datepicker">
															<a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
														</div>
													</Div>
												</div> <!----------------------- Full Inout col-12    ------------------------------>
											</div>	
										</div>
									</Div>	
								</div>
							</div>
							<div class="btn-footer-slider">
								<a id="auditBtn" onClick="return chartpop('<?php echo $get_http_path;?>');" class="btn btn-info" href="javascript:void(0)">
									Audit
								</a>
								<a id="resetBtn" onClick="return submitfn();" class="btn btn-default" href="javascript:void(0)">
									<b class="fa fa-refresh"></b> Reset
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
		document.chart_form.patient_name.value='';
		document.chart_form.date1.value='';
		document.chart_form.date2.value='';
		document.chart_form.type.value='';
		$(".selectpicker").selectpicker('refresh');
	}
</script>	
</body>
</html>