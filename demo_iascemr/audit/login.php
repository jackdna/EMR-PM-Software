<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../globalsSurgeryCenter.php");
//include_once("logout.php");
//include("adminLinkfile.php");
//include_once("funcSurgeryCenter.php");
//include_once("classObjectFunction.php");
$userid=$_REQUEST['patient_name'];
$start_datetxt=$_REQUEST['date1'];
$end_datetxt=$_REQUEST['date2'];
if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
		$get_http_path = 'https';
         }
	elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
	{
		$get_http_path= 'http';
	}	

$provArr = $jsProvArr = array();
$qry="SELECT usersId,fname,mname,lname,user_type FROM users WHERE deleteStatus!='Yes' ORDER BY lname";
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
<!DOCTYPE html>
<html>
<head>
	<title>Audit</title>
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

function resultpop(path){
	//var q = document.loginform.patient_name.value;
	var flag=0;
	msg = "Please fill following\n";
	var q = $("#provider").val();
	var provider_type=document.loginform.provider_type.value;
	var logindate = document.loginform.date1.value;
	var logoutdate = document.loginform.date2.value;
	if(!logindate) { msg = msg+"\u2003\u2022 From Date\n"; ++flag; }
	if(!logoutdate) { msg = msg+"\u2003\u2022 To Date\n"; ++flag; }
	if(flag > 0){
		alert(msg);
		return false;	
	}else {
		window.open('loginpop.php?provider='+q+'&provider_type='+provider_type+'&login='+logindate+'&logout='+logoutdate+'&get_http_path='+path,'','width=650,height=550,top=100,left=100,resizable=yes,scrollbars=1');
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

function change_prov_type(usrTyp) {
	
	var usrArr = <?php echo $jsProvArr;?>;
	$("#provider").html("");
	var cur_splt=""; 
	var type_options = '<option value="all" data-attending = "0" selected="selected" >All Providers</option>'
	for(var i=0;i<usrArr.length;i++){
		cur_splt=usrArr[i].split("|-|");
		
		if(usrTyp==cur_splt[1] || usrTyp==""){
			type_options += '<option data-attending = "1" value="'+cur_splt[0]+'@@'+cur_splt[1]+'">'+cur_splt[2]+'</option>';
		}
	}
	//document.getElementById("provider").innerHTML = type_options;
	$('#provider').html(type_options);
	$('#provider').selectpicker('refresh');
}
$(document).ready(function(e) {
	change_prov_type("");
});
	</script>
</head>
<body id="main" class="laser-continue" >

<div class="main_wrapper">
	<form name="loginform" method="post">
    
    	<div class="container-fluid padding_0">
        	<div class="inner_surg_middle ">
            	<div style="" id="" class="all_content1_slider ">
                	
                    <div class="wrap_inside_admin">
                    	<div class=" subtracting-head">
                            <div class="head_scheduler new_head_slider padding_head_adjust_admin">
                                <span>Login/Logout</span>
                            </div>
                  		</div>
                        
                        <Div class="wrap_inside_admin ">
                        	
                            <div class="col-md-2 visible-md"></div>
                            <div class="col-lg-4 visible-lg"></div>
                            
                            <div class="col-md-8 col-sm-12 col-xs-12 col-lg-4">
                            
                            	<div class="audit_wrap">
                                	<h5 class="ans_pro_h"> <span>    Login/Logout Audit Report </span>  </h5>
                                    <div class="form_outer">
                                    	<Div class="row">
                                        
                                            <div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
                                                <div class="form_reg">
                                                    
                                                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        <label for="pro_select" class="text-left">Provider Type </label>
                                                    </div>
                                                    
                                                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        <Select class="selectpicker form-control" id="pro_select" name="provider_type" onChange="change_prov_type(this.value);" tabindex="1"  title="All Provider Types">
                                                            <option data-attending = "0" value="">All Provider Types</option>
                                                            <option data-attending = "1" value="Surgeon">Surgeon</option>
                                                            <option data-attending = "1" value="Anesthesiologist">Anesthesiologist</option>
                                                            <option data-attending = "1" value="Nurse">Nurse</option>
                                                            <option data-attending = "1" value="Coordinator">Coordinator</option>
                                                            <option data-attending = "1" value="Staff">Staff</option>
                                                            
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
                                            		<label class="date_r">Date Range</label>
                                           		</div>
                                           	</div>
                                            
                                            <div class="clearfix margin_adjustment_only  its_line"></div>
                                            
                                            <div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
                                            	<div class="form_reg">
                                                	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                    	<label class="" for="from">From</label>
                                                  	</div>
                                                    
                                                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                    	<Div class="input-group"  id='datetimepicker1'>
                                                        	<input type="text" name="date1" id="date1" class="form-control" value="<?php echo $start_datetxt;  ?>">
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
                                                        <label class="" for="to">To</label>
                                                    </div>
                                                    
                                                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        <Div class="input-group" id="datetimepicker2">
                                                            <input type="text" name="date2" id="date2" class="form-control" tabindex="1" value="<?php echo $end_datetxt;  ?>" />
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
                                	<a class="btn btn-info" href="javascript:void(0)" id="auditBtn" onClick="return resultpop('<?php echo $get_http_path;?>');">Audit </a>
                                    <a class="btn btn-default" href="javascript:void(0)" id="ResetForm"><b class="fa fa-refresh"></b>	Reset</a>
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

<script type="text/javascript">
	function submitfn()
	{
		
		
		document.loginform.patient_name.value='';
		document.loginform.date1.value='';
		document.loginform.date2.value='';
		
		$('select').selectpicker('render');
	}
</script>	
</body>
</html> 
 
	